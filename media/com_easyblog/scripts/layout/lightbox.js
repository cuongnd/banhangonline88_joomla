EasyBlog.module('layout/lightbox', function($) {

	var module = this;

	EasyBlog.require()
		.library('fancybox')
		.stylesheet('fancybox/default')
		.script('legacy')
		.done(function(){

			/**
			 * Initializes all the gallery stuffs here
			 **/
			// Init fancy box images.
			if (window.eblog_enable_lightbox) {

				var options = {
					showOverlay: true,
					centerOnScroll: true,
					overlayOpacity: 0.7
				}

				if (!window.eblog_lightbox_title) {
					options.helpers = { title: false };
				}

				if (window.eblog_lightbox_enforce_size) {
					options.maxWidth = window.eblog_lightbox_width;
					options.maxHeight = window.eblog_lightbox_height;
				}

				eblog.images.initFancybox('a.easyblog-thumb-preview', options);
			}

			eblog.images.initCaption('img.easyblog-image-caption');

			module.resolve();
		});
});