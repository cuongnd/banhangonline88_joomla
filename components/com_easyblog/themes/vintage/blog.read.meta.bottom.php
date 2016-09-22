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
<!-- Bottom metadata -->
<div class="blog-meta-bottom fsm mtl">
	<div class="in clearfix">

		<?php if( $this->getParam( 'show_category' ) ){ ?>
		<span class="blog-category">
			<?php $categoryName   = isset($blog->category) ? $blog->category : $blog->getCategoryName(); ?>
			<?php echo JText::sprintf( 'COM_EASYBLOG_IN' , EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $blog->category_id ), $categoryName ); ?>
		</span>
		<?php } ?>

		<?php if( $this->getParam( 'show_hits' , true ) ){ ?>
		<span class="blog-text-hits">
			<i></i>
			<?php echo JText::sprintf( 'COM_EASYBLOG_HITS_TOTAL' , $blog->hits ); ?>
		</span>
		<?php } ?>

		<?php if( $system->config->get('main_comment') && $blog->totalComments !== false && $this->getParam( 'show_comments' ) ){ ?>
		<span class="blog-text-comments">
			<i></i>
			<?php if( $system->config->get('comment_disqus') ) { ?>
				<?php echo $blog->totalComments; ?>
			<?php } else { ?>
				<a href="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id ); ?>#comments"><?php echo $this->getNouns( 'COM_EASYBLOG_COMMENT_COUNT' , $blog->totalComments , true ); ?></a>
			<?php } ?>
		</span>
		<?php } ?>

		<?php if( $system->config->get( 'main_ratings_frontpage' ) ) { ?>
			<!-- Blog ratings -->
			<?php echo $this->fetch( 'blog.rating.php' , array( 'row' => $blog , 'locked' => $system->config->get( 'main_ratings_frontpage_locked' ) ) ); ?>
		<?php } ?>
	</div>
</div>
