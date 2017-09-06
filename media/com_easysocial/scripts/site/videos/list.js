EasySocial.module('site/videos/list', function($) {

var module = this;

EasySocial.Controller('Videos.List', {
	defaultOptions: {

		// Video filters
		"{filter}": "[data-videos-filter]",
		"{sorting}": "input[name='sorting']",
		"{sortItem}": "[data-sorting]",

		// content wrapper
		"{wrapper}": "[data-wrapper]",

		// Videos result
		"{result}": "[data-videos-result]",
		"{list}": "[data-result-list]",

		// Video actions
		"{item}": "[data-video-item]",
		"{deleteButton}": "[data-video-delete]",
		"{featureButton}": "[data-video-feature]",
		"{unfeatureButton}": "[data-video-unfeature]",
		"{createFilter}": "[data-video-create-filter]"
	}
}, function(self, opts, base) { return {

	clicked: false,

	init: function() {
		self.activeFilter = self.filter('[data-type=' + opts.active + ']');
	},

	// Default filter
	currentFilter: "",
	currentSorting: "",
	categoryId: null,
	isSort: false,

	setActiveFilter: function(filter) {

		self.activeFilter = filter;

		// Remove all active classes.
		self.filter().parent().removeClass('active');

		// Set the active class to the filter's parent.
		filter.parent().addClass('active is-loading');
	},

	getVideos: function() {

		if (!self.currentSorting) {
			// Set the current sorting
			self.currentSorting = self.sorting().val();
		}

		if (!self.currentFilter) {
			// Set the current sorting
			self.currentFilter = self.activeFilter.data('type');
		}

		// if still empty the filter, just set to all.
		if (!self.currentFilter) {
			self.currentFilter = "all";
		}

		var isSortReq = self.isSort ? "1" : "0";

		EasySocial.ajax('site/controllers/videos/getVideos',{
			"filter": self.currentFilter,
			"categoryId": self.categoryId,
			"sort": self.currentSorting,
			"uid": opts.uid,
			"type": opts.type,
			"hashtags": opts.hashtag,
			"hashtagFilterId": self.hashtagId,
			"isSort": isSortReq
		}).done(function(output) {

			self.activeFilter.parent().removeClass('is-loading');

			if (self.isSort) {
				self.result().removeClass('is-loading');
				self.list().html(output);
			} else {
				self.wrapper().removeClass('is-loading');
				self.result().html(output);
			}

			$('body').trigger('afterUpdatingContents', [output]);
		});
	},

	"{sortItem} click" : function(sortItem, event) {

		// Stop propagation
		// event.preventDefault();
		// event.stopPropagation();

		// Get the sort type
		var type = sortItem.data('type');
		self.currentSorting = type;

		self.isSort = true;

		// Route the item so that we can update the url
		sortItem.route();

		self.result().addClass('is-loading');
		self.list().empty();

		self.getVideos();
	},

	"{filter} click": function(filter, event) {
		// Prevent bubbling up
		event.preventDefault();
		event.stopPropagation();

		var type = filter.data('type');

		// Route the inner filter links
		filter.route();

		// Add an active state to the parent
		self.setActiveFilter(filter);

		// Filter by category
		var categoryId = null;

		if (type == 'category') {
			type = 'all';
			categoryId = filter.data('id');
		}

		var hashtagId = null;

		if (type == 'hashtag') {
			hashtagId = filter.data('tagId');
		}

		// Set the current filter
		self.currentFilter = type;
		self.categoryId = categoryId;
		self.hashtagId = hashtagId;
		self.isSort = false;

		self.result().empty();
		self.wrapper().addClass('is-loading');

		self.getVideos();

        // trigger sidebar toggle for responsive view.
        self.trigger('onEasySocialFilterClick');

	},

	"{createFilter} click": function(filter, event) {

		if (self.clicked) {
			return;
		}

		// Prevent default
		event.preventDefault();
		event.stopPropagation();

		self.clicked = true;

		// Update the url
		filter.route();

		// Add an active state to the parent
		self.setActiveFilter(filter);

		EasySocial.ajax('site/views/videos/getFilterForm', {
			"type": filter.data('type'),
			"id": filter.data('id'),
			"cid": filter.data('uid'),
			"clusterType": filter.data('clusterType')
		}).done(function(outputs) {
			// Stop the loading
			self.element.removeClass('is-loading');

			self.result().html(outputs);
		}).fail(function(messageObj) {
			return messageObj;
		}).always(function() {
			self.clicked = false;
		});
	},

	"{deleteButton} click": function(deleteButton, event) {

		var item = deleteButton.parents(self.item.selector);
		var id = item.data('id');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/videos/confirmDelete', {
				"id": id
			})
		});
	},

	"{unfeatureButton} click": function(unfeatureButton, event) {
		var item = unfeatureButton.parents(self.item.selector);
		var id = item.data('id');
		var returnUrl = unfeatureButton.data('return');

		var options = {
			"id": id
		};

		if (returnUrl.length > 0) {
			options["callbackUrl"] = returnUrl;
		}

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/videos/confirmUnfeature', options)
		});
	},

	"{featureButton} click": function(featureButton, event) {
		var item = featureButton.parents(self.item.selector);
		var id = item.data('id');
		var returnUrl = featureButton.data('return');

		var options = {
			"id": id
		};

		if (returnUrl) {
			options["callbackUrl"] = returnUrl;
		}

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/videos/confirmFeature', options)
		});
	}
}});

module.resolve();


});
