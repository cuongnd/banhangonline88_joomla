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

$events	= EasyBlogHelper::getHelper( 'Event' )->getFormHTML( $external , $blogSource );

if( $events )
{
?>
	<strong style="display:block;padding:5px 0;margin-top:10px;border-top:1px solid #ddd"><?php echo JText::_( 'COM_EASYBLOG_INTEGRATION_EVENTS' );?></strong>
	<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
	<input type="hidden" name="return" value="<?php echo JRequest::getVar( 'return' ); ?>" />
<?php
	echo $events;
}