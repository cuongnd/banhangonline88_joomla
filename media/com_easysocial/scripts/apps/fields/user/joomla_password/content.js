EasySocial.module('apps/fields/user/joomla_password/content', function($) {
    var module = this;

    EasySocial.require()
        .library( 'passwordstrength' )
        .language(
            'PLG_FIELDS_JOOMLA_PASSWORD_EMPTY_PASSWORD',
            'PLG_FIELDS_JOOMLA_PASSWORD_EMPTY_RECONFIRM_PASSWORD',
            'PLG_FIELDS_JOOMLA_PASSWORD_NOT_MATCHING',
            'PLG_FIELDS_JOOMLA_PASSWORD_MINIMUM_CHAR',
            'PLG_FIELDS_JOOMLA_PASSWORD_MAXIMUM_CHAR',
            'PLG_FIELDS_JOOMLA_PASSWORD_STRENGTH_VERY_WEAK',
            'PLG_FIELDS_JOOMLA_PASSWORD_STRENGTH_WEAK',
            'PLG_FIELDS_JOOMLA_PASSWORD_STRENGTH_NORMAL',
            'PLG_FIELDS_JOOMLA_PASSWORD_STRENGTH_STRONG',
            'PLG_FIELDS_JOOMLA_PASSWORD_STRENGTH_VERY_STRONG',
            'PLG_FIELDS_JOOMLA_PASSWORD_EMPTY_ORIGINAL_PASSWORD'
        )
        .done(function(){

            EasySocial.Controller(
                'Field.Joomla_password',
                {
                    defaultOptions:
                    {
                        event               : null,

                        required            : false,
                        passwordStrength    : false,
                        reconfirmPassword   : false,
                        requireOriginal     : false,

                        min : 4,

                        max : 0,

                        '{field}'       : '[data-field-joomla_password]',

                        '{original}'    : '[data-field-password-orig]',
                        '{input}'       : '[data-field-password-input]',
                        '{reconfirm}'   : '[data-field-password-confirm]',

                        '{strength}'    : '[data-field-password-strength]',

                        '{reconfirmNotice}' : '[data-reconfirmPassword-failed]'
                    }
                },
                function( self )
                {
                    return {
                        init : function()
                        {
                            if(self.options.passwordStrength) {
                                self.initPasswordStrength();
                            }
                        },

                        '{input} keyup': function() {
                            self.validatePassword();
                        },

                        '{input} blur': function() {
                            self.validatePassword();
                        },

                        '{reconfirm} keyup': function() {
                            self.validatePassword();
                        },

                        '{reconfirm} blur': function() {
                            self.validatePassword();
                        },

                        validatePassword: function()
                        {
                            self.clearError();

                            var input = self.input().val(),
                                reconfirm = self.reconfirm().val();

                            if(self.options.event === 'onRegister' && !self.validatePasswordInput() ) {
                                return false;
                            }

                            if(self.options.event === 'onEdit' && !self.validatePasswordEdit()) {
                                return false;
                            }

                            if(self.options.reconfirmPassword && !self.validatePasswordConfirm()) {
                                return false;
                            }

                            return true;
                        },

                        validatePasswordInput: function() {
                            var input = self.input().val();

                            if($.isEmpty(input)) {
                                self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_EMPTY_PASSWORD'));
                                return false;
                            }

                            if(self.options.min > 0 && input.length < self.options.min) {
                                self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_MINIMUM_CHAR', self.options.min));
                                return false;
                            }

                            if(self.options.max > 0 && input.length > self.options.max) {
                                self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_MAXIMUM_CHAR', self.options.max));
                                return false;
                            }

                            return true;
                        },

                        validatePasswordEdit: function() {
                            var orig = self.original().val(),
                                input = self.input().val();

                            // If both original and input is empty, then we return true as it is not mandatory in edit
                            if($.isEmpty(input) && $.isEmpty(orig)) {
                                return true;
                            }

                            // Only original is empty
                            if($.isEmpty(orig) && self.options.requireOriginal) {
                                self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_EMPTY_ORIGINAL_PASSWORD'));
                                return false;
                            }

                            // Original is not empty, then we validate the new password
                            return self.validatePasswordInput();
                        },

                        validatePasswordConfirm: function() {
                            var input = self.input().val(),
                                reconfirm = self.reconfirm().val();

                            // Check if either input or reconfirm is not empty
                            if(!$.isEmpty(input) || !$.isEmpty(reconfirm)) {
                                if($.isEmpty(input)) {
                                    self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_EMPTY_PASSWORD'));
                                    return false;
                                }

                                if($.isEmpty(reconfirm)) {
                                    self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_EMPTY_RECONFIRM_PASSWORD'));
                                    return false;
                                }

                                if(input !== reconfirm) {
                                    self.raiseError($.language('PLG_FIELDS_JOOMLA_PASSWORD_NOT_MATCHING'));
                                    return false;
                                }
                            }

                            return true;
                        },

                        initPasswordStrength: function() {
                            self.input().password_strength({
                                container: self.strength.selector,
                                minLength: self.options.min,
                                texts: {
                                    1: $.language('PLG_FIELDS_JOOMLA_PASSWORD_STRENGTH_VERY_WEAK'),
                                    2: $.language('PLG_FIELDS_JOOMLA_PASSWORD_STRENGTH_WEAK'),
                                    3: $.language('PLG_FIELDS_JOOMLA_PASSWORD_STRENGTH_NORMAL'),
                                    4: $.language('PLG_FIELDS_JOOMLA_PASSWORD_STRENGTH_STRONG'),
                                    5: $.language('PLG_FIELDS_JOOMLA_PASSWORD_STRENGTH_VERY_STRONG')
                                },
                                onCheck: function(level) {
                                    if(level <= 1) {
                                        self.strength()
                                            .removeClass('text-warning')
                                            .removeClass('text-success')
                                            .addClass('text-error small help-inline');
                                    }

                                    if(level > 1 && level <= 3) {
                                        self.strength()
                                            .removeClass('text-error')
                                            .removeClass('text-success')
                                            .addClass('text-warning small help-inline');
                                    }

                                    if(level >= 4) {
                                        self.strength()
                                            .removeClass('text-error')
                                            .removeClass('text-warning')
                                            .addClass('text-success small help-inline');
                                    }
                                }
                            })
                        },

                        raiseError: function(msg) {
                            self.trigger('error', [msg]);
                        },

                        clearError: function() {
                            self.trigger('clear');
                        },

                        "{self} onSubmit": function(el, event, register, mode) {
                            if (mode === 'onRegisterMini') {
                                return;
                            }

                            register.push(self.validatePassword());
                        }
                    }
                });

            module.resolve();

        });
});
