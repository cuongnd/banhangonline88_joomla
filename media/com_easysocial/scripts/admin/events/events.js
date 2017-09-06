EasySocial.module('admin/events/events' , function($) {

	var module = this;

	EasySocial
	.require()
	.library('expanding')
	.done(function($)
	{
		EasySocial.Controller(
			'Events.Pending.Item',
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
						self.options.id = self.element.data('id');
					},

					"{approve} click" : function()
					{
						EasySocial.dialog(
						{
							content 	: EasySocial.ajax('admin/views/events/approveEvent' , { "ids" : self.options.id })
						});
					},

					"{reject} click" : function()
					{
						EasySocial.dialog(
						{
							content		: EasySocial.ajax('admin/views/events/rejectEvent' , { "ids" : self.options.id })
						});
					}
				}
			});

		module.resolve();
	});

});