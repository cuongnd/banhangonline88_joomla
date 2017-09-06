EasySocial.module('site/api/likes', function($){

var module = this;

$(document)
	.on("click.es.likes.action", "[data-es-likes]", function(event) {

		var link = $(this);
		var data = {
				"id": link.data("id"),
				"type": link.data("type"),
				"group": link.data("group"),
				"verb": link.data("verb"),
				"streamid": link.data("streamid"),
				"clusterid": link.data("clusterid")
		};
		var key = data.type + "-" + data.group + "-" + data.id;

		EasySocial.ajax("site/controllers/likes/toggle", data)
			.done(function(label, hideInfo, info, action, count) {

				// Update the text on the link
				link.text(label);

				//streamid
				id = link.data("streamid");

				var actions = link.closest('[data-stream-actions]');
				var infoItem = actions.find('[data-likes-' + key + ']');

				// Update like's content
				infoItem.find('[data-info]').html(info);

				if (count > 0) {
					infoItem.removeClass('t-hidden');
				} else {
					infoItem.addClass('t-hidden');
				}

				// Furnish data with like count
				data.uid = data.id;
				data.count = count;

				// verb = like/unlike
				link.trigger((action=="like") ? "onLiked" : "onUnliked", [data]);

				if (action == 'like' && id != "") {
					var exclusion = $('[data-es-streams]').data('excludeids');
					var newIds = '';
					
					if (exclusion != '' && exclusion != undefined) {
						newIds = exclusion + ',' + id;
					} else {
						newIds = id;
					}

					$('[data-es-streams]').data('excludeids', newIds);
				}
			});
	})
	.on("click.es.likes.others", "[data-likes-others]", function(){

		var button = $(this),
			content = button.parents("[data-likes-content]"),
			data = {
				uid    : content.data("id"),
				type   : content.data("type"),
				verb   : content.data('verb'),
				group  : content.data('group'),
				exclude: button.data("authors")
			};

		EasySocial.dialog({
			content: EasySocial.ajax("site/controllers/likes/showOthers", data)
		});
	});

	module.resolve();
});
