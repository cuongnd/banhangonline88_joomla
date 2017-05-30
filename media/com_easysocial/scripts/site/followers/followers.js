EasySocial.module( 'site/followers/followers' , function($){

	var module 	= this;

	EasySocial.require()
	.view( 'site/loading/small' )
	.script( 'site/conversations/composer' )
	.done(function($){

		EasySocial.Controller(
			'Followers',
			{
				defaultOptions :
				{
					"{content}"	: "[data-followers-content]",
					"{filter}"	: "[data-followers-filter]",
					"{items}"	: "[data-followers-item]",
					"{followingCounter}" : "[data-following-count]",
					view :
					{
						loader 				: "site/loading/small"
					}
				}
			},
			function( self )
			{
				return {

					init : function()
					{
						self.initItemController();
					},

					initItemController: function()
					{
						self.items().implement( EasySocial.Controller.Followers.Item ,
						{
							"{parent}"	: self
						});
					},

					updateFollowingCounter: function( value )
					{
						var current 	= self.followingCounter().html(),
							updated		= parseInt( current ) + value;

						self.followingCounter().html( updated );
					},

					updateContents : function( contents )
					{
						self.content().replaceWith( contents );
					},

					"{filter} click" : function(filter, event) {

						var type 	= filter.data( 'followers-filter-type' ),
							title 	= filter.data( 'followers-filter-title' ),
							id 		= filter.data( 'followers-filter-id' ),
							url 	= filter.data( 'followers-filter-url' );

						// Remove active class on all filters
						self.filter().removeClass("active");
						
						// Add active class to current filter
						filter.addClass("active");

						History.pushState({state:1}, title, url);

						EasySocial.ajax(
							"site/controllers/followers/filter",
							{
								id: id,
								type: type
							})
							.done(function(contents){
								self.updateContents(contents);
								self.initItemController();
							});
					}
				}
			});

			EasySocial.Controller(
				'Followers.Item',
				{
					defaultOptions : 
					{
						"{unfollowButton}"	: "[data-followers-item-unfollow]",
						"{composer}"		: "[data-followers-item-compose]"
					}
				},
				function( self )
				{
					return {
						init : function()
						{
							self.options.id 			= self.element.data( 'id' );

							self.initComposer();
						},

						initComposer: function()
						{
							self.composer().implement( EasySocial.Controller.Conversations.Composer.Dialog,
							{
								"recipient"	:
								{
									"id"	: self.options.id
								}
							});
						},

						"{unfollowButton} click" : function()
						{
							EasySocial.dialog(
							{
								content 	: EasySocial.ajax( 'site/views/followers/confirmUnfollow' , { 'id' : self.options.id }),
								bindings 	:
								{
									"{unfollowButton} click" : function()
									{
										EasySocial.ajax( 'site/controllers/followers/unfollow' , { "id" : self.options.id} )
										.done(function()
										{
											// Update the counter
											self.parent.updateFollowingCounter( -1 );

											// Remove this item
											self.element.remove();

											EasySocial.dialog().close();
										});
									}
								}
							});
						}
					}
				});
		module.resolve();
	});
});
