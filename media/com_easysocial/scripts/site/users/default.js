EasySocial.module('site/users/default', function($){

var module = this;

EasySocial.Controller('Users', {
	defaultOptions: {

		sort: '',

		// Sorting
		"{sort}": "[data-sort]",

		// Contents and result
		"{contents}": "[data-contents]",
		"{wrapper}": "[data-wrapper]",
		"{result}": "[data-es-users-result]",
		"{header}": "[data-header]",

		// Sidebar filters
		"{filterItem}": "[data-filter-item]"
	}
}, function(self, opts) { return {

	init : function() {
		opts.sort = self.sort('.active').data('type');
	},

	setActiveFilter: function(filter) {
		// Remove all filter item's active class
		self.filterItem().removeClass('active');

		// Add active state to itself
		filter.addClass('active');
	},

	updateContents: function(contents) {
		self.result().removeClass('is-loading');

		self.result().html(contents);
	},

	getActiveFilter: function() {
		return self.filterItem('.active');
	},

	filter: function(sortRequest) {

		// Determines the filter type
		var filter = self.getActiveFilter();
		var type = filter.data('type');
		var id = filter.data('id');

		// Add loading indicator
		self.contents().addClass('is-loading');

		if (sortRequest) {
			self.result().empty();
		} else {
			self.wrapper().empty();
		}

		EasySocial.ajax('site/controllers/users/filter',{
			"type": type,
			"id": id,
			"sorting": opts.sort,
			"pagination": 1,
			"sortRequest": sortRequest ? 1 : 0
		}).done(function(output) {

			self.contents().removeClass('is-loading');

			if (sortRequest) {
				self.result().html(output);
			} else {
				self.wrapper().html(output);
			}

			$('body').trigger('afterUpdatingContents');

		}).always(function() {
			// Remove loading state
			filter.removeClass('is-loading');
		});
	},

	"{filterItem} click": function(filter, event) {
		// Prevent default
		event.stopPropagation();
		event.preventDefault();

		// Set active filter
		self.setActiveFilter(filter);

		// Route the url
		var anchor = filter.find('> a');
		anchor.route();

		// Add loading state to the filter link
		filter.addClass('is-loading');


		if (filter.data('type') != 'users') {
			opts.sort = '';
		}

		self.filter(false);

        // trigger sidebar toggle for responsive view.
        self.trigger('onEasySocialFilterClick');
	},

	"{sort} click" : function(link, event) {

		// event.preventDefault();
		// event.stopPropagation();

		// Set active class
		self.sort().removeClass('active');
		link.addClass('active');

		// Route the link
		link.route();

		var sort = link.data('type');

		opts.sort = link.data('type');

		self.filter(true);
	}
}});

module.resolve();
});
