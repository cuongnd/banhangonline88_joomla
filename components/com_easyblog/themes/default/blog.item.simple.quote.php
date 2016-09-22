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
<li class="blog-post micro-quote<?php echo isset( $customClass ) ? ' item-' . $customClass : '';?>" itemscope itemtype="http://schema.org/Blog">
	<div class="blog-quote">
		<h3 class="blog-title rip" itemprop="name">
			<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id=' . $entry->id ); ?>" itemprop="url"><?php echo $entry->title; ?></a>
			<?php if( $entry->isFeatured ) { ?><sup class="tag-featured"><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURED'); ?></sup><?php } ?>
		</h3>
	</div>

	<?php echo $this->fetch( 'blog.meta.simple.php' , array( 'entry' => $entry, 'postedText' => JText::_( 'COM_EASYBLOG_QUOTE_SHARED_BY' ) ) ); ?>

	<div class="blog-content mts">
		<?php echo JString::substr( strip_tags( $entry->text ) , 0 , 350 ); ?> ...

		<?php if( $entry->readmore ) { ?>
			<div class="blog-readmore mtl mbm">
				<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id=' . $entry->id ); ?>">
					<span><?php echo JText::_('COM_EASYBLOG_CONTINUE_READING'); ?></span>
				</a>
			</div>
		<?php } ?>
	</div>

	<?php if( $this->getparam( 'show_tags' , true ) && !empty($entry->tags) ){ ?>
	<div class="mts">
		<?php echo $this->fetch( 'tags.item.php' , array( 'tags' => $entry->tags ) ); ?>
	</div>
	<?php } ?>
</li>