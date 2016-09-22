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
<p><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_ENTRIES_CONFIRM_DELETE_MESSAGE' );?></p>
<form id="dashboard" name="dashboard" method="post" action="<?php echo JRoute::_('index.php');?>">
	<input type="hidden" name="blogId" value="<?php echo $ids; ?>" />
	<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="controller" value="dashboard" />
	<input type="hidden" name="from" value="dashboard" />
	<input type="hidden" name="task" value="deleteBlog" />
	<?php echo JHTML::_( 'form.token' ); ?>
	<div class="dialog-actions">
		<input type="button" value="<?php echo JText::_( 'COM_EASYBLOG_CANCEL_BUTTON' );?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
		<input type="submit" value="<?php echo JText::_( 'COM_EASYBLOG_PROCEED_BUTTON' );?>" class="button" />
	</div>
</form>
