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
<?php echo JText::_( 'COM_EASYBLOG_NOTIFICATION_HELLO' );?>,


<?php echo JText::_( 'COM_EASYBLOG_NOTIFICATION_NEW_BLOG_PENDING_REVIEW' ); ?>


<?php echo JText::_( 'COM_EASYBLOG_NOTIFICATION_BLOG_TITLE' );?>:
<?php echo $blogTitle; ?>


<?php echo JText::_( 'COM_EASYBLOG_NOTIFICATION_BLOG_AUTHOR' );?>:
<?php echo $blogAuthor; ?>


<?php echo JText::_( 'COM_EASYBLOG_NOTIFICATION_BLOG_CONTENT' );?>:
<?php echo strip_tags( $blogIntro ); ?>


<?php echo JText::_( 'COM_EASYBLOG_NOTIFICATION_REVIEW_POST_TEXT' );?>: <?php echo $reviewLink;?>