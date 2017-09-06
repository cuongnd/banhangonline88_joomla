<?php
// namespace components\com_jchat\views\attachments;
/**
 * @package JCHAT::ATTACHMENTS::components::com_jchat
 * @subpackage views
 * @subpackage attachments
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');?>

<script type="text/javascript" src="<?php echo $this->liveSite;?>components/com_jchat/js/jquery.js"></script>
<script type="text/javascript">
	jQuery(function() {
		jQuery('div.closemsgbtn').on('click', function(){
			jQuery(this).parent().css('display', 'none')
		});
		setTimeout(function(){
			jQuery('div.upload_usermessage').fadeOut();
		}, 1000);

		<?php if($this->app->input->get('form', null) == 'pm'):?>
		jQuery('#uploadform').on('submit', function(jqEvent){
			window.parent.JChatMessaging.appendFileMessage(jQuery('#newfile').val().replace('C:\\fakepath\\', ''));
		});
		<?php endif; ?>
	})
</script>
<?php if($this->baseTemplate == 'custom.css'): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $this->liveSite;?>templates/<?php echo $this->joomlaTemplate;?>/css/com_jchat/css/templates/default.css"/>
<?php else: ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $this->liveSite;?>components/com_jchat/css/templates/<?php echo $this->baseTemplate;?>"/>
<?php endif;?>
<?php if($this->chatTemplate):?>
	<link type="text/css" rel="stylesheet" href="<?php echo $this->liveSite;?>components/com_jchat/css/templates/<?php echo $this->chatTemplate;?>"/>
<?php endif;?>

<div class="upload_usermessage <?php echo $this->visibleClass . $this->success;?>"><?php echo $this->modelMessage;?><div class="closemsgbtn"></div></div>
<form name="uploadform" enctype="multipart/form-data" id="uploadform" method="post" action="index.php">
	<input type="file" id="newfile" name="newfile" />
	<div class="formbutton">
		<input id="file_upload_button" type="submit" value="" /><label class="buttonlabel"><?php echo JText::_('COM_JCHAT_UPLOAD');?></label>
	</div>
	<input type="hidden" name="option" value="<?php echo $this->option;?>" />
	<input type="hidden" name="task" value="attachments.saveEntity" />
	<input type="hidden" name="format" value="raw" />
	<input type="hidden" name="to" value="<?php echo $this->to;?>" />
	<input type="hidden" name="tologged" value="<?php echo $this->tologged;?>" />
</form> 