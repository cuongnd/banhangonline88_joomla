EasySocial.module('site/apps/discussions/discussions', function($) {

var module = this;

EasySocial.Controller('Apps.Discussions', {
    defaultOptions: {
        "{filter}": "[data-discussion-filter]",
        "{wrapper}": "[data-discussion-wrapper]",
        "{contents}": "[data-discussion-contents]"
    }
}, function(self, opts) { return {

    init: function() {
    },
    
    setActiveFilter: function(filter) {
        self.filter().removeClass('active');
        filter.addClass('active');
    },

    updatingContents: function() {
        self.contents().html('&nbsp;');

        self.wrapper().removeClass('is-empty').addClass('is-loading');
    },

    updateContents: function(html, empty) {
        self.wrapper().removeClass('is-loading');
        self.contents().html(html);

        if (empty) {
            self.wrapper().addClass('is-empty');
        } else {
            self.wrapper().removeClass('is-empty');
        }
    },

    "{filter} click": function(filter, event) {

        var type = filter.data('discussion-filter');

        self.setActiveFilter(filter);
        self.updatingContents();

        EasySocial.ajax('site/controllers/discussions/getDiscussions', {
            "id": opts.id,
            "filter": type
        }).done(function(contents, empty) {

            self.updateContents(contents, empty);
        });
    }
}});

EasySocial.Controller('Apps.Discussion.Item', {
    defaultOptions: {

        // Replies listing
        "{list}": "[data-reply-list]",
        "{replies}": "[data-reply-item]",
        "{repliesWrap}": "[data-replies-wrapper]",
        "{replyCounter}": "[data-reply-count]",

        // Main Discussion actions
        "{lock}": "[data-lock]",
        "{unlock}": "[data-unlock]",
        "{delete}": "[data-delete]",

        // Reply item actions
        "{item}": "[data-reply-item]",
        "{itemAcceptAnswer}": "[data-reply-accept-answer]",
        "{itemDelete}": "[data-reply-delete]",
        "{itemEdit}": "[data-reply-edit]",
        "{itemCancelEdit}": "[data-reply-edit-cancel]",
        "{itemUpdate}": "[data-reply-edit-update]",
        "{itemEditor}": "[data-reply-editor]",
        "{itemPreview}": "[data-reply-preview]",
        "{itemAlert}": "div.alert-error",

        // Reply form
        "{replyForm}": "[data-reply-form]",
        "{submitReply}" : "[data-reply-submit]",
        "{textarea}": "[data-reply-editor][data-reply-editor-new]",
    }
}, function(self, opts) { return {
    
    init: function() {
        opts.id = self.element.data('id');
        opts.uid = self.element.data('uid');
        opts.type = self.element.data('type');
    },

    insertReply: function(html) {
        // Since we know that we need to append the reply item, we need to remove is-unanswered
        self.element.removeClass('is-unanswered');

        // Since an item is added, we want to remove the empty class.
        self.repliesWrap().removeClass('is-empty');

        // Append the new item
        self.list().append(html);
    },

    updateReplyCounter: function(total) {
        
        if (total == 0) {
            self.repliesWrap().addClass( 'is-empty' );
        }
        
        self.replyCounter().html( total );
    },

    setResolved: function() {
        self.element.addClass('is-resolved');
    },
    
    "{textarea} keydown": function(textarea, event) {

        // Bind cmd + enter key to submit 
        // If pressing enter submits form
        // And enter key was pressed
        // Without any meta keys involved
        if ((event.metaKey && event.keyCode == 13)|| (opts.enterToSubmit && event.keyCode==13 && !(event.shiftKey || event.altKey || event.ctrlKey || event.metaKey))) {
            self.submitReply().click();
            event.preventDefault();
        }
    },

    "{lock} click" : function(link, event) {

        EasySocial.ajax('site/controllers/discussions/lock', {
            "id": opts.id
        }).done(function() {
            self.element.addClass('is-locked');    
        });
    },

    "{unlock} click" : function(el , event) {

        EasySocial.ajax('site/controllers/discussions/unlock', {
            "id": opts.id
        }).done(function() {
            self.element.removeClass('is-locked');
        });
    },

    "{delete} click" : function(el, event) {
        EasySocial.dialog({
            "content": EasySocial.ajax('site/views/discussions/confirmDelete', { "id" : opts.id})
        });
    },

    "{submitReply} click" : function(button, event) {
        var content = self.textarea().val();

        // If content is empty, throw some errors
        if (content == '') {
            self.replyForm().addClass('is-empty');
            return false;
        }

        EasySocial.ajax('site/controllers/discussions/reply', {
            "id": opts.id,
            "uid": opts.uid,
            "type": opts.type,
            "content": content
        }).done(function(html) {
            
            self.insertReply(html);

            // Reset the textarea
            self.textarea().val('');
        });

    },


    getReplyItem: function(element) {
        var item = element.closest(self.item.selector);

        return item;
    },

    "{itemAcceptAnswer} click" : function(link, event) {
        var item = self.getReplyItem(link);
        var id = item.data('id');

        EasySocial.ajax('site/controllers/discussions/accept', {
            "id": id
        }).done(function() {
            self.setResolved();

            item.siblings().removeClass('is-answer-item');
            item.addClass('is-answer-item');
        });
    },

    "{itemCancelEdit} click" : function(link, event) {
        var item = self.getReplyItem(link);
        var editForm = item.find(self.replyForm.selector);

        item.removeClass('is-editing');
        editForm.addClass('t-hidden');
    },

    "{itemEdit} click" : function(link, event) {
        var item = self.getReplyItem(link);
        var editForm = item.find(self.replyForm.selector);

        editForm.removeClass('t-hidden');

        item.addClass('is-editing');
    },


    "{itemUpdate} click" : function(button, event) {
        var item = self.getReplyItem(button);
        var id = item.data('id');
        var preview = item.find(self.itemPreview.selector);

        var editForm = button.closest(self.replyForm.selector);
        var content = editForm.find(self.itemEditor.selector).val();

        // If content is empty, throw some errors
        if (content == '') {
            editForm.addClass('is-empty');
            editForm.find(self.itemAlert.selector).show();
            return false;
        }

        EasySocial.ajax('site/controllers/discussions/update', {
            "id": id,
            "content": content
        }).done(function(content) {
            preview.html(content);

            editForm.addClass('t-hidden');
            editForm.removeClass('is-empty');
            editForm.find(self.itemAlert.selector).hide();
        });
    },

    "{itemDelete} click" : function(link, event) {
        var item = self.getReplyItem(link);
        var id = item.data('id');

        EasySocial.dialog({
            "content": EasySocial.ajax('site/views/discussions/confirmDeleteReply', {"id": id}),
            "bindings": {
                "{deleteButton} click" : function() {
                    EasySocial.ajax('site/controllers/discussions/deleteReply', {
                        "id": id
                    }).done(function(totalReplies) {
                        // Update the counter
                        self.updateReplyCounter(totalReplies);

                        // Hide the dialog
                        EasySocial.dialog().close();

                        // Remove the element
                        item.remove();
                    });
                }
            }
        });
    }
}});

module.resolve();
});

