EasySocial.module("site/api/repost", function($){

var module 	= this;

EasySocial.require()
.library('mentions')
.done(function() {
		
	$(document)
		.on("click.es.repost.action", "[data-repost-action]", function(){

			var button = $(this),
				data = {
					id     : button.data('id'),
					element: button.data('element'),
					group  : button.data('group'),
					clusterId  : button.data('clusterid'),
					clusterType  : button.data('clustertype'),
				},
				key = data.element + '-' + data.group + '-' + data.id;


			EasySocial.dialog({
				content	: EasySocial.ajax('site/views/repost/form', data),
				bindings: {
					init: function() {

						// There could be instances where this dialog doesn't contain a form.
						if (!this.textbox) {
							return;
						}

						// Get available hints for friend suggestions and hashtags
						this.hints = {
								"friends": this.form().find('[data-hints-friends]'),
								"hashtags": this.form().find('[data-hints-hashtags]')
						};

						this.setMentionsLayout();
					},

					setMentionsLayout: function() {
						var textbox = this.textbox();
						var mentions = textbox.controller("mentions");

						if (mentions) {
							mentions.cloneLayout();
							return;
						}

						var header = this.header();

						textbox
							.mentions({
								triggers: {
								    "@": {
										type: "entity",
										wrap: false,
										stop: "",
										allowSpace: true,
										finalize: true,
										query: {
											loadingHint: true,
											"searchHint": this.hints.friends.find('[data-search]').html(),
											"emptyHint": this.hints.friends.find('[data-empty]').html(),
											data: function(keyword) {

												var task = $.Deferred();

												EasySocial.ajax("site/controllers/friends/suggest", {search: keyword})
													.done(function(items){

														if (!$.isArray(items)) {
															task.reject();
														}

														var items = $.map(items, function(item){

															var html = $('<div/>').html(item);
															var title = html.find('[data-suggest-title]').val();
															var id = html.find('[data-suggest-id]').val();

															return {
																"id": id,
																"title": title,
																"type": "user",
																"menuHtml": item
															};
														});

														task.resolve(items);
													})
													.fail(task.reject);

												return task;
											},
											use: function(item) {
												return item.type + ":" + item.id;
											}
									    }
									},
									"#": {
									    type: "hashtag",
									    wrap: true,
									    stop: " #",
									    allowSpace: false,
										query: {
											loadingHint: true,
											emptyHint: this.hints.hashtags.find('[data-empty]').html(),
											searchHint: this.hints.hashtags.find('[data-search]').html(),
											data: function(keyword) {

												var task = $.Deferred();

												EasySocial.ajax("site/controllers/hashtags/suggest", {search: keyword})
													.done(function(items){

														if (!$.isArray(items)) task.reject();

														var items = $.map(items, function(item){
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
								plugin: {
									autocomplete: {
										id: "es",
										component: "",
										modifier: "es-story-mentions-autocomplete",
										sticky: true,
										shadow: true,
										position: {
											my: 'left top',
											at: 'left bottom',
											of: header,
											collision: 'none'
										},
										size: {
											width: function() {
												return header.outerWidth(true);
											}
										}
									}
								}
							});
					},
					
					"{sendButton} click": function(sendButton) {
						var self = this;
						var dialog = this.parent;
						var content = $.trim(this.repostContent().val());
						var postAs = sendButton.attr('data-post-as');
						
						// Add data content
						data.content = content;
						data.postAs = postAs;

						var mentions = this.textbox().mentions("controller").toArray();

						data.mentions = $.map(mentions, function(mention){
							if (mention.type==="hashtag" && $.isPlainObject(mention.value)) {
								mention.value = mention.value.title.slice(1);
							}
							return JSON.stringify(mention);
						});

						this.element.addClass('is-loading');

						EasySocial.ajax("site/controllers/repost/share", data)
							.done(function(content, isHidden, count, streamHTML) {
								var content = $.buildHTML(content);

								actionContent =
									$('[data-repost-' + key + ']')
										.toggleClass("hide", isHidden)
										.toggle(!isHidden);

								actionContent.find("span.repost-counter")
									.html(content);

								button.trigger("create", [streamHTML]);
							})
							.fail(function(message) {
								dialog.clearMessage();
								dialog.setMessage(message);
							})
							.always(function() {
								self.element.removeClass('is-loading');
								EasySocial.dialog().close();
							});
					}
				}
			});
		});

	EasySocial.module("repost/authors", function(){
		this.resolve(function(popbox) {

			var id = popbox.button.data('id');
			var element = popbox.button.data('element');

			var options = {
				"id": id,
				"element": element
			};

			return {
				content: EasySocial.ajax('site/controllers/repost/getRepostAuthors', options),
				id: "es",
				component: "",
				type: "repost",
				position: "bottom-right"
			}
		});
	});

	module.resolve();
});

});
