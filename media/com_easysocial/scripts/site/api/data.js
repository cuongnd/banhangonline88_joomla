EasySocial.module('site/api/data', function($){

var module = this;

EasySocial.require()
.script('site/friends/suggest', 'site/api/events', 'site/api/groups', 'site/api/pages', 'site/api/friends')
.done(function(){

	// Reports
	$(document).on("click.es.reports.link", "[data-reports-link]", function(){

		var button = $(this);
		var props = "url,extension,uid,type,object,title,description".split(",");
		var data = {};

		$.each(props, function(i, prop){
			data[prop] = button.data(prop);
		});

		EasySocial.dialog({

			content: EasySocial.ajax("site/views/reports/confirmReport", {
					title: data.title,
					description: data.description
			}),
			selectors: {
				"{message}": "[data-reports-message]",
				"{reportButton}": "[data-report-button]",
				"{cancelButton}": "[data-cancel-button]"
			},
			bindings: {

				"{reportButton} click": function() {

					var message	= this.message().val();

					EasySocial.dialog({
						content: EasySocial.ajax("site/controllers/reports/store", {
								url      : data.url,
								extension: data.extension,
								uid      : data.uid,
								type     : data.type,
								title    : data.object,
								message  : message
							})
					});
				},

				"{cancelButton} click": function() {
					EasySocial.dialog().close();
				}
			}
		});
	});


	// Data API
	$(document)
		.on('click.es.conversations.compose', '[data-es-conversations-compose]', function(){

			var element = $(this);
			var userId = element.data('id') || element.data('es-conversations-id');
			var listId = element.data('list-id') || element.data('es-conversations-listid');

			EasySocial.dialog({
				"content": EasySocial.ajax( 'site/views/conversations/composer' , { "id" : userId , "listId" : listId }),
				"bindings": {

					"{sendButton} click" : function() {
						var recipients = $('input[name=recipient\\[\\]]');
						var message = $('[data-composer-message]').val();
						var notice = $('[data-composer-notice]')
						var uids = new Array;
						var dialog = this.parent;

						if (!notice.hasClass('t-hidden')) {
							// remove the notice message.
							notice.addClass('t-hidden');
						}

						$(recipients).each(function(){
							uids.push($(this).val());
						});

						EasySocial.ajax( 'site/controllers/conversations/store', {
							"uid": uids,
							"message": message
						}).done(function(link) {

							if (userId) {

								EasySocial.dialog({
									"content": EasySocial.ajax('site/views/conversations/sent', {"id" : userId }),
									"bindings": {
										"{viewButton} click" : function() {
											document.location = link;
										}
									}
								});
							}

							if (listId) {
								EasySocial.dialog({
									"content": EasySocial.ajax('site/views/conversations/sentList', {"id" : listId}),
									"bindings": {
										"{viewButton} click" : function() {
											document.location = link;
										}
									}
								});
							}
						}).fail(function(err) {
							notice.text(err.message);
							notice.removeClass('t-hidden');
						});
					}
				}
			});
		});


	// Legacy api
	$(document)
		.on('click.es.friends.cancelLegacy', '[data-es-friends-cancel]', function() {
			var element = $(this);
			var friendId = element.data('es-friends-id');

			// Show confirmation dialog
			EasySocial.dialog({
				content: EasySocial.ajax('site/views/friends/confirmCancel'),
				bindings: {

					"{confirmButton} click": function() {
						EasySocial.ajax( 'site/controllers/friends/cancelRequest', {
							"id": friendId
						}).done(function() {
							EasySocial.dialog().close();
						});
					}
				}
			});
		});

	// Apply actions upon clicking the follow / unfollow button
	$(document)
		.on('click.es.user.subscription', '[data-es-subscription]', function() {

			var button = $(this);
			var task = $(this).data('task');
			var id = $(this).data('id');

			// Add loading indicator on the button
			button.addClass('is-loading');

			// Let's do an ajax call to follow the user.
			EasySocial.ajax( 'site/controllers/subscriptions/' + task, {
				"id": id,
				"type": "user"
			}).done(function(html) {
				button.replaceWith(html);
				button.removeClass('is-loading');
			});
		});


	// Block a target user
	$(document).on("click.es.blocks.link", "[data-blocks-link]", function(){

		var target = $(this).data('target');

		EasySocial.dialog({

			content: EasySocial.ajax(
				"site/views/blocks/confirmBlock",
				{
					"target": target
				}),

				selectors: {
					"{reason}": "[data-block-reason]",
					"{blockButton}": "[data-block-button]",
					"{cancelButton}": "[data-cancel-button]"
				},

				bindings: {

					"{blockButton} click": function() {

						var reason = this.reason().val();

						EasySocial.dialog({
							content: EasySocial.ajax(
								"site/controllers/blocks/store",
								{
									"target": target,
									"reason": reason
								})
							});
					},

					"{cancelButton} click": function() {
						EasySocial.dialog().close();
					}
				}
		});
	});

	// Unblock a target user
	$(document).on("click.es.unblock.link", "[data-unblock-link]", function(){

		var target = $(this).data('target');

		EasySocial.dialog({

			content: EasySocial.ajax(
				"site/views/blocks/confirmUnblock",
				{
					"target": target
				}),

				selectors: {
					"{unblockButton}": "[data-unblock-button]",
					"{cancelButton}": "[data-cancel-button]"
				},

				bindings: {

					"{unblockButton} click": function() {

						EasySocial.dialog({
							content: EasySocial.ajax(
								"site/controllers/blocks/unblock",
								{
									"target": target
								})
								.done(function() {

									// remove the user from the listing page.
									$('[data-blocked-user-' + target + ']').remove();

								}),

							selectors: {
								"{cancelButton}": "[data-cancel-button]"
							},

							bindings: {
								"{cancelButton} click": function() {
									EasySocial.dialog().close();
								}
							}
						});
					},

					"{cancelButton} click": function() {
						EasySocial.dialog().close();
					}
				}
		});
	});

	// Logout buttons
	$(document)
		.on('click', '[data-es-logout-button]', function() {
			var parent = $(this).closest('[data-es-logout]')
			var form = parent.find('[data-es-logout-form]');

			form.submit();
		});

	// Video embeds on stream
	$(document).on("click", "[data-es-links-embed-item]", function() {

        var button = $(this);
        var player = $('<div>').html(button.data('es-stream-embed-player'));
        var embed = '<div class="video-container">' + player.html() + '</div>';

        button.replaceWith(embed);
	});

    // Processes stream items containing videojs embed codes
    $(document).on('click', '[data-es-video-embed]', function() {
        var button = $(this);
        var embed = button.siblings('[data-es-video-embed-player]');

        button.replaceWith(embed);
        embed.removeClass('hide');
    });


    // Api for EasySocial.login
	EasySocial.login = function() {

		EasySocial.dialog({
			"content": EasySocial.ajax('site/views/login/form')
		});
	};

	module.resolve();
});

});
