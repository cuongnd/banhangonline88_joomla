EasySocial.module("site/albums/editor", function($){

var module = this;

// Constants
var photoEditorController = "EasySocial.Controller.Photos.Editor"

// Non-essential dependencies
EasySocial.require()
.script("site/albums/editor/sortable", "site/albums/editor/uploader")
.done();

// Essential dependencies
var Controller =

EasySocial.Controller("Albums.Editor", {
	hostname: "editor",

	defaultOptions: {

		canReorder: false,
		canUpload: true,

		"{titleField}"        : "[data-album-title-field]",
		"{captionField}"      : "[data-album-caption-field]",
		"{coverField}"        : "[data-album-cover-field]",

		"{type}"			  : "[data-album-type]",
		"{uid}"				  : "[data-album-uid]",


		"{date}"              : "[data-album-date]",
		"{dateCaption}"       : "[data-album-date-caption]",
		"{addDateCaption}"    : "[data-album-addDate-button]",
		"{privacy}"           : "[data-album-privacy]",

		"{uploadButton}"      : "[data-album-upload-button]",
		"{deleteButton}"      : "[data-album-delete-button]",
		"{moreButton}"        : "[data-album-more-button]",

		"{privacy}"			  : "[data-privacy-hidden]",
		"{privacycustom}"	  : "[data-privacy-custom-hidden]",

		"{uploadItem}"        : "[data-photo-upload-item]",

		"{dateDay}"		    : "[data-date-day]",
		"{dateMonth}"		: "[data-date-month]",
		"{dateYear}"		: "[data-date-year]",

		"{editButton}"     : "[data-album-edit-button]",
		"{doneButton}"     : "[data-album-done-button]",
		"{cancelButton}"   : "[data-album-cancel-button]",

		// Location
		"{location}"          : "[data-album-location]",
		"{removeLocation}": "[data-album-location-remove]",
		"{locationCaption}"   : "[data-album-location-caption]",
		"{addLocationButton}" : "[data-album-addLocation-button]",
		"{locationWidget}"  : "[data-album-location-form] .es-locations",
		"{latitude}"        : "[data-location-lat]",
		"{longitude}"       : "[data-location-lng]"
	}
}, function(self, opts) { return {

	init: function() {

		self.id = self.element.data("album-id");

		// If we can sort photos, load & implement sortable.
		if (opts.canReorder) {
			EasySocial.module("site/albums/editor/sortable")
				.done(function(SortableController){
					self.addPlugin("sortable", SortableController);
				});
		}

		// If we can upload photos, load & implement uploader.
		if (opts.canUpload) {
			EasySocial.module("site/albums/editor/uploader")
				.done(function(UploaderController){
					self.uploader = self.addPlugin("uploader", UploaderController);
				});
		}

		// If this is an existing album, there's no need to create album
		if (self.id) {
			self.createAlbum.task = $.Deferred().resolve();
			self.createStream = 0;
		} else {
			self.createStream = 1;
		}
	},

	data: function() {

		var title         	= self.titleField().val(),
			caption       	= self.captionField().val(),
			date          	= self.formatDate(),
			address       	= self.locationCaption().html(),
			latitude      	= self.latitude().val(),
			longitude     	= self.longitude().val(),
			privacy       	= self.privacy().val(),
			privacycustom 	= self.privacycustom().val();
			uid 			= self.element.data( 'album-uid' );
			type 			= self.element.data( 'album-type' );

		obj = {
			id           : self.id,
			uid 		 : uid,
			type 		 : type,
			title        : title,
			caption      : caption,
			date         : date,
			address      : address,
			latitude     : latitude,
			longitude    : longitude,
			privacy      : privacy,
			privacycustom: privacycustom,
			createStream : self.createStream
		};

		return obj;
	},

	createAlbum: function() {

		var task = self.createAlbum.task;

		if (!task) {

			task = self.createAlbum.task =

				self.save({
						createStream: 0
					})
					.done(function(album){
						self.deleteButton().disabled(false);
						self.element.attr("data-album-id", self.id = album.id);
					})
					.fail(function(message, type){
						self.setMessage(message, type);
					});
		}

		return task;
	},

	save: function(options) {

		self.trigger("beforeAlbumSave", [self]);

		// Build save data
		var data = $.extend(self.data(), options);

			data.photos =
				$.map(
					self.album.photoItem(),
					function(photoItem, i){
						var editor = $(photoItem).controller("EasySocial.Controller.Photos.Editor");
						return (editor) ? editor.data() : null;
					});

			// TODO: Get photo ordering
			// data.ordering = self.getPhotoOrdering();

		// Clear any messages
		self.clearMessage();

		// Save album
		var task = EasySocial.ajax( "site/controllers/albums/store" , data );

		// Trigger albumSave event
		self.trigger("albumSave", [task, self]);

		// Return task
		return task;
	},

	"{self} photoAdd": function(el, event, photoItem, photoData) {

		// Set cover if this is the first photo
		if (self.album.photoItem().length <= 1) {
			self.changeCover(photoData);
		}
	},

	setCover: function(photoId) {

		var task =
			EasySocial.ajax(
				"site/controllers/albums/setCover",
				{
					albumId: self.id,
					coverId: photoId
				}
			)
			.done(function(photo){
				self.changeCover(photo);
			})
			.fail(function(){

			});

		return task;
	},

	removeCover: function() {

		self.trigger("coverRemove", [self.album]);
	},

	changeCover: function(photo) {

		self.trigger("coverChange", [photo, self]);
	},

	"{self} coverChange": function(el, event, photo) {

		self.coverField()
			.removeClass("no-cover")
			.css("backgroundImage", $.cssUrl(photo.sizes.thumbnail.url));
	},

	"{self} coverRemove": function() {

		self.coverField()
			.addClass("no-cover")
			.css("backgroundImage", "");
	},

	"{editButton} click": function(editButton, event) {
		event.preventDefault();
		event.stopPropagation();

		// Change viewer layout
		self.album.setLayout("form");

		// Route the button
		editButton.find('a').route();
	},

	"{editButtonLink} click": function(editButtonLink, event) {

		event.preventDefault();
	},

	"{cancelButton} click": function(cancelButton, event) {
		event.preventDefault();
		event.stopPropagation();

		// Change viewer layout
		self.album.setLayout("item");

		// Change address bar url
		cancelButton.route();
	},

	"{doneButton} click": function(doneButton, event) {
		event.preventDefault();
		event.stopPropagation();

		doneButton.addClass('is-loading');

		self.save({
			finalized : 1
		}).done(function(album, html){
			doneButton.removeClass('is-loading');

			// hide unfinalized message if there is any.
			$('[data-album-unfinalized-label]').hide();

			$.buildHTML(html).replaceAll(self.element);
		}).progress(function(message, type){
			self.setMessage(message, type);
		});
	},


	"{deleteButton} click": function(deleteButton) {

		if (deleteButton.disabled()) return;

		EasySocial.dialog({
			content: EasySocial.ajax("site/views/albums/confirmDelete", {id: self.id})
		});
	},

	formatDate: function() {
		var day = self.dateDay().val() || self.dateDay().data('date-default'),
			month = self.dateMonth().val() || self.dateMonth().data('date-default'),
			year = self.dateYear().val() || self.dateYear().data('date-default');

		return year + '-' + month + '-' + day;
		},

	updateDate: function() {

		self.date().addClass("has-data");
		var dateCaption = self.dateDay().val() + ' ' + $.trim(self.dateMonth().find(":selected").html()) + ' ' + self.dateYear().val();
		self.dateCaption().html(dateCaption);
	},

	"{dateDay} keyup": function() {
		self.updateDate();
	},

	"{dateMonth} change": function() {
		self.updateDate();
	},

	"{dateYear} keyup": function() {
		self.updateDate();
	},

	"{titleField} keyup": function(titleField) {

		self.trigger("titleChange", [titleField.val(), self]);
	},

	"{removeLocation} click": function(link, event) {
		event.preventDefault();
		event.stopPropagation();

		// Reset the location
		self.location().removeClass('has-data');
		self.locationCaption().empty();
		self.latitude().val('');
		self.longitude().val('');
	},

	"{locationWidget} locationChange": function(element, event, location) {
		var address = location.name || location.address || location.fulladdress || location.formatted_address;
		var controller = self.locationWidget().controller();

		// Set the address in the caption
		self.locationCaption().html(address);
		self.location().addClass("has-data");

		// We should now hide the location suggestion popup
		self.addLocationButton().click();
	}

}});

module.resolve(Controller);

});
