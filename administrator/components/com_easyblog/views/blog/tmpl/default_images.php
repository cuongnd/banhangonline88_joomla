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
<?php if( $this->config->get( 'main_media_manager' ) ){ ?>
<a href="javascript:void(0);" class="ico-dimage float-l prel mrs ui-togmenu insertMedia" togbox="insertMedia">
	<b><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_MEDIA' );?></b>
	<span class="ui-toolnote">
		<i></i>
		<b><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_MEDIA' );?></b>
		<span><?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_INSERT_MEDIA_TIPS'); ?></span>
	</span>
</a>
<?php } ?>