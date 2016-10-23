/**
 * Emoticons user controller manager
 * 
 * @package JCHAT::EMOTICONS::administrator::components::com_jchat
 * @subpackage js
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
//'use strict';
(function($) {
	var Emoticons = function() {
		/**
		 * Message timeout handler
		 * 
		 * @access private
		 * @var Object
		 */
		var msgTimeout = null;
		
		/**
		 * Open first operation progress bar
		 * 
		 * @access private
		 * @return void 
		 */
		var showMessages = function(message, state) {
			var icon = state == 'success' ? 'save' : 'cancel';
			var messageSnippet = '<div id="jchat_alert_message" class="alert alert-' + state + '">' +
						            '<span class="icon-' + icon + '"></span><span class="alert-message"> ' + message + '</span>' +
						          '</div>';		
			
			clearTimeout(msgTimeout);
			$('#jchat_alert_message').remove();
			
			$('#alert_append').append(messageSnippet);
			$('#jchat_alert_message').fadeIn('fast');
			
			timerReady = $.Deferred();
			$.when(timerReady).done(function(response){
				$('#jchat_alert_message').fadeOut('fast', function(){
					$('#jchat_alert_message').remove();
				});
			});
			
			msgTimeout = setTimeout(function(){
				timerReady.resolve();
			}, 3000);
		};
		
		/**
		 * Register user events for interface controls
		 * 
		 * @access private
		 * @param Boolean initialize
		 * @return Void
		 */
		var addListeners = function(initialize) {
			// Register button task actions
			// Save emoticons
			$('#adminForm button[data-action=save_emoticon]').on('click.emoticons', function(jqEvent) {
				// Prevent button default
				jqEvent.preventDefault();
				
				// Retrive information to save
				var rowIdentifier = $(this).data('save');
				var mediaIdentifierField = $('input[data-mediaidentifier=' + rowIdentifier + '], #jform_media_identifier_' + rowIdentifier);
				var linkIdentifier = mediaIdentifierField.val();
				var keycodeIdentifier = $('input[data-keycode=' + rowIdentifier + ']').val();
				
				// Purify the keycode
				keycodeIdentifier = keycodeIdentifier.replace(/(<([^>]+)>)/ig, ''); // Strip tags completely
				keycodeIdentifier = keycodeIdentifier.replace(/[\x00-\x1F\x7F<>"\'\/%&]/gi, ''); // Apply username filter
				
				//Check if the linkurl is specified in a valid path format
				if(mediaIdentifierField.length) {
					if(!linkIdentifier.match(/^[A-Za-z0-9_\/-]+[A-Za-z0-9_\.-]*([\\\\\/][A-Za-z0-9_-]+[A-Za-z0-9_\.-]*)*$/gi)) {
						// Clear previous error states
						$('input[data-mediaidentifier]').removeClass('keycode_invalid');
						$('span.label-mediaidentifier').remove();
					
						$('input[data-mediaidentifier=' + rowIdentifier + ']')
							.prev('div.media-preview.add-on')
							.addClass('keycode_invalid')
							.before('<span title="' + COM_JCHAT_INVALID_LINKURL_DESC + '" class="label-mediaidentifier label label-important">' + COM_JCHAT_INVALID_LINKURL + '</span>');
						$('span.label-keycode').tooltip({trigger:'hover', placement:'top'});
						return false;
					}	
				}
				
				//Check if the keycode is at least 2 characters otherwise invalidate it
				if(keycodeIdentifier.length < 2) {
					// Clear previous error states
					$('input[data-keycode]').removeClass('keycode_invalid');
					$('span.label-keycode').remove();
					
					$('input[data-keycode=' + rowIdentifier + ']')
						.addClass('keycode_invalid')
						.after('<span title="' + COM_JCHAT_INVALID_KEYCODE_DESC + '" class="label-keycode label label-important">' + COM_JCHAT_INVALID_KEYCODE + '</span>');
					$('span.label-keycode').tooltip({trigger:'hover', placement:'top'});
					return false;
				}
				
				var publishedStatus = $('input[name=published' + (rowIdentifier-1) + ']:checked').prop('value');

				// Now build the object to send to server endpoint
				var dataObject = {
					id : rowIdentifier,
					keycode : keycodeIdentifier,
					published : publishedStatus
				};
				
				if(mediaIdentifierField.length) {
					dataObject.linkurl = linkIdentifier;
				}
				
				// Now save to server side
				var keyCodeField = $('input[data-keycode=' + rowIdentifier + ']');
				saveDataStatus('saveEmoticon', dataObject, keyCodeField);
				
				return false;
			});
			
			// Change emoticons record state
			$('#adminForm fieldset[data-action=state_emoticon] label').on('click.emoticons', function(jqEvent, noTrigger) {
				// Avoid triggering if not user click
				if(noTrigger) {
					return false;
				}
				
				// Retrive information to save
				var rowIdentifier = $(this).parents('fieldset').data('state');
				var publishedState = $(this).children('input').val();
				
				// Now build the object to send to server endpoint
				var dataObject = {
					id : rowIdentifier,
					published : parseInt(publishedState)
				};
				
				// Now save to server side
				saveDataStatus('stateEmoticon', dataObject);
				
				return false;
				
			});
			
			$('table.adminlist tbody input[data-mediaidentifier]').on('change.emoticons propertychange.emoticons', function(jqEvent) {
				if(typeof(jqEvent.isTrigger) !== 'undefined') {
					var parentRow = $(this).parents('tr');
					var currentRefreshedImage = $('input[data-mediaidentifier]', parentRow).val();
					$('img[data-mediapreview]', parentRow).attr('src', jchat_livesite + currentRefreshedImage);
					$('input[data-mediaidentifier]').removeClass('keycode_invalid');
					$('span.label-mediaidentifier').remove();
				}
			});
			
			$('table.adminlist tbody input[data-keycode], table.adminlist tbody input[data-mediaidentifier]').on('keyup.emoticons', function(jqEvent) {
				// Clear previous error states
				$('input[data-keycode], input[data-mediaidentifier]').removeClass('keycode_invalid');
				$('span.label-keycode, span.label-mediaidentifier').remove();
			});
			
			// Required for Joomla 3.5+
			$('table.adminlist tbody a.button-clear').on('click.emoticons', {context:this}, function(jqEvent) {
				var parentRow = $(this).parents('tr');
				var indentifier = $('td.link_loc a').data('linkidentifier');
				// Update notify button status callback
				setTimeout(function(context){
					context.refreshRowStatus(parentRow, indentifier);
				}, 1, jqEvent.data.context)
			});
			
			// Patch for Joomla 3.6+ media button event based on jQuery mediafield
			$('a.button-select').on('click', function(){
				setTimeout(function(){
					var iframe = $('iframe[name=field-media-modal]');
					iframe.on('load',  function(){
						var iframeHTMLObject = iframe.get(0);
						var innerDoc = iframeHTMLObject.contentDocument || iframeHTMLObject.contentWindow.document;
						var insertButton = $('button.btn-success', innerDoc);
						insertButton.removeAttr('onclick');
					});
				}, 0);
			});
		};
		
		/**
		 * Manage the data saving and the status change for each sitemap record
		 * in the model database table
		 * 
		 * @access private
		 * @param String action
		 * @return Void
		 */
		var saveDataStatus = function(action, dataObject, keyCodeField) {
			// Object to send to server
			var ajaxparams = {
				idtask : action,
				param: dataObject
			};

			// Unique param 'data'
			var uniqueParam = JSON.stringify(ajaxparams);
			
			// Request JSON2JSON
			var emoticonsPromise = $.Deferred(function(defer) {
				$.ajax({
					type : "POST",
					url: "../administrator/index.php?option=com_jchat&task=emoticons.storeEmoticon&format=json",
					dataType : 'json',
					context : this,
					data : {
						data : uniqueParam
					}
				}).done(function(data, textStatus, jqXHR) {
					if(!data.result) {
						// Error found
						defer.reject(data.exception_message, textStatus);
						return false;
					}
					
					// Check response all went well
					if(data.result) {
						var userMessage = COM_JCHAT_EMOTICON_SAVED;
						defer.resolve(userMessage, data);
					}
				}).fail(function(jqXHR, textStatus, errorThrown) {
					// Error found
					var genericStatus = textStatus[0].toUpperCase() + textStatus.slice(1);
					defer.reject('-' + genericStatus + '- ' + errorThrown);
				});
			}).promise();

			emoticonsPromise.then(function(message, dataResponse) {
				// Update process status, we started
				showMessages(message, 'success');
				
				// Refresh the stored keycode
				$(keyCodeField).val(dataResponse.stored_keycode);
			}, function(errorText, error) {
				// Do stuff and exit
				showMessages(errorText, 'error');
			});
		};
		
		/**
		 * Function dummy constructor
		 * 
		 * @access private
		 * @param String
		 *            contextSelector
		 * @method <<IIFE>>
		 * @return Void
		 */
		(function __construct() {
			// Fix for Joomla 3.5 modals
			$('td.emoticonimage a.button-select').on('click', function(jqEvent) {
				$('button[data-dismiss=modal]').removeAttr('disabled');
			})
			$('td.emoticonimage input.field-media-input.mediaimagefield').removeAttr('readonly');
			 
			// Add UI events
			addListeners.call(this, true);
		}).call(this);
	}

	// On DOM Ready
	$(function() {
		window.JChatEmoticons = new Emoticons();
	});
})(jQuery);