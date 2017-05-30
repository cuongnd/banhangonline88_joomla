<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<form name="edit-comment" id="edit-comment" action="" method="post">
<p><?php echo JText::_( 'COM_EASYBLOG_DELETE_COMMENTS_TIPS' ); ?></p>
<input class="inputbox" type="hidden" name="commentId" value="<?php echo $comment->id; ?>" />
<input class="inputbox" type="hidden" name="controller" value="entry" />
<input class="inputbox" type="hidden" name="task" value="deleteComment" />
<?php echo JHTML::_( 'form.token' ); ?>
<div class="dialog-actions">
	<input type="button" value="<?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON');?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
	<input type="submit" value="<?php echo JText::_('COM_EASYBLOG_PROCEED_BUTTON');?>" class="button" id="edialog-submit" name="edialog-submit" />
</div>
</form>
