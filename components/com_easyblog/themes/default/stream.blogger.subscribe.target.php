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
<li class="subscriber-plus">
    <span class="small"><?php echo $time; ?></span>
	<?php echo EasyBlogHelper::getHelper( 'string')->getNoun('COM_EASYBLOG_STREAM_BLOGGER_NEW_SUBSCRIBER', $actorCnt, true); ?>
	<?php echo JText::sprintf('COM_EASYBLOG_STREAM_BLOGGER_NEW_SUBSCRIBER_TO_BLOG', $target);?>
    <div class="stream-comment clearfix">
        <b><?php echo $actor; ?></b>
    </div>
</li>