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
<p><?php echo JText::_( 'COM_EASYBLOG_ARE_YOU_SURE_YOU_WANT_TO_REMOVE_TAG' );?></p>
<form id="dashboard" name="dashboard" method="post" action="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&controller=dashboard&task=deleteTag' );?>">
	<input type="hidden" name="tagId" value="<?php echo $id;?>" />
	<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
	
	<div class="dialog-actions">
		<input type="button" value="<?php echo JText::_( 'COM_EASYBLOG_CANCEL_BUTTON' );?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
		<input type="submit" value="<?php echo JText::_( 'COM_EASYBLOG_PROCEED_BUTTON' );?>" class="button" />
	</div>
</form>
