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

	var left = (screen.width/2)-( 300 /2);
	var top = (screen.height/2)-( 300 /2);

	$( '#facebook-login' ).bind( 'click' , function(){
		var url = '<?php echo rtrim( JURI::root() , '/' );?>/administrator/index.php?option=com_easyblog&c=autoposting&task=request&type=<?php echo EBLOG_OAUTH_FACEBOOK;?>&call=doneLogin';
		window.open(url, "", 'scrollbars=no,resizable=no, width=300,height=300,left=' + left + ',top=' + top );
	});

	$( '#integrations_facebook_impersonate_page' ).bind( 'change' , function(){
		$( '#page-form' ).toggle();
	});
});

window.doneLogin = function(){
	window.location.href = '<?php echo rtrim( JURI::root() , '/' );?>/administrator/index.php?option=com_easyblog&view=autoposting&layout=facebook&step=2';
}
</script>
<form name="facebook" action="index.php" method="post">
	<ul class="list-instruction reset-ul pa-15">
		<li>
			<?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_STEP_2_ACTION_1'); ?>

			<div style="margin-top:5px">
			<?php if( !$this->isAssociated ){ ?>
				<a href="javascript:void(0);" id="facebook-login">
					<img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/autoposting/facebook_signon.png" />
				</a>
			<?php } else { ?>
				<span class="completed">
					<?php echo JText::_( 'COM_EASYBLOG_COMPLETED' );?>.
				</span>
				<div style="margin-top:10px;">
					<?php echo JText::_( 'COM_EASYBLOG_FACEBOOK_EXPIRE_TOKEN' );?> <strong><?php echo $this->expire;?></strong>
				</div>
			<?php } ?>
			</div>
		</li>

		<li>
			<div class="clearfix">
				<label><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_CENTRALIZED_STEP' );?></label>
			</div>
			<div>
				<?php echo $this->renderCheckbox( 'integrations_facebook_centralized' , $this->config->get( 'integrations_facebook_centralized' ) ); ?>
			</div>
			<div style="clear:both;"></div>
		</li>

		<li>
			<div class="clearfix">
				<input type="checkbox" value="1" id="integrations_facebook_impersonate_page" name="integrations_facebook_impersonate_page"<?php echo $this->config->get( 'integrations_facebook_impersonate_page' ) ? ' checked="checked"' : '';?> />
				<label for="integrations_facebook_impersonate_page" style="height:20px;line-height:20px;margin-left:8px;"><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_STEP_2_ACTION_2' );?></label>
			</div>
			<div id="page-form" class="mini-form"<?php echo $this->config->get( 'integrations_facebook_impersonate_page' ) ? ' style="display:block;"' : ' style="display:none;"';?>>
				<label for="page-id" style="line-height:24px;"><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_PAGEID' );?>:</label>
				<input type="text" id="page-id" name="integrations_facebook_page_id" value="<?php echo $this->config->get( 'integrations_facebook_page_id' );?>" class="input" style="width:200px;margin-right:10px" />
				<span class="small" style="line-height:24px;"><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_PAGEID_DESC' );?></span>
			</div>
		</li>
		<li>
			<div class="clearfix">
				<label><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_AUTO_UPDATE_STEP' );?></label>
			</div>
			<div>
				<?php echo $this->renderCheckbox( 'integrations_facebook_centralized_auto_post' , $this->config->get( 'integrations_facebook_centralized_auto_post' ) ); ?>
			</div>
			<div style="clear:both;"></div>
		</li>
		<li>
			<div class="clearfix">
				<label><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_AUTO_UPDATE_FOR_UPDATES_STEP' );?></label>
			</div>
			<div>
				<?php echo $this->renderCheckbox( 'integrations_facebook_centralized_send_updates' , $this->config->get( 'integrations_facebook_centralized_send_updates' ) ); ?>
			</div>
			<div style="clear:both;"></div>
		</li>
	</ul>

	<input type="button" class="button btn" onclick="previousPage();" value="<?php echo JText::_( 'COM_EASYBLOG_PREVIOUS_STEP_BUTTON' );?>" />
	<input type="submit" class="button social facebook btn btn-primary" value="<?php echo JText::_( 'COM_EASYBLOG_NEXT_STEP_BUTTON' );?>" />
	<input type="hidden" name="step" value="2" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="layout" value="facebook" />
	<input type="hidden" name="c" value="autoposting" />
	<input type="hidden" name="option" value="com_easyblog" />
	<?php echo JHTML::_( 'form.token' );?>
</form>
