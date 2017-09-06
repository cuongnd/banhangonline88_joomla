EasySocial.module('site/comments/form', function($) {

var module = this;

EasySocial.Controller('Comments.Form', {
	defaultOptions: {
		'{editor}': '[data-comments-editor]',
		'{input}': '[data-comments-form-input]',
		'{submit}': '[data-comments-form-submit]',
		
		// Smileys
		"{smileyLink}": "[data-comment-smileys]",
		"{smileyItem}": "[data-comment-smiley-item]",

		// Attachments
		"{attachmentQueue}": "[data-comment-attachment-queue]",
		"{attachmentProgress}": "[data-comment-attachment-progress-bar]",
		"{attachmentBackground}": "[data-comment-attachment-background]",
		"{attachmentRemove}": "[data-comment-attachment-remove]",
		"{attachmentItem}": "[data-comment-attachment-item]",

		"{attachmentDelete}": "[data-comment-attachment-delete]",

		"{uploaderForm}": "[data-uploader-form]",
		"{itemTemplate}": "[data-comment-attachment-template]",

		attachmentIds:[]
	}
}, function(self, opts, base, parent) { return {

	init: function() {

		// Assign the parent
		parent = self.parent;

		// Get available hints for friend suggestions and hashtags
		opts.hints = {
				"friends": $('[data-hints-friends]'),
				"hashtags": $('[data-hints-hashtags]')
		};

		// Apply the mentions on the comment form
		self.setMentionsLayout();

		// Implement attachments on the comment form.
		if (parent.options.attachments) {
			self.implementAttachments();
		}

	},

	attachmentTemplate: null,

	getAttachmentTemplate: function() {

		if (!self.attachmentTemplate) {
			self.attachmentTemplate = self.itemTemplate().detach();
		}

		var tpl = $(self.attachmentTemplate).clone().html();

		return $(tpl);
	},
	
	implementAttachments: function() {

		// Implement uploader controller
		self.editor().implement(EasySocial.Controller.Uploader, {
			'temporaryUpload': true,
			'query': 'type=comments',
			'type': 'comments',
			extensionsAllowed: 'jpg,jpeg,png,gif'
		});

	},

	"{smileyItem} click": function(smileyItem, event) {
		var value = smileyItem.data('comment-smiley-value');

		// Get the input
		var isEditing = smileyItem.parents('[data-comment-editor]').length > 0 ? true : false;
		var input = self.input();

		if (isEditing) {
			input = smileyItem.parents('[data-comment-editor]').find('[data-comment-input]');
		}

		var currentValue = input.val();
		currentValue += " " + value;

		// Update the comment form with the smiley
		input.val(currentValue);
	},

	"{smileyLink} click": function(smileyLink, event) {

		if (smileyLink.hasClass('active')) {
			smileyLink.removeClass('active');
			return;
		}

		smileyLink.addClass('active');
	},

	"{attachmentDelete} click": function(deleteLink, event) {

		var attachmentId = deleteLink.data('id');
		
		EasySocial.dialog({
			content: EasySocial.ajax('site/views/comments/confirmDeleteCommentAttachment', {
							"id": attachmentId
						}),
			bindings: {
				"{deleteButton} click": function() {

					// Perform an ajax call to the server
					EasySocial.ajax('site/controllers/comments/deleteAttachment', {
						"id": attachmentId
					})
					.done(function() {
						// Remove the dom from the page
						var item = deleteLink.parents(self.attachmentItem.selector);
						item.remove();

						EasySocial.dialog().close();
					});
				}
			}
		});

	},

	"{attachmentRemove} click": function(removeLink, event) {
		var item = removeLink.parents(self.attachmentItem.selector);

		// Remove the item from the attachment ids
		opts.attachmentIds = $.without(opts.attachmentIds, item.data('id'));

		// Remove the item
		item.remove();

		if (self.attachmentItem().length < 1) {
			self.attachmentQueue().removeClass('has-attachments');
		}
	},

	// When a new item is added, we want to display
	"{uploaderForm} FilesAdded": function(el, event, uploader, files) {

		$.each(files, function(index, file) {
			// Get the attachment template
			var item = self.getAttachmentTemplate();

			// Set the queue to use has-attachments class
			self.attachmentQueue()
				.addClass('has-attachments');

			// Insert the item into the queue
			item.attr('id', file.id)
				.addClass('is-uploading')
				.prependTo(self.attachmentQueue());
		});
	},

	// When the file is uploaded, we need to remove the uploading state
	"{uploaderForm} FileUploaded": function(el, event, uploader, file, response) {

		var item = self.attachmentQueue().find('#' + file.id);

		// Add preview
		self.attachmentBackground.inside(item)
			.css('background-image', 'url(' + response.preview + ')');

		// Remove the is-uploading state on the upload item
		item.removeClass('is-uploading');

		// Push the id
		item.data('id', response.id);

		opts.attachmentIds.push(response.id);
	},

	// When item is being uploaded
	"{uploaderForm} UploadProgress" : function(el, event, uploader, file) {

		var item = $('#' + file.id);
		var progress = self.attachmentProgress.inside(item);

		progress.css('width', file.percent + '%');
	},

	'{input} keypress': function(el, event) {

		// Only allow control + shift or cmd + enter to submit comments
		if ((event.metaKey || event.ctrlKey) && event.keyCode == 13) {
			self.submitComment();
		}
	},

	'{submit} click': function(el, event) {
		if (el.enabled()) {
			self.submitComment();
		}
	},

	setMentionsLayout: function() {
		var loader = $.Deferred();
		var editor = self.editor();
		var mentions = editor.controller('mentions');

		if (mentions) {
			mentions.cloneLayout();
			return;
		}

		// Get the immediate parent
		var header = self.editor().parent();

		editor.mentions({
			triggers: {
			    
			    "@": {
					type: "entity",
					wrap: false,
					stop: "",
					allowSpace: true,
					finalize: true,
					query: {
						loadingHint: true,
						emptyHint: opts.hints.friends.find('[data-empty]').html(),
						searchHint: opts.hints.friends.find('[data-search]').html(),

						data: function(keyword) {

							var task = $.Deferred();

							EasySocial.ajax("site/controllers/friends/suggest" , { 
									search: keyword 
							}).done(function(items) {
								
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

									return item;
								});

								task.resolve(items);
							}).fail(task.reject);

							return task;
						},
						use: function(item) {
							return item.type + ":" + item.id;
						}
				    }
				},
				"#": {
				    "type": "hashtag",
				    "wrap": true,
				    "stop": " #",
				    "allowSpace": false,
					"query": {
						loadingHint: true,
						emptyHint: opts.hints.hashtags.find('[data-empty]').html(),
						searchHint: opts.hints.hashtags.find('[data-search]').html(),
						data: function(keyword) {

							var task = $.Deferred();

							EasySocial.ajax("site/controllers/hashtags/suggest", {search: keyword})
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
			plugin: {
				autocomplete: {
					id: "es",
					component: "",
					sticky: true,
					position: {
						my: 'left top',
						at: 'left bottom',
						of: header,
						collision: 'none'
					},
					size: {
						width: function() {
							return header.outerWidth();
						}
					}
				}
			}
		});
	},

	submitComment: function() {
		var comment = self.input().val();

		// If comment value is empty, then don't proceed
		if ($.trim(comment) == '') {
			return false;
		}

		// Add loading indicator below the textarea
		self.submit()
			.addClass('is-loading');

		// Disable comment form
		self.disableForm();

		// Execute save
		self.save()
			.done(function(comment) {
				// Rather than using commentItem ejs, let PHP return a full block of HTML codes
				// This is to unify 1 single theme file to use loading via static or ajax

				// Trigger parent's commentSaved event
				self.parent.trigger('newCommentSaved', [comment]);

				// Enable the submit button
				self.submit().enabled(true);

				var editor = self.editor();
				var mentions = editor.controller("mentions");

				// Reset the mentions upon saving.
				mentions && mentions.reset();

				// Update the stream exclude id if applicable
				if (self.parent.options.streamid != '') {
					self.updateStreamExcludeIds(self.parent.options.streamid);
				}

			});
	},

	save: function() {
		var mentions = self.editor().controller("mentions");

		var data = {
			url: self.parent.options.url,
			mentions: mentions ? mentions.toArray() : []
		};

		data.mentions = $.map(data.mentions, function(mention){

			if (mention.type==="hashtag" && $.isPlainObject(mention.value)) {
				mention.value = mention.value.title.slice(1);
			}
			return JSON.stringify(mention);
		});

		return EasySocial.ajax('site/controllers/comments/save', {
			uid: self.parent.options.uid,
			element: self.parent.options.element,
			group: self.parent.options.group,
			verb: self.parent.options.verb,
			streamid: self.parent.options.streamid,
			input: self.input().val(),
			attachmentIds: opts.attachmentIds,
			data: data,
			clusterid : self.parent.options.clusterid
		});
	},

	updateStreamExcludeIds: function(id) {
		// ids = self.element.data('excludeids' );
		ids = $('[data-streams-wrapper]').data( 'excludeids' );

		newIds = '';

		if (ids != '' && ids != undefined) {
			newIds = ids + ',' + id;
		} else {
			newIds = id;
		}

		$('[data-streams-wrapper]').data('excludeids', newIds);
	},

	disableForm: function() {
		// Disable input
		self.input().attr('disabled', true);

		// Disable submit button
		self.submit().disabled(true);
	},

	enableForm: function() {
		// Enable and reset input
		self.input().removeAttr('disabled');

		// Enable submit button
		self.submit().enabled(true);
	},

	'{parent} newCommentSaved': function() {
		self.submit()
			.removeClass('is-loading');

		// Enable comment form
		self.enableForm();

		// Reset the attachments
		opts.attachmentIds = [];

		// Get all the attachment items in the queue
		var attachmentItems = self.attachmentItem.inside(self.attachmentQueue.selector);
		attachmentItems.remove();
		
		self.attachmentQueue().removeClass('has-attachments');

		// Reset comment input
		self.input().val('');
	}
}});


module.resolve();

});