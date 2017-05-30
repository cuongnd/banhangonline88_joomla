EasySocial.module( 'site/dashboard/sidebar' , function($){

	var module 				= this;

	EasySocial.require()
	.library( 'history' )
	.done(function($){

		EasySocial.Controller('Dashboard.Sidebar', {
			
			defaultOptions: {
				"{menuItem}"	: "[data-dashboardSidebar-menu]",
				"{filterBtn}"	: "[data-stream-filter-button]",
				"{editIcon}" 	: "[data-dashboardFeeds-Filter-edit]"
			}
		}, function(self) {

			return{

				init: function()
				{
				},
				
				"{menuItem} click" : function( el , event )
				{
					// Remove all active class.
					self.menuItem().removeClass( 'active' );

					// Add active class on this item.
					$( el ).addClass( 'active' );
				},

				"{editIcon} click" : function( el , event )
				{
					event.preventDefault();

					$(el).route();
					
					var id = el.data( 'id' );

					// Notify the dashboard that it's starting to fetch the contents.
					self.parent.content().html("");
					self.parent.updatingContents();

					EasySocial.ajax('site/controllers/stream/getFilter', {
						"id": id
					}).done(function(contents) {
						self.parent.updateContents(contents);
					}).fail(function(messageObj) {
						return messageObj;
					});
				},

				"{filterBtn} click" : function( el , event )
				{
					event.preventDefault();

					// Update the url
					$( el ).route();

					// Notify the dashboard that it's starting to fetch the contents.
					self.parent.content().html("");
					self.parent.updatingContents();

					EasySocial.ajax( 'site/controllers/stream/getFilter' ,
					{
						"id"	: 0
					})
					.done(function( contents )
					{
						// self.dashboard.updateHeading( title , desc );

						self.parent.updateContents( contents );
					})
					.fail( function( messageObj ){

						return messageObj;
					})
					.always(function(){

					});

				}
			}
		});

		module.resolve();
	});

});
