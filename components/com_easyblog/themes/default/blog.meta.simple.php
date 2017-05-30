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

$customClass    = isset( $this->customClass ) ? $this->customClass : '';
?>
<?php if( $this->getParam( 'show_author') || ( $system->config->get('main_comment') && $this->getParam( 'show_comments' ) ) ) { ?>
	<div class="blog-meta fsm mts <?php echo $customClass; ?>">
		<div class="in">

		<?php if( $this->getParam( 'show_author') ) { ?>
			<!-- author info -->
			<?php echo $postedText; ?>
			<a href="<?php echo $entry->blogger->getProfileLink(); ?>" itemprop="author"><?php echo $entry->blogger->getName();?></a>

		<?php } ?>

		<?php if( $this->getParam( 'show_created_date' ) ){ ?>
			<!-- Creation date -->
			<?php echo JText::_( 'COM_EASYBLOG_ON' ); ?>
			<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $entry->{$this->getParam( 'creation_source')} ); ?>">
				<span><?php echo $this->formatDate( $system->config->get('layout_dateformat') , $entry->{$this->getParam( 'creation_source')} ); ?></span>
			</time>
		<?php } ?>

		<?php echo $this->fetch( 'blog.item.comment.php' , array( 'row' => $entry ) ); ?>
		</div>
	</div>
<?php } ?>