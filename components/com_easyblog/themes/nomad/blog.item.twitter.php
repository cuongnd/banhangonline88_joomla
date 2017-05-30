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
<div id="entry_<?php echo $row->id; ?>" class="blog-post type-twitter micro-twitter clearfix prel type-twitter<?php echo (!empty($team)) ? 'teamblog-post' : '' ;?>" itemscope itemtype="http://schema.org/Blog">
	<div class="blog-post-in">
	<?php echo $this->fetch( 'blog.admin.tool.php' , array( 'row' => $row ) ); ?>

	<div class="blog-head">
		<?php if( $system->config->get( 'layout_avatar' ) && $this->getParam( 'show_avatar_frontpage' ) ){ ?>
			<!-- @template: Avatar -->
			<?php echo $this->fetch( 'blog.avatar.php' , array( 'row' => $row ) ); ?>
		<?php } ?>

		<div class="blog-head-in">
			<?php if( $row->isFeatured ) { ?>
				<div class="tag-featured"><div class="in"><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURED'); ?></div></div>
			<?php } ?>

			<h2 id="title_<?php echo $row->id; ?>" class="blog-title<?php echo ($row->isFeatured) ? ' featured' : '';?> rip mbm" itemprop="name">
				<?php echo EasyBlogHelper::getHelper( 'String' )->linkTweets( EasyBlogHelper::getHelper( 'String' )->url2link(  $row->text ) ); ?>
			</h2>

			<!-- Post metadata -->
			<?php echo $this->fetch( 'blog.meta.php' , array( 'row' => $row, 'postedText' => JText::_( 'COM_EASYBLOG_TWEETED' ) ) ); ?>
		</div>
		<div class="clear"></div>
	</div>

	<div class="blog-content clearfix">
		<!-- joomla content plugin call -->
		<?php echo $row->event->afterDisplayTitle; ?>

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

		<?php if( $this->getParam( 'show_tags' ) && $this->getParam( 'show_tags_frontpage' , true ) ){ ?>
			<?php echo $this->fetch( 'tags.item.php' , array( 'tags' => $row->tags ) ); ?>
		<?php } ?>

		<!-- Load bottom social buttons -->
		<?php if( $system->config->get( 'main_socialbutton_position' ) == 'bottom' ){ ?>
			<?php echo EasyBlogHelper::showSocialButton( $row , true ); ?>
		<?php } ?>

		<!-- Standard facebook like button needs to be at the bottom -->
		<?php if( $system->config->get('main_facebook_like') && $system->config->get('main_facebook_like_layout') == 'standard' && $system->config->get( 'integrations_facebook_show_in_listing') ) : ?>
			<?php echo $this->fetch( 'facebook.standard.php' , array( 'facebook' => $row->facebookLike ) ); ?>
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
