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
	$( '#microblog-save-text' ).bind( 'click' , function(){

		var title 		= $( '#microblog-text input[name=title]' ).val(),
			content 	= $( '#microblog-text textarea[name=content]' ).val(),
			category	= $( '#microblog-text select[name=category_id]' ).val(),
			privacy 	= $( '#microblog-text select[name=private]' ).val(),
			autopost 	= new Array,
			tags 		= getTags( 'text' )


		$( '#microblog-text .autopost-microblog' ).find( 'input[name=socialshare\\[\\]]:checked').each(function(){
			autopost.push( $(this).val() );
		});

		EasyBlog.ajax( 'site.views.microblog.save',
			{
				'type'		: 'text',
				'title'		: title,
				'content'	: content,
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
					$( '#microblog-text input[name=cancel]' ).click();
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

	$( '#microblog-text textarea[name=content]' ).focus();

	$( '#microblog-text input[name=title]' ).bind( 'focus blur' , function(){

		if( $(this).val() == '<?php echo $this->escape( JText::_( 'COM_EASYBLOG_MICROBLOG_TITLE_OPTIONAL' , true ) );?>' )
		{
			$(this).val( '' );
		}
		else if( $(this).val() == '' )
		{
			$(this).val( '<?php echo $this->escape( JText::_( 'COM_EASYBLOG_MICROBLOG_TITLE_OPTIONAL' , true ) );?>' );
		}
	});

	/**
	 * Cancel click event
	 */
	$( '#microblog-text input[name=cancel]' ).bind( 'click' , function(){

		// Reset the URL values
		$( '#microblog-text input[name=title]' ).val( '<?php echo $this->escape( JText::_( 'COM_EASYBLOG_MICROBLOG_TITLE_OPTIONAL' , true ) );?>' );

		// Reset the output indications
		$( '#microblog-text textarea[name=content]' ).val( '' );
	});
});
</script>
<form id="microblog-text" name="quick-post" method="post">
	<ul class="list-form tight reset-ul">
		<li>
			<div>
				<input type="text" class="input text width-full fwb" name="title" value="<?php echo $this->escape( JText::_( 'COM_EASYBLOG_MICROBLOG_TITLE_OPTIONAL' , true ) );?>" />
			</div>
		</li>
		<li>
			<div class="input-label quick-content" style="background:#f5f5f5;border:1px solid #bbb;border-top:1px solid #888;margin:0!important;padding:6px">
				<?php echo JText::_('COM_EASYBLOG_DASHBOARD_QUICKPOST_CONTENT'); ?>
			</div>
			<div><textarea class="input textarea width-full" rows="6" name="content" id="eblog-post-content"></textarea></div>
			<div class="mtm">
				<a href="javascript:void(0);" class="show-advanced-parameters buttons"><?php echo JText::_( 'COM_EASYBLOG_ADVANCED_PARAMETERS' );?></a>
			</div>
		</li>
		<li class="clearfix columns-2 add-seperator advanced-parameters">
			<div>
				<div id="param-category" class="prel">
					<i></i><?php echo $categories; ?>
				</div>
			</div>
			<?php if( $this->acl->rules->enable_privacy ){ ?>
			<div>
				<div id="param-privacy" class="prel">
					<i></i>
					<?php echo JHTML::_( 'select.genericlist' , EasyBlogHelper::getHelper( 'Privacy' )->getOptions() , 'private' , 'size="1" class="input select"' , 'value' , 'text' , $system->config->get( 'main_blogprivacy') );?>
				</div>
			</div>
			<?php } ?>
		</li>
		<li class="advanced-parameters mtm">
			<?php echo $this->fetch( 'dashboard.microblog.tags.php' , array( 'microblogType' => '#microblog-text' ) ); ?>
		</li>
	</ul>

	<div class="ui-modfoot clearfix">
		<span id="quickpost-loading" class="float-r mts ir"></span>
		<input id="microblog-save-text" type="button" value="<?php echo JText::_('COM_EASYBLOG_PUBLISH_STORY_BUTTON'); ?>" class="buttons float-r" name="publish-post" />
		<?php echo $this->fetch( 'dashboard.microblog.autopost.php' ); ?>
		<span id="quickdraft-loading" class="float-l mts ir"></span>
		<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=entries' );?>" class="buttons sibling-l"><?php echo JText::_( 'COM_EASYBLOG_CANCEL_BUTTON' ); ?></a>
	</div>
</form>
