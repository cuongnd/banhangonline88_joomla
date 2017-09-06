EasySocial.module('site/api/oauth', function($) {

var module = this;

$(document).on('click', '[data-oauth-login-button]', function() {
	
	var button = $(this);

	// Implement the controller on the parent
	var parent = button.closest("[data-oauth-login]");
	var controller = "EasySocial.Controller.OAuth.Login";

	if (parent.length < 1) {
		return;
	}
	
	if (parent.hasController(controller)) {
		return;
	}

	parent.addController(controller)
		.openDialog();
});


EasySocial.Controller('OAuth.Login', {
	defaultOptions: {
		"popup": {
			"width": 500,
			"height": 520
		},
		"{button}": "[data-oauth-login-button]"
	}
}, function(self, opts, base) { return {

	init: function() {
		opts.url = base.data('url');
	},

	openDialog : function() {
		var width = opts.popup.width;
		var height = opts.popup.height;
		var url = opts.url || self.button().data('url');
		var left = (screen.width/2) - (width / 2);
		var top = (screen.height/2) - (height / 2);

		window.open(url , "" , 'scrollbars=no,resizable=no,width=' + width + ',height=' + height + ',left=' + left + ',top=' + top);
	},

	"{button} click": function(button, event) {
		self.openDialog();
	}
}});


module.resolve();
});
