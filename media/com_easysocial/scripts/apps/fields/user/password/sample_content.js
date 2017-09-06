EasySocial.module('apps/fields/user/password/sample_content', function($) {
    var module = this;

    EasySocial.Controller('Field.Password.Sample', {
        defaultOptions: {
            '{input}'           : '[data-input]',

            'min'                   : '',
            'max'                   : ''
        }
    }, function(self) {
        return {
            init: function() {

            },

            '{self} onConfigChange': function(el, event, name, value) {
                switch(name) {
                    case 'placeholder':
                        self.input().attr('placeholder', value);
                    break;

                    case 'default':
                        self.input().val(value);
                    break;

                    break;
                }
            }
        }
    });

    module.resolve();
});
