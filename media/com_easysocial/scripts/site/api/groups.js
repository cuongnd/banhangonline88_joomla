EasySocial.module('site/api/groups', function($){

var module = this;


// Groups API
$(document)
	.on('click.es.groups.join', '[data-es-groups-join]', function() {

		var button = $(this);
		var groupId = button.data('id');
		var join = "[data-group-join-count-"+groupId+"]";

		// Add loading indicator
		button.addClass('is-loading');

		EasySocial.ajax('site/controllers/groups/join', {
			"api": 1, 
			"id": groupId
		}).done(function(dialog, newButton, newJoinCount) {
				
				// Once the request is completed, we just replace the button
				if (newButton) {
					button.replaceWith(newButton);
					$(join).html(newJoinCount);
				} else {
					button.removeClass('is-loading');
				}

				if (dialog) {
					EasySocial.dialog({
						"content": dialog
					});
				}

				// Force page reload
				var reload = button.data('page-reload');

				if (reload) {
					location.reload(true);
				}
		})
		.fail(function(dialog) {
			EasySocial.dialog({
				"content": dialog
			});

			button.removeClass('is-loading');
		});
	});

// Groups API - Leave group
$(document)
	.on('click.es.groups.join', '[data-es-groups-leave]', function() {

		var button = $(this);
		var groupId = button.data('id');

		// Add loading indicator
		button.addClass('is-loading');

		EasySocial.dialog({
			"content": EasySocial.ajax('site/views/groups/confirmLeaveGroup', {
						"api": 1, 
						"id": groupId
			}).done(function(){
				button.removeClass('is-loading');
			}),
			"bindings": {
				"{leaveButton} click": function() {
					this.leaveForm().submit();
				}
			}
		});
	});

// Groups API - Withdraw request
$(document)
	.on('click.es.groups.withdraw', '[data-es-groups-withdraw]', function() {

		var link = $(this);
		var id = link.data('id');
		var parent = link.closest('[data-request-sent]');

		EasySocial.ajax('site/controllers/groups/withdraw', {
			"id": id
		}).done(function(newButton) {
			parent.replaceWith(newButton);
		});
	});

// Groups API - Respond to invitation request
$(document)
	.on('click.es.groups.respond.invitation', '[data-es-groups-respond-invitation]', function() {

		var button = $(this);
		var groupId = button.data('id');

		button.addClass('is-loading');

		EasySocial.dialog({
			"content": EasySocial.ajax('site/views/groups/confirmRespondInvitation', {
				"id": groupId
			}),
			"bindings": {
				"{rejectButton} click" : function() {
					this.responseValue().val('reject');
					this.respondForm().submit();
				},
				"{acceptButton} click" : function() {
					this.responseValue().val('accept');
					this.respondForm().submit();
				}
			}
		});
	});

// Group invite friends
$(document)
	.on('click.es.groups.invite', '[data-es-groups-invite]', function() {

		var element = $(this);
		var id = element.data('id');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/groups/invite', { "id" : id }),
			position: {
				my: "center top",
				at: "center top",
				of: window
			}
		});
	});

// Group admin tools - Feature group
$(document)
	.on('click.es.groups.admin.feature', '[data-es-groups-feature]', function() {
		var element = $(this);
		var id = element.data('id');
		var returnUrl = element.data('return');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/groups/confirmFeature', {"id" : id, "return": returnUrl})
		});
	});

// Group admin tools - Unfeature group
$(document)
	.on('click.es.groups.admin.feature', '[data-es-groups-unfeature]', function() {
		var element = $(this);
		var id = element.data('id');
		var returnUrl = element.data('return');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/groups/confirmUnfeature', {"id" : id, "return": returnUrl})
		});
	});

// Group admin tools - Unpublish group
$(document)
	.on('click.es.groups.admin.unpublish', '[data-es-groups-unpublish]', function() {
		var element = $(this);
		var id = element.data('id');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/groups/confirmUnpublishGroup', {"id" : id})
		});
	});

// Group admin tools - Delete group
$(document)
	.on('click.es.groups.admin.delete', '[data-es-groups-delete]', function() {
		var element = $(this);
		var id = element.data('id');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/groups/confirmDelete', {"id" : id})
		});
	});

module.resolve();

});
