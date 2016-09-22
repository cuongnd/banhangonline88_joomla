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
	$( '#microblog-video input[name=publish-post]' ).bind( 'click' , function(){

		var title 		= $( '#microblog-video input[name=title]' ).val(),
			content 	= $( '#microblog-video textarea[name=content]' ).val(),
			category	= $( '#microblog-video select[name=category_id]' ).val(),
			privacy 	= $( '#microblog-video select[name=private]' ).val(),
			videoURL 	= $( '#microblog-video input[name=video-source]' ).val(),
			tags 		= getTags( 'video' );

		EasyBlog.ajax( 'site.views.microblog.save',
			{
				'type'		: 'video',
				'title'		: title,
				'content'	: content,
				'tags'		: tags,
				'category'	: category,
				'privacy'	: privacy,
				'videoSource'	: videoURL
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
					$( '#microblog-video input[name=cancel]' ).click();
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

	$( '#microblog-video button[name=insert-url]' ).bind('click', function(){
		var videoURL 	= $( '#microblog-video input[name=video-url]' ).val();


		if( videoURL == '' || videoURL == '<?php echo $this->escape( JText::_( 'COM_EASYBLOG_MICROBLOG_PASTE_VIDEO_URL' , true ) );?>')
		{
			$( '#eblog-message' )
				.removeClass( 'error info success' )
				.addClass( 'error' )
				.html( '<?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_PLEASE_ENTER_VIDEO_URL' , true );?>' )
				.show();

			return false;
		}

		EasyBlog.ajax( 'site.views.microblog.getVideo' ,
			{
				'url'	: videoURL
			},
			{
				beforeSend: function(){
					// Show some loading image
					$( '#microblog-video .video-preview' ).show();
					$( '#microblog-video .video-preview i.upload-indicator' ).show();
				},
				success: function( embedCodes ){

					// Hide the loader
					$( '#microblog-video .video-preview i.upload-indicator' ).hide();

					// Embed the embed codes
					$( '#microblog-video .video-preview' ).append( embedCodes );

					// Assign the url to the hidden input
					$( '#microblog-video .video-preview input[name=video-source]' ).val( videoURL );
				}
			}
		);
		return false;
	});

	$( '#microblog-video input[name=cancel]' ).bind( 'click' , function(){
		var tmp			= $( '#microblog-video .video-preview i.upload-indicator' );
		var tmpSource	= $( '#microblog-video .video-preview input[name=video-source]' );

		// Reset previews
		$( '#microblog-video .video-preview' ).hide();
		$( '#microblog-video .video-preview' ).html( tmp ).append( tmpSource );

		// Reset the hidden input
		$( '#microblog-video input[name=video-source]' ).val( '' );

		// Reset the video url
		$( '#microblog-video input[name=video-url]' ).val( '<?php echo $this->escape( JText::_( 'COM_EASYBLOG_MICROBLOG_PASTE_VIDEO_URL' , true ) );?>' );
	});

	$( '#microblog-video input[name=video-url]' ).bind( 'focus blur' , function(){
		if( $(this).val() == '<?php echo $this->escape( JText::_( 'COM_EASYBLOG_MICROBLOG_PASTE_VIDEO_URL' , true ) );?>' )
		{
			$(this).val( '' );
		}
		else if( $(this).val() == '' )
		{
			$(this).val( '<?php echo $this->escape( JText::_( 'COM_EASYBLOG_MICROBLOG_PASTE_VIDEO_URL' , true ) );?>' );
		}
	});

});
</script>
<form id="microblog-video" name="quick-post" method="post">
	<div class="quick-attachment clearfix">
		<input type="text" class="input width-300" value="<?php echo JText::_('COM_EASYBLOG_MICROBLOG_PASTE_VIDEO_URL' , true ); ?>" name="video-url"/>
		<button class="buttons" name="insert-url" type="button"><?php echo JText::_( 'COM_EASYBLOG_INSERT_BUTTON' );?></button>
	</div>

	<div class="quick-output video-preview">
		<i class="upload-indicator"></i>
		<input type="hidden" name="video-source" value="" />
	</div>

	<div class="mtm">
		<a href="javascript:void(0);" class="show-advanced-parameters buttons"><?php echo JText::_( 'COM_EASYBLOG_ADVANCED_PARAMETERS' );?></a>
	</div>

	<ul class="list-form tight reset-ul advanced-parameters">
		<li>
			<div>
				<input type="text"  class="input text width-full fwb" value="<?php echo JText::_('COM_EASYBLOG_MICROBLOG_TITLE_OPTIONAL' , true ); ?>" onfocus="if (this.value == '<?php echo JText::_('COM_EASYBLOG_MICROBLOG_TITLE_OPTIONAL' , true ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php echo JText::_('COM_EASYBLOG_MICROBLOG_TITLE_OPTIONAL' , true ); ?>';}" name="title" id="title">
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
			<?php echo $this->fetch( 'dashboard.microblog.tags.php' , array( 'microblogType' => '#microblog-video' ) ); ?>
		</li>
    </ul>
	<div class="ui-modfoot clearfix">
		<span id="quickpost-loading" class="float-r mts ir"></span>
		<input type="button" value="<?php echo JText::_('COM_EASYBLOG_PUBLISH_VIDEO_BUTTON'); ?>" class="buttons float-r" name="publish-post" />

		<?php echo $this->fetch( 'dashboard.microblog.autopost.php' ); ?>

		<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=entries' );?>" class="buttons sibling-l"><?php echo JText::_( 'COM_EASYBLOG_CANCEL_BUTTON' ); ?></a>
	</div>
</form>
