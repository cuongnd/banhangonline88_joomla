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
<form id="deletePostForm" class="si_form" action="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=conversation&task=delete' );?>" method="post">
<p><?php echo JText::_( 'COM_EASYDISCUSS_CONFIRM_DELETE_CONVERSATION' );?></p>
<input type="hidden" name="id" value="<?php echo $id;?>" />
<?php echo JHTML::_( 'form.token' );?>
</form>
