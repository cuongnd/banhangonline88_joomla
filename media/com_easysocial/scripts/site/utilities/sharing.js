EasySocial.module('site/utilities/sharing', function($) {

var module = this;

EasySocial.require()
.library('textboxlist')
.done(function() {

$.template('sharing/recipientContent', '[%= title %]<input type="hidden" name="items" value="[%= title %]" />');

EasySocial.Controller('Sharing', {
	defaultOptions: {
		'{vendors}': '[data-sharing-vendor]',
		'{emailForm}': '[data-sharing-email]'
	}
}, function(self, opts, base) { return {
	init: function() {
		self.initLinks();

		self.initEmail();
	},

	initLinks: function() {
		$.each(self.vendors(), function(i, vendor) {

			vendor = $(vendor);

			if(!vendor.data('loaded')) {

				// Extract the href
				var link = vendor.attr('href');

				// Assign it to a data
				vendor.data('href', link);

				// Assign a void to the href
				vendor.attr('href', 'javascript:void(0);');

				// Assign loaded state
				vendor.attr('loaded', true);
			}
		});
	},

	initEmail: function() {
		$.each(self.emailForm(), function(i, form) {

			form = $(form);

			if (!form.data('loaded')) {

				// Implement email form controller
				self.addPlugin('email');

				// Assign loaded state
				form.attr('loaded', true);
			}
		});
	},

	'{vendors} click': function(el, ev) {
		var optionString = el.data('options') || '';

		window.open(el.data('href'), '', optionString);
	}
}});

EasySocial.Controller('Sharing.Email', {
	defaultOptions: {
		token			: '',

		'{container}'	: '[data-sharing-email]',
		'{frames}'		: '[data-sharing-email-frame]',
		'{recipients}'	: '[data-sharing-email-recipients]',
		'{input}'		: '[data-sharing-email-input]',
		'{content}'		: '[data-sharing-email-content]',

		// Frames
		'{frames}'		: '[data-sharing-email-frame]',
		'{frameForm}'	: '[data-sharing-email-form]',
		'{frameSending}': '[data-sharing-email-sending]',
		'{frameDone}'	: '[data-sharing-email-done]',
		'{frameFail}'	: '[data-sharing-email-fail]',
		'{failMsg}'		: '[data-sharing-email-fail-msg]'
	}
}, function(self, opts, base) { return {
		init: function() {
			// Initiate textboxlist plugin
			self.recipients().textboxlist({
				"component": 'es',
				"view": { itemContent: 'sharing/recipientContent'}
			});

			opts.token = self.container().data('token');
			self.originalPosition = self.container().css('position');
		},

		getRecipients: function() {
			var items = self.recipients().controller('textboxlist').getAddedItems();

			var recipients = [];

			$.each(items, function(i, item) {
				recipients.push(item.title);
			});

			var input = self.input().val();

			if(recipients.length < 1 && !$.isEmpty(input)) {
				recipients.push(input);
			}

			return recipients;
		},

		getContent: function() {
			return self.content().val();
		},

		sending: false,
		send: function() {

			if (self.sending) {
				return;
			}

			self.sending = true;

			// Control frames
			self.frames().hide();
			self.frameSending().show();

			// Get the data
			var token = self.options.token;
			var recipients = self.getRecipients();
			var content = self.getContent();

			/// Make the ajax call
			self.submitForm(token, recipients, content)
				.done(function() {
					// Control frames
					self.frames().hide();
					self.frameDone().show();

					// Show the form after 1 second
					setTimeout(function() {
						// Clear recipients
						self.recipients().controller('textboxlist').clearItems();

						// Clear content
						self.content().val('');

						// Control frames
						self.frameDone().hide();
						self.frameForm().show();
					}, 1000);
				}).fail(function(msg) {

					// Control frames
					self.frames().hide();
					self.frameFail().show();
					self.frameForm().show();

					if (msg !== undefined) {
						self.failMsg().html(msg);
					}
				})
				.always(function() {
					self.sending = false;
				});
		},

		// Add email address in if comma is pressed
		'{input} keypress': function(el, ev) {
			if(ev.which === 44) {
				self.recipients().controller('textboxlist').addItem(el.val());
				el.val('');
				return false;
			}
		},

		submitForm: function(token, recipients, content) {
			return EasySocial.ajax('site/controllers/sharing/send', {
				token: token,
				recipients: recipients,
				content: content
			});
		}
	}
});

module.resolve();

});
});
