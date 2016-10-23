<?php
/** 
 * @package JCHAT::components::com_jchat
 * @subpackage views
 * @subpackage avatar
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
<!--[if lt IE 9]>
	<style type="text/css">
		div.up {
			margin-top: 0px; 
		}
	</style>
<![endif]-->

<form name="deleteform" enctype="multipart/form-data" id="deleteform" method="post" action="index.php">
	    <img class="avatar_img" src="<?php echo $this->liveSite;?>components/com_jchat/images/avatars/<?php echo $this->userAvatar;?>" alt="avatar" />
		<div class="formbutton up">
			<?php echo $this->avatarDeleteButton;?>
			<input type="hidden" name="option" value="com_jchat" />
			<input type="hidden" name="task" value="avatar.deleteEntity" />
			<input type="hidden" name="format" value="raw" />
		</div>
</form>
		
<form name="uploadform" enctype="multipart/form-data" id="uploadform" method="post" action="index.php">
		<input type="file" name="newavatar" />
		<div class="formbutton">
			<input id="avatar_upload" type="submit" value="" /><label class="buttonlabel"><?php echo JText::_('COM_JCHAT_UPLOAD');?></label>
			<input type="hidden" name="option" value="com_jchat" />
			<input type="hidden" name="task" value="avatar.saveEntity" />
			<input type="hidden" name="format" value="raw" />
		</div>
</form> 
<?php 		
if(isset($this->gdMissingAlert)) {
	echo $this->gdMissingAlert;
}
?>
<div class="upload_usermessage <?php echo $this->visibleClass . $this->success;?>"><?php echo $this->modelMessage;?><div class="closemsgbtn"></div></div>