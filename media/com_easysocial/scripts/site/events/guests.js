EasySocial.module('site/events/guests', function($) {
    
    var module = this;

    EasySocial.Controller('Events.App.Guests', {
        defaultOptions: {

            // Wrapper
            "{wrapper}": "[data-wrapper]",
            "{result}": "[data-result]",

            // Item
            "{item}": "[data-item]",

            // Actions
            "{remove}": "[data-guest-remove]",
            "{approve}": "[data-guest-approve]",
            "{promote}": "[data-guest-promote]",
            "{demote}": "[data-guest-demote]",
            "{reject}": "[data-guest-reject]",

            // Filters
            "{filter}": "[data-filter]"
        }
    }, function(self, opts) { return {
        
        init : function() {
            // Get the id of the page
            opts.id = self.element.data('id');
            opts.returnUrl = self.element.data('return');
        },

        setActiveFilter: function(filter) {
            self.filter().removeClass('active');
            filter.addClass('active');
        },

        "{filter} click": function(filter, event) {
            var type = filter.data('type');

            self.setActiveFilter(filter);

            self.result().removeClass('is-empty');
            self.wrapper().addClass('is-loading');
            self.result().empty();

            EasySocial.ajax('apps/event/guests/controllers/events/filter', {
                "id": opts.id,
                "filter": type
            }).done(function(contents) {

                // Set the loading
                self.wrapper().removeClass('is-loading');

                // Update the result
                self.result().html(contents);

                // Show empty if necessary
                if (!self.item().length) {
                    self.result().addClass('is-empty');
                }

            });
        },

        "{remove} click" : function(link, event) {

            // Get the user id
            var userId = link.closest(self.item.selector).data('id');

            EasySocial.dialog({
                content: EasySocial.ajax('site/views/events/confirmRemoveGuest', {"id": userId})
            });
        },

        // Approve a follower
        "{approve} click" : function(link, event) {
            // Get the user id
            var userId = link.closest(self.item.selector).data('id');

            EasySocial.dialog({
                content: EasySocial.ajax('site/views/events/confirmApproveGuest', {
                    "userId": userId, 
                    "id": opts.id, 
                    "return": opts.returnUrl
                })
            });
        },

        "{promote} click": function(link, event) {
            // Get the user id
            var userId = link.closest(self.item.selector).data('id');

            EasySocial.dialog({
                content: EasySocial.ajax('site/views/events/confirmPromoteGuest', {
                    "uid" : userId,
                    "id" : opts.id
                })
            });
        },

        "{demote} click": function(link, event) {
            
            // Get the user id
            var userId = link.closest(self.item.selector).data('id');

            EasySocial.dialog({
                content: EasySocial.ajax('site/views/events/confirmDemoteGuest', {
                    "uid" : userId,
                    "id" : opts.id
                })
            });
        },

        "{reject} click" : function(link, event) {
            // Get the user id
            var userId = link.closest(self.item.selector).data('id');

            EasySocial.dialog({
                content: EasySocial.ajax('site/views/events/confirmRejectGuest', {
                    "userId": userId, 
                    "id": opts.id, 
                    "return": opts.returnUrl
                })
            });
        }
    }});

    module.resolve();
});

