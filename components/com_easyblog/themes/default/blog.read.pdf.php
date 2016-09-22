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
$url = rtrim( JURI::base() , '/' );
?>

<h1><a href="<?php echo $url . EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$blog->id); ?>"><?php echo $blog->title; ?></a></h1>
<?php
$date		= EasyBlogDateHelper::dateWithOffSet($blog->created);
$postdate 	= EasyBlogDateHelper::toFormat($date, $config->get('layout_shortdateformat', '%b %d'));
?>
<p class="meta-bottom">
<?php echo JText::_('COM_EASYBLOG_POSTED_ON'); ?> <?php echo $postdate; ?>,
<?php echo JText::sprintf('COM_EASYBLOG_POSTED_BY_AUTHOR', $url . $blogger->getProfileLink(), $blogger->displayName); ?>
<?php echo JText::_('COM_EASYBLOG_CATEGORY'); ?> <a href="<?php echo $url . EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$blog->category_id); ?>"><?php echo $this->escape($blog->getCategoryName()); ?></a>
</p>
<?php echo $blog->event->afterDisplayTitle; ?>
<?php echo $blog->event->beforeDisplayContent; ?>
<?php if(!empty($blog->toc)){ echo $blog->toc; }?>
<?php echo $blog->intro; ?>
<?php echo $blog->content; ?>
<?php echo $blog->event->afterDisplayContent; ?>
<p><?php echo $blog->copyrights; ?></p>
<p><?php echo JText::_('COM_EASYBLOG_TAGS'); ?>: <?php echo $tags; ?></p>
