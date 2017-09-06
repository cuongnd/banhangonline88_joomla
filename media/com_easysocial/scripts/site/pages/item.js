EasySocial.module('site/pages/item', function($) {

var module = this;

EasySocial.require()
.library('history')
.script('site/avatar/avatar', 'site/cover/cover')
.done(function($) {

EasySocial.Controller('Pages.Item', {
	defaultOptions: {

		// Content area
		"{contents}": "[data-contents]",
		"{wrapper}": "[data-wrapper]",

		// Filter item
		"{filterItem}": "[data-filter-item]",
		"{createFilter}": "[data-create-filter]",

		// Edit custom filters
        "{editFilter}": "[data-edit-filter]",

		// Page cover and avatar
		"{cover}": "[data-cover]",
		"{avatar}": "[data-avatar]",

		// About page
		"{infoTab}": "[data-info]",

		// Sections
		"{section}": "[data-section]",
		"{sectionLists}": "[data-section-lists]",
		"{showAllSection}": "[data-section-showall]",

		"{filterBtn}"	 : "[data-stream-filter-button]",
		"{filterEditBtn}": "[data-dashboardFeeds-Filter-edit]",

		// hashtag filter save
		"{saveHashTag}"		: "[data-hashtag-filter-save]"
	}
}, function(self, opts, base) { return {

	init : function(){
        opts.id = self.element.data('id');

        var activeFilter = self.filterItem('.active');
        opts.filter = activeFilter.data('filter-item');
        opts.filterId = activeFilter.data('id');

		// Implement the avatar and cover
		self.cover().implement(EasySocial.Controller.Cover, {
			"uid": opts.id,
			"type": "page"
		});

		self.avatar().implement(EasySocial.Controller.Avatar, {
			"uid": opts.id,
			"type": "page"
		});
	},

	removeActive: function() {
		self.filterItem().removeClass('active');
		self.createFilter().removeClass('active');
		self.infoTab().removeClass('active');
	},

    setActiveFilter: function(filterItem) {
        // Toggle active state
        self.filterItem().removeClass('active');
        self.createFilter().removeClass('active');
        filterItem.addClass('active');

        var activeFilter = filterItem;
        opts.filter = filterItem.data('filter-item');
        opts.filterId = filterItem.data('id');

        // Update the url
        var anchor = filterItem.find('a');
        anchor.attr('title', opts.title);
        anchor.route();

        // Set loading class
        filterItem.addClass('is-loading');
        self.contents().empty();
        self.wrapper().addClass('is-loading');
    },

    setContents: function(filterItem, contents) {
        // Remove loading indicators
        filterItem.removeClass('is-loading');
        self.wrapper().removeClass('is-loading');

        // Update the contents
        self.contents().html(contents);
    },

    getAppContents: function(filterItem, callback) {
        EasySocial.ajax('site/controllers/pages/getAppContents', {
            "appId": opts.filterId,
            "id": opts.id
        }).done(function(contents) {
            self.setContents(filterItem, contents);

            if ($.isFunction(callback)) {
                callback.call(this, contents);
            }
        }).always(function() {
            filterItem.removeClass('is-loading');
        });
    },

    getStream: function(filterItem, callback) {
		// Perform an ajax to get the page's stream data
		EasySocial.ajax('site/controllers/pages/getStream', {
			"pageId": opts.id,
			"filter": opts.filter,
			"id": opts.filterId
		}).done(function(contents) {

			self.setContents(filterItem, contents);
		});
    },

    getInfo: function(filterItem, callback) {
        EasySocial.ajax('site/controllers/pages/getInfo', {
            "step": opts.filterId,
            "id": opts.id
        }).done(function(contents) {
            self.setContents(filterItem, contents);

            if ($.isFunction(callback)) {
                callback.call(this, contents);
            }
        }).always(function() {
            filterItem.removeClass('is-loading');
        });
    },

	"{showAllSection} click": function(link, event) {

		var parent = link.closest(self.section.selector);

		// Display all filters under the respective section
		parent.find(self.sectionLists.selector).removeClass('t-hidden');

		link.remove();
	},

	"{createFilter} click": function(createFilter, event) {
		// Prevent event from bubbling up
		event.preventDefault();
		event.stopPropagation();

		// Update the browsers address bar
		var anchor = createFilter.find('> a');
		anchor.route();

		// Remove active classes
		self.removeActive();
		createFilter.addClass('active');

		// Set the loading state
		createFilter.addClass('is-loading');
        self.contents().empty();
        self.wrapper().addClass('is-loading');

		EasySocial.ajax('site/views/stream/getFilterForm', {
			"uid": opts.id,
			"type": "page"
		}).done(function(contents) {
			self.setContents(createFilter, contents);
		});
	},

	"{editFilter} click": function(editFilter, event) {

        event.preventDefault();
        event.stopPropagation();

        // Updated the browser's url
        editFilter.route();

        // Get the filter attributes
        var id = editFilter.data('id');
        var type = editFilter.data('type');

        editFilter.addClass('is-loading');
        self.contents().empty();
        self.wrapper().addClass('is-loading');

        EasySocial.ajax('site/views/stream/getFilterForm', {
            "id": id,
            "uid": opts.id,
            "type": type
        }).done(function(contents) {
            self.setContents(editFilter, contents);
        }).fail(function(messageObj) {
            return messageObj;
        });
    },

	"{filterItem} click": function(filterItem, event) {
		// Prevent event from bubbling up
		event.preventDefault();
		event.stopPropagation();

		// Set the active filter now
        self.setActiveFilter(filterItem);

        var stream = true;

        // Trigger so that other scripts can perform other stuffs if needed
        $('body').trigger('beforeUpdatingContents');

        if (opts.filter == 'info') {
            self.getInfo(filterItem);
            stream = false;
        }

        if (opts.filter == 'apps') {
            self.getAppContents(filterItem);
            stream = false;
        }

        if (stream) {
            self.getStream(filterItem);
        }

        $('body').trigger('afterUpdatingContents');

        // trigger sidebar toggle for responsive view.
        self.trigger('onEasySocialFilterClick');

	},

    addCustomFilter: function(feed) {
        var sectionContent = self.section('[data-type="custom-filters"]').find(self.sectionLists.selector);

        if (!$.trim(sectionContent.html()).length) {
            var emptyList = self.section('[data-type="custom-filters"]').find('[data-filter-empty]');
            emptyList.hide();
        }

        feed.appendTo(sectionContent);
    },

    "{saveHashTag} click": function(el) {
        var hashtag = el.data('tag');
        var uid = el.data('uid');

        EasySocial.dialog({
            content: EasySocial.ajax('site/views/stream/confirmSaveFilter', {"tag" : hashtag}),
            bindings: {
                "{saveButton} click": function() {
                    this.inputWarning().hide();

                    filterName = this.inputTitle().val();

                    if (filterName == '') {
                        this.inputWarning().show();
                        return;
                    }

                    EasySocial.ajax('site/controllers/stream/addFilter', {
                        "title": filterName,
                        "tag": hashtag,
                        "uid": uid,
                        "type": 'page'
                    }).done(function(html, msg) {
                        var item = $.buildHTML(html);

                        // Add a new custom filter to the sidebar
                        self.addCustomFilter(item);

                        // Show message
                        Easysocial.dialog(msg);

                        // Close the dialog automatically
                        setTimeout(function() {
                            EasySocial.dialog().close();
                        }, 2000);
                    });
                }
            }
        });
    }
}});

	module.resolve();
});

});

