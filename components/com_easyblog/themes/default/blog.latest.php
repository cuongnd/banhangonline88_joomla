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
<!-- Wrapper for the front page listings -->
<div id="ezblog-body">
	<!-- Include the featured module that appears on the front page -->
	<?php echo $this->fetch( 'blog.featured.module.php' ); ?>

	<!-- The rss and headers -->
	<?php if( $this->getParam( 'show_headers_latestentries' , true ) ){ ?>
	<div id="ezblog-label" class="latest-post clearfix">
		<span><?php echo JText::_('COM_EASYBLOG_LATEST_PAGE_HEADING'); ?></span>
	</div>
	<?php } ?>

	<!-- Post listings begins here -->
	<div id="ezblog-posts"<?php echo ( $system->config->get('layout_avatar') ) ? '' : ' class="no-avatar"'; ?>>

		<!-- @module: easyblog-before-pagination -->
		<?php echo EasyBlogHelper::renderModule( 'easyblog-before-entries' ); ?>

		<?php if( $data ){ ?>
			<?php foreach( $data as $row ){ ?>
				<?php if( $system->config->get( 'main_password_protect' ) && !empty( $row->blogpassword ) ){ ?>

					<!-- Password protected theme files -->
					<?php echo $this->fetch( 'blog.item.protected.php' , array( 'row' => $row ) ); ?>

				<?php } else { ?>

					<!-- Normal post theme files -->
					<?php echo $this->fetch( 'blog.item'. EasyBlogHelper::getHelper( 'Sources' )->getTemplateFile( $row->source ) . '.php' , array( 'row' => $row ) ); ?>

				<?php } ?>
			<?php } ?>
		<?php } else { ?>

			<!-- Empty listing notice -->
			<div class="mtl"><?php echo JText::_('COM_EASYBLOG_NO_BLOG_ENTRY');?></div>

		<?php } ?>

		<!-- @module: easyblog-after-entries -->
		<?php echo EasyBlogHelper::renderModule( 'easyblog-after-entries' ); ?>

	</div>

	<?php if( $pagination ){?>
		<!-- @module: easyblog-before-pagination -->
		<?php echo EasyBlogHelper::renderModule( 'easyblog-before-pagination' ); ?>

		<!-- Pagination items -->
		<div class="eblog-pagination"><?php echo $pagination;?></div>

		<!-- @module: easyblog-after-pagination -->
		<?php echo EasyBlogHelper::renderModule( 'easyblog-after-pagination' ); ?>
	<?php } ?>
</div>
