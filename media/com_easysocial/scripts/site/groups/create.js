EasySocial.module('site/groups/create', function($) {

var module	= this;

EasySocial.require()
.script('shared/fields/validate', 'shared/fields/base')
.done(function($) {

EasySocial.Controller('Groups.Create', {
	defaultOptions: {
		'previousLink': null,
		'{field}': '[data-field]',
		"{form}": "[data-form]",
		"{previous}": "[data-previous]",
		"{next}": "[data-next]"
	}
}, function(self, options) { return {

	init: function() {
		self.field().addController('EasySocial.Controller.Field.Base');
	},

	"{previous} click": function() {
		window.location = options.previousLink;
	},

	"{next} click": function(button, event) {

		if (!button.enabled()) {
			return false;
		}

		// Set it to disabled
		button.disabled(true);
		button.addClass('is-loading');

		self.element
			.validate()
			.done(function() {
				button.removeClass('is-loading');
				button.enabled(true);

				self.form().submit();
			}).fail(function() {
				button.removeClass('is-loading');
				button.enabled(true);

				EasySocial.dialog({
					"content": EasySocial.ajax('site/views/profile/showFormError')
				});
			});
	}

}});

module.resolve();
});
});

