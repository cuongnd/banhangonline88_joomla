EasySocial.module('apps/fields/page/permalink/content', function($) {

var module = this;

EasySocial.Controller('Field.Page.Permalink', {
    defaultOptions: {
        required: false,

        max     : 0,

        id      : null,
        pageid : null,
        userid  : null,

        '{field}'           : '[data-field-permalink]',

        '{checkButton}'     : '[data-permalink-check]',
        '{input}'           : '[data-permalink-input]',
        '{available}'       : '[data-permalink-available]'
    }
}, function(self, opts, base) { return {
    state: false,

    init: function() {
        opts.max = self.field().data('max');
    },

    "{self} onRender": function() {
        var data = self.field().htmlData();

        opts.error = data.error || {};
    },

    "{checkButton} click" : function() {
        self.delayedCheck();
    },

    "{input} keyup" : function()
    {
        self.delayedCheck();
    },

    delayedCheck: $.debounce(function()
    {
        self.checkPermalink();
    }, 250),

    checkPermalink: function()
    {
        self.clearError();

        var permalink   = self.input().val();

        self.available().hide();

        if (self.options.max > 0 && permalink.length > self.options.max) {
            self.raiseError(opts.error.max);
            return false;
        }

        if (!$.isEmpty(permalink)) {
            self.checkButton().addClass('is-loading');

            var state = $.Deferred();

            EasySocial.ajax('fields/page/permalink/isValid', {
                "id"        : self.options.id,
                "pageid"   : self.options.pageid,
                "permalink" : permalink
            })
            .done(function(msg) {
                self.clearError();

                self.checkButton().removeClass('is-loading');

                self.available().show();

                state.resolve();
            })
            .fail(function(msg) {
                self.raiseError(msg);

                self.checkButton().removeClass('is-loading');

                self.available().hide();

                state.reject();
            });

            return state;
        }

        if (self.options.required && $.isEmpty(permalink)) {
            self.available().hide();

            self.raiseError(opts.error.required);
            return false;
        }

        return true;
    },

    raiseError: function(msg) {
        self.trigger('error', [msg]);
    },

    clearError: function() {
        self.trigger('clear');
    },

    '{self} onSubmit': function(el, ev, register) {
        register.push(self.checkPermalink());
    }
}});

module.resolve();
});
