EasySocial.module('site/polls/polls', function($){

var module = this;
var lang = EasySocial.options.momentLang;

EasySocial.require()
.library('datetimepicker', 'moment/' + lang)
.done(function($) {

EasySocial.Controller('Polls.Vote', {
    defaultOptions: {
        "multiple": false,

        // Editing
        "{edit}": "[data-edit]",

        // Item
        "{item}": "[data-option]",
        "{checkbox}": "[data-checkbox]",
        "{progress}": "[data-progress]",
        "{counter}": "[data-counter]",
        "{viewVoters}": "[data-view-voters]",
        "{voters}": "[data-voters]"
    }
}, function(self,opts,base) { return {

    init : function() {
        opts.id = base.data("id");
    },

    updateProgressBar: function() {
        var total = 0;

        // Get the total number of votes
        self.item().each(function(index, item) {
            total += $(item).data('count');    
        })

        // Now we need to update each other's progress bar
        self.item().each(function(index, item) {

            var item = $(item);
            var count = item.data('count');
            var id = item.data('id');
            var percentage = (count / total) * 100;

            // Update the progress bar
            item.find(self.progress.selector)
                .css('width', percentage + '%');

            // Update the label counter
            item.find(self.counter.selector)
                .text(count);

        });
    },

    "notify": function(action, itemId, isVoted) {

        if (isVoted || action == 'unvote') {
            return true;
        }

        EasySocial.ajax("site/controllers/polls/notify", {
            "id": opts.id,
            "itemId": itemId,
            "action": action
        });
    },

    updateVote: function(optionId, action, item, notify) {

        EasySocial.ajax("site/controllers/polls/vote", {
            "id": opts.id,
            "itemId": optionId,
            "act": action
        }).done(function(msg, items, isVoted, userid) {

            // Update the counter
            var count = item.data('count');

            voter = self.voters().find('[data-user-id="'+ userid +'"]')
           
            if (action == 'vote') {
                count += 1;

                // Update the voters
                EasySocial.ajax('site/views/polls/voters', {
                    "id": opts.id,
                    "optionId": optionId
                }).done(function(contents) {

                    if ($.trim(contents) == "") {
                        return;
                    }

                    item.find(self.voters.selector)
                        .html(contents)
                        .removeClass('t-hidden');
                });

            } else {
                count -= 1;

                // Remove the voter
                voter.remove();
            }

            item.data('count', count);

            // Update the progress bar
            self.updateProgressBar();

            var isVoted = isVoted;

            // Notify polls owner
            if (notify) {
                self.notify(action, optionId, isVoted);
            }
        });
    },

    getItem: function(element) {
        var item = element.closest(self.item.selector);
        return item;
    },

    "{checkbox} change": function(checkbox, event){
        
        var item = self.getItem(checkbox);
        var id = item.data('id');

        var checked = checkbox.is(':checked') ? true : false;
        var action = checked ? 'vote' : 'unvote';

        // Update the vote
        self.updateVote(id, action, item, true);

        // If it is checked, we need to uncheck the rest of the items if it is not a multiple choices
        if (checked && !opts.multiple) {

            self.item().each(function(i, item) {
                var item = $(item);
                var checkbox = item.find(self.checkbox.selector);

                if (item.data('id') != id && checkbox.is(':checked')) {
                    checkbox.prop('checked', false);
                    self.updateVote(item.data('id'), 'unvote', item, false);                    
                }
            });
        }

        return;
    },

    "{viewVoters} click": function(button, event) {
        var item = self.getItem(button);
        var id = item.data('id');

        EasySocial.ajax('site/views/polls/voters', {
            "id": opts.id,
            "optionId": id
        }).done(function(contents) {

            if ($.trim(contents) == "") {
                return;
            }

            item.find(self.voters.selector)
                .html(contents)
                .removeClass('t-hidden');
        });
    }

}});

EasySocial.Controller('Polls.Form', {
	defaultOptions: {

        // Actions
        "{add}": "[data-polls-add]",
        "{delete}": "[data-polls-item-delete]",

        // Options
        "{options}": "[data-polls-options]",
        "{option}": "[data-polls-option]",
        "{input}": "[data-polls-option-input]",

        // Template
        "{template}": "[data-polls-option-template]",

		// Elements
		"{itemList}": "[data-polls-list]",

        // inputs
        "{title}": "[data-polls-title]",
        "{multiple}": "[data-polls-multiple]",
        "{expiration}": "[data-polls-expiration]",

        // Hidden input
        "{removeItems}": "[data-remove]"
	}
}, function(self, opts, base) { return {

		init : function() {
            // When initialized, grab the template
            opts.template = self.template().clone();
            opts.id = base.data('id');
            opts.uid = base.data('uid');
            opts.element = base.data('element');
            opts.clusterId = base.data('cluster');

            self.expiration().addController('EasySocial.Controller.Polls.Datetime', {
                '{parent}': self
            });
		},

        insertItem: function() {
            var tmpl = opts.template.clone();

            //remove data attribute.
            tmpl.removeAttr("data-polls-option-template")
                .attr("data-polls-option", "")
                .show();

            // Traverse inside the input textbox and change the name.
            tmpl.find("input[name='copied']")
                .attr("name", "items[]")
                .attr("data-polls-option-input","");

            self.options().append(tmpl);

            return tmpl.find(self.input.selector);
        },

        "{input} keydown": function(input, event) {

            if (event.keyCode == 13) {
                event.stopPropagation();
                event.preventDefault();

                var newInput = self.insertItem();
                newInput.focus();
                return;
            }
        },

		"{delete} click": function(button, event) {
            var total = self.option().length;

			if (total <= 1) {
				return;
			}

			// Remove the item
            var item = button.closest(self.option.selector);
            var id = item.data('id');

            if (id) {
                var temp = self.removeItems().val();

                temp = temp == '' ? id : temp + ',' + id;

                self.removeItems().val(temp);
            }

            item.remove();
		},

		"{add} click": function(button, event) {
            var input = self.insertItem();
            input.focus();
		},

        toData: function(){

            var data = {
                "id": opts.id,
                "uid": opts.uid,
                "element": opts.element,
                "title": self.title().val(),
                "items": [],
                "multiple": self.multiple().is(':checked') ? 1: 0,
                "toberemove": self.removeItems().val(),
                "sourceid": opts.clusterId
            };

            // Get the date
            self.expiration().trigger('datetimeExport', [data]);

            if (self.input().length <= 0) {
                return data;
            }

            // Get each input
            self.input().each(function(i, item) {

                var item = $(item);
                var val = item.val();

                // Get the container
                var wrapper = item.closest(self.option.selector);
                var id = wrapper.data('id') ? wrapper.data('id') : 0;

                if ($.trim(val) != '') {
                    data.items.push({
                        "id": id,
                        "text": val
                    });
                }
            });

            return data;
        },

        validateForm: function() {
            var data = self.toData();

            if ($.isEmpty(data.title) || data.items.length == 0) {
                return false;
            }

            if (data.items.length <= 0) {
                return false;
            }

            return true;
        }

	}
});


EasySocial.Controller('Polls.Datetime', {
    defaultOptions: {
        '{picker}': '[data-picker]',
        '{toggle}': '[data-picker-toggle]',
        '{datetime}': '[data-datetime]'
    }
}, function(self) {
    return {
        init: function() {

            var minDate = new $.moment();
            var yearto = new Date().getFullYear() + 10;
            var dateFormat = 'DD-MM-YYYY';

            // Minus 1 on the date to allow today
            minDate.date(minDate.date() - 1);

            self.picker()._datetimepicker({
                component: "es",
                useCurrent: false,
                format: dateFormat,
                minDate: minDate,
                maxDate: new $.moment({y: yearto}),
                icons: {
                    time: 'fa fa-time',
                    date: 'fa fa-calendar',
                    up: 'fa fa-chevron-up',
                    down: 'fa fa-chevron-down'
                },
                sideBySide: false,
                pickTime: false,
                minuteStepping: 1,
                language: lang
            });

            var curActiveDateTime = self.element.data('value');
            if (curActiveDateTime != '') {
                var dateObj = $.moment(curActiveDateTime);
                self.datetimepicker('setDate', dateObj);
            }
        },

        datetimepicker: function(name, value) {
            return self.picker().data('DateTimePicker')[name](value);
        },

        '{toggle} click': function() {
            self.picker().focus();
        },

        '{picker} dp.change': function(el, ev) {
            self.setDateValue(ev.date.toDate());

            //self.parent.element.trigger('event' + $.String.capitalize(self.options.type), [ev.date]);
        },

        setDateValue: function(date) {
            // Convert the date object into sql format and set it into the input
            self.datetime().val(date.getFullYear() + '-' +
                                ('00' + (date.getMonth()+1)).slice(-2) + '-' +
                                ('00' + date.getDate()).slice(-2) + ' ' +
                                ('00' + date.getHours()).slice(-2) + ':' +
                                ('00' + date.getMinutes()).slice(-2) + ':' +
                                ('00' + date.getSeconds()).slice(-2));
        },

        '{self} datetimeExport': function(el, ev, data) {
            data['expirydate'] = self.datetime().val();
        }
    }
})

module.resolve();

});

});
