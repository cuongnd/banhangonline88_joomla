EasyBlog.module('legacy', function($){

var module = this;

// eblog.js start
window.isSave = false;

var eblog = window.eblog = {

	stream: {
		load: function( startlimit ){
			ejax.load( 'dashboard' , 'loadStream' , startlimit );
		}
	},

	login:{
		toggle: function(){
			$( '#easyblog-search-form' ).hide().siblings().removeClass('active');
			$( '.user-options' ).hide().siblings().removeClass('active');
			$( '#easyblog-login-form' ).toggle();

            $( '#easyblog-login-form' ).siblings().toggleClass('active')
		}
	},
	report:{
		show: function( objId , objType ){
			EasyBlog.ajax( 'site.views.reports.show' , {
				id: objId,
				type: objType
			}, function( title , html ){
				ejax.dialog( { 'title' : title , 'content' : html } );
			});
		}
	},
	search:{
		toggle: function(){
			$( '.user-options' ).hide().siblings().removeClass('active');
			$( '#easyblog-login-form' ).hide().siblings().removeClass('active');
			$( '#easyblog-search-form' ).toggle();
			$( '#easyblog-search-form' ).siblings().toggleClass( 'active' );
		}
	},
	toolbar:{
		dashboard: function(){
			$( '#easyblog-search-form' ).hide();
			$( '#easyblog-login-form' ).hide();
			$( '.user-options' ).toggle();
			$( '.user-options' ).siblings().toggleClass( 'active' );
		}
	},
	images: {
		initFancybox: function(element, options) {

			if (window.eblog_lightbox_strip_extension && window.eblog_lightbox_title) {

				$(element).each(function(){

					var el = $(this),
						title = el.attr("title") || "",
						parts = title.split(".").reverse();

					if (/jpg|png|gif|xcf|odg|bmp|jpeg/.test(parts[0].toLowerCase())) {
						parts.splice(0, 1);
					}

					title = parts.reverse().join('.');

					el.fancybox($.extend(true, {}, options, {title: title}));
				});

			} else {

				$(element).fancybox(options);
			}
		},

		initCaption: function(images) {

			$(images).each(function(i, image){

				var image = $(image).removeClass("easyblog-image-caption");

				if (image.parents(".easyblog-image-caption-container").length > 0) {
					return;
				}

				var imageUrl = $(image).attr("src"),
					hasFancybox = image.parent().hasClass("easyblog-thumb-preview");

				var hasSiblings = function(image) {

					var image = image[0],
						value = false;

					$.each(image.parentNode.childNodes, function(i, node){

						if (node!=image && !$(node).hasClass("easyblog-image-caption") && !$(node).hasClass("easyblog-thumb-preview")) {
							value = true;
						}

					});

					return value;
				}

				// Reinject src so we can trigger the load function
				image
					.one("load", function() {

						var target = (hasFancybox) ? image.parent() : image;

						// Decide where to float
						var orientation = target.css("float");

						if (orientation=="none") {

							orientation = image.css("float");
						}

						if (orientation=="none") {

							var props = (image.attr("style") || "").split(";"),
								css = {};

							$.each(props, function(i, prop){
								var _prop = prop.split(":");
								css[$.trim(_prop[0])] = $.trim(_prop[1]);
							});

							if (css["margin-left"]=="auto" && css["margin-right"]=="auto") {
								orientation = "center";
							}
						}

						// Use alignment
						if (orientation=="none") {

							var alignment = image.parent().attr("align");

							if (alignment===undefined || alignment=="none") {
								alignment = image.attr("align");
							}

							switch (alignment) {

								case "left":
									orientation = "left";
									break;

								case "right":
									orientation = "right";
									break;

								case "center":
								case "middle":
									orientation = "center";
									break;
							}
						}

						if (!/none|center/.test(orientation)) {

							orientation = ((hasSiblings(target)) ? "float" : "align") + orientation;
						}

						// Prepare container
						var container =
							$("<span>")
								.addClass("easyblog-image-caption-container orientation-" + orientation);

						if (orientation=="center" || orientation=="alignright" || orientation=="alignleft") {

							var additionalWrapper = $("<span>");

							target.wrap(additionalWrapper);

							target.parent().wrap(container);

						} else {

							// Insert container
							target.wrap(container);
						}

						// Prepare caption
						var caption = $("<span>").addClass("easyblog-image-caption");

						caption
							.width(target.outerWidth())
							.html(image.attr("title"));

						// Insert caption
						target.after(caption);

					})
					.removeAttr("src")
					.attr("src", imageUrl);
			});
		}
	},
	captcha: {
		reload: function(){
			var previousId	= $( '#captcha-id' ).val();
			ejax.load( 'entry' , 'reloadCaptcha' , previousId );
		},
		reloadImage: function( id , source ){
			$( '#captcha-image' ).attr( 'src' , source );
			$( '#captcha-id' ).val( id );
			$( '#captcha-response' ).val( '' );
		}
	},
	comments:{
		edit: function( id ){
			ejax.load( 'entry' , 'editComment' , id );
		},
		remove: function( id ){
			ejax.load( 'entry' , 'deleteComment' , id );
		}
	},
	checkbox: {
		render: function(){
			// Transform all checkboxes into nicer switches
			$( '.option-enable' ).click( function(){
				var parent = $(this).parent();
				$( '.option-disable' , parent ).removeClass( 'selected' );
				$( this ).addClass( 'selected' );
				$( '.radiobox' , parent ).attr( 'value' , 1 );
			});

			$( '.option-disable' ).click( function(){
				var parent = $(this).parent();
				$( '.option-enable' , parent ).removeClass( 'selected' );
				$( this ).addClass( 'selected' );
				$( '.radiobox' , parent ).attr( 'value' , 0 );
			});
		}
	},
	categories :
	{
		loadMore : function( element )
		{
			$( element ).parent().hide().next( '.more-subcategories' ).show();
		}
	},

	drafts: {
		getContent: null,
		// Frequency of draft checks by default to 5 seconds
		frequency: 15000,
		chars: 0,
		check: function(){

			// Returns the content
			var content	= eblog.drafts.getContent();

			if( typeof content == 'undefined' )
			{
				return;
			}

			var title	= $("#title").val();

			if( content.length > 0 || ( title.length > 0 && title != emptyText ))
			{
				// Only run this when there's more contents
				ejax.load( 'dashboard' , 'saveDraft' , ejax.getFormVal( '#blogForm' ) , content , '' );
			}
			setTimeout( 'eblog.drafts.check()', eblog.drafts.frequency );
		},
		save: function() {
	        //do submitting
	        eblog.editor.toggleSave();

			$(window).unbind('beforeunload');
			$('#form-task').val('savedraft');

			var data 	= eblog.editor.getContent(),
				content	= $( '<div>' ).html( data ).html();

			$('#write_content_hidden' ).val( content );
			$('#blogForm').submit();
		}
	},
	subscription: {
        // show subscription
		show: function( type , id ) {
			ejax.load( 'subscription', 'showForm' , type , id );
		},
		submit: function( type ) {
			eblog.loader.loading( 'eblog_loader' );
			ejax.load( 'subscription', 'submitForm', type , ejax.getFormVal('#frmSubscribe') );
		}
	},
	/**
	 * Dashboard
	 */
	dashboard: {
		logout: function(){
			$( '#eblog-logout' ).submit();
		},
		changeCollab: function( type ){
			$( '#blog_contribute_source' ).val( type );
		},
		changeAuthor: function( title , url ){
			ejax.dialog({
				width: 700,
				height: 500,
				title: title ,
				content: '',
				beforeDisplay: function(){

					var dialog = $(this);

					// Remove padding from dialog
					dialog.find('.dialog-middle').css('padding', 0);
				},
				afterDisplay: function(){

					var dialog = $(this);

					// Add iframe
					$('<iframe>')
						.attr('src', url )
						.css({
							width: dialog.find('.dialog-middle').width(),
							height: dialog.find('.dialog-middle').height(),
							border: 'none'
						})
						.appendTo(dialog.find('.dialog-middle-content'));
				}
			});

		},
		changeCategory: function( title, url ){
			ejax.dialog({
				width: 700,
				height: 500,
				title: title ,
				content: '',
				beforeDisplay: function(){

					var dialog = $(this);

					// Remove padding from dialog
					dialog.find('.dialog-middle').css('padding', 0);
				},
				afterDisplay: function(){

					var dialog = $(this);

					// Add iframe
					$('<iframe>')
						.attr('src', url )
						.css({
							width: dialog.find('.dialog-middle').width(),
							height: dialog.find('.dialog-middle').height(),
							border: 'none'
						})
						.appendTo(dialog.find('.dialog-middle-content'));
				}
			});

		},
		socialshare: {
			setActive: function( element ){
				$( element ).parent().toggleClass( 'active' );
			}
		},
		drafts: {
			discard: function( cids ){
				ejax.load( 'dashboard' , 'confirmDeleteDraft' , cids );
			},
			discardAll: function(){
				ejax.load( 'dashboard' , 'confirmDeleteAllDraft' );
			}
		},
		lists: {
			init: function( element ){

				$( '#dashboard-'+ element ).checkList({
					checkbox: ".stackSelect",
					masterCheckbox: ".stackSelectAll",
					change: function(selected, deselected){
						this.parent('.ui-list-select').toggleClass('active', selected.length > 0);
						$('#select-actions').toggle(selected.length > 0);
					}
				});
			}
		},
		toggle: function( element ){
			if( $( element ).parent().next().css( 'display' ) == 'block' )
			{
                $( element ).parent().addClass( 'ui-togbox' );
				$( element ).parent().next().hide();
			}
			else
			{
                $( element ).parent().removeClass( 'ui-togbox' );
				$( element ).parent().next().show();
			}
		},
		quickpost: {
			notify: function( type , message ){
				$( '#eblog-message' ).removeClass( 'error info success' ).addClass( type );
				$( '#eblog-message div' ).html( message );
				$( '#eblog-message').show();
			},
			save: function(){
				eblog.loader.loading( 'quickpost-loading' );
				var values	= ejax.getFormVal( '#quick-post' );
				ejax.load( 'dashboard' , 'save' , values );
			},
			draft: function(){
				eblog.loader.loading( 'quickdraft-loading' );

				var content	= $( '#eblog-post-content' ).val();

				// Only run this when there's more contents
				ejax.load( 'dashboard' , 'quickSaveDraft' , ejax.getFormVal( '#quick-post' ) , content , '' );
			}
		},
		settings: {
			submit: function(){
				// Validate password
				if( $( '#password' ).val() != '' || $( '#password2' ).val() != '' )
				{
					if( $( '#password' ).val() != $( '#password2' ).val() )
					{
						$( '.password-notice' ).show();
						return false;
					}
				}
				$( '#dashboard' ).submit();
			}
		},
		categories:{
			create: function(){
				if( $( '#widget-create-category' ).css( 'display' ) == 'block' )
				{
					$( '#widget-create-category' ).slideUp();
				}
				else
				{
					$( '#widget-create-category' ).slideDown();
				}
				return false;
			},
			edit: function( id ) {
				ejax.load( 'dashboard', 'editCategory' , id );
			},
			remove: function( url , id ){
				ejax.load( 'dashboard' , 'confirmDeleteCategory' , id , url );
			},
			quicksave: function( name ){
			    ejax.load( 'dashboard' , 'quickSaveCategory' , name);
			}
		},
		comments: {
			publish: function( id , status ){
				ejax.load( 'dashboard' , 'publishComment' , id , status );
			},
			publishModerated: function( id , status ){
				ejax.load( 'dashboard' , 'publishModerateComment' , id , status );
			},
			edit: function( id ){
				ejax.load( 'dashboard' , 'editComment' , id );
			},
			remove: function( url , id ){
				ejax.load( 'dashboard' , 'confirmDeleteComment' , id , url );
			}
		},
		action: function( element , redirect ){
			var action	= $( '#' + element + '-action' ).val();
			var form	= '#' + element + '-form';
			var cids	= '';

		    $( form + ' INPUT[name="cid[]"]').each( function() {
		        if ( $(this).prop('checked') ) {
		            if(cids.length == 0)
		            {
		                cids    = $(this).val();
		            }
		            else
		            {
		                cids    = cids + ',' + $(this).val();
		            }
				}
			});

			if( cids == '' )
			{
				eblog.system.alert('COM_EASYBLOG_PLEASE_SELECT_ONE_ITEM_TO_CONTINUE', 'COM_EASYBLOG_WARNING');
				return;
			}

			switch( action )
			{
				case 'copy':
					// Copy blog posts
					ejax.load( 'dashboard' , 'copyForm' , cids );
				break;
				case 'discardDraft':
					eblog.dashboard.drafts.discard( cids );
				break;
				case 'publishBlog':
					eblog.blog.togglePublish( cids , 'publish' );
				break;
				case 'unpublishBlog':
					eblog.blog.togglePublish( cids , 'unpublish' );
				break;
				case 'deleteBlog':
					eblog.blog.confirmDelete( cids , redirect );
				break;
				case 'rejectBlog':
					eblog.editor.reject( cids );
				break;
				case 'unpublishComment':
					eblog.dashboard.comments.publish( cids , 'unpublish' );
				break;
				case 'publishComment':
					eblog.dashboard.comments.publish( cids , 'publish' );
				break;
				case 'removeComment':
					eblog.dashboard.comments.remove( redirect , cids, '' );
				break;
				default :
					eblog.system.alert('COM_EASYBLOG_PLEASE_SELECT_ACTION_TO_PERFORM', 'COM_EASYBLOG_WARNING');
				break;
			}
		},
		videos:{
			insert: function( editor ){
				var url		= $( '#video-source' ).val();
				var width	= $( '#video-width' ).val();
				var height	= $( '#video-height' ).val();

				var data 	= '[embed=videolink]'
							+ '{"video":"' + url + '","width":"' + width + '","height":"' + height + '"}'
							+ '[/embed]';

				jInsertEditorText( data , editor )
				ejax.closedlg();
			},
			showForm: function( editor ){
			    try { IeCursorFix(); } catch(e) {};
				ejax.load( 'dashboard' , 'showVideoForm' , editor );
			}
		},
		preview: function( itemId ) {
			var content	= eblog.drafts.getContent();

			if( typeof content == 'undefined' )
			{
				return;
			}

			var title	= $("#eblog-wrapper #title").val();

			if( content.length > 0 && ( title.length > 0 && title != emptyText ))
			{
				ejax.call('dashboard','saveDraft', [ejax.getFormVal( '#blogForm' ), content, ''], {
				    success: function(){
				    	//second step
						var draftId = $('#draft_id').val();
						if( draftId != '')
						{	var url 	= $.rootPath + '/index.php?option=com_easyblog&view=entry&layout=preview&draftid=' + draftId + '&Itemid=' + itemId,
								width	= screen.width,
								height	= screen.height,
								left	= (screen.width/2)-( width /2),
								top		= (screen.height/2)-(height/2);


							window.open( url , '' , 'toolbar=no, location=no, directories=no, status=yes, menubar=yes, scrollbars=yes, resizable=yes, copyhistory=no, width='+width+', height='+height+', top='+top+', left='+left );
						}
					}
				});
			}
			else
			{
				if( content.length > 0 )
				{
					eblog.system.alert('COM_EASYBLOG_PLEASE_SET_TITLE_BEFORE_PREVIEW', 'COM_EASYBLOG_ENTRY_PREVIEW_MODE');
				}
				else
				{
					eblog.system.alert('COM_EASYBLOG_ENTRY_PREVIEW_MODE_NO_CONTENT', 'COM_EASYBLOG_ENTRY_PREVIEW_MODE');
				}

			}
		}
	},
	/**
	 * Editor
	 */
	editor: {
	    checkPublishStatus: function() {
	        var status  		= $('#published').val();
	        var unpublishdate   = $('#publish_down').val();

	        if(unpublishdate == '' || unpublishdate == '0000-00-00 00:00:00')
	        {
	            eblog.editor.postSubmit();
	            return true;
	        }

	        ejax.load( 'dashboard' , 'checkPublishStatus' , status,  unpublishdate);
	        return true;
	    },
	    cancelSubmit: function() {
			isSave = false;
			$("#save_post").attr('disabled', '');
			return false;
	    },
	    postSubmit: function () {
	        //do submitting
			$(window).unbind('beforeunload');
			$('#blogForm').submit();
	    },
		// save the post
		save: function() {
			eblog.editor.toggleSave();

			// Retrieve the main content.
			var editorContents 	= eblog.editor.getContent();

			// Try to break the parts with the read more.
			var val	= editorContents.split( '<hr id="system-readmore" />' );

			if( val.length > 1 )
			{
				// It has a read more tag
				var intro		= $.sanitizeHTML( val[0] );
				var fulltext	= $.sanitizeHTML( val[1] );
				var content 	= intro + '<hr id="system-readmore" />' + fulltext;
			}
			else
			{
				// Since there is no read more tag here, the first index is always the full content.
				var content 	= $.sanitizeHTML( editorContents );;
			}
			$( '#write_content_hidden' ).val( content );

			$('#save_post_button' ).addClass( 'ui-disabled' );
			$( '#save_post_button' ).attr( 'disabled' , 'disabled' );
			eblog.editor.checkPublishStatus();
		},
		apply: function(){
			$( 'input[name=apply]' ).val( 1 );

			eblog.editor.save();
		},
		reject: function( blogId ) {
		    ejax.load( 'Dashboard' , 'confirmRejectBlog' , blogId );
		},
		search: {
			load: function(){
				try { IeCursorFix(); } catch(e) {};
				ejax.load( 'search' , 'search' , $( '#search-content' ).val() );
			},
			insert: function( value , title , editor ){
				var link = '<a href="'+value+'">'+title+'</a>';

				switch(editor)
				{
					case 'intro':
						if($( '#widget-write-introtext .ui-modhead' ).hasClass('ui-togbox'))
						{
							eblog.editor.setIntro(link);
						}
						else
						{
							jInsertEditorText( link, editor );
						}
						break;
					case 'write_content':
						if($( '#widget-writepost .ui-modhead' ).hasClass('ui-togbox'))
						{
							eblog.editor.setContent(link);
						}
						else
						{
							jInsertEditorText( link, editor );
						}
						break;
					default:
						//do nothing
				}
			}
		},
		setIntro: null,
		setContent: null,
		getContent: null,
		/**
		 * Generate date time picker like Wordpress
		 */
		datetimepicker: {

			element: function( id, reset ) {
				// Referenced from http://www.quackit.com/javascript/javascript_date_and_time_functions.cfm

				// Adds active class on the element.
				$( '#datetime_' + id ).addClass( 'toggle-active' );

				// Hide edit button
				$( '#datetime_edit_' + id ).hide();
				var day, month, year, hour, minute, ampm;

				eblog.editor.datetimepicker.hideEditLink(id);

				var day, month, year, hour, minute, ampm;

				eblog.editor.datetimepicker.hideEditLink(id);

				if( id == 'publish_down' && $('#' + id).val() == '')
				{
				    var tmpStr  = $('#publish_down_reset').val();
				    $('#' + id).val(tmpStr);
				}

				if ( $('#' + id).val() != '' ) {
					var strValue = $('#' + id).val();

					var strTemp = strValue.split(' ');
					var strTime = strTemp[1].split(':');
					var strDate = strTemp[0].split('-');

					day 	= strDate[2];
					month 	= strDate[1];
					year 	= strDate[0];
					hour	= strTime[0];
					minute	= strTime[1];

				}
				else {
					today = new Date();

					day 	= today.getDate();
					month 	= today.getMonth() + 1;  //in js, month start from 0, not 1
					year 	= today.getFullYear();
					hour	= today.getHours();
					minute	= today.getMinutes();
				}

				//minute = parseInt(minute);
				hour = parseInt(hour, 10);

				if (minute.length <= 1) {
					minute = '0' + minute;
				}

				if ( hour >= 12 ) {
					ampm = 'pm';
				}
				else {
					ampm = 'am';
				}

				if ( hour > 12 ) {
					hour -= 12;
				}

				if( ampm == 'am' && hour == 0)
				{
				    hour    = 12;
				}

				if( hour < 10 )
				{
				    hour    = '0' + hour;
				}

				var html = '';
				html += '<div class="dtpicker-wrap" id="dtpicker_'+id+'" style="display: none;">';
				html += '	<select tabindex="4" name="dt_month" id="dt_month_'+id+'">';
				html += '		<option value="01" '+ (month == '01' ? 'selected="selected"' : "" )  +'>'+ sJan +'</option>';
				html += '		<option value="02" '+ (month == '02' ? 'selected="selected"' : "" )  +'>'+ sFeb +'</option>';
				html += '		<option value="03" '+ (month == '03' ? 'selected="selected"' : "" )  +'>'+ sMar +'</option>';
				html += '		<option value="04" '+ (month == '04' ? 'selected="selected"' : "" )  +'>'+ sApr +'</option>';
				html += '		<option value="05" '+ (month == '05' ? 'selected="selected"' : "" )  +'>'+ sMay +'</option>';
				html += '		<option value="06" '+ (month == '06' ? 'selected="selected"' : "" )  +'>'+ sJun +'</option>';
				html += '		<option value="07" '+ (month == '07' ? 'selected="selected"' : "" )  +'>'+ sJul +'</option>';
				html += '		<option value="08" '+ (month == '08' ? 'selected="selected"' : "" )  +'>'+ sAug +'</option>';
				html += '		<option value="09" '+ (month == '09' ? 'selected="selected"' : "" )  +'>'+ sSep +'</option>';
				html += '		<option value="10" '+ (month == '10' ? 'selected="selected"' : "" )  +'>'+ sOct +'</option>';
				html += '		<option value="11" '+ (month == '11' ? 'selected="selected"' : "" )  +'>'+ sNov +'</option>';
				html += '		<option value="12" '+ (month == '12' ? 'selected="selected"' : "" )  +'>'+ sDec +'</option>';
				html += '	</select>';
				html += '	<input type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="' + day + '" name="dt_day" id="dt_day_'+id+'">, ';
				html += '	<input type="text" autocomplete="off" tabindex="4" maxlength="4" size="4" value="' + year + '" name="dt_year" id="dt_year_'+id+'"> @ ';
				html += '	<input type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="' + hour + '" name="dt_hour" id="dt_hour_'+id+'"> : ';
				html += '	<input type="text" autocomplete="off" tabindex="4" maxlength="2" size="2" value="' + minute + '" name="dt_min" id="dt_min_'+id+'">';
				html += '	<select tabindex="4" name="dt_ampm" id="dt_ampm_'+id+'">';
				html += '		<option value="am" ' + (ampm == "am" ? 'selected="selected"' : '') + '>'+ sAm +'</option>';
				html += '		<option value="pm" ' + (ampm == "pm" ? 'selected="selected"' : '') + '>'+ sPm +'</option>';
				html += '	</select>';
				html += '	<div class="dtpicker-action" id="dtpicker_action_'+id+'">';
				html += '		<a class="dtpicker-save ui-button" href="javascript:void(0);" onclick="eblog.editor.datetimepicker.save(\''+id+'\')">'+btnOK+'</a>';

				if ( reset )
				{
					html += '		<a class="dtpicker-reset" href="javascript:void(0);" onclick="eblog.editor.datetimepicker.reset(\''+id+'\')">'+btnReset+'</a>';
				}

				html += '		<a class="dtpicker-cancel" href="javascript:void(0);" onclick="eblog.editor.datetimepicker.cancel(\''+id+'\')">'+btnCancel+'</a>';
				html += '	</div>';
				html += '</div>';

				$(html).insertAfter('#datetime_' + id);
				$('#dtpicker_' + id).slideDown('fast');
			},
			reset: function(id) {
				$('#dtpicker_' + id).slideUp('fast');
				$('#' + id).val('');
				$('#datetime_' + id + ' .datetime_caption').html(sNever);
				eblog.editor.datetimepicker.showEditLink(id);
			},
			cancel: function(id) {
				$('#dtpicker_' + id).slideUp('fast');

				if( id == 'publish_down' && ($('#publish_down_ori').val() == '' || $('#publish_down_ori').val() == '0000-00-00 00:00:00'))
				{
				    // make sure the value get remove.
				    $('#' + id).val('');
				}

				// Remove toggle-active class once the cancel is clicked
				$( '#datetime_' + id ).removeClass( 'toggle-active' );

				eblog.editor.datetimepicker.showEditLink(id);
			},
			save: function(id) {
				$('#dtpicker_' + id).slideUp('fast');

				// Remove toggle-active class once the cancel is clicked
				$( '#datetime_' + id ).removeClass( 'toggle-active' );

				// construct date time
				var day, month, year, hour, minute, ampm;

				//today = new Date();
				day 	= $('#dtpicker_' + id + ' #dt_day_' + id).val();
				month 	= $('#dtpicker_' + id + ' #dt_month_' + id).val();

				month = parseInt(month, 10);
				if ( month < 10 ) {
					month = '0' + month;
				}

				year 	= $('#dtpicker_' + id + ' #dt_year_' + id).val();
				hour	= $('#dtpicker_' + id + ' #dt_hour_' + id).val();

				ampm	= $('#dtpicker_' + id + ' #dt_ampm_' + id).val();
				if ( ampm == 'pm' ) {
					switch ( parseInt(hour, 10) ) {
						case 12:
							//hour = parseInt(hour);
							break;
						default:
							hour = parseInt(hour, 10) + 12;
							break;
					}
				}
				else {
					switch ( parseInt(hour) ) {
						case 12:
							hour = '00';
							break;

						default:
							if ( hour.length <= 1) {
							    hour    = '0' + hour;
							}
							break;
					}
				}

				minute	= $('#dtpicker_' + id + ' #dt_min_' + id).val();
				//minute  = parseInt(minute);
				if ( minute.length <= 1) {
					minute = '0' + minute;
				}


				var setTime = year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':00';
				$('#' + id).val(setTime);

				// $('#datetime_' + id + ' .datetime_caption').html(setTime);
				ejax.load( 'dashboard' , 'updateDisplayDate' , id,  setTime);

				eblog.editor.datetimepicker.showEditLink(id);
				// $('.dtpicker-wrap').remove();
			},
			showEditLink: function(id) {
				$('#datetime_edit_' + id ).show();
			},
			hideEditLink: function(id) {
				$('#datetime_edit_' + id ).hide();
			}
		},
		permalink: {
			edited: false,
			// get permalink from controller
			generate: function() {
				// We don't want to generate empty permalinks and
				// if the permalink is edited, we don't want to change the user's value.
				if( $('#title').val() != '' && !eblog.editor.permalink.edited )
				{
					ejax.load( 'dashboard' , 'getPermalink' , $('#title').val() );
				}
			},
			edit: function(){
				if( $( '#permalink-edit' ).css( 'display' ) == 'none' )
				{
					// We try to remember the state here so that permalink don't get
					// generated everytime we try to change the title.
					eblog.editor.permalink.edited	= true;

					$( '#permalink-edit' ).show();
					$( '#permalink-value' ).hide();
				}
				else
				{
					$( '#permalink-edit' ).hide();
					$( '#permalink-value' ).show();
				}
			},
			save: function(){
				// Change the value of the display
				$( '#permalink-url' ).html( $( '#permalink-data').val() );

				// Hide the edit form first.
				eblog.editor.permalink.edit();
			}
		}
	},
	tags: {
		search: {
			init: function(){
				$('#filter-tags').keyup(function()
				{
					var text = $.trim($(this).val());

					$('.post-tags li')
						.hide()
						.filter(function()
						{
							return (this.textContent || this.innerText || '').toUpperCase().indexOf(text.toUpperCase()) >= 0
						})
						.show();
				});
			}
		}
	},
	loader:{
		item: null,
		loading: function( elementId ) {
			eblog.loader.item = elementId;
			$( '#' + elementId ).addClass( 'eblog_loader' );
			$( '#' + elementId ).show();
		},
		doneLoading: function(){
			if( eblog.loader.item != null )
			{
				$( '#' + eblog.loader.item ).removeClass( 'eblog_loader');
			}
		}
	},
	/**
	 * All comment operations
	 */
	comment: {
	    /**
	     * Comment like or dislike
	     */
	    likes: function(contentId, status, likeId) {
	    	eblog.loader.loading( 'likes-' + contentId );
	        ejax.load('Entry', 'likesComment', contentId, status, likeId);
	    },
		/**
		 * Save comment
		 */
		save: function() {
			//clear err-msg
			$('#eblog-message').removeClass('info error');
			$('#eblog-message').html('');

			eblog.loader.loading( 'comment-form-title' );

			finalData	= ejax.getFormVal('#frmComment');
			ejax.load('Entry', 'commentSave', finalData);

			if ( $('#empty-comment-notice').length > 0 ) {
				$('#empty-comment-notice').fadeOut('100');
			}
		},
		/**
		 * Reply to comment
		 */
		reply: function( id , commentDepth , autoTitle ) {
			// hide all reply container
			$('.cancel_container').hide();

			// show all reply container
			$('.reply_container').show();

			//prepare the comment input form
			$('#comment-reply-form-' + id).show();
			var commentForm = $('#eblog-wrapper #frmComment').clone();
			$('#eblog-wrapper #frmComment').remove();

			$('#comment-reply-form-' + id).addClass('comment-form-inline').append(commentForm);
			$('#parent_id').val(id);
			$('#comment_depth').val(commentDepth);

			if(autoTitle)
			{
				//auto insert title
				var title   = $('#comment-title-' + id).text();
				var reTitle = (title != '') ? 'RE:' + title : '';
				$('#title.inputbox').val(reTitle);
			}

			//toggle toolbar button
			//$('#toolbar-reply-' + id).hide();
			//$('#toolbar-cancel-' + id).show();

			// reset all reply/cancel to reply
			$('.comment-reply-no').removeClass('show-this');
			$('.comment-reply-yes').addClass('show-this');

			// set reply to cancel
			$('#toolbar-reply-' + id).removeClass('show-this');
			$('#toolbar-cancel-' + id).addClass('show-this');
			$('#toolbar-cancel-' + id).parent('.comment-reply').toggleClass('in-action');

			//need to check if bbcode enabled
			if($('.markItUpContainer').length > 0)
			{
				$("#comment").markItUpRemove();
				$("#comment").markItUp(EasyBlogBBCodeSettings);
			}

		},
		/**
		 * Cancel comment reply
		 */
		cancel: function(id) {
			//revert the comment input form
			var commentForm = $('#eblog-wrapper #comment-reply-form-' + id + ' #frmComment').clone();
			$('#eblog-wrapper #comment-reply-form-' + id + ' #frmComment').remove();
			$('#eblog-wrapper #comment-separator').after(commentForm);
			$('#parent_id').val('0');
			$('#comment_depth').val('0');
			$('#comment-reply-form-' + id).hide();

			$('#title.inputbox').val('');

			//toggle toolbar button
			//$('#toolbar-cancel-' + id).hide();
			//$('#toolbar-reply-' + id).toggleClass('hide-this');
			//$('#toolbar-reply-' + id).removeAttr('style');
			$('#toolbar-reply-' + id).toggleClass('show-this');
			$('#toolbar-cancel-' + id).toggleClass('show-this');
			$('#toolbar-cancel-' + id).parent('.comment-reply').toggleClass('in-action');

			//need to check if bbcode enabled
			if($('.markItUpContainer').length > 0)
			{
				$("#comment").markItUpRemove();
				$("#comment").markItUp(EasyBlogBBCodeSettings);
			}
		},

		/**
		 * Save edit comment
		 */
		edit: function() {
			//clear err-msg
			$('#eblog-message').removeClass('info error');
			$('#eblog-message').html('');
			//toggleSpinner(true);

			finalData	= ejax.getFormVal('#frmComment');
			ejax.load('dashboard', 'updateComment', finalData);
		},


		/**
		 * Actions
		 */
		action: function(param, url) {
			var count	= 0;
			var cids    = "";
			var actionStr   = $("#"+param).val();

			if(actionStr == '')
			{
				eblog.system.alert('COM_EASYBLOG_PLEASE_SELECT_ACTION_TO_PERFORM', 'COM_EASYBLOG_WARNING');
				return;
			}

		    $("#adminForm INPUT[name='cid[]']").each( function() {
		        if ( $(this).attr('checked') ) {
		            if(cids.length == 0)
		            {
		                cids    = $(this).val();
		            }
		            else
		            {
		                cids    = cids + ',' + $(this).val();
		            }
		            count++;
				}
			});

			if(count <= 0)
			{
				eblog.system.alert('COM_EASYBLOG_PLEASE_SELECT_ONE_ITEM_TO_CONTINUE', 'COM_EASYBLOG_WARNING');
				return;
			}

			if(actionStr == 'unpublishComment')
			{
				ejax.load('dashboard', 'publishComment', cids, '0', 'comment');
			}
			else if(actionStr == 'publishComment')
			{
			    ejax.load('dashboard', 'publishComment', cids, '1', 'comment');
			}
			else if(actionStr == 'removeComment')
			{
				eblog.comment.confirm( url , cids, '');
			}
		},
		confirm: function(url, commentId, lbl)
		{
			var targetUrl   = url + '&task=removeComment&commentId=' + commentId;
			var callback    = 'window.location = "' + targetUrl + '";';

			eblog.system.dialog('COM_EASYBLOG_ARE_YOU_SURE_YOU_WANT_TO_REMOVE_COMMENT', callback, 'CONFIRMATION');
		    return;
		},

		displayInlineMsg: function (msgType, msg)
		{
		    //$('#err-msg').show();
		    $('#eblog-message').removeClass('info error');
		    $('#eblog-message').html(msg);
		    $('#eblog-message').addClass(msgType);

		    if(msgType == 'info')
		    {
				setTimeout( function() {
					$('#eblog-message').removeClass('info error');
					$('#eblog-message').html('');
				}, 6000);
			}
		}

	},

	/**
	 * Featured
	 */

	featured: {
	    add: function(type, cid){
			ejax.load('Latest', 'makeFeatured', type, cid);
	    },
	    remove: function(type, cid){
		    ejax.load('Latest', 'removeFeatured', type, cid);
		},
		slider: {
			holderWidth: 0,
			element: Array(),
			autorotate: function( interval ){
				var items	= $( '#ezblog-featured .featured-a' ).children();
				var set		= false;

				$( items ).each( function(){
					if( $( this ).hasClass( 'active' ) && set != true )
					{
						if( $( this ).next().length == 0 )
						{
							// return to the parent
							$( '#ezblog-featured .featured-a :first' ).click();
						}
						else
						{
							$( this ).next().click();
						}
						set	= true;
					}
				});

				setTimeout( 'eblog.featured.slider.autorotate(' + interval + ');' , interval );
			},
			init: function( sliderElement , autorotate , interval ){

				eblog.featured.slider.element[ sliderElement ]	= { 'width' : parseInt( $( '.' + sliderElement ).parent().width() ) , 'element' : '.' + sliderElement };

				var total	= 0;
				$( eblog.featured.slider.element[ sliderElement ].element ).children().each( function(){
					total	+= eblog.featured.slider.element[ sliderElement ].width;
				});
				$( eblog.featured.slider.element[ sliderElement ].element ).css( 'width' , total );
				$( eblog.featured.slider.element[ sliderElement ].element ).children().css( 'width' , eblog.featured.slider.element[ sliderElement ].width );

				if( autorotate )
				{
					interval	= parseInt( interval ) * 1000;
					setTimeout( 'eblog.featured.slider.autorotate(' + interval + ');' , interval );
				}
			},
			slide: function( index , sliderElement ){
				var left		= 0;
				var elementId	= index;

				if( index != 1 )
				{
					index	-= 1;
					left	= eblog.featured.slider.element[ sliderElement ].width * parseInt( index );
				}
				$( eblog.featured.slider.element[ sliderElement ].element ).animate( { 'left' : '-' + left + 'px' } , 'slow' );
				$( eblog.featured.slider.element[ sliderElement ].element ).parent().parent().children( 'div.featured-navi' ).children().children().removeClass( 'active' );

				// Set active element for the slider buttons.
				$( eblog.featured.slider.element[ sliderElement ].element ).parent().parent().children( 'div.featured-navi' ).children().children( '.slider-navi-' + elementId ).addClass( 'active' );
			}
		}
	},


	/**
	 * Spinner
	 */
	spinner: {

		// toggle btw the spinner and save button
		show: function() {
			$('#blogSubmitBtn').hide();
			$('#blogSubmitWait').show();
		},

		// toggle btw the spinner and save button
		hide: function() {
			$('#blogSubmitWait').hide();
			$('#blogSubmitBtn').show();
		},

		// for publish operation
		publish: function(id, show) {
			if(show == 1)
			{
				$("#"+id+"Spinner").html("<img src=\""+spinnerPath+"\" alt=\"Loading\">");
			}
			else
			{
				$("#"+id+"Spinner").html("");
			}
		}

	},


	/**
	 * Elements
	 */
	element: {

		focus: function(element) {
			ele	= '#' + element;
			$(ele).focus();
			ejax.closedlg();
		}
	},


	/**
	 * Blog
	 */
	blog: {

		/**
		 * HTTP POST
		 */
		publish: function(url, id, status) {
			var targetUrl   = url + '&task=toggleBlogStatus&status=' + status + '&blogId=' + id;
			window.location = targetUrl;
		},
		remove: function(id, actionSrc) {
			var id_str = "";
			var src     = (actionSrc) ? actionSrc : 'Dashboard';

			$.each(id, function() {
				eblog.spinner.publish(id, 1);
				if(id_str!="")
				{
					id_str += ",";
				}
				id_str += this;
		    });

			ejax.load('Dashboard', 'deleteBlog', id_str, src);
		},
		confirmDelete: function( ids , url ){
			ejax.load( 'dashboard' , 'confirmDelete' , ids , url );
		},
		confirm: function(url, blogId, lbl) {
			var targetUrl   = url + '&task=deleteBlog&blogId=' + blogId;
			var callback    = 'window.location = "' + targetUrl + '";';

			//console.log(callback);

 		    //eblog.system.dialog( 'COM_EASYBLOG_ARE_YOU_SURE_YOU_WANT_TO_REMOVE_BLOG' , callback, 'CONFIRMATION');
		    return;
		},

		approve: function( url , blogId ) {
			ejax.load( 'dashboard' , 'confirmApproveBlog' , blogId , url );
		},
		ajaxpublish: function(id, status, actionSrc) {
			var id_str  = "";
			var src     = (actionSrc) ? actionSrc : 'Dashboard';

			$.each(id, function() {
				eblog.spinner.publish( id , 1 );
				if( id_str!="" )
				{
					id_str += ",";
				}
				id_str += this;
		    });
			ejax.load( 'Dashboard', 'togglePublishStatus', id_str , status , src);
		},
		togglePublish: function( id , action ){
			ejax.load( 'dashboard' , 'togglePublish' , id , action );
		},
		action: function( param , url ) {
			var count		= 0;
			var cids    	= "";
			var actionStr   = $("#"+param).val();

			if(actionStr == 'default')
			{
				return;
			}

		    $("#adminForm INPUT[name='cid[]']").each( function() {
		        if ( $(this).attr('checked') ) {
		            if(cids.length == 0)
		            {
		                cids    = $(this).val();
		            }
		            else
		            {
		                cids    = cids + ',' + $(this).val();
		            }
		            count++;
				}
			});

			if(count <= 0)
			{
				eblog.system.alert('COM_EASYBLOG_PLEASE_SELECT_ONE_ITEM_TO_CONTINUE', 'COM_EASYBLOG_WARNING');
				return;
			}

			if(actionStr == 'unpublishBlog')
			{
				eblog.blog.togglePublish( cids , 'unpublish' );
			}
			else if(actionStr == 'publishBlog')
			{
				eblog.blog.togglePublish( cids , 'publish' );
			}
			else if(actionStr == 'deleteBlog')
			{
				eblog.blog.confirmDelete( cids , url );
			}
		},


		/**
		 * Tab section in blog.read.php
		 */
		tab: {
			init: function() {

				// hide all containers
				$('.tab_container').hide();

				// Show the first container
				$( 'div.tab-wrapper .tab_container:first' ).show();

				// Make the first tab active all the time.
				$('.tab_item:first').addClass('item-active');

				// Bind the click function on the tabs.
				$('ul.tab_button li.tab_item a').click( function() {
					var element	= $(this).parent();

					if ( element.hasClass( 'item-active' ) )
					{
						return false;
					}

					element.siblings().each( function(){

						if( $(this).hasClass( 'item-active' ) )
						{
							$(this).removeClass( 'item-active' );
						}
					});

					element.addClass( 'item-active' );

					// hide all other container
					$( '.tab_container' ).hide();

					// get id from element
					var _id = element.attr('id');

					var _x = _id.split('-');
					var id = _x[1];

					$( '#section-' + id ).show();

					return false;
				});
			}
		},

		/**
		 * ajax unsubscribe blog
		 */
		unsubscribe: function( sid, bid )
		{
			ejax.load( 'entry' , 'confirmUnsubscribeBlog' , sid , bid );
		}
	},
	/**
	 * Tags
	 */
	tag: {
		remove: function( redirect , tagId ) {
			ejax.load( 'Dashboard' , 'confirmDeleteTag' , tagId , redirect );
		},
		edit: function(id) {
			ejax.load('Dashboard', 'editTagDialog', id);
		},
		/**
		 * Actions
		 */
		action: function(param, url) {
			var count	= 0;
			var cids    = "";
			var actionStr   = $("#"+param).val();

			if(actionStr == '')
			{
				eblog.system.alert('COM_EASYBLOG_PLEASE_SELECT_ACTION_TO_PERFORM', 'COM_EASYBLOG_WARNING');
				return;
			}

		    $("#adminForm INPUT[name='cid[]']").each( function() {
		        if ( $(this).attr('checked') ) {
		            if(cids.length == 0)
		            {
		                cids    = $(this).val();
		            }
		            else
		            {
		                cids    = cids + ',' + $(this).val();
		            }
		            count++;
				}
			});

			if(count <= 0)
			{
				eblog.system.alert('COM_EASYBLOG_PLEASE_SELECT_ONE_ITEM_TO_CONTINUE', 'COM_EASYBLOG_WARNING');
				return;
			}

			if(actionStr == 'deleteTag')
			{
				eblog.tag.confirm(url, cids, '');
			}
		},

		confirm: function(url, tagId, lbl)
		{
			var targetUrl   = url + '&task=deleteTag&tagId=' + tagId;
			var callback    = 'window.location = "' + targetUrl + '";';

			eblog.system.dialog('COM_EASYBLOG_ARE_YOU_SURE_YOU_WANT_TO_REMOVE_TAGS', callback, 'CONFIRMATION');
		    return;
		},

		save: function() {
			finalData	= ejax.getFormVal('#frmEditTag');
			ejax.load('Dashboard', 'saveTag', finalData);
		}

	},
	socialshare:{
		share: function( id , type ){
			eblog.spinner.publish( id , 1 );
			ejax.load( 'dashboard' , 'ajaxSocialShare' , id , type );
		}
	},
	/**
	 * Twitter
	 */
	twitter: {

		update: function(id) {
			if ( id != "" )
			{
				eblog.spinner.publish(id, 1);
				ejax.load('Dashboard', 'ajaxUpdateTwitter', id);
			}
		}

	},


	/**
	 * Forms
	 */
	form: {

		checkbox: {

			checkall: function() {
				$("#adminForm INPUT[type='checkbox']").each( function() {
				    if ( $('#toggle').attr('checked') )
						$(this).attr('checked', true);
			  		else
			  		    $(this).attr('checked', false);
				});
				return false;
			}
		}

	},


	/**
	 * Trackbacks
	 */
	trackback: {

		url: {

			copy: function() {
				$( '#trackback-url' ).focus().select();
			}

		}

	},
	/**
	 *  Common method for EasyBlog
	 */
	system: {
	    alert: function ( text, title ) {
	    	ejax.alert(ejax.string(text), ejax.string(title), '450', 'auto');

			//ejax.load('Latest', 'ajaxShowAlertDialog', _text, _title);
	    },

	    dialog: function ( text, callback, title ) {

			var dialogActions = '<div class="dialog-actions"><input type="button" value="' + ejax.string('No') + '" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" /><input type="button" value="' + ejax.string('Yes') + '" class="button" id="edialog-submit" name="edialog-submit" onclick="' + callback + '" /></div>';

	       	var options = {
	    		title: ejax.string(title),
	    		content: ejax.string(text) + dialogActions
	    	}

	    	ejax.dialog(options);

	        //ejax.load('Latest', 'ajaxShowDialog', text, callback, title);
	    },

	    loader: function (show) {

	        if(show)
	        {
	            if($('img#easyblog-loader').length > 0)
	            {
	                $('img#easyblog-loader').remove();
	            }

	            var img  = new Image;
	            img.src  = '/components/com_easyblog/assets/images/loader.gif';
	            img.name = 'easyblog-loader';
	            img.id 	 = 'easyblog-loader';


	            var divBody     = $('div#eblog-wrapper');
	            var divWidth	= divBody.width();

	            //divHeight   	= window.innerHeight || self.innerHeight || (de&&de.clientHeight) || window.parent.document.body.clientHeight;
	            divHeight   	= window.innerHeight || self.innerHeight || window.parent.document.body.clientHeight;

	            divBody.prepend(img);
	            $('img#easyblog-loader').css('marginTop', (divHeight / 2));
	            $('img#easyblog-loader').css('marginLeft', (divWidth / 2));
	            $('img#easyblog-loader').css('position', 'absolute');
	            $('img#easyblog-loader').css('z-index', 10);
	        }
	        else
	        {
	            if($('img#easyblog-loader').length > 0)
	            {
	                $('img#easyblog-loader').remove();
	            }
	        }
		}
	},

	teamblog: {
	    join: function(teamId) {
	        var id  = String(teamId);
	        ejax.load('TeamBlog', 'showDialog', id, 'join');
	    },
	    leave: function(teamId) {
	        var id  = String(teamId);
	        ejax.load('TeamBlog', 'showDialog', id, 'leave');
	    },
	    leaveteam: function() {
			eblog.loader.loading( 'eblog_loader' );
			ejax.load( 'teamblog' , 'leaveTeam' , ejax.getFormVal( '#frmLeave' ) );
	    },
	    send: function() {
			eblog.loader.loading( 'eblog_loader' );
			ejax.load( 'teamblog' , 'addJoinRequest' , ejax.getFormVal( '#frmJoin' ) );
	    },
	    approve: function() {

	    },
	    reject: function() {

	    }
	},

	calendar: {
		reload: function(view, func, position, itemid, size, type, timestamp) {
			ejax.load( view , func, position, itemid, size, type, timestamp);
		},

		showtooltips : function(id)
		{
			$('.easyblog_calendar_tooltips').hide();
			$('#com_easyblog_calendar_day_'+id).show();
		}
	}
}
// eblog.js ends

// ejax.js starts

var ejax = window.ejax = {
	http:		false, //HTTP Object
	format: 	'text',
	callback:	function(data){},
	error:		false,
	getHTTPObject : function() {
		var http = false;

		//Use IE's ActiveX items to load the file.
		if ( typeof ActiveXObject != 'undefined' ) {
			try {
				http = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e) {
				try {
					http = new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch (E) {
					http = false;
				}
			}
		//If ActiveX is not available, use the XMLHttpRequest of Firefox/Mozilla etc. to load the document.
		}
		else if ( XMLHttpRequest ) {
			try {http = new XMLHttpRequest();}
			catch (e) {http = false;}
		}
		return http;
	},

	/**
	 * Ajax function
	 */

	// ejax.call('controller','task', ['arg1', 'arg2'], function(){});
	// ejax.call('controller','task', ['arg1', 'arg2'], {
	//    success: function(){},
	//    error: function(){}
	// });
	call: function(view, method, params, callback)
	{
		var args = [{view: view, callback: callback}, method];
		args = args.concat(params);
		ejax.load.apply(this, args);
	},

	load : function ( view, method )
	{
		var callback = {
			success: function(){},
			error: function(){}
		};

		if (typeof view == "object")
		{
			callback = $.extend(callback, ($.isFunction(view.callback)) ? {success: view.callback} : view.callback);
			view = view.view;
		}

		// This will be the site we are trying to connect to.
		url	 = eblog_site;
		url	+= '&tmpl=component';
		url += '&no_html=1';
		url += '&format=ejax';
		url += '&' + EasyBlog.getToken() + '=1';

		//Kill the Cache problem in IE.
		url	+= "&uid=" + new Date().getTime();

		var parameters	= '&view=' + view + '&layout=' + method;

		// If there is more than 1 arguments, we want to accept it as parameters.
		if ( arguments.length > 2 )
		{
			for ( var i = 2; i < arguments.length; i++ )
			{
				var myArgument	= arguments[ i ];

				if($.isArray(myArgument))
				{
					for(var j = 0; j < myArgument.length; j++)
					{
					    var argument    = myArgument[j];
						if ( typeof( argument ) == 'string' )
						{
							// Encode value to proper html entities.
							parameters	+= '&value' + ( i - 2 ) + '[]=' + encodeURIComponent( argument );
						}
					}
				} else {
				    var argument    = myArgument;
					if ( typeof( argument ) == 'string' )
					{
						// Encode value to proper html entities.
						parameters	+= '&value' + ( i - 2 ) + '=' + encodeURIComponent( argument );
					}
				}
			}
		}

		var http = this.getHTTPObject(); //The XMLHttpRequest object is recreated at every call - to defeat Cache problem in IE

		if ( !http || !view || !method ) return;

// 		if ( this.http.overrideMimeType )
// 			this.http.overrideMimeType( 'text/xml' );

		//Closure
 		var ths = this;

		http.open( 'POST' , url , true );

		// Required because we are doing a post
		http.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );

		http.onreadystatechange = function(){
			//Call a function when the state changes.

			if (http.readyState == 4)
			{
				//Ready State will be 4 when the document is loaded.
				if (http.status == 200)
				{
					var result = "";

					if (http.responseText)
					{
						result = http.responseText;
					}

					// Evaluate the result before processing the JSON text. New lines in JSON string,
					// when evaluated will create errors in IE.
					result	= result.replace(/[\n\r]/g,"");

					try {
						result	= eval( result );
					} catch(e) {
						if (callback.error) { callback.error('Invalid response.'); }
					}

					// Give the data to the callback function.
					ths.process( result, callback );
				}
				else
				{
					//An error occured
					if (ths.error)
					{
						ths.error( http.status );
						if (callback.error) { callback.error(http.status); }
					}
				}
			}
		}
		http.send( parameters );
	},

	/**
	 * Method to get translated string from server
	 *
	 * @param	string
	 */
	_string: [],

	string: function( str ) {

		if (ejax._string[str]!=undefined)
			return ejax._string[str];

		var url	 = eblog_site + '&tmpl=component&no_html=1&controller=easyblog&task=ajaxGetSystemString';

		var r1 = $.ajax({
		    type: "POST",
			url: url,
			data: "data=" + str,
			async: false,
			cache: true
		}).responseText;

		ejax._string[str] = r1;

		return r1;
	},

	/**
	 * Get form values
	 *
	 * @param	string	Form ID
	 */
	getFormVal : function( element ) {

	    var inputs  = [];
	    var val		= null;

		$( ':input', $( element ) ).each( function() {
			val = this.value.replace(/"/g, "&quot;");
			val = encodeURIComponent(val);

			if($(this).is(':checkbox') || $(this).is(':radio'))
		    {
				if($(this).prop('checked'))
				{
					inputs.push( this.name + '=' + escape( val ) );
				}
		    }
		    else
		    {
				inputs.push( this.name + '=' + escape( val ) );
			}
		});
		//var finalData = inputs.join('&&');
		//return finalData;
		return inputs;
	},

	process : function ( result, callback ){

		// Process response according to the key
		for(var i=0; i < result.length;i++)
		{
			var action	= result[ i ][ 0 ];

			switch( action )
			{
				case 'script':
					var data	= result[ i ][ 1 ];
					eval("EasyBlog(function($){" + data + "});");
					break;

				case 'after':
					var id		= result[ i ][ 1 ];
					var value	= result[ i ][ 2 ];


					$( '#' + id ).after( value );
					break;

				case 'append':
					var id		= result[ i ][ 1 ];
					var value	= result[ i ][ 2 ];

					$( '#' + id ).append( value );
					break;

				case 'assign':
					var id		= result[ i ][ 1 ];
					var value	= result[ i ][ 2 ];

					$( '#' + id ).html( value );
					break;

				case 'value':
					var id		= result[ i ][ 1 ];
					var value	= result[ i ][ 2 ];

					$( '#' + id ).val( value );
					break;
				case 'prepend':
					var id		= result[ i ][ 1 ];
					var value	= result[ i ][ 2 ];
					$( '#' + id ).prepend( value );
					break;
				case 'destroy':
					var id		= result[ i ][ 1 ];
					$( '#' + id ).remove();
					break;
				case 'dialog':
					ejax.dialog( result[ i ][ 1 ] );
					break;
				case 'alert':
					ejax.alert( result[ i ][ 1 ], result[ i ][ 2 ], result[ i ][ 3 ] , result[ i ][ 4 ] );
					break;
				case 'create':
					break;
				case 'error':
					var args = result[ i ].slice(1);
					callback.error.apply(this,args);
					break;
				case 'callback':
					var args = result[ i ].slice(1);
					callback.success.apply(this, args);
					break;
			}
		}
		delete result;
	},

	/**
	 * Dialog
	 */
	dialog: function( options ) {
		ejax._showPopup( options );
	},

	closedlg: function() {
		var dialog = $('#eblog-dialog');
		var dialogOverlay = $('#eblog-overlay');

		var options = dialog.data('options');

		dialogOverlay.hide();

		dialog
			.fadeOut(function()
			{
				options.afterClose.apply(dialog);
			});

		$(window).unbind('.dialog');

		$(document).unbind('keyup', ejax._attachPopupShortcuts);
	},

	_attachPopupShortcuts: function(e)
	{
		if (e.keyCode == 27) { ejax.closedlg(); }
	},

	/**
	 * Alert
	 */
	alert: function( content, title, width, height ) {

		var COM_EASYBLOG_OK = ejax.string('COM_EASYBLOG_OK');

		var dialogActions = '<div class="dialog-actions"><input type="button" value="' + COM_EASYBLOG_OK + '" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" /></div>';

 		var options = {
 			title: title,
			content: content + dialogActions,
			width: width,
			height: height
		}

		ejax._showPopup( options );
	},

	/**
	 * Private function
	 *
	 * Generate dialog and popup dialog
	 */
	_showPopup: function( options ){

		var defaultOptions = {
			width: '500',
			height: 'auto',
			type: 'dialog',
			beforeDisplay: function(){},
			afterDisplay: function(){},
			afterClose: function(){}
		}

		var options = $.extend({}, defaultOptions, options);

		var dialogOverlay = $('#eblog-overlay');

		if (dialogOverlay.length < 1)
		{
			dialogOverlay = '<div id="eblog-overlay"></div>';

			dialogOverlay = $(dialogOverlay).appendTo('body');

			dialogOverlay.click(function()
			{
				ejax.closedlg();
			});
		}

		var dialog = $('#eblog-dialog');

		if (dialog.length < 1)
		{
			dialogTemplate   = '<div id="eblog-dialog">';
			dialogTemplate	+= '	<div class="dialog">';
			dialogTemplate	+= '		<div class="dialog-wrap">';
			dialogTemplate	+= '			<div class="dialog-top">';
			dialogTemplate	+= '				<h3></h3>';
			dialogTemplate	+= '				<a href="javascript:void(0);" onclick="ejax.closedlg();" class="closeme">Close</a>';
			dialogTemplate	+= '			</div>';
			dialogTemplate	+= '			<div class="dialog-middle clearfix">';
			dialogTemplate	+= '				<div class="dialog-middle-content"></div>';
			dialogTemplate	+= '			</div>';
			dialogTemplate	+= '		</div>';
			dialogTemplate	+= '	</div>';
			dialogTemplate	+= '</div>';

			dialog = $(dialogTemplate).appendTo('body');
		}

		// Store dialog options
		dialog
			.data('options', options);

		var dialogTitle = dialog.find('.dialog-top h3');

		options.title	= options.title != null ? options.title : '&nbsp;';
		dialogTitle.html(unescape(options.title));

		var dialogContent = $('#eblog-dialog .dialog-middle-content');

		dialogContent
			.css({
				width : (options.width=='auto') ? 'auto' : parseInt(options.width),
				height: (options.height=='auto') ? 'auto' : parseInt(options.height)
			})
			.html(options.content);

		options.beforeDisplay.apply(dialog);


		var positionDialog = function()
		{
			dialog
				.css({ top: 0, left: 0 })
				.position({ my: 'center', at: 'center', of: window });

			dialogOverlay
				.css({
					width: $(document).width(),
					height: $(document).height()
				})
				.show();
		};

		dialog
			.show(0, function()
			{
				positionDialog();

				var positionDelay;
				$(window)
					.bind('resize.dialog scroll.dialog', function()
					{
						clearTimeout(positionDelay);
						positionDelay = setTimeout(positionDialog, 50);
					});
			});

		dialog.fadeOut(0, function() {
			dialog.fadeIn(function() {
				options.afterDisplay.apply(dialog);
			});
		});

		$(document).on('click.eb.dialog', '#edialog-cancel, #edialog-submit', function() {
		 	ejax.closedlg();
		});

		$(document).bind('keyup', ejax._attachPopupShortcuts);
	}
}
// ejax.js ends

module.resolve();

});
