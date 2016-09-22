// module: start
EasyBlog.module("media/uploader", function($) {

var module = this;

// require: start
EasyBlog.require()
.library(
    "plupload"
)
// .view(
//     "media/browser.uploader",
//     "media/browser.uploader.item",
//     "media/browser.treeItemGroup",
//     "media/browser.treeItem"
// )
// .language(
//     'COM_EASYBLOG_MM_UPLOADING',
//     'COM_EASYBLOG_MM_UPLOADING_STATE',
//     'COM_EASYBLOG_MM_UPLOADING_PENDING',
//     'COM_EASYBLOG_MM_UPLOAD_COMPLETE',
//     'COM_EASYBLOG_MM_UPLOAD_PREPARING',
//     'COM_EASYBLOG_MM_UPLOAD_UNABLE_PARSE_RESPONSE',
//     'COM_EASYBLOG_MM_UPLOADING_LEFT'
// )
.done(function(){

var $Media, $Library, $Uploader, DS;

// controller: start
EasyBlog.Controller("Media.Uploader",

	{
		defaultOptions: {

            view: {
                uploader: "media/browser.uploader",
                uploadItem: "media/browser.uploader.item"
            },

            "{modalHeader}": ".modalHeader",
            "{modalToolbar}": ".modalToolbar",
            "{modalContent}": ".modalContent",
            "{modalFooter}": ".modalFooter",
            "{modalPrompt}": ".modalPrompt",
            "{modalBrowserButton}": ".modalButton.browserButton",
            "{modalDashboardButton}": ".modalButton.dashboardButton",

            "{uploadButton}" : ".uploadButton",
            "{uploadNavigation}" : ".uploadNavigation",

            "{uploadForm}": ".uploadForm",
            "{uploadPath}": ".uploadPath",
            "{uploadSize}": ".uploadSize",
            "{uploadExtensionList}": ".uploadExtensionList",

            "{uploadItemGroup}": ".uploadItemGroup",
            "{uploadItem}": ".uploadItem",

            "{uploadDropHint}": ".uploadDropHint",
            "{uploadInstructions}": ".uploadInstructions",

            "{clearListButton}": ".clearListButton"
		}
	},

    function(self) { return {

        init: function() {

            $Media = self.media;
            $Library = $Media.library;
            $Uploader = $Media.uploader = self;
            DS = $Media.options.directorySeparator;

            // Uploader template
            self.element
                .addClass("uploader")
                .html(self.view.uploader({
                    uploadSize: self.options.settings.max_file_size,
                    uploadExtensionList: self.options.settings.filters[0].extensions.split(",").join(", ")
                }));

            // Browser navigation
            self.uploadNavigation()
                .implement(
                    EasyBlog.Controller.Media.Navigation,
                    {
                        controller: self.controllerProps()
                    },
                    function() {
                        // Assign controller as a property of myself
                        self.navigation = this;
                    }
                );

            // Modal prompt
            self.modalPrompt()
                .implement(
                    EasyBlog.Controller.Media.Prompt,
                    {
                        controller: self.controllerProps()
                    },
                    function() {
                        self.promptDialog = this;
                    }
                );

            // Folder switcher
            self.element
                .implement(
                    EasyBlog.Controller.Media.Uploader.FolderSwitcher,
                    {
                        controller: self.controllerProps()
                    },
                    function() {
                        self.folderSwitcher = this;
                    }
                );

            // Plupload
            self.element
                .implement(
                    "plupload",
                    {
                        settings: self.options.settings,
                        "{uploadButton}" : self.options["{uploadButton}"],
                        "{uploadDropsite}": self.options["{uploadItemGroup}"]
                    },
                    function() {

                        self.plupload = this.plupload;

                        if (self.plupload.runtime=="html4" || $.browser.msie) {

                            // No drag & drop support
                            self.uploadDropHint().remove();

                            self.uploadItemGroup().addClass("indefinite-progress");

                            // Really dirty fix to fix tooltip in IE
                            var uploadInstructions =
                                    self.uploadInstructions()
                                        .appendTo($Media.element);

                            self.element.find("> form")
                                .mouseover(function(){

                                    var uploadButton = self.uploadButton(),
                                        offset = uploadButton.offset();

                                    uploadInstructions
                                        .addClass("show")
                                        .css({
                                            top: offset.top + uploadButton.outerHeight() - $Media.element.offset().top + 3,
                                            right: $(window).width() - (offset.left + uploadButton.outerWidth()) - 1
                                        });


                                })
                                .mouseout(function(){
                                    uploadInstructions.removeClass("show");
                                });

                        } else {

                            self.uploadButton()
                                .mouseover(function(){
                                    self.uploadInstructions().addClass("show");
                                })
                                .mouseout(function(){
                                    self.uploadInstructions().removeClass("show");
                                });
                        }
                    }
                );

            self.setLayout();
		},

        setLayout: function() {

            // Don't set layout if current modal is not us
            if ($Media.currentModal!=="uploader") return;

            var contentHeight;

            self.modalContent()
                .hide()
                .height(
                    contentHeight =
                        self.element.height() -
                        self.modalHeader().outerHeight() -
                        self.modalToolbar().outerHeight() -
                        self.modalFooter().outerHeight()
                )
                .show();

            if ($.browser.msie) {

                self.uploadDropHint()
                    .height(contentHeight);

                self.uploadItemGroup()
                    .height(contentHeight);
            }

            if (self.plupload) {
                self.plupload.refresh();
            }
        },

        controllerProps: function(prop) {

            return $.extend(
            {
                media: self.media,
                uploader: self
            }, prop || {});
        },

        setUploadFolder: function(key) {

            if (!key) return;

            self.navigation
                .setPathway(key);

            self.currentUploadFolder = key;
        },

        items: {},

        createItem: function(file) {

            // Create item controller
            var item = new EasyBlog.Controller.Media.Uploader.Item(
                self.view.uploadItem(),
                {
                    controller: self.controllerProps({
                        id: file.id,
                        originalFile: file,
                        uploadFolder: self.currentUploadFolder
                    })
                }
            );

            // Add to item group
            item.element
                .appendTo(self.uploadItemGroup());

            // Set initial status to pending
            var filesize = item.file().filesize,
                filesize = (filesize) ? "" : " (" + filesize + ").";

            item.setMessage($.language( "COM_EASYBLOG_MM_UPLOADING_PENDING" ) + filesize);

            // Keep a copy of the item in our registry
            self.items[file.id] = item;

            return item;
        },

        "{self} BeforeUpload": function(el, event, uploader, file) {

            var item = self.items[file.id];

            if (item===undefined) return;

            item.setMessage( $.language( 'COM_EASYBLOG_MM_UPLOAD_PREPARING' ) );

            var uploadUrl = self.options.settings.url,
                meta  = $Library.getMeta(item.uploadFolder),
                place = (meta) ? meta.place : item.uploadFolder.split("|")[0],
                path  = encodeURIComponent((meta) ? meta.path : item.uploadFolder.split("|")[1]);

            uploader.settings.url = uploadUrl + "&place=" + place + "&path=" + path;
        },

        "{self} FilesAdded": function(el, event, uploader, files) {

            // Wrap the entire body in a try...catch scope to prevent
            // browser from trying to redirect and load the file if anything goes wrong here.
            try {

                $.each(files, function(i, file) {

                    // The item may have been created before, e.g.
                    // when plupload error event gets triggered first.
                    if (self.items[file.id]!==undefined) return;

                    self.createItem(file);
                });

                if (self.uploadItem().length > 0) {

                    self.uploadItemGroup().removeClass("empty");
                }

                self.plupload.start();

            } catch (e) {

                console.error(e);
            };
        },

        "{self} UploadFile": function(el, event, uploader, file) {

            var item = self.items[file.id];

            if (item===undefined) return;

            item.setState("uploading");

            item.setMessage( $.language( 'COM_EASYBLOG_MM_UPLOADING_STATE' ) );
        },

        "{self} UploadProgress": function(el, event, uploader, file) {

            var item = self.items[file.id];

            if (item===undefined) return;

            item.setProgress(file.percent);

            item.setMessage(
                $.language( 'COM_EASYBLOG_MM_UPLOADING' )  +
                ((file.percent!==undefined) ? " " + file.percent + "%" : "") +
                ((file.loaded!==undefined && !file.size!==undefined) ?
                    ((file.size - file.loaded) ?
                        " (" + $.plupload.formatSize(file.size - file.loaded) + " " + $.language( 'COM_EASYBLOG_MM_UPLOADING_LEFT' ) + ")" : ""
                    ) : ""
                )
            );
        },

        "{self} FileUploaded": function(el, event, uploader, file, response) {

            // Get upload item
            var item = self.items[file.id];

            if (item===undefined) return;

            // Store the item response (For debugging purposes)
            item.response = response;

            // If the response is not a valid object
            if (!$.isPlainObject(response)) {

                // Set upload item state to failed.
                item.setState("failed");
                item.setMessage($.language('COM_EASYBLOG_MM_SERVER_RETURNED_INVALID_RESPONSE'));
                return;
            }

            // If the response object did not include the meta
            if (!$.isPlainObject(response.item)) {

                // Set upload item state to failed.
                item.setState("failed");
                item.setMessage(response.message || $.language('COM_EASYBLOG_MM_UPLOAD_UNABLE_PARSE_RESPONSE'));
                return;
            }

            // If all goes well, set upload item state to done.
            item.setState("done");
            item.setMessage( $.language( 'COM_EASYBLOG_MM_UPLOAD_COMPLETE' ) );

            // Store the meta
            item.meta = response.item;

            // hack: Restore place
            item.meta.place = $Media.library.get(item.uploadFolder).place;

            // Remove insert blog image button for non-image files
            if (item.meta.type!=="image") {
                item.insertBlogImageButton().remove();
            }

            $Media.library.addMeta(item.meta);
        },

        "{self} FileError": function(el, event, uploader, file, response) {

            var item = self.items[file.id];

            if (item===undefined) {

                // Create the item
                item = self.createItem(file);
            }

            item.response = response;

            item.setState("failed");

            item.setMessage(response.message);


            if (self.uploadItem().length > 0) {

                self.uploadItemGroup().removeClass("empty");
            }
        },

        "{self} Error": function(el, event, uploader, error) {

            // If the returned error object also returns a file object
            if (error.file) {

                // Check if the upload item has been created
                var file = error.file,
                    item = self.items[file.id];

                // If the upload item doesn't exist
                if (item===undefined) {

                    // Create the item
                    item = self.createItem(file);
                }

                // Set the item state as failed
                item.setState("failed");

                // And the message for the item.
                item.setMessage(error.message);
            }

            if (self.uploadItem().length > 0) {

                self.uploadItemGroup().removeClass("empty");
            }
        },

        "{modalBrowserButton} click": function() {

            $Media.browse();
        },

        "{modalDashboardButton} click": function() {

            $Media.hide();
        },

        "{uploadNavigation} activate": function(el, event, key) {

            self.setUploadFolder(key);
        },

        "{self} modalActivate": function(el, event, key) {

            self.setUploadFolder(key);

            if ($Media.browser) {
                self.uploadItemGroup()
                    .toggleClass("blogimage", $Media.browser.mode()=="blogimage");
            }
        },

        removeItem: function(id) {

            var item = self.items[id];

            if (item!==undefined) {

                self.plupload.removeFile(item.file());

                item.element.remove();

                delete self.items[id];
            }

            if (self.uploadItem().length < 1) {

                self.uploadItemGroup().addClass("empty");
            }
        },

        "{clearListButton} click": function() {

            for (id in self.items) {

                self.removeItem(id);
            }
        }
	}}

);

EasyBlog.Controller("Media.Uploader.Item",

    {
        defaultOptions: {
            "{filename}": ".uploadFilename",
            "{progressBar}": ".uploadProgressBar progress",
            "{percentage}": ".uploadPercentage",
            "{status}": ".uploadStatus",
            "{removeButton}": ".uploadRemoveButton",
            "{insertItemButton}": ".insertItemButton",
            "{locateItemButton}": ".locateItemButton",
            "{insertBlogImageButton}": ".insertBlogImageButton"
        }
    },

    // Instance properties
    function(self) { return {

        init: function() {

            var file = self.file();

            self.filename()
                .html(file.name);

            self.setState("queued");
        },

        file: function() {

            var file = $Uploader.plupload.getFile(self.id) || self.originalFile;

            if (file) {

                file.filesize = (file.size===undefined || file.size=="N/A") ? "" : $.plupload.formatSize(self.file.size);
            }

            return file;
        },

        "dblclick": function(el, event) {

            if (event.shiftKey) {
                $Media.console("log", self);
            }
        },

        setProgress: function(val) {

            self.progressBar()
                .attr("value", val);

            self.percentage()
                .html(val);
        },

        setState: function(state) {

            // queued, uploading, failed, done
            self.element
                .removeClass("upload-state-" + self.state)
                .addClass("upload-state-" + state);

            self.state = state;
        },

        setMessage: function(message) {

            self.status()
                .html(message);
        },

        "{removeButton} click": function(el, event) {

            event.stopPropagation();

            $Uploader.removeItem(self.id);
        },

        "{insertItemButton} click": function() {

            $Media.insert(self.meta);
        },

        "{locateItemButton} click": function() {

            $Media.browse(self.meta);
        },

        "{insertBlogImageButton} click": function() {

            // We are getting the raw meta
            var meta = $Library.meta[$Library.getMeta(self.meta).key];

            EasyBlog.dashboard.blogImage.setImage(meta);

            $Media.hide();
        }
    }}

);

EasyBlog.Controller("Media.Uploader.FolderSwitcher",
    {
        defaultOptions: {

            view: {
                treeItemGroup: "media/browser.tree-item-group",
                treeItem     : "media/browser.tree-item"
            },

            "{changeUploadFolderButton}": ".changeUploadFolderButton",
            "{selectFolderButton}": ".selectFolderButton",
            "{treeItemField}"   : ".browserTreeItemField",
            "{treeItemGroup}"   : ".browserTreeItemGroup",
            "{treeItem}"        : ".browserTreeItem"
        }
    },

    function(self) { return {

        init: function() {

            var initialUploadFolder;

            self.promptDialog = $Uploader.promptDialog.get("changeUploadFolderPrompt");

            // Create all places
            $.each($Media.library.places, function(id, place) {

                if (!place.acl.canUploadItem) return;

                place.uploaderTreeItemGroup =
                    self.view.treeItemGroup()
                        .addClass("expanded") // Always expanded
                        .appendTo(self.treeItemField());

                place.uploaderTreeItem =
                    self.view.treeItem({title: place.title})
                        .addClass("loading")
                        .addClass("type-place")
                        .data("place", place)
                        .appendTo(place.uploaderTreeItemGroup);

                if (!initialUploadFolder) {

                    initialUploadFolder = place.id + "|" + DS;

                    $Uploader.setUploadFolder(initialUploadFolder);
                }

                place
                    .done(function(){

                        self.createTreeItem(place.baseFolder());

                        place.uploaderTreeItem
                           .removeClass("loading");
                    });
            });
        },

        treeItems: {},

        createTreeItem: function(meta) {

            var meta = $Library.getMeta(meta),

                treeItem = self.treeItems[meta.key] || (function(){

                    var place = $Library.getPlace(meta.place),

                        parentMeta = $Library.getMeta(meta.parentKey),

                        // Create tree item
                        treeItem = self.treeItems[meta.key] =

                            ((parentMeta) ?
                                self.view.treeItem({title: meta.title})
                                    .addClass("type-folder")
                                    .insertAfter(self.treeItems[parentMeta.key])
                                :
                                place.uploaderTreeItem

                            // Store a reference to the key
                            ).data("key", meta.key);

                        // Remove tree item when meta is removed
                        meta.data.on("removed", function(){
                            self.removeTreeItem(meta);
                        });

                        // Listen to the subfolder for changes
                        meta.data.views
                            .create({group: "folders"})
                            .updated(function(folders) {

                                $.each(folders, function(i, key) {
                                    self.createTreeItem(key);
                                });
                            });

                    return treeItem;
                })();

            return treeItem;
        },

        removeTreeItem: function(meta) {

            var meta = $Library.getMeta(meta),

                treeItem = self.treeItems[meta.key];

            if (treeItem) {

                treeItem.remove();

                var parentTreeItem = self.treeItems[meta.parentKey];

                $Uploader.setUploadFolder(meta.parentKey);
            }
        },

        "{treeItem} click": function(el) {

            self.treeItem().removeClass("active");

            el.addClass("active");
        },

        "{changeUploadFolderButton} click": function() {

            var key = self.currentUploadFolder,

                treeItem = self.treeItems[key] || self.treeItem(":first");

            // Highlght on that tree item
            treeItem.click()

            self.promptDialog.show();
        },

        "{selectFolderButton} click": function() {

            var key = self.treeItem(".active").data("key");

            $Uploader.setUploadFolder(key);

            self.promptDialog.hide();
        }

    }}
);


// controller: end

module.resolve();

});
// require: end

});
// module: end
