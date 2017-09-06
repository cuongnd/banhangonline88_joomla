EasySocial.module('apps/fields/user/keycaptcha/registermini_content', function($) {
    var module = this;

    EasySocial.require()
        EasySocial.Controller('Field.keycaptcha.Mini', {
            defaultOptions: {
                id: null,

                required: false,

                '{input}': '[data-field-keycaptcha]'
            }
        }, function(self) {
            return {
                init: function() {

                },
                validateInput: function() {

                   self.clearError();

                var state = $.Deferred();

                var keycaptcha = self.input().val();
                var a = keycaptcha.split('|');  

                    if(a[4] == 1){
                     state.resolve();
                      return true;
                    }
                     self.raiseError(msg);
                     state.reject();
                     return state;  
                },

                raiseError: function(msg) {
                    self.trigger('error', [msg]);
                },

                clearError: function() {
                    self.trigger('clear');
                },

                '{self} onSubmit': function(el, ev, register, mode) {
                    if (mode !== 'onRegisterMini') {
                        return;
                    }
                }
            }
        });

        module.resolve();
   
});
