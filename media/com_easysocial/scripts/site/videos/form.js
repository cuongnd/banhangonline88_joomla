EasySocial.module('site/videos/form', function($) {

var module = this;

EasySocial.require()
.script('site/friends/suggest')
.library('mentions')
.done(function($) {

EasySocial.Controller('Videos.Form', {
	defaultOptions: {
		"{videoSource}": "[data-video-source]",

		// Forms for video source
		"{forms}": "[data-form-source]",
		"{linkForm}": "[data-form-link]",
		"{uploadForm}": "[data-form-upload]",

		// Mentions
		"{mentions}": "[data-mentions]",
		'{hashtags}': '[data-hashtags]',
		"{header}": "[data-hashtags-header]"
	}
}, function(self, opts, base) { return {

	init: function() {
		self.initMentions();

		// Get available hints for friend suggestions and hashtags
		opts.hints = {
				"friends": self.element.find('[data-hints-friends]'),
				"hashtags": self.element.find('[data-hints-hashtags]')
		};

		// Apply the mentions on the comment form
		self.setMentionsLayout();
	},

	initMentions: function() {

		self.mentions()
			.addController("EasySocial.Controller.Friends.Suggest", {
				"showNonFriend": false,
				"includeSelf": true,
				"name": "tags[]",
				"exclusion": opts.tagsExclusion
			});
	},

	"{videoSource} change": function(videoSource, event) {

		var source = $(videoSource).val();
		var form = self[source + "Form"]();

		// Hide all source forms
		self.forms().addClass('t-hidden');

		// Remove hidden class for the active form
		form.removeClass('t-hidden');
	},

	setMentionsLayout: function() {
		var hashtags = self.hashtags();
		var mentions = hashtags.controller("mentions");

		if (mentions) {
			mentions.cloneLayout();
			return;
		}

		var header = self.header();

		hashtags.mentions({
			
			triggers: {
				"#": {
					"type": "hashtag",
					"wrap": true,
					"stop": " #",
					"allowSpace": false,
					"query": {
						"loadingHint": false,
						"searchHint": opts.hints.hashtags.find('[data-search]'),
						"emptyHint": opts.hints.hashtags.find('[data-empty]'),
						data: function(keyword) {

							var task = $.Deferred();

							EasySocial.ajax("site/controllers/hashtags/suggest", {search: keyword, type: "video"})
								.done(function(items) {
									
									if (!$.isArray(items)) {
										task.reject();
									}

									var items = $.map(items, function(item) {

										return {
											"title": "#" + $.trim(item),
											"type": "hashtag",
											"menuHtml": item
										};
									});

									task.resolve(items);
								})
								.fail(task.reject);

							return task;
						}
					}
				}
			},
			"plugin": {
					"autocomplete": {
						"id": "es",
						"component": "",
						"position": {
							my: 'left top',
							at: 'left bottom',
							of: header.parent(),
							collision: 'none'
						},
						"size": {
							width: function() {
								return header.parent().outerWidth();
							}
						}
					}
				}

		});
	}
}});

module.resolve();

});
});
