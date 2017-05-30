EasySocial.module('site/popbox/popbox', function($) {
	var module = this;

	EasySocial.module("notifications/popbox", function($){

		this.resolve(function(popbox)
		{
			return {
				content: EasySocial.ajax( "site/controllers/notifications/getSystemItems",
				{
					layout	: "popbox.notifications"
				}),
				id: "fd",
				component: "es",
				type: "notifications",
				cache: false
			};
		});
	});

	EasySocial.module("conversations/popbox", function($){

		this.resolve(function(popbox)
		{
			return {
				content: EasySocial.ajax( "site/controllers/notifications/getConversationItems",
				{
					usemax 	: "1",
					layout	: "popbox.conversations"
				}),
				id: "fd",
				component: "es",
				type: "notifications",
				cache: false
			};
		});
	});

	EasySocial.module("friends/popbox", function($){

		this.resolve(function(popbox){

			return {
				content: EasySocial.ajax( "site/controllers/notifications/friendsRequests",
				{
					layout	: "popbox.friends"
				}),
				id: "fd",
				component: "es",
				type: "notifications",
				cache: false
			};
		});
	});

	module.resolve();
});
