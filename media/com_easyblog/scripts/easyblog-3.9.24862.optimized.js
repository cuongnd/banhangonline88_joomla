FD31.installer("EasyBlog", "definitions", function($){
$.module(["easyblog/admin","easyblog/dashboard","easyblog/dashboard/blogimage","easyblog/dashboard/editor","easyblog/dashboard/medialink","easyblog/easyblog","easyblog/layout/responsive","easyblog/layout/lightbox","easyblog/legacy","easyblog/featured","easyblog/location","easyblog/media","easyblog/media/navigation","easyblog/media/uploader","easyblog/media/browser","easyblog/media/browser.item","easyblog/media/editor","easyblog/","easyblog/media/constrain","easyblog/media/editor.audio","easyblog/media/editor.file","easyblog/media/editor.image","easyblog/media/editor.video","easyblog/media/uploader.item","easyblog/ratings","easyblog/tag"]);
$.require.template.loader(["easyblog/media/recent.item","easyblog/media/browser","easyblog/media/browser.item-group","easyblog/media/browser.item","easyblog/media/browser.tree-item-group","easyblog/media/browser.tree-item","easyblog/media/browser.pagination-page","easyblog/media/browser.uploader","easyblog/media/browser.uploader.item","easyblog/media/editor","easyblog/media/editor.viewport","easyblog/media/navigation.item","easyblog/media/navigation.itemgroup","easyblog/media/editor.audio","easyblog/media/editor.audio.player","easyblog/media/editor.file","easyblog/media/editor.file.preview","easyblog/media/editor.image","easyblog/media/editor.image.variation","easyblog/media/editor.image.caption","easyblog/media/editor.video","easyblog/media/editor.video.player","easyblog/dashboard/dashboard.tags.item"]);
$.require.language.loader(["COM_EASYBLOG_MM_UNABLE_TO_FIND_EXPORTER","COM_EASYBLOG_MM_GETTING_IMAGE_SIZES","COM_EASYBLOG_MM_UNABLE_TO_RETRIEVE_VARIATIONS","COM_EASYBLOG_MM_ITEM_INSERTED","COM_EASYBLOG_MM_UPLOADING","COM_EASYBLOG_MM_UPLOADING_STATE","COM_EASYBLOG_MM_UPLOADING_PENDING","COM_EASYBLOG_MM_UPLOAD_COMPLETE","COM_EASYBLOG_MM_UPLOAD_PREPARING","COM_EASYBLOG_MM_UPLOAD_UNABLE_PARSE_RESPONSE","COM_EASYBLOG_MM_UPLOADING_LEFT","COM_EASYBLOG_MM_CONFIRM_DELETE_ITEM","COM_EASYBLOG_MM_CANCEL_BUTTON","COM_EASYBLOG_MM_YES_BUTTON","COM_EASYBLOG_MM_ITEM_DELETE_CONFIRMATION"]);
(function(){
var stylesheetNames = ["easyblog/fancybox/default"];
var state = ($.stylesheet({"content":""})) ? "resolve" : "reject";
$.each(stylesheetNames, function(i, stylesheet){ $.require.stylesheet.loader(stylesheet)[state](); });
})();
});
FD31.installer("EasyBlog", "scripts", function($){
EasyBlog.module("admin", function($){

	var module = this;

	var admin = window.admin = {
		blog: {
			reject: function( blogId ) {
				ejax.load( 'Pending' , 'confirmRejectBlog' , blogId );
			    return;
			},
			approve: function(blogId , msg ) {
				if ( confirm( msg ) ) {
			    	window.location = eblog_site + '&c=pending&task=approve&cid[]=' + blogId;
			    }
			    return;
			}
		},
		settings: {
			importSettings: function()
			{
				ejax.load( 'settings' , 'import' );
			}
		},
		checkbox: {
			init: function(){
				// Transform checkboxes.
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
		pending:
		{
			reject: function()
			{

			}
		},
		spools: {
			preview: function( id ){
				ejax.load( 'Spools' , 'preview' , id );
			}
		},
		teamblog: {
		    markAdmin : function(teamid, userid , token ) {
	            window.location = eblog_site + '&c=teamblogs&task=markAdmin&teamid=' + teamid + '&userid=' + userid + '&' + token + '=1';
			},
		    removeAdmin : function(teamid, userid) {
				window.location = eblog_site + '&c=teamblogs&task=removeAdmin&teamid=' + teamid + '&userid=' + userid + '&' + token + '=1';
			}
		}
	}


	$(function(){
		
		var className	= $( '#submenu' ).attr( 'class' );

		if( $('#submenu li').eq( 4 ).length > 0 && className != 'settings' )
		{
			ejax.load( 'Easyblog' , 'appendPending' );
		}

		// move system message
		// TODO: This should be disabled in Joomla 3.0
		if ( $('#system-message').length > 0 && $('.eb-bootstrap').length == 0 )
		{
			var message = $('#system-message').html();

			$('#system-message').remove();

			$( '<dl id="system-message">' + message + '</dl>' ).insertAfter('#toolbar-box');
		}

		$('body #settingsForm .admintable tr:odd').addClass('tr-odd');

		$('.admintable tr').hover( function(){
			$(this).addClass('tr-hover');
		},
		function() {
			$(this).removeClass('tr-hover');
		});

		admin.checkbox.init();
	});

	module.resolve();

});
// module: start
EasyBlog.module("dashboard", function($){

var module = this;

EasyBlog.Controller(
	"Dashboard",
	{
		defaultOptions: function() {

		}
	},
	function(self) { return {

		init: function() {

			EasyBlog.dashboard = self;
		},

		registerPlugin: function(pluginName, instance) {

			if (self[pluginName]===undefined) {

				self[pluginName] = instance;
			}
		}
	}}
);

module.resolve();

});

// module: start
EasyBlog.module("dashboard/blogimage", function($) {

var module = this;

EasyBlog.require()
	.library("image")
	.done(function(){

		// controller: start
		EasyBlog.Controller("Dashboard.BlogImage",

			{
				defaultOptions: {

					// Containers
					"{placeHolder}"	: ".blogImagePlaceHolder",
					"{imageData}"	: ".imageData",
					"{image}"       : ".image",

					// Actions
					"{selectBlogImageButton}": ".selectBlogImage",
					"{removeBlogImageButton}": ".removeBlogImage"
				}
			},

			// Instance properties
			function(self) { return {

				init: function() {

					EasyBlog.dashboard.registerPlugin("blogImage", self);

					var meta = $.trim(self.imageData().val());

					if (meta) {
						self.setImage($.parseJSON(meta));
					}
 				},

				"{selectBlogImageButton} click": function() {

					// Optional: For the first param which is null now,
					// by passing in the key of the previously selected blog image,
					// it will activate the item on the browser.
					EasyBlog.mediaManager.browse(null, "blogimage");
				},

				"{removeBlogImageButton} click" : function(el) {

					self.removeImage();

					el.blur();
				},

				removeImage: function() {

					self.image().remove();

					self.element.addClass("empty");

					self.imageData().val("");
				},

				setImage: function(meta) {

					if (!meta) return;

					self.removeImage();

					self.element
						.addClass("loading");

					clearTimeout(self.imageTimer);

					// Clone the meta
					var meta = $.extend({}, meta);

					// Delete metadata
					delete meta.data;

					$.Image.get(meta.thumbnail.url)
						.done(function(image) {

							var resize = function(){

								// Keep a copy of the placeholder's width & height
								var placeHolder = self.placeHolder();
								maxWidth      = placeHolder.width();
								maxHeight     = placeHolder.height();

								// Calculate size
								var size = 	$.Image.resizeWithin(
									image.data("width"),
									image.data("height"),
									maxWidth,
									maxHeight
								);

								size.top  = (maxHeight - size.height) / 2;
								size.left = (maxWidth - size.width) / 2;

								return size;
							};

							var size = resize();

							var checkDimension = function() {

								if (size.width===0 || size.height===0) {

									self.imageTimer = setTimeout(function(){

										size = resize();

										checkDimension();

									}, 1000);

								} else {

									image.css(size);
								}
							}

							checkDimension();

							// Resize and insert
							image
								.addClass("image")
								.css(size)
								.appendTo(self.placeHolder());

							self.imageData().val(JSON.stringify(meta));

							self.element.removeClass("empty");
						})
						.fail(function(){

							self.element.addClass("empty");
						})
						.always(function(){

							self.element.removeClass("loading");
						});
				}
			}}

		);
		// controller: end

		module.resolve();
	});
	// require: end

});
// module: end

// module: start
EasyBlog.module("dashboard/editor", function($){

var module = this;

EasyBlog.Controller(
	"Dashboard.Editor",
	{
		defaultOptions: {

			editorId: "write_content"
		}
	},
	function(self) { return {

		init: function() {

			EasyBlog.dashboard.registerPlugin("editor", self);
		},

		insert: function(html) {

			window.jInsertEditorText(html, self.options.editorId);
		},

		content: function(html) {

			// TODO: Port eblog.editor to this new controller

			if (html!==undefined) {

				window.eblog.editor.setContent(html);
			}

			return window.eblog.editor.getContent();
		}
	}}
);

module.resolve();

});

// module: start
EasyBlog.module("dashboard/medialink", function($) {

var module = this;

EasyBlog.Controller(
	"Dashboard.MediaLink",
	{
		defaultOptions: {
			"{menu}": ".ui-togmenu",
			"{content}": ".ui-togbox"
		}
	},
	function(self){ return {

		init: function() {

		},

		"{menu} click": function(el) {

			var hiding = el.hasClass("active");

			self.menu().removeClass("active");

			self.content().removeClass("active");

			if (!hiding) {

				el.addClass("active");

				self.content("." + el.attr("togbox")).addClass("active");
			}
		}
	}}
);

module.resolve();

});
// module: end

EasyBlog.require()
	.library(
		'ui/position',
		'fancybox',
		'bookmarklet',
		'checked',
		'checkList'
	)
	.script(
		'layout/responsive',
		'layout/lightbox',
		'legacy'
	)
	.done();
EasyBlog.module('layout/responsive', function($) {

	var module = this;

	if (EasyBlog.options.responsive) {
		$(function(){
			$('#eblog-wrapper')
				.responsive([
					{at: 818,  switchTo: 'w768'},
					{at: 600,  switchTo: 'w768 w600'},
					{at: 500,  switchTo: 'w768 w600 w320'}
				]);
		});
	}

	module.resolve();

});
EasyBlog.module('layout/lightbox', function($) {

	var module = this;

	EasyBlog.require()
		.library('fancybox')
		.stylesheet('fancybox/default')
		.script('legacy')
		.done(function(){

			/**
			 * Initializes all the gallery stuffs here
			 **/
			// Init fancy box images.
			if (window.eblog_enable_lightbox) {

				var options = {
					showOverlay: true,
					centerOnScroll: true,
					overlayOpacity: 0.7
				}

				if (!window.eblog_lightbox_title) {
					options.helpers = { title: false };
				}

				if (window.eblog_lightbox_enforce_size) {
					options.maxWidth = window.eblog_lightbox_width;
					options.maxHeight = window.eblog_lightbox_height;
				}

				eblog.images.initFancybox('a.easyblog-thumb-preview', options);
			}

			eblog.images.initCaption('img.easyblog-image-caption');

			module.resolve();
		});
});
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

EasyBlog.module( "featured" , function($) {

var module = this;

// require: start
EasyBlog.require()
.done(function(){

// controller: start

EasyBlog.Controller(

	"Featured.Scroller",

	{
		defaultOptions: {

			elements: null,

			itemWidth: null,

			// Auto rotate option
			autorotate: {
				enabled		: false,
				interval	: 50
			},

			// Items
			"{placeHolder}"	: ".slider-holder",
			"{slider}"		: ".featured-entries",
			"{sliderItems}"	: ".slider-holder ul li",
			"{sliderNavigation}" : ".featured-navi .featured-a a"
		}
	},

	function(self) {return {

		/**
		 * Featured scroller object initialization happens here.
		 *
		 */
		init: function() {

			// Set the current holder width to a temporary location.
			self.options.itemWidth	= self.placeHolder().width() + 1;

			// Calculate the total width of the whole parent container as we need to multiply this by the number of child elements.
			var totalWidth 			= self.sliderItems().length * parseInt( self.options.itemWidth );

			// Now, we need to stretch the parent's width to match the total items.
			self.slider().css( 'width' , totalWidth );

			// Make sure the width of each child items has the same width as its parent.
			self.sliderItems().css( 'width' , self.options.itemWidth );

			if( self.options.autorotate.enabled )
			{
				setTimeout( function(){
					self.initAutoRotate();	
				}, parseInt( self.options.autorotate.interval ) * 1000 );
			}
		},


		nextItem : function( element )
		{

			var index 	= $( element ).data( 'slider' );
			var left 	= 0;

			// If the current index is 1, we can just leave left as 0
			if( index != 1 )
			{
				left 	= self.options.itemWidth * parseInt( index - 1 );
			}

			// Since any items after the first item is hidden by default, we need to show the current item.
			self.slider().children( ':nth-child(' + index + ')' ).show();

			// Now let's animate the placeholder.
			self.slider().animate( {
				left : '-' + left + 'px'
			}, 'slow' );

			// Remove active class from the navigation anchor link.
			self.sliderNavigation( '.active' ).removeClass( 'active' );

			// Set the active element on the current item.
			$( element ).addClass( 'active' );
		},

		"{sliderNavigation} click" : function( element ){
			self.nextItem( element );
		},

		/**
		 * This initializes the auto rotation for the featured items.
		 */
		initAutoRotate: function(){

			var set 	= false;

			self.sliderNavigation().each(function(){

				if( $( this ).hasClass( 'active' ) && set != true )
				{
					if( $( this ).next().length == 0 )
					{
						self.nextItem( self.sliderNavigation( ':first' ) );
					}
					else
					{
						self.nextItem( $(this).next() );
					}
					set	= true;
				}

			});

			setTimeout( function(){
				self.initAutoRotate();
			}, parseInt( self.options.autorotate.interval ) * 1000 );

		}

	} }
);

module.resolve();

// controller: end	
});

});

EasyBlog.module("location", function($) {

var module = this;

// require: start
EasyBlog.require()
	.library(
		"ui/autocomplete"
	)
	.done(function(){

// controller: start

EasyBlog.Controller(

	"Location.Form",

	{
		defaultOptions: {

			language: 'en',

			initialLocation: null,

			mapType			: "ROADMAP",

			"{locationInput}": ".locationInput",

			"{locationLatitude}": ".locationLatitude",

			"{locationLongitude}": ".locationLongitude",

			"{locationMap}": ".locationMap",

			"{autoDetectButton}": ".autoDetectButton"

		}
	},

	function(self) { return {


		init: function() {

			var mapReady = $.uid("ext");

			window[mapReady] = function() {
				$.___GoogleMaps.resolve();
			}

			if (!$.___GoogleMaps) {

				$.___GoogleMaps = $.Deferred();

				EasyBlog.require()
					.script(
						{prefetch: false},
						"https://maps.googleapis.com/maps/api/js?sensor=true&language=" + self.options.language + "&callback=" + mapReady
					);
			}

			// Defer instantiation of controller until Google Maps library is loaded.
			$.___GoogleMaps.done(function() {
				self._init();
			});
		},

		_init: function(el, event) {

			self.geocoder = new google.maps.Geocoder();

			self.hasGeolocation = navigator.geolocation!==undefined;

			if (!self.hasGeolocation) {
				self.autoDetectButton().remove();
			} else {
				self.autoDetectButton().show();
			}

			self.locationInput()
				.autocomplete({

					delay: 300,

					minLength: 0,

					source: self.retrieveSuggestions,

					select: function(event, ui) {

						self.locationInput()
							.autocomplete("close");

						self.setLocation(ui.item.location);
					}
				})
				.prop("disabled", false);

			self.autocomplete = self.locationInput().autocomplete("widget");

			self.autocomplete
				.addClass("location-suggestion");

			var initialLocation = $.trim(self.options.initialLocation);

			if (initialLocation) {

				self.getLocationByAddress(

					initialLocation,

					function(location) {

						self.setLocation(location[0]);
					}
				);

			};

			self.busy(false);
		},

		busy: function(isBusy) {
			self.locationInput().toggleClass("loading", isBusy);
		},

		getUserLocations: function(callback) {
			self.getLocationAutomatically(
				function(locations) {
					self.userLocations = self.buildDataset(locations);
					callback && callback(locations);
				}
			);
		},

		getLocationByAddress: function(address, callback) {

			self.geocoder.geocode(
				{
					address: address
				},
				callback);
		},

		getLocationByCoords: function(latitude, longitude, callback) {

			self.geocoder.geocode(
				{
					location: new google.maps.LatLng(latitude, longitude)
				},
				callback);
		},

		getLocationAutomatically: function(success, fail) {

			if (!navigator.geolocation) {
				return fail("ERRCODE", "Browser does not support geolocation or do not have permission to retrieve location data.")
			}

			navigator.geolocation.getCurrentPosition(
				// Success
				function(position) {
					self.getLocationByCoords(position.coords.latitude, position.coords.longitude, success)
				},
				// Fail
				fail
			);
		},

		renderMap: function(location, tooltipContent) {

			self.busy(true);

			self.locationMap().show();

			var map	= new google.maps.Map(
				self.locationMap()[0],
				{
					zoom: 15,
					center: location.geometry.location,
					mapTypeId: google.maps.MapTypeId[self.options.mapType],
					disableDefaultUI: true
				}
			);

			var marker = new google.maps.Marker(
				{
					position: location.geometry.location,
					center	: location.geometry.location,
					title	: location.formatted_address,
					map		: map
				}
			);

			var infoWindow = new google.maps.InfoWindow({ content: tooltipContent });

			google.maps.event.addListener(map, "tilesloaded", function() {
				infoWindow.open(map, marker);
				self.busy(false);
			});
		},

		setLocation: function(location) {

			if (!location) return;

			self.locationResolved = true;

			self.lastResolvedLocation = location;

			self.locationInput()
				.val(location.formatted_address);

			self.locationLatitude()
				.val(location.geometry.location.lat());

			self.locationLongitude()
				.val(location.geometry.location.lng());

			self.renderMap(location, location.formatted_address);
		},

		removeLocation: function() {

			self.locationResolved = false;

			self.locationInput()
				.val('');

			self.locationLatitude()
				.val('');

			self.locationLongitude()
				.val('');

			self.locationMap().hide();
		},

		buildDataset: function(locations) {

			var dataset = $.map(locations, function(location){
				return {
					label: location.formatted_address,
					value: location.formatted_address,
					location: location
				};
			});

			return dataset;
		},

		retrieveSuggestions: function(request, response) {

			self.busy(true);

			var address = request.term,

				respondWith = function(locations) {
					response(locations);
					self.busy(false);
				};

			// User location
			if (address=="") {

				respondWith(self.userLocations || []);

			// Keyword search
			} else {

				self.getLocationByAddress(address, function(locations) {

					respondWith(self.buildDataset(locations));
				});
			}
		},

		suggestUserLocations: function() {

			if (self.hasGeolocation && self.userLocations) {

				self.removeLocation();

				self.locationInput()
					.autocomplete("search", "");
			}

			self.busy(false);
		},

		"{locationInput} blur": function() {

			// Give way to autocomplete
			setTimeout(function(){

				var address = $.trim(self.locationInput().val());

				// Location removal
				if (address=="") {

					self.removeLocation();

				// Unresolved location, reset to last resolved location
				} else if (self.locationResolved) {

					if (address != self.lastResolvedLocation.formatted_address) {

						self.setLocation(self.lastResolvedLocation);
					}
				} else {
					self.removeLocation();
				}

			}, 250);
		},

		"{autoDetectButton} click": function() {

			self.busy(true);

			if (self.hasGeolocation && !self.userLocations) {

				self.getUserLocations(self.suggestUserLocations);

			} else {

				self.suggestUserLocations();
			}
		}

	}}
);

EasyBlog.Controller(

	"Location.Map",

	{
		defaultOptions: {
			animation: 'drop',
			language: 'en',
			useStaticMap: false,
			disableMapsUI: true,

			// fitBounds = true will disobey zoom
			// single location with fitBounds = true will set zoom to max (by default from Google)
			// locations.length == 1 will set fitBounds = false unless explicitly specified
			// locations.length > 1 will set fitBounds = true unless explicitly specified
			zoom: 5,
			fitBounds: null,

			minZoom: null,
			maxZoom: null,

			// location in center has to be included in locations array
			// center will default to first object in locations
			// latitude and longitude always have precedence over address
			// {
			// 	"latitude": latitude,
			// 	"longitude": longitude,
			// 	"address": address
			// }
			center: null,

			// address & title are optional
			// latitude and longitude always have precedence over address
			// title will default to geocoded address
			// first object will open info window
			// [
			// 	{
			// 		"latitude": latitude,
			// 		"longitude": longitude,
			// 		"address": address,
			// 		"title": title
			// 	}
			// ]
			locations: [],

			// Default map type to be road map. Can be overriden.
			mapType: "ROADMAP",

			width: 500,
			height: 400,

			"{locationMap}": ".locationMap"
		}
	},

	function(self) { return {

		init: function() {
			self.mapLoaded = false;

			var mapReady = $.uid("ext");

			window[mapReady] = function() {
				$.___GoogleMaps.resolve();
			}

			if(self.options.useStaticMap == true) {
				var language = '&language=' + String(self.options.language);
				var dimension = '&size=' + String(self.options.width) + 'x' + String(self.options.height);
				var zoom = '&zoom=' + String(self.options.zoom);
				var center = '&center=' + String(parseFloat(self.options.locations[0].latitude).toFixed(6)) + ',' + String(parseFloat(self.options.locations[0].longitude).toFixed(6));
				var maptype = '&maptype=' + google.maps.MapTypeId[ self.options.mapType ];
				var markers = '&markers=';
				var url = 'https://maps.googleapis.com/maps/api/staticmap?sensor=false' + language + dimension;

				if(self.options.locations.length == 1) {
					markers += String(parseFloat(self.options.locations[0].latitude).toFixed(6)) + ',' + String(parseFloat(self.options.locations[0].longitude).toFixed(6));

					url += zoom + center + maptype + markers;
				} else {
					var temp = new Array();
					$.each(self.options.locations, function(i, location) {
						temp.push(String(parseFloat(location.latitude).toFixed(6)) + ',' + String(parseFloat(location.longitude).toFixed(6)));
					})
					markers += temp.join('|');

					url += markers + maptype;
				}

				self.locationMap().show().html('<img src="' + url + '" />');
				self.busy(false);
			} else {
				var mapReady = $.uid("ext");

				window[mapReady] = function() {
					$.___GoogleMaps.resolve();
				}

				if (!$.___GoogleMaps) {

					$.___GoogleMaps = $.Deferred();

					EasyBlog.require()
						.script(
							{prefetch: false},
							"https://maps.googleapis.com/maps/api/js?sensor=true&language=" + self.options.language + "&callback=" + mapReady
						);
				}

				// Defer instantiation of controller until Google Maps library is loaded.
				$.___GoogleMaps.done(function() {
					self._init();
				});
			}
		},

		_init: function() {

			// initialise fitBounds according to locations.length
			if(self.options.fitBounds === null) {
				if(self.options.locations.length == 1) {
					self.options.fitBounds = false;
				} else {
					self.options.fitBounds = true;
				}
			}

			// initialise disableMapsUI value to boolean
			self.options.disableMapsUI = Boolean(self.options.disableMapsUI);

			// initialise all location object
			self.locations = new Array();
			$.each(self.options.locations, function(i, location) {
			    if( location.latitude != 'null' && location.longitude != 'null' ) {
					self.locations.push(new google.maps.LatLng(location.latitude, location.longitude));
				}
			});

			if(self.locations.length > 0) {
				self.renderMap();
			}

			self.busy(false);
		},

		busy: function(isBusy) {
			self.locationMap().toggleClass("loading", isBusy);
		},

		renderMap: function() {
			self.busy(true);

			self.locationMap().show();

			var latlng;

			if(self.options.center) {
				latlng = new google.maps.LatLng(center.latitude, center.longitude);
			} else {
				latlng = self.locations[0];
			}

			self.map = new google.maps.Map(
				self.locationMap()[0],
				{
					zoom: parseInt( self.options.zoom ),
					minZoom: parseInt( self.options.minZoom ),
					maxZoom: parseInt( self.options.maxZoom ),
					center: latlng,
					mapTypeId: google.maps.MapTypeId[ self.options.mapType ],
					disableDefaultUI: self.options.disableMapsUI
				}
			);

			google.maps.event.addListener(self.map, "tilesloaded", function() {
				if(self.mapLoaded == false) {
					self.mapLoaded = true;
					self.loadLocations();
				}
			});
		},

		loadLocations: function() {
			self.bounds = new google.maps.LatLngBounds();
			self.infoWindow = new Array();

			var addLocations = function() {
				$.each(self.locations, function(i, location) {
					self.bounds.extend(location);
					var placeMarker = function() {
						self.addMarker(location, self.options.locations[i]);
					}

					setTimeout(placeMarker, 100 * ( i + 1 ) );
				});

				if(self.options.fitBounds) {
					self.map.fitBounds(self.bounds);
				}
			};

			setTimeout(addLocations, 500);
		},

		addMarker: function(location, info) {
			if (!location) return;

			var marker = new google.maps.Marker(
				{
					position: location,
					map: self.map
				}
			);

			marker.setAnimation(google.maps.Animation.DROP);
			self.addInfoWindow(marker, info)
		},

		addInfoWindow: function(marker, info) {
			var content = info.content;

			if(!content) {
				content = info.address;
			}

			var infoWindow = new google.maps.InfoWindow();
			infoWindow.setContent(content);
			self.infoWindow.push(infoWindow);

			if(self.options.locations.length > 1) {
				google.maps.event.addListener(marker, 'click', function() {
					$.each(self.infoWindow, function(i, item) {
						item.close();
					});
					infoWindow.open(self.map, marker);
				});
			} else {
				google.maps.event.addListener(marker, 'click', function() {
					infoWindow.open(self.map, marker);
				});

				infoWindow.open(self.map, marker);
			}

			// custom hack for postmap module
			if(info.ratingid) {
				google.maps.event.addListener(infoWindow, 'domready', function() {
					$.each(info.ratingid, function(i, rid) {
						eblog.ratings.setup( 'ebpostmap_' + rid + '-ratings' , true , 'entry' );
						$('#ebpostmap_' + rid + '-ratings').removeClass('ui-state-disabled');
						$('#ebpostmap_' + rid + '-ratings-form').find('.blog-rating-text').hide();
						$('#ebpostmap_' + rid + '-ratings .ratings-value').hide();
					})
				});
			}
		}
	}}
);

module.resolve();

// controller: end

	});
// require: end
});

EasyBlog.module("media", function($){

	var module = this;


	var htmlentity = function(str) {

		return $("<div>").text(str)
					.html()
					.replace(/&/g, '&amp;')
					.replace(/"/g, '&quot;')
					.replace(/'/g, '&apos;');
	}

	var $Media, $Library, $Browser, $Uploader, DS;

	//
	// 1. Create media manager controller.
	//
	EasyBlog.Controller("Media",

		{
			defaultOptions: {

				debug: {
					logging: EasyBlog.debug,

					itemVisibility: false,

					delayConfiguration: 0,
					delayCommon: 0,
					delayBrowser: 0,
					delayUploader: 0,
					delayEditor: 0
				},

				ui: "#EasyBlogMediaManagerUI",


				overlay: {
					background: "black",
					opacity: 0
				},

				modal: {
					size: 0.9
				},

				recentActivities: {
					hideAfter: 3000
				},

				"{modalGroup}"    : ".mediaModalGroup",
				"{modal}"         : ".mediaModal",

				"{loaderModal}"   : ".loaderModal",
				"{uploaderModal}" : ".uploaderModal",
				"{browserModal}"  : ".browserModal",
				"{editorModal}"   : ".editorModal",

				"{modalContent}"  : ".modalContent",

				"{overlay}": ".media-overlay",

				"{modalDashboardButton}": ".dashboardButton",

				"{assetItem}": ".assetItem"
			}
		},

		function(self) { return {

			console: function(method, args) {

				if (!self.options.debug.logging) return;

				var console = window.console;

				if (!console) return;

				var method = console[method];

				if (!method) return;

				// Normal browsers
				if (method.apply) {
					method.apply(console, args);
				// IE
				} else {
					method(args.join(" "));
				}
			},

			assets: {},

			getAsset: function(name) {

				if (self.assets[name]===undefined) {

					var asset = self.assets[name] = $.Deferred();

					asset
						.done(function(){

							self.assetItem(".asset-type-"+name)
								.removeClass("loading done failed")
								.addClass("done");
						})
						.fail(function(){
							self.assetItem(".asset-type-"+name)
								.removeClass("loading done failed")
								.addClass("fail");
						});
				}

				return self.assets[name];
			},

			createAsset: function(name, factory, delay) {

				var asset = self.getAsset(name);
					asset.factory = factory;

				setTimeout(function(){
					asset.factory && asset.factory(asset);
				}, delay);

				return asset;
			},

			init: function() {

				// Globals
				$Media = self;

				self.IE = (function(){

				    var undef,
				        v = 3,
				        div = document.createElement('div'),
				        all = div.getElementsByTagName('i');

				    while (
				        div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->',
				        all[0]
				    );

				    return v > 4 ? v : undef;

				}());


				if( typeof( tinyMCE ) != 'undefined' )
				{

					// Caret position fix
					if (tinyMCE && tinyMCE.isIE && self.IE==9) {

						// Wait for TinyMCE to be ready
						var waitForTinyMCE = setInterval(function(){

							var editor = tinyMCE.editors.write_content;

							if (!editor) return;

							var events = "keydown.mediaManager mousedown.mediaManager focus.mediaManager";

							$(editor.contentWindow)
								// Just in case it was binded
								.off(events)
								.on(events, function(){
									self.bookmark = {
										element: editor.selection.getEnd(),
										range: editor.selection.getBookmark(1).rng
									}
								});

							clearInterval(waitForTinyMCE);

						}, 500);
					}
				}

				// Remember the document body's original overflow property
				// Used with .hide();
				self.originalBodyOverflow = $("body").css("overflow");

				// When "module/configuration" gets resolved,
				// file & folder indexing kicks in immediately
				// without waiting for the other assets to resolve.
				self.createAsset(
					"configuration",
					function(asset) {
						EasyBlog.module("media/configuration")
							.done(function() {
								var options = this;
								self.initialize(this);
								asset.resolve();
							})
							.fail(function(){
								asset.reject();
							});
					},
					self.options.debug.delayConfiguration
				);

				// Stylesheet & navigation is given priority because it needs to be
				// ready before uploader can initialize. And we need
				// uploader to be up & ready as fast as possible.
				self.createAsset(
					"common",
					function(asset) {
						EasyBlog.require()
							.script(
								"media/navigation"
							)
							.view(
								"media/recent.item",

								// Browser
								"media/browser",
								"media/browser.item-group",
								"media/browser.item",
								"media/browser.tree-item-group",
								"media/browser.tree-item",
								"media/browser.pagination-page",

								// Uploader
							    "media/browser.uploader",
							    "media/browser.uploader.item",

							    // Editor
								"media/editor",
								"media/editor.viewport",

								// Navigation
								"media/navigation.item",
								"media/navigation.itemgroup"
							)
							.language(
								"COM_EASYBLOG_MM_UNABLE_TO_FIND_EXPORTER",
								"COM_EASYBLOG_MM_GETTING_IMAGE_SIZES",
								"COM_EASYBLOG_MM_UNABLE_TO_RETRIEVE_VARIATIONS",
								"COM_EASYBLOG_MM_ITEM_INSERTED",
								"COM_EASYBLOG_MM_UPLOADING",
								"COM_EASYBLOG_MM_UPLOADING_STATE",
								"COM_EASYBLOG_MM_UPLOADING_PENDING",
								"COM_EASYBLOG_MM_UPLOAD_COMPLETE",
								"COM_EASYBLOG_MM_UPLOAD_PREPARING",
								"COM_EASYBLOG_MM_UPLOAD_UNABLE_PARSE_RESPONSE",
								"COM_EASYBLOG_MM_UPLOADING_LEFT",
								"COM_EASYBLOG_MM_CONFIRM_DELETE_ITEM",
								"COM_EASYBLOG_MM_CANCEL_BUTTON",
								"COM_EASYBLOG_MM_YES_BUTTON",
								"COM_EASYBLOG_MM_ITEM_DELETE_CONFIRMATION"
							)
							.done(function() {
								asset.resolve();
							})
							.fail(function(){
								asset.reject();
							});
					},
					self.options.debug.delayCommon
				);

				// Load all uploader dependencies NOW so we can shave off
				// that extra 1-2 seconds that was used to wait for
				// "media/uploader" module to resolve.
				self.createAsset(
					"uploader",
					function(asset) {
						$.when(
							self.getAsset("configuration"),
							self.getAsset("common"),
							EasyBlog.require().script("media/uploader").done()
						)
						.done(function() {
							var modal = self.createModal("uploader");
							$Uploader = modal.controller = new EasyBlog.Controller.Media.Uploader(modal.element, self.options.uploader);
							asset.resolve();
						})
						.fail(function(){
							asset.reject();
						});
					},
					self.options.debug.delayUploader
				);

				// Browser
				self.createAsset(
					"browser",
					function(asset) {
						$.when(
							self.getAsset("configuration"),
							self.getAsset("common"),
							EasyBlog.require().script("media/browser").done()
						)
						.done(function(){
							var modal = self.createModal("browser");
							$Browser = modal.controller = new EasyBlog.Controller.Media.Browser(modal.element, self.options.browser);
							asset.resolve();
						})
						.fail(function(){
							asset.reject();
						});
					},
					self.options.debug.delayBrowser
				);

				// Editor
				self.createAsset(
					"editor",
					function(asset) {
						$.when(
							self.getAsset("browser"),
							EasyBlog.require().script("media/editor").done()
						)
						.done(function(){
							var modal = self.createModal("editor");
							modal.controller = new EasyBlog.Controller.Media.Editor(modal.element, self.options.editor);
							asset.resolve();
						})
						.fail(function(){
							asset.reject();
						});
					},
					self.options.debug.delayEditor
				);


				// Debounce self.setLayout(). Debouncing setLayout() is only useful when
				// media explorer is doing resource-intensive thumbnail resizing.
				self.setLayout = $.debounce(self._setLayout, 200);
			},

			initialize: function(options) {

				// Inject subcontroller options with back-reference to media
				var media = {controller: {media: self}},
					options = $.extend(true, options, {browser: media, uploader: media, library: media, editor: media, exporter: media});

				// Reload configuration
				self.update(options);

				// Globals
				DS = options.directorySeparator;

				// Render media manager UI
				var UI = $(self.options.ui);
				self.element.append(UI.html());
				UI.remove();

				// Set up overlay
				self.overlay()
					.css(self.options.overlay);

				// Set up loader
				self.loader = new EasyBlog.Controller.Media.Loader(self.createModal("loader"), {controller: {media: self}});

				// Implement media library
				self.element
					.implement(
						EasyBlog.Controller.Media.Library,
						self.options.library,
						function(){

						}
					);

				// Implement media exporter
				self.element
					.implement(
						EasyBlog.Controller.Media.Exporter,
						self.options.exporter
					);

				module.resolve();
			},

			// This will be replaced with a debounced function later on.
			setLayout: function() {

				self._setLayout();
			},

			// We are retaining direct access to non-debounced setLayout()
			// because we might need it sometimes.
			_setLayout: function() {

				self.layout = (self.element.hasClass("active")) ? $.uid() : null;

				if (self.layout) {
					self.setModalLayout();
				}

				return self.layout;
			},

			modals: {},

			createModal: function(name) {

				var element = self.modal("."+name+"Modal");

				if (element.length < 1) {
					element = $('<div class="mediaModal"></div>').addClass(name+"Modal").appendTo(self.modalGroup());
				}

				return self.modals[name] = {
					name: name,
					element: element
				}
			},

			activateModal: function(name, args) {

				if (self.modals[name]===undefined) {

					self.loader
						.when(self.assets[name])
						.done(function(){
							setTimeout(function(){
								self.activateModal(name, args);
							}, 1000);
						});

					return;
				}

				// If modal to activate is current modal, skip.
				if (self.currentModal===name) return true;

				var modal = self.modals[name];

				if (!modal) return false;

				self.deactivateModal(self.currentModal);

				self.currentModal = name;

				// This will also set modal layout
				self.show();

				// Trigger "modalActivate event"
				var controller = modal.controller;

				if (controller) {
					controller.trigger("modalActivate", args);
				}

				return true;
			},

			deactivateModal: function(name, args) {

				var modal = self.modals[name];

				if (!modal) return false;

				// Trigger "modalActivate event"
				var controller = modal.controller;

				if (controller) {

					try {
						controller.trigger("modalDeactivate", args);
					} catch (e) {
						console.error(e);
					}
				}

				modal.element.removeClass("active");

				self.currentModal = undefined;

				return true;
			},

			setModalLayout: function() {

				clearTimeout(self.setModalLayout.task);

				var modal = self.modals[self.currentModal];

				// Skip if modal does not exist, or no visible modal.
				if (!modal) return;

				// If no layout has been set, set it first.
				// setModalLayout will eventually be called again.
				if (!self.layout) return self._setLayout();

				// Show the modal
				modal.element.addClass("active");

				// Set the layout modal
				var controller = modal.controller;

				if (controller && $.isFunction(controller.setLayout)) {

					// This fixes an issue where the modal requires
					// more time than expected to paint on the screen.
					// Whenever the modal is painted on the screen,
					// its top/left is never 0.

					var task = function() {

						var offset = controller.element.offset();

						if (offset.top===0 || offset.left===0) {
							self.setModalLayout.task = setTimeout(task, 50);
						} else {
							controller.setLayout();
						}
					};

					task();
				}
			},

			show: function() {

				// This prevents scrolling of page body
				// $("body").css("overflow", "hidden");

				self.element
					.addClass("active");

				self._setLayout();

				// Conflict with certain mootools version
				// self.trigger("show");

				self.trigger("showModal");
			},

			hide: function() {

				self.element
					.removeClass("active");

				self.deactivateModal(self.currentModal);

				// $("body").css("overflow", self.originalBodyOverflow);

				// Conflict with certain mootools version
				// self.trigger("hide");

				self.trigger("hideModal");
			},

			"{overlay} click": function() {
				self.hide();
			},

			"{window} resize": function() {
				self.setLayout();
			},

			"{modalDashboardButton} click": function(el, event) {

				// #debug:start
				if (event.shiftKey) return self.console("dir", [self]);
				// #debug:end

				self.hide();
			},

			// Sugar methods
			upload: function() { self.activateModal("uploader", arguments); },
			browse: function() { self.activateModal("browser", arguments); },
			edit  : function() { self.activateModal("editor", arguments); }
		}}
	);

	EasyBlog.Controller("Media.Loader",

		{
			defaultOptions: {

			}
		},

		function(self) { return {

			init: function() {

			},

			when: function() {

				self.media.activateModal("loader");

				var queue = $.when.apply(null, arguments),

					onQueueDone = queue.done; // Keep an original copy of done method

					queue.id = $.uid();

				queue.done = function(callback) {

					onQueueDone(function(){

						// If we are still waiting for it
						if (self.currentQueueId==queue.id) {

							// then execute the callback
							callback && callback();
						}
					});
				}

				self.currentQueueId = queue.id;

				return queue;
			},

			"{self} hide": function() {

				self.currentQueueId = null;
			}
		}}
	);

	EasyBlog.Controller("Media.Prompt",

		{
			defaultOptions: {

				"{dialog}": ".modalPromptDialog",
				"{cancelButton}": ".promptCancelButton"
			}
		},

		function(self){ return {

			init: function() {

			},

			get: function(name) {

				var dialog = self.dialog("." + name);

				return self.instantiate(dialog);
			},

			instantiate: function(dialog) {

				var methods = {

					element: dialog,

					show: function() {

						dialog.addClass("active");

						self.element.addClass("active");

						return methods;
					},

					hide: function() {

						dialog.removeClass("active");

						self.element.removeClass("active");

						return methods;
					},

					state: function(state) {

						var lastState = dialog.data("lastPromptState");

						if (state===undefined) {

							return lastState;
						}

						var getStateElement = function(state) {
								return dialog.find(".promptState" + ".state-" + state);
							},
							currentState = getStateElement(state),
							lastState = getStateElement(lastState);

						if (currentState.length < 1) {
							return;
						}

						lastState.removeClass("active");

						currentState.addClass("active");

						dialog.data("lastPromptState", state);

						return methods;
					}
				}

				return methods;
			},

			hideAll: function() {

				self.dialog().removeClass("active");

				self.element.removeClass("active");
			},

			"{cancelButton} click": function() {

				self.hideAll();
			}

		}}
	);

	EasyBlog.Controller("Media.Library",

		{
			defaultOptions: {
				// options for managing indexing of metas here

				places: [],

				place: {
					files: {},
					acl: {
						canCreateFolder: false,
						canUploadItem: false,
						canRenameItem: false,
						canRemoveItem: false,
						canCreateVariation: false,
						canDeleteVariation: false
					},
					populateImmediately: false
				}
			}
		},

		function(self) { return {

			init: function() {

				// Register itself to media
				self.media.library = self;

				$.each(self.options.places, function(i, place) {
					self.addPlace(place);
				});
			},

			places: {},

			getPlace: function(place) {

				// Skip going through all the tests below.
				if (!place) return;

				// Place (test using acl property)
				if (place.acl) {
					return place;
				}

				// Place ID or Key
				if (typeof place==="string") {
					return self.places[place.split("|")[0]];
				}
			},

			addPlace: function(place) {

				var place = self.places[place.id] = $.extend(
						{
							tasks: $.Threads({threadLimit: 1}),

							ready: $.Deferred(),

							baseFolder: function() {
								return self.getMeta(place.id + "|" + self.media.options.directorySeparator);
							}
						},
						self.options.place,
						place
					);

					place.done = place.ready.done;

					place.fail = place.ready.fail;

					place.always = place.ready.always;


				var importJSON = function(data) {

					// When tree is not populated, it is an empty object.
					if ($.isEmptyObject(data)) return;

					// When tree is a string, it might be json string.
					if (typeof data === "string") {

						// Try to eval it.
						try { data = $.parseJSON(data); } catch(e) {}
					}

					return (typeof data === "object") ? data : undefined;
				}

				// Import initial file tree
				place.files = importJSON(place.files);

				if (place.files) {

					place.tasks.add(function(){
						self.importMeta(place.files);
						place.ready.resolve(place);
					});
				};

				place.populate = function() {

					if (place.populate.task!==undefined) return place.populate.task;

					place.populate.task = $.Deferred();

					// JomSocial & Flickr goes directly to getFileTree,
					// User & Shared folder getFolderTree first.
					place.populate[
						(/easysocial|jomsocial|flickr/.test(place.id) || place.files) ? "getFileTree" : "getFolderTree"
					]();

					return place.populate.task;
				};

				place.populate.getFolderTree = function() {

					// Get final folder tree
					return self.getRemoteMeta({place: place.id, foldersOnly: 1})
								.done(function(meta){

									// Import the folder tree
									place.tasks.add(function(){

										// #debug:start
										// var profiler = "Importing final folder tree for " + place.id;
										// self.media.console("time", [profiler]);
										// #debug:end

										self.importMeta(meta);
										place.ready.resolve(place);

										// #debug:start
										// self.media.console("timeEnd", [profiler]);
										// #debug:end
									});

									place.populate.getFileTree();
								})
								.fail(function(){

									place.populate.task.reject(place);
									delete place.populate.task;

									place.ready.reject(place);
								});
				};

				place.populate.getFileTree = function() {

					// Get final file tree
					return self.getRemoteMeta({place: place.id})
								.done(function(meta){

									// Import the folder tree
									place.tasks.add(function(){

										// #debug:start
										// var profiler = "Importing final file tree for " + place.id;
										// self.media.console("time", [profiler]);
										// #debug:end

										self.importMeta(meta);
										place.ready.resolve(place);
										place.populate.task.resolve(place);

										// #debug:start
										// self.media.console("timeEnd", [profiler]);
										// #debug:end
									});
								})
								.fail(function(){

									place.populate.task.reject(place);
									delete place.populate.task;
								});
				};

				if (place.populateImmediately) {
					place.populate();
				}

				return place;
			},

			meta: {},

			metadata: {}, // Extended data attribute for meta

			isMeta: function(meta) {

				return !(meta===undefined || !$.isPlainObject(meta) || $.isEmptyObject(meta));
			},

			get: function(key) {

				if (!self.meta.hasOwnProperty(key)) return;

				// Create a clone of the meta, keeping the original intact
				var meta = $.extend({}, self.meta[key]);

					// Extend meta with additional data attributes
					meta.data = self.metadata[key];

				return meta;
			},

			getKey: function(meta) {
				return (meta) ? meta.place + "|" + meta.path : null;
			},

			getParentKey: function(meta) {

				if (!meta) return;

				var key   = (typeof meta==="string") ? meta : (meta.key || self.getKey(meta)),
					start = key.indexOf(DS),
					end   = key.lastIndexOf(DS);

				return (end===key.length-1) ? undefined : key.substring(0, end + ((start===end) ? 1 : 0));
			},

			getMeta: function(meta) {

				// Skip going through all the tests below.
				if (!meta) return;

				// Meta
				if (self.isMeta(meta)) {

					// Try to get the updated meta,
					// if it doesn't work, just return the existing meta.
					return self.get(self.getKey(meta)) || meta;
				}

				// Key
				if (typeof meta==="string") {
					return self.get(meta); // meta == key
				}
			},

			getRemoteMeta: function(options) {

				var task = $.Deferred();
					task.retry = 0;

				var defaultOptions = {
						path: self.media.options.directorySeparator,
						retry: 3,
						retryAfter: 1000,
						variation: 0,
						foldersOnly: 0
					},
					options = $.extend(defaultOptions, options);

				// Don't do anything if place is not given
				if (options.place===undefined) {
					return task.rejectWith(task, "Error: Place not given!");
				}

				var loadRemoteMeta = (function() {

					task.loader =
						EasyBlog.ajax(
							"site.views.media.getMeta",
							options,
							{
								success: function(data) {
									task.resolveWith(task, arguments);
								},

								// Server-side error
								fail: function() {
									task.rejectWith(task, arguments);
								},

								// Network error
								error: function() {
									task.retry++;
									if (task.retry < options.retry) {
										loadRemoteMeta();
									} else {
										task.rejectWith(task, arguments);
									}
								}
							}
						);

					return arguments.callee;
				})();

				return task;
			},

			getMetaVariations: function(meta) {

				var meta = self.getMeta(meta);

				if (meta===undefined) return;

				return self.getRemoteMeta({place: meta.place, path: meta.path, variation: 1})
						   .done(function(meta){
						   		self.addMeta(meta);
						    });
			},

			removeMetaVariation: function(meta, variationName) {

				var meta = self.getMeta(meta);

				if (meta===undefined) return;

				if (meta.variations===undefined) return;

				var variation;

				for (var i=0; i<meta.variations.length; i++) {

					if (meta.variations[i].name===variationName) {

						variation = meta.variations[i];

						meta.variations.splice(i, 1);

						break;
					}
				};

				return variation;
			},

			addMeta: function(meta) {

				var key = self.getKey(meta),
					existingMeta = self.getMeta(key);

				// If the meta passed in is the meta that we already have,
				// just return the existing meta.
				if (existingMeta && meta.hash===existingMeta.hash) return existingMeta;

				// Create meta
				meta.key   = key;
				meta.hash  = $.uid();
				meta.group = (meta.type=="folder") ? "folders" : "files";

				// Store parent key if this is not the top level folder
				meta.parentKey = self.getParentKey(meta);

				// Add friendly path
				var place = self.getPlace(meta.place);

				meta.friendlyPath =
					(meta.path===DS) ?
						place.title :
						place.title + meta.path.substring(meta.path.indexOf(DS), meta.path.length);

				// Store it to our meta library
				self.meta[key] = meta;

				// Create metadata
				var data = meta.data = (self.metadata[key] || (self.metadata[key] = $.eventable({})));

				// Additional metadata for folder type
				if (meta.type=="folder") {

					data.files   = data.files   || {};
					data.folders = data.folders || {};
					data.views   = data.views   || self.createMetaView(meta);
				}

				// For new meta, add to parent meta's view.
				if (!existingMeta) {

					var parentMeta = self.getMeta(meta.parentKey);

					if (parentMeta) {
						parentMeta.data.views.addMeta(meta);
					}

				// For existing meta, fire update event.
				} else {

					// Ensure events don't slow down adding of large list of metas
					setTimeout(function(){ meta.data.fire("updated", meta); }, 0);
				}

				return meta;
			},

			removeMeta: function(meta) {

				var meta = self.getMeta(meta);

				if (meta===undefined) return;

				if (meta.type==="folder") {

					var folders = meta.data.folders,
						files = meta.data.files;

					for (key in folders) {
						self.removeMeta(key);
					}

					for (key in files) {
						self.removeMeta(key);
					}
				}

				// If this is not base folder
				if (meta.parentKey) {

					// Remove meta from parent
					var parentMeta = self.getMeta(meta.parentKey);

					if (parentMeta) {
						parentMeta.data.views.removeMeta(meta);
					}

					delete self.meta[meta.key];

					setTimeout(function(){ meta.data.fire("removed", meta); }, 0);
				}

				return meta;
			},

			removeRemoteMeta: function(meta) {

				var task = $.Deferred();

				var meta = self.getMeta(meta);

				if (meta===undefined) {
					return task.rejectWith(task, "The file does not exist in the media library.");
				}

				task.loader =
					EasyBlog.ajax(
						"site.views.media.delete",
						{
							place: meta.place,
							path: meta.path
						},
						{
							success: function() {

								self.removeMeta(meta);

								// Return the meta which is no longer in the library
								task.resolveWith(task, [meta]);
							},

							// Server-side error
							fail: function() {

								task.rejectWith(task, arguments);
							},

							// Network error
							error: function(xhr, errorStatus) {

								task.rejectWith(task, [errorStatus])
							}
						});

				return task;
			},

			createFolder: function(meta, name) {

				var task = $.Deferred();

				// Don't do anything if parent meta not found.
				var meta = self.getMeta(meta),
					place = meta.place;

				if (meta===undefined || meta.type!=="folder") {
					return task.rejectWith(task, "Parent folder was not found.");
				}

				task.loader =
					EasyBlog.ajax(
						"site.views.media.createFolder",
						{
							place: place,
							path : meta.path + DS + name
						},
						{
							success: function(meta) {

								// #hack: Restore place property
								meta.place = place;
								var meta = self.addMeta(meta);

								task.resolveWith(task, [meta]);
							},

							// Server-side error
							fail: function() {

								task.rejectWith(task, arguments);
							},

							// Network error
							error: function(xhr, errorStatus) {

								task.rejectWith(task, [errorStatus])
							}
						}
					);

				return task;
			},

			importMeta: function(meta, recursive) {

				if (meta===undefined) return;

				if (meta.type!=="folder") return;

				if (recursive===undefined) recursive = true;

				// Remove the contents property
				// before adding into our meta library.
				var contents = meta.contents,
					length = contents.length;
					delete meta.contents;

					// Temporary hack
					contents.reverse();

				// If the folder meta was created before,
				// the update event is triggered by the parent.
				var folder = self.addMeta(meta),
					 data  = folder.data,
					_data  = {files: {}, folders: {}};

				var i = 0;

				while (i < length) {

					var meta  = self.addMeta(contents[i]),
						key   = meta.key,
						group = meta.group;

					_data[group][key] = delete data[group][key];

					i++;
				}

				for (key in data.files) {
					self.removeMeta(key);
				}

				for (key in data.folders) {
					self.removeMeta(key);
				}

				// Update to the new set of data
				data.folders = _data.folders;
				data.files   = _data.files;

				if (recursive) {
					for (key in data.folders) {
						self.importMeta(self.getMeta(key), recursive);
					}
				}

				return folder;
			},

			createMetaView: function(meta) {

				var view = {

					meta: meta,

					addMeta: function(meta) {

						for (mode in view.modes) {
							var viewMap = view.modes[mode][meta.group];
							viewMap && viewMap.add(meta);
						}
					},

					removeMeta: function(meta) {

						for (mode in view.modes) {
							var viewMap = view.modes[mode][meta.group];

							viewMap && viewMap.remove(meta);
						}
					},

					create: function(options) {

						var defaultOptions = {
							from: 0,
							to: 1024,
							mode: "dateModified",
							group: "files"
						};

						// Create monitor
						var monitor = $.Callbacks("unique memory");

						$.extend(

							monitor,

							defaultOptions,

							options,

							{
								uid: $.uid(),

								select: function(options) {

									// Update options
									$.extend(monitor, options);

									// Deregister from the previous map
									if (monitor.map) {
										delete monitor.map.monitors[monitor.uid];
									}

									monitor.map = view.modes[monitor.mode][monitor.group];

									// Register to the new map
									monitor.map.monitors[monitor.uid] = monitor;

									// #debug:start
									// self.media.console("log", ["Monitoring " + monitor.group + " in " + meta.key, monitor]);
									// #debug:end

									return monitor.refresh();
								},

								updated: monitor.add,

								refresh: function() {

									return monitor.fire(monitor.map.slice(monitor.from, monitor.to));
								},

								destroy: function() {

									monitor.disable();

									if (monitor.map) {

										delete monitor.map.monitors[monitor.uid];
									}

									return monitor;
								}
							}
						);

						return monitor.select();
					}
				};

				// Extend view with view modes
				self.createViewModes(view);

				return view;
			},

			createViewModes: function(view) {

				// TODO: Make this extensible for other types of sort maps.
				view.modes = {
					dateModified: {
						folders: self.createViewMap(view, true), // Monkey patch
						files: self.createViewMap(view)
					}
				};

				return view;
			},

			// TODO: When createViewModes is extensible,
			//       this is only part of dateModified mode.
			createViewMap: function(view, folderGroup) {

				var map = $.extend([], {

					task: $.Threads({threadLimit: 1}),

					affectedIndex: [],

					add: function(meta) {

						map.task.add(function() {
							// TODO: Proper date modified insertion
							map.unshift(meta.key);
							map.affectedIndex.push(0);

							// Monkey patch to show folder tree in alphabetical order
							if (folderGroup) {
								map.sort().reverse();
							}

							map.refreshMonitor();
						});
					},

					remove: function(meta) {

						map.task.add(function() {

							var key = meta.key,
								i = map.length,
								position;

							while (i--) {
								if (map[i]===key) {
									position = i;
									break;
								}
							}

							if (position===undefined) return;

							map.splice(i, 1);
							map.affectedIndex.push(i);

							map.refreshMonitor();
						});
					},

					monitors: {},

					refreshMonitor: function() {

						clearTimeout(map.refreshMonitor.timer);

						map.refreshMonitor.timer = setTimeout(function(){

							var affectedIndex = map.affectedIndex;
							map.affectedIndex = [];

							map.task.add(function(){

								var l = affectedIndex.length,
									i = 0;

								for (id in map.monitors) {

									var monitor = map.monitors[id],
										from = monitor.from,
										to = monitor.to,
										i;

									for (i=0; i<l; i++) {

										var a = affectedIndex[i];

										if (a >= from || a <= to) {

											monitor.refresh();
											break;
										}
									}
								}
							});

						}, 250);
					}
				});

				return map;
			},

			search: function(keyword) {

				// TODO: Also remove DS from keyword
				if ($.trim(keyword)==="") return [];

				// #debug:start
				// var profiler = "Searching library using keyword '" + keyword + "'";
				// self.media.console("time", [profiler]);
				// #debug:end

				var keyword = keyword.toLowerCase();

				var results = self.createMetaView({type: "search"});

				for (key in self.meta) {

					var parts = key.split("|"),
						place = parts[0],
						path = parts[1],
						meta = self.meta[key];

					if (/easysocial|jomsocial|flickr/.test(place)) {
						// Note: This means that album name keyword cannot be matched.
						path = meta.title || "";
					}

					if (path.toLowerCase().match(keyword)) {
						results.addMeta(meta);
					}
				}

				// #debug:start
				// self.media.console("timeEnd", [profiler]);
				// #debug:end

				return results;
			}
		}}
	);

	EasyBlog.Controller("Media.Exporter",

		{
			defaultOptions: {

				view: {
					recentItem: "media/recent.item"
				},

				// Recent inserts
				"{recentActivities}"            : ".recentActivities",
				"{recentActivitiesDialog}"      : ".recentActivities .modalPromptDialog",
				"{hideRecentActivitiesButton}"  : ".recentActivities .promptHideButton",
				"{dashboardButton}"             : ".recentActivities .dashboardButton",
				"{recentItemGroup}": ".recentItemGroup",
				"{recentItem}"     : ".recentItem"
			}
		},

		function(self){ return {

			init: function() {

				$Media.exporter = self;

				$Media.insert = self.insert;
			},

			handler: {},

			showDialog: function() {

				self.recentActivitiesDialog()
					.addClass("active");

				self.recentActivities()
					.css({top: 0, opacity: 1})
					.addClass("active");
			},

			hideDialog: function() {

				self.recentActivities()
					.animate({top: "-=50px", opacity: 0}, {duration: 250, complete: function() {

							self.recentActivities()
								.removeClass("active");

							self.recentActivitiesDialog()
								.removeClass("active");
						}
					});
			},

			"{dashboardButton} click": function() {
				self.hideDialog();
			},

			"{hideRecentActivitiesButton} click": function() {

				self.hideDialog();
			},

			// Recent inserts
			insert: function(item, settings) {

				var meta = $Media.library.getMeta(item);

				if (meta===undefined) return;

				var task = self.create(meta.type, meta.key, settings);

				// Show dialog
				self.showDialog();

				// Create recent item
				task.recentItem =
					self.view.recentItem({meta: meta})
						.addClass("loading")
						.css({opacity: 0})
						.prependTo(self.recentItemGroup())
						.animate({opacity: 1}, {duration: 500, complete: function() {

							task
								.done(function(html) {

									if (html==="") {
										task.recentItem
											.removeClass("loading done")
											.addClass("error")
											.find(".itemProgress")
											.html($.language("COM_EASYBLOG_MM_UNABLE_TO_EXPORT_ITEM"));
									}

									if( typeof( tinyMCE ) != 'undefined' )
									{

										// If you are TinyMCE/JCE on IE
										if (tinyMCE && tinyMCE.isIE && $Media.IE==9) {

											// Get back the bookmark we stored just now
											var bookmark = $Media.bookmark;

											if (bookmark) {

												var editor = tinyMCE.editors.write_content;

												editor.selection.moveToBookmark({rng: bookmark.range});

												editor.execCommand('mceInsertContent', false, html);

											// Just in case we did not get the bookmark
											} else {

												EasyBlog.dashboard.editor.insert(html);
											}

										} else {

											EasyBlog.dashboard.editor.insert(html);
										}
									}
									else
									{
										EasyBlog.dashboard.editor.insert(html);
									}

									task.recentItem
										.removeClass("loading failed")
										.addClass("done")
										.find(".itemProgress")
										.html($.language("COM_EASYBLOG_MM_ITEM_INSERTED"));

									if ($Media.options.recentActivities.hideAfter > 0) {

										setTimeout(function(){ self.hideDialog(); }, $Media.options.recentActivities.hideAfter);
									}
								})
								.progress(function(message){

									if (task.recentItem.hasClass("done")) return;

									task.recentItem
										.find(".itemProgress")
										.html(message);
								})
								.fail(function(message){

									task.recentItem
										.removeClass("loading done")
										.addClass("error")
										.find(".itemProgress")
										.html(message);
								});
						}
					});

				return task;
			},

			create: function(type, key, settings) {

				var handler = self.handler[type],

					task = $.Deferred();

				if (handler===undefined) {

					var Exporter = EasyBlog.Controller.Media.Exporter[$.String.capitalize(type)];

					if (Exporter===undefined) {

						task.reject($.language("COM_EASYBLOG_MM_UNABLE_TO_FIND_EXPORTER"));

						return task;
					}

					handler = self.handler[type] = new Exporter(self.element, $.extend({}, {settings: self.options[type], controller: { media: self.media }}));
				}

				handler.create(task, key, settings);

				return task;
			}

		}}
	);

	EasyBlog.Controller("Media.Exporter.File",

		{
			defaultOptions: {

				settings: {
					title: "",
					target: "_self",
					content: ""
				}
			}
		},

		function(self){ return {

			init: function() {

			},

			create: function(task, key, customSettings) {

				var settings = $.extend({}, self.options.settings, customSettings),

					meta = self.media.library.getMeta(key),

					link =
						$(document.createElement("A"))
							.attr({
								title: settings.title,
								target: settings.target,
								href: meta.url
							})
							.html((settings.content) ? settings.content : meta.title);

				task.resolve(link.toHTML());

				return task;
			}

		}}
	);

	EasyBlog.Controller("Media.Exporter.Folder",

		{
			defaultOptions: {

				settings: {

				}
			}
		},

		function(self){ return {

			init: function() {

			},

			create: function(task, key, customSettings) {

				var settings = $.extend({}, self.options.settings, customSettings),

					meta = self.media.library.getMeta(key),

					embedType = (meta.place=="jomsocial" || meta.place == 'easysocial') ? "album" : "gallery";

					settings.file = meta.path;

					settings.place = meta.place;

				var snippet = "[embed=" + embedType + "]" + JSON.stringify(settings) + "[/embed]";

				task.resolve(snippet);

				return task;
			}

		}}
	);

	EasyBlog.Controller("Media.Exporter.Image",

		{
			defaultOptions: {

				settings: {
					zoom: null, // variationName
					caption: null,
					lightbox: false,
					enforceDimension: false,
					enforceWidth: null,
					enforceHeight: null,
					variation: null, // variationName
					defaultVariation: "thumbnail",
					defaultZoom: "original"
				}
			}
		},

		function(self){ return {

			init: function() {

			},

			create: function(task, key, customSettings) {

				var settings = $.extend({}, self.options.settings, customSettings),

					meta = self.media.library.getMeta(key),

					image = $(new Image());

					// Convert caption into html entities,
					// escape quotes and other special characters.
					var title = htmlentity(meta.title);

					image.attr({
						src: meta.thumbnail.url,
						alt: title,
						title: title
					});

				var resolve = function() {

						task.resolve(image.toHTML());
					},

					process = function() {

						var variations = {};

						$.each(meta.variations, function(i, variation) {
							variations[variation.name] = variation;
						});


						// Use provided variation, else use default variation, or the first variation on the list.
						var variation = variations[settings.variation] ||
										variations[settings.defaultVariation] ||
										meta.variations[0];


						// Convert caption into html entities,
						// escape quotes and other special characters.
						var title = htmlentity(variation.title);

						// Use setting from selected variation
						image.attr({
							src: variation.url,
							alt: title,
							title: title
						});


						// Enforce dimension
						if (settings.enforceDimension) {

							if (settings.enforceWidth!==null && settings.enforceHeight!==null) {

								var sizes = $.Image.resizeWithin(
									variation.width, variation.height,
									settings.enforceWidth, settings.enforceHeight
								);

								// Turn it into whole number
								sizes.width = Math.floor(sizes.width);
								sizes.height = Math.floor(sizes.height);

								image.attr(sizes);
							}
						}

						// Image caption
						if (settings.caption!==null) {

							// Convert caption into html entities,
							// escape quotes and other special characters.
							var caption = htmlentity(settings.caption);

							image
								.addClass("easyblog-image-caption")
								.attr("title", caption)
								.attr('alt', caption);
						}

						// Image zooming capabilities
						if (settings.zoom!==null) {

							var zoomWith = variations[settings.zoom] ||
										   variations[settings.defaultZoom] ||
										   {url: meta.thumbnail.url, title: meta.title};

							var title = htmlentity(settings.caption || zoomWith.title || "");

							image =
								$("<a>")
									.addClass("easyblog-thumb-preview")
									.attr({
										href: zoomWith.url,
										title: title
									})
									.html(image);
						};

						resolve();
					}

				// If any of these criterias were true,
				// we need to retrieve the variations.
				if (settings.variation || settings.zoom || settings.enforceWidth || settings.enforceHeight) {

					// If the variations hasn't been loaded
					if (meta.variations===undefined) {

						task.notify($.language("COM_EASYBLOG_MM_GETTING_IMAGE_SIZES"));

						// Then get it first
						self.media.library.getMetaVariations(key)
							.done(function(metaWithVariations) {

								// Add variations to our meta
								meta = metaWithVariations;

								// Process the rest of the image settings
								process();
							})
							.fail(function() {

								// If the ajax call failed, reject task.
								task.reject($.language("COM_EASYBLOG_MM_UNABLE_TO_RETRIEVE_VARIATIONS"));
							});

					// If variations has been loaded,
					// process the rest of the image settings now.
					} else {

						process();
					}

				// If there are no fancy image settings,
				// the default one will work just fine.
				} else {

					resolve();
				}

				return task;
			}

		}}
	);

	EasyBlog.Controller("Media.Exporter.Audio",

		{
			defaultOptions: {
				width: 400,
				height: 24,
				autostart: false,
				controlbar: "bottom",
				backcolor: "#333333",
				frontcolor: "#ffffff"
			}
		},

		function(self){ return {

			init: function() {

			},

			create: function(task, key, customSettings) {

				var settings = $.extend({}, self.options.settings, customSettings),

					meta = self.media.library.getMeta(key);

					settings.file = meta.path;

					settings.place = meta.place;

				var snippet = "[embed=audio]" + JSON.stringify(settings) + "[/embed]";

				task.resolve(snippet);

				return task;
			}

		}}
	);

	EasyBlog.Controller("Media.Exporter.Video",

		{
			defaultOptions: {

				settings: {
					width: 400,
					height: 225,
					autostart: false,
					controlbar: "bottom",
					backcolor: "#333333",
					frontcolor: "#ffffff"
				}
			}
		},

		function(self){ return {

			init: function() {

			},

			create: function(task, key, customSettings) {

				var settings = $.extend({}, self.options.settings, customSettings),

					meta = self.media.library.getMeta(key);

					settings.file = meta.path;

					settings.place = meta.place;

				var snippet = "[embed=video]" + JSON.stringify(settings) + "[/embed]";

				task.resolve(snippet);

				return task;
			}

		}}
	);

	EasyBlog.Controller("MediaLauncher",

		{
			defaultOptions: {
				"{uploadImageButton}": ".uploadImageButton",
				"{chooseImageButton}": ".chooseImageButton"
			}
		},

		function(self) { return {

			init: function() {

				$("#media_manager_button").click(function(){
					EasyBlog.mediaManager.browse();
				});
			},

			"{uploadImageButton} click": function() {

				EasyBlog.mediaManager.upload();
			},

			"{chooseImageButton} click": function() {


				EasyBlog.mediaManager.browse();
			}
		}}
	);

	//
	// 2. Initialize media manager.
	//
	var container = $(document.createElement("div")).attr("id", "EasyBlogMediaManager").appendTo("body");

	EasyBlog.mediaManager = new EasyBlog.Controller.Media(container);
});


// module: start
EasyBlog.module("media/navigation", function($){

var module = this;

// require: start
EasyBlog.require()
// .view(
// 	"media/navigation.item",
// 	"media/navigation.itemgroup"
// )
.done(function(){


var $Media, $Library, DS;

// controller: start
EasyBlog.Controller(

	"Media.Navigation",

	{
		defaultOptions: {

			view: {
				item: "media/navigation.item",
				itemGroup: "media/navigation.itemgroup"
			},

			nestLevel: 8,
			groupCollapseDelay: 1000,
			canActivate: true,

			"{itemGroup}": ".navigationItemGroup",
			"{item}": ".navigationItem"
		}
	},

	function(self) { return {

		init: function() {

			// Globals
			$Media = self.media;
			$Library = $Media.library;
			DS = $Media.options.directorySeparator;

			self.element.toggleClass("canActivate", self.options.canActivate);
		},

		setPathway: function(meta) {

			var meta = $Library.getMeta(meta);

			if (!meta) return;

			self.currentKey = meta.key;

			// Set path as current path
			var place      = $Library.getPlace(meta.place),
				path       = meta.path,
				isFolder   = (meta.type==="folder"),
				folders    = path.split(DS).splice(1),
				nestLevel  = self.options.nestLevel,
				groupUntil = folders.length - ((folders.length % nestLevel) || nestLevel),
				itemGroup;

			// Clear out existing breadcrumb and
			// toggle folder class if path lead to a folder.
			self.element
				.empty()
				.toggleClass("type-folder", isFolder);

			// Base folder
			self.view.item({title: place.title || DS})
				.addClass("base")
				.data("key", place.id + "|" + DS)
				.appendTo(self.element);

			var isJomSocial = place.id==="jomsocial";

			if (path!==DS) {

				$.each(folders, function(i, folder) {

					var isFile = (!isFolder && i==folders.length-1),

						path = DS + folders.slice(0, i + 1).join(DS),

						key = place.id + "|" + path,

						folder = (isJomSocial) ? $Library.getMeta(key).title : folder,

						item = self.view.item({title: (isFile) ? meta.title : folder})
								   .data("key", key)
								   .toggleClass("filename", isFile);

					if (i >= groupUntil) {
						item.appendTo(self.element);
					} else {
						if (i % nestLevel == 0) {
							itemGroup = self.view.itemGroup()
											.appendTo(self.element);
						}
						item.appendTo(itemGroup);
					}
				});
			}
		},

		"{itemGroup} mouseover": function(el) {

			clearTimeout(el.data("delayCollapse"));
			el.addClass("expand");
		},

		"{itemGroup} mouseout": function(el) {

			el.data("delayCollapse",
				setTimeout(function() {
					el.removeClass("expand");
				}, self.options.groupCollapseDelay)
			);
		},

		"{item} click": function(el) {

			if (self.options.canActivate) {
				var key = el.data("key");
				self.trigger("activate", key);
			}
		}

	}}

);
// controller: end

module.resolve();

})
// require: end

});
// module: end

// module: start
EasyBlog.module("media/uploader", function($) {

var module = this;

// require: start
EasyBlog.require()
.library(
    "plupload"
)
// .view(
//     "media/browser.uploader",
//     "media/browser.uploader.item",
//     "media/browser.treeItemGroup",
//     "media/browser.treeItem"
// )
// .language(
//     'COM_EASYBLOG_MM_UPLOADING',
//     'COM_EASYBLOG_MM_UPLOADING_STATE',
//     'COM_EASYBLOG_MM_UPLOADING_PENDING',
//     'COM_EASYBLOG_MM_UPLOAD_COMPLETE',
//     'COM_EASYBLOG_MM_UPLOAD_PREPARING',
//     'COM_EASYBLOG_MM_UPLOAD_UNABLE_PARSE_RESPONSE',
//     'COM_EASYBLOG_MM_UPLOADING_LEFT'
// )
.done(function(){

var $Media, $Library, $Uploader, DS;

// controller: start
EasyBlog.Controller("Media.Uploader",

	{
		defaultOptions: {

            view: {
                uploader: "media/browser.uploader",
                uploadItem: "media/browser.uploader.item"
            },

            "{modalHeader}": ".modalHeader",
            "{modalToolbar}": ".modalToolbar",
            "{modalContent}": ".modalContent",
            "{modalFooter}": ".modalFooter",
            "{modalPrompt}": ".modalPrompt",
            "{modalBrowserButton}": ".modalButton.browserButton",
            "{modalDashboardButton}": ".modalButton.dashboardButton",

            "{uploadButton}" : ".uploadButton",
            "{uploadNavigation}" : ".uploadNavigation",

            "{uploadForm}": ".uploadForm",
            "{uploadPath}": ".uploadPath",
            "{uploadSize}": ".uploadSize",
            "{uploadExtensionList}": ".uploadExtensionList",

            "{uploadItemGroup}": ".uploadItemGroup",
            "{uploadItem}": ".uploadItem",

            "{uploadDropHint}": ".uploadDropHint",
            "{uploadInstructions}": ".uploadInstructions",

            "{clearListButton}": ".clearListButton"
		}
	},

    function(self) { return {

        init: function() {

            $Media = self.media;
            $Library = $Media.library;
            $Uploader = $Media.uploader = self;
            DS = $Media.options.directorySeparator;

            // Uploader template
            self.element
                .addClass("uploader")
                .html(self.view.uploader({
                    uploadSize: self.options.settings.max_file_size,
                    uploadExtensionList: self.options.settings.filters[0].extensions.split(",").join(", ")
                }));

            // Browser navigation
            self.uploadNavigation()
                .implement(
                    EasyBlog.Controller.Media.Navigation,
                    {
                        controller: self.controllerProps()
                    },
                    function() {
                        // Assign controller as a property of myself
                        self.navigation = this;
                    }
                );

            // Modal prompt
            self.modalPrompt()
                .implement(
                    EasyBlog.Controller.Media.Prompt,
                    {
                        controller: self.controllerProps()
                    },
                    function() {
                        self.promptDialog = this;
                    }
                );

            // Folder switcher
            self.element
                .implement(
                    EasyBlog.Controller.Media.Uploader.FolderSwitcher,
                    {
                        controller: self.controllerProps()
                    },
                    function() {
                        self.folderSwitcher = this;
                    }
                );

            // Plupload
            self.element
                .implement(
                    "plupload",
                    {
                        settings: self.options.settings,
                        "{uploadButton}" : self.options["{uploadButton}"],
                        "{uploadDropsite}": self.options["{uploadItemGroup}"]
                    },
                    function() {

                        self.plupload = this.plupload;

                        if (self.plupload.runtime=="html4" || $.browser.msie) {

                            // No drag & drop support
                            self.uploadDropHint().remove();

                            self.uploadItemGroup().addClass("indefinite-progress");

                            // Really dirty fix to fix tooltip in IE
                            var uploadInstructions =
                                    self.uploadInstructions()
                                        .appendTo($Media.element);

                            self.element.find("> form")
                                .mouseover(function(){

                                    var uploadButton = self.uploadButton(),
                                        offset = uploadButton.offset();

                                    uploadInstructions
                                        .addClass("show")
                                        .css({
                                            top: offset.top + uploadButton.outerHeight() - $Media.element.offset().top + 3,
                                            right: $(window).width() - (offset.left + uploadButton.outerWidth()) - 1
                                        });


                                })
                                .mouseout(function(){
                                    uploadInstructions.removeClass("show");
                                });

                        } else {

                            self.uploadButton()
                                .mouseover(function(){
                                    self.uploadInstructions().addClass("show");
                                })
                                .mouseout(function(){
                                    self.uploadInstructions().removeClass("show");
                                });
                        }
                    }
                );

            self.setLayout();
		},

        setLayout: function() {

            // Don't set layout if current modal is not us
            if ($Media.currentModal!=="uploader") return;

            var contentHeight;

            self.modalContent()
                .hide()
                .height(
                    contentHeight =
                        self.element.height() -
                        self.modalHeader().outerHeight() -
                        self.modalToolbar().outerHeight() -
                        self.modalFooter().outerHeight()
                )
                .show();

            if ($.browser.msie) {

                self.uploadDropHint()
                    .height(contentHeight);

                self.uploadItemGroup()
                    .height(contentHeight);
            }

            if (self.plupload) {
                self.plupload.refresh();
            }
        },

        controllerProps: function(prop) {

            return $.extend(
            {
                media: self.media,
                uploader: self
            }, prop || {});
        },

        setUploadFolder: function(key) {

            if (!key) return;

            self.navigation
                .setPathway(key);

            self.currentUploadFolder = key;
        },

        items: {},

        createItem: function(file) {

            // Create item controller
            var item = new EasyBlog.Controller.Media.Uploader.Item(
                self.view.uploadItem(),
                {
                    controller: self.controllerProps({
                        id: file.id,
                        originalFile: file,
                        uploadFolder: self.currentUploadFolder
                    })
                }
            );

            // Add to item group
            item.element
                .appendTo(self.uploadItemGroup());

            // Set initial status to pending
            var filesize = item.file().filesize,
                filesize = (filesize) ? "" : " (" + filesize + ").";

            item.setMessage($.language( "COM_EASYBLOG_MM_UPLOADING_PENDING" ) + filesize);

            // Keep a copy of the item in our registry
            self.items[file.id] = item;

            return item;
        },

        "{self} BeforeUpload": function(el, event, uploader, file) {

            var item = self.items[file.id];

            if (item===undefined) return;

            item.setMessage( $.language( 'COM_EASYBLOG_MM_UPLOAD_PREPARING' ) );

            var uploadUrl = self.options.settings.url,
                meta  = $Library.getMeta(item.uploadFolder),
                place = (meta) ? meta.place : item.uploadFolder.split("|")[0],
                path  = encodeURIComponent((meta) ? meta.path : item.uploadFolder.split("|")[1]);

            uploader.settings.url = uploadUrl + "&place=" + place + "&path=" + path;
        },

        "{self} FilesAdded": function(el, event, uploader, files) {

            // Wrap the entire body in a try...catch scope to prevent
            // browser from trying to redirect and load the file if anything goes wrong here.
            try {

                $.each(files, function(i, file) {

                    // The item may have been created before, e.g.
                    // when plupload error event gets triggered first.
                    if (self.items[file.id]!==undefined) return;

                    self.createItem(file);
                });

                if (self.uploadItem().length > 0) {

                    self.uploadItemGroup().removeClass("empty");
                }

                self.plupload.start();

            } catch (e) {

                console.error(e);
            };
        },

        "{self} UploadFile": function(el, event, uploader, file) {

            var item = self.items[file.id];

            if (item===undefined) return;

            item.setState("uploading");

            item.setMessage( $.language( 'COM_EASYBLOG_MM_UPLOADING_STATE' ) );
        },

        "{self} UploadProgress": function(el, event, uploader, file) {

            var item = self.items[file.id];

            if (item===undefined) return;

            item.setProgress(file.percent);

            item.setMessage(
                $.language( 'COM_EASYBLOG_MM_UPLOADING' )  +
                ((file.percent!==undefined) ? " " + file.percent + "%" : "") +
                ((file.loaded!==undefined && !file.size!==undefined) ?
                    ((file.size - file.loaded) ?
                        " (" + $.plupload.formatSize(file.size - file.loaded) + " " + $.language( 'COM_EASYBLOG_MM_UPLOADING_LEFT' ) + ")" : ""
                    ) : ""
                )
            );
        },

        "{self} FileUploaded": function(el, event, uploader, file, response) {

            // Get upload item
            var item = self.items[file.id];

            if (item===undefined) return;

            // Store the item response (For debugging purposes)
            item.response = response;

            // If the response is not a valid object
            if (!$.isPlainObject(response)) {

                // Set upload item state to failed.
                item.setState("failed");
                item.setMessage($.language('COM_EASYBLOG_MM_SERVER_RETURNED_INVALID_RESPONSE'));
                return;
            }

            // If the response object did not include the meta
            if (!$.isPlainObject(response.item)) {

                // Set upload item state to failed.
                item.setState("failed");
                item.setMessage(response.message || $.language('COM_EASYBLOG_MM_UPLOAD_UNABLE_PARSE_RESPONSE'));
                return;
            }

            // If all goes well, set upload item state to done.
            item.setState("done");
            item.setMessage( $.language( 'COM_EASYBLOG_MM_UPLOAD_COMPLETE' ) );

            // Store the meta
            item.meta = response.item;

            // hack: Restore place
            item.meta.place = $Media.library.get(item.uploadFolder).place;

            // Remove insert blog image button for non-image files
            if (item.meta.type!=="image") {
                item.insertBlogImageButton().remove();
            }

            $Media.library.addMeta(item.meta);
        },

        "{self} FileError": function(el, event, uploader, file, response) {

            var item = self.items[file.id];

            if (item===undefined) {

                // Create the item
                item = self.createItem(file);
            }

            item.response = response;

            item.setState("failed");

            item.setMessage(response.message);


            if (self.uploadItem().length > 0) {

                self.uploadItemGroup().removeClass("empty");
            }
        },

        "{self} Error": function(el, event, uploader, error) {

            // If the returned error object also returns a file object
            if (error.file) {

                // Check if the upload item has been created
                var file = error.file,
                    item = self.items[file.id];

                // If the upload item doesn't exist
                if (item===undefined) {

                    // Create the item
                    item = self.createItem(file);
                }

                // Set the item state as failed
                item.setState("failed");

                // And the message for the item.
                item.setMessage(error.message);
            }

            if (self.uploadItem().length > 0) {

                self.uploadItemGroup().removeClass("empty");
            }
        },

        "{modalBrowserButton} click": function() {

            $Media.browse();
        },

        "{modalDashboardButton} click": function() {

            $Media.hide();
        },

        "{uploadNavigation} activate": function(el, event, key) {

            self.setUploadFolder(key);
        },

        "{self} modalActivate": function(el, event, key) {

            self.setUploadFolder(key);

            if ($Media.browser) {
                self.uploadItemGroup()
                    .toggleClass("blogimage", $Media.browser.mode()=="blogimage");
            }
        },

        removeItem: function(id) {

            var item = self.items[id];

            if (item!==undefined) {

                self.plupload.removeFile(item.file());

                item.element.remove();

                delete self.items[id];
            }

            if (self.uploadItem().length < 1) {

                self.uploadItemGroup().addClass("empty");
            }
        },

        "{clearListButton} click": function() {

            for (id in self.items) {

                self.removeItem(id);
            }
        }
	}}

);

EasyBlog.Controller("Media.Uploader.Item",

    {
        defaultOptions: {
            "{filename}": ".uploadFilename",
            "{progressBar}": ".uploadProgressBar progress",
            "{percentage}": ".uploadPercentage",
            "{status}": ".uploadStatus",
            "{removeButton}": ".uploadRemoveButton",
            "{insertItemButton}": ".insertItemButton",
            "{locateItemButton}": ".locateItemButton",
            "{insertBlogImageButton}": ".insertBlogImageButton"
        }
    },

    // Instance properties
    function(self) { return {

        init: function() {

            var file = self.file();

            self.filename()
                .html(file.name);

            self.setState("queued");
        },

        file: function() {

            var file = $Uploader.plupload.getFile(self.id) || self.originalFile;

            if (file) {

                file.filesize = (file.size===undefined || file.size=="N/A") ? "" : $.plupload.formatSize(self.file.size);
            }

            return file;
        },

        "dblclick": function(el, event) {

            if (event.shiftKey) {
                $Media.console("log", self);
            }
        },

        setProgress: function(val) {

            self.progressBar()
                .attr("value", val);

            self.percentage()
                .html(val);
        },

        setState: function(state) {

            // queued, uploading, failed, done
            self.element
                .removeClass("upload-state-" + self.state)
                .addClass("upload-state-" + state);

            self.state = state;
        },

        setMessage: function(message) {

            self.status()
                .html(message);
        },

        "{removeButton} click": function(el, event) {

            event.stopPropagation();

            $Uploader.removeItem(self.id);
        },

        "{insertItemButton} click": function() {

            $Media.insert(self.meta);
        },

        "{locateItemButton} click": function() {

            $Media.browse(self.meta);
        },

        "{insertBlogImageButton} click": function() {

            // We are getting the raw meta
            var meta = $Library.meta[$Library.getMeta(self.meta).key];

            EasyBlog.dashboard.blogImage.setImage(meta);

            $Media.hide();
        }
    }}

);

EasyBlog.Controller("Media.Uploader.FolderSwitcher",
    {
        defaultOptions: {

            view: {
                treeItemGroup: "media/browser.tree-item-group",
                treeItem     : "media/browser.tree-item"
            },

            "{changeUploadFolderButton}": ".changeUploadFolderButton",
            "{selectFolderButton}": ".selectFolderButton",
            "{treeItemField}"   : ".browserTreeItemField",
            "{treeItemGroup}"   : ".browserTreeItemGroup",
            "{treeItem}"        : ".browserTreeItem"
        }
    },

    function(self) { return {

        init: function() {

            var initialUploadFolder;

            self.promptDialog = $Uploader.promptDialog.get("changeUploadFolderPrompt");

            // Create all places
            $.each($Media.library.places, function(id, place) {

                if (!place.acl.canUploadItem) return;

                place.uploaderTreeItemGroup =
                    self.view.treeItemGroup()
                        .addClass("expanded") // Always expanded
                        .appendTo(self.treeItemField());

                place.uploaderTreeItem =
                    self.view.treeItem({title: place.title})
                        .addClass("loading")
                        .addClass("type-place")
                        .data("place", place)
                        .appendTo(place.uploaderTreeItemGroup);

                if (!initialUploadFolder) {

                    initialUploadFolder = place.id + "|" + DS;

                    $Uploader.setUploadFolder(initialUploadFolder);
                }

                place
                    .done(function(){

                        self.createTreeItem(place.baseFolder());

                        place.uploaderTreeItem
                           .removeClass("loading");
                    });
            });
        },

        treeItems: {},

        createTreeItem: function(meta) {

            var meta = $Library.getMeta(meta),

                treeItem = self.treeItems[meta.key] || (function(){

                    var place = $Library.getPlace(meta.place),

                        parentMeta = $Library.getMeta(meta.parentKey),

                        // Create tree item
                        treeItem = self.treeItems[meta.key] =

                            ((parentMeta) ?
                                self.view.treeItem({title: meta.title})
                                    .addClass("type-folder")
                                    .insertAfter(self.treeItems[parentMeta.key])
                                :
                                place.uploaderTreeItem

                            // Store a reference to the key
                            ).data("key", meta.key);

                        // Remove tree item when meta is removed
                        meta.data.on("removed", function(){
                            self.removeTreeItem(meta);
                        });

                        // Listen to the subfolder for changes
                        meta.data.views
                            .create({group: "folders"})
                            .updated(function(folders) {

                                $.each(folders, function(i, key) {
                                    self.createTreeItem(key);
                                });
                            });

                    return treeItem;
                })();

            return treeItem;
        },

        removeTreeItem: function(meta) {

            var meta = $Library.getMeta(meta),

                treeItem = self.treeItems[meta.key];

            if (treeItem) {

                treeItem.remove();

                var parentTreeItem = self.treeItems[meta.parentKey];

                $Uploader.setUploadFolder(meta.parentKey);
            }
        },

        "{treeItem} click": function(el) {

            self.treeItem().removeClass("active");

            el.addClass("active");
        },

        "{changeUploadFolderButton} click": function() {

            var key = self.currentUploadFolder,

                treeItem = self.treeItems[key] || self.treeItem(":first");

            // Highlght on that tree item
            treeItem.click()

            self.promptDialog.show();
        },

        "{selectFolderButton} click": function() {

            var key = self.treeItem(".active").data("key");

            $Uploader.setUploadFolder(key);

            self.promptDialog.hide();
        }

    }}
);


// controller: end

module.resolve();

});
// require: end

});
// module: end

// module: start
EasyBlog.module("media/browser", function($) {

var module = this;

// require: start
EasyBlog.require()
.library(
	"image",
	"easing",
	"scrollTo"
)
.script(
	"media/browser.item"
)
// .view(
// 	"media/browser",
// 	"media/browser.itemGroup",
// 	"media/browser.item",
// 	"media/browser.treeItemGroup",
// 	"media/browser.treeItem",
// 	"media/browser.paginationPage"
// )
// .language(
// 	'COM_EASYBLOG_MM_CONFIRM_DELETE_ITEM',
// 	'COM_EASYBLOG_MM_CANCEL_BUTTON',
// 	'COM_EASYBLOG_MM_YES_BUTTON',
// 	'COM_EASYBLOG_MM_ITEM_DELETE_CONFIRMATION'
// )
.done(function(){

var $Media, $Library, $Browser, $Uploader, $Prompt, DS;

// controller: start
EasyBlog.Controller("Media.Browser",

	// Class properties
	{
		defaultOptions: {

			view: {
				browser			: "media/browser",
				itemGroup		: "media/browser.item-group",
				item			: "media/browser.item",
				treeItemGroup	: "media/browser.tree-item-group",
				treeItem		: "media/browser.tree-item",
				paginationPage	: "media/browser.pagination-page"
			},

			path: "",

			items: undefined,

			mode: "browse",

			layout: {
				viewMode: "tile",
				tileSize: 0.125,
				scrollToItemDuration: 500,
				scrollToItemEasing: 'swing',
				iconMaxLoadThread: 8
			},

			search: {
				chunkSize: 128,
				chunkDelay: 500
			},

			"{modalHeader}"			: ".modalHeader",
			"{modalToolbar}"		: ".modalToolbar",
			"{modalContent}"		: ".modalContent",
			"{modalFooter}"			: ".modalFooter",
			"{modalPrompt}"			: ".modalPrompt",

			// Also shared with folder hint's .uploaderButton
			"{modalUploaderButton}"	: ".uploaderButton",

			"{header}"	: ".browserHeader",
			"{content}"	: ".browserContent",
			"{footer}"	: ".browserFooter",

			"{treeToggleButton}": ".browserTreeToggleButton",
			"{tileViewButton}"	: ".browserTileViewButton",
			"{listViewButton}"	: ".browserListViewButton",

			"{itemField}"	: ".browserItemField",
			"{itemGroup}"	: ".browserItemGroup",
			"{item}"		: ".browserItem",

			"{treeItemField}"	: ".browserTreeItemField",
			"{treeItemGroup}"	: ".browserTreeItemGroup",
			"{treeItem}"		: ".browserTreeItem",

			"{headerTitle}"		: ".browserTitle",
			"{headerSearch}"	: ".browserSearch",
			"{headerNavigation}": ".browserNavigation",
			"{headerUpload}"	: ".browserUploadButton",

			"{footerStatus}"	: ".browserStatus",
			"{footerMessage}"	: ".browserMessage",

			"{itemActionSet}": ".browserItemActionSet",

			"{itemFieldHints}": ".browserItemField .hints",

			"{browserPagination}"	: ".browserPagination",
			"{currentPage}"			: ".currentPage",
			"{totalPage}"			: ".totalPage",
			"{prevPageButton}"		: ".prevPageButton",
			"{nextPageButton}"		: ".nextPageButton",
			"{pageSelection}"		: ".pageSelection",
			"{paginationPage}"		: ".paginationPage",

			"{searchInput}"  : ".searchInput"
		}
	},

	// Instance properties
	function(self) {

		return {

		init: function() {

			// Globals
			$Media = self.media;
			$Library = $Media.library;
			$Browser = $Media.browser = self;
			DS = $Media.options.directorySeparator;

			self.iconThread = $.Threads({threadLimit: self.options.layout.iconMaxLoadThread});
			self.enqueue = $.Enqueue();

			var flickr = $Library.getPlace('flickr');

			// Render browser UI
			self.element
				.addClass("browser")
				.html(
					self.view.browser({
						canUpload: self.options.acl.canUpload,
						flickrCallback: (flickr) ? flickr.options.callback : '',
						flickrLogin: (flickr) ? flickr.options.login : ''
					})
				);

			// Browser navigation
			self.headerNavigation()
				.implement(
					EasyBlog.Controller.Media.Navigation,
					{
						controller: self.controllerProps()
					},
					function() {
						// Assign controller as a property of myself
						self.navigation = this;
					}
				);

			self.modalPrompt()
				.implement(
					EasyBlog.Controller.Media.Prompt,
					{
						controller: self.controllerProps()
					},
					function() {
						$Prompt = self.promptDialog = this;
					}
				);

			// Implement browser actions
			self.element
				.implement(
					EasyBlog.Controller.Media.Browser.Actions,
					{
						controller: self.controllerProps()
					},
					function() {
						self.actions = this;
					}
				);

			self.search = $.debounce(self._search, 500);

			// Set to browse mode
			self.mode("browse");

			// Always revert to browser mode when going back to dashboard
			$Media.element.bind("hideModal", function(){
				self.mode("browse");
			});

			// Set browser layout
			self.viewMode(self.options.layout.viewMode);

			self.setLayout();

			// Bind item group scroll event
			self.on(
				"scroll",
				self.itemField(),
				$.debounce(self["{itemField} scroll"], 250)
			);

			// Create all places
			$.each($Library.places, function(id, place) {

				self.createPlace(place);
			});

			// Determine which place is the initial place,
			// if no intial place was given, automatically select
			// the first place on the list.
			self.activatePlace(self.options.initialPlace || $Library.places[0].id)
				.done(
					self.enqueue(function(place){
						self.activateItem(place.baseFolder());
					})
				);
		},

		controllerProps: function(prop) {

			return $.extend(
			{
				media: $Media

			}, prop || {});
		},

		items: {},

		createPlace: function(place) {

			var place = $Library.getPlace(place);

			place.treeItemGroup =
				self.view.treeItemGroup()
					.appendTo(self.treeItemField());

			place.treeItem =
				self.view.treeItem({title: place.title})
					.addClass("type-place")
					.data("place", place)
					.appendTo(place.treeItemGroup);

			place.itemGroup =
				self.view.itemGroup()
					.appendTo(self.itemField());

			return place;
		},

		activatePlace: function(place) {

			var place = $Library.getPlace(place);

			if (place===undefined) return;

			// Toggle active class on item group
			self.itemGroup().removeClass("active");
			place.itemGroup.addClass("active");

			// Toggle active class on item tree
			self.treeItem().removeClass("active");
			place.treeItem.addClass("active");

			// Create the activator task
			if (!place.activator) {

				place.activator = $.Deferred();
			}

			// If flickr is not authenticated yet
			if (place.id==="flickr" && !place.options.associated) {

				// Show the flickr login prompt
				$Browser.currentFolderStatus("flickr");

				return place.activator;
			}

			if (!place.populated) {

				// Mark as populated so this doesn't run again
				place.populated = true;

				// Add busy indicator
				self.currentFolderStatus("loading");

				var populator = /easysocial|jomsocial|flickr/.test(place.id) ? place.populate() : place.ready;

				populator
					.done(function() {

						// Create base folder
						var baseFolderItem = self.createFolder(place.baseFolder());

						place.activator.resolveWith(place, [place, baseFolderItem]);
					})
					.fail(function() {

						self.currentFolderStatus("error");

						place.activator.rejectWith(place, arguments);
					});
			}

			return place.activator;
		},

		getItem: function(item) {

			// Skip going through all the tests below.
			if (item===undefined) return;

			// Item instance
			if (item instanceof EasyBlog.Controller.Media.Browser.Item) {
				return item;
			}

			// Item key
			if (typeof item === "string") {
				return self.items[item];
			}

			// Meta
			if ($Library.isMeta(item)) {
				return self.items[$Library.getKey(item)];
			}
		},

		createItem: function(meta, options) {

			var meta = $Library.getMeta(meta);

			if (!meta) return;

			// Create item, store to item map, return item.
			return self.items[meta.key] =

				new EasyBlog.Controller.Media.Browser.Item(

					self.view.item({meta: meta}),
					{
						controller: {
							key: meta.key,
							media: $Media
						}
					}
				);
		},

		createFolder: function(meta, options) {

			var meta = $Library.getMeta(meta);

			return self.getItem(meta) || (function(){

				// Create & insert item
				var item = self.createItem(meta);

				var place = item.place(),

					parentFolder = item.parentFolder();

					((parentFolder) ?
						item.element
							.insertAfter(parentFolder.element)
						:
						item.element
							.appendTo(place.itemGroup));

				// Create & insert tree item
				item.treeItem =
					((parentFolder) ?
						self.view.treeItem({title: meta.title})
							.addClass("type-folder")
							.css("marginLeft", 18 * (meta.path.split(DS).length - 1))
							.insertAfter(parentFolder.treeItem)
						:
						place.treeItem

					// Store a reference to the item
					).data("item", item);

				// Listen to the subfolder for changes
				meta.data.views
					.create({group: "folders"})
					.updated(function(folders){
						$.each(folders, function(i, key) {
							self.createFolder(key);
						});
					});

				return item;

			})();
		},

		createFile: function(meta, options) {

			var meta = $Library.getMeta(meta);

			return self.getItem(meta) || self.createItem(meta);
		},

		removeItem: function(item) {

			clearTimeout(self.removeItem.revert);

			var item = self.getItem(item),

				parentFolder = item.parentFolder();

				// Remove item element & handler
				item.remove();

				// Remove treeItem if it exists
				if (item.treeItem) {
					item.treeItem.remove();
				}

				// Delete from entry
				delete self.items[item.key];

			// Don't revert if searching
			if (self.itemField().hasClass("searching")) return;

			self.removeItem.revert = setTimeout(function(){

				if (parentFolder) {
					self.activateItem(parentFolder);
				}

			}, 500);
		},

		focusItem: function(item, alsoActivate) {

			var item = self.getItem(item);

			// If item does not exist, skip.
			if (!item) return;

			if (!alsoActivate) {

				// Set item as current item
				self.currentItem(item);

				// Remove the active class because
				// we just want to focus it, not activate it.
				item.element.removeClass("active");

			} else {

				self.activateItem(item);
			}

			self.scrollTo(item);

			self.trigger("itemFocus", [item]);
		},

		locateItem: function(meta) {

			if (self.itemField().hasClass("searching")) {
				self.clearSearch(true);
			}

			var meta = $Library.getMeta(meta);

			if (!meta) return;

			self.activatePlace(meta.place)
				.done(
					self.enqueue(function() {

						var isFolder = meta.type==="folder",

							item = self.activateItem((isFolder) ? meta : meta.parentKey);

							if (item===undefined) return;

							if (!isFolder) {
								item.handler.locateItem(meta);
							}

							// Quick monkey patch
							var folderView = item.handler.folderView;

							if (folderView) {
								folderView.refresh();
							}
					})
				);
		},

		activateItem: function(item) {

			var item = self.getItem(item);

			// If item does not exist, skip.
			if (!item) return;

			// Set item as current item
			self.currentItem(item);

			// Activating an item will trigger its handler, e.g.
			// for folders it will generate items inside it.
			item.activate();

			self.trigger("itemActivate", [item]);

			return item;
		},

		scrollTo: function(item) {

			var item = self.getItem(item);

			if (!item) return;

			self.itemField()
				.scrollTo(item.element, {
					duration: self.options.layout.scrollToItemDuration,
					easing: self.options.layout.scrollToItemEasing,

					// This means that the item will be scrolled to 10% from the top of the field
					offset: {top: self.itemField().height() * -0.10}
				});
		},

		currentItem: function(item)
		{
			// Get current item.
			var currentItem = self.currentItem.item;

			// If current item has been destroyed,
			if (currentItem && (currentItem._destroyed || !self.items[currentItem.key]))
			{
				// set current item as undefined.
				currentItem = self.currentItem.item = undefined;
			}

			// If this is a setter operation, get the item.
			var item = self.getItem(item);

			// If the new current item does not exist,
			// just return current item.
			if (!item)
			{
				return currentItem;
			}

			// If the new current item has already been destroyed,
			// just return current item.
			if (item._destroyed)
			{
				return currentItem;
			}

			// If previous current item exists,
			if (currentItem)
			{
				// remove its active & focus class.
				currentItem.element.removeClass("active focus");

				// Also, if this item is a file,
				// remove active & focus class from its parent folder.
				if (currentItem.meta().type!=="folder")
				{

					currentItem.parentFolder().element.removeClass("active focus");
				}

				// Also remove active class from place's itemgroup
				currentItem.place().itemGroup.removeClass("active");
			}

			// Add active & focus class to new current item
			item.element.addClass("active focus");

			// Lets see if this item is a folder.
			var isFolder = item.meta().type=="folder";

			// If new current item is file,
			if (!isFolder) {

				// then add a focus class to its parent folder.
				item.parentFolder().element.addClass("focus");
			}

			// Add active class to item group
			item.place().itemGroup.addClass("active");

			// If this a folder, set current folder to the item itself.
			// If this is a file, set current folder to the item's parent folder.
			self.currentFolder(
				(isFolder) ? item : item.parentFolder()
			);

			// Set the navigation to the new current item.
			self.navigation.setPathway(item.key);

			// Set and return new current item.
			return self.currentItem.item = item;
		},

		currentFolder: function(folder) {

			// Get current folder
			var currentFolder = self.currentFolder.folder;

			// If current folder has been destroyed,
			if (currentFolder && currentFolder._destroyed) {

				// set current folder as undefined.
				currentFolder = self.currentFolder.folder = undefined;
			}

			// If this is a setter operation, get the item.
			var folder = self.getItem(folder);

			// If the folder does not exist, return current folder.
			// Also, as getter operation.
			if (!folder) return currentFolder;

			// Add active class to new current folder
			self.treeItem().removeClass("active");
			folder.treeItem.addClass("active");

			// Also expand the place tree
			if (folder.meta().path!==DS) {
				folder.place().treeItemGroup.addClass("expanded");
			}

			// Also refresh view
			if (folder.handler.folderView) {

				if (folder.handler.refreshSeed!==self.folderRefreshSeed) {
					folder.handler.folderView.refresh();
					folder.handler.refreshSeed = self.folderRefreshSeed;
				}
			}

			// Set and return new current folder.
			return self.currentFolder.folder = folder;
		},

		currentFolderStatus: function(status) {

			// Quick monkey patch to prevent double activation (also fixes jomsocial)
			if (self.itemField().hasClass("searching") && !/emptySearch|ready/.test(status)) return;

			var lastStatus = self.currentFolderStatus.lastStatus;

			// Getter
			if (status === undefined) return lastStatus;

			// Setter
			if (typeof status !== "string") return;

			// Remove last status
			if (lastStatus) {
				self.itemField()
					.removeClass(lastStatus);
			}

			// Add new status
			self.itemField().addClass(status);

			return self.currentFolderStatus.lastStatus = status;
		},

		setLayout: function() {

			// Skip if no layout has been set OR current modal is not us.
			if (!$Media.layout || $Media.currentModal!=="browser") return;

			var contentHeight;

			self.modalContent()
				.hide()
				.height(
					contentHeight =
						self.element.height() -
						self.modalHeader().outerHeight() -
						self.modalToolbar().outerHeight() -
						self.modalFooter().outerHeight()
				)
				.show();

			if ($.browser.msie) {

				self.treeItemField()
					.height(contentHeight);

				self.itemField()
					.height(contentHeight);

				self.itemFieldHints()
					.height(contentHeight);
			}

			self.setItemLayout();

			self.trigger("setLayout");
		},

		setItemStyle: function(force) {

			// If this is not forced, skip setting item style
			// if layout seed hasn't changed yet.
			if (!force) {

				// Get current layout seed
				var seed = $Media.layout;

				// If layout seed matches, no setting of item style is necessary.
				if (self.setItemStyle.seed===seed) return;

				// Set current layout seed
				self.setItemStyle.seed = seed;
			}

			// Set up variables
			var viewMode = self.viewMode(),
				cssRules = {};

			if (viewMode=="tile") {

				var browserItem = "#EasyBlogMediaManager .browser .browserItemField.view-tile .browserItem",
					availableWidth = (function() {
						var testElement = $(document.createElement("DIV")).prependTo(self.itemField()),
							availableWidth = testElement.width();
						testElement.remove();
						return availableWidth;
					})(),
					itemWidth = Math.floor(availableWidth * self.options.layout.tileSize),
					itemHeight = itemWidth - 24;

				cssRules[browserItem] = {
					width: itemWidth + "px",
					height: itemHeight + "px"
				}
			}

			// Get the document head
			var head = document.getElementsByTagName("head")[0];

			// Remove previous stylesheet
			if (self.itemStyle) {
				try {
					head.removeChild(self.itemStyle);
				} catch(e) {};
			}

			// Create new stylesheet
			self.itemStyle = document.createElement("style");
			self.itemStyle.type = "text/css";

			// Generate css text
			var cssText = "";
			$.each(cssRules, function(selector, props) {
				cssText += selector + "{" + $.map(props, function(val, key){ return key + ":" + val; }).join(";") + "}\n";
			});

			// Append css text to stylesheet
			if (self.itemStyle.styleSheet) {
				self.itemStyle.styleSheet.cssText = cssText;
			} else {
				self.itemStyle.appendChild(document.createTextNode(cssText));
			}

			// Append stylesheet to head
			head.appendChild(self.itemStyle);
		},

		setItemLayout: function() {

			// Skip if no layout has been set OR current modal is not us.
			if (!$Media.layout || $Media.currentModal!=="browser") return;

			self.setItemStyle();

			setTimeout(function() {

				var items = [];

				if (self.itemField().hasClass("searching")) {

					// Monkey patch
					if (self.searchItemGroup) {
						items = self.searchItemGroup.find(".browserItem");
					}

				} else {

					// If there's no current folder selected, don't do anything.
					var currentFolder = self.currentFolder();
					if (currentFolder===undefined) return;

					items = currentFolder.childItem();
				}

				if (items.length < 1) return;

				// Drill down
				var itemFieldOffset = self.itemField().offset(),
					item,
					itemOffset,
					j = items.length,
					i = 1;

				if (items.length < 1) return;

				while (Math.abs(j - i) > 1) {
					item = items.eq(i-1);
					itemOffset = item.offset();

					var itemBottom = itemOffset.top - itemFieldOffset.top + item.outerHeight();
					if (itemBottom < 0) {
						i = Math.ceil((j + i) / 2);
					} else {
						j = i;
						i = Math.ceil(j / 2);
					}
				}

				// From the first found visible item,
				// work backwards & forwards until all
				// visible items on the viewport are covered
				if (i===1) i = 0;

				var b = i,
					f = i,
					min = 0,
					max = items.length - 1;
					setLayout = function(i) {
						if (i < min || i > max) return false;
						var item = items.eq(i).data("item");
						if (!item.isVisible()) return false;
						item.setLayout();
					};

					while (true) {
						if (setLayout(b)===false) break;
						b--;
					}

					while (true) {
						if (setLayout(f)===false) break;
						f++;
					}

			}, 0);
		},

		viewMode: function(mode) {

			// Get current view mode
			var currentMode = self.viewMode.mode;

			// If a mode hasn't been set yet, take from options.
			if (!currentMode) {
				currentMode = self.viewMode.mode = self.options.layout.viewMode;
			}

			// Setter operation
			if (mode!==undefined) {

				// Force a seed refresh
				self.setItemStyle.seed = null;

				// Replace view mode
				self.itemField()
					.removeClass("view-" + currentMode)
					.addClass("view-" + mode);

				// Update current view mode
				self.viewMode.mode = currentMode = mode;

				// Set browser layout
				self.setLayout();

				// Scroll to current item (as its position have changed in different view modes)
				var currentItem = self.currentItem();

				if (currentItem!==undefined) {

					self.scrollTo(currentItem);
				}
			}

			// Getter operation
			return currentMode;
		},

		mode: function(mode) {

			// Getter
			if (mode===undefined) return self.mode.currentMode || "browse";

			switch (mode) {
				case "browse":

					self.element
						.removeClass("mode-blogimage")
						.addClass("mode-browse");

					// Quick monkey patch to hide jomsocial & flickr items
					// under blog imagemode.
					$.each($Library.places, function(i, place){
						if (/easysocial|jomsocial|flickr/.test(place.id)) {
							place.treeItemGroup && place.treeItemGroup.show();
							place.itemGroup && place.itemGroup.show();
						}
					});

					break;

				case "blogimage":

					self.element
						.addClass("mode-blogimage")
						.removeClass("mode-browse");

					// Quick monkey patch to hide jomsocial & flickr items
					// under blog imagemode.
					var currentItem = self.currentItem(),
						switchToNearestLocalPlace = false;

					if (currentItem) {
						if (/easysocial|jomsocial|flickr/.test(currentItem.place().id)) {
							switchToNearestLocalPlace = true;
						}
					}

					$.each($Library.places, function(i, place){

						if (/easysocial|jomsocial|flickr/.test(place.id)) {
							place.treeItemGroup && place.treeItemGroup.hide();
							place.itemGroup && place.itemGroup.hide();
						} else {
							if (switchToNearestLocalPlace) {
								switchToNearestLocalPlace = false;
								if (place.treeItem) {
									place.treeItem.click();
								}
							}
						}
					});
					break;
			}

			self.mode.currentMode = mode;
		},

		"{self} itemActivate": function(el, event, item) {

			self.itemActionSet().removeClass("active");

			if (item.meta().type=="folder") {

				self.itemActionSet(".type-folder").addClass("active");

			} else {

				self.itemActionSet(".type-item").addClass("active");
			}
		},

		"{headerNavigation} activate": function(el, event, key) {

			self.activateItem(key);
		},

		"{tileViewButton} click": function(el, event) {

			el.addClass("active")
				.siblings()
				.removeClass("active");

			self.viewMode("tile");
		},

		"{listViewButton} click": function(el, event) {

			el.addClass("active")
				.siblings()
				.removeClass("active");

			self.viewMode("list");
		},

		// Not sure why this is triggered twice
		"{treeItem} click": $.debounce(function(el, event) {

			self.clearSearch(true);

			var item = el.data("item");

			if (el.hasClass("type-place")) {

				var place = el.data("place");

				// If the toggle icon was clicked
				if ($(event.target).hasClass("treeItemToggle")) {

					// Expand the tree group
					place.treeItemGroup.toggleClass("expanded");
				}

				self.activatePlace(place)
					.done(
						self.enqueue(function(place, baseFolder) {

							if (place.id==="jomsocial") {
								place.treeItemGroup.addClass("expanded");
							}

							if (place.id==="easysocial") {
								place.treeItemGroup.addClass("expanded");
							}

							self.activateItem(baseFolder);
						})
					);

				return;
			}

			self.activateItem(item);
		}, 50),

		"{itemField} scroll": function(el, event) {

			self.setItemLayout();
		},

		"{item} click": function(el, event) {

			// Prevents click event from being bubbled back to parent folder item
			event.stopPropagation();

			var item = el.data("item");

			if (item===undefined) return;

			var place = item.place();

			self.activatePlace(place)
				.done(
					self.enqueue(function(place, baseFolder) {

						// Monkey patch to work with search
						// self.activateItem(baseFolder);
						self.activateItem(item);
					})
				);
		},

		"{item} dblclick": function(el, event) {

			// Prevents click event from being bubbled back to parent folder item
			event.stopPropagation();

			var item = el.data("item");

			// #debug:start
			if (event.shiftKey) {
				$Media.console("log", [item]);
				return;
			}
			// #debug:end

			if (item===undefined) return;

			if (item.meta().type=="folder") return;

			if(self.mode.currentMode==='blogimage') {

				if (item.meta().type=="image") {

					// We are getting the raw meta
					var meta = $Library.meta[item.key];

					EasyBlog.dashboard.blogImage.setImage(meta);

					$Media.hide();
				}

			} else {

				$Media.edit(item.key);
			}
		},

		"{modalUploaderButton} click": function() {

			var item = self.currentFolder();

			$Media.upload((item.place().acl.canUploadItem) ? item.key : "");
		},

		"{self} modalActivate": function(el, event, meta, mode) {

			if (mode!==undefined) {

				self.mode(mode);
			}

			var meta = $Library.getMeta(meta) || self.currentItem().meta();

			if (meta) {

				self.locateItem(meta);
			}
		},

		"{prevPageButton} click": function() {
			var folder = $Browser.currentFolder();
			folder.handler.changePage('prev');
		},

		"{nextPageButton} click": function() {
			var folder = $Browser.currentFolder();
			folder.handler.changePage('next');
		},

		// "{pageSelection} change": function(el) {
		// 	var folder = $Browser.currentFolder(),
		// 		page = el.val();

		// 	folder.handler.currentPage(page);
		// },

		"{pageSelection} click": function(el) {
			if(self.paginationPage().length > 1) {
				el.toggleClass('expanded');
			}
		},

		"{paginationPage} click": function(el) {
			if(self.pageSelection().hasClass('expanded') && !el.hasClass('selected')) {
				var page = el.data('page'),
					folder = $Browser.currentFolder();

				folder.handler.isChangingPage = true;

				folder.handler.currentPage(page);
			}
		},

		"{window} click": function(el, event) {

			var className = $(event.target).attr('class');

			if(!/pageSelection|paginationPage/.test(className)) {
				if(self.pageSelection().hasClass('expanded')) {
					self.pageSelection().removeClass('expanded');
				}
			}
		},

		_search: function(keyword) {

			if (!self.itemBeforeSearch) {
				self.itemBeforeSearch = self.currentItem().meta();
			}

			self.element.addClass("searching");

			self.itemField()
				.addClass("searching");

			if (!self.searchItemGroup) {
				self.searchItemGroup =
					self.view.itemGroup()
						.appendTo(self.itemField());
			}

			self.searchItemGroup
				.addClass("active search-mode");

			var timer;

			self.searchView =
				$Library.search(keyword)
					.create({from: 0, to: 300})
					.updated(function(files){

						var l = files.length;

						if (l < 1) {
							timer = setTimeout(function(){
								$Browser.currentFolderStatus("emptySearch");
							}, 500);
							return;
						}

						clearTimeout(timer);
						$Browser.currentFolderStatus("ready");

						for (i=0; i<l; i++) {

							var key = files[i];

							// This either return the newly created file,
							// or the file that has been previously created.
							var file = $Browser.createFile(key);

							file.element
								.appendTo(self.searchItemGroup);
						}

						self.setItemLayout();
					});
		},

		clearSearch: function(cancel) {

			self.folderRefreshSeed = $.uid();

			if (cancel) {
				self.searchInput().val("").blur();
			}

			self.element.removeClass("searching");

			self.itemField()
				.removeClass("searching");

			if (self.searchItems) {
				$.each(self.searchItems, function(i, item){
					$(item).detach();
				});
			}

			if (self.searchItemGroup) {

				self.searchItemGroup
					.find(".browserItem")
					.detach();

				self.searchItemGroup.removeClass("active");
			}

			if (self.searchView) {
				self.searchView.destroy();
			}

			delete self.searchView;

			if (self.itemBeforeSearch) {
				self.locateItem(self.itemBeforeSearch);
			}
		},

		"{searchInput} focusin": function(el) {
			el.parent().addClass("active");

			if ($.trim(el.val())!=="") {
				el.parent().addClass("showCancelButton");
			}
		},

		"{searchInput} focusout": function(el) {

			setTimeout(function(){
				if ($.trim(el.val())==="") {
					el.parent().removeClass("active showCancelButton");
 				}
			}, 50);
		},

		"{searchInput} keyup": function(el) {

			var keyword = $.trim(el.val());

			if (keyword==="") {
				el.parent().removeClass("showCancelButton");
				self.clearSearch();
				delete self.itemBeforeSearch;
				return;
			}

			el.parent().addClass("showCancelButton");

			self.search(keyword);
		}
	}}

);
// controller: end

EasyBlog.Controller("Media.Browser.Actions",
	{
		defaultOptions: {

			// Item actions
			"{customizeItemButton}": ".customizeItemButton",
			"{insertAsGalleryButton}": ".insertAsGalleryButton",
			"{insertItemButton}": ".insertItemButton",
			"{insertBlogImageButton}": ".insertBlogImageButton",

			// Create folder prompt
			"{createFolderButton}"        : ".createFolderButton",
			"{confirmCreateFolderButton}" : ".createFolderPrompt .confirmCreateFolderButton",
			"{folderPath}"                : ".createFolderPrompt .folderPath",
			"{folderCreationPath}"        : ".createFolderPrompt .folderCreationPath",
			"{folderInput}"               : ".createFolderPrompt .folderInput",
			"{folderCreationFailedReason}": ".createFolderPrompt .folderCreationFailedReason",

			// Remove item prompt
			"{removeItemButton}"       : ".removeItemButton",
			"{removeItemFilename}"     : ".removeItemPrompt .removeItemFilename",
			"{confirmRemoveItemButton}": ".confirmRemoveItemButton",
			"{removeItemFailedReason}" : ".removeItemPrompt .removeItemFailedReason",

			// Flickr login
			"{flickrLoginButton}": ".flickrLoginButton",

			// Search
			"{cancelSearchButton}": ".cancelSearchButton",

			// Error prompt
			"{retryPopulateButton}": ".retryPopulateButton"
		}
	},
	function(self) { return {

		init: function() {

		},

		"{self} itemActivate": function(el, event, item) {

			self.item = item;

			var acl = item.place().acl;

			// If the current file can't be removed, hide remove item button.
			self.removeItemButton()
				.toggle(acl.canRemoveItem);

			self.createFolderButton()
				.toggle(acl.canCreateFolder);

			// Show insert blog image button if we are selecting blog image
			self.insertBlogImageButton().toggle($Browser.mode()==="blogimage" && item.meta().type==="image");
		},

		// Item actions
		"{customizeItemButton} click": function() {

			$Media.edit(self.item.key);
		},

		"{insertAsGalleryButton} click": function() {

			$Media.insert(self.item.key);
		},

		"{insertItemButton} click": function() {

			$Media.insert(self.item.key);
		},

		"{insertBlogImageButton} click": function() {

			// We are getting the raw meta
			var meta = $Library.meta[self.item.key];

			EasyBlog.dashboard.blogImage.setImage(meta);

			$Media.hide();
		},

		// Create folder prompt
		"{createFolderButton} click": function() {

			$Prompt.get("createFolderPrompt")
				.show()
				.state("default");

			var currentFolder = $Browser.currentFolder();

			// Set folder path
			self.folderPath()
				.html(currentFolder.meta().friendlyPath);

			self.folderInput()
				.focus()[0]
				.select();
		},

		"{folderInput} keyup": function(el, event) {

			if (event.keyCode==13) {
				self.confirmCreateFolderButton().click();
			}
		},

		"{confirmCreateFolderButton} click": function() {

			var folderName = $.trim(self.folderInput().val());

			// Don't do anything if folder name not given
			if (folderName==="") return;

			// Get friendly path of the new folder
			var parentMeta = $Browser.currentFolder().meta(),
				path = parentMeta.friendlyPath + DS + folderName;

				// and set it to the folder creation path
				self.folderCreationPath()
					.html(path);

			// Show progress state
			var createFolderPrompt = $Prompt.get("createFolderPrompt");
				createFolderPrompt.state("progress");

			// Create folder on server
			$Library.createFolder(parentMeta, folderName)
				.done(function(meta){

					var item = $Browser.createFolder(meta);

					createFolderPrompt.hide();

					$Browser.activateItem(item);
				})
				.fail(function(message){

					self.folderCreationFailedReason()
						.html(message);

					createFolderPrompt.state("fail");
				});
		},

		// Remove item prompt
		"{removeItemButton} click": function() {

			self.removeItemFilename()
				.html(self.item.meta().title);

			// Show prompt
			$Prompt.get("removeItemPrompt")
				.show()
				.state("default");
		},

		"{confirmRemoveItemButton} click": function(el) {

			var removeItemPrompt = $Prompt.get("removeItemPrompt");

			removeItemPrompt.state("progress");

			$Library.removeRemoteMeta(self.item.key)
				.done(function(){

					removeItemPrompt.hide();
				})
				.fail(function(message){

					self.removeItemFailedReason()
						.html(message);

					removeItemPrompt.state("fail");
				});
		},

		"{flickrLoginButton} click": function(el) {

			var login = el.data("login"),

				callback = el.data("callback"),

				activateFlickrPlace = $Browser.enqueue(function(){

					$Browser.activatePlace("flickr")
						.done(
							$Browser.enqueue(function(place, baseFolder){

								$Browser.activateItem(baseFolder);
							})
						);

				});

				window[callback] = function() {

					var place = $Library.getPlace("flickr");

					// Flickr is now associated
					place.options.associated = true;

					// Reactivate flickr place
					activateFlickrPlace();
				}

			window.open(login, "Flickr Login", 'scrollbars=no, resizable=no, width=650, height=700');
		},

		"{cancelSearchButton} click": function() {
			$Browser.clearSearch(true);
		},

		"{retryPopulateButton} click": function() {

			var place = self.item.place();
			delete place.activator;
			place.populated = false;
			place.treeItem.click();
		}
	}}
);

// controller: end

module.resolve();

});
// require: end

});
// module: end

// module: start
EasyBlog.module("media/browser.item", function($) {

var module = this;

EasyBlog.Controller("Media.Browser.Item",

	{
		defaultOptions: {

			"{itemTitle}": ".itemTitle",
			"{itemIcon}": ".itemIcon",
			"{childItem}": ".browserItem",

			hasCustomHandler: ["folder"]
		}
	},

	// Instance properties
	function(self) {

		var $Media, $Library, $Browser;

		return {

			init: function() {

				$Media = self.media;
				$Library = $Media.library;
				$Browser = $Media.browser;

				// Store a reference to the controller inside item data
				// and also add a item-type class.
				self.element
					.data("item", self)
					.addClass("item-type-" + self.meta().type);

				// Bind to the remove event
				self.meta().data.on("removed", function(){
					$Browser.removeItem(self);
				});

				// Create item handler
				self.createHandler();
			},

			meta: function() {
				return $Library.getMeta(self.key);
			},

			place: function() {
				return $Library.getPlace(self.key);
			},

			parentFolder: function() {
				return $Browser.getItem($Library.getParentKey(self.key));
			},

			createHandler: function() {

				if ($.inArray(self.meta().type, self.options.hasCustomHandler) < 0) return;

				var ItemHandler = EasyBlog.Controller.Media.Browser.Item[$.String.capitalize(self.meta().type)];

				if (ItemHandler===undefined) {

					EasyBlog.require()
						.script("media/browser.item." + self.meta().type)
						.done(function() {
							self.createHandler();
						});

					return;
				}

				self.handler = new ItemHandler(
					self.element,
					{
						controller: {
							media: self.media,
							item: self
						}
					}
				);
			},

			activate: function() {

				self.setLayout();

				if (self.handler) {
					self.handler.activate();
				}
			},

			remove: function() {

				try {

					if (self.element) {
						self.element.detach().remove();
					}

					// Destroy handler
					// TODO: This fails because this.element.data("controllers") is undefined.
					if (self.handler) {
						if (!self.handler._destroyed) {
							self.handler.destroy();
						}
					}

				} catch(e) {
				}
			},

			isVisible: function() {

				// TODO: Optimize routines using seed
				var itemElement = self.element,
					itemHeight  = itemElement.outerHeight(),
					itemTop     = itemElement.offset().top,
					itemBottom  = itemTop + itemHeight,
					itemField       = self.media.browser.itemField(),
					itemFieldTop    = itemField.offset().top,
					itemFieldBottom = itemFieldTop + itemField.height();

					isVisible = !((itemTop < itemFieldTop    && itemBottom < itemFieldTop) ||
						          (itemTop > itemFieldBottom && itemBottom > itemFieldBottom));

					// #debug:start
					if (self.media.options.debug.itemVisiblity) {

						self.media.console("info", [
							"Item visibility",
							{
								title: self.meta().title,
								isVisible: isVisible,
								item: self,
								itemHeight: itemHeight,
								itemTop: itemTop,
								itemBottom: itemBottom,
								itemFieldTop: itemFieldTop,
								itemFieldBottom: itemFieldBottom
							}
						]);
					}
					// #debug:end

				return isVisible;
			},

			setLayout: function(animate) {

				// Nothing to be done for folders
				if (self.meta().type=="folder") return;

				// Call handler's setLayout if exists
				if (self.handler && $.isFunction(self.handler.setLayout)) {

					return self.handler.setLayout();
				}

				self.setIcon();
			},

			setIcon: function() {

				// If icon is loading, skip.
				if (self.setIcon.loading || self.setIcon.loaded) return;

				// If no icon given or item has been destroyed, skip.
				if (self.meta().icon===undefined || self._destroyed) return;

				self.setIcon.loading = true;

				$Browser.iconThread.addDeferred(function(thread) {

					var itemIcon = self.itemIcon();

					// Save on calculating this
					// if we rely on a set layout seed
					if (!self.isVisible()) {

						self.setIcon.loading = false;

						thread.reject();

					} else {

						var meta = self.meta(),
							place = self.place(),
							iconUrl = meta.icon.url;

						if (!self.setIcon.useNaturalUrl &&
							!/easysocial|jomsocial|flickr/.test(place.id) &&
							meta.type==="image") {

							iconUrl = EasyBlog.baseUrl
							          + "&view=media&layout=getIconImage"
							          + "&place=" + encodeURIComponent(place.id)
							          + "&path="  + encodeURIComponent(self.meta().path)
							          + "&format=image&tmpl=component";
						}

						self.element.addClass("loading-icon");

						itemIcon
							.image("get", iconUrl)
							.done(function(){

								self.element.removeClass("loading-icon");

								self.setIcon.loaded = true;
								self.setIcon.loading = false;

								thread.resolve();
							})
							.fail(function(){

								self.element.removeClass("loading-icon");

								self.setIcon.loaded = false;
								self.setIcon.loading = false;

								thread.reject();

								if (!self.setIcon.triedNaturalUrl) {
									self.setIcon.useNaturalUrl = true;
									self.setIcon.triedNaturalUrl = true;
								}
							});
					}
				});
			}

		}
	}

);

EasyBlog.Controller("Media.Browser.Item.Folder",

	{
		defaultOptions: {
			"{childItem}": ".browserItem"
		}
	},

	// Instance properties
	function(self) {

		var $Media, $Library, $Browser;

		return {

			init: function() {

				$Media = self.media;
				$Library = $Media.library;
				$Browser = $Media.browser;

				self.element.empty();
			},

			items: {},

			// This is to make sure parent class's setLayout isn't called.
			setLayout: function() {

				var place = self.item.place(),
					status;

				switch (place.ready.state()) {

					case "pending":
						status = "loading";
						break;

					case "rejected":
						status = "error";
						break;

					case "resolved":

						if (self.folderView && self.folderView.map.length > 0) {

							status = "ready";
							$Browser.browserPagination().show();

						} else {

							switch (place.populate.task.state()) {

								case "pending":
									status = "loading";
									break;

								case "rejected":
									status = "error";
									break;

								case "resolved":

									if (self.folderView && self.folderView.map.length < 1) {

										status = place.acl.canUploadItem ? "empty canUpload" : "empty";

										if (self.item.meta().place==="jomsocial" && self.item.meta().path===$Media.options.directorySeparator) {
											status = "selectAlbum";
										}

										if (self.item.meta().place==="easysocial" && self.item.meta().path===$Media.options.directorySeparator) {
											status = "selectAlbum";
										}

									} else {

										status = "ready";
									}

									$Browser.browserPagination().toggle(!/empty|selectAlbum/.test(status));
									break;
							}
						}
						break;
				}

				$Browser.currentFolderStatus(status);

				$Browser.setItemLayout();

				if(self.isChangingPage) {
					self.isChangingPage = false;
				} else {
					self.populatePages();
				}
			},

			populate: function(files) {

				if ($Browser.itemField().hasClass("searching")) return;

				var i, l = files.length;

				var _items = self.items;
					self.items = {};

				// If there is nothing to show
				if (l<1) {

					// Detach everything
					try {
						// Wrapped in try catch because deleted items by may be involved.
						self.childItem().detach();
					} catch(e) {

					}

					// TODO: If this at an imposible range,
					// revert to the last possible range.
					// return;

				} else {

					for (i=0; i<l; i++) {

						var key = files[i];

						// This either return the newly created file,
						// or the file that has been previously created.
						var file = $Browser.createFile(key);

						file.element
							.appendTo(self.element);

						self.items[key] = file;
						delete _items[key];
					}

					for (key in _items) {

						// TODO: Check if this will result in error for removed item
						// Then remove the try catch
						try {
							_items[key].element.detach();
						} catch(e) {

						}
					}
				}

				// If we are populating in the background,
				// we don't need to set the item layout yet.
				if (self.item.place().itemGroup.hasClass("active") &&
					self.element.hasClass("focus")) {

					self.setLayout();
				}
			},

			activate: function() {

				if (!self.folderView) {

					self.folderView =
						self.item.meta().data.views
							.create({from: 0, to: $Browser.options.layout.maxIconPerPage});

					self.folderView
						.updated(self.populate);
				}

				self.setLayout();
			},

			populatePages: function() {

				var page = self.totalPage();

				if(page < 2) {
					$Browser.browserPagination().hide();
				} else {
					$Browser.browserPagination().show();

					$Browser.pageSelection().html('');

					for(var i = 1; i <= page; i++) {
						$Browser.view.paginationPage({
							page: i
						}).appendTo($Browser.pageSelection());
					}

					self.folderView.currentPage = self.folderView.currentPage || 1;

					$Browser.paginationPage().removeClass('selected');
					$Browser.paginationPage('.page' + self.folderView.currentPage).addClass('selected');
				}
			},

			totalPage: function() {

				var totalItems = self.folderView.map.length,
					basePage = totalItems % $Browser.options.layout.maxIconPerPage,
					page = Math.floor((basePage > 0) ? totalItems / $Browser.options.layout.maxIconPerPage + 1 : totalItems / $Browser.options.layout.maxIconPerPage);

				if($Browser.totalPage().text() != page) {
					$Browser.totalPage().text(page);
				}

				return page;
			},

			// both getter and setter
			currentPage: function(page, callback) {

				var current = parseInt(self.folderView.currentPage);

				if(isNaN(current)) {
					current = 1;

					self.folderView.currentPage = 1;

					$Browser.paginationPage().removeClass('selected');
					$Browser.paginationPage(':first').addClass('selected');
				}

				if(page===undefined) {
					page = {
						from: self.folderView.from
					};
				}

				if($.isPlainObject(page) && page.from !== undefined) {
					var totalItems = self.folderView.map.length,
						currentPage = Math.floor(page.from / $Browser.options.layout.maxIconPerPage) + 1;
				}

				page = currentPage || page;

				if(page != current) {

					var from = (page - 1) * $Browser.options.layout.maxIconPerPage,
						to = from + $Browser.options.layout.maxIconPerPage;

					if(from != page.from) {
						self.folderView.select({from: from, to: to});
					}

					self.folderView.currentPage = page;

					$Browser.paginationPage().removeClass('selected');
					$Browser.paginationPage('.page' + page).addClass('selected');

					$Browser.trigger('pageChanged', [current, page]);
				}

				callback && callback();

				return page;
			},

			next: function() {
				self.changePage('next');
			},

			prev: function() {
				self.changePage('prev');
			},

			changePage: function(type) {
				self.isChangingPage = true;

				var totalPage = self.totalPage(),
					currentPage = self.currentPage();

				if(type == 'next' && currentPage < totalPage) {
					currentPage += 1;
				}

				if(type == 'prev' && currentPage > 1) {
					currentPage -= 1;
				}

				self.currentPage(currentPage);
			},

			locateItem: function(meta) {

				var meta = $Library.getMeta(meta),
					page = self.getItemPage(meta);

				if (page) {

					self.currentPage(page, function(){
						$Browser.focusItem(meta.key, true);
					});
				}
			},

			getItemPage: function(meta) {

				var meta = $Library.getMeta(meta),
					key = meta.key,
					mapLength = self.folderView.map.length,
					matchedIndex;

				$.each(self.folderView.map, function(i, value) {
					if(value == key) {
						matchedIndex = i;
						return false;
					}
				});

				return (matchedIndex !== undefined) ? Math.floor(matchedIndex / $Browser.options.layout.maxIconPerPage) + 1 : false;
			}
		}
	}

);

module.resolve();

});
// module: end

// module: start
EasyBlog.module("media/editor", function($){

var module = this;

// require: start
EasyBlog.require()
// .library(
// 	"ui/position"
// )
// .view(
// 	"media/editor",
// 	"media/editor.viewport"
// )
.done(function(){

// controller: start
EasyBlog.Controller(

	"Media.Editor",

	{
		defaultOptions: {

			view: {
				editor: "media/editor",
				viewport: "media/editor.viewport"
			},

            "{modalHeader}": ".modalHeader",
            "{modalToolbar}": ".modalToolbar",
            "{modalContent}": ".modalContent",

            "{navigationPathway}": ".navigationPathway",

            "{insertItemButton}": ".insertItemButton",
            "{cancelEditingButton}": ".cancelEditingButton"
		}
	},

	function(self) { return {

		init: function() {

            self.element
                .addClass("editor")
                .html(self.view.editor());

			// Browser navigation
			self.navigationPathway()
				.implement(
					EasyBlog.Controller.Media.Navigation,
					{
						controller: {
							media: self.media
						},

						canActivate: false
					},
					function() {
						// Assign controller as a property of myself
						self.navigation = this;
					}
				);

			self.setLayout();
		},

		setLayout: function() {

            // Don't set layout if current modal is not us
            if (self.media.currentModal!=="editor") return;

			self.modalContent()
				.hide()
				.height(
					self.element.height() -
					self.modalHeader().outerHeight() -
					self.modalToolbar().outerHeight()
				)
				.show();

			// Also trigger set layout on handler
			var currentEditor = self.getEditor(self.currentEditor);

			if (currentEditor) {
				currentEditor.setLayout && currentEditor.setLayout();
			}
		},

		editors: [],

		handlers: [],

		loadHandler: function(type) {

			var handlerLoader = self.handlers[type];

			if (handlerLoader!==undefined) return handlerLoader;

			// Create new handler loader
			handlerLoader = $.Deferred();

			// Load handler
			handlerLoader.require =

				EasyBlog.require()
					.script(
						"media/editor." + type
					)
					.done(function(){

						var EditorHandler = EasyBlog.Controller.Media.Editor[$.String.capitalize(type)];

						if (EditorHandler!==undefined) {

							handlerLoader.resolve(EditorHandler);

						} else {

							delete self.handlers[type];

							handlerLoader.reject();
						}
					})
					.fail(function(){

						handlerLoader.reject();
					});

			return handlerLoader;
		},

		createEditor: function(key, callback) {

			// This will attempt to remove any previously created editor
			self.removeEditor(key);

			var meta = self.media.library.getMeta(key);

			// If there's no meta, skip.
			// TODO: Show error.
			if (meta===undefined) return;

			self.loadHandler(meta.type)
				.done(function(EditorHandler) {

					// Create editor & implement handler
					var editor = new EditorHandler(

						self.view.viewport()
							.addClass("editor-type-" + meta.type)
							.prependTo(self.modalContent()),

						{
							controller: {
								media: self.media,
								editor: self,
								key: self.media.library.getKey(meta)
							}
						}
					);

					// Register this editor instance
					self.editors[key] = editor;

					callback && callback(editor);
				});
		},

		removeEditor: function(key) {

			var editor = self.editors[key];

			if (editor===undefined) return;

			editor.destroy();

			delete self.editors[key];
		},

		getEditor: function(key) {

			return self.editors[key];
		},

		activateEditor: function(key) {

			self.deactivateEditor(self.currentEditor);

			// Set navigation pathway
			self.navigation.setPathway(key);

			var editor = self.getEditor(key),

				activateEditor = function(editor) {

					self.currentEditor = key;

					editor.element.addClass("active");

					editor.activate && editor.activate();
				};

			if (editor===undefined) {

				self.createEditor(key, activateEditor);

			} else {

				activateEditor(editor);
			}
		},

		deactivateEditor: function() {

			var editor = self.getEditor(self.currentEditor);

			if (editor===undefined) return;

			editor.deactivate && editor.deactivate();

			editor.element.removeClass("active");
		},

		"{self} modalActivate": function(el, event, key) {

			self.activateEditor(key);
		},

		"{self} modalDeactivate": function() {

			self.deactivateEditor();
		},

		"{insertItemButton} click": function() {

			var editor = self.getEditor(self.currentEditor);

			if (editor===undefined) return;

			editor.trigger("insertItem");
		},

		"{cancelEditingButton} click": function() {

			var editor = self.getEditor(self.currentEditor);

			if (editor) {
				editor.trigger("cancelItem");
			}

			self.media.browse();
		}
	}}

);

EasyBlog.Controller(

	"Media.Editor.Panel",

	{
		defaultOptions: {

			"{sectionHeader}": ".panelSectionHeader",
			"{sectionContent}": ".panelSectionContent"
		}
	},

	function(self) { return {

		init: function() {

		},

		// Common editor UI behaviour
		"{sectionHeader} click": function(sectionHeader) {

			var section = sectionHeader.parent();

			section.toggleClass("active");
		}
	}}
);

EasyBlog.Controller(

	"Media.Editor.Preview",

	{
		defaultOptions: {

			"{container}": ".previewContainer",
			"{dialogGroup}": ".previewDialogGroup"
		}
	},

	function(self) { return {

		init: function() {
		},

		resetLayout: function() {

			clearTimeout(self.resetLayoutTimer);

			self.resetLayoutTimer = setTimeout(function(){

				var container = self.container(),
					width = self.element.width(),
					height = self.element.height(),
					containerWidth = container.width(),
					containerHeight = container.height(),
					top = 0,
					left = 0,
					overflow = "none";

				if (containerWidth < width) {
					left = (width - containerWidth) / 2;
				} else {
					overflow = "auto";
				}

				if (containerHeight < height) {
					top = (height - containerHeight) / 2;
				} else {
					overflow = "auto";
				}

				self.element.css("overflow", overflow);

				container.css({
					top: top,
					left: left
				});

			}, 100);
		},

		showDialog: function(dialogName) {
			self.dialogGroup().addClass("show-dialog-" + dialogName);
		},

		hideDialog: function(dialogName) {
			self.dialogGroup().removeClass("show-dialog-" + dialogName);
		}
	}}
);

module.resolve();

});
// require: end

});
// module: end


EasyBlog.module('media/constrain', function($) {
	var module = this;

	EasyBlog.require().library('image').done(function() {
		$.fn.constrain = function() {
			var constrain = this.data('constrain');
			if(constrain instanceof $.Constrain) {
				if(arguments.length > 0) {
					constrain.update(arguments[0]);
				} else {
					return constrain;
				}
			} else {
				constrain = new $.Constrain(this, arguments[0]);
				this.data('constrain', constrain);
			}
		};

		$.Constrain = function(element, options) {

			var self = this;

			var defaultOptions = {
				selector: {
					width: '.inputWidth',
					height: '.inputHeight',
					constrain: '.inputConstrain'
				},
				forceConstrain: false
			}

			var userOptions = options ? options : {};

			self.options = $.extend(true, {}, defaultOptions, userOptions);

			self.options.element = {
				width: element.find(self.options.selector.width).data('type', 'width'),
				height: element.find(self.options.selector.height).data('type', 'height'),
				constrain: element.find(self.options.selector.constrain)
			}

			self.options.initial = self.options.initial || self.options.source;

			if(self.options.allowedMax !== undefined) {
				self.options.max = $.Image.resizeWithin(self.options.source.width, self.options.source.height, self.options.allowedMax.width, self.options.allowedMax.height);

				self.options.initial.width = Math.min(self.options.max.width, self.options.initial.width);
				self.options.initial.height = Math.min(self.options.max.height, self.options.initial.height);
			}

			self.options.element.width.data('initial', self.options.initial.width);
			self.options.element.height.data('initial', self.options.initial.height);

			self.fieldValues(self.calculate('width', self.options.initial));

			$.each([self.options.element.width, self.options.element.height], function(i, element) {

				var	thisType = element.data('type'),
					oppositeType = self.getOppositeType(thisType),
					element = $(element),
					opposite = self.options.element[oppositeType];

				element.bind('keyup', function(event) {
					if(event.keyCode == 9 || event.keyCode == 16) {
						return false;
					}

					self.fieldValues(self.calculate(thisType));
				});

				element.bind('blur', function() {
					if(!self.options.element.constrain.is(':checked') && $.trim(element.val()) == '') {
						element.val(self.options.initial[thisType]);
					}

					if($.trim(element.val()) == '' && $.trim(opposite.val()) == '') {
						element.val(self.options.initial[thisType]);
						opposite.val(self.options.initial[oppositeType]);
					}
				});
			});

			$(self.options.element.constrain).bind('change', function() {
				if($(this).is(':checked')) {
					var values = self.fieldValues(),
						type = (values.width === '') ? 'height' : 'width';

					self.fieldValues(self.calculate(type));
				}
			});
		};

		$.extend($.Constrain.prototype, {
			calculate: function(thisType, values) {
				var self = this,
					values = (values !== undefined) ? values : self.fieldValues(),
					oppositeType = self.getOppositeType(thisType),
					thisMax = self.options.max ? self.options.max[thisType] : undefined,
					oppositeMax = self.options.max ? self.options.max[oppositeType] : undefined,
					thisSource = self.options.source[thisType],
					oppositeSource = self.options.source[oppositeType],
					thisVal = values[thisType],
					oppositeVal = values[oppositeType];

				thisVal = (thisVal != '' && thisMax && thisVal > thisMax) ? thisMax : thisVal;

				if(this.enforceConstrain()) {
					if(thisVal == '') {
						oppositeVal = '';
					} else {
						var ratio = thisSource / thisVal;

						oppositeVal = Math.round(oppositeSource / ratio);

						if(oppositeMax && oppositeVal > oppositeMax) {
							oppositeVal = oppositeMax;
						}
					}
				}

				var result = {};
				result[thisType] = thisVal;
				result[oppositeType] = oppositeVal;
				return result;
			},

			getOppositeType: function(type) {
				return (type == 'width') ? 'height' : 'width';
			},

			getInput: function(type) {
				var self = this,
					value = self.options.element[type].val();

				value = $.trim(value);
				value = value.replace(new RegExp('[^0-9.]','g'), "");
				value = parseInt(value, 10);

				return isNaN(value) ? '' : value;
			},

			fieldValues: function(values) {
				var self = this,
					result = {};

				if(values === undefined) {
					// getter
					result.width = self.getInput('width');
					result.height = self.getInput('height');

				} else {
					// setter
					self.options.element.width.val(Math.floor(values.width));
					self.options.element.height.val(Math.floor(values.height));
					result = values;
				}

				return result;
			},

			enforceConstrain: function() {
				var self = this;
				return self.options.forceConstrain ? true : (self.options.element.constrain.length < 1) ? true : self.options.element.constrain.is(':checked');
			},

			update: function(options) {
				var self = this;

				// set initial first because initial might have previous source value
				self.options.initial = options.initial || options.source || self.options.initial;

				if(options.allowedMax !== undefined && options.source !== undefined) {
					options.max = $.Image.resizeWithin(options.source.width, options.source.height, options.allowedMax.width, options.allowedMax.height);

					self.options.initial.width = Math.min(options.max.width, self.options.initial.width);
					self.options.initial.height = Math.min(options.max.height, self.options.initial.height);
				}

				self.options = $.extend(true, {}, self.options, options);

				values = this.calculate('width', {
					width: self.options.initial.width || self.options.source.width,
					height: self.options.initial.height || self.options.source.height
				});

				this.fieldValues(values);
			}
		});

		module.resolve();
	});
});

// module: start
EasyBlog.module("media/editor.audio", function($){

var module = this;

// require: start
EasyBlog.require()
.view(
	"media/editor.audio",
	"media/editor.audio.player"
)
.done(function() {

// controller: start
EasyBlog.Controller(

	"Media.Editor.Audio",

	{
		defaultOptions: {

			view: {
				panel: "media/editor.audio",
				player: "media/editor.audio.player"
			},

			player: {
				width: 400,
				height: 24,
				autostart: false,
				controlbar: "bottom",
				backcolor: "#333333",
				frontcolor: "#ffffff",
				modes: [
					{
						type: 'html5'
					},
					{
						type: 'flash',
						src: $.rootPath + "/components/com_easyblog/assets/vendors/jwplayer/player.swf"
					},
					{
						type: 'download'
					}
				]
			},

			"{editorPreview}": ".editorPreview",
			"{editorPanel}": ".editorPanel",

			"{playerContainer}": ".playerContainer",

			// Insert options
			"{autoplay}": ".autoplay"
		}
	},

	function(self) {

		var $Media, $Library, $Browser;

		return {

		init: function() {

			$Media = self.media;
			$Library = $Media.library;
			$Browser = $Media.browser;

			var meta = self.meta(),
				place = self.place();

			// Panel
			self.editorPanel()
				.html(self.view.panel({
					meta: meta
				}))
				.implement(
					EasyBlog.Controller.Media.Editor.Panel,
					{},
					function() {

						// Keep a reference to this controller
						self.panel = this;
					}
				);

			// Preview
			self.editorPreview()
				.implement(
					EasyBlog.Controller.Media.Editor.Preview,
					{},
					function() {

						// Keep a reference to this controller
						self.preview = this;

						self.initPlayer();
					}
				);
		},

		initPlayer: function() {

			EasyBlog.require()
				.script($.rootPath + "/components/com_easyblog/assets/vendors/jwplayer/jwplayer.js")
				.done(function($) {

					var meta = self.meta(),

						place = self.place(),

						id = "player-" + $.uid(),

						options = $.extend(self.options.player, {
							id: id,
							file: self.meta().url,
						}),

						player = self.view.player({
							id: id,
							meta: meta,
							options: options
						});

					// Append player container
					self.preview.container()
						.append(player);

					self.player = jwplayer(id).setup(options);

					self.preview.resetLayout();
				});
		},

		meta: function() {
			return $Library.getMeta(self.key);
		},

		place: function() {
			return $Library.getPlace(self.meta().place);
		},

		setLayout: function() {

		},

		stop: function() {

			if (self.player) {

				if (self.player.getState()=="PLAYING") {

					self.player.pause();
				}
			}
		},

		deactivate: function() {

			self.stop();
		},

		"{self} cancelItem": function() {

			self.stop();
		},

		//
		// Insert audio
		//

		"{self} insertItem": function() {
			var options = {
				autostart: (self.autoplay().val() == '1') ? true : false,
			}

			$Media.insert(self.meta(), options);
		}
	}}

);
// controller: end

module.resolve();

});
// require: end

});
// module: end

// module: start
EasyBlog.module("media/editor.file", function($){

var module = this;

// require: start
EasyBlog.require()
.view(
	"media/editor.file",
	"media/editor.file.preview"
)
.done(function() {

// controller: start
EasyBlog.Controller(

	"Media.Editor.File",

	{
		defaultOptions: {

			view: {
				panel: "media/editor.file",
				preview: "media/editor.file.preview"
			},

			"{editorPreview}": ".editorPreview",
			"{editorPanel}": ".editorPanel",

			// Preview
			"{filePreviewCaption}" : ".filePreviewCaption",

			// Insert button
			"{insertItemButton}": ".insertItemButton",
			"{insertItemDetail}": ".insertItemDetail",

			// Insert options
			"{insertCaption}"	: ".insertCaption",
			"{insertAs}"		: ".insertAs"
		}
	},

	function(self) {

		var $Media, $Library, $Browser;

		return {

		init: function() {

			$Media = self.media;
			$Library = $Media.library;
			$Browser = $Media.browser;

			var meta = self.meta();

			// Panel
			self.editorPanel()
				.html(self.view.panel({
					meta: meta
				}))
				.implement(
					EasyBlog.Controller.Media.Editor.Panel,
					{},
					function() {

						// Keep a reference to this controller
						self.panel = this;
					}
				);

			// Preview
			self.editorPreview()
				.implement(
					EasyBlog.Controller.Media.Editor.Preview,
					{},
					function() {

						// Keep a reference to this controller
						self.preview = this;

						self.generatePreview();
					}
				);
		},

		generatePreview: function() {
			var preview = self.preview.container().find('a'),
				target = self.insertAs().val(),
				content = self.insertCaption().val();

			if(preview.length < 1) {
				var meta = self.meta();

				self.preview.container().html(self.view.preview({
					meta: meta,
					target: target,
					content: content
				}));
			} else {
				preview.attr('target', target).text(content);
			}
			self.preview.resetLayout();
		},

		setLayout: function() {

		},

		meta: function() {
			return $Library.getMeta(self.key);
		},

		place: function() {
			return $Library.getPlace(self.meta().place);
		},

		"{self} insertItem": function() {

			var meta = self.meta(),
				options = {
					title: meta.title,
					target: self.insertAs().val(),
					content: self.insertCaption().val()
				};

			$Media.insert(self.meta(), options);
		},

		"{insertCaption} keyup" : function(el) {
			self.generatePreview();
		},

		"{insertCaption} blur": function(el) {
			if(el.val() == '') {
				var meta = self.meta();

				el.val(meta.title);

				self.generatePreview();
			}
		},

		"{insertAs} change": function(el) {
			self.generatePreview();
		}
	}}

);
// controller: end

module.resolve();

});
// require: end

});
// module: end

// module: start
EasyBlog.module("media/editor.image", function($){

var module = this;

// require: start
EasyBlog.require()
.library(
	"ui/position"
)
.script(
	'media/constrain'
)
.view(
	"media/editor.image",
	"media/editor.image.variation",
	"media/editor.image.caption"
)
.done(function() {

EasyBlog.Controller(

	"Media.Editor.Image",
	{
		defaultOptions: {

			view: {
				panel: "media/editor.image",
				variation: "media/editor.image.variation",
				caption: "media/editor.image.caption"
			},

			defaultVariation: "thumbnail",

			"{editorPreview}": ".editorPreview",
			"{editorPanel}": ".editorPanel",

			// Variation list
			"{imageVariationPanel}": ".imageVariationPanel",
			"{imageVariationList}" : ".imageVariationList",
			"{imageVariations}"    : ".imageVariations",
			"{imageVariation}"     : ".imageVariation",

			// Enforce Dimension
			"{imageEnforceDimensionOption}"	: ".imageEnforceDimensionOption",
			"{imageEnforceWidth}"			: ".imageEnforceWidth",
			"{imageEnforceHeight}"			: ".imageEnforceHeight",

			// Caption
			"{imageCaptionOption}"	: ".imageCaptionOption",
			"{imageCaption}"		: ".imageCaption",

			// Zoom
			"{imageZoomOption}"				: ".imageZoomOption",
			"{imageZoomLargeImageSelection}": ".imageZoomLargeImageSelection",

			// File properties
			"{itemFilesize}": ".itemFilesize",
			"{itemFilename}": ".itemFilename",
			"{itemUrl}"		: ".itemUrl",
			"{itemCreationDate}": ".itemCreationDate",

			// Prompt
			"{modalPrompt}": ".modalPrompt"
		}
	},

	function(self) {

		var $Media, $Library, $Browser, $Prompt;

		return {

		init: function() {

			$Media = self.media;
			$Library = $Media.library;
			$Browser = $Media.browser;

			var meta = self.meta(),
				place = self.place(),
				subcontrollerOptions = {
					controller: {
						editor: self,
						media: self.media
					}
				};

			// Panel
			self.editorPanel()
				.html(self.view.panel({
					meta: meta,
					acl: place.acl,
					enableLightbox: $Media.options.exporter.image.lightbox,
					enforceImageDimension: $Media.options.exporter.image.enforceDimension,
					enforceImageWidth: $Media.options.exporter.image.enforceWidth,
					enforceImageHeight: $Media.options.exporter.image.enforceHeight
				}))
				.implement(
					EasyBlog.Controller.Media.Editor.Panel,
					{},
					function() {

						// Keep a reference to this controller
						self.panel = this;

						// Don't show file size when editing jomsocial image
						// because we are unable to retrieve them.
						if (meta.place==="jomsocial" || meta.place == 'easysocial') {
							self.itemFilesize().remove();
							self.itemFilename().css("padding-right", 0);
						}
					}
				);

			self.modalPrompt()
				.implement(EasyBlog.Controller.Media.Prompt, subcontrollerOptions, function() {
					$Prompt = self.promptDialog = this;
				});


			// Image filters
			var Filter = EasyBlog.Controller.Media.Editor.Image.Filter;

			self.element
				.implement(Filter.Dimension, subcontrollerOptions)
				.implement(Filter.Caption,   subcontrollerOptions)
				.implement(Filter.Lightbox,  subcontrollerOptions);

			// Preview
			self.editorPreview()
				.implement(
					EasyBlog.Controller.Media.Editor.Preview,
					{
						draggable: true
					},
					function() {

						// Keep a reference to this controller
						self.preview = this;

						// Attempt to load thumbnail image first
						self.previewImage(self.meta().thumbnail.url);
					}
				)

			// Variation form
			if (place.acl.canCreateVariation && place.acl.canDeleteVariation) {

				self.element
					.implement(EasyBlog.Controller.Media.Editor.Image.VariationForm, subcontrollerOptions);
			}

			self.populateImageVariations();

			self.setLayout();
		},

		meta: function() {
			return $Library.getMeta(self.key);
		},

		place: function() {
			return $Library.getPlace(self.meta().place);
		},

		setLayout: function() {

			self.preview.resetLayout();
		},

		"{self} insertItem": function() {
			var variation = self.currentImageVariation().data("variation"),
				options = {
					variation: variation.name
				};

			if(self.imageEnforceDimensionOption().is(":checked")) {
				options.enforceDimension = true;
				options.enforceWidth = self.imageEnforceWidth().val();
				options.enforceHeight = self.imageEnforceHeight().val();
			}

			if(self.imageCaptionOption().is(":checked")) {
				options.caption = self.imageCaption().val();
			}

			if(self.imageZoomOption().is(":checked")) {
				options.zoom = self.imageZoomLargeImageSelection().val();
			}

			$Media.insert(self.meta(), options);
		},

		//
		// Image variation
		//

		populateImageVariations: function() {

			var meta = self.meta(),
				variations = meta.variations;

			self.imageVariationsData = self.imageVariationsData || {};

			if (variations===undefined) {

				// Show loading indicator
				self.imageVariations()
					.empty()
					.addClass("busy");

				// Get file variations from server
				$Library.getMetaVariations(meta.key)
					.done(function(){

						// Try to populate variations again
						self.populateImageVariations();

						self.imageVariations()
							.removeClass("busy");
					})
					.fail(function() {

						// Ask user to try again on the preview screen
					})
					.always(function() {

						self.imageVariations()
							.removeClass("busy");
					});

				return;
			}

			$.each(variations, function(i, variation) {

				// Skip icon variation
				if (variation.name=="icon") return;

				self.createImageVariation(variation);
			});

			self.trigger("variationPopulated", [self.imageVariationsData]);
		},

		createImageVariation: function(variation) {

			var imageVariation = self.view.variation({variation: variation});

			imageVariation
				.data("variation", variation)
				.appendTo(self.imageVariations());

			// Add default class if this is a default variation
			if (variation["default"]===true || variation["default"]=="true") {
				imageVariation.addClass("default");
			}

			// If this variation can't be deleted, e.g. thumbnail, original,
			// then add a lock indicator.
			if (!variation.canDelete) {
				imageVariation.addClass("locked");
			}

			self.imageVariationsData[variation.name] = imageVariation;

			self.trigger("variationCreated", [imageVariation, variation]);

			return imageVariation;
		},

		"{self} variationPopulated": function() {

			// Find default variation and highlight default variation
			var variationName,
				defaultImageVariation = self.imageVariation(".default");

			if(self.imageVariation().length > 0) {

				if (defaultImageVariation.length < 1) {

					var meta = self.meta(),
						image = self.previewImage();


					if (image!==undefined) {

						$.each(meta.variations, function(i, variation) {

							if(variation.width == image.width() && variation.height == image.height()) {
								variationName = variation.name;
								return false;
							}
						});

					}

					variationName = variationName || self.imageVariation(":first").data("variation").name;

				} else {

					variationName = defaultImageVariation.eq(0).data("variation").name;
				}

				self.currentImageVariation(variationName);
			}

		},

		"{imageVariation} click": function(imageVariation) {
			var variation = imageVariation.data("variation");

			self.currentImageVariation(variation.name);
		},

		currentImageVariation: function(variationName) {

			var currentImageVariation = self.currentImageVariation.imageVariation,

				imageVariation = self.imageVariationsData[variationName];

			if (imageVariation!==undefined) {

				var variation = imageVariation.data("variation");

				var meta = self.meta();

				if(meta.place == 'jomsocial' || meta.place == 'easysocial') {
					var image = self.previewImage();
					variation.width = (image) ? image.data('width') : 0;
					variation.height = (image) ? image.data('height') : 0;
					$('<span class="variationDimension"></span>').text(variation.width + 'x' + variation.height).appendTo(imageVariation);
				}

				// Deactivate current image variation
				if (currentImageVariation) {
					currentImageVariation.removeClass("active");
				}

				imageVariation.addClass("active");

				self.currentImageVariation.imageVariation = imageVariation;

				self.trigger("variationSelected", [imageVariation, variation]);
			}

			return self.currentImageVariation.imageVariation;
		},

		"{self} variationSelected": function(el, event, imageVariation, variation) {

			self.itemFilesize()
				.html(variation.filesize);

			self.itemUrl()
				.html(variation.url);

			self.itemCreationDate()
				.html(variation.dateCreated);

			self.previewImage(variation.url);
		},

		"{self} variationRemoved": function(el, event, imageVariation, variation) {
			delete self.imageVariationsData[variation.name];
			imageVariation.remove();
			// $Library.removeMetaVaration(self.meta(), variation.name);
		},

		previewImage: function(url) {

			// No url given, return.
			if (url===undefined) {
				return self.previewImage.currentImage;
			};

			// Create a collection of image previews (if this is the first time)
			if (self.previewImage.images===undefined) {
				self.previewImage.images = {};
			}

			var image        = self.previewImage.images[url],
				currentUrl   = self.previewImage.currentUrl,
				currentImage = self.previewImage.images[currentUrl];

			// Show loading indicator
			self.preview.showDialog("loading");

			// Detach current image
			if (currentImage!==undefined && !$.isDeferred(currentImage)) {
				currentImage.detach();
				self.preview.container().empty();
			}

			// Store a copy of the current url
			self.previewImage.currentUrl = url;

			// If image hasn't been loaded
			if (image===undefined) {

				// Load image
				self.previewImage.images[url]  =
					$.Image.get(url)
						.done(function(image) {

							self.previewImage.images[url] = image;

							// If current url has changed, don't show this one.
							if (self.previewImage.currentUrl==url) {
								self.previewImage(url);
							}
						})
						.fail(function() {

							self.preview.hideDialog("loading");

							// If current url is still the same, show error message
							if (self.previewImage.currentUrl==url) {

								// TODO: Show error message
							}
						});

				return;
			}

			// If image is still loading
			if ($.isDeferred(image)) {
				return;
			}

			self.preview.container()
				.append(image);

			self.previewImage.currentImage = image;

			self.trigger("previewImage", [self.preview.container(), image]);

			// Hide loading indicator
			self.preview.hideDialog("loading");

			self.preview.resetLayout();
		}

	}}

);

EasyBlog.Controller(

	"Media.Editor.Image.VariationForm",

	{
		defaultOptions: {

			// Variation form
			"{imageVariationForm}"		: ".imageVariationForm",
			"{addVariationButton}"		: ".addVariationButton",
			"{createVariationButton}"	: ".createVariationButton",
			"{removeVariationButton}"	: ".removeVariationButton",
			"{cancelVariationButton}"	: ".cancelVariationButton",
			"{tryCreateVariationButton}": ".tryCreateVariationButton",
			"{newVariationName}"		: ".newVariationName",
			"{newVariationWidth}"		: ".newVariationWidth",
			"{newVariationHeight}"		: ".newVariationHeight",
			"{newVariationRatio}"		: ".newVariationRatio",
			"{newVariationLockRatio}"	: ".newVariationLockRatio",
			"{imageVariationMessage}"	: ".imageVariationMessage",
			variationNameFilter			: new RegExp('[^a-zA-Z0-9]','g'),

			// Variation prompt
			"{createNewImageVariationPrompt}"	: ".createNewImageVariationPrompt",
			"{promptVariationName}"				: ".createNewImageVariationPrompt .variationName",
			"{promptVariationWidth}"			: ".createNewImageVariationPrompt .variationWidth",
			"{promptVariationHeight}"			: ".createNewImageVariationPrompt .variationHeight"
		}
	},

	function(self) { return {

		init: function() {
		},

		"{self} variationSelected": function() {
			var variation = self.editor.currentImageVariation().data('variation');

			self.removeVariationButton()
				.toggle(variation.canDelete);
		},

		nextVariationName: function(name) {

			var match = false,
				name = $.trim(name.toLowerCase());

			$.each(self.editor.imageVariationsData, function(i, variation) {
				if (name==variation.data('variation').name.toLowerCase()) {

					match = true;

					var suffix = name.substr(-1, 1);

					name = ($.isNumeric(suffix)) ?
								name.substr(0, name.length - 1) + (parseInt(suffix, 10) + 1) :
								name + 1;

					return false;
				}
			});

			return (match) ? self.nextVariationName(name) : name;
		},

		"{addVariationButton} click": function() {

			self.editor.promptDialog
				.get('createNewImageVariationPrompt')
				.state('default')
				.show();

			var variation = self.editor.currentImageVariation().data('variation');
				variationName = $.String.capitalize(self.nextVariationName(variation.name));

			self.newVariationName()
				.data("default", variationName)
				.val(variationName)
				.select();

			self.newVariationWidth()
				.data("default", variation.width)
				.val(variation.width);

			self.newVariationHeight()
				.data("default", variation.height)
				.val(variation.height);

			self.imageVariationForm().constrain({
				selector: {
					width: self.options["{newVariationWidth}"],
					height: self.options["{newVariationHeight}"],
					constrain: self.options["{newVariationLockRatio}"]
				},
				source: {
					width: variation.width,
					height: variation.height
				},
				allowedMax: {
					width: self.editor.media.options.exporter.image.maxVariationWidth,
					height: self.editor.media.options.exporter.image.maxVariationHeight
				}
			})
		},

		"{newVariationRatio} click": function(el) {

			el.toggleClass("locked");

			if (el.hasClass("locked")) {
				self.newVariationLockRatio().attr('checked', 'checked');
			} else {
				self.newVariationLockRatio().removeAttr('checked');
			}

			self.newVariationLockRatio().trigger('change');
		},

		"{createVariationButton} click": function() {
			self.createVariation();
		},

		"{tryCreateVariationButton} click": function() {
			self.createVariation();
		},

		"{newVariationName} keyup": function(el, event) {
			var value = $.trim($(el).val());
			value = value.replace(new RegExp('[^0-9a-zA-Z]','g'), "");
			$(el).val(value);

			if(event.keyCode == 13) {
				self.createVariationButton().trigger('click');
			}
		},

		"[{newVariationWidth}, {newVariationHeight}] keyup": function(el, event) {
			if(event.keyCode == 13) {
				self.createVariationButton().trigger('click');
			}
		},

		createVariation: function() {
			var meta = self.editor.meta(),
				place = self.editor.place(),
				name = self.newVariationName().val(),
				width = self.newVariationWidth().val(),
				height = self.newVariationHeight().val();

			if(!$.trim(name) || !$.trim(width) || !$.trim(height)) {
				return false;
			}

			self.promptVariationName().text(name);
			self.promptVariationWidth().text(width);
			self.promptVariationHeight().text(height);

			self.editor.promptDialog
				.get('createNewImageVariationPrompt')
				.state('progress')
				.show();

			EasyBlog.ajax(
				"site.views.media.createVariation",
				{
					path: meta.path,
					place: place.id,
					name: name,
					width: width,
					height: height
				},
				{
					success: function( variation ) {

						self.media.library.meta[meta.key].variations.push(variation);

						self.editor.createImageVariation(variation);

						self.editor.currentImageVariation(variation.name);

						self.cancelVariationButton().click();
					},
					fail: function( message ) {
						self.editor.promptDialog
							.get('createNewImageVariationPrompt')
							.state('fail')
							.show();
					}
				}
			);
		},

		"{removeVariationButton} click": function() {

			var imageVariation = self.editor.imageVariation(".active"),
				variation = imageVariation.data("variation"),
				meta = self.editor.meta(),
				place = self.editor.place();

			if (variation.canDelete) {

				EasyBlog.ajax(

					"site.views.media.deleteVariation",

					{
						"fromPath": meta.path,
						"place": place.id,
						'name': variation.name
					},

					{
						beforeSend: function() {

							imageVariation.addClass("busy");
						},

						success: function() {

							// Once the item is successfully removed, we need to remove this variation.
							imageVariation.slideUp(function(){
								self.trigger('variationRemoved', [imageVariation, variation]);
							});

							// Revert to default image variation
							self.editor.imageVariation(".default")
								.click();

							self.media.library.removeMetaVariation(meta, variation.name);
						},

						fail: function(message) {

							try { console.log(message); } catch(e) {};
						},

						complete: function() {

							imageVariation.removeClass("busy");
						}
					}
				);
			}
		}
	}}
);


EasyBlog.Controller(
	"Media.Editor.Image.Filter.Caption",
	{
		defaultOptions: {
			view: {
				caption: "media/editor.image.caption"
			},

			"{imageVariation}"		: ".imageVariation",
			"{imageCaptionOption}"	: ".imageCaptionOption",
			"{imageCaption}"		: ".imageCaption"
		}
	},
	function(self) { return {

		init: function() {
			self.item = {
				meta: self.editor.meta()
			}
		},

		"{imageVariation} click": function(el) {
			self.transform();
		},

		"{self} dimensionEnforced": function() {
			self.transform();
		},

		"{imageCaptionOption} change": function(el, event) {

			event.stopPropagation();

			el.parent(".field").toggleClass("hide-field-content", !el.is(":checked"));

			self.transform();
		},

		"{imageCaptionOption} mouseup": function() {

			setTimeout(function(){
				self.imageCaption().focus()[0].select();
			}, 1);
		},

		"{imageCaption} blur": function(el) {
			if($.trim(el.val()) == '') {
				el.val(self.item.meta.title);
			}

			self.transform();
		},

		"{imageCaption} keyup": function(el, event) {
			self.transform();
		},

		transform: function() {
			var previewContainer = self.editor.preview.container(),
				image = previewContainer.find('img'),
				captionText = previewContainer.find('div.imageCaptionText');

			if(self.imageCaptionOption().is(':checked')) {
				var caption = self.imageCaption().val();

				captionText.remove();

				previewContainer.width(image.width());

				previewContainer.addClass('imageCaptionBorder');

				previewContainer.width(previewContainer.width());

				previewContainer.append(self.view.caption({
					caption: caption
				}));
			} else {
				previewContainer.removeClass('imageCaptionBorder');

				captionText.remove();

				previewContainer.width("auto");
			}

			self.editor.preview.resetLayout();
		}
	}}
);

EasyBlog.Controller(
	"Media.Editor.Image.Filter.Lightbox",
	{
		defaultOptions: {
			defaultImageZoomVariation       : "original",
			"{imageZoomOption}"				: ".imageZoomOption",
			"{imageZoomLargeImageSelection}": ".imageZoomLargeImageSelection",
			"{imageZoomLargeImageOption}"	: ".imageZoomLargeImageSelection option"
		}
	},
	function(self) { return {

		init: function() {

		},

		"{self} variationCreated": function(el, event, imageVariation, variation) {

			// Also add to insert options
			var variationName = $.String.capitalize(variation.name),
				largeImageOption = $("<option>")
										.val(variationName)
										.html(variationName)
										.data("variation", variation);

			var defaultSelectedVariationName =
					self.media.options.exporter.image.zoom ||
					self.options.defaultImageZoomVariation;

			if (variation.name==defaultSelectedVariationName) {
				largeImageOption.attr("selected", true);
			}

			self.imageZoomLargeImageSelection()
				.append(largeImageOption);
		},

		"{self} variationRemoved": function(el, event, imageVariation, variation) {
			self.imageZoomLargeImageOption('[value="' + variation.name + '"]').remove();
		},

		"{imageZoomOption} change": function(el, event) {

			event.stopPropagation();

			el.parent(".field").toggleClass("hide-field-content", !el.is(":checked"));
		},

		transform: function() {

			// Enable image zooming
			if (self.imageZoomOption().is(":checked")) {

				var largeImageVariation =
					self.imageZoomLargeImageOption(":selected").data("variation");

				image = $("<a>")
					.addClass("easyblog-thumb-preview")
					.attr({
						href: largeImageVariation.url,
						title: imageCaption || self.item.meta.title
					})
					.html(image);
			}
		}
	}}
);

EasyBlog.Controller(
	"Media.Editor.Image.Filter.Dimension",
	{
		defaultOptions: {
			"{imageEnforceDimension}"		: ".imageEnforceDimension",
			"{imageEnforceDimensionOption}"	: ".imageEnforceDimensionOption",
			"{imageEnforceWidth}"			: ".imageEnforceWidth",
			"{imageEnforceHeight}"			: ".imageEnforceHeight",
			"{imageEnforceRatio}"			: ".imageEnforceRatio",
			"{imageEnforceLockRatio}"		: ".imageEnforceLockRatio",
			"{imageVariation}"				: ".imageVariation"
		}
	},
	function(self) { return {

		init: function() {
			var options = {
				selector: {
					width: self.options["{imageEnforceWidth}"],
					height: self.options["{imageEnforceHeight}"],
					constrain: self.options["{imageEnforceLockRatio}"]
				}
			};

			// only apply constrain once variation has been populated
			self.editor.element.bind('variationPopulated', function() {
				// enforce dimension option
				if(self.editor.media.options.exporter.image.enforceDimension) {
					self.imageEnforceDimensionOption().attr({
						'checked': 'checked',
						'disabled': 'disabled'
					}).parent('.field').removeClass('hide-field-content');
				}

				self.applyConstrain(options);
			});

			// self.imageEnforceWidth().data("default", width);
			// self.imageEnforceHeight().data("default", height);
		},

		"{imageVariation} click": function(el) {
			self.applyConstrain();
		},

		"{imageEnforceDimensionOption} change": function(el, event) {
			event.stopPropagation();

			el.parent(".field").toggleClass("hide-field-content", !el.is(":checked"));

			self.transform();

			// self.imageEnforceWidth().trigger("keyup");
		},

		"{imageEnforceRatio} click": function(el) {
			el.toggleClass("locked");

			if (el.hasClass("locked")) {
				self.imageEnforceLockRatio().attr('checked', 'checked');
			} else {
				self.imageEnforceLockRatio().removeAttr('checked');
			}

			self.imageEnforceLockRatio().trigger('change');

			if(el.hasClass("locked")) {
				self.transform();
			}
		},

		"{self} previewImage": function() {
			self.transform();
		},

		"{imageEnforceWidth} keyup": function() {
			self.transform();
		},

 		"{imageEnforceHeight} keyup": function() {
			self.transform();
 		},

		"{imageEnforceWidth} blur": function(el) {
			if($.trim(el.val()) == '' && !self.imageEnforceLockRatio().is(':checked')) {
				el.val(el.data('initial'));
			}

			self.transform();
		}, 		

		"{imageEnforceHeight} blur": function(el) {
			if($.trim(el.val()) == '' && !self.imageEnforceLockRatio().is(':checked')) {
				el.val(el.data('initial'));
			}

			self.transform();
		},

		transform: function() {
			var image = self.editor.previewImage();

			if(image === undefined) return;

			var dimensions = {};

			// Enforce image dimension
			if (self.imageEnforceDimensionOption().is(":checked")) {
				dimensions = {
					width: self.imageEnforceWidth().val(),
					height: self.imageEnforceHeight().val()
				};
			} else {
				var variation = self.editor.currentImageVariation();

				variation = variation === undefined ? self.editor.meta() : variation.data('variation');

				dimensions = {
					width: variation.width,
					height: variation.height
				};
			}

			if(image.width() !== dimensions.width || image.height() !== dimensions.height) {
				image.css(dimensions);

				// if dimension changed, trigger change in dimension
				self.editor.trigger('dimensionEnforced');
			}

			self.editor.preview.resetLayout();
		},

		applyConstrain: function(options) {
			var variation = self.editor.currentImageVariation() === undefined ? self.editor.meta() : self.editor.currentImageVariation().data('variation'),
				dimensions = {
					source: {
						width: variation.width,
						height: variation.height
					}
				};

			if(self.editor.media.options.exporter.image.enforceDimension) {
				dimensions.allowedMax = {
					width: self.editor.media.options.exporter.image.enforceWidth,
					height: self.editor.media.options.exporter.image.enforceHeight
				}
			}

			options = $.extend(true, {}, dimensions, options);

			self.imageEnforceDimension().constrain(options);

			self.transform();
		}
	}}
);

module.resolve();

});
// require: end

});
// module: end

// module: start
EasyBlog.module("media/editor.video", function($){

var module = this;

// require: start
EasyBlog.require()
.view(
	"media/editor.video",
	"media/editor.video.player"
)
.done(function() {

// controller: start
EasyBlog.Controller(

	"Media.Editor.Video",

	{
		defaultOptions: {

			view: {
				panel: "media/editor.video",
				player: "media/editor.video.player"
			},

			player: {
				width: 400, // 16
				height: 225, // 9
				autostart: false,
				controlbar: "bottom",
				backcolor: "#333333",
				frontcolor: "#ffffff",
				modes: [
					{
						type: 'html5'
					},
					{
						type: 'flash',
						src: $.rootPath + "/components/com_easyblog/assets/vendors/jwplayer/player.swf"
					},
					{
						type: 'download'
					}
				]
			},

			"{editorPreview}": ".editorPreview",
			"{editorPanel}": ".editorPanel",

			"{playerContainer}": ".playerContainer",

			// Insert options
			"{insertWidth}" : ".insertWidth",
			"{insertHeight}": ".insertHeight",
			"{autoplay}": ".autoplay"
		}
	},

	function(self) {
		var $Media, $Library, $Browser;

		return {

		init: function() {

			$Media = self.media;
			$Library = $Media.library;
			$Browser = $Media.browser;

			var meta = self.meta();

			var insertWidth = $Media.options.exporter.video.width;
			var insertHeight = $Media.options.exporter.video.height;

			if (insertWidth!==undefined) {
				self.options.player.width = insertWidth;
			}

			if (insertHeight!==undefined) {
				self.options.player.height = insertHeight;
			}

			// Panel
			self.editorPanel()
				.html(self.view.panel({
					meta: meta,
					insertWidth: self.options.player.width,
					insertHeight: self.options.player.height
				}))
				.implement(
					EasyBlog.Controller.Media.Editor.Panel,
					{},
					function() {

						// Keep a reference to this controller
						self.panel = this;
					}
				);

			// Preview
			self.editorPreview()
				.implement(
					EasyBlog.Controller.Media.Editor.Preview,
					{},
					function() {

						// Keep a reference to this controller
						self.preview = this;

						self.initPlayer();
					}
				);
		},

		initPlayer: function() {

			// Show loading indicator
			self.preview.showDialog("loading");

			EasyBlog.require()
				.script($.rootPath + "/components/com_easyblog/assets/vendors/jwplayer/jwplayer.js")
				.done(function($) {

					var meta = self.meta(),

						place = self.place(),

						id = "player-" + $.uid(),

						options = $.extend(self.options.player, {
							id: id,
							file: self.meta().url,
						}),

						player = self.view.player({
							id: id,
							meta: meta,
							options: options
						});

					// Append player container
					self.preview.container()
						.append(player);

					self.player = jwplayer(id).setup(options);

					self.preview.resetLayout();

					// Hide loading indicator
					self.preview.hideDialog("loading");
				})
				.fail(function() {
				});
		},

		meta: function() {
			return $Library.getMeta(self.key);
		},

		place: function() {
			return $Library.getPlace(self.meta().place);
		},

		setLayout: function() {

		},

		"{self} cancelItem": function() {

			if (self.player) {

				if (self.player.getState()=="PLAYING") {

					self.player.pause();
				}
			}
		},

		//
		// Insert video
		//

		"{self} insertItem": function() {
			var options = {
				autostart: (self.autoplay().val() == '1') ? true : false,
				width: parseInt(self.insertWidth().val(), 10),
				height: parseInt(self.insertHeight().val(), 10)
			}

			$Media.insert(self.meta(), options);
		},

		resize: function() {

			if (self.player) {
				var width = parseInt(self.insertWidth().val(), 10);
				var height = parseInt(self.insertHeight().val(), 10);
				self.player.resize(width, height);
				self.preview.resetLayout();
			}
		},

		"{insertWidth} keyup": function() {

			self.resize();
		},

		"{insertHeight} keyup": function() {

			self.resize();
		}

	}}

);
// controller: end

module.resolve();

});
// require: end

});
// module: end

// module: start
EasyBlog.module("media/uploader.item", function($) {

var module = this;

// controller: start
EasyBlog.Controller("Media.Browser.Uploader.Item",

	{
		defaultOptions: {
			"{filename}": ".uploadFilename",
			"{progressBar}": ".uploadProgressBar progress",
			"{percentage}": ".uploadPercentage",
			"{status}": ".uploadStatus",
			"{removeButton}": ".uploadRemoveButton"
		}
	},

	// Instance properties
	function(self) { return {

		init: function() {

			self.element.data("item", self);

			self.filename()
				.html(self.file.name);

			self.setState("queued");
		},

		getFilesize: function(p, s) {

			return (
				(self.file.size===undefined || self.file.size=="N/A") ?
					"":
                    ((p) ? p : "") + $.plupload.formatSize(self.file.size) +  ((s) ? s : "")
            );
		},

		setProgress: function(val) {

			self.progressBar()
				.attr("value", val);

			self.percentage()
				.html(val);
		},

		setState: function(state) {

			// queued, uploading, failed, done

			self.element
				.removeClass("upload-state-" + self.state)
				.addClass("upload-state-" + state);

			self.state = state;
		},

		setMessage: function(message) {

			self.status()
				.html(message);
		},

		"{removeButton} click": function(el, event) {

			event.stopPropagation();

			// TODO: Garbage collection
			self.element
				.slideUp(function(){

					self.element.remove();
				});
		}
	}}

);
// controller: end

module.resolve();


});
// module: end

EasyBlog.module('ratings', function($){

	var module = this;

	EasyBlog
		.require()
		.library(
			'ui/stars'
		)
		.script(
			'legacy'
		)
		.done(function(){

			/**
			 * Ratings
			 **/
			eblog.ratings = {
				setup: function( elementId , disabled , ratingType ){
					$("#" + elementId ).stars({
						split: 2,
						disabled: disabled,
						oneVoteOnly: true,
						cancelShow: false,
						callback: function( element ){
							eblog.loader.loading( elementId + '-command .rating-text' );
							ejax.load( 'ratings' , 'vote' , element.value() , $( '#' + elementId ).children( 'input:hidden' ).val() , ratingType , elementId );
						}
					});
				},
				showVoters: function( elementId , elementType ){
					ejax.load( 'ratings' , 'showvoters' , elementId , elementType );
				},
				update: function( elementId , ratingType , value , resultCommand ){
					$( '#' + elementId ).children( '.ui-stars-star' ).removeClass( 'ui-stars-star-on' );
					value	= parseInt( value );

					// Hide command
					$( '#' + elementId + '-command' ).hide();

					$( '#' + elementId ).addClass( 'voted' );

					$( '#' + elementId ).children( '.ui-stars-star' ).each( function( index ){
						if( index < value )
						{
							$( this ).addClass( 'ui-stars-star-on' );
						}
						else
						{
							$( this ).removeClass( 'ui-stars-star-on' );
						}
					});
				}
			};

			module.resolve();
		});

});
EasyBlog.module("tag", function($){

var module = this;

EasyBlog.require()
	.view("dashboard/dashboard.tags.item")
	.done(function(){

		EasyBlog.Controller(

			"Tag.Form",
			{
				defaultOptions: {

					tags: [],
					tagLimit: 0,
					tagSelections: [],
					tagSelectionLimit: 25,

					"{tagList}"             : ".tag-list.creation",
					"{tagItems}"            : ".tag-list.creation .tag-item",
					"{tagItemRemoveButton}" : ".remove-tag",

					"{tagCreationForm}"		: ".new-tag-item",
					"{tagInput}"            : ".tagInput",
					"{tagCreateButton}"     : ".tag-create",
					"{totalTags}"			: ".total-tags",

					"{tagSelectionFilter}"  : ".tag-selection-filter",
					"{tagSelectionList}"    : ".tag-list.selection",
					"{tagSelectionItems}"   : ".tag-list.selection .tag-item",
					"{tagSelection}"        : ".tag-selection",

					"{showAllTagsButton}"   : ".show-all-tags",

					view: {
						tagItem: "dashboard/dashboard.tags.item"
					}
				}
			},

			function(self) { return {

				init: function() {

					if (EasyBlog.dashboard) {
						EasyBlog.dashboard.registerPlugin("tags", self);
					}

					// Fork this into an asynchronous process
					// in case of large dataset
					setTimeout(function(){

						// Populate tag selections
						var tags = self.options.tagSelections,
							i, l = tags.length;

						for (i=0; i<l; i++) {
							self.tags[tags[i].title.toLowerCase()] = tags[i];
						}

						// Populate selected tags if any
						var tags = self.options.tags,
							l = tags.length;

						for (i=0; i<l; i++) {

							var key = self.getKey(tags[i].title);

							self.selectTag(key);
						}

						// Generate tag selections
						self.showTagSelections("");

					}, 0);
				},

				// Tag data
				tags: {},

				// Tag elements
				items: {},

				// Selected tags
				selected: {},

				sanitizeTitle: function(title) {

					return $.trim(title).replace(/[,\'\"\#\<\>]/gi,"");
				},

				getKey: function(title) {

					return self.sanitizeTitle(title).toLowerCase();
				},

				getTag: function(key) {

					// This is because of key conflicts with native object methods
					// like "watch" or "hasOwnProperty" since tags can be anything.
					return Object.prototype.hasOwnProperty.call(self.tags, key) ? self.tags[key] : undefined;
				},

				createTag: function(title) {

					var title = self.sanitizeTitle(title),
						key = title.toLowerCase();

					return self.getTag(key) || (self.tags[key] = {title: title});
				},

				getTagItem: function(key) {

					var tag = self.getTag(key);

					if (!tag) return;

					return tag.item || (tag.item = self.view.tagItem({title: tag.title}).data("key", key));
				},

				getTagData: function(key) {

					var tag = self.getTag(key);

					if (!tag) return;

					return tag.data || (tag.data = $('<input class="tagdata" type="hidden" name="tags[]" value="' + tag.title + '" />'));
				},

				search: function(keyword) {

					var keyword = $.trim(keyword).toLowerCase(),
						results = [];

					for (key in self.tags) {

						if (key.indexOf(keyword) < 0) continue;

						results.push(key);
					}

					return results;
				},

				selectTag: function(key) {

					clearTimeout(self.selectTag.refreshTagSelection);

					var tagItem = self.getTagItem(key);

					if (!tagItem) return;

					var tagItems = self.tagItems();

					if (self.options.tagLimit > 0 && tagItems.length >= self.options.tagLimit) return;

					tagItem.css({opacity: 0});

					// When no item is selected
					if (tagItems.length < 1) {

						self.tagList()
							.prepend(tagItem);

					// When there are selected items
					} else {

						var lastTagItem = tagItems.filter(":last");

						// Don't move tag if it's already the last one.
						if (lastTagItem[0]!=tagItem[0]) {

							tagItem.insertAfter(lastTagItem);
						}
					}

					tagItem.animate({opacity: 1});

					// Attach tag data
					var tagData = self.getTagData(key);

					tagData.appendTo(self.element);

					self.selected[key] = true;

					self.checkTagLimit();

					self.selectTag.refreshTagSelection = setTimeout(function(){

						// Refresh tag selection
						self.showTagSelections();

					}, 500);
				},

				unselectTag: function(key) {

					var tagItem = self.getTagItem(key);

					if (!tagItem) return;

					// Detach tag item
					tagItem.detach();

					// Detach tag data
					var tagData = self.getTagData(key);

					tagData.detach();

					delete self.selected[key];

					self.checkTagLimit();

					var tag = self.getTag(key);

					if (tag.alias!==undefined) {

						// Refresh tag selection
						self.showTagSelections();
					}
				},

				addToTagSelectionList: function(key) {

					var tagItem = self.getTagItem(key);

					return tagItem && tagItem.appendTo(self.tagSelectionList());
				},

				showTagSelections: function(filter) {

					// Detach everything
					self.tagSelectionItems().detach();

					filter = self.currentFilter =
						(filter===undefined) ? self.currentFilter || "" : filter;

					var c = 0,
						limit = self.options.tagSelectionLimit;

					if (filter==="") {

						for (key in self.tags) {
							if (c >= limit) break;
							if (self.selected[key] || self.getTag(key).alias===undefined) continue;
							self.addToTagSelectionList(key);
							c++;
						}

					} else {

						var results = self.search(filter),
							i, l = results.length;

						for (i=0; i<l; i++) {
							if (c >= limit) break;
							var key = results[i];
							if (self.selected[key] || self.getTag(key).alias===undefined) continue;
							self.addToTagSelectionList(key);
							c++;
						}
					}

					self.tagSelection().toggleClass("no-selection", c < 1);
				},

				"{tagInput} keydown": function(tagInput, event) {

					event.stopPropagation();

					self.realEnterKey = (event.keyCode==13);
				},

				"{tagInput} keypress": function(tagInput, event) {

					event.stopPropagation();

					// We need to verify whether or not the user is actually entering
					// an ENTER key or exiting from an IME context menu.
					self.realEnterKey = self.realEnterKey && (event.keyCode==13);
				},

				"{tagInput} keyup": function(tagInput, event) {

					clearTimeout(self.filterTask);

					event.stopPropagation();

					switch(event.keyCode) {

						case 27: // escape
							tagInput.val("");
							break;

						case 13: // enter
							if (self.realEnterKey && tagInput.hasClass("canCreate")) {
								self.createTagFromInput();
							}
							break;
					}

					self.filterTask = setTimeout(function(){

						self.showTagSelections(tagInput.val());

					}, 250);
				},

				createTagFromInput: function() {

					var title = $.trim(self.tagInput().val());

					if (title!=="") {

						var key = self.getKey(title),
							tag = self.createTag(title);

						self.selectTag(key);

						self.tagInput().val("");
					}

					// Reset show tag selections to original state
					self.showTagSelections("");
				},

				checkTagLimit: function() {

					var limit = self.options.tagLimit;

					if (limit < 1) return;

					var totalTags = self.tagItems().length;

					// Update data count
					self.totalTags().text(totalTags);

					self.tagCreationForm()[totalTags >= limit ? "hide" : "show"]();
				},

				"{tagCreateButton} click": function() {

					self.createTagFromInput();
				},

				"{tagSelectionItems} click": function(el) {

					var key = el.data("key");
					self.selectTag(key);
				},

				"{tagItemRemoveButton} click": function(el) {

					var key = el.parents(".tag-item").data("key");

					self.unselectTag(key);
				},

				"{showAllTagsButton} click": function(el) {

					if (el.hasClass("active")) {

						el.removeClass("active");
						self.options.tagSelectionLimit = self.originalLimit;

					} else {

						el.addClass("active");
						self.originalLimit = self.options.tagSelectionLimit;
						self.options.tagSelectionLimit = 9999;
					}

					self.showTagSelections("");
				}
			}}
		);

		module.resolve();

	});
});
});
