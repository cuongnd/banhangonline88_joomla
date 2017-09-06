EasySocial.module('site/apps/feeds/feeds', function($) {

var module = this;

EasySocial.Controller('Apps.Feeds', {
    defaultOptions: {
        "{browser}": "[data-feeds-browser]",
        "{sources}": "[data-feeds-lists]",
        "{item}": "[data-feed-item]",

        // Actions
        "{create}" : "[data-feeds-create]",
        "{remove}": "[data-feeds-remove]"
    }
}, function(self, opts, base) { return {

    init: function() {
        opts.id = base.data('uid');
        opts.appId = base.data('app');
    },

    "{create} click": function() {
        EasySocial.dialog({
            "content": EasySocial.ajax('site/views/feeds/create', {"id" : opts.id}),
            caller: self
        });
    },


    "{remove} click": function(link, event) {
        var item = link.parents(self.item.selector);
        var id = item.data('id');

        EasySocial.dialog({
            "content": EasySocial.ajax('site/views/feeds/confirmDelete', {"uid": opts.id, "id": id}),
            caller: self
        });
    }
}});

module.resolve();
});

