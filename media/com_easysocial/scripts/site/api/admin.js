EasySocial.module('site/api/admin', function($){

var module = this;

// Admin tools - Unban user
$(document)
	.on('click.es.user.unban', '[data-es-user-unban]', function() {
		var element = $(this);
		var uid = element.data('id');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/profile/confirmUnban', {id: uid}),
			bindings: {
				"{unbanButton} click": function() {

					EasySocial.ajax('site/controllers/profile/unbanUser', {
						"id": uid
					}).done(function(html) {
						EasySocial.dialog({
							content: html
						});
					});

				},

				"{closeButton} click": function() {
					EasySocial.dialog().close();
				}
			}
		});

	});


// Admin tools - Delete user
$(document)
	.on('click.es.user.delete', '[data-es-user-delete]', function() {

		var element = $(this);
		var uid = element.data('id');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/profile/confirmDeleteUser', {id: uid}),
			bindings: {
				"{deleteButton} click": function() {
					EasySocial.ajax('site/controllers/profile/deleteUser', {
						"id": uid
					}).done(function(html) {
						EasySocial.dialog({
							content: html
						});
					});
				},

				"{closeButton} click": function() {
					EasySocial.dialog().close();
				}
			}
		});
	});

// Admin tools - Ban user
$(document)
	.on('click.es.user.ban', '[data-es-user-ban]', function() {
		var element = $(this);
		var uid = element.data('id');

		EasySocial.dialog({
			content: EasySocial.ajax( 'site/views/profile/confirmBanUser', {id: uid}),
			bindings: {

				"{banButton} click": function() {
					var period = $('[data-ban-period]').val();

					EasySocial.ajax('site/controllers/profile/banUser', {
						"id"	: uid,
						"period": period
					}).done(function(html) {
						EasySocial.dialog({
							content: html
						});
					});
				},

				"{closeButton} click": function() {
					EasySocial.dialog().close();
				}
			}
		});
	});


module.resolve();

});
