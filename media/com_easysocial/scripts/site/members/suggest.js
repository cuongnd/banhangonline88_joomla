EasySocial.module('site/members/suggest', function($){

var module = this;

EasySocial.require()
.library('textboxlist')
.done(function($) {

EasySocial.Controller('Members.Suggest', {
	defaultOptions: {
		max: null,
		exclusive: true,
		exclusion: [],
		minLength: 1,
		highlight: true,
		uid: "",
		name: "user_id",
		type: "",

		// Namespace to query for suggestions
		"query": {
			"members": "site/controllers/members/suggest"
		},

		includeSelf: false,
		showNonFriend: false
	}
}, function(self, opts, base) { return {

	init: function() {

		// Implement the textbox list on the implemented element.
		self.element
			.textboxlist({
				"component": 'es',
				"name": opts.name,
				"max": opts.max,
				"plugin": {
					"autocomplete": {
						"exclusive": opts.exclusive,
						"minLength": opts.minLength,
						"highlight": opts.highlight,
						"showLoadingHint": true,
						"showEmptyHint": true,

						query: function(keyword) {

							var options = {
											"search": keyword,
											"type": opts.type,
											"inputName": opts.name,
											"uid": opts.uid
										};

							return EasySocial.ajax(opts.query.members, options);
						}
					}
				}
			})
			.textboxlist("enable");
	},

	"{self} filterItem": function(el, event, item) {

		var html = $('<div/>').html(item.html);
		var title = html.find('[data-suggest-title]').text();
		var id = html.find('[data-suggest-id]').val();

		item.id = id;
		item.title = title;
		item.menuHtml = item.html;
	},

	"{self} filterMenu": function(el, event, menu, menuItems, autocomplete, textboxlist) {

		// Get list of excluded users
		var items = textboxlist.getAddedItems();
		var users = $.pluck(items, "id");
		var users = users.concat(self.options.exclusion);

		menuItems.each(function(){

			var menuItem = $(this);
			var item = menuItem.data("item");

			// If this user is excluded, hide the menu item
			menuItem.toggleClass("hidden", $.inArray(item.id.toString(), users) > -1);
		});
	}

}});

module.resolve();
});

});

