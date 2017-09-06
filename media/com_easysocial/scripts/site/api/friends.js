EasySocial.module('site/api/friends', function($){

var module = this;


	// Data API
	$(document)
		.on('click.es.friends.addLegacy', '[data-es-friends-add]', function(){

			var element = $(this);
			var userId = element.data( 'es-friends-id');
			var popboxContent = $.Deferred();
			var popboxOptions = {
				"content": popboxContent,
				"id" : "es",
				"component": "",
				"type": "api-friends",
				"toggle": "click"
			};

			// Display the popbox
			element.popbox('destroy');

			// Generate a new popbox instance
			element.popbox(popboxOptions);

			// Display the popbox
			element.popbox('show');

			// Run an ajax call now to perform the add friend request.
			EasySocial.ajax( 'site/controllers/friends/request' , {
				"viewCallback": "popboxRequest",
				"id": userId
			}).done(function(content) {

				popboxContent.resolve(content);

			});
		});


// New api to add friends
$(document)
	.on('click.es.friends.add', '[data-es-friends] [data-task="add"]', function() {

		var button = $(this);
		var wrapper = button.closest('[data-es-friends]');
		var targetId = wrapper.data('id');
		
		button.addClass('is-loading');

		EasySocial.ajax('site/controllers/friends/request', {
			"id": targetId
		}).done(function(newButton) {

			button.trigger('es.friends.add', [newButton]);
			wrapper.replaceWith(newButton);
		});
	});

// New api to unfriend a person
$(document)
	.on('click.es.friends.unfriend', '[data-es-friends] [data-task="unfriend"]', function() {

		var anchor = $(this);
		var wrapper = anchor.closest('[data-es-friends]');
		var targetId = wrapper.data('id');
		var button = wrapper.find('[data-es-friends-button]');

		// Add loading indicator			
		button.addClass('is-loading');

		EasySocial.ajax('site/controllers/friends/unfriend', {
			"id": targetId
		}).done(function(newButton) {

			anchor.trigger('es.friends.unfriend', [newButton]);
			wrapper.replaceWith(newButton);
		});
	});

// New api to cancel friend requests
$(document)
	.on('click.es.friends.cancel', '[data-es-friends] [data-task="cancel"]', function() {

		var anchor = $(this);
		var wrapper = anchor.closest('[data-es-friends]');
		var targetId = wrapper.data('id');
		var button = wrapper.find('[data-es-friends-button]');

		// Add loading indicator
		button.addClass('is-loading');

		EasySocial.ajax('site/controllers/friends/cancelRequest', {
			"id": targetId
		}).done(function(newButton) {

			anchor.trigger('es.friends.cancel', [newButton]);
			wrapper.replaceWith(newButton);
		});
	});

// API to reject a friend request
$(document)
	.on('click.es.friends.reject', '[data-es-friends] [data-task="reject"]', function() {
		
		var anchor = $(this);
		var wrapper = anchor.closest('[data-es-friends]');
		var friendId = wrapper.data('id');

		EasySocial.ajax('site/controllers/friends/reject', {
			"id": friendId
		}).done(function(newButton) {
			
			anchor.trigger('es.friends.reject', [newButton]);
			wrapper.replaceWith(newButton);
		});

	});

// Api to approve a friend request
$(document)
	.on('click.es.friends.accept', '[data-es-friends] [data-task="accept"]', function() {
		
		var anchor = $(this);
		var wrapper = anchor.closest('[data-es-friends]');
		var friendId = wrapper.data('id');

		EasySocial.ajax('site/controllers/friends/approve', {
			"id": friendId
		}).done(function(newButton) {
			
			anchor.trigger('es.friends.accept', [newButton]);
			wrapper.replaceWith(newButton);
		});
	});

module.resolve();

});
