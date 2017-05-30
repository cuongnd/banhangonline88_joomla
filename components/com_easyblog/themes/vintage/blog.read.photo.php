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
<!-- EasyBlog wrappers -->
<div id="ezblog-body" itemscope itemtype="http://schema.org/Blog">
	<!-- Entry wrapper -->
	<div id="entry-<?php echo $blog->id; ?>" class="blog-read micro-photo clearfix">

		<?php if( !empty( $notice ) ){ ?>
			<?php echo $this->fetch( 'notice.php' , array( 'notice' => $notice ) ); ?>
		<?php } ?>

		<?php if( !$ispreview ){ ?>
			<?php echo $this->fetch( 'blog.admin.tool.php' , array( 'row' => $blog ) ); ?>
		<?php } ?>

		<!-- @module: easyblog-before-entry -->
		<?php echo EasyBlogHelper::renderModule( 'easyblog-before-entry' ); ?>

		<div class="blog-head mtm">

			<?php if( $blog->isFeatured() ) { ?>
				<div class="tag-featured"><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURED'); ?><i></i></div>
			<?php } ?>

			<!-- @template: Date -->
			<?php if( $this->getParam( 'show_created_date' ) ){ ?>
				<!-- Creation date -->
				<div class="blog-created">
					<?php //echo JText::_( 'COM_EASYBLOG_ON' ); ?>
					<!-- @php -->
					<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $blog->{$this->getParam( 'creation_source')} ); ?>">
						<div class="blog-text-date">
							<?php echo $this->formatDate( '%d %B %Y' , $blog->{$this->getParam( 'creation_source')} );?>
						</div>
					</time>
				</div>
			<?php } ?>

			<h1 id="title-<?php echo $blog->id; ?>" class="blog-title<?php echo ($isFeatured) ? ' featured-item' : '';?> rip" itemprop="name"><?php echo $blog->title; ?></h1>

			<div class="blog-horizonline">
				<div class="blog-horizonline-inner">
					<!-- @template: Avatar -->
					<?php if( $system->config->get( 'layout_avatar' ) && $system->config->get( 'layout_avatar_in_read_blog' ) && $this->getParam( 'show_avatar_entry' ) ){ ?>
						<!-- @template: Avatar -->
						<?php echo $this->fetch( 'blog.avatar.php' , array( 'row' => $blog , 'customClass' => 'blog-read-avatar' ) ); ?>
					<?php } ?>

					<?php if( $this->getParam( 'show_author') ){ ?>
					<span class="blog-author">
						<?php echo JText::_( 'COM_EASYBLOG_BY' );?> <a href="<?php echo $blog->blogger->getProfileLink(); ?>" itemprop="author"><?php echo $blog->blogger->getName(); ?></a>
					</span>
					<?php } ?>
				</div>
			</div>

		</div>

		<div class="blog-brief">
			<div class="in">

				<div class="blog-option">
					<ul class="reset-ul float-li small fsm clearfix">

						<?php if($system->config->get('main_subscription') && $blog->subscription) { ?>
						<li class="email">
							<a href="javascript:eblog.subscription.show('<?php echo EBLOG_SUBSCRIPTION_ENTRY;?>' , '<?php echo $blog->id;?>');"><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_BLOG'); ?></a>
						</li>
						<?php } ?>

						<?php echo EasyBlogHelper::getHelper( 'publishtools' )->getHTML( $blog->id ); ?>

						<?php if( $system->config->get( 'main_reporting') && ( $system->my->id > 0 || $system->my->id <= 0 && $system->config->get( 'main_reporting_guests') ) ){ ?>
						<li class="blog-report">
							<a href="javascript:void(0);" onclick="eblog.report.show( '<?php echo $blog->id;?>' , '<?php echo EBLOG_REPORTING_POST;?>' );"><?php echo JText::_( 'COM_EASYBLOG_REPORT_THIS_POST');?></a>
						</li>
						<?php } ?>

					</ul>
				</div><!--/blog-option-->
			</div>
		</div><!--blog-brief-->
		<!-- Post metadata -->


		<!-- Load social buttons -->
		<?php if( in_array( $system->config->get( 'main_socialbutton_position' ) , array( 'top' , 'left' , 'right' ) ) ){ ?>
			<?php echo EasyBlogHelper::showSocialButton( $blog); ?>
		<?php } ?>

		<!-- @Trigger: onAfterDisplayTitle -->
		<?php echo $blog->event->afterDisplayTitle; ?>
		<div class="blog-text clearfix prel mtm mbm">

			<!-- @Trigger: onBeforeDisplayContent -->
			<?php echo $blog->event->beforeDisplayContent; ?>

			<!-- Table of contents if necessary -->
			<?php if(!empty($blog->toc)){ echo $blog->toc; } ?>

			<!-- @module: easyblog-before-content -->
			<?php echo EasyBlogHelper::renderModule( 'easyblog-before-content' ); ?>

			<!-- Photo items -->
			<?php if( $blog->images ){ ?>
				<?php foreach( $blog->images as $image ){ ?>
					<p class="photo-source">
						<span>
							<a href="<?php echo $image;?>" target="_blank"><img src="<?php echo $image;?>" /></a>
						</span>
					</p>
				<?php } ?>
			<?php } ?>

			<!-- Post content -->
			<?php echo $blog->content; ?>

			<!-- @module: easyblog-after-content -->
			<?php echo EasyBlogHelper::renderModule( 'easyblog-after-content' ); ?>

			<?php if( $system->config->get( 'main_locations_blog_entry' ) ){ ?>
				<?php echo EasyBlogHelper::getHelper( 'Maps' )->getHTML( true ,
																		$blog->address,
																		$blog->latitude,
																		$blog->longitude ,
																		$system->config->get( 'main_locations_blog_map_width') ,
																		$system->config->get( 'main_locations_blog_map_height' ),
																		JText::sprintf( 'COM_EASYBLOG_LOCATIONS_BLOG_POSTED_FROM' , $blog->address ),
																		'post_map_canvas_' . $blog->id );?>
			<?php } ?>

			<!-- @Trigger: onAfterDisplayContent -->
			<?php echo $blog->event->afterDisplayContent; ?>

			<!-- Copyright text -->
			<?php if( $system->config->get( 'layout_copyrights' ) && !empty($blog->copyrights) ) { ?>
				<?php echo $this->fetch( 'blog.copyright.php' , array( 'row' => $blog ) ); ?>
			<?php } ?>

			<?php if( $this->getParam( 'show_last_modified' ) ){ ?>
				<!-- Modified date -->
				<span class="blog-modified-date">
					<?php echo JText::_( 'COM_EASYBLOG_LAST_MODIFIED' ); ?>
					<?php echo JText::_( 'COM_EASYBLOG_ON' ); ?>
					<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $blog->modified ); ?>">
						<span><?php echo $this->formatDate( $system->config->get('layout_dateformat') , $blog->modified ); ?></span>
					</time>
				</span>
			<?php } ?>

		</div>

		<?php if( $this->getparam( 'show_tags' ) ){ ?>
			<?php echo $this->fetch( 'tags.item.php' , array( 'tags' => $tags ) ); ?>
		<?php } ?>

		<?php if( $system->config->get( 'main_socialbutton_position' ) == 'bottom'){ ?>
			<?php echo EasyBlogHelper::showSocialButton($blog); ?>
		<?php } ?>

		<!-- Standard facebook like button needs to be at the bottom -->
		<?php if( $system->config->get('main_facebook_like') && $system->config->get('main_facebook_like_layout') == 'standard' ){ ?>
			<?php echo $this->fetch( 'facebook.standard.php' , array( 'facebook' => $facebookLike ) ); ?>
			<div class="clear"></div>
		<?php } ?>

		<!-- Bottom metadata -->
		<?php echo $this->fetch( 'blog.read.meta.bottom.php') ?>

	</div>
	<?php echo $this->fetch( 'blog.read.navigation.php' , array( 'nextLink' => $nextLink , 'prevLink' => $prevLink ) ); ?>

	<?php echo $adsenseHTML; ?>

	<?php echo $this->fetch( 'blog.read.tabs.php' , array( 'related' => $blogRelatedPost ) ); ?>

	<?php if( $system->config->get('main_showauthorinfo') ) { ?>
		<!-- Blog Author's section -->
		<?php echo $this->fetch( 'author.info.php' ); ?>
	<?php } ?>

	<?php if( $system->config->get('main_comment') ) { ?>
		<!-- Comment form and comment listings -->
		<?php echo $commentHTML;?>
	<?php } ?>
</div>
