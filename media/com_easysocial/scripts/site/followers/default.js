EasySocial.module('site/followers/default', function($) {

	var module = this;

	EasySocial.Controller('Followers', {
		defaultOptions: {

			"{filterItem}": "[data-filter-item]",

			"{content}": "[data-followers-content]",

			"{items}": "[data-followers-item]",
			"{followingCounter}": "[data-following-count]",
			'{suggestionCounter}': "[data-suggest-count]"
		}
	}, function(self) { return {

		updateFollowingCounter: function(value) {
			var current = self.followingCounter().html(),
				updated = parseInt(current) + value;

			self.followingCounter().html(updated);
		},

		updateSuggestionCounter: function(value) {
			var current = self.suggestionCounter().html(),
				updated = parseInt(current) + value;

			self.suggestionCounter().html(updated);
		},

		updateContents: function(contents) {
			self.content().replaceWith(contents);

			$('body').trigger('afterUpdatingContents');
		},

		setActiveFilter: function(item) {

			// Remove all active class
			self.filterItem().removeClass('active');

			// Set the current item to be active
			item.addClass('active');
		},

		"{filterItem} click": function(filterItem, event) {
			event.preventDefault();
			event.stopPropagation();

			var anchor = filterItem.find('> a');
			anchor.route();

			var type = filterItem.data('type');
			var id = filterItem.data('id');

			// Set the active filter item
			self.setActiveFilter(filterItem);

			EasySocial.ajax("site/controllers/followers/filter", {
				"id": id,
				"type": type
			}).done(function(contents) {
				self.updateContents(contents);

        		// trigger sidebar toggle for responsive view.
        		self.trigger('onEasySocialFilterClick');

			});
		}
	}});

	module.resolve();
});
