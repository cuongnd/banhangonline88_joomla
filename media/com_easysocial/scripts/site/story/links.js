EasySocial.module("site/story/links", function($){

var module = this;

EasySocial.Controller("Story.Links", {
	defaultOptions: {

		// urlParser: /(^|\s)((https?:\/\/)?[\w-]+(\.[\w-]+)+\.?(:\d+)?(\/\S*)?)/gi,
		// /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi,
		// urlParser: /(^|\s)(https?:\/\/)?(([a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.([a-z]{2,7}))|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(:[0-9]{1,5})?(\/.*)?/gi,
		urlParser: /(((http|https):\/{2})+(([0-9a-z_-]+\.)+(aero|asia|biz|cat|com|coop|edu|gov|club|info|int|jobs|mil|mobi|museum|name|net|org|pro|tel|travel|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cu|cv|cx|cy|cz|cz|de|dj|dk|dm|do|dz|ec|ee|eg|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mn|mn|mo|mp|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|nom|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ra|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw|arpa|live|today)(:[0-9]+)?((\/([~0-9a-zA-Z\#\!\=\+\%@\.\/_-]+))?(\?[0-9a-zA-Z\+\%@\/&\[\];=_-]+)?)?))\b/gi,
		// urlParser: /^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,}))\.?)(?::\d{2,5})?(?:[/?#]\S*)?$/i,
		validateUrl: false,

		// Attachment item
		"{linkForm}": "[data-story-link-form]",
		"{linkInput}": "[data-story-link-input]",

		"{linkContent}": "[data-story-link-content]",
		"{linkItem}": "[data-story-link-item]",
		"{linkTitle}": "[data-story-link-title]",
		"{linkDescription}": "[data-story-link-description]",
		"{linkImages}": "[data-story-link-images]",
		"{linkImage}": "[data-story-link-image]",
		"{imageWrapper}": "[data-story-link-image-wrapper]",

		"{linkVideo}": "[data-story-link-video]",
		"{titleTextfield}": "[data-story-link-title-textfield]",
		"{descriptionTextfield}": "[data-story-link-description-textfield]",

		"{panelButton}"		: "[data-story-link-panel-button]",
		"{attachButton}"    : "[data-story-link-attach-button]",
		"{removeButton}"    : "[data-story-link-remove-button]",
		"{removeThumbnail}"	: "[data-story-link-remove-image]"
	}
}, function(self, opts, base) {
	
	return {
	
	init: function() {
		self.linkInput().placeholder();
	},

	activateAttachment: function() {

		if (self.doNotFocus) {
			return;
		}

		setTimeout(function(){
			self.linkInput().focus();
			self.doNotFocus = false;
		}, 500);
	},

	//
	// Link manipulation
	//
	links: {},

	currentLink: null,

	crawling: false,

	extractUrls: function(str) {

		var urlParser = self.options.urlParser;
		var urls = str.match(urlParser);

		// Discard non http/https protocols
		if ($.isArray(urls)) {
			return $.map(urls, function(url, i){
				return $.trim(url);
			});
		} else {
			return [];
		}
	},

	fixUrl: function(url) {

		// If there's no protocol, use "http".
		var url = $.uri(url);

		if (!/http|https/.test(url.protocol())) {
			url.setProtocol("http");
		}

		return url.toString();
	},

	getLink: function(urls) {

		// If a block of string was given,
		// extract urls from it.
		if ($.isString(urls)) {
			urls = self.extractUrls(urls);
		}

		// If there are no urls, stop.
		if (urls.length < 0) return;

		// Get only the first url
		var url = urls[0];

		// If this is a new url,
		// create a new link object for it.
		link = self.links[url] || self.createLink(url);

		// When the link is resolved,
		// add link to the attachment item.
		return link;
	},

	createLink: function(url) {

		// Create a new link object
		var link = self.links[url] = $.Deferred();

		// Add url property
		link.url = url;

		self.crawling = true;

		// Get link info from crawler
		EasySocial.ajax('site/controllers/crawler/fetch', {
			"url": url,
			"preview": 1
		}).done(function(data, preview) {

			if (!data) {
				link.reject();
			}

			// Create link item
			link.item = $(preview).data("link", data);

			link.item.addController(EasySocial.Controller.Story.Links.Preview);

			link.resolve(link);
		})
		.fail(function(){
			link.reject();
		})
		.always(function() {
			self.crawling = false;
		});

		return link;
	},

	addLink: function(link) {

		// Add link item to attachment item
		self.linkContent()
			.empty()
			.append(link.item);

		self.linkForm()
			.hide();

		self.currentLink = link;
	},

	removeLink: function() {

		self.linkItem()
			.detach();

		self.linkForm()
			.show();

		self.currentLink = null;
	},

	checkAllowedPanel: function() {

		var allowedPanel = ["links", "text"];
		var allow = false;

		$.each(allowedPanel, function(index, panelName){
			var pluginSelector = '[data-story-plugin-name="' + panelName + '"]';
			var panel = $(pluginSelector);

			if (panel.hasClass('active')) {
				allow = true;
				return false;
			}
		});

		return allow;
	},

	checkVideoLink: function(url) {

		var videoURL = url.match(/(youtube|youtu|vimeo|dailymotion)\.(com|be)\/((watch\?v=([-\w]+))|(video\/([-\w]+))|([-\w]+))/);

		if (videoURL !== null) {
			return true;
		}

		return false;
	},

	//
	// Link form
	//
	"{attachButton} click": function() {

		var linkInput = self.linkInput();
		var linkForm  = self.linkForm();
		var url = $.trim(self.linkInput().val());

		// If there's no url, stop.
		if (url === "") {
			return;
		}

		// Fix the url
		url = self.fixUrl(url);

		// Set fixed link back to input box
		self.linkInput().val(url);

		// Set link form as busy
		linkForm.addClass("busy");

		// Get link
		self.getLink(url)
			.done(function(link){
				self.addLink(link);
			})
			.always(function(){
				linkForm.removeClass("busy");
			});
	},

	"{removeButton} click": function(button) {

		self.currentLink.disabled = true;

		self.removeLink();
	},


	"{linkInput} keyup": function(input, event) {

		if (event.keyCode == 13) {
			self.attachButton().click();
		}
	},

	"{story.textField} input": $._.debounce(function(textField, event) {

		// Don't look for links if we've already added one
		if (self.currentLink || self.crawling) {
			return;
		}

		// Retrieve the last typed url
		var content = textField.val();
		var urls = self.extractUrls(content);
		var url = urls[urls.length - 1];

		if (!url) {
			return;
		}

		videoLink = self.checkVideoLink(url);

		// We let video panel to handle video link
		if (videoLink) {
			$(window).trigger('easysocial.story.video.panel.insertvideolink', [url]);
			return;
		}		

		// Check for allowed panel for the link to active
		if (!self.checkAllowedPanel()) {
			return;
		}

		// Check if link has been crawled before
		var url = self.fixUrl(url);
		var link = self.links[url];

		if (link && link.disabled) {
			return;
		}

		if (self.options.validateUrl) {
			var failed = false;

			EasySocial.ajax('site/controllers/crawler/validate', {
				"url": url
			}).done(function() {

				// Set the url as the value
				self.linkInput().val(url);

				// Do not focus when attachment is activated
				self.doNotFocus = true;

				// Trigger links attachment
				self.panelButton().click();

				// Add link
				self.attachButton().click();
			});

			return;
		}

		// Set the url as the value
		self.linkInput().val(url);

		// Do not focus when attachment is activated
		self.doNotFocus = true;

		// Trigger links attachment
		self.panelButton().click();

		// Add link
		self.attachButton().click();

	}, 950),

	//
	// Saving
	//
	"{story} save": function(element, event, save) {
		
		if (!self.currentLink) {
			return;
		}

		var data = {
					title: self.titleTextfield().val(),
					description: self.descriptionTextfield().val(),
					url: self.currentLink.url,
					video: self.linkVideo().val()
				};

		if (!self.removeThumbnail().is(":checked")) {

			data.image = self.imageWrapper('.active')
							.find('img')
							.attr('src');
		}

		save.addData(self, data);
	},

	"{story} clear": function() {

		self.linkInput().val("");

		self.removeLink();
	}
}});

EasySocial.Controller('Story.Links.Preview', {
	defaultOptions: {

		"{previousImage}": "[data-story-link-image-prev]",
		"{nextImage}": "[data-story-link-image-next]",
		"{image}": "[data-story-link-image]",
		"{imageWrapper}": "[data-story-link-image-wrapper]",
		"{imagesWrapper}"	: "[data-story-link-images]",
		"{imageIndex}"		: "[data-story-link-image-index]",
		"{removeThumbnail}"	: "[data-story-link-remove-image]",
		"{imageDimensions}": "[data-story-link-image-dimensions]",
		"{imageWidth}": "[data-image-width]",
		"{imageHeight}": "[data-image-height]",

		"{title}": "[data-story-link-title]",
		"{description}": "[data-story-link-description]",
		"{titleTextfield}"      : "[data-story-link-title-textfield]",
		"{descriptionTextfield}": "[data-story-link-description-textfield]"
	}
}, function(self, opts, base) { 

	return {

	init: function() {

		// Init dimensions
		self.initDimensions();
	},

	initDimensions: function() {

		// When the images are loaded, set the width and height accordingly.
		self.image()
			.on('load', function() {
				var width = this.naturalWidth;
				var height = this.naturalHeight;

				var wrapper = $(this).parent();

				// Set the width and height on the width and height
				wrapper.find(self.imageWidth.selector)
					.html(width);

				wrapper.find(self.imageHeight.selector)
					.html(height);
			});
	},

	"{removeThumbnail} click" : function() {
		var isChecked = self.removeThumbnail().is(':checked');

		if (isChecked) {
			self.imagesWrapper().hide();
		} else {
			self.imagesWrapper().show();
		}

		self.element.toggleClass("has-images", !isChecked);
	},

	"{previousImage} click" : function() {
		var currentImage = self.imageWrapper('.active');
		var prevImage = currentImage.prev();
		var index = parseInt(self.imageIndex().html());
		var nextIndex = index - 1;

		if (prevImage.length > 0) {
			currentImage.removeClass('active');
			prevImage.addClass('active');

			self.imageIndex().html(nextIndex);
		}
	},

	"{nextImage} click" : function() {
		var currentImage = self.imageWrapper('.active');
		var nextImage = currentImage.next();

		var index = parseInt(self.imageIndex().html());
		var nextIndex = index + 1;

		if (nextImage.length > 0) {
			currentImage.removeClass('active');
			nextImage.addClass('active');

			self.imageIndex().html(nextIndex);
		}
	},

	"{title} click": function() {

		var editingTitle = self.element.hasClass("editing-title");

		self.element.toggleClass("editing-title", !editingTitle);

		if (!editingTitle) {
			self.editTitle();
		}
	},

	editTitleEvent: "click.es.story.editLinkTitle",

	editTitle: function() {

		self.element.addClass("editing-title");

		setTimeout(function(){

			self.titleTextfield()
				.val(self.title().text())
				.focus()[0].select();

			$(document).on(self.editTitleEvent, function(event) {
				if (event.target!==self.titleTextfield()[0]) {
					self.saveTitle("save");
				}
			});
		}, 1);
	},

	saveTitle: function(operation) {

		if (!operation) {
			operation = save;
		}

		var value = self.titleTextfield().val();

		if (operation == 'save') {
			if (value === '') {
				value = self.title().data('default');
			}

			self.title().html(value);
		}

		self.element.removeClass("editing-title");

		$(document).off(self.editTitleEvent);
	},

	"{titleTextfield} keyup": function(el, event) {

		// Escape
		if (event.keyCode==27) {
			self.saveTitle("revert");
		}
	},

	"{description} click": function() {

		var editingDescription = self.element.hasClass("editing-description");

		self.element.toggleClass("editing-description", !editingDescription);

		if (!editingDescription) {
			self.editDescription();
		}
	},

	editDescriptionEvent: "click.es.story.editLinkDescription",

	editDescription: function() {

		self.element.addClass("editing-description");

		setTimeout(function(){

			var descriptionClone = self.description().clone(),
				noDescription = descriptionClone.hasClass("no-description");

			descriptionClone.wrapInner(self.descriptionTextfield());

			if (noDescription) {
				self.descriptionTextfield().val("");
			}

			// self.descriptionTextfield()
			// 	.expandingTextarea();

			self.descriptionTextfield()
				.focus()[0].select();

			$(document).on(self.editDescriptionEvent, function(event) {

				if (event.target!==self.descriptionTextfield()[0]) {
					self.saveDescription("save");
				}
			});
		}, 1);
	},

	saveDescription: function(operation) {
		if (!operation) operation = save;

		var value = self.descriptionTextfield().val().replace(/\n/g, "<br//>");

		switch (operation) {

			case "save":

				var noValue = (value==="");

				self.description()
					.toggleClass("no-description", noValue);

				if (noValue) {
					value = self.descriptionTextfield().attr("placeholder");
				}

				self.description()
					.html(value);
				break;

			case "revert":
				break;
		}

		self.element.find(".textareaClone").remove();

		self.element.removeClass("editing-description");

		$(document).off(self.editDescriptionEvent);
	},

	"{descriptionTextfield} keyup": function(el, event) {
		// Escape
		if (event.keyCode==27) {
			self.saveDescription("revert");
		}
	}
}});

module.resolve();

});