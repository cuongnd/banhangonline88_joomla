EasySocial.module('shared/fields/base', function($) {

var module = this;

EasySocial.Controller('Field.Base', {
	defaultOptions: {
		regPrefix : 'easysocial/',
		modPrefix : 'field.',
		ctrlPrefix : 'EasySocial.Controller.Field.',
		
		// Field attributes
		"id": null,
		"name": null,
		"element": null,
		"required": false,

		// Default mode
		"mode": 'edit',

		// Container items
		'{field}': '[data-field]',
		'{content}': '[data-content]',
		'{notice}': '[data-check-notice]'
	}
}, function(self, opts, base) { return {
	
	init: function() {

		// Initialize properties of the field
		opts.name = base.data('name');
		opts.element = opts.element || base.data('field-item');
		opts.id = base.data('id');
		opts.required = base.data('required') ? true : false;

		// Start triggering the field apps so that they can start doing their own initialization
		self.triggerFields();

		// Initialize error messages if there are any
		self.initializeErrorMessages();
	},

	initializeErrorMessages: function() {
		var notice = self.notice();
		var text = notice.text().trim();

		if (text.length <= 0) {
			return;
		}

		self.showError(text);
	},

	showError: function(message) {

		var content = self.content();
		
		content
			.find('[data-field-error]')
			.html(message);
	},

	// Trigger the necessary mode here for field to do necessary init
	triggerFields: function() {
		
		var field = self.field();
		var trigger = 'onEdit';

		if (opts.mode == 'registermini') {
			trigger = 'onRegisterMini';
		}

		if (opts.mode == 'register') {
			trigger = 'onRegister';
		}

		if (opts.mode == 'edit') {
			trigger = 'onEdit';
		}

		if (opts.mode == 'adminedit') {
			trigger = 'onAdminEdit';
		}

		if (opts.mode == 'sample') {
			trigger = 'onSample';
		}

		if (opts.mode == 'display') {
			trigger = 'onDisplay';
		}

		// Trigger the field
		field.trigger('onRender');
		field.trigger(trigger);
	},

	// Some base triggers/functions
	'{field} error': function(element, event, state, message) {
		var state = state !== undefined ? state : true;

		if ($.isString(state)) {
			message = state;
			state = true;
		}

		if ($.isBoolean(state)) {
			self.field().toggleClass('has-error', state);
		}

		if (message !== undefined) {
			self.showError(message);
		}
	},

	'{field} clear': function(el, ev) {
		self.field().removeClass('has-error');
		self.field().removeClass('is-loading');

		self.content().popover('destroy');
	},

	'{self} show': function() {
		self.field().trigger('onShow');
	},

	'{field} loading': function(el, ev, msg) {
		self.field().addClass('is-loading');

		self.notice().html(msg);
	},

	'{field} loaded': function(el, ev) {
		self.field().removeClass('is-loading');
	}
}});

module.resolve();
});
