EasySocial.module('apps/fields/event/recurring/content', function($) {
    var module = this;

    EasySocial.require().library('datetimepicker').done(function() {

        EasySocial.Controller('Field.Event.Recurring', {
            defaultOptions: {
                '{type}': '[data-recurring-type]',

                '{endBlock}': '[data-recurring-end-block]',

                '{picker}': '[data-recurring-end-picker]',

                '{toggle}': '[data-recurring-end-toggle]',

                '{result}': '[data-recurring-end-result]'
            }
        }, function(self) {
            return {
                init: function() {
                    self.picker()._datetimepicker({
                        pickTime: false,
                        component: "es",
                        useCurrent: false
                    });

                    var value = self.result().val();

                    if (!$.isEmpty(value)) {
                        var dateObj = $.moment(value);

                        self.datetimepicker('setDate', dateObj);
                    }
                },

                '{toggle} click': function() {
                    self.picker().focus();
                },

                '{picker} dp.change': function(el, ev) {
                    self.setDateValue(ev.date.toDate());
                },

                '{type} change': function(el, ev) {
                    var value = el.val();

                    self.endBlock()[value === 'none' ? 'hide' : 'show']();
                },

                datetimepicker: function(name, value) {
                    return self.picker().data('DateTimePicker')[name](value);
                },

                setDateValue: function(date) {
                    // Convert the date object into sql format and set it into the input
                    self.result().val(date.getFullYear() + '-' +
                                        ('00' + (date.getMonth()+1)).slice(-2) + '-' +
                                        ('00' + date.getDate()).slice(-2) + ' ' +
                                        ('00' + date.getHours()).slice(-2) + ':' +
                                        ('00' + date.getMinutes()).slice(-2) + ':' +
                                        ('00' + date.getSeconds()).slice(-2));
                },

                '{self} onSubmit': function(el, ev, register) {
                    register.push(true);
                }
            }
        });

        module.resolve();
    });
});
