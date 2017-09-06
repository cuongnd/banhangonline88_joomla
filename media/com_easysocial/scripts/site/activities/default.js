EasySocial.module('site/activities/default', function($){

var module	= this;

EasySocial.Controller('Activities', {
	defaultOptions: {
        // Properties
        items : null,

        // Elements
        "{container}" : "[data-activities]",

        // Wrapper and content
        "{wrapper}": "[data-wrapper]",
        "{contents}": "[data-contents]",
        "{contentTitle}": "[data-activities-content-title]",

        "{sidebar}" : "[data-sidebar]",
        "{sidebarItem}" : "[data-sidebar-item]",

        // lists
        "{list}": "[data-activities-list]",

        // Single item
        "{item}": "[data-activity-item]",
        "{itemContent}" : "[data-activity-content]",
        "{toggle}" : "[data-toggle]",
        "{delete}" : "[data-delete]",

        // hidden apps
        "{appItem}": "[data-hidden-app-item]",
        "{appContent}" : "[data-hidden-app-content]",
        "{unhideApp}" : "[data-hidden-app-unhide]",

        // hidden actor
        "{actorItem}": "[data-hidden-actor-item]",
        "{actorContent}" : "[data-hidden-actor-content]",
        "{unhideActor}" : "[data-hidden-actor-unhide]",


        // pagination
        "{pagination}" : "[data-pagination]",

	}
}, function(self, opts){ return {

    clicked: false,

    "{sidebarItem} click": function(item, event) {

        // Prevent event from bubbling up
        event.preventDefault();
        event.stopPropagation();

        // Get the attributes of the item
        var type = item.data('type');
        var id = item.data('id');

        // Prevent clicking any items more than once
        if (self.clicked) {
            return;
        }

        self.clicked = true;

        // Route the anchor links embedded
        var anchor = item.find('> .o-tabs__link');

        anchor.route();

        // Notify the dashboard that it's starting to fetch the contents.
        self.updatingContents();

        // Set the active filter
        self.setActiveFilter(item);

        // Remove empty state
        self.wrapper().removeClass('is-empty');

        EasySocial.ajax( 'site/controllers/activities/getActivities', {
            "type": type,
        }).done(function(contents, count) {

            if (count == 0) {
                self.wrapper().addClass('is-empty');
            }

            // Update the contents of the dashboard area
            self.updateContents(contents);

        }).fail(function(message) {
            return message;
        }).always(function() {

            self.clicked = false;
            item.removeClass('is-loading');
        });
    },

    setActiveFilter: function(item) {

        // Set active state
        self.sidebarItem().removeClass('active');
        item.addClass('active');

        // Add loading indicator
        item.addClass('is-loading');
    },

    updatingContents: function() {
        // When this method is invoked, clear the contents and add a loading indication
        self.contents().empty();
        self.wrapper().addClass('is-loading');
    },

    updateContents: function(contents) {
        self.wrapper().removeClass("is-loading");

        // Hide the content first.
        $.buildHTML(contents)
            .appendTo(self.contents());
    },


    // for single activity item
    getItem: function(element) {
        var item = element.closest(self.item.selector);
        return item;
    },


    "{toggle} click" : function(button, event) {
        var item = self.getItem(button);

        var id = item.data('id');
        var curState = item.data('current-state');

        EasySocial.ajax('site/controllers/activities/toggle', {
            "id" : id,
            "curState" : curState
        }).done(function(lbl, isHidden) {
            item.data('current-state', isHidden);

            var content = button.closest(self.itemContent.selector);
            // we need to hide the item when:
            //  from normal item to hidden,
            //  from hidden item to show.
            //  both scenario will have to hide the content.
            content.addClass('isHidden');

        });
    },

    "{delete} click" : function(button, event) {

        var item = self.getItem(button);
        var id = item.data('id');

        EasySocial.dialog({
            content : EasySocial.ajax('site/views/activities/confirmDelete'),
            bindings :
            {
                "{deleteButton} click" : function() {
                    EasySocial.ajax('site/controllers/activities/delete', {
                        "id" : id,
                    })
                    .done(function(html) {
                        item.fadeOut();

                        // close dialog box.
                        EasySocial.dialog().close();
                    });
                }
            }
        });

    },

    "{pagination} click" : function() {
        self.loadMore();
    },

    loadMore: function() {

        var type = self.pagination().data('type');
        var startlimit = self.pagination().data('startlimit');

        if (startlimit == '') {
            self.pagination().remove();
            return;
        }

        self.loading = true;
        self.pagination().addClass('is-loading');

        EasySocial.ajax( 'site/controllers/activities/getActivities', {
            "limitstart" : startlimit,
            "loadmore" : '1',
            "type" : type
        }).done(function(contents, startlimit) {
            // update next start date
            self.pagination().data('startlimit', startlimit);

            var contents = $.buildHTML(contents);

            contents
                .appendTo(self.list());

            if (startlimit=="") {
                self.pagination().remove();
            }

        }).always(function(){
            self.pagination().removeClass('is-loading');
            self.loading = false;
        });
    },

    // for hidden apps
    "{unhideApp} click": function(button, event) {
        var item = button.closest(self.appItem.selector);

        EasySocial.ajax('site/controllers/activities/unhideapp', {
            "context" : item.data('context'),
            "id" : item.data('id')
        }).done(function(message) {
            var content = button.closest(self.appContent.selector);
            content.html(message);
        });
    },

    "{unhideActor} click": function(button, event) {
        var item = button.closest(self.actorItem.selector);

        EasySocial.ajax('site/controllers/activities/unhideactor', {
            "actor" : item.data('actor'),
            "id" : item.data('id')
        }).done(function(message) {
            var content = button.closest(self.actorContent.selector);
            content.html(message);
        });
    }


}});

module.resolve();
});
