EasySocial.module('site/apps/reviews/reviews', function($) {

var module = this;


EasySocial.Controller('Apps.Review.Item', {
    defaultOptions: {

        "{delete}": "[data-delete]"
    }
}, function(self, opts) { return {
    
    init: function() {
        opts.id = self.element.data('id');
        opts.uid = self.element.data('uid');
        opts.type = self.element.data('type');
    },

    "{delete} click" : function(el, event) {
        EasySocial.dialog({
            "content": EasySocial.ajax('site/views/reviews/confirmDelete', { "id" : opts.id})
        });
    }
}});

module.resolve();
});

