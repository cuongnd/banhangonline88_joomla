EasySocial.module("site/api/photos", function($){

var module = this;

// Non-essential dependencies
EasySocial.require()
.script("site/photos/popup")
.done();

var DialogController = EasySocial.Controller("Photos.Dialog", { 
}, function(self) { return {

	init: function() {
		EasySocial.photos.selectPhoto = self.show;
	},

	show: function(options) {
		var task = $.Deferred();
		var dialog = EasySocial.ajax( "site/views/albums/dialog" , { "uid" : options.uid , "type" : options.type });
		var browser = EasySocial.require().script("site/albums/browser").done();

		// Show a loading indicator first
		EasySocial.dialog(
			$.extend({
			    content: task
			}, options)
		);

		$.when(browser, dialog)
			.done(function(){
				dialog.done(function(html){
					task.resolve(html);
				});
			});
	}

}});

EasySocial.Controller("Photos", {
}, function(self) { return {

	init: function() {

		// Extend EasySocial object
		EasySocial.photos = self;

		// Popup plugin
		EasySocial.module("site/photos/popup")
			.done(function(PopupController){
				self.popup = self.addPlugin("popup", PopupController);
			});

		// Dialog plugin
		// EasySocial.module("site/photos/dialog")
			// .done(function(DialogController){
		self.dialog = self.addPlugin("dialog", DialogController);
			// });
	},

	crop: function(id, options) {

		if (id == undefined) {
			return;
		}

		if (!options) {
			options = {};
		}

		var avatarOptions = { "id" : id };

		if (options.uid) {
			avatarOptions.uid = options.uid;
			delete options.uid;
		}

		if (options.type) {
			avatarOptions.type 	= options.type;
			delete options.type;
		}

		if (options.redirect) {

			// Legacy
			avatarOptions.redirect = options.redirect;

			// New way of passing redirect url
			avatarOptions.return = options.redirect;

			delete options.redirect;
		}


		EasySocial.dialog($.extend({
			"content": EasySocial.ajax('site/views/avatar/crop', avatarOptions)
		}, options));
	}

}});

// Add this controller to the html body;
$(function(){
	$("body").addController("EasySocial.Controller.Photos");
});

module.resolve();

});