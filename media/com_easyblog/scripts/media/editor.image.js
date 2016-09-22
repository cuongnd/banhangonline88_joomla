// module: start
EasyBlog.module("media/editor.image", function($){

var module = this;

// require: start
EasyBlog.require()
.library(
	"ui/position"
)
.script(
	'media/constrain'
)
.view(
	"media/editor.image",
	"media/editor.image.variation",
	"media/editor.image.caption"
)
.done(function() {

EasyBlog.Controller(

	"Media.Editor.Image",
	{
		defaultOptions: {

			view: {
				panel: "media/editor.image",
				variation: "media/editor.image.variation",
				caption: "media/editor.image.caption"
			},

			defaultVariation: "thumbnail",

			"{editorPreview}": ".editorPreview",
			"{editorPanel}": ".editorPanel",

			// Variation list
			"{imageVariationPanel}": ".imageVariationPanel",
			"{imageVariationList}" : ".imageVariationList",
			"{imageVariations}"    : ".imageVariations",
			"{imageVariation}"     : ".imageVariation",

			// Enforce Dimension
			"{imageEnforceDimensionOption}"	: ".imageEnforceDimensionOption",
			"{imageEnforceWidth}"			: ".imageEnforceWidth",
			"{imageEnforceHeight}"			: ".imageEnforceHeight",

			// Caption
			"{imageCaptionOption}"	: ".imageCaptionOption",
			"{imageCaption}"		: ".imageCaption",

			// Zoom
			"{imageZoomOption}"				: ".imageZoomOption",
			"{imageZoomLargeImageSelection}": ".imageZoomLargeImageSelection",

			// File properties
			"{itemFilesize}": ".itemFilesize",
			"{itemFilename}": ".itemFilename",
			"{itemUrl}"		: ".itemUrl",
			"{itemCreationDate}": ".itemCreationDate",

			// Prompt
			"{modalPrompt}": ".modalPrompt"
		}
	},

	function(self) {

		var $Media, $Library, $Browser, $Prompt;

		return {

		init: function() {

			$Media = self.media;
			$Library = $Media.library;
			$Browser = $Media.browser;

			var meta = self.meta(),
				place = self.place(),
				subcontrollerOptions = {
					controller: {
						editor: self,
						media: self.media
					}
				};

			// Panel
			self.editorPanel()
				.html(self.view.panel({
					meta: meta,
					acl: place.acl,
					enableLightbox: $Media.options.exporter.image.lightbox,
					enforceImageDimension: $Media.options.exporter.image.enforceDimension,
					enforceImageWidth: $Media.options.exporter.image.enforceWidth,
					enforceImageHeight: $Media.options.exporter.image.enforceHeight
				}))
				.implement(
					EasyBlog.Controller.Media.Editor.Panel,
					{},
					function() {

						// Keep a reference to this controller
						self.panel = this;

						// Don't show file size when editing jomsocial image
						// because we are unable to retrieve them.
						if (meta.place==="jomsocial" || meta.place == 'easysocial') {
							self.itemFilesize().remove();
							self.itemFilename().css("padding-right", 0);
						}
					}
				);

			self.modalPrompt()
				.implement(EasyBlog.Controller.Media.Prompt, subcontrollerOptions, function() {
					$Prompt = self.promptDialog = this;
				});


			// Image filters
			var Filter = EasyBlog.Controller.Media.Editor.Image.Filter;

			self.element
				.implement(Filter.Dimension, subcontrollerOptions)
				.implement(Filter.Caption,   subcontrollerOptions)
				.implement(Filter.Lightbox,  subcontrollerOptions);

			// Preview
			self.editorPreview()
				.implement(
					EasyBlog.Controller.Media.Editor.Preview,
					{
						draggable: true
					},
					function() {

						// Keep a reference to this controller
						self.preview = this;

						// Attempt to load thumbnail image first
						self.previewImage(self.meta().thumbnail.url);
					}
				)

			// Variation form
			if (place.acl.canCreateVariation && place.acl.canDeleteVariation) {

				self.element
					.implement(EasyBlog.Controller.Media.Editor.Image.VariationForm, subcontrollerOptions);
			}

			self.populateImageVariations();

			self.setLayout();
		},

		meta: function() {
			return $Library.getMeta(self.key);
		},

		place: function() {
			return $Library.getPlace(self.meta().place);
		},

		setLayout: function() {

			self.preview.resetLayout();
		},

		"{self} insertItem": function() {
			var variation = self.currentImageVariation().data("variation"),
				options = {
					variation: variation.name
				};

			if(self.imageEnforceDimensionOption().is(":checked")) {
				options.enforceDimension = true;
				options.enforceWidth = self.imageEnforceWidth().val();
				options.enforceHeight = self.imageEnforceHeight().val();
			}

			if(self.imageCaptionOption().is(":checked")) {
				options.caption = self.imageCaption().val();
			}

			if(self.imageZoomOption().is(":checked")) {
				options.zoom = self.imageZoomLargeImageSelection().val();
			}

			$Media.insert(self.meta(), options);
		},

		//
		// Image variation
		//

		populateImageVariations: function() {

			var meta = self.meta(),
				variations = meta.variations;

			self.imageVariationsData = self.imageVariationsData || {};

			if (variations===undefined) {

				// Show loading indicator
				self.imageVariations()
					.empty()
					.addClass("busy");

				// Get file variations from server
				$Library.getMetaVariations(meta.key)
					.done(function(){

						// Try to populate variations again
						self.populateImageVariations();

						self.imageVariations()
							.removeClass("busy");
					})
					.fail(function() {

						// Ask user to try again on the preview screen
					})
					.always(function() {

						self.imageVariations()
							.removeClass("busy");
					});

				return;
			}

			$.each(variations, function(i, variation) {

				// Skip icon variation
				if (variation.name=="icon") return;

				self.createImageVariation(variation);
			});

			self.trigger("variationPopulated", [self.imageVariationsData]);
		},

		createImageVariation: function(variation) {

			var imageVariation = self.view.variation({variation: variation});

			imageVariation
				.data("variation", variation)
				.appendTo(self.imageVariations());

			// Add default class if this is a default variation
			if (variation["default"]===true || variation["default"]=="true") {
				imageVariation.addClass("default");
			}

			// If this variation can't be deleted, e.g. thumbnail, original,
			// then add a lock indicator.
			if (!variation.canDelete) {
				imageVariation.addClass("locked");
			}

			self.imageVariationsData[variation.name] = imageVariation;

			self.trigger("variationCreated", [imageVariation, variation]);

			return imageVariation;
		},

		"{self} variationPopulated": function() {

			// Find default variation and highlight default variation
			var variationName,
				defaultImageVariation = self.imageVariation(".default");

			if(self.imageVariation().length > 0) {

				if (defaultImageVariation.length < 1) {

					var meta = self.meta(),
						image = self.previewImage();


					if (image!==undefined) {

						$.each(meta.variations, function(i, variation) {

							if(variation.width == image.width() && variation.height == image.height()) {
								variationName = variation.name;
								return false;
							}
						});

					}

					variationName = variationName || self.imageVariation(":first").data("variation").name;

				} else {

					variationName = defaultImageVariation.eq(0).data("variation").name;
				}

				self.currentImageVariation(variationName);
			}

		},

		"{imageVariation} click": function(imageVariation) {
			var variation = imageVariation.data("variation");

			self.currentImageVariation(variation.name);
		},

		currentImageVariation: function(variationName) {

			var currentImageVariation = self.currentImageVariation.imageVariation,

				imageVariation = self.imageVariationsData[variationName];

			if (imageVariation!==undefined) {

				var variation = imageVariation.data("variation");

				var meta = self.meta();

				if(meta.place == 'jomsocial' || meta.place == 'easysocial') {
					var image = self.previewImage();
					variation.width = (image) ? image.data('width') : 0;
					variation.height = (image) ? image.data('height') : 0;
					$('<span class="variationDimension"></span>').text(variation.width + 'x' + variation.height).appendTo(imageVariation);
				}

				// Deactivate current image variation
				if (currentImageVariation) {
					currentImageVariation.removeClass("active");
				}

				imageVariation.addClass("active");

				self.currentImageVariation.imageVariation = imageVariation;

				self.trigger("variationSelected", [imageVariation, variation]);
			}

			return self.currentImageVariation.imageVariation;
		},

		"{self} variationSelected": function(el, event, imageVariation, variation) {

			self.itemFilesize()
				.html(variation.filesize);

			self.itemUrl()
				.html(variation.url);

			self.itemCreationDate()
				.html(variation.dateCreated);

			self.previewImage(variation.url);
		},

		"{self} variationRemoved": function(el, event, imageVariation, variation) {
			delete self.imageVariationsData[variation.name];
			imageVariation.remove();
			// $Library.removeMetaVaration(self.meta(), variation.name);
		},

		previewImage: function(url) {

			// No url given, return.
			if (url===undefined) {
				return self.previewImage.currentImage;
			};

			// Create a collection of image previews (if this is the first time)
			if (self.previewImage.images===undefined) {
				self.previewImage.images = {};
			}

			var image        = self.previewImage.images[url],
				currentUrl   = self.previewImage.currentUrl,
				currentImage = self.previewImage.images[currentUrl];

			// Show loading indicator
			self.preview.showDialog("loading");

			// Detach current image
			if (currentImage!==undefined && !$.isDeferred(currentImage)) {
				currentImage.detach();
				self.preview.container().empty();
			}

			// Store a copy of the current url
			self.previewImage.currentUrl = url;

			// If image hasn't been loaded
			if (image===undefined) {

				// Load image
				self.previewImage.images[url]  =
					$.Image.get(url)
						.done(function(image) {

							self.previewImage.images[url] = image;

							// If current url has changed, don't show this one.
							if (self.previewImage.currentUrl==url) {
								self.previewImage(url);
							}
						})
						.fail(function() {

							self.preview.hideDialog("loading");

							// If current url is still the same, show error message
							if (self.previewImage.currentUrl==url) {

								// TODO: Show error message
							}
						});

				return;
			}

			// If image is still loading
			if ($.isDeferred(image)) {
				return;
			}

			self.preview.container()
				.append(image);

			self.previewImage.currentImage = image;

			self.trigger("previewImage", [self.preview.container(), image]);

			// Hide loading indicator
			self.preview.hideDialog("loading");

			self.preview.resetLayout();
		}

	}}

);

EasyBlog.Controller(

	"Media.Editor.Image.VariationForm",

	{
		defaultOptions: {

			// Variation form
			"{imageVariationForm}"		: ".imageVariationForm",
			"{addVariationButton}"		: ".addVariationButton",
			"{createVariationButton}"	: ".createVariationButton",
			"{removeVariationButton}"	: ".removeVariationButton",
			"{cancelVariationButton}"	: ".cancelVariationButton",
			"{tryCreateVariationButton}": ".tryCreateVariationButton",
			"{newVariationName}"		: ".newVariationName",
			"{newVariationWidth}"		: ".newVariationWidth",
			"{newVariationHeight}"		: ".newVariationHeight",
			"{newVariationRatio}"		: ".newVariationRatio",
			"{newVariationLockRatio}"	: ".newVariationLockRatio",
			"{imageVariationMessage}"	: ".imageVariationMessage",
			variationNameFilter			: new RegExp('[^a-zA-Z0-9]','g'),

			// Variation prompt
			"{createNewImageVariationPrompt}"	: ".createNewImageVariationPrompt",
			"{promptVariationName}"				: ".createNewImageVariationPrompt .variationName",
			"{promptVariationWidth}"			: ".createNewImageVariationPrompt .variationWidth",
			"{promptVariationHeight}"			: ".createNewImageVariationPrompt .variationHeight"
		}
	},

	function(self) { return {

		init: function() {
		},

		"{self} variationSelected": function() {
			var variation = self.editor.currentImageVariation().data('variation');

			self.removeVariationButton()
				.toggle(variation.canDelete);
		},

		nextVariationName: function(name) {

			var match = false,
				name = $.trim(name.toLowerCase());

			$.each(self.editor.imageVariationsData, function(i, variation) {
				if (name==variation.data('variation').name.toLowerCase()) {

					match = true;

					var suffix = name.substr(-1, 1);

					name = ($.isNumeric(suffix)) ?
								name.substr(0, name.length - 1) + (parseInt(suffix, 10) + 1) :
								name + 1;

					return false;
				}
			});

			return (match) ? self.nextVariationName(name) : name;
		},

		"{addVariationButton} click": function() {

			self.editor.promptDialog
				.get('createNewImageVariationPrompt')
				.state('default')
				.show();

			var variation = self.editor.currentImageVariation().data('variation');
				variationName = $.String.capitalize(self.nextVariationName(variation.name));

			self.newVariationName()
				.data("default", variationName)
				.val(variationName)
				.select();

			self.newVariationWidth()
				.data("default", variation.width)
				.val(variation.width);

			self.newVariationHeight()
				.data("default", variation.height)
				.val(variation.height);

			self.imageVariationForm().constrain({
				selector: {
					width: self.options["{newVariationWidth}"],
					height: self.options["{newVariationHeight}"],
					constrain: self.options["{newVariationLockRatio}"]
				},
				source: {
					width: variation.width,
					height: variation.height
				},
				allowedMax: {
					width: self.editor.media.options.exporter.image.maxVariationWidth,
					height: self.editor.media.options.exporter.image.maxVariationHeight
				}
			})
		},

		"{newVariationRatio} click": function(el) {

			el.toggleClass("locked");

			if (el.hasClass("locked")) {
				self.newVariationLockRatio().attr('checked', 'checked');
			} else {
				self.newVariationLockRatio().removeAttr('checked');
			}

			self.newVariationLockRatio().trigger('change');
		},

		"{createVariationButton} click": function() {
			self.createVariation();
		},

		"{tryCreateVariationButton} click": function() {
			self.createVariation();
		},

		"{newVariationName} keyup": function(el, event) {
			var value = $.trim($(el).val());
			value = value.replace(new RegExp('[^0-9a-zA-Z]','g'), "");
			$(el).val(value);

			if(event.keyCode == 13) {
				self.createVariationButton().trigger('click');
			}
		},

		"[{newVariationWidth}, {newVariationHeight}] keyup": function(el, event) {
			if(event.keyCode == 13) {
				self.createVariationButton().trigger('click');
			}
		},

		createVariation: function() {
			var meta = self.editor.meta(),
				place = self.editor.place(),
				name = self.newVariationName().val(),
				width = self.newVariationWidth().val(),
				height = self.newVariationHeight().val();

			if(!$.trim(name) || !$.trim(width) || !$.trim(height)) {
				return false;
			}

			self.promptVariationName().text(name);
			self.promptVariationWidth().text(width);
			self.promptVariationHeight().text(height);

			self.editor.promptDialog
				.get('createNewImageVariationPrompt')
				.state('progress')
				.show();

			EasyBlog.ajax(
				"site.views.media.createVariation",
				{
					path: meta.path,
					place: place.id,
					name: name,
					width: width,
					height: height
				},
				{
					success: function( variation ) {

						self.media.library.meta[meta.key].variations.push(variation);

						self.editor.createImageVariation(variation);

						self.editor.currentImageVariation(variation.name);

						self.cancelVariationButton().click();
					},
					fail: function( message ) {
						self.editor.promptDialog
							.get('createNewImageVariationPrompt')
							.state('fail')
							.show();
					}
				}
			);
		},

		"{removeVariationButton} click": function() {

			var imageVariation = self.editor.imageVariation(".active"),
				variation = imageVariation.data("variation"),
				meta = self.editor.meta(),
				place = self.editor.place();

			if (variation.canDelete) {

				EasyBlog.ajax(

					"site.views.media.deleteVariation",

					{
						"fromPath": meta.path,
						"place": place.id,
						'name': variation.name
					},

					{
						beforeSend: function() {

							imageVariation.addClass("busy");
						},

						success: function() {

							// Once the item is successfully removed, we need to remove this variation.
							imageVariation.slideUp(function(){
								self.trigger('variationRemoved', [imageVariation, variation]);
							});

							// Revert to default image variation
							self.editor.imageVariation(".default")
								.click();

							self.media.library.removeMetaVariation(meta, variation.name);
						},

						fail: function(message) {

							try { console.log(message); } catch(e) {};
						},

						complete: function() {

							imageVariation.removeClass("busy");
						}
					}
				);
			}
		}
	}}
);


EasyBlog.Controller(
	"Media.Editor.Image.Filter.Caption",
	{
		defaultOptions: {
			view: {
				caption: "media/editor.image.caption"
			},

			"{imageVariation}"		: ".imageVariation",
			"{imageCaptionOption}"	: ".imageCaptionOption",
			"{imageCaption}"		: ".imageCaption"
		}
	},
	function(self) { return {

		init: function() {
			self.item = {
				meta: self.editor.meta()
			}
		},

		"{imageVariation} click": function(el) {
			self.transform();
		},

		"{self} dimensionEnforced": function() {
			self.transform();
		},

		"{imageCaptionOption} change": function(el, event) {

			event.stopPropagation();

			el.parent(".field").toggleClass("hide-field-content", !el.is(":checked"));

			self.transform();
		},

		"{imageCaptionOption} mouseup": function() {

			setTimeout(function(){
				self.imageCaption().focus()[0].select();
			}, 1);
		},

		"{imageCaption} blur": function(el) {
			if($.trim(el.val()) == '') {
				el.val(self.item.meta.title);
			}

			self.transform();
		},

		"{imageCaption} keyup": function(el, event) {
			self.transform();
		},

		transform: function() {
			var previewContainer = self.editor.preview.container(),
				image = previewContainer.find('img'),
				captionText = previewContainer.find('div.imageCaptionText');

			if(self.imageCaptionOption().is(':checked')) {
				var caption = self.imageCaption().val();

				captionText.remove();

				previewContainer.width(image.width());

				previewContainer.addClass('imageCaptionBorder');

				previewContainer.width(previewContainer.width());

				previewContainer.append(self.view.caption({
					caption: caption
				}));
			} else {
				previewContainer.removeClass('imageCaptionBorder');

				captionText.remove();

				previewContainer.width("auto");
			}

			self.editor.preview.resetLayout();
		}
	}}
);

EasyBlog.Controller(
	"Media.Editor.Image.Filter.Lightbox",
	{
		defaultOptions: {
			defaultImageZoomVariation       : "original",
			"{imageZoomOption}"				: ".imageZoomOption",
			"{imageZoomLargeImageSelection}": ".imageZoomLargeImageSelection",
			"{imageZoomLargeImageOption}"	: ".imageZoomLargeImageSelection option"
		}
	},
	function(self) { return {

		init: function() {

		},

		"{self} variationCreated": function(el, event, imageVariation, variation) {

			// Also add to insert options
			var variationName = $.String.capitalize(variation.name),
				largeImageOption = $("<option>")
										.val(variationName)
										.html(variationName)
										.data("variation", variation);

			var defaultSelectedVariationName =
					self.media.options.exporter.image.zoom ||
					self.options.defaultImageZoomVariation;

			if (variation.name==defaultSelectedVariationName) {
				largeImageOption.attr("selected", true);
			}

			self.imageZoomLargeImageSelection()
				.append(largeImageOption);
		},

		"{self} variationRemoved": function(el, event, imageVariation, variation) {
			self.imageZoomLargeImageOption('[value="' + variation.name + '"]').remove();
		},

		"{imageZoomOption} change": function(el, event) {

			event.stopPropagation();

			el.parent(".field").toggleClass("hide-field-content", !el.is(":checked"));
		},

		transform: function() {

			// Enable image zooming
			if (self.imageZoomOption().is(":checked")) {

				var largeImageVariation =
					self.imageZoomLargeImageOption(":selected").data("variation");

				image = $("<a>")
					.addClass("easyblog-thumb-preview")
					.attr({
						href: largeImageVariation.url,
						title: imageCaption || self.item.meta.title
					})
					.html(image);
			}
		}
	}}
);

EasyBlog.Controller(
	"Media.Editor.Image.Filter.Dimension",
	{
		defaultOptions: {
			"{imageEnforceDimension}"		: ".imageEnforceDimension",
			"{imageEnforceDimensionOption}"	: ".imageEnforceDimensionOption",
			"{imageEnforceWidth}"			: ".imageEnforceWidth",
			"{imageEnforceHeight}"			: ".imageEnforceHeight",
			"{imageEnforceRatio}"			: ".imageEnforceRatio",
			"{imageEnforceLockRatio}"		: ".imageEnforceLockRatio",
			"{imageVariation}"				: ".imageVariation"
		}
	},
	function(self) { return {

		init: function() {
			var options = {
				selector: {
					width: self.options["{imageEnforceWidth}"],
					height: self.options["{imageEnforceHeight}"],
					constrain: self.options["{imageEnforceLockRatio}"]
				}
			};

			// only apply constrain once variation has been populated
			self.editor.element.bind('variationPopulated', function() {
				// enforce dimension option
				if(self.editor.media.options.exporter.image.enforceDimension) {
					self.imageEnforceDimensionOption().attr({
						'checked': 'checked',
						'disabled': 'disabled'
					}).parent('.field').removeClass('hide-field-content');
				}

				self.applyConstrain(options);
			});

			// self.imageEnforceWidth().data("default", width);
			// self.imageEnforceHeight().data("default", height);
		},

		"{imageVariation} click": function(el) {
			self.applyConstrain();
		},

		"{imageEnforceDimensionOption} change": function(el, event) {
			event.stopPropagation();

			el.parent(".field").toggleClass("hide-field-content", !el.is(":checked"));

			self.transform();

			// self.imageEnforceWidth().trigger("keyup");
		},

		"{imageEnforceRatio} click": function(el) {
			el.toggleClass("locked");

			if (el.hasClass("locked")) {
				self.imageEnforceLockRatio().attr('checked', 'checked');
			} else {
				self.imageEnforceLockRatio().removeAttr('checked');
			}

			self.imageEnforceLockRatio().trigger('change');

			if(el.hasClass("locked")) {
				self.transform();
			}
		},

		"{self} previewImage": function() {
			self.transform();
		},

		"{imageEnforceWidth} keyup": function() {
			self.transform();
		},

 		"{imageEnforceHeight} keyup": function() {
			self.transform();
 		},

		"{imageEnforceWidth} blur": function(el) {
			if($.trim(el.val()) == '' && !self.imageEnforceLockRatio().is(':checked')) {
				el.val(el.data('initial'));
			}

			self.transform();
		}, 		

		"{imageEnforceHeight} blur": function(el) {
			if($.trim(el.val()) == '' && !self.imageEnforceLockRatio().is(':checked')) {
				el.val(el.data('initial'));
			}

			self.transform();
		},

		transform: function() {
			var image = self.editor.previewImage();

			if(image === undefined) return;

			var dimensions = {};

			// Enforce image dimension
			if (self.imageEnforceDimensionOption().is(":checked")) {
				dimensions = {
					width: self.imageEnforceWidth().val(),
					height: self.imageEnforceHeight().val()
				};
			} else {
				var variation = self.editor.currentImageVariation();

				variation = variation === undefined ? self.editor.meta() : variation.data('variation');

				dimensions = {
					width: variation.width,
					height: variation.height
				};
			}

			if(image.width() !== dimensions.width || image.height() !== dimensions.height) {
				image.css(dimensions);

				// if dimension changed, trigger change in dimension
				self.editor.trigger('dimensionEnforced');
			}

			self.editor.preview.resetLayout();
		},

		applyConstrain: function(options) {
			var variation = self.editor.currentImageVariation() === undefined ? self.editor.meta() : self.editor.currentImageVariation().data('variation'),
				dimensions = {
					source: {
						width: variation.width,
						height: variation.height
					}
				};

			if(self.editor.media.options.exporter.image.enforceDimension) {
				dimensions.allowedMax = {
					width: self.editor.media.options.exporter.image.enforceWidth,
					height: self.editor.media.options.exporter.image.enforceHeight
				}
			}

			options = $.extend(true, {}, dimensions, options);

			self.imageEnforceDimension().constrain(options);

			self.transform();
		}
	}}
);

module.resolve();

});
// require: end

});
// module: end
