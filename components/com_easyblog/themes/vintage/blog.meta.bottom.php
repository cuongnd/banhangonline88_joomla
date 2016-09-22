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
		<!-- Category info -->
		<span class="blog-text-categories">
			<i></i>
			<?php $categoryName   = isset($row->category) ? JText::_( $row->category ) : $row->getCategoryName(); ?>
			<?php echo JText::sprintf( 'COM_EASYBLOG_IN' , EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $row->category_id ), $categoryName ); ?>
		</span>
		<?php } ?>

		<?php if( $this->getParam( 'show_hits' , true ) ){ ?>
		<span class="blog-text-hits">
			<i></i>
			<?php echo $row->hits; ?>
		</span>
		<?php } ?>

		<?php if( $system->config->get('main_comment') && $this->getParam( 'show_comments' ) ) { ?>
		<span class="blog-text-comments">
			<i></i>
			<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$row->id); ?>#comments">
			<?php echo $row->totalComments; ?>
			</a>
		</span>
		<?php } ?>

		<?php if( $system->config->get( 'main_ratings_frontpage' ) ) { ?>
			<!-- Blog ratings -->
			<?php echo $this->fetch( 'blog.rating.php' , array( 'row' => $row , 'locked' => $system->config->get( 'main_ratings_frontpage_locked' ) ) ); ?>
		<?php } ?>
	</div>
</div>
