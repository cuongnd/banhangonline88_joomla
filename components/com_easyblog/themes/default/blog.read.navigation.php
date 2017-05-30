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
<?php if ( !empty( $prevLink ) || !empty( $nextLink ) ){ ?>
<!-- Blog navigations for previous / next link -->
<ul class="blog-navi reset-ul float-li clearfix">
	<?php if( !empty( $prevLink ) ){ ?>
	<li class="entry-prev float-l">
		<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id=' . $prevLink['id'] ); ?>" title="<?php echo JText::sprintf('COM_EASYBLOG_ENTRY_BLOG_PREVIOUS_TITLE', $this->escape( $prevLink['title'] ) ); ?>">
			<?php echo JText::sprintf('COM_EASYBLOG_BLOG_ENTRY_PREV', $prevLink['title']); ?>
		</a>
	</li>
	<?php } ?>

	<?php if( !empty( $nextLink ) ){ ?>
	<li class="entry-next float-r">
		<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id=' . $nextLink['id'] ); ?>" title="<?php echo JText::sprintf('COM_EASYBLOG_ENTRY_BLOG_NEXT_TITLE', $this->escape( $nextLink['title'] ) ); ?>">
			<?php echo JText::sprintf('COM_EASYBLOG_BLOG_ENTRY_NEXT', $nextLink['title']); ?>
		</a>
	</li>
	<?php } ?>
</ul>
<?php } ?>
