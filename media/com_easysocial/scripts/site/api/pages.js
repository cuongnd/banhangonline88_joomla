EasySocial.module('site/api/pages', function($){

var module = this;

// Page API
// Like Page
$(document)
	.on('click.es.pages.like', '[data-es-pages-like]', function() {

		var button = $(this);
		var pageId = button.data('id');

		var like = "[data-page-like-count-"+pageId+"]";

		//Add loading indicator
		button.addClass('is-loading');

		EasySocial.ajax('site/controllers/pages/like', {
			"api": 1, 
			"id": pageId
		}).done(function(dialog, newButton, newLikeCount) {
				
				// Once the request is completed, we just replace the button
				if (newButton) {
					button.replaceWith(newButton);
					$(like).html(newLikeCount);
				} else {
					button.removeClass('is-loading');
				}

				if (dialog) {
					EasySocial.dialog({
						"content": dialog
					});
				}
		});
	});

// Unlike page
$(document)
	.on('click.es.pages.unlike', '[data-es-pages-unlike]', function() {

		var button = $(this);
		var pageId = button.data('id');
		var returnUrl = button.data('return');

		EasySocial.dialog({
			"content": EasySocial.ajax('site/views/pages/confirmUnlike', {
						"api": 1, 
						"id": pageId,
						"return": returnUrl
			}),
			"bindings": {
				"{unlikeButton} click": function() {
					this.unlikeForm().submit();
				}
			}
		});
	});


// Withdraw request
$(document)
	.on('click.es.pages.withdraw', '[data-es-pages-withdraw]', function() {

		var link = $(this);
		var id = link.data('id');
		var parent = link.closest('[data-request-sent]');

		EasySocial.ajax('site/controllers/pages/withdraw', {
			"id": id
		}).done(function(newButton) {
			parent.replaceWith(newButton);
		});
	});

// Page invite friends
$(document)
	.on('click.es.pages.invite', '[data-es-pages-invite]', function() {

		var element = $(this);
		var id = element.data('id');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/pages/invite', { "id" : id }),
			position: {
				my: "center top",
				at: "center top",
				of: window
			}
		});
	});

// Page admin tools - Feature page
$(document)
	.on('click.es.pages.admin.feature', '[data-es-pages-feature]', function() {
		var element = $(this);
		var id = element.data('id');
		var returnUrl = element.data('return');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/pages/confirmFeature', {"id" : id, "return": returnUrl})
		});
	});

// Page admin tools - Unfeature page
$(document)
	.on('click.es.pages.admin.feature', '[data-es-pages-unfeature]', function() {
		var element = $(this);
		var id = element.data('id');
		var returnUrl = element.data('return');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/pages/confirmUnfeature', {"id" : id, "return": returnUrl})
		});
	});

// Page admin tools - Unpublish page
$(document)
	.on('click.es.pages.admin.unpublish', '[data-es-pages-unpublish]', function() {
		var element = $(this);
		var id = element.data('id');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/pages/confirmUnpublishPage', {"id" : id})
		});
	});

// Page admin tools - Delete page
$(document)
	.on('click.es.pages.admin.delete', '[data-es-pages-delete]', function() {
		var element = $(this);
		var id = element.data('id');

		EasySocial.dialog({
			content: EasySocial.ajax('site/views/pages/confirmDelete', {"id" : id})
		});
	});

module.resolve();

});
