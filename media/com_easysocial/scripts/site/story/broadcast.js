EasySocial.module("site/story/broadcast", function($){

var module = this;
var lang = EasySocial.options.momentLang;

EasySocial.require()
.library('datetimepicker', 'moment/' + lang)
.done(function($) {

EasySocial.Controller("Story.Broadcast", {
	defaultOptions: {
		'{base}': '[data-story-event-base]',
		"{profile}" : "[data-broadcast-profile]",
		"{title}" : "[data-broadcast-title]",
		"{link}" : "[data-broadcast-link]",
		"{message}": "[data-broadcast-message]",
		"{type}": "[data-broadcast-type]",

        // Broadcast context
        "{context}": "[data-broadcast-context]",

        // send to all or selected
        "{sendType}": "[data-broadcast-send-type]",

        // Multilist form
        "{multilist}": "[data-broadcast-multilist]",

        // multilist value
        "{sendList}": "[data-broadcast-send-list]",

		"{broadcastExpiry}": "[data-broadcast-expirydate]"
	}
}, function(self, opts) { return {

	init: function() {
		self.broadcastExpiry().addController('EasySocial.Controller.Broadcast.Datetime', {
		    '{parent}': self
		});

        if (self.sendType().val() == 'all') {
            self.multilist().hide();
        } else {
            self.multilist().show();
        }

        self.loadSelection(self.context().val());
	},

    "{sendType} change": function(sendType, event){
        if (sendType.val() == 'all') {
            self.multilist().hide();
        } else {
            self.multilist().show();
        }
    },

    "{context} change": function(context, event){
        self.loadSelection(context.val());
    },

	"{story} save": function(element, event, save) {
		
        if (save.currentPanel != 'broadcast') {
			return;
		}

		self.savePost = save.addTask('savePost');

		self.save(save);

	},

    loadSelection: function(context) {
        EasySocial.ajax('apps/user/broadcast/controllers/broadcast/getSelectionItems', {
            "type": context,
        }).done(function(html) {
            self.sendList().html(html);
        });
    },

	save: function(save) {

        var savePost = self.savePost;

        if (!savePost) {
            return;
        }

        var profileId = new Array();

        if (self.sendType().val() != 'all') {
            profileId = self.sendList().val();
        }

        // Determines which profile we should broadcast to
		var	title = self.title().val(),
			link = self.link().val(),
			content = self.message().val(),
			type = self.type().val(),
            context = self.context().val();

		// Check if user doesn't fill in title or accidently add a space
        if ($.isEmpty($.trim(title))) {
            self.clearMessage();
            save.reject(opts.error);
            return false;
        }

        if ($.isEmpty($.trim(content))) {
            self.clearMessage();
            save.reject(opts.error);
            return false;
        }

        if (self.sendType().val() != 'all' && profileId == '') {
            self.clearMessage();
            save.reject(opts.error);
            return false;
        }

        var data = {"broadcast": "1", "profileId" : profileId, "title" : title, "content" : content, "link" : link, "type" : type, "context" : context};
        						self.broadcastExpiry().trigger('datetimeExport', [data]);

        save.addData(self, data);

        savePost.resolve();

        delete self.savePost;
    },

	"{story} beforeSubmit": function(element, event, save) {
        
        if (save.currentPanel != 'broadcast') {
            return;
        }

		save.data.content = self.message().val();
	}
}});

EasySocial.Controller('Broadcast.Datetime', {
	defaultOptions: {
	    '{picker}': '[data-picker]',
	    '{toggle}': '[data-picker-toggle]',
	    '{datetime}': '[data-datetime]'
	}
}, function(self) { return {
    init: function() {

        var minDate = new $.moment();
        var yearto = new Date().getFullYear() + 10;
        var datetimeFormat = self.picker().data('datetimeFormat');

        // 12 hour format
        var dateFormat = 'DD-MM-YYYY hh:mm A';

        // 24 hour format
        if (datetimeFormat == 24) {
            var dateFormat = 'DD-MM-YYYY HH:mm';            
        }

        // Minus 1 on the date to allow today
        minDate.date(minDate.date() - 1);

        self.picker()._datetimepicker({
            component: "es",
            useCurrent: false,
            format: dateFormat,
            minDate: minDate,
            maxDate: new $.moment({y: yearto}),
            icons: {
                time: 'fa fa-clock-o',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down'
            },
            sideBySide: false,
            pickTime: 1,
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
}});

// Resolve module
module.resolve();

});
});
