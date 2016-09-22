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
<div id="ezblog-body">
	<div id="ezblog-section">
		<span><?php echo JText::_('COM_EASYBLOG_FEATURED_PAGE_HEADING'); ?></span>
		<?php if( $system->config->get( 'main_rss' ) ){ ?>
		<a href="<?php echo  EasyBlogHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easyblog&view=featured' );?>" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?>" class="float-r ico link-rss"><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS'); ?></a>
		<?php } ?>
	</div>

	<div id="ezblog-posts" class="blog-list featured-list-view">
		<?php if( $data ){ ?>
			<?php foreach( $data as $row ){ ?>
				<?php echo $this->fetch( 'blog.item'. EasyBlogHelper::getHelper( 'Sources' )->getTemplateFile( $row->source ) . '.php' , array( 'row' => $row ) ); ?>
			<?php } ?>
		<?php } else { ?>
			<div><?php echo JText::_('COM_EASYBLOG_NO_BLOG_ENTRY');?></div>
		<?php } ?>

		<?php if( $pagination ){ ?>
			<div class="eblog-pagination"><?php echo $pagination;?></div>
		<?php } ?>
	</div>
</div>
