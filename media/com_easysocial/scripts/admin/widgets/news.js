EasySocial.module( 'admin/widgets/news' , function($) {

	var module = this;

	EasySocial.require()
	.done(function($){

		EasySocial.Controller(
				'News',
				{
					defaultOptions: 
					{
						
						// Properties
						loadOnInit 	: true,

						// Elements
						"{news}"		: "[data-widget-news] > [data-widget-news-items]",
						"{appNews}"		: "[data-widget-app-news] > [data-widget-news-items]",
						"{placeholder}"	: "[data-widget-news-placeholder]"
					}
				},
				function( self ){

					return {

						init: function()
						{
							// When page loads, obtain the news
							if( self.options.loadOnInit )
							{
								self.getNews();
							}
						},

						/**
						 * Gets the news items from the server.
						 */
						getNews: function()
						{
							EasySocial.ajax( 'admin/controllers/news/getnews' )
							.done(function( content , appsContent )
							{
								// Append the news.
								self.news().append( content );

								// Append the app news
								self.appNews().append( appsContent );

								// Hide placeholder
								self.placeholder().remove();
							});
						}
					}
				}
		);
	
		module.resolve();
	});

});
