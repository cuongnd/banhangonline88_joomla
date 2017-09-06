EasySocial.module('site/apps/news/news', function($) {

var module = this;

EasySocial.Controller('Apps.News.Item', {
    defaultOptions: {
        "{delete}": "[data-delete]",
        "{likes}": "[data-likes-action]",
        "{counter}": "[data-news-counter]",
        "{likeContent}": "[data-likes-content]",
    }
}, function(self, opts) { return {

    init : function() {
        opts.id = self.element.data('id');
    },

    //need to make the data-stream-counter visible
    "{likes} onLiked": function(el, event, data) {
        self.counter().removeClass('hide');
    },

    "{likes} onUnliked": function(el, event, data) {
        var hideCounter     = self.likeContent().hasClass( 'hide' );

        if (hideCounter) {
            self.counter().addClass( 'hide' );
        }
    },

    "{delete} click" : function(button, event) {
        EasySocial.dialog({
            "content": EasySocial.ajax('site/views/news/confirmDelete' , { "id" : opts.id})
        });
    }

}});

module.resolve();

});

