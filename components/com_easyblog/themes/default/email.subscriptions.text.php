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


<?php echo JText::_( 'COM_EASYBLOG_NOTIFICATION_NEW_SUBSCRIPTIONS' ); echo JText::_( 'COM_EASYBLOG_SUBSCRIPTION_TYPE_' . strtoupper( $type ) ); ?>.


<?php echo JText::_( 'COM_EASYBLOG_EMAIL_TITLE' );?>:
<?php echo $title; ?>


<?php echo JText::_( 'COM_EASYBLOG_SUBSCRIBER' );?>:
<?php echo $subscriber;?>

<?php
	if( isset( $reviewLink ) )
	{
		echo JText::_( 'COM_EASYBLOG_NOTIFICATION_REVIEW_POST_TEXT' ) . ': ';
		echo $reviewLink;
	}
?>

