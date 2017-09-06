EasySocial.module("site/api/share", function($){

$(document)
	.on("click.es.share.button", "[data-es-share-button]", function(){

		var button = $(this);
		var self = this;

		EasySocial.dialog({
			"content":
				EasySocial.ajax("site/views/sharing/dialog", {
					"url": button.data("url"),
					"title": button.data("title")
				}),
			"bindings": {
				init: function() {
		            EasySocial
		            .require()
		            .script('site/utilities/sharing').done(function($) {
		                self.controller = $("[data-sharing]").addController("EasySocial.Controller.Sharing");
		            });
				},

				"{shareButton} click": function() {

					var controller = self.controller.getPlugin('email');

					controller.send();
				}
			}
		});
	});

	this.resolve();
});
