EasySocial.module( 'site/conversations/message' , function($){

	var module 	= this;

	EasySocial.require()
	.done( function($){

		EasySocial.Controller(
			'Conversations.Message', {
				defaultOptions: {
					"{attachmentDelete}" : "[data-es-attachment-delete]",

                    "{pagination}": "[data-es-message-pagination]",
                    "{paginationWrapper}" : "[data-es-message-pagination-wrapper]"
				}
			},
			function(self, opts){ return {

                init: function() {},

                loadMore: function() {

                    //conversation id
                    var id = self.pagination().data('id');

                    // Get the pagination attributes
                    var limitstart = self.pagination().data('limitstart');

                    if (id == '') {
                        return;
                    }

                    if (limitstart < 0) {
                        return;
                    }

                    // Set the current loading state
                    self.loading = true;

                    // Add loading indicator
                    self.pagination().addClass('is-loading');

                    EasySocial.ajax('site/views/conversations/getConversation',{
                        "id" : id,
                        "limitstart" : limitstart,
                        "isloadmore" : 1
                    }).done(function(title, messages) {

                        // we need to remove the pagination becuase the new html already included the pagination div.
                        self.paginationWrapper().remove();

                        // prepend the messages into the list
                        self.parent.messageContent().prepend(messages);

                        // add support to kunena [tex] replacement.
                        try { MathJax && MathJax.Hub.Queue(["Typeset",MathJax.Hub]); } catch(err) {};

                    }).always(function(){
                        self.pagination().removeClass('is-loading');
                        self.loading = false;
                    });

                },

                "{pagination} click" : function(el, event) {
                    self.loadMore();
                },


                "{attachmentDelete} click" : function(el, event) {

                    var attachmentId = $(el).data('id');

                    EasySocial.dialog({
                        content : EasySocial.ajax( 'site/views/conversations/confirmDeleteAttachment', { "id" : attachmentId } ),
                        bindings :
                        {
                            "{deleteButton} click" : function() {
                                EasySocial.ajax( 'site/controllers/conversations/deleteAttachment', {
                                    id  : attachmentId
                                })
                                .done(function(message) {
                                    // Remove the attachment element.
                                    $(el).parents('[data-es-attachment]').remove();

                                    EasySocial.dialog({
                                        content : message
                                    });
                                })
                                .fail(function(message) {
                                    self.setMessage(message);
                                })
                            }
                        }
                    });

                }

            }});

		module.resolve();
	});

});

