EasySocial.module("site/story/friends", function($){

var module = this;

EasySocial.require()
.library("textboxlist")
.done(function(){

	EasySocial.Controller("Story.Friends", {
		defaultOptions: {
			"{wrapper}": "[data-friends-wrapper]",
			"{field}": "[data-friends-wrapper] [data-textboxlist-textfield]"
		}
	}, function(self){ return {

		init: function() {

			// Apply placeholder compatibility
			self.field().placeholder();

			// Friend tagging
			self.wrapper()
				.textboxlist({
					component: 'es',
					plugin: {
						autocomplete: {
							exclusive : true,
							cache: false,
							query: self.search,
							component: "es",
							modifier: "es-story-friends-autocomplete",
							sticky: true,
							shadow: true
						}
					}
				});
		},

		search: function(keyword) {

			var users = self.getTaggedUsers();

			return EasySocial.ajax("site/controllers/friends/suggest", {
					   	   "search": keyword,
					   	   "exclude": users
					   });
		},

		getTaggedUsers: function() {

			var users = [];
			var items = $("[data-textboxlist-item]");

			if (items.length <= 0) {
				return users;
			}

			$.each(items, function(i, element) {

				var id = $(element).data('id');

				users.push(id);
			});

			return users;
		},

		"{wrapper} filterItem": function(el, event, item) {

			var html = $('<div/>').html(item.html);
			var title = html.find('[data-suggest-title]').text();
			var id = html.find('[data-suggest-id]').val();

			item.id = id;
			item.title = title;
			item.menuHtml = item.html;
		},

		mention: function(mode, query, callback) {

			self.search(query)
				.done(function(users){

					var friends = [];

					$.each(users, function(i, user) {
						friends.push({
							id: user.id,
							name: user.screenName,
							avatar: user.avatar,
							type: 'contact'
						});
					});

					callback(friends);
				});
		},

		updateMeta: function() {

			var controller = self.wrapper().controller("textboxlist");
			var friends = controller.getAddedItems();

			if (friends.length < 1) {
				self.story.setMeta("friends", "");
				return;
			}

			var ids = [];

			$.each(friends, function(i, user) {
				ids.push(user.id);
			});

			EasySocial.ajax('site/views/story/buildStoryMeta', {
				"ids": ids
			}).done(function(caption) {
				self.story.setMeta('friends', caption);
			});
		},

		"{wrapper} addItem": function() {
			self.updateMeta();
		},

		"{wrapper} removeItem": function() {
			self.updateMeta();
		},

		"{story} activateMeta": function(el, event, meta) {

			if (meta.name==="friends") {
				setTimeout(function(){
					self.field().focus();
				}, 1);
			}
		},

		"{story} save": function(el, event, save) {

			var controller = self.wrapper().controller("textboxlist");

			var tags = controller.getAddedItems().map(function(friend){
				return friend.id;
			});

			save.data['friends_tags'] = tags;
		},

		"{story} clear": function() {

			var controller = self.wrapper().controller("textboxlist");
			controller.clearItems();
		}

	}});

	// Resolve module
	module.resolve();

});

});
