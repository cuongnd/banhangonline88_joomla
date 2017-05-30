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
<div class="blog-meta-bottom fsm mtm">
	<div class="in clearfix">
		<?php if( $this->getParam( 'show_hits' , true ) ){ ?>
			<span class="blog-hit"><?php echo JText::sprintf( 'COM_EASYBLOG_HITS_TOTAL' , $row->hits ); ?></span>
		<?php } ?>

		<?php echo $this->fetch( 'blog.item.comment.php' , array( 'row' => $row ) ); ?>

		<?php if( $system->config->get( 'main_ratings_frontpage' ) ) { ?>
			<!-- Blog ratings -->
			<?php echo $this->fetch( 'blog.rating.php' , array( 'row' => $row , 'locked' => $system->config->get( 'main_ratings_frontpage_locked' ) ) ); ?>
		<?php } ?>

		<?php if( $row->readmore ) { ?>
			<!-- Readmore link -->
			<?php echo $this->fetch( 'blog.readmore.php' , array( 'row' => $row ) ); ?>
		<?php } ?>
	</div>
</div>