EasySocial.module('apps/fields/user/joomla_password/registermini_content', function($) {
    var module = this;

    EasySocial.require()
    .language(
        'PLG_FIELDS_JOOMLA_PASSWORD_TOO_SHORT',
        'PLG_FIELDS_JOOMLA_PASSWORD_TOO_LONG',
        'PLG_FIELDS_JOOMLA_PASSWORD_EMPTY_PASSWORD')
    .done(function() {
        EasySocial.Controller('Field.Joomla_password.Mini', {
            defaultOptions: {
                required: false,
                min: 4,
                max: 0,

                '{input}': '[data-password]'
            }
        }, function(self) {
            return {
                init: function() {

                },

                '{input} keyup': function() {
                    self.checkPassword();
                },

                checkPassword: function() {
                    self.clearError();

                    var value = self.input().val();

                    if(self.options.min > 0 && value.length < self.options.min) {
                        self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_TOO_SHORT'));
                        return false;
                    }

                    if(self.options.max > 0 && value.length > self.options.max) {
                        self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_TOO_LONG'));
                        return false;
                    }

                    if(self.options.required && value.length == 0) {
                        self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_EMPTY_PASSWORD'));
                        return false;
                    }

                    return true;
                },

                '{self} onSubmit': function(el, event, register, mode) {
                    if (mode !== 'onRegisterMini') {
                        return;
                    }

                    register.push(self.checkPassword());
                },

                clearError: function() {
                    self.trigger('clear');
                },

                raiseError: function(msg) {
                    self.trigger('error', [msg]);
                }
            }
        });

        module.resolve();
    });
})
