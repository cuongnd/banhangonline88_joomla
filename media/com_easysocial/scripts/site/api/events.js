EasySocial.module('site/api/events', function($){

var module = this;

// Events invite friends
$(document)
	.on('click.es.events.invite', '[data-es-events-invite]', function() {

		var element = $(this);
		var id = element.data('id');
		var returnUrl = element.data('return');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/events/invite', { "id" : id, "return" : returnUrl }),
			position: {
				my: "center top",
				at: "center top",
				of: window
			}
		});
	});

// Changing the action state for an event
$(document)
	.on('click.es.events.rsvp', '[data-es-events-rsvp] [data-state]', function() {

		var item = $(this);
		var task = item.data('state');

		// Get the other dom objects
		var wrapper = item.closest('[data-es-events-rsvp]');
		var eventId = wrapper.data('id');
		var button = wrapper.find('[data-button]');

		// Text for rsvp button
		var text = wrapper.find('[data-text]');

		// Add loading indicator
		button.addClass('is-loading');

		// Remove all states active class
		item.siblings().removeClass('active');
		item.addClass('active');

		// Perform a query to the server
		EasySocial.ajax('site/controllers/events/rsvp', {
			"task": task,
			"id": eventId
		}).done(function(newButton, newText) {

			// Remove loading
			button.removeClass('is-loading');

			// Update the current text of the button
			text.html(newText);

			// Trigger the button
			button.trigger('es.events.rsvp', [task, newButton, newText]);

			// Force page reload
			var reload = wrapper.data('page-reload');

			if (reload) {
				location.reload(true);
			}
		});
});

// Request join request
$(document)
	.on('click.es.events.request', '[data-es-events-request]', function() {

		var button = $(this);
		var eventId = button.data('id');

		button.addClass('is-loading');

		// Perform a query to the server
		EasySocial.ajax('site/controllers/events/rsvp', {
			"task": "request",
			"id": eventId
		}).done(function(newButton, newText) {

			// Remove loading
			button.replaceWith(newButton);

			// Trigger the button
			button.trigger('es.events.rsvp', ["request", newButton, newText]);
		});
	});

// Withdraw join request
$(document)
	.on('click.es.events.withdraw', '[data-es-events-withdraw]', function() {
		var button = $(this);
		var eventId = button.data('id');

		button.addClass('is-loading');

		EasySocial.ajax('site/controllers/events/rsvp', {
			"task": "withdraw",
			"id": eventId
		}).done(function(newButton, newText) {

			button.replaceWith(newButton);

			button.trigger('es.events.rsvp', ['withdraw'], newButton, newText);
		});
	});

// Featuring events
$(document)
	.on('click.es.events.admin.feature', '[data-es-events-feature]', function() {
		var element = $(this);
		var id = element.data('id');
		var returnUrl = element.data('return');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/events/confirmFeature', {"id" : id, "return": returnUrl})
		});
	});

// Unfeature events
$(document)
	.on('click.es.events.admin.feature', '[data-es-events-unfeature]', function() {
		var element = $(this);
		var id = element.data('id');
		var returnUrl = element.data('return');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/events/confirmUnfeature', {"id" : id, "return": returnUrl})
		});
	});


// Group admin tools - Unpublish group
$(document)
	.on('click.es.events.admin.unpublish', '[data-es-events-unpublish]', function() {
		var element = $(this);
		var id = element.data('id');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/events/confirmUnpublish', {"id" : id})
		});
	});

// Group admin tools - Delete group
$(document)
	.on('click.es.events.admin.delete', '[data-es-events-delete]', function() {
		var element = $(this);
		var id = element.data('id');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/events/confirmDelete', {"id" : id})
		});
	});

module.resolve();

});
