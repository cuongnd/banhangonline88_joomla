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
<p><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_DIALOG_REJECT_PENDING_ENTRIES_MESSAGE' );?></p>
<form name="reject-post" id="reject-post" action="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&controller=dashboard&task=rejectBlog' );?>" method="post">
<div class="mtm">
	<label for="message"><?php echo JText::_( 'Specify a reason for rejecting this post' ); ?></label>
	<textarea class="full" id="message" name="message" rows="5"></textarea>
</div>
<div class="dialog-actions">
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="ids" value="<?php echo $ids;?>" />
	<input type="button" value="<?php echo JText::_( 'COM_EASYBLOG_CANCEL_BUTTON' );?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
	<input type="submit" value="<?php echo JText::_( 'COM_EASYBLOG_PROCEED_BUTTON' );?>" class="button" />
</div>
