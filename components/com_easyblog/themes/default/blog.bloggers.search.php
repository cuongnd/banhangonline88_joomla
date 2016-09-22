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
<div id="bloggers-sorting">
	<form name="frmBlogger" id="frmBlogger" action="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=blogger' ); ?>" method="post" class="blogger-filter">
		<input type="hidden" name="option" value="com_easyblog" />
		<input type="hidden" name="view" value="blogger" class="inputbox" />
		<input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid', '');?>"/>
		<input type="text" name="search" value="<?php echo $this->escape( $search );?>" class="inputbox text mrm width-200" />
		<?php echo $sortHTML; ?>
		<input class="button" type="submit" value="<?php echo JText::_('COM_EASYBLOG_FILTER');?>" name="btnSubmit" id="btnSubmit" />
	</form>
</div>
