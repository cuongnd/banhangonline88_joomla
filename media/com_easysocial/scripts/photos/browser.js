EasySocial.module("photos/browser", function($){

	var module = this;

	// Non-essential dependencies
	EasySocial.require()
		.library("masonry")
		.done();

	EasySocial.require()
		.library(
			"history"
		)
		.done(function(){

			EasySocial.Controller("Photos.Browser",
			{
				defaultOptions: {

					// For masonry
					tilesPerRow: 4,

					"{sidebar}": "[data-photo-browser-sidebar]",
					"{content}": "[data-photo-browser-content]",

					"{backButton}"    : "[data-photo-back-button]",
					"{backButtonLink}": "[data-photo-back-button-link]",

					"{listItemGroup}" : "[data-photo-list-item-group]",
					"{listItem}"      : "[data-photo-list-item]",
					"{listItemLink}"  : "[data-photo-list-item] > a",
					"{listItemTitle}" : "[data-photo-list-item-title]",
					"{listItemCover}" : "[data-photo-list-item-cover]",
					"{listItemImage}" : "[data-photo-list-item-image]",

					"{featuredListItem}"      : "[data-photo-list-item].featured",
					"{featuredListItemImage}" : "[data-photo-list-item].featured [data-photo-list-item-image]",

					"{photoItem}": "[data-photo-item]"
				}
			},
			function(self) { return {

				init: function() {

					// If there's masonry, activate it.
					$.module("masonry")
						.done(function(){
							self.setLayout();
						});
				},

				setLayout: function() {

					var listItemGroup = self.listItemGroup().addClass("masonry"),

						// Build layout state
						seed          = self.setLayout.seed,
						intact        = seed == listItemGroup.width()
						hasPhotoItem  = self.listItem().length > 0,
						masonry       = listItemGroup.data("masonry");

					if (intact) {

						// Just reload masonry
						masonry && masonry.reload();

					// Else recalculate sizes
					} else {

						// Get listItemGroup
						var listItemGroup 		= self.listItemGroup(),
							listItem      		= self.listItem(),
							tilesPerRow         = self.options.tilesPerRow,

							viewportWidth       = listItemGroup.width(),
							containerWidth      = Math.floor(viewportWidth / tilesPerRow) * tilesPerRow,
							tileWidth           = containerWidth / tilesPerRow,
							tileWidthOffset     = listItem.outerWidth(true) - listItem.width(),
							tileHeight          = tileWidth,
							tileHeightOffset    = listItem.outerHeight(true) - listItem.height(),

							itemWidth           = tileWidth  - tileWidthOffset,
							itemHeight          = tileHeight - tileHeightOffset,
							imageWidth          = itemWidth,
							imageHeight         = itemHeight,
							featuredItemWidth   = (tileWidth  * 2) - tileWidthOffset,
							featuredItemHeight  = (tileHeight * 2) - tileHeightOffset,
							featuredImageWidth  = featuredItemWidth,
							featuredImageHeight = featuredItemHeight;

						self.listItem
							.css({
								width : itemWidth,
								height: itemHeight
							});

						self.listItemImage
							.css({
								width : imageWidth,
								height: imageHeight
							});

						self.featuredListItem
							.css({
								width : featuredItemWidth,
								height: featuredItemHeight
							});

						self.featuredListItemImage
							.css({
								width : featuredImageWidth,
								height: featuredImageHeight
							});

						listItemGroup
							.masonry({
								columnWidth: tileWidth
							})
							.masonry("reload");
					}

					// Save current layout
					self.setLayout.seed = listItemGroup.width();
				},

				open: function(view) {

					var args = $.makeArray(arguments);

					self.trigger("contentLoad", args);

					var method = "view" + $.String.capitalize(view),
						loader = self[method].apply(self, args.slice(1));

					loader
						.done(self.displayContent(function(){
							self.trigger("contentDisplay", args);
							return arguments;
						}))
						.fail(function(){
							self.trigger("contentFail", args);
						})
						.always(function(){
							self.trigger("contentComplete", args);
						});

					return loader;
				},

				displayContent: $.Enqueue(function(html){

					var scripts = [],
						content = $($.buildFragment([html], document, scripts));

					// Insert content
					self.content().html(content);

					// Remove scripts
					$(scripts).remove();
				}),

				viewPhoto: function(photoId) {

					var state = "active loading",

						listItem =
							self.listItem()
								.removeClass(state)
								.filterBy("photoId", photoId)
								.addClass(state),

						loader =
							EasySocial.ajax(
								"site/views/photos/item",
								{
									id: photoId,
									browser: false
								})
								.fail(function(){
								})
								.always(function(){
									listItem.removeClass("loading");
								});

					return loader;
				},

				"{listItem} click": function(listItem) {

					var photoId = listItem.data("photoId");

					// Load album
					self.open("Photo", photoId);

					// Change address bar url
					listItem.find("> a").route();
				},

				"{listItemLink} click": function(listItemLink, event) {

					// Progressive enhancement, no longer refresh the page.
					event.preventDefault();

					// Prevent item from getting into :focus state
					listItemLink.blur();
				},

				"{backButtonLink} click": function(albumsButtonLink, event) {

					var browser = self.browser;

					// If albums browser exists, use it to load album
					if (browser) {

						var albumId = self.element.data("albumId");

						browser.open("album", albumId);

						event.preventDefault();

						albumsButtonLink.route();

						// To quickly change back to album
						browser.setLayout("album");

						self.element.remove();
					}
				},

				getListItem: function(photoId, context) {

					var listItem =
						(!photoId) ?
							self.listItem(".new") :
							self.listItem().filterBy("photoId", photoId);

					if (!context) return listItem;

					return listItem.find(self["listItem" + $.String.capitalize(context)].selector);
				},

				getNextListItem: function(photoId) {

					var listItem =
						self.getListItem(photoId)
							.next(self.listItem.selector);

					if (listItem.length < 1) {
						listItem = self.listItem(":first");
					}

					return listItem;
				},

				getPrevListItem: function(photoId) {

					var listItem =
						self.getListItem(photoId)
							.prev(self.listItem.selector);

					if (listItem.length < 1) {
						listItem = self.listItem(":last");
					}

					return listItem;
				},

				removeListItem: function(photoId, loadPreviousItem) {

					var listItem = self.getListItem(photoId),
						prevListItem = self.getPrevListItem(photoId);

					// Remove list item
					listItem.remove();

					// Reset list item masonry layout
					self.setLayout();

					// If there are no more items on the list
					if (self.listItem().length < 1) {

						self.element.addClass("loading");

						// Go back to albums
						return window.location = self.backButtonLink().attr("href");
					}

					// Else load previous item
					if (loadPreviousItem) {

						prevListItem.click();
					}
				},

				"{photoItem} init.photos.item": function(el, event, photoItem) {

					// Attach browser plugin to album
					self.addSubscriber(photoItem);
				},

				"{photoItem} photoSave": function(el, event, task) {

					// Update list item title when photo is updated.
					task.done(function(photo, html){

						self.getListItem(photo.id, "title")
							.html(photo.title);
					});
				},

				"{photoItem} photoNext": function(el, event, photo) {

					var listItem = self.getNextListItem(photo.id);
					listItem.click();
				},

				"{photoItem} photoPrev": function(el, event, photo) {

					var listItem = self.getPrevListItem(photo.id);
					listItem.click();
				},

				"{photoItem} photoMove": function(el, event, task, photo, targetAlbumId) {

					task
						.done(function(){
							self.removeListItem(photo.id, true);
						});
				},

				"{photoItem} photoDelete": function(el, event, task, photo) {

					task
						.done(function(){
							self.removeListItem(photo.id, true);
						});
				},

				"{photoItem} photoFeature": function(el, event, task, photo, featured) {

					var item = self.getListItem(photo.id);

					item.toggleClass("featured", featured);
					self.setLayout();

					task
						.fail(function(){
							item.toggleClass("featured", !featured);
							self.setLayout();
						});
				},

				"{sidebar} sidebarToggle": function() {

					self.setLayout();
				}

			}});

			module.resolve();

		});
});