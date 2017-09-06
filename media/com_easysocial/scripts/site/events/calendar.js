EasySocial.module('site/events/calendar', function($) {

var module = this;

EasySocial.Controller('Events.Browser.Calendar', {
	defaultOptions: {
		'{nav}': '[data-calendar-nav]',
		'{day}': '.day',
		'{month}': '[data-month]',
		isModule: false
	}
}, function(self, opts) { return {

	loading: function() {
		self.parent.calendarWrapper().addClass('is-loading');
		self.element.html('&nbsp;');
	},

	updateContents: function(html) {
		self.element.html(html).trigger('calendarLoaded');
	},

	'{self} calendarLoaded': function() {
		self.parent.calendarWrapper().removeClass('is-loading');

		self.day('.has-events').each(function(index, el) {
			el = $(el);

			var content = el.find('.es-event-details').html();

			el.popbox({
				content: content,
				id: 'es',
				component: '',
				type: 'events-calendar-filter',
				position: 'bottom-left',
				toggle: 'hover'
			});
		});

		var month = self.month('.has-events');

		if (month.length > 0) {
			var content = month.find('.es-event-details').html();
			month.popbox({
				content: content,
				id: 'es',
				component: '',
				type: 'events-calendar-filter',
				position: 'bottom-left',
				toggle: 'hover'
			});
		}
	},

	'{nav} click': function(nav, event) {
		var date = nav.data('calendar-nav');

		self.loading();

		EasySocial.ajax('site/views/events/renderCalendar', {
			"date": date
		}).done(function(html) {
			self.updateContents(html);
		});
	},

	'{day} click': function(day, event) {

		if (!opts.isModule) {
			event.preventDefault();
			event.stopPropagation();
		}

		var date = day.data('date');

		// Update the url in the address bar
		day.find('a[data-route]:first').route();

		self.loadEvents(date);
	},

	'{day} popboxActivate': function(day, event, popbox) {
		popbox.tooltip.find('a[data-route]').on('click', function(event) {

			if (!opts.isModule) {
				event.preventDefault();
				event.stopPropagation();
			}

			$(this).route();

			self.loadEvents($(this).data('date'));
		});
	},

	'{month} click': function(el, event) {
		if (!opts.isModule) {
			event.preventDefault();
		}

		// Update the url in the address bar
		el.find('a[data-route]:first').route();

		self.loadEvents(el.data('month'));
	},

	'{month} popboxActivate': function(el, ev, popbox) {
		popbox.tooltip.find('a[data-route]').on('click', function(event) {
			if (!opts.isModule) {
				event.preventDefault();
			}

			$(this).route();

			self.loadEvents($(this).data('month'));
		});
	},

	loadEvents: function(date) {

		var parent = self.parent;

		// Update the parent's filter type
		parent.options.filter = 'date';
		parent.options.date = date;

		// Show loading
		parent.updatingContents();

		parent.getEvents(false, function() {

		});
	}
}});

module.resolve();
});