<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<b><?php echo $replyAuthor; ?></b> <?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_HAS_REPLIED_TO_THE_DISCUSSION' );?> <b><?php echo $postTitle; ?></b>
<br />
<hr style="clear:both;margin:10px 0 15px;padding:0;border:0;border-top:1px solid #ddd" />
<img src="<?php echo $replyAuthorAvatar; ?>" width="80" alt="<?php echo $replyAuthor; ?>" style="width:80px;height:80px;border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px;float:left;margin:0 15px 0 0" />
	<?php echo $replyContent; ?>
<br style="clear:both" />
<br />
<?php if( $attachments ) { ?>
<div class="discuss-attachments mv-15">
	<h5><?php echo JText::_( 'COM_EASYDISCUSS_ATTACHMENTS' ); ?>:</h5>

	<ul class="thumbnails">
	<?php foreach( $attachments as $attachment ) { ?>
		<li class="attachmentsItem thumbnail thumbnail-small attachment-<?php echo $attachment->attachmentType; ?>" id="attachment-<?php echo $attachment->id;?>">
			<?php echo $attachment->toHTML( true );?>
		</li>
	<?php } ?>
	</ul>

</div>
<?php } ?>
<br />
<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_VIEW_DISCUSSION_LINK_BELOW' );?>
<div style="padding:20px;border-top:1px solid #ccc;padding:20px 0 10px;margin-top:20px;line-height:19px;color:#555;font-family:'Lucida Grande',Tahoma,Arial;font-size:12px;text-align:left">
	<a href="<?php echo $postLink;?>" target="_blank" style="display:inline-block;padding:5px 15px;background:#fc0;border:1px solid #caa200;border-bottom-color:#977900;color:#534200;text-shadow:0 1px 0 #ffe684;font-weight:bold;box-shadow:inset 0 1px 0 #ffe064;-moz-box-shadow:inset 0 1px 0 #ffe064;-webkit-box-shadow:inset 0 1px 0 #ffe064;border-radius:2px;moz-border-radius:2px;-webkit-border-radius:2px;text-decoration:none!important"><?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_READ_THIS_DISCUSSION' );?> &nbsp; &raquo;</a>
</div>