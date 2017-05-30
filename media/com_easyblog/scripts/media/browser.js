// module: start
EasyBlog.module("media/browser", function($) {

var module = this;

// require: start
EasyBlog.require()
.library(
	"image",
	"easing",
	"scrollTo"
)
.script(
	"media/browser.item"
)
// .view(
// 	"media/browser",
// 	"media/browser.itemGroup",
// 	"media/browser.item",
// 	"media/browser.treeItemGroup",
// 	"media/browser.treeItem",
// 	"media/browser.paginationPage"
// )
// .language(
// 	'COM_EASYBLOG_MM_CONFIRM_DELETE_ITEM',
// 	'COM_EASYBLOG_MM_CANCEL_BUTTON',
// 	'COM_EASYBLOG_MM_YES_BUTTON',
// 	'COM_EASYBLOG_MM_ITEM_DELETE_CONFIRMATION'
// )
.done(function(){

var $Media, $Library, $Browser, $Uploader, $Prompt, DS;

// controller: start
EasyBlog.Controller("Media.Browser",

	// Class properties
	{
		defaultOptions: {

			view: {
				browser			: "media/browser",
				itemGroup		: "media/browser.item-group",
				item			: "media/browser.item",
				treeItemGroup	: "media/browser.tree-item-group",
				treeItem		: "media/browser.tree-item",
				paginationPage	: "media/browser.pagination-page"
			},

			path: "",

			items: undefined,

			mode: "browse",

			layout: {
				viewMode: "tile",
				tileSize: 0.125,
				scrollToItemDuration: 500,
				scrollToItemEasing: 'swing',
				iconMaxLoadThread: 8
			},

			search: {
				chunkSize: 128,
				chunkDelay: 500
			},

			"{modalHeader}"			: ".modalHeader",
			"{modalToolbar}"		: ".modalToolbar",
			"{modalContent}"		: ".modalContent",
			"{modalFooter}"			: ".modalFooter",
			"{modalPrompt}"			: ".modalPrompt",

			// Also shared with folder hint's .uploaderButton
			"{modalUploaderButton}"	: ".uploaderButton",

			"{header}"	: ".browserHeader",
			"{content}"	: ".browserContent",
			"{footer}"	: ".browserFooter",

			"{treeToggleButton}": ".browserTreeToggleButton",
			"{tileViewButton}"	: ".browserTileViewButton",
			"{listViewButton}"	: ".browserListViewButton",

			"{itemField}"	: ".browserItemField",
			"{itemGroup}"	: ".browserItemGroup",
			"{item}"		: ".browserItem",

			"{treeItemField}"	: ".browserTreeItemField",
			"{treeItemGroup}"	: ".browserTreeItemGroup",
			"{treeItem}"		: ".browserTreeItem",

			"{headerTitle}"		: ".browserTitle",
			"{headerSearch}"	: ".browserSearch",
			"{headerNavigation}": ".browserNavigation",
			"{headerUpload}"	: ".browserUploadButton",

			"{footerStatus}"	: ".browserStatus",
			"{footerMessage}"	: ".browserMessage",

			"{itemActionSet}": ".browserItemActionSet",

			"{itemFieldHints}": ".browserItemField .hints",

			"{browserPagination}"	: ".browserPagination",
			"{currentPage}"			: ".currentPage",
			"{totalPage}"			: ".totalPage",
			"{prevPageButton}"		: ".prevPageButton",
			"{nextPageButton}"		: ".nextPageButton",
			"{pageSelection}"		: ".pageSelection",
			"{paginationPage}"		: ".paginationPage",

			"{searchInput}"  : ".searchInput"
		}
	},

	// Instance properties
	function(self) {

		return {

		init: function() {

			// Globals
			$Media = self.media;
			$Library = $Media.library;
			$Browser = $Media.browser = self;
			DS = $Media.options.directorySeparator;

			self.iconThread = $.Threads({threadLimit: self.options.layout.iconMaxLoadThread});
			self.enqueue = $.Enqueue();

			var flickr = $Library.getPlace('flickr');

			// Render browser UI
			self.element
				.addClass("browser")
				.html(
					self.view.browser({
						canUpload: self.options.acl.canUpload,
						flickrCallback: (flickr) ? flickr.options.callback : '',
						flickrLogin: (flickr) ? flickr.options.login : ''
					})
				);

			// Browser navigation
			self.headerNavigation()
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

			self.modalPrompt()
				.implement(
					EasyBlog.Controller.Media.Prompt,
					{
						controller: self.controllerProps()
					},
					function() {
						$Prompt = self.promptDialog = this;
					}
				);

			// Implement browser actions
			self.element
				.implement(
					EasyBlog.Controller.Media.Browser.Actions,
					{
						controller: self.controllerProps()
					},
					function() {
						self.actions = this;
					}
				);

			self.search = $.debounce(self._search, 500);

			// Set to browse mode
			self.mode("browse");

			// Always revert to browser mode when going back to dashboard
			$Media.element.bind("hideModal", function(){
				self.mode("browse");
			});

			// Set browser layout
			self.viewMode(self.options.layout.viewMode);

			self.setLayout();

			// Bind item group scroll event
			self.on(
				"scroll",
				self.itemField(),
				$.debounce(self["{itemField} scroll"], 250)
			);

			// Create all places
			$.each($Library.places, function(id, place) {

				self.createPlace(place);
			});

			// Determine which place is the initial place,
			// if no intial place was given, automatically select
			// the first place on the list.
			self.activatePlace(self.options.initialPlace || $Library.places[0].id)
				.done(
					self.enqueue(function(place){
						self.activateItem(place.baseFolder());
					})
				);
		},

		controllerProps: function(prop) {

			return $.extend(
			{
				media: $Media

			}, prop || {});
		},

		items: {},

		createPlace: function(place) {

			var place = $Library.getPlace(place);

			place.treeItemGroup =
				self.view.treeItemGroup()
					.appendTo(self.treeItemField());

			place.treeItem =
				self.view.treeItem({title: place.title})
					.addClass("type-place")
					.data("place", place)
					.appendTo(place.treeItemGroup);

			place.itemGroup =
				self.view.itemGroup()
					.appendTo(self.itemField());

			return place;
		},

		activatePlace: function(place) {

			var place = $Library.getPlace(place);

			if (place===undefined) return;

			// Toggle active class on item group
			self.itemGroup().removeClass("active");
			place.itemGroup.addClass("active");

			// Toggle active class on item tree
			self.treeItem().removeClass("active");
			place.treeItem.addClass("active");

			// Create the activator task
			if (!place.activator) {

				place.activator = $.Deferred();
			}

			// If flickr is not authenticated yet
			if (place.id==="flickr" && !place.options.associated) {

				// Show the flickr login prompt
				$Browser.currentFolderStatus("flickr");

				return place.activator;
			}

			if (!place.populated) {

				// Mark as populated so this doesn't run again
				place.populated = true;

				// Add busy indicator
				self.currentFolderStatus("loading");

				var populator = /easysocial|jomsocial|flickr/.test(place.id) ? place.populate() : place.ready;

				populator
					.done(function() {

						// Create base folder
						var baseFolderItem = self.createFolder(place.baseFolder());

						place.activator.resolveWith(place, [place, baseFolderItem]);
					})
					.fail(function() {

						self.currentFolderStatus("error");

						place.activator.rejectWith(place, arguments);
					});
			}

			return place.activator;
		},

		getItem: function(item) {

			// Skip going through all the tests below.
			if (item===undefined) return;

			// Item instance
			if (item instanceof EasyBlog.Controller.Media.Browser.Item) {
				return item;
			}

			// Item key
			if (typeof item === "string") {
				return self.items[item];
			}

			// Meta
			if ($Library.isMeta(item)) {
				return self.items[$Library.getKey(item)];
			}
		},

		createItem: function(meta, options) {

			var meta = $Library.getMeta(meta);

			if (!meta) return;

			// Create item, store to item map, return item.
			return self.items[meta.key] =

				new EasyBlog.Controller.Media.Browser.Item(

					self.view.item({meta: meta}),
					{
						controller: {
							key: meta.key,
							media: $Media
						}
					}
				);
		},

		createFolder: function(meta, options) {

			var meta = $Library.getMeta(meta);

			return self.getItem(meta) || (function(){

				// Create & insert item
				var item = self.createItem(meta);

				var place = item.place(),

					parentFolder = item.parentFolder();

					((parentFolder) ?
						item.element
							.insertAfter(parentFolder.element)
						:
						item.element
							.appendTo(place.itemGroup));

				// Create & insert tree item
				item.treeItem =
					((parentFolder) ?
						self.view.treeItem({title: meta.title})
							.addClass("type-folder")
							.css("marginLeft", 18 * (meta.path.split(DS).length - 1))
							.insertAfter(parentFolder.treeItem)
						:
						place.treeItem

					// Store a reference to the item
					).data("item", item);

				// Listen to the subfolder for changes
				meta.data.views
					.create({group: "folders"})
					.updated(function(folders){
						$.each(folders, function(i, key) {
							self.createFolder(key);
						});
					});

				return item;

			})();
		},

		createFile: function(meta, options) {

			var meta = $Library.getMeta(meta);

			return self.getItem(meta) || self.createItem(meta);
		},

		removeItem: function(item) {

			clearTimeout(self.removeItem.revert);

			var item = self.getItem(item),

				parentFolder = item.parentFolder();

				// Remove item element & handler
				item.remove();

				// Remove treeItem if it exists
				if (item.treeItem) {
					item.treeItem.remove();
				}

				// Delete from entry
				delete self.items[item.key];

			// Don't revert if searching
			if (self.itemField().hasClass("searching")) return;

			self.removeItem.revert = setTimeout(function(){

				if (parentFolder) {
					self.activateItem(parentFolder);
				}

			}, 500);
		},

		focusItem: function(item, alsoActivate) {

			var item = self.getItem(item);

			// If item does not exist, skip.
			if (!item) return;

			if (!alsoActivate) {

				// Set item as current item
				self.currentItem(item);

				// Remove the active class because
				// we just want to focus it, not activate it.
				item.element.removeClass("active");

			} else {

				self.activateItem(item);
			}

			self.scrollTo(item);

			self.trigger("itemFocus", [item]);
		},

		locateItem: function(meta) {

			if (self.itemField().hasClass("searching")) {
				self.clearSearch(true);
			}

			var meta = $Library.getMeta(meta);

			if (!meta) return;

			self.activatePlace(meta.place)
				.done(
					self.enqueue(function() {

						var isFolder = meta.type==="folder",

							item = self.activateItem((isFolder) ? meta : meta.parentKey);

							if (item===undefined) return;

							if (!isFolder) {
								item.handler.locateItem(meta);
							}

							// Quick monkey patch
							var folderView = item.handler.folderView;

							if (folderView) {
								folderView.refresh();
							}
					})
				);
		},

		activateItem: function(item) {

			var item = self.getItem(item);

			// If item does not exist, skip.
			if (!item) return;

			// Set item as current item
			self.currentItem(item);

			// Activating an item will trigger its handler, e.g.
			// for folders it will generate items inside it.
			item.activate();

			self.trigger("itemActivate", [item]);

			return item;
		},

		scrollTo: function(item) {

			var item = self.getItem(item);

			if (!item) return;

			self.itemField()
				.scrollTo(item.element, {
					duration: self.options.layout.scrollToItemDuration,
					easing: self.options.layout.scrollToItemEasing,

					// This means that the item will be scrolled to 10% from the top of the field
					offset: {top: self.itemField().height() * -0.10}
				});
		},

		currentItem: function(item)
		{
			// Get current item.
			var currentItem = self.currentItem.item;

			// If current item has been destroyed,
			if (currentItem && (currentItem._destroyed || !self.items[currentItem.key]))
			{
				// set current item as undefined.
				currentItem = self.currentItem.item = undefined;
			}

			// If this is a setter operation, get the item.
			var item = self.getItem(item);

			// If the new current item does not exist,
			// just return current item.
			if (!item)
			{
				return currentItem;
			}

			// If the new current item has already been destroyed,
			// just return current item.
			if (item._destroyed)
			{
				return currentItem;
			}

			// If previous current item exists,
			if (currentItem)
			{
				// remove its active & focus class.
				currentItem.element.removeClass("active focus");

				// Also, if this item is a file,
				// remove active & focus class from its parent folder.
				if (currentItem.meta().type!=="folder")
				{

					currentItem.parentFolder().element.removeClass("active focus");
				}

				// Also remove active class from place's itemgroup
				currentItem.place().itemGroup.removeClass("active");
			}

			// Add active & focus class to new current item
			item.element.addClass("active focus");

			// Lets see if this item is a folder.
			var isFolder = item.meta().type=="folder";

			// If new current item is file,
			if (!isFolder) {

				// then add a focus class to its parent folder.
				item.parentFolder().element.addClass("focus");
			}

			// Add active class to item group
			item.place().itemGroup.addClass("active");

			// If this a folder, set current folder to the item itself.
			// If this is a file, set current folder to the item's parent folder.
			self.currentFolder(
				(isFolder) ? item : item.parentFolder()
			);

			// Set the navigation to the new current item.
			self.navigation.setPathway(item.key);

			// Set and return new current item.
			return self.currentItem.item = item;
		},

		currentFolder: function(folder) {

			// Get current folder
			var currentFolder = self.currentFolder.folder;

			// If current folder has been destroyed,
			if (currentFolder && currentFolder._destroyed) {

				// set current folder as undefined.
				currentFolder = self.currentFolder.folder = undefined;
			}

			// If this is a setter operation, get the item.
			var folder = self.getItem(folder);

			// If the folder does not exist, return current folder.
			// Also, as getter operation.
			if (!folder) return currentFolder;

			// Add active class to new current folder
			self.treeItem().removeClass("active");
			folder.treeItem.addClass("active");

			// Also expand the place tree
			if (folder.meta().path!==DS) {
				folder.place().treeItemGroup.addClass("expanded");
			}

			// Also refresh view
			if (folder.handler.folderView) {

				if (folder.handler.refreshSeed!==self.folderRefreshSeed) {
					folder.handler.folderView.refresh();
					folder.handler.refreshSeed = self.folderRefreshSeed;
				}
			}

			// Set and return new current folder.
			return self.currentFolder.folder = folder;
		},

		currentFolderStatus: function(status) {

			// Quick monkey patch to prevent double activation (also fixes jomsocial)
			if (self.itemField().hasClass("searching") && !/emptySearch|ready/.test(status)) return;

			var lastStatus = self.currentFolderStatus.lastStatus;

			// Getter
			if (status === undefined) return lastStatus;

			// Setter
			if (typeof status !== "string") return;

			// Remove last status
			if (lastStatus) {
				self.itemField()
					.removeClass(lastStatus);
			}

			// Add new status
			self.itemField().addClass(status);

			return self.currentFolderStatus.lastStatus = status;
		},

		setLayout: function() {

			// Skip if no layout has been set OR current modal is not us.
			if (!$Media.layout || $Media.currentModal!=="browser") return;

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

				self.treeItemField()
					.height(contentHeight);

				self.itemField()
					.height(contentHeight);

				self.itemFieldHints()
					.height(contentHeight);
			}

			self.setItemLayout();

			self.trigger("setLayout");
		},

		setItemStyle: function(force) {

			// If this is not forced, skip setting item style
			// if layout seed hasn't changed yet.
			if (!force) {

				// Get current layout seed
				var seed = $Media.layout;

				// If layout seed matches, no setting of item style is necessary.
				if (self.setItemStyle.seed===seed) return;

				// Set current layout seed
				self.setItemStyle.seed = seed;
			}

			// Set up variables
			var viewMode = self.viewMode(),
				cssRules = {};

			if (viewMode=="tile") {

				var browserItem = "#EasyBlogMediaManager .browser .browserItemField.view-tile .browserItem",
					availableWidth = (function() {
						var testElement = $(document.createElement("DIV")).prependTo(self.itemField()),
							availableWidth = testElement.width();
						testElement.remove();
						return availableWidth;
					})(),
					itemWidth = Math.floor(availableWidth * self.options.layout.tileSize),
					itemHeight = itemWidth - 24;

				cssRules[browserItem] = {
					width: itemWidth + "px",
					height: itemHeight + "px"
				}
			}

			// Get the document head
			var head = document.getElementsByTagName("head")[0];

			// Remove previous stylesheet
			if (self.itemStyle) {
				try {
					head.removeChild(self.itemStyle);
				} catch(e) {};
			}

			// Create new stylesheet
			self.itemStyle = document.createElement("style");
			self.itemStyle.type = "text/css";

			// Generate css text
			var cssText = "";
			$.each(cssRules, function(selector, props) {
				cssText += selector + "{" + $.map(props, function(val, key){ return key + ":" + val; }).join(";") + "}\n";
			});

			// Append css text to stylesheet
			if (self.itemStyle.styleSheet) {
				self.itemStyle.styleSheet.cssText = cssText;
			} else {
				self.itemStyle.appendChild(document.createTextNode(cssText));
			}

			// Append stylesheet to head
			head.appendChild(self.itemStyle);
		},

		setItemLayout: function() {

			// Skip if no layout has been set OR current modal is not us.
			if (!$Media.layout || $Media.currentModal!=="browser") return;

			self.setItemStyle();

			setTimeout(function() {

				var items = [];

				if (self.itemField().hasClass("searching")) {

					// Monkey patch
					if (self.searchItemGroup) {
						items = self.searchItemGroup.find(".browserItem");
					}

				} else {

					// If there's no current folder selected, don't do anything.
					var currentFolder = self.currentFolder();
					if (currentFolder===undefined) return;

					items = currentFolder.childItem();
				}

				if (items.length < 1) return;

				// Drill down
				var itemFieldOffset = self.itemField().offset(),
					item,
					itemOffset,
					j = items.length,
					i = 1;

				if (items.length < 1) return;

				while (Math.abs(j - i) > 1) {
					item = items.eq(i-1);
					itemOffset = item.offset();

					var itemBottom = itemOffset.top - itemFieldOffset.top + item.outerHeight();
					if (itemBottom < 0) {
						i = Math.ceil((j + i) / 2);
					} else {
						j = i;
						i = Math.ceil(j / 2);
					}
				}

				// From the first found visible item,
				// work backwards & forwards until all
				// visible items on the viewport are covered
				if (i===1) i = 0;

				var b = i,
					f = i,
					min = 0,
					max = items.length - 1;
					setLayout = function(i) {
						if (i < min || i > max) return false;
						var item = items.eq(i).data("item");
						if (!item.isVisible()) return false;
						item.setLayout();
					};

					while (true) {
						if (setLayout(b)===false) break;
						b--;
					}

					while (true) {
						if (setLayout(f)===false) break;
						f++;
					}

			}, 0);
		},

		viewMode: function(mode) {

			// Get current view mode
			var currentMode = self.viewMode.mode;

			// If a mode hasn't been set yet, take from options.
			if (!currentMode) {
				currentMode = self.viewMode.mode = self.options.layout.viewMode;
			}

			// Setter operation
			if (mode!==undefined) {

				// Force a seed refresh
				self.setItemStyle.seed = null;

				// Replace view mode
				self.itemField()
					.removeClass("view-" + currentMode)
					.addClass("view-" + mode);

				// Update current view mode
				self.viewMode.mode = currentMode = mode;

				// Set browser layout
				self.setLayout();

				// Scroll to current item (as its position have changed in different view modes)
				var currentItem = self.currentItem();

				if (currentItem!==undefined) {

					self.scrollTo(currentItem);
				}
			}

			// Getter operation
			return currentMode;
		},

		mode: function(mode) {

			// Getter
			if (mode===undefined) return self.mode.currentMode || "browse";

			switch (mode) {
				case "browse":

					self.element
						.removeClass("mode-blogimage")
						.addClass("mode-browse");

					// Quick monkey patch to hide jomsocial & flickr items
					// under blog imagemode.
					$.each($Library.places, function(i, place){
						if (/easysocial|jomsocial|flickr/.test(place.id)) {
							place.treeItemGroup && place.treeItemGroup.show();
							place.itemGroup && place.itemGroup.show();
						}
					});

					break;

				case "blogimage":

					self.element
						.addClass("mode-blogimage")
						.removeClass("mode-browse");

					// Quick monkey patch to hide jomsocial & flickr items
					// under blog imagemode.
					var currentItem = self.currentItem(),
						switchToNearestLocalPlace = false;

					if (currentItem) {
						if (/easysocial|jomsocial|flickr/.test(currentItem.place().id)) {
							switchToNearestLocalPlace = true;
						}
					}

					$.each($Library.places, function(i, place){

						if (/easysocial|jomsocial|flickr/.test(place.id)) {
							place.treeItemGroup && place.treeItemGroup.hide();
							place.itemGroup && place.itemGroup.hide();
						} else {
							if (switchToNearestLocalPlace) {
								switchToNearestLocalPlace = false;
								if (place.treeItem) {
									place.treeItem.click();
								}
							}
						}
					});
					break;
			}

			self.mode.currentMode = mode;
		},

		"{self} itemActivate": function(el, event, item) {

			self.itemActionSet().removeClass("active");

			if (item.meta().type=="folder") {

				self.itemActionSet(".type-folder").addClass("active");

			} else {

				self.itemActionSet(".type-item").addClass("active");
			}
		},

		"{headerNavigation} activate": function(el, event, key) {

			self.activateItem(key);
		},

		"{tileViewButton} click": function(el, event) {

			el.addClass("active")
				.siblings()
				.removeClass("active");

			self.viewMode("tile");
		},

		"{listViewButton} click": function(el, event) {

			el.addClass("active")
				.siblings()
				.removeClass("active");

			self.viewMode("list");
		},

		// Not sure why this is triggered twice
		"{treeItem} click": $.debounce(function(el, event) {

			self.clearSearch(true);

			var item = el.data("item");

			if (el.hasClass("type-place")) {

				var place = el.data("place");

				// If the toggle icon was clicked
				if ($(event.target).hasClass("treeItemToggle")) {

					// Expand the tree group
					place.treeItemGroup.toggleClass("expanded");
				}

				self.activatePlace(place)
					.done(
						self.enqueue(function(place, baseFolder) {

							if (place.id==="jomsocial") {
								place.treeItemGroup.addClass("expanded");
							}

							if (place.id==="easysocial") {
								place.treeItemGroup.addClass("expanded");
							}

							self.activateItem(baseFolder);
						})
					);

				return;
			}

			self.activateItem(item);
		}, 50),

		"{itemField} scroll": function(el, event) {

			self.setItemLayout();
		},

		"{item} click": function(el, event) {

			// Prevents click event from being bubbled back to parent folder item
			event.stopPropagation();

			var item = el.data("item");

			if (item===undefined) return;

			var place = item.place();

			self.activatePlace(place)
				.done(
					self.enqueue(function(place, baseFolder) {

						// Monkey patch to work with search
						// self.activateItem(baseFolder);
						self.activateItem(item);
					})
				);
		},

		"{item} dblclick": function(el, event) {

			// Prevents click event from being bubbled back to parent folder item
			event.stopPropagation();

			var item = el.data("item");

			// #debug:start
			if (event.shiftKey) {
				$Media.console("log", [item]);
				return;
			}
			// #debug:end

			if (item===undefined) return;

			if (item.meta().type=="folder") return;

			if(self.mode.currentMode==='blogimage') {

				if (item.meta().type=="image") {

					// We are getting the raw meta
					var meta = $Library.meta[item.key];

					EasyBlog.dashboard.blogImage.setImage(meta);

					$Media.hide();
				}

			} else {

				$Media.edit(item.key);
			}
		},

		"{modalUploaderButton} click": function() {

			var item = self.currentFolder();

			$Media.upload((item.place().acl.canUploadItem) ? item.key : "");
		},

		"{self} modalActivate": function(el, event, meta, mode) {

			if (mode!==undefined) {

				self.mode(mode);
			}

			var meta = $Library.getMeta(meta) || self.currentItem().meta();

			if (meta) {

				self.locateItem(meta);
			}
		},

		"{prevPageButton} click": function() {
			var folder = $Browser.currentFolder();
			folder.handler.changePage('prev');
		},

		"{nextPageButton} click": function() {
			var folder = $Browser.currentFolder();
			folder.handler.changePage('next');
		},

		// "{pageSelection} change": function(el) {
		// 	var folder = $Browser.currentFolder(),
		// 		page = el.val();

		// 	folder.handler.currentPage(page);
		// },

		"{pageSelection} click": function(el) {
			if(self.paginationPage().length > 1) {
				el.toggleClass('expanded');
			}
		},

		"{paginationPage} click": function(el) {
			if(self.pageSelection().hasClass('expanded') && !el.hasClass('selected')) {
				var page = el.data('page'),
					folder = $Browser.currentFolder();

				folder.handler.isChangingPage = true;

				folder.handler.currentPage(page);
			}
		},

		"{window} click": function(el, event) {

			var className = $(event.target).attr('class');

			if(!/pageSelection|paginationPage/.test(className)) {
				if(self.pageSelection().hasClass('expanded')) {
					self.pageSelection().removeClass('expanded');
				}
			}
		},

		_search: function(keyword) {

			if (!self.itemBeforeSearch) {
				self.itemBeforeSearch = self.currentItem().meta();
			}

			self.element.addClass("searching");

			self.itemField()
				.addClass("searching");

			if (!self.searchItemGroup) {
				self.searchItemGroup =
					self.view.itemGroup()
						.appendTo(self.itemField());
			}

			self.searchItemGroup
				.addClass("active search-mode");

			var timer;

			self.searchView =
				$Library.search(keyword)
					.create({from: 0, to: 300})
					.updated(function(files){

						var l = files.length;

						if (l < 1) {
							timer = setTimeout(function(){
								$Browser.currentFolderStatus("emptySearch");
							}, 500);
							return;
						}

						clearTimeout(timer);
						$Browser.currentFolderStatus("ready");

						for (i=0; i<l; i++) {

							var key = files[i];

							// This either return the newly created file,
							// or the file that has been previously created.
							var file = $Browser.createFile(key);

							file.element
								.appendTo(self.searchItemGroup);
						}

						self.setItemLayout();
					});
		},

		clearSearch: function(cancel) {

			self.folderRefreshSeed = $.uid();

			if (cancel) {
				self.searchInput().val("").blur();
			}

			self.element.removeClass("searching");

			self.itemField()
				.removeClass("searching");

			if (self.searchItems) {
				$.each(self.searchItems, function(i, item){
					$(item).detach();
				});
			}

			if (self.searchItemGroup) {

				self.searchItemGroup
					.find(".browserItem")
					.detach();

				self.searchItemGroup.removeClass("active");
			}

			if (self.searchView) {
				self.searchView.destroy();
			}

			delete self.searchView;

			if (self.itemBeforeSearch) {
				self.locateItem(self.itemBeforeSearch);
			}
		},

		"{searchInput} focusin": function(el) {
			el.parent().addClass("active");

			if ($.trim(el.val())!=="") {
				el.parent().addClass("showCancelButton");
			}
		},

		"{searchInput} focusout": function(el) {

			setTimeout(function(){
				if ($.trim(el.val())==="") {
					el.parent().removeClass("active showCancelButton");
 				}
			}, 50);
		},

		"{searchInput} keyup": function(el) {

			var keyword = $.trim(el.val());

			if (keyword==="") {
				el.parent().removeClass("showCancelButton");
				self.clearSearch();
				delete self.itemBeforeSearch;
				return;
			}

			el.parent().addClass("showCancelButton");

			self.search(keyword);
		}
	}}

);
// controller: end

EasyBlog.Controller("Media.Browser.Actions",
	{
		defaultOptions: {

			// Item actions
			"{customizeItemButton}": ".customizeItemButton",
			"{insertAsGalleryButton}": ".insertAsGalleryButton",
			"{insertItemButton}": ".insertItemButton",
			"{insertBlogImageButton}": ".insertBlogImageButton",

			// Create folder prompt
			"{createFolderButton}"        : ".createFolderButton",
			"{confirmCreateFolderButton}" : ".createFolderPrompt .confirmCreateFolderButton",
			"{folderPath}"                : ".createFolderPrompt .folderPath",
			"{folderCreationPath}"        : ".createFolderPrompt .folderCreationPath",
			"{folderInput}"               : ".createFolderPrompt .folderInput",
			"{folderCreationFailedReason}": ".createFolderPrompt .folderCreationFailedReason",

			// Remove item prompt
			"{removeItemButton}"       : ".removeItemButton",
			"{removeItemFilename}"     : ".removeItemPrompt .removeItemFilename",
			"{confirmRemoveItemButton}": ".confirmRemoveItemButton",
			"{removeItemFailedReason}" : ".removeItemPrompt .removeItemFailedReason",

			// Flickr login
			"{flickrLoginButton}": ".flickrLoginButton",

			// Search
			"{cancelSearchButton}": ".cancelSearchButton",

			// Error prompt
			"{retryPopulateButton}": ".retryPopulateButton"
		}
	},
	function(self) { return {

		init: function() {

		},

		"{self} itemActivate": function(el, event, item) {

			self.item = item;

			var acl = item.place().acl;

			// If the current file can't be removed, hide remove item button.
			self.removeItemButton()
				.toggle(acl.canRemoveItem);

			self.createFolderButton()
				.toggle(acl.canCreateFolder);

			// Show insert blog image button if we are selecting blog image
			self.insertBlogImageButton().toggle($Browser.mode()==="blogimage" && item.meta().type==="image");
		},

		// Item actions
		"{customizeItemButton} click": function() {

			$Media.edit(self.item.key);
		},

		"{insertAsGalleryButton} click": function() {

			$Media.insert(self.item.key);
		},

		"{insertItemButton} click": function() {

			$Media.insert(self.item.key);
		},

		"{insertBlogImageButton} click": function() {

			// We are getting the raw meta
			var meta = $Library.meta[self.item.key];

			EasyBlog.dashboard.blogImage.setImage(meta);

			$Media.hide();
		},

		// Create folder prompt
		"{createFolderButton} click": function() {

			$Prompt.get("createFolderPrompt")
				.show()
				.state("default");

			var currentFolder = $Browser.currentFolder();

			// Set folder path
			self.folderPath()
				.html(currentFolder.meta().friendlyPath);

			self.folderInput()
				.focus()[0]
				.select();
		},

		"{folderInput} keyup": function(el, event) {

			if (event.keyCode==13) {
				self.confirmCreateFolderButton().click();
			}
		},

		"{confirmCreateFolderButton} click": function() {

			var folderName = $.trim(self.folderInput().val());

			// Don't do anything if folder name not given
			if (folderName==="") return;

			// Get friendly path of the new folder
			var parentMeta = $Browser.currentFolder().meta(),
				path = parentMeta.friendlyPath + DS + folderName;

				// and set it to the folder creation path
				self.folderCreationPath()
					.html(path);

			// Show progress state
			var createFolderPrompt = $Prompt.get("createFolderPrompt");
				createFolderPrompt.state("progress");

			// Create folder on server
			$Library.createFolder(parentMeta, folderName)
				.done(function(meta){

					var item = $Browser.createFolder(meta);

					createFolderPrompt.hide();

					$Browser.activateItem(item);
				})
				.fail(function(message){

					self.folderCreationFailedReason()
						.html(message);

					createFolderPrompt.state("fail");
				});
		},

		// Remove item prompt
		"{removeItemButton} click": function() {

			self.removeItemFilename()
				.html(self.item.meta().title);

			// Show prompt
			$Prompt.get("removeItemPrompt")
				.show()
				.state("default");
		},

		"{confirmRemoveItemButton} click": function(el) {

			var removeItemPrompt = $Prompt.get("removeItemPrompt");

			removeItemPrompt.state("progress");

			$Library.removeRemoteMeta(self.item.key)
				.done(function(){

					removeItemPrompt.hide();
				})
				.fail(function(message){

					self.removeItemFailedReason()
						.html(message);

					removeItemPrompt.state("fail");
				});
		},

		"{flickrLoginButton} click": function(el) {

			var login = el.data("login"),

				callback = el.data("callback"),

				activateFlickrPlace = $Browser.enqueue(function(){

					$Browser.activatePlace("flickr")
						.done(
							$Browser.enqueue(function(place, baseFolder){

								$Browser.activateItem(baseFolder);
							})
						);

				});

				window[callback] = function() {

					var place = $Library.getPlace("flickr");

					// Flickr is now associated
					place.options.associated = true;

					// Reactivate flickr place
					activateFlickrPlace();
				}

			window.open(login, "Flickr Login", 'scrollbars=no, resizable=no, width=650, height=700');
		},

		"{cancelSearchButton} click": function() {
			$Browser.clearSearch(true);
		},

		"{retryPopulateButton} click": function() {

			var place = self.item.place();
			delete place.activator;
			place.populated = false;
			place.treeItem.click();
		}
	}}
);

// controller: end

module.resolve();

});
// require: end

});
// module: end
