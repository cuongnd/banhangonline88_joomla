EasySocial.module('site/groups/default', function($) {
	var module	= this;

	EasySocial.Controller('Groups.Browser', {
		defaultOptions: {

			// Sidebar filter
			"{filterItem}": "[data-filter-item]",

			// Result
			"{wrapper}": "[data-wrapper]",
			"{result}": "[data-result]",
			"{list}": "[data-list]",
			"{header}": "[data-header]",

			// Sorting
			"{sortItem}": "[data-sorting]",
			"{items}": "[data-groups-item]",
			"{featured}": "[data-groups-featured-item]",
			"{listContents}": "[data-es-groups-list]"
		}
	}, function(self, opts) { return {

		// Set active filter
		setActiveFilter: function(filter) {

			// Set correct active state
			self.filterItem().removeClass('active');
			filter.addClass('active');

			// Update the URL on the browser
			filter.find('a').route();

			// Set loading on the correct filter
			filter.addClass('is-loading');

			// Remove any header available
			self.header().remove();

			// Remove all result
			self.result().empty();

			// Set loading indicator on wrapper
			self.wrapper().addClass('is-loading');

		},

		// Set active sorting
		setActiveSort: function(sortItem) {

			// Set the correct active state
			self.sortItem().removeClass('active');
			sortItem.addClass('active');

			// Only remove the contents of the group listings
			self.list().empty();

			// Set the loading indicator of the result area
			self.result().addClass('is-loading')
		},

		"{filterItem} click": function(filterItem, event) {
			// Prevent default.
			event.preventDefault();
			event.stopPropagation();

			// Set active filter state
			self.setActiveFilter(filterItem);

			var type = filterItem.data('type');
			var options = {};

			if (type == 'category') {
				options.categoryId = filterItem.data('id');
			}

			self.wrapper().addClass('is-loading');

			EasySocial.ajax('site/controllers/groups/filter', $.extend({
				filter: type
			}, options)).done(function(contents) {

				// Remove loading indicators
				self.wrapper().removeClass('is-loading');
				filterItem.removeClass('is-loading');

				// Update the contents
				self.wrapper().html(contents);

				$('body').trigger('afterUpdatingContents', [contents]);

        		// trigger sidebar toggle for responsive view.
        		self.trigger('onEasySocialFilterClick');
			});
		},

		"{sortItem} click" : function(sortItem, event) {

			// Get the sort type
			var type = sortItem.data('type');
			var filter = sortItem.data('filter');
			var categoryId = sortItem.data('id');

			// Route the item so that we can update the url
			sortItem.route();

			// Add the active state on the current element.
			self.setActiveSort(sortItem);

			// Render the ajax to load the contents.
			EasySocial.ajax('site/controllers/groups/filter', {
				"ordering": type,
				"filter": filter,
				"categoryId": categoryId,
				"userId": opts.userId
			}).done(function(contents) {

				self.result().removeClass('is-loading');

				self.list().html(contents);
			});
		}

	}});

	module.resolve();
});

