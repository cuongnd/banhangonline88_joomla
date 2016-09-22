EasySocial.module("albums/item", function($){

	var module = this;

	// Non-essential dependencies
	EasySocial.require()
		.script("albums/editor")
		.done();

	// Essential dependencies
	EasySocial.require()
		.library(
			"masonry"
		)
		.done(function(){

			EasySocial.Controller("Albums.Item",
			{
				hostname: "album",

				defaultOptions: {

					tilesPerRow: 4,
					editable: false,
					multipleSelection: false,

					"{header}"        : "[data-album-header]",
					"{content}"       : "[data-album-content]",
					"{footer}"        : "[data-album-footer]",

					"{info}"          : "[data-album-info]",

					"{title}"         : "[data-album-title]",
					"{caption}"       : "[data-album-caption]",
					"{location}"      : "[data-album-location]",
					"{date}"          : "[data-album-date]",
					"{cover}"         : "[data-album-cover]",

					"{photoItemGroup}": "[data-photo-item-group]",
					"{photoItem}"     : "[data-photo-item]",
					"{photoImage}"    : "[data-photo-image]",
					"{photoImageCss}" : "[data-photo-image-css]",
					"{featuredItem}"  : "[data-photo-item].featured",
					"{featuredImage}" : "[data-photo-item].featured [data-photo-image]",
					"{featuredImageCss}" : "[data-photo-item].featured [data-photo-image-css]",
					"{uploadItem}"    : "[data-photo-upload-item]",

					"{moreButton}"    : "[data-album-more-button]",
					"{viewButton}"    : "[data-album-view-button]",

					"{share}"			  : "[data-repost-action]",
					"{likes}"			  : "[data-likes-action]",
					"{likeContent}" 	  : "[data-likes-content]",
					"{repostContent}" 	  : "[data-repost-content]",
					"{counterBar}"	  	  : "[data-stream-counter]"
				}
			},
			function(self) { return {

				init: function()
				{
					self.id = self.element.data("album-id");

					self.nextStart = self.element.data("album-nextstart") || -1;

					// If this viewer is editable, load & implement editor.
					if (self.options.editable)
					{
						EasySocial.module("albums/editor")
							.done(function(EditorController)
							{
								self.editor = self.addPlugin("editor", EditorController);
							});
					}

					// Set layout when window is resized
					self.setLayout();

					// Let setLayout sink in first.
					setTimeout(function(){
						self.content().addClass("ready");
					}, 1);

					// Attach existing photo items as subscribers
					self.addSubscriber(
						self.photoItem()
							.controllers("EasySocial.Controller.Photos.Item")
					);
				},

				"{window} resize": $.debounce(function(){
					self.setLayout();
				}, 250),

				currentLayout: function() {

					return self.element.data("albumLayout");
				},

				setLayout_: $.debounce(function(){

					self.setLayout();
				}, 100),

				setLayout: function(layoutName) {

					var photoItemGroup = self.photoItemGroup(),

						// Build layout state
						currentLayout = self.currentLayout(),
						layoutName    = layoutName || currentLayout,
						seed          = self.setLayout.seed,
						intact        = (seed == photoItemGroup.width() && currentLayout==layoutName)
						hasPhotoItem  = self.photoItem().length > 0,
						hasUploadItem = self.uploadItem().length > 0,
						hasItem       = hasPhotoItem || hasUploadItem,
						masonry       = photoItemGroup.data("masonry"),

						// Put them in an object
						layout = {
							currentLayout: currentLayout,
							seed         : seed,
							intact       : intact,
							hasPhotoItem : hasPhotoItem,
							hasUploadItem: hasUploadItem,
							hasItem      : hasItem,
							masonry      : masonry
						};

					// Determine if we need to switch layout
					if (!intact) {

						// Switch layout
						self.element
							.data("albumLayout", layoutName)
							.switchClass("layout-" + layoutName);

						// Switch all photo item's layout
						self.photoItem()
							.switchClass("layout-" + layoutName);

						// Reset viewport width to force layout redraw
						self.setLayout.seed = layout.seed = null;

						// Trigger layout change event
						self.trigger("layoutChange", [layoutName, layout]);
					}

					// Show upload hint when content is empty
					self.element.toggleClass("has-photos", hasItem);

					// If there's no item from the list
					if (!hasItem) {

						// If this is coming from deleting the last item
						// from the list, we need to keep the container
						// on zero height.
						photoItemGroup.css("opacity", 1);
					}

					// Execute layout handler
					var layoutHandler = "set" + $.String.capitalize(layoutName) + "Layout";
					self[layoutHandler](layout);

					// Save current layout
					self.setLayout.seed = photoItemGroup.width();
				},

				setItemLayout: function(layout) {

					self.photoItem().attr("data-es-photo-disabled", 0);

					if (layout.intact) {

						// Just reload masonry
						layout.masonry && layout.masonry.reload();

					// Else recalculate sizes
					} else {

						var tilesPerRow = 4;

						// Override tilesPerRow depending on mobile sizes
						if ($(".es-main").hasClass("w600")) {
							tilesPerRow = 2;
						}

						// Override tilesPerRow depending on mobile sizes
						if ($(".es-main").hasClass("w320")) {
							tilesPerRow = 1;
						}

						// Get photoItemGroup
						var photoItemGroup 		= self.photoItemGroup(),
							photoItem      		= self.photoItem(),
							tilesPerRow         = tilesPerRow,

							viewportWidth       = photoItemGroup.width(),
							containerWidth      = Math.floor(viewportWidth / tilesPerRow) * tilesPerRow,
							tileWidth           = containerWidth / tilesPerRow,
							// tileWidthOffset     = photoItem.outerWidth(true) - photoItem.width(),
							tileHeight          = tileWidth,
							// tileHeightOffset    = photoItem.outerHeight(true) - photoItem.height(),

							itemWidth           = tileWidth,
							itemHeight          = tileHeight,
							imageWidth          = itemWidth,
							imageHeight         = itemHeight;

							if (tilesPerRow >= 4) {
								var featuredItemWidth   = tileWidth  * 2;
								var featuredItemHeight  = tileHeight * 2;
							} else {
								var featuredItemWidth   = itemWidth;
								var featuredItemHeight  = itemHeight;
							}

							var featuredImageWidth  = featuredItemWidth;
							var featuredImageHeight = featuredItemHeight;

						self.photoItem
							.css({
								width : itemWidth,
								height: itemHeight
							});

						// self.photoImageCss
						// 	.css({
						// 		width : imageWidth,
						// 		height: imageHeight
						// 	});

						self.featuredItem
							.css({
								width : featuredItemWidth,
								height: featuredItemHeight
							});

						// self.featuredImageCss
						// 	.css({
						// 		width : featuredImageWidth,
						// 		height: featuredImageHeight
						// 	});

						self.uploadItem
							.css({
								width : itemWidth,
								height: itemHeight
							});

						photoItemGroup
							.masonry({
								columnWidth: tileWidth,
								isRTL: self.options.rtl
							});

						setTimeout(function(){
							photoItemGroup
								.addTransitionClass("no-transition")
								.masonry("reload");
						}, 1);
					}
				},

				setFormLayout: function(layout) {

					self.photoItem().attr("data-es-photo-disabled", 1);

					// Destroy masonry if we are on form layout
					layout.masonry && layout.masonry.destroy();

					// Reset layout
					self.clearLayout();
				},

				setDialogLayout: function() {

					self.photoItem().attr("data-es-photo-disabled", 1);

					// Destroy masonry if we are on form layout
					layout.masonry && layout.masonry.destroy();

					// Reset layout
					self.clearLayout();
				},

				setThumbnailLayout: function() {

				},

				setRowLayout: function() {

					self.photoItem().attr("data-es-photo-disabled", 0);

					self.clearLayout();
				},

				clearLayout: function() {

					self.photoItemGroup()
						.addClass("no-transition");

					self.photoItem
						.css().remove();

					self.photoImage
						.css().remove();

					self.photoImageCss
						.css().remove();

					self.featuredItem
						.css().remove();

					self.featuredImage
						.css().remove();

					self.featuredImageCss
						.css().remove();

					self.uploadItem
						.css().remove();

					self.setLayout.seed = null;
				},

				getSelectedItems: function() {

					var selectedPhotos = self.photoItem(".selected");

					var data = [];

					selectedPhotos.each(function(i, photo){
						data.push($(photo).controller("EasySocial.Controller.Photos.Item").data());
					});

					return data;
				},

				"{photoItem} init.photos.item": function(el, event, photoItem) {

					self.addSubscriber(photoItem);
				},

				"{photoItem} destroyed": function() {

					self.setLayout();
				},

				"{photoItem} activate": function(photoItem, event, photo) {

					// Activate is a non-standard IE event,
					// if photo is undefined then it is coming
					// from the browser not photo item controller.
					if (!photo) return;

					var currentLayout = self.currentLayout();

					switch (currentLayout) {

						case "item":
						case "row":

							// Show loading indicator
							photoItem.addClass("loading");

							// If browser is available, ask browser
							// to load photo view via ajax.
							if (self.browser) {

								// View photo
								self.browser
									.open("photo", photo.id)
									.always(function(){

										// Remove loading indicator
										photoItem.removeClass("loading");
									});

								// Change address bar url
								photo.imageLink().route();

							// If browser is not available,
							// just load the photo view normally.
							} else {
								window.location = photo.imageLink().attr("href");
							}
							break;

						case "form":
							// photo.editor && photo.editor.enable();
							break;

						case "dialog":

							var selectedPhotos = self.photoItem(".selected");

							if (!self.options.multipleSelection) {

								var selected = photoItem.hasClass("selected");

								// In case it came from multiple selection
								selectedPhotos.removeClass("selected");

								photoItem.toggleClass("selected", !selected);

							} else {

								photoItem.toggleClass("selected");
							}
							break;
					}
				},

				"{photoItem} photoFeature": function(el, event, task, photo, featured) {

					// Set layout to accomodate double size photo item
					self.setLayout();

					// When a photo fail to be featured, it shrinks
					task
						.fail(function(){

							// So we're resetting layout again
							self.setLayout();
						});
				},

				"{photoItem} photoMove": function(el, event, task, photo, targetAlbumId) {

					self.clearMessage();

					task
						.done(function(){

							// Remove photo
							photo.element.remove();

							// Set layout
							self.setLayout();

							// If there are no more photos, remove cover
							if (self.photoItem().length < 1) {
								self.trigger("coverRemove", [self]);
							}
						})
						.fail(function(message, type){
							self.setMessage(message, type);
						});
				},

				"{photoItem} photoDelete": function(el, event, task, photo) {

					self.clearMessage();

					task
						.done(function(){

							// Remove photo
							photo.element.remove();

							// Set layout
							self.setLayout();

							// If there are no more photos, remove cover
							if (self.photoItem().length < 1) {
								self.trigger("coverRemove", [self]);
							}
						})
						.fail(function(message, type){
							self.setMessage(message, type);
						});
				},

				// These are coming from album editor
				"{self} albumSave": function(el, event, task) {

					task.done(function(album){
						self.id = album.id;
					});
				},

				"{self} coverChange": function(el, event, photo, album) {

					self.cover()
						.css("backgroundImage", $.cssUrl(photo.sizes.thumbnail.url));
				},

				"{self} coverRemove": function() {

					self.cover()
						.css("backgroundImage", "");
				},

				"{viewButton} click": function(viewButton, event) {
					if (self.browser)
					{
						event.preventDefault();
						self.element.addClass("loading");
						self.browser.open("Album", self.id);
					}
				},

				"{moreButton} click": function(moreButton) {

					// If nextStart is -1, means no more photos
					if (self.nextStart == -1) {
						return;
					}

					if (moreButton.disabled()) return;

					// Disable this button
					moreButton.disabled(true);

					// Set the button into loading state
					// moreButton.addClass('loading');

					// Get the new photos content
					EasySocial.ajax(
						"site/controllers/albums/loadMore",
						{
							albumId: self.id,
							start: self.nextStart,
							layout: self.currentLayout()
						})
						.done(function(htmls, nextStart) {

							self.nextStart = nextStart;

							var photoItemGroup = self.photoItemGroup();

							$.each(htmls, function(i, html){
								$.buildHTML(html).appendTo(photoItemGroup);
							});

							// If there is no more photos to load, hide the button
							if (nextStart < 0) moreButton.hide();

							self.setLayout();
						})
						.always(function(){

							moreButton.disabled(false);
						});
				},

                "{share} create": function(el, event, itemHTML) {
                	self.counterBar().removeClass('hide');
                },

 				"{likes} onLiked": function(el, event, data) {

					//need to make the data-stream-counter visible
					self.counterBar().removeClass( 'hide' );
				},

				"{likes} onUnliked": function(el, event, data) {

					var isLikeHide 		= self.likeContent().hasClass('hide');
					var isRepostHide 	= self.repostContent().hasClass('hide');

					if( isLikeHide && isRepostHide )
					{
						self.counterBar().addClass( 'hide' );
					}
				}

			}});

			module.resolve();
		});
});
