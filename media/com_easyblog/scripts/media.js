EasyBlog.module("media", function($){

	var module = this;


	var htmlentity = function(str) {

		return $("<div>").text(str)
					.html()
					.replace(/&/g, '&amp;')
					.replace(/"/g, '&quot;')
					.replace(/'/g, '&apos;');
	}

	var $Media, $Library, $Browser, $Uploader, DS;

	//
	// 1. Create media manager controller.
	//
	EasyBlog.Controller("Media",

		{
			defaultOptions: {

				debug: {
					logging: EasyBlog.debug,

					itemVisibility: false,

					delayConfiguration: 0,
					delayCommon: 0,
					delayBrowser: 0,
					delayUploader: 0,
					delayEditor: 0
				},

				ui: "#EasyBlogMediaManagerUI",


				overlay: {
					background: "black",
					opacity: 0
				},

				modal: {
					size: 0.9
				},

				recentActivities: {
					hideAfter: 3000
				},

				"{modalGroup}"    : ".mediaModalGroup",
				"{modal}"         : ".mediaModal",

				"{loaderModal}"   : ".loaderModal",
				"{uploaderModal}" : ".uploaderModal",
				"{browserModal}"  : ".browserModal",
				"{editorModal}"   : ".editorModal",

				"{modalContent}"  : ".modalContent",

				"{overlay}": ".media-overlay",

				"{modalDashboardButton}": ".dashboardButton",

				"{assetItem}": ".assetItem"
			}
		},

		function(self) { return {

			console: function(method, args) {

				if (!self.options.debug.logging) return;

				var console = window.console;

				if (!console) return;

				var method = console[method];

				if (!method) return;

				// Normal browsers
				if (method.apply) {
					method.apply(console, args);
				// IE
				} else {
					method(args.join(" "));
				}
			},

			assets: {},

			getAsset: function(name) {

				if (self.assets[name]===undefined) {

					var asset = self.assets[name] = $.Deferred();

					asset
						.done(function(){

							self.assetItem(".asset-type-"+name)
								.removeClass("loading done failed")
								.addClass("done");
						})
						.fail(function(){
							self.assetItem(".asset-type-"+name)
								.removeClass("loading done failed")
								.addClass("fail");
						});
				}

				return self.assets[name];
			},

			createAsset: function(name, factory, delay) {

				var asset = self.getAsset(name);
					asset.factory = factory;

				setTimeout(function(){
					asset.factory && asset.factory(asset);
				}, delay);

				return asset;
			},

			init: function() {

				// Globals
				$Media = self;

				self.IE = (function(){

				    var undef,
				        v = 3,
				        div = document.createElement('div'),
				        all = div.getElementsByTagName('i');

				    while (
				        div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
				        all[0]
				    );

				    return v > 4 ? v : undef;

				}());


				if( typeof( tinyMCE ) != 'undefined' )
				{

					// Caret position fix
					if (tinyMCE && tinyMCE.isIE && self.IE==9) {

						// Wait for TinyMCE to be ready
						var waitForTinyMCE = setInterval(function(){

							var editor = tinyMCE.editors.write_content;

							if (!editor) return;

							var events = "keydown.mediaManager mousedown.mediaManager focus.mediaManager";

							$(editor.contentWindow)
								// Just in case it was binded
								.off(events)
								.on(events, function(){
									self.bookmark = {
										element: editor.selection.getEnd(),
										range: editor.selection.getBookmark(1).rng
									}
								});

							clearInterval(waitForTinyMCE);

						}, 500);
					}
				}

				// Remember the document body's original overflow property
				// Used with .hide();
				self.originalBodyOverflow = $("body").css("overflow");

				// When "module/configuration" gets resolved,
				// file & folder indexing kicks in immediately
				// without waiting for the other assets to resolve.
				self.createAsset(
					"configuration",
					function(asset) {
						EasyBlog.module("media/configuration")
							.done(function() {
								var options = this;
								self.initialize(this);
								asset.resolve();
							})
							.fail(function(){
								asset.reject();
							});
					},
					self.options.debug.delayConfiguration
				);

				// Stylesheet & navigation is given priority because it needs to be
				// ready before uploader can initialize. And we need
				// uploader to be up & ready as fast as possible.
				self.createAsset(
					"common",
					function(asset) {
						EasyBlog.require()
							.script(
								"media/navigation"
							)
							.view(
								"media/recent.item",

								// Browser
								"media/browser",
								"media/browser.item-group",
								"media/browser.item",
								"media/browser.tree-item-group",
								"media/browser.tree-item",
								"media/browser.pagination-page",

								// Uploader
							    "media/browser.uploader",
							    "media/browser.uploader.item",

							    // Editor
								"media/editor",
								"media/editor.viewport",

								// Navigation
								"media/navigation.item",
								"media/navigation.itemgroup"
							)
							.language(
								"COM_EASYBLOG_MM_UNABLE_TO_FIND_EXPORTER",
								"COM_EASYBLOG_MM_GETTING_IMAGE_SIZES",
								"COM_EASYBLOG_MM_UNABLE_TO_RETRIEVE_VARIATIONS",
								"COM_EASYBLOG_MM_ITEM_INSERTED",
								"COM_EASYBLOG_MM_UPLOADING",
								"COM_EASYBLOG_MM_UPLOADING_STATE",
								"COM_EASYBLOG_MM_UPLOADING_PENDING",
								"COM_EASYBLOG_MM_UPLOAD_COMPLETE",
								"COM_EASYBLOG_MM_UPLOAD_PREPARING",
								"COM_EASYBLOG_MM_UPLOAD_UNABLE_PARSE_RESPONSE",
								"COM_EASYBLOG_MM_UPLOADING_LEFT",
								"COM_EASYBLOG_MM_CONFIRM_DELETE_ITEM",
								"COM_EASYBLOG_MM_CANCEL_BUTTON",
								"COM_EASYBLOG_MM_YES_BUTTON",
								"COM_EASYBLOG_MM_ITEM_DELETE_CONFIRMATION"
							)
							.done(function() {
								asset.resolve();
							})
							.fail(function(){
								asset.reject();
							});
					},
					self.options.debug.delayCommon
				);

				// Load all uploader dependencies NOW so we can shave off
				// that extra 1-2 seconds that was used to wait for
				// "media/uploader" module to resolve.
				self.createAsset(
					"uploader",
					function(asset) {
						$.when(
							self.getAsset("configuration"),
							self.getAsset("common"),
							EasyBlog.require().script("media/uploader").done()
						)
						.done(function() {
							var modal = self.createModal("uploader");
							$Uploader = modal.controller = new EasyBlog.Controller.Media.Uploader(modal.element, self.options.uploader);
							asset.resolve();
						})
						.fail(function(){
							asset.reject();
						});
					},
					self.options.debug.delayUploader
				);

				// Browser
				self.createAsset(
					"browser",
					function(asset) {
						$.when(
							self.getAsset("configuration"),
							self.getAsset("common"),
							EasyBlog.require().script("media/browser").done()
						)
						.done(function(){
							var modal = self.createModal("browser");
							$Browser = modal.controller = new EasyBlog.Controller.Media.Browser(modal.element, self.options.browser);
							asset.resolve();
						})
						.fail(function(){
							asset.reject();
						});
					},
					self.options.debug.delayBrowser
				);

				// Editor
				self.createAsset(
					"editor",
					function(asset) {
						$.when(
							self.getAsset("browser"),
							EasyBlog.require().script("media/editor").done()
						)
						.done(function(){
							var modal = self.createModal("editor");
							modal.controller = new EasyBlog.Controller.Media.Editor(modal.element, self.options.editor);
							asset.resolve();
						})
						.fail(function(){
							asset.reject();
						});
					},
					self.options.debug.delayEditor
				);


				// Debounce self.setLayout(). Debouncing setLayout() is only useful when
				// media explorer is doing resource-intensive thumbnail resizing.
				self.setLayout = $.debounce(self._setLayout, 200);
			},

			initialize: function(options) {

				// Inject subcontroller options with back-reference to media
				var media = {controller: {media: self}},
					options = $.extend(true, options, {browser: media, uploader: media, library: media, editor: media, exporter: media});

				// Reload configuration
				self.update(options);

				// Globals
				DS = options.directorySeparator;

				// Render media manager UI
				var UI = $(self.options.ui);
				self.element.append(UI.html());
				UI.remove();

				// Set up overlay
				self.overlay()
					.css(self.options.overlay);

				// Set up loader
				self.loader = new EasyBlog.Controller.Media.Loader(self.createModal("loader"), {controller: {media: self}});

				// Implement media library
				self.element
					.implement(
						EasyBlog.Controller.Media.Library,
						self.options.library,
						function(){

						}
					);

				// Implement media exporter
				self.element
					.implement(
						EasyBlog.Controller.Media.Exporter,
						self.options.exporter
					);

				module.resolve();
			},

			// This will be replaced with a debounced function later on.
			setLayout: function() {

				self._setLayout();
			},

			// We are retaining direct access to non-debounced setLayout()
			// because we might need it sometimes.
			_setLayout: function() {

				self.layout = (self.element.hasClass("active")) ? $.uid() : null;

				if (self.layout) {
					self.setModalLayout();
				}

				return self.layout;
			},

			modals: {},

			createModal: function(name) {

				var element = self.modal("."+name+"Modal");

				if (element.length < 1) {
					element = $('<div class="mediaModal"></div>').addClass(name+"Modal").appendTo(self.modalGroup());
				}

				return self.modals[name] = {
					name: name,
					element: element
				}
			},

			activateModal: function(name, args) {

				if (self.modals[name]===undefined) {

					self.loader
						.when(self.assets[name])
						.done(function(){
							setTimeout(function(){
								self.activateModal(name, args);
							}, 1000);
						});

					return;
				}

				// If modal to activate is current modal, skip.
				if (self.currentModal===name) return true;

				var modal = self.modals[name];

				if (!modal) return false;

				self.deactivateModal(self.currentModal);

				self.currentModal = name;

				// This will also set modal layout
				self.show();

				// Trigger "modalActivate event"
				var controller = modal.controller;

				if (controller) {
					controller.trigger("modalActivate", args);
				}

				return true;
			},

			deactivateModal: function(name, args) {

				var modal = self.modals[name];

				if (!modal) return false;

				// Trigger "modalActivate event"
				var controller = modal.controller;

				if (controller) {

					try {
						controller.trigger("modalDeactivate", args);
					} catch (e) {
						console.error(e);
					}
				}

				modal.element.removeClass("active");

				self.currentModal = undefined;

				return true;
			},

			setModalLayout: function() {

				clearTimeout(self.setModalLayout.task);

				var modal = self.modals[self.currentModal];

				// Skip if modal does not exist, or no visible modal.
				if (!modal) return;

				// If no layout has been set, set it first.
				// setModalLayout will eventually be called again.
				if (!self.layout) return self._setLayout();

				// Show the modal
				modal.element.addClass("active");

				// Set the layout modal
				var controller = modal.controller;

				if (controller && $.isFunction(controller.setLayout)) {

					// This fixes an issue where the modal requires
					// more time than expected to paint on the screen.
					// Whenever the modal is painted on the screen,
					// its top/left is never 0.

					var task = function() {

						var offset = controller.element.offset();

						if (offset.top===0 || offset.left===0) {
							self.setModalLayout.task = setTimeout(task, 50);
						} else {
							controller.setLayout();
						}
					};

					task();
				}
			},

			show: function() {

				// This prevents scrolling of page body
				// $("body").css("overflow", "hidden");

				self.element
					.addClass("active");

				self._setLayout();

				// Conflict with certain mootools version
				// self.trigger("show");

				self.trigger("showModal");
			},

			hide: function() {

				self.element
					.removeClass("active");

				self.deactivateModal(self.currentModal);

				// $("body").css("overflow", self.originalBodyOverflow);

				// Conflict with certain mootools version
				// self.trigger("hide");

				self.trigger("hideModal");
			},

			"{overlay} click": function() {
				self.hide();
			},

			"{window} resize": function() {
				self.setLayout();
			},

			"{modalDashboardButton} click": function(el, event) {

				// #debug:start
				if (event.shiftKey) return self.console("dir", [self]);
				// #debug:end

				self.hide();
			},

			// Sugar methods
			upload: function() { self.activateModal("uploader", arguments); },
			browse: function() { self.activateModal("browser", arguments); },
			edit  : function() { self.activateModal("editor", arguments); }
		}}
	);

	EasyBlog.Controller("Media.Loader",

		{
			defaultOptions: {

			}
		},

		function(self) { return {

			init: function() {

			},

			when: function() {

				self.media.activateModal("loader");

				var queue = $.when.apply(null, arguments),

					onQueueDone = queue.done; // Keep an original copy of done method

					queue.id = $.uid();

				queue.done = function(callback) {

					onQueueDone(function(){

						// If we are still waiting for it
						if (self.currentQueueId==queue.id) {

							// then execute the callback
							callback && callback();
						}
					});
				}

				self.currentQueueId = queue.id;

				return queue;
			},

			"{self} hide": function() {

				self.currentQueueId = null;
			}
		}}
	);

	EasyBlog.Controller("Media.Prompt",

		{
			defaultOptions: {

				"{dialog}": ".modalPromptDialog",
				"{cancelButton}": ".promptCancelButton"
			}
		},

		function(self){ return {

			init: function() {

			},

			get: function(name) {

				var dialog = self.dialog("." + name);

				return self.instantiate(dialog);
			},

			instantiate: function(dialog) {

				var methods = {

					element: dialog,

					show: function() {

						dialog.addClass("active");

						self.element.addClass("active");

						return methods;
					},

					hide: function() {

						dialog.removeClass("active");

						self.element.removeClass("active");

						return methods;
					},

					state: function(state) {

						var lastState = dialog.data("lastPromptState");

						if (state===undefined) {

							return lastState;
						}

						var getStateElement = function(state) {
								return dialog.find(".promptState" + ".state-" + state);
							},
							currentState = getStateElement(state),
							lastState = getStateElement(lastState);

						if (currentState.length < 1) {
							return;
						}

						lastState.removeClass("active");

						currentState.addClass("active");

						dialog.data("lastPromptState", state);

						return methods;
					}
				}

				return methods;
			},

			hideAll: function() {

				self.dialog().removeClass("active");

				self.element.removeClass("active");
			},

			"{cancelButton} click": function() {

				self.hideAll();
			}

		}}
	);

	EasyBlog.Controller("Media.Library",

		{
			defaultOptions: {
				// options for managing indexing of metas here

				places: [],

				place: {
					files: {},
					acl: {
						canCreateFolder: false,
						canUploadItem: false,
						canRenameItem: false,
						canRemoveItem: false,
						canCreateVariation: false,
						canDeleteVariation: false
					},
					populateImmediately: false
				}
			}
		},

		function(self) { return {

			init: function() {

				// Register itself to media
				self.media.library = self;

				$.each(self.options.places, function(i, place) {
					self.addPlace(place);
				});
			},

			places: {},

			getPlace: function(place) {

				// Skip going through all the tests below.
				if (!place) return;

				// Place (test using acl property)
				if (place.acl) {
					return place;
				}

				// Place ID or Key
				if (typeof place==="string") {
					return self.places[place.split("|")[0]];
				}
			},

			addPlace: function(place) {

				var place = self.places[place.id] = $.extend(
						{
							tasks: $.Threads({threadLimit: 1}),

							ready: $.Deferred(),

							baseFolder: function() {
								return self.getMeta(place.id + "|" + self.media.options.directorySeparator);
							}
						},
						self.options.place,
						place
					);

					place.done = place.ready.done;

					place.fail = place.ready.fail;

					place.always = place.ready.always;


				var importJSON = function(data) {

					// When tree is not populated, it is an empty object.
					if ($.isEmptyObject(data)) return;

					// When tree is a string, it might be json string.
					if (typeof data === "string") {

						// Try to eval it.
						try { data = $.parseJSON(data); } catch(e) {}
					}

					return (typeof data === "object") ? data : undefined;
				}

				// Import initial file tree
				place.files = importJSON(place.files);

				if (place.files) {

					place.tasks.add(function(){
						self.importMeta(place.files);
						place.ready.resolve(place);
					});
				};

				place.populate = function() {

					if (place.populate.task!==undefined) return place.populate.task;

					place.populate.task = $.Deferred();

					// JomSocial & Flickr goes directly to getFileTree,
					// User & Shared folder getFolderTree first.
					place.populate[
						(/easysocial|jomsocial|flickr/.test(place.id) || place.files) ? "getFileTree" : "getFolderTree"
					]();

					return place.populate.task;
				};

				place.populate.getFolderTree = function() {

					// Get final folder tree
					return self.getRemoteMeta({place: place.id, foldersOnly: 1})
								.done(function(meta){

									// Import the folder tree
									place.tasks.add(function(){

										// #debug:start
										// var profiler = "Importing final folder tree for " + place.id;
										// self.media.console("time", [profiler]);
										// #debug:end

										self.importMeta(meta);
										place.ready.resolve(place);

										// #debug:start
										// self.media.console("timeEnd", [profiler]);
										// #debug:end
									});

									place.populate.getFileTree();
								})
								.fail(function(){

									place.populate.task.reject(place);
									delete place.populate.task;

									place.ready.reject(place);
								});
				};

				place.populate.getFileTree = function() {

					// Get final file tree
					return self.getRemoteMeta({place: place.id})
								.done(function(meta){

									// Import the folder tree
									place.tasks.add(function(){

										// #debug:start
										// var profiler = "Importing final file tree for " + place.id;
										// self.media.console("time", [profiler]);
										// #debug:end

										self.importMeta(meta);
										place.ready.resolve(place);
										place.populate.task.resolve(place);

										// #debug:start
										// self.media.console("timeEnd", [profiler]);
										// #debug:end
									});
								})
								.fail(function(){

									place.populate.task.reject(place);
									delete place.populate.task;
								});
				};

				if (place.populateImmediately) {
					place.populate();
				}

				return place;
			},

			meta: {},

			metadata: {}, // Extended data attribute for meta

			isMeta: function(meta) {

				return !(meta===undefined || !$.isPlainObject(meta) || $.isEmptyObject(meta));
			},

			get: function(key) {

				if (!self.meta.hasOwnProperty(key)) return;

				// Create a clone of the meta, keeping the original intact
				var meta = $.extend({}, self.meta[key]);

					// Extend meta with additional data attributes
					meta.data = self.metadata[key];

				return meta;
			},

			getKey: function(meta) {
				return (meta) ? meta.place + "|" + meta.path : null;
			},

			getParentKey: function(meta) {

				if (!meta) return;

				var key   = (typeof meta==="string") ? meta : (meta.key || self.getKey(meta)),
					start = key.indexOf(DS),
					end   = key.lastIndexOf(DS);

				return (end===key.length-1) ? undefined : key.substring(0, end + ((start===end) ? 1 : 0));
			},

			getMeta: function(meta) {

				// Skip going through all the tests below.
				if (!meta) return;

				// Meta
				if (self.isMeta(meta)) {

					// Try to get the updated meta,
					// if it doesn't work, just return the existing meta.
					return self.get(self.getKey(meta)) || meta;
				}

				// Key
				if (typeof meta==="string") {
					return self.get(meta); // meta == key
				}
			},

			getRemoteMeta: function(options) {

				var task = $.Deferred();
					task.retry = 0;

				var defaultOptions = {
						path: self.media.options.directorySeparator,
						retry: 3,
						retryAfter: 1000,
						variation: 0,
						foldersOnly: 0
					},
					options = $.extend(defaultOptions, options);

				// Don't do anything if place is not given
				if (options.place===undefined) {
					return task.rejectWith(task, "Error: Place not given!");
				}

				var loadRemoteMeta = (function() {

					task.loader =
						EasyBlog.ajax(
							"site.views.media.getMeta",
							options,
							{
								success: function(data) {
									task.resolveWith(task, arguments);
								},

								// Server-side error
								fail: function() {
									task.rejectWith(task, arguments);
								},

								// Network error
								error: function() {
									task.retry++;
									if (task.retry < options.retry) {
										loadRemoteMeta();
									} else {
										task.rejectWith(task, arguments);
									}
								}
							}
						);

					return arguments.callee;
				})();

				return task;
			},

			getMetaVariations: function(meta) {

				var meta = self.getMeta(meta);

				if (meta===undefined) return;

				return self.getRemoteMeta({place: meta.place, path: meta.path, variation: 1})
						   .done(function(meta){
						   		self.addMeta(meta);
						    });
			},

			removeMetaVariation: function(meta, variationName) {

				var meta = self.getMeta(meta);

				if (meta===undefined) return;

				if (meta.variations===undefined) return;

				var variation;

				for (var i=0; i<meta.variations.length; i++) {

					if (meta.variations[i].name===variationName) {

						variation = meta.variations[i];

						meta.variations.splice(i, 1);

						break;
					}
				};

				return variation;
			},

			addMeta: function(meta) {

				var key = self.getKey(meta),
					existingMeta = self.getMeta(key);

				// If the meta passed in is the meta that we already have,
				// just return the existing meta.
				if (existingMeta && meta.hash===existingMeta.hash) return existingMeta;

				// Create meta
				meta.key   = key;
				meta.hash  = $.uid();
				meta.group = (meta.type=="folder") ? "folders" : "files";

				// Store parent key if this is not the top level folder
				meta.parentKey = self.getParentKey(meta);

				// Add friendly path
				var place = self.getPlace(meta.place);

				meta.friendlyPath =
					(meta.path===DS) ?
						place.title :
						place.title + meta.path.substring(meta.path.indexOf(DS), meta.path.length);

				// Store it to our meta library
				self.meta[key] = meta;

				// Create metadata
				var data = meta.data = (self.metadata[key] || (self.metadata[key] = $.eventable({})));

				// Additional metadata for folder type
				if (meta.type=="folder") {

					data.files   = data.files   || {};
					data.folders = data.folders || {};
					data.views   = data.views   || self.createMetaView(meta);
				}

				// For new meta, add to parent meta's view.
				if (!existingMeta) {

					var parentMeta = self.getMeta(meta.parentKey);

					if (parentMeta) {
						parentMeta.data.views.addMeta(meta);
					}

				// For existing meta, fire update event.
				} else {

					// Ensure events don't slow down adding of large list of metas
					setTimeout(function(){ meta.data.fire("updated", meta); }, 0);
				}

				return meta;
			},

			removeMeta: function(meta) {

				var meta = self.getMeta(meta);

				if (meta===undefined) return;

				if (meta.type==="folder") {

					var folders = meta.data.folders,
						files = meta.data.files;

					for (key in folders) {
						self.removeMeta(key);
					}

					for (key in files) {
						self.removeMeta(key);
					}
				}

				// If this is not base folder
				if (meta.parentKey) {

					// Remove meta from parent
					var parentMeta = self.getMeta(meta.parentKey);

					if (parentMeta) {
						parentMeta.data.views.removeMeta(meta);
					}

					delete self.meta[meta.key];

					setTimeout(function(){ meta.data.fire("removed", meta); }, 0);
				}

				return meta;
			},

			removeRemoteMeta: function(meta) {

				var task = $.Deferred();

				var meta = self.getMeta(meta);

				if (meta===undefined) {
					return task.rejectWith(task, "The file does not exist in the media library.");
				}

				task.loader =
					EasyBlog.ajax(
						"site.views.media.delete",
						{
							place: meta.place,
							path: meta.path
						},
						{
							success: function() {

								self.removeMeta(meta);

								// Return the meta which is no longer in the library
								task.resolveWith(task, [meta]);
							},

							// Server-side error
							fail: function() {

								task.rejectWith(task, arguments);
							},

							// Network error
							error: function(xhr, errorStatus) {

								task.rejectWith(task, [errorStatus])
							}
						});

				return task;
			},

			createFolder: function(meta, name) {

				var task = $.Deferred();

				// Don't do anything if parent meta not found.
				var meta = self.getMeta(meta),
					place = meta.place;

				if (meta===undefined || meta.type!=="folder") {
					return task.rejectWith(task, "Parent folder was not found.");
				}

				task.loader =
					EasyBlog.ajax(
						"site.views.media.createFolder",
						{
							place: place,
							path : meta.path + DS + name
						},
						{
							success: function(meta) {

								// #hack: Restore place property
								meta.place = place;
								var meta = self.addMeta(meta);

								task.resolveWith(task, [meta]);
							},

							// Server-side error
							fail: function() {

								task.rejectWith(task, arguments);
							},

							// Network error
							error: function(xhr, errorStatus) {

								task.rejectWith(task, [errorStatus])
							}
						}
					);

				return task;
			},

			importMeta: function(meta, recursive) {

				if (meta===undefined) return;

				if (meta.type!=="folder") return;

				if (recursive===undefined) recursive = true;

				// Remove the contents property
				// before adding into our meta library.
				var contents = meta.contents,
					length = contents.length;
					delete meta.contents;

					// Temporary hack
					contents.reverse();

				// If the folder meta was created before,
				// the update event is triggered by the parent.
				var folder = self.addMeta(meta),
					 data  = folder.data,
					_data  = {files: {}, folders: {}};

				var i = 0;

				while (i < length) {

					var meta  = self.addMeta(contents[i]),
						key   = meta.key,
						group = meta.group;

					_data[group][key] = delete data[group][key];

					i++;
				}

				for (key in data.files) {
					self.removeMeta(key);
				}

				for (key in data.folders) {
					self.removeMeta(key);
				}

				// Update to the new set of data
				data.folders = _data.folders;
				data.files   = _data.files;

				if (recursive) {
					for (key in data.folders) {
						self.importMeta(self.getMeta(key), recursive);
					}
				}

				return folder;
			},

			createMetaView: function(meta) {

				var view = {

					meta: meta,

					addMeta: function(meta) {

						for (mode in view.modes) {
							var viewMap = view.modes[mode][meta.group];
							viewMap && viewMap.add(meta);
						}
					},

					removeMeta: function(meta) {

						for (mode in view.modes) {
							var viewMap = view.modes[mode][meta.group];

							viewMap && viewMap.remove(meta);
						}
					},

					create: function(options) {

						var defaultOptions = {
							from: 0,
							to: 1024,
							mode: "dateModified",
							group: "files"
						};

						// Create monitor
						var monitor = $.Callbacks("unique memory");

						$.extend(

							monitor,

							defaultOptions,

							options,

							{
								uid: $.uid(),

								select: function(options) {

									// Update options
									$.extend(monitor, options);

									// Deregister from the previous map
									if (monitor.map) {
										delete monitor.map.monitors[monitor.uid];
									}

									monitor.map = view.modes[monitor.mode][monitor.group];

									// Register to the new map
									monitor.map.monitors[monitor.uid] = monitor;

									// #debug:start
									// self.media.console("log", ["Monitoring " + monitor.group + " in " + meta.key, monitor]);
									// #debug:end

									return monitor.refresh();
								},

								updated: monitor.add,

								refresh: function() {

									return monitor.fire(monitor.map.slice(monitor.from, monitor.to));
								},

								destroy: function() {

									monitor.disable();

									if (monitor.map) {

										delete monitor.map.monitors[monitor.uid];
									}

									return monitor;
								}
							}
						);

						return monitor.select();
					}
				};

				// Extend view with view modes
				self.createViewModes(view);

				return view;
			},

			createViewModes: function(view) {

				// TODO: Make this extensible for other types of sort maps.
				view.modes = {
					dateModified: {
						folders: self.createViewMap(view, true), // Monkey patch
						files: self.createViewMap(view)
					}
				};

				return view;
			},

			// TODO: When createViewModes is extensible,
			//       this is only part of dateModified mode.
			createViewMap: function(view, folderGroup) {

				var map = $.extend([], {

					task: $.Threads({threadLimit: 1}),

					affectedIndex: [],

					add: function(meta) {

						map.task.add(function() {
							// TODO: Proper date modified insertion
							map.unshift(meta.key);
							map.affectedIndex.push(0);

							// Monkey patch to show folder tree in alphabetical order
							if (folderGroup) {
								map.sort().reverse();
							}

							map.refreshMonitor();
						});
					},

					remove: function(meta) {

						map.task.add(function() {

							var key = meta.key,
								i = map.length,
								position;

							while (i--) {
								if (map[i]===key) {
									position = i;
									break;
								}
							}

							if (position===undefined) return;

							map.splice(i, 1);
							map.affectedIndex.push(i);

							map.refreshMonitor();
						});
					},

					monitors: {},

					refreshMonitor: function() {

						clearTimeout(map.refreshMonitor.timer);

						map.refreshMonitor.timer = setTimeout(function(){

							var affectedIndex = map.affectedIndex;
							map.affectedIndex = [];

							map.task.add(function(){

								var l = affectedIndex.length,
									i = 0;

								for (id in map.monitors) {

									var monitor = map.monitors[id],
										from = monitor.from,
										to = monitor.to,
										i;

									for (i=0; i<l; i++) {

										var a = affectedIndex[i];

										if (a >= from || a <= to) {

											monitor.refresh();
											break;
										}
									}
								}
							});

						}, 250);
					}
				});

				return map;
			},

			search: function(keyword) {

				// TODO: Also remove DS from keyword
				if ($.trim(keyword)==="") return [];

				// #debug:start
				// var profiler = "Searching library using keyword '" + keyword + "'";
				// self.media.console("time", [profiler]);
				// #debug:end

				var keyword = keyword.toLowerCase();

				var results = self.createMetaView({type: "search"});

				for (key in self.meta) {

					var parts = key.split("|"),
						place = parts[0],
						path = parts[1],
						meta = self.meta[key];

					if (/easysocial|jomsocial|flickr/.test(place)) {
						// Note: This means that album name keyword cannot be matched.
						path = meta.title || "";
					}

					if (path.toLowerCase().match(keyword)) {
						results.addMeta(meta);
					}
				}

				// #debug:start
				// self.media.console("timeEnd", [profiler]);
				// #debug:end

				return results;
			}
		}}
	);

	EasyBlog.Controller("Media.Exporter",

		{
			defaultOptions: {

				view: {
					recentItem: "media/recent.item"
				},

				// Recent inserts
				"{recentActivities}"            : ".recentActivities",
				"{recentActivitiesDialog}"      : ".recentActivities .modalPromptDialog",
				"{hideRecentActivitiesButton}"  : ".recentActivities .promptHideButton",
				"{dashboardButton}"             : ".recentActivities .dashboardButton",
				"{recentItemGroup}": ".recentItemGroup",
				"{recentItem}"     : ".recentItem"
			}
		},

		function(self){ return {

			init: function() {

				$Media.exporter = self;

				$Media.insert = self.insert;
			},

			handler: {},

			showDialog: function() {

				self.recentActivitiesDialog()
					.addClass("active");

				self.recentActivities()
					.css({top: 0, opacity: 1})
					.addClass("active");
			},

			hideDialog: function() {

				self.recentActivities()
					.animate({top: "-=50px", opacity: 0}, {duration: 250, complete: function() {

							self.recentActivities()
								.removeClass("active");

							self.recentActivitiesDialog()
								.removeClass("active");
						}
					});
			},

			"{dashboardButton} click": function() {
				self.hideDialog();
			},

			"{hideRecentActivitiesButton} click": function() {

				self.hideDialog();
			},

			// Recent inserts
			insert: function(item, settings) {

				var meta = $Media.library.getMeta(item);

				if (meta===undefined) return;

				var task = self.create(meta.type, meta.key, settings);

				// Show dialog
				self.showDialog();

				// Create recent item
				task.recentItem =
					self.view.recentItem({meta: meta})
						.addClass("loading")
						.css({opacity: 0})
						.prependTo(self.recentItemGroup())
						.animate({opacity: 1}, {duration: 500, complete: function() {

							task
								.done(function(html) {

									if (html==="") {
										task.recentItem
											.removeClass("loading done")
											.addClass("error")
											.find(".itemProgress")
											.html($.language("COM_EASYBLOG_MM_UNABLE_TO_EXPORT_ITEM"));
									}

									if( typeof( tinyMCE ) != 'undefined' )
									{

										// If you are TinyMCE/JCE on IE
										if (tinyMCE && tinyMCE.isIE && $Media.IE==9) {

											// Get back the bookmark we stored just now
											var bookmark = $Media.bookmark;

											if (bookmark) {

												var editor = tinyMCE.editors.write_content;

												editor.selection.moveToBookmark({rng: bookmark.range});

												editor.execCommand('mceInsertContent', false, html);

											// Just in case we did not get the bookmark
											} else {

												EasyBlog.dashboard.editor.insert(html);
											}

										} else {

											EasyBlog.dashboard.editor.insert(html);
										}
									}
									else
									{
										EasyBlog.dashboard.editor.insert(html);
									}

									task.recentItem
										.removeClass("loading failed")
										.addClass("done")
										.find(".itemProgress")
										.html($.language("COM_EASYBLOG_MM_ITEM_INSERTED"));

									if ($Media.options.recentActivities.hideAfter > 0) {

										setTimeout(function(){ self.hideDialog(); }, $Media.options.recentActivities.hideAfter);
									}
								})
								.progress(function(message){

									if (task.recentItem.hasClass("done")) return;

									task.recentItem
										.find(".itemProgress")
										.html(message);
								})
								.fail(function(message){

									task.recentItem
										.removeClass("loading done")
										.addClass("error")
										.find(".itemProgress")
										.html(message);
								});
						}
					});

				return task;
			},

			create: function(type, key, settings) {

				var handler = self.handler[type],

					task = $.Deferred();

				if (handler===undefined) {

					var Exporter = EasyBlog.Controller.Media.Exporter[$.String.capitalize(type)];

					if (Exporter===undefined) {

						task.reject($.language("COM_EASYBLOG_MM_UNABLE_TO_FIND_EXPORTER"));

						return task;
					}

					handler = self.handler[type] = new Exporter(self.element, $.extend({}, {settings: self.options[type], controller: { media: self.media }}));
				}

				handler.create(task, key, settings);

				return task;
			}

		}}
	);

	EasyBlog.Controller("Media.Exporter.File",

		{
			defaultOptions: {

				settings: {
					title: "",
					target: "_self",
					content: ""
				}
			}
		},

		function(self){ return {

			init: function() {

			},

			create: function(task, key, customSettings) {

				var settings = $.extend({}, self.options.settings, customSettings),

					meta = self.media.library.getMeta(key),

					link =
						$(document.createElement("A"))
							.attr({
								title: settings.title,
								target: settings.target,
								href: meta.url
							})
							.html((settings.content) ? settings.content : meta.title);

				task.resolve(link.toHTML());

				return task;
			}

		}}
	);

	EasyBlog.Controller("Media.Exporter.Folder",

		{
			defaultOptions: {

				settings: {

				}
			}
		},

		function(self){ return {

			init: function() {

			},

			create: function(task, key, customSettings) {

				var settings = $.extend({}, self.options.settings, customSettings),

					meta = self.media.library.getMeta(key),

					embedType = (meta.place=="jomsocial" || meta.place == 'easysocial') ? "album" : "gallery";

					settings.file = meta.path;

					settings.place = meta.place;

				var snippet = "[embed=" + embedType + "]" + JSON.stringify(settings) + "[/embed]";

				task.resolve(snippet);

				return task;
			}

		}}
	);

	EasyBlog.Controller("Media.Exporter.Image",

		{
			defaultOptions: {

				settings: {
					zoom: null, // variationName
					caption: null,
					lightbox: false,
					enforceDimension: false,
					enforceWidth: null,
					enforceHeight: null,
					variation: null, // variationName
					defaultVariation: "thumbnail",
					defaultZoom: "original"
				}
			}
		},

		function(self){ return {

			init: function() {

			},

			create: function(task, key, customSettings) {

				var settings = $.extend({}, self.options.settings, customSettings),

					meta = self.media.library.getMeta(key),

					image = $(new Image());

					// Convert caption into html entities,
					// escape quotes and other special characters.
					var title = htmlentity(meta.title);

					image.attr({
						src: meta.thumbnail.url,
						alt: title,
						title: title
					});

				var resolve = function() {

						task.resolve(image.toHTML());
					},

					process = function() {

						var variations = {};

						$.each(meta.variations, function(i, variation) {
							variations[variation.name] = variation;
						});


						// Use provided variation, else use default variation, or the first variation on the list.
						var variation = variations[settings.variation] ||
										variations[settings.defaultVariation] ||
										meta.variations[0];


						// Convert caption into html entities,
						// escape quotes and other special characters.
						var title = htmlentity(variation.title);

						// Use setting from selected variation
						image.attr({
							src: variation.url,
							alt: title,
							title: title
						});


						// Enforce dimension
						if (settings.enforceDimension) {

							if (settings.enforceWidth!==null && settings.enforceHeight!==null) {

								var sizes = $.Image.resizeWithin(
									variation.width, variation.height,
									settings.enforceWidth, settings.enforceHeight
								);

								// Turn it into whole number
								sizes.width = Math.floor(sizes.width);
								sizes.height = Math.floor(sizes.height);

								image.attr(sizes);
							}
						}

						// Image caption
						if (settings.caption!==null) {

							// Convert caption into html entities,
							// escape quotes and other special characters.
							var caption = htmlentity(settings.caption);

							image
								.addClass("easyblog-image-caption")
								.attr("title", caption)
								.attr('alt', caption);
						}

						// Image zooming capabilities
						if (settings.zoom!==null) {

							var zoomWith = variations[settings.zoom] ||
										   variations[settings.defaultZoom] ||
										   {url: meta.thumbnail.url, title: meta.title};

							var title = htmlentity(settings.caption || zoomWith.title || "");

							image =
								$("<a>")
									.addClass("easyblog-thumb-preview")
									.attr({
										href: zoomWith.url,
										title: title
									})
									.html(image);
						};

						resolve();
					}

				// If any of these criterias were true,
				// we need to retrieve the variations.
				if (settings.variation || settings.zoom || settings.enforceWidth || settings.enforceHeight) {

					// If the variations hasn't been loaded
					if (meta.variations===undefined) {

						task.notify($.language("COM_EASYBLOG_MM_GETTING_IMAGE_SIZES"));

						// Then get it first
						self.media.library.getMetaVariations(key)
							.done(function(metaWithVariations) {

								// Add variations to our meta
								meta = metaWithVariations;

								// Process the rest of the image settings
								process();
							})
							.fail(function() {

								// If the ajax call failed, reject task.
								task.reject($.language("COM_EASYBLOG_MM_UNABLE_TO_RETRIEVE_VARIATIONS"));
							});

					// If variations has been loaded,
					// process the rest of the image settings now.
					} else {

						process();
					}

				// If there are no fancy image settings,
				// the default one will work just fine.
				} else {

					resolve();
				}

				return task;
			}

		}}
	);

	EasyBlog.Controller("Media.Exporter.Audio",

		{
			defaultOptions: {
				width: 400,
				height: 24,
				autostart: false,
				controlbar: "bottom",
				backcolor: "#333333",
				frontcolor: "#ffffff"
			}
		},

		function(self){ return {

			init: function() {

			},

			create: function(task, key, customSettings) {

				var settings = $.extend({}, self.options.settings, customSettings),

					meta = self.media.library.getMeta(key);

					settings.file = meta.path;

					settings.place = meta.place;

				var snippet = "[embed=audio]" + JSON.stringify(settings) + "[/embed]";

				task.resolve(snippet);

				return task;
			}

		}}
	);

	EasyBlog.Controller("Media.Exporter.Video",

		{
			defaultOptions: {

				settings: {
					width: 400,
					height: 225,
					autostart: false,
					controlbar: "bottom",
					backcolor: "#333333",
					frontcolor: "#ffffff"
				}
			}
		},

		function(self){ return {

			init: function() {

			},

			create: function(task, key, customSettings) {

				var settings = $.extend({}, self.options.settings, customSettings),

					meta = self.media.library.getMeta(key);

					settings.file = meta.path;

					settings.place = meta.place;

				var snippet = "[embed=video]" + JSON.stringify(settings) + "[/embed]";

				task.resolve(snippet);

				return task;
			}

		}}
	);

	EasyBlog.Controller("MediaLauncher",

		{
			defaultOptions: {
				"{uploadImageButton}": ".uploadImageButton",
				"{chooseImageButton}": ".chooseImageButton"
			}
		},

		function(self) { return {

			init: function() {

				$("#media_manager_button").click(function(){
					EasyBlog.mediaManager.browse();
				});
			},

			"{uploadImageButton} click": function() {

				EasyBlog.mediaManager.upload();
			},

			"{chooseImageButton} click": function() {


				EasyBlog.mediaManager.browse();
			}
		}}
	);

	//
	// 2. Initialize media manager.
	//
	var container = $(document.createElement("div")).attr("id", "EasyBlogMediaManager").appendTo("body");

	EasyBlog.mediaManager = new EasyBlog.Controller.Media(container);
});

