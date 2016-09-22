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
<!-- Post item wrapper -->
<div id="entry-<?php echo $row->id; ?>" class="blog-post clearfix prel<?php echo (!empty($row->team)) ? ' teamblog-post' : '' ;?>" itemscope itemtype="http://schema.org/Blog">
	<div class="blog-post-in">

		<div class="blog-head">
			<!-- @template: Admin tools -->
			<?php echo $this->fetch( 'blog.admin.tool.php' , array( 'row' => $row ) ); ?>

			<?php if( $system->config->get( 'layout_avatar' ) && $this->getParam( 'show_avatar_frontpage' ) ){ ?>
				<!-- @template: Avatar -->
				<?php echo $this->fetch( 'blog.avatar.php' , array( 'row' => $row ) ); ?>
			<?php } ?>

			<div class="blog-head-in">
				<!-- Post title -->
				<h2 id="title_<?php echo $row->id; ?>" class="blog-title<?php echo ($row->isFeatured) ? ' featured' : '';?> rip mbs" itemprop="name">
					<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$row->id); ?>" title="<?php echo $this->escape( $row->title );?>" itemprop="url"><?php echo $row->title; ?></a>

					<?php if( $row->isFeatured ) { ?>
						<!-- Show a featured tag if the entry is featured -->
						<sup class="tag-featured"><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURED'); ?></sup>
					<?php } ?>
				</h2>

				<!-- Post metadata -->
				<?php echo $this->fetch( 'blog.meta.php' , array( 'row' => $row, 'postedText' => JText::_( 'COM_EASYBLOG_VIA_EMAIL' ) ) ); ?>
			</div>
			<div class="clear"></div>
		</div>

		<!-- Content wrappings -->
		<div class="blog-content clearfix">

			<!-- @Trigger onAfterDisplayTitle -->
			<?php echo $row->event->afterDisplayTitle; ?>

			<!-- Post content -->
			<div class="blog-text clearfix prel">

				<!-- @Trigger: onBeforeDisplayContent -->
				<?php echo $row->event->beforeDisplayContent; ?>

				<!-- Load social buttons -->
				<?php if( in_array( $system->config->get( 'main_socialbutton_position' ) , array( 'top' , 'left' , 'right' ) ) ){ ?>
					<?php echo EasyBlogHelper::showSocialButton( $row , true ); ?>
				<?php } ?>

				<!-- Post content -->
				<?php echo $row->text; ?>

				<!-- @Trigger: onAfterDisplayContent -->
				<?php echo $row->event->afterDisplayContent; ?>

				<!-- Copyright text -->
				<?php if( $system->config->get( 'layout_copyrights' ) && !empty($row->copyrights) ) { ?>
					<?php echo $this->fetch( 'blog.copyright.php' , array( 'row' => $row ) ); ?>
				<?php } ?>

				<!-- Maps -->
				<?php if( $system->config->get( 'main_locations_blog_frontpage' ) ){ ?>
					<?php echo EasyBlogHelper::getHelper( 'Maps' )->getHTML( true ,
																			$row->address,
																			$row->latitude ,
																			$row->longitude ,
																			$system->config->get( 'main_locations_blog_map_width') ,
																			$system->config->get( 'main_locations_blog_map_height' ),
																			JText::sprintf( 'COM_EASYBLOG_LOCATIONS_BLOG_POSTED_FROM' , $row->address ),
																			'post_map_canvas_' . $row->id );?>
				<?php } ?>
			</div>

			<?php if( $this->getParam( 'show_last_modified' ) ){ ?>
				<!-- Modified date -->
				<span class="blog-modified-date">
					<?php echo JText::_( 'COM_EASYBLOG_LAST_MODIFIED' ); ?>
					<?php echo JText::_( 'COM_EASYBLOG_ON' ); ?>
					<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $row->modified ); ?>">
						<span><?php echo $this->formatDate( $system->config->get('layout_dateformat') , $row->modified ); ?></span>
					</time>
				</span>
			<?php } ?>

			<?php if( $this->getparam( 'show_tags' ) ){ ?>
				<?php echo $this->fetch( 'tags.item.php' , array( 'tags' => $row->tags ) ); ?>
			<?php } ?>

			<!-- Load bottom social buttons -->
			<?php if( $system->config->get( 'main_socialbutton_position' ) == 'bottom' ){ ?>
				<?php echo EasyBlogHelper::showSocialButton( $row , true ); ?>
			<?php } ?>

			<!-- Standard facebook like button needs to be at the bottom -->
			<?php if( $system->config->get('main_facebook_like') && $system->config->get('main_facebook_like_layout') == 'standard' && $system->config->get( 'integrations_facebook_show_in_listing') ) : ?>
				<div class="facebook-likes mtm clearfix">
					<div id="eb-fblikes" class="align<?php echo ($this->getDirection() == 'rtl') ? 'right' : 'left'; ?>">
						<?php echo $row->facebookLike; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if( $system->config->get( 'layout_showcomment' ) && EasyBlogHelper::getHelper( 'Comment')->isBuiltin() ){ ?>
				<!-- Recent comment listings on the frontpage -->
				<?php echo $this->fetch( 'blog.item.comment.list.php' , array( 'row' => $row ) ); ?>
			<?php } ?>
		</div>

		<!-- Bottom metadata -->
		<?php echo $this->fetch( 'blog.meta.bottom.php' , array( 'row' => $row ) ); ?>
	</div>
</div>
