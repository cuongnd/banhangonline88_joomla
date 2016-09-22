<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
EasyBlog.ready(function($){
	$( '#microblog-photo input[name=publish-post]' ).bind( 'click' , function(){

		var title 		= $( '#microblog-photo input[name=title]' ).val(),
			content 	= $( '#microblog-photo textarea[name=content]' ).val(),
			category	= $( '#microblog-photo select[name=category_id]' ).val(),
			privacy 	= $( '#microblog-photo select[name=private]' ).val(),
			imageSource = $( '#microblog-photo input[name=image-source]' ).val(),
			autopost 	= new Array,
			tags 		= getTags( 'photo' );

		$( '#microblog-photo .autopost-microblog' ).find( 'input[name=socialshare\\[\\]]:checked').each(function(){
			autopost.push( $(this).val() );
		});


		EasyBlog.ajax( 'site.views.microblog.save',
			{
				'type'		: 'photo',
				'title'		: title,
				'content'	: content,
				'imageSource': imageSource,
				'tags'		: tags,
				'category'	: category,
				'privacy'	: privacy,
				'autopost'	: autopost
			},
			{
				beforeSend: function(){
					eblog.loader.loading( 'quickpost-loading' );
				},
				success: function( message ){

					$( '#eblog-message' )
						.removeClass( 'error info success' )
						.addClass( 'success' )
						.html( message )
						.show();


					// Once successful, reset the form so that the user can begin posting the next post.
					$( '#microblog-photo input[name=cancel]' ).click();

					eblog.loader.doneLoading( "quickpost-loading" );
				},
				fail: function( message ){
					$( '#eblog-message' )
						.removeClass( 'error info success' )
						.addClass( 'error' )
						.html( message )
						.show();

					eblog.loader.doneLoading( "quickpost-loading" );
				}
			}
		);
	});

	/**
	 * Event that gets triggered when the insert photo button is clicked
	 */
	$( '#microblog-photo button[name=insert-url]' ).bind('click', function(){
		var photoURL 	= $( '#microblog-photo input[name=photo-url]' ).val();

		if( photoURL == '' || photoURL == '<?php echo $this->escape( JText::_( 'COM_EASYBLOG_MICROBLOG_PASTE_PHOTO_URL' , true ) );?>')
		{
			$( '#eblog-message' )
				.removeClass( 'error info success' )
				.addClass( 'error' )
				.html( '<?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_PLEASE_ENTER_IMAGE_URL' , true );?>' )
				.show();

			return false;
		}

		// Reset the preview pane first.
		$( '#microblog-photo input[name=cancel]' ).click();

		var image 		= new Image();
		image.src		= photoURL;

		$( '#microblog-photo .quick-output' )
			.show()
			.css( 'text-align' , 'center' )
			.append( image );

		// Assign the url to the hidden input
		$( '#microblog-photo input[name=image-source]' ).val( photoURL );

		return false;
	});

	/**
	 * Cancel click event
	 */
	$( '#microblog-photo input[name=cancel]' ).bind( 'click' , function(){

		// Reset the URL values
		$( '#microblog-photo input[name=photo-url]' ).val( '<?php echo $this->escape( JText::_( 'COM_EASYBLOG_MICROBLOG_PASTE_PHOTO_URL' , true ) );?>' );

		// Reset the output indications
		$( '#microblog-photo .photo-preview img' ).remove();

		// Reset the hidden input
		$( '#microblog-photo input[name=image-source]' ).val( '' );

		// Hide the preview
		$( '#microblog-photo .photo-preview' ).hide();
	});

	/**
	 * Bind the events when focusing or blur on the photo url textbox.
	 */
	$( '#microblog-photo input[name=photo-url]' ).bind( 'focus blur' , function(){
		if( $(this).val() == '<?php echo $this->escape( JText::_( 'COM_EASYBLOG_MICROBLOG_PASTE_PHOTO_URL' , true ) );?>' )
		{
			$(this).val( '' );
		}
		else if( $(this).val() == '' )
		{
			$(this).val( '<?php echo $this->escape( JText::_( 'COM_EASYBLOG_MICROBLOG_PASTE_PHOTO_URL' , true ) );?>' );
		}
	});

	$( '#microblog-photo #photo-source' ).change(function(){
		var action_url  = '<?php echo JURI::root();?>index.php?option=com_easyblog&namespace=site.views.microblog.uploadPhoto&format=ajax&tmpl=component&<?php echo EasyBlogHelper::getToken();?>=1',
			form 		= $( '#microblog-photo' ),
			iframe 		= $( '<iframe>' );

		// Set the iframe properties here
		$( iframe ).attr(
		{
			'id'	: 'upload_iframe',
			'name'	: 'upload_iframe',
			'width'	: 0,
			'height': 0,
			'border': 0
		})
		.css({
			'width'	: 0,
			'height': 0,
			'border': 0,
			'opacity': 0,
			'visiblity': 'hidden'
		});

		// Append iframe into DOM
		$( form ).parent().append( iframe );

		window.frames['upload_iframe'].name="upload_iframe";


		var iframeId	= $('#upload_iframe')[0];

		// Add event...
		var eventHandler = function()  {

			if (iframeId.detachEvent)
			{
				iframeId.detachEvent("onload", eventHandler);
			}
			else
			{
				iframeId.removeEventListener("load", eventHandler, false);
			}

			// Message from server...
			if( iframeId.contentDocument )
			{
				content = iframeId.contentDocument;
			}
			else if( iframeId.contentWindow )
			{
				content = iframeId.contentWindow.document;
			}
			else if( iframeId.document )
			{
				content = iframeId.document;
			}

			content 	= $( content ).find( 'script#ajaxResponse' ).html();
			var result 	= $.parseJSON( content );

			switch( result.type )
			{
				case 'error':
					$( '#eblog-message' )
						.removeClass( 'error info success' )
						.addClass( 'error' )
						.html( result.message )
						.show();
				break;
				case 'success':

					// Reset the preview pane first.
					$( '#microblog-photo input[name=cancel]' ).click();

					// Create new image tag
					var img 	= $( '<img>' );
					$( img ).attr( 'src' , result.uri );

					// Hide loader
					$( '#microblog-photo .photo-preview i' ).hide();

					// Append the new image into the preview section
					$( '#microblog-photo .photo-preview' ).append( img );

					// Assign the url to the hidden input
					$( '#microblog-photo input[name=image-source]' ).val( result.uri );

					$( '#microblog-photo .photo-preview' ).show();
				break;
			}

			// Try to delete the hidden iframe
			setTimeout(function()
			{
				$( iframeId ).remove();
			}, 250);
		}

		$(iframeId).load(eventHandler);

		// Set properties of form...
		$( form ).attr(
		{
			'target' : 'upload_iframe',
			'action' : action_url,
			'method' : 'post',
			'enctype': 'multipart/form-data',
			'encoding': 'multipart/form-data'
		})
		.submit();

		$( '#microblog-photo .photo-preview' ).show();
		$( '#microblog-photo .photo-preview i' ).show();
	});

	$( '#microblog-photo input[name=title]' ).bind( 'focus' , function(){

		if( $( this ).val() == '<?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_TITLE_OPTIONAL' , true );?>' )
		{
			$( this ).val( '' );
		}
	});

	$( '#microblog-photo input[name=title]' ).bind( 'blur' , function(){

		if( $(this).val() == '' )
		{
			$( this ).val( '<?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_TITLE_OPTIONAL' , true );?>' );
		}

	});
});
</script>
<form id="microblog-photo" name="quick-post" method="post">
	<div class="quick-attachment clearfix">
		<?php if( $this->acl->rules->upload_image ) { ?>
		<span style="position:relative;">
			<label for="photo-source">
				<span class="buttons upload-file"><i><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_UPLOAD_PHOTO' );?></i></span>
				<input type="file" name="photo-source" id="photo-source" style="" />
			</label>
		</span>
		<b style="display:inline-block;margin:0 10px;font-size:16px;line-height:30px;height:30px"><?php echo JText::_( 'COM_EASYBLOG_OR' );?></b>
		<?php } ?>
		<input type="text" class="input width-200" value="<?php echo JText::_('COM_EASYBLOG_MICROBLOG_PASTE_PHOTO_URL'); ?>" name="photo-url" />
		<button class="buttons" name="insert-url" type="button"><?php echo JText::_( 'COM_EASYBLOG_INSERT_BUTTON' );?></button>
	</div>

	<div class="quick-output photo-preview">
		<i class="upload-indicator"></i>
		<input type="hidden" name="image-source" value="" />
	</div>

	<div class="mtm">
		<a href="javascript:void(0);" class="show-advanced-parameters buttons"><?php echo JText::_( 'COM_EASYBLOG_ADVANCED_PARAMETERS' );?></a>
	</div>

	<ul class="list-form reset-ul advanced-parameters">
		<li>
			<div>
				<input type="text" class="input text width-full fwb" name="title" value="<?php echo $this->escape( JText::_( 'COM_EASYBLOG_MICROBLOG_TITLE_OPTIONAL' , true ) );?>" />
			</div>
        </li>
		<li class="columns-2 add-seperator advanced-parameters">
			<div>
				<div><?php echo $categories; ?></div>
			</div>
			<?php if( $this->acl->rules->enable_privacy ){ ?>
			<div>
				<div>
					<?php echo JHTML::_( 'select.genericlist' , EasyBlogHelper::getHelper( 'Privacy' )->getOptions() , 'private' , 'size="1" class="input select"' , 'value' , 'text' , $system->config->get( 'main_blogprivacy') );?>
				</div>
			</div>
			<?php } ?>
		</li>
		<li>
			<div class="input-label" style="background:#f5f5f5;border:1px solid #bbb;border-top:1px solid #888;margin:0!important;padding:6px">
				<?php echo JText::_('COM_EASYBLOG_DASHBOARD_QUICKPOST_DESCRIPTION'); ?> <i class="small">(<?php echo JText::_('COM_EASYBLOG_DASHBOARD_QUICKPOST_OPTIONAL'); ?>)</i>
			</div>
			<div>
				<textarea class="input textarea width-full" rows="6" name="content" id="eblog-post-content"></textarea>
			</div>
		</li>
		<li class="advanced-parameters">
			<?php echo $this->fetch( 'dashboard.microblog.tags.php' , array( 'microblogType' => '#microblog-photo' ) ); ?>
		</li>
    </ul>
	<div class="ui-modfoot clearfix">
		<span id="quickpost-loading" class="float-r mts ir"></span>
		<input type="button" value="<?php echo JText::_('COM_EASYBLOG_PUBLISH_PHOTO_BUTTON'); ?>" class="buttons float-r" name="publish-post" />

		<?php echo $this->fetch( 'dashboard.microblog.autopost.php' ); ?>

		<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=entries' );?>" class="buttons sibling-l"><?php echo JText::_( 'COM_EASYBLOG_CANCEL_BUTTON' ); ?></a>
	</div>
</form>
