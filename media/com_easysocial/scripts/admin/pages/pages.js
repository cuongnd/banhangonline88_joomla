EasySocial.module('admin/pages/pages' , function($) {

	var module = this;

	EasySocial
	.require()
	.library('expanding')
	.done(function($)
	{
		EasySocial.Controller(
			'Pages.Pending.Item',
			{
				defaultOptions:
				{
					"{approve}" : "[data-pending-approve]",
					"{reject}"	: "[data-pending-reject]"
				}
			},
			function(self) {
				return {
					init: function()
					{
						self.options.id 	= self.element.data('id');
					},

					"{approve} click" : function()
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax('admin/views/pages/approvePage' , { "ids" : self.options.id })
						});
					},

					"{reject} click" : function()
					{
						EasySocial.dialog(
						{
							content		: EasySocial.ajax('admin/views/pages/rejectPage' , { "ids" : self.options.id })
						});
					}
				}
			});

		module.resolve();
	});

});