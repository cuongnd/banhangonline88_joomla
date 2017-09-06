EasySocial.module('site/profile/default', function($){

var module = this;

EasySocial.Controller('Profile', {
	defaultOptions: {

		// The current user being viewed
		id : null,

		// Elements
		"{header}": "[data-profile-header]",

		// Sidebar menu item
		"{filterItem}": "[data-filter-item]",

		"{sidebarItem}": "[data-es-profile-sidebar-menu]",

		// App item
		"{feeds}": "[data-profile-feeds]",
		"{app}": "[data-profile-apps-item]",
		"{action}": "[data-profile-apps-menu]",

		// Contents
		"{contents}": "[data-profile-real-content]",

		// Sidebar
		"{sidebar}": "[data-sidebar]",
		"{sidebarToggle}": "[data-sidebar-toggle]",

		// About section
		"{infoTab}": "[data-info]"
	}
}, function(self, opts, base) { return {

	init : function() {

		// Get the user's id.
		opts.id = base.data('id');
	},

	setActiveFilter: function(element) {

		// Remove all active classes from the sidebar
		self.filterItem().removeClass('active');
		element.addClass('active');

		opts.filterId = element.data('id');

		var anchor = element.find('> a');
		anchor.route();
	},

	updateContents: function(content, filterItem) {
		self.element.removeClass('is-loading');
		self.contents().html(content);
	},

	updatingContents: function() {

		// Empty the contents wrapper
		self.contents().empty();

		// Apply is-loading on the wrapper
		self.element.addClass("is-loading");
	},

    getAppContents: function(filterItem, callback) {
        EasySocial.ajax('site/controllers/profile/getAppContents', {
            "appId": opts.filterId,
            "id": opts.id
        }).done(function(contents) {
            self.updateContents(contents, filterItem);

            if ($.isFunction(callback)) {
                callback.call(this, contents);
            }
        });
    },

    getInfo: function(filterItem, callback) {

        EasySocial.ajax('site/controllers/profile/getInfo', {
            "index": opts.filterId,
            "id": opts.id
        }).done(function(contents) {
            self.updateContents(contents, filterItem);

            // Trigger the fields
            self.contents().find('[data-field]').trigger('onShow');

            if ($.isFunction(callback)) {
                callback.call(this, contents);
            }
        });
    },

    getStream: function(filterItem, callback) {

		// Perform an ajax to get the group's stream data
		EasySocial.ajax('site/controllers/profile/getStream', {
			"id": opts.id
		}).done(function(contents) {
			self.updateContents(contents, filterItem);
		});
    },

	"{filterItem} click": function(filter, event) {
		event.preventDefault();
		event.stopPropagation();

		var data = filter.data();
		var type = data.filterItem;

		if (type == 'apps' && data.layout == 'canvas') {
			window.location = data.canvasUrl;
			return;
		}

		// Set active state
		self.setActiveFilter(filter);

		// Scroll to contents on mobile view
		if (self.sidebarToggle().is(':visible')) {
			$.scrollTo(self.contents());
		}

		$('body').trigger('beforeUpdatingContents');

		// Show loading indication
		self.updatingContents();

		if (type == 'info') {
			self.getInfo(filter);
		}

		if (type == 'apps') {
			self.getAppContents(filter);
		}

		if (type == 'feeds') {
			self.getStream();
		}

		$('body').trigger('afterUpdatingContents');

        // trigger sidebar toggle for responsive view.
        self.trigger('onEasySocialFilterClick');
	},

	"{sidebarToggle} sidebarToggle": function(sidebarToggle) {
		self.setLayout();
	}
}});

module.resolve();
});
