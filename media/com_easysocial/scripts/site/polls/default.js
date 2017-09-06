EasySocial.module('site/polls/default', function($){

var module = this;

EasySocial.Controller('Polls', {
    defaultOptions: {
        "{filter}": "[data-filter]",
        "{contents}": "[data-contents]",
        "{wrapper}": "[data-wrapper]",
        "{result}": "[data-result]"
    }
}, function(self,opts,base) { return {

    init : function() {
    },

    setActiveFilter: function(element) {
        self.filter().removeClass('active');
        element.addClass('active is-loading');
    },

    updatingContents: function() {
        self.wrapper().empty();
        self.contents().addClass('is-loading');
    },

    updateContents: function(html) {
        self.contents().removeClass('is-loading');
        self.wrapper().replaceWith(html);
    },

    "{filter} click": function(filterItem, event) {

        //stop the anchor from trigger
        event.preventDefault();
        event.stopPropagation();

        // Route the anchor links embedded
        var anchor = filterItem.find('> .o-tabs__link');
        anchor.route();

        var type = filterItem.data('filter');

        self.setActiveFilter(filterItem);

        self.updatingContents();

        EasySocial.ajax('site/views/polls/filter', {
            "type": type
        }).done(function(html) {
            self.updateContents(html);
            filterItem.removeClass('is-loading');

            // trigger sidebar toggle for responsive view.
            self.trigger('onEasySocialFilterClick');

        });
    }
}});

module.resolve();

});
