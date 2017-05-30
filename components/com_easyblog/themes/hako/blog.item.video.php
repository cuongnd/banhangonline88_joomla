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
<div id="entry-<?php echo $row->id; ?>" class="blog-post micro-video clearfix prel<?php echo (!empty($row->team)) ? ' teamblog-post' : '' ;?>" itemscope itemtype="http://schema.org/Blog">
	<div class="blog-post-in">

		<!-- @template: blog.header -->
		<?php echo $this->fetch( 'blog.header.php' , array( 'row' => $row ) ); ?>

				<!-- @Trigger: onBeforeDisplayContent -->
				<?php echo $row->event->beforeDisplayContent; ?>

				<!-- Video items -->
				<?php if( $row->videos ){ ?>
					<?php foreach( $row->videos as $video ){ ?>
						<p class="video-source">
							<?php echo $video->html; ?>
						</p>
					<?php } ?>
				<?php } ?>

				<!-- Post content -->
				<?php echo $row->text; ?>

				<!-- @Trigger: onAfterDisplayContent -->
				<?php echo $row->event->afterDisplayContent; ?>


		<!-- @template: blog.footer -->
		<?php echo $this->fetch( 'blog.footer.php' , array( 'row' => $row ) ); ?>

	</div><!--/blog-post-in-->
</div><!--/blog-post-->
