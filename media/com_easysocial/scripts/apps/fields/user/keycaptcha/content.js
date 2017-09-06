EasySocial.module('apps/fields/user/keycaptcha/content', function($) {
var module = this;

EasySocial.Controller('Field.Keycaptcha', {
    defaultOptions: {
     required: true,
    '{input}': '[data-field-keycaptcha]',
   }
}, function(self, opts, base) { return {

    "{self} onRender": function() {
        var data = self.input().htmlData();

        opts.error = data.error || {};
    },

    validateInput: function() {
        self.clearError();
        var keycaptcha = self.input().val();

        if(keycaptcha.length > 100){
            return true;
        }

        self.raiseError();
        return false;
    },
    
    raiseError: function() {
        self.trigger('error', opts.error.required);
    },

    clearError: function() {
        self.trigger('clear');
    },
    
    '{self} onSubmit': function(el, event, register) {
        register.push(self.validateInput());
    }

}});

module.resolve();

});