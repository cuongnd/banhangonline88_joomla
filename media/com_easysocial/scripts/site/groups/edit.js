EasySocial.module('site/groups/edit', function($) {

var module	= this;

EasySocial.require()
.script('shared/fields/validate', 'shared/fields/base')
.done(function($) {
EasySocial.Controller(
	'Groups.Edit', {
		defaultOptions: {
			id: null,

			"{stepContent}": "[data-group-edit-fields-content]",
			"{stepItem}": "[data-group-edit-fields-step]",

			// Forms.
			"{profileForm}": "[data-group-fields-form]",

			// Content for profile editing
			"{profileContent}": "[data-group-edit-fields]",

			"{fieldItem}": "[data-group-edit-fields-item]",

			// Submit buttons
			"{save}"			: "[data-group-fields-save]"
		}
	}, function(self) {
		return {

			init: function()
			{
				self.fieldItem().addController('EasySocial.Controller.Field.Base', {
					mode: 'edit'
				});
			},

			errorFields: [],

			// Support field throwing error internally
			'{fieldItem} error': function(el, ev)
			{
				self.triggerStepError(el);
			},

			// Support for field resolving error internally
			'{fieldItem} clear': function(el, ev)
			{
				self.clearStepError(el);
			},

			// Support validate.js throwing error externally
			'{fieldItem} onError': function(el, ev)
			{
				self.triggerStepError(el);
			},

			triggerStepError: function(el)
			{
				var fieldid = el.data('id'),
					stepid = el.parents(self.stepContent.selector).data('id');

				if($.inArray(fieldid, self.errorFields) < 0)
				{
					self.errorFields.push(fieldid);
				}

				self.stepItem().filterBy('for', stepid).trigger('error');
			},

			clearStepError: function(el)
			{
				var fieldid = el.data('id'),
					stepid = el.parents(self.stepContent.selector).data('id');

				self.errorFields = $.without(self.errorFields, fieldid);

				self.stepItem().filterBy('for', stepid).trigger('clear');
			},

			"{stepItem} click" : function(el, event)
			{
				var id 	= $(el).data('for');

				// Profile form should be hidden
				self.profileContent().show();

				// Hide all profile steps.
				self.stepContent().hide();

				// Remove active class on step item
				self.stepItem().removeClass('active');

				// Add active class on the selected item.
				el.addClass('active');

				// Get the step content element
				var stepContent = self.stepContent('.step-' + id);

				// Show active profile step.
				stepContent.show();

				// Trigger onShow on the field item in the content
				stepContent.find(self.fieldItem.selector).trigger('show');
			},

			"{stepItem} error": function(el)
			{
				el.addClass('error');
			},

			"{stepItem} clear": function(el)
			{
				if(self.errorFields.length < 1)
				{
					el.removeClass('error');
				}
			},

			"{save} click" : function(el, event)
			{
				// Run some error checks here.
				event.preventDefault();

				el.addClass('btn-loading');

				self.profileForm()
					.validate()
					.fail(function()
					{
						el.removeClass('btn-loading');
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax('site/views/profile/showFormError')
						});
					})
					.done(function()
					{
						self.profileForm().submit();
					});

				return false;
			}
		}
	}
);


module.resolve();
});
});

