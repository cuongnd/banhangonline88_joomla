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
<?php if( $this->getParam( 'show_author') || $this->getParam( 'show_created_date') || $this->getParam( 'show_category') ) { ?>
	<div class="post-author">
		<?php if( $this->getParam( 'show_author') ){ ?>
			<!-- Author information -->
			<span class="blog-author">
				<span class="ffg fsi fwb fss"><?php echo $postedText; ?> <?php echo JText::_( 'COM_EASYBLOG_BY' );?></span>
				<a href="<?php echo $row->blogger->getProfileLink(); ?>" class="fwb"><?php echo $row->blogger->getName(); ?></a>						
			</span>
		<?php } ?>

		<?php if( $this->getParam( 'show_created_date' , true ) ){ ?>
		<div class="fsm small">
			<span class="blog-date"><?php echo $this->formatDate( $system->config->get('layout_dateformat') , $row->{$this->getParam( 'creation_source')} ); ?></span>
		</div>
		<?php } ?>
	</div>

	<?php if( $this->getParam( 'show_category' , true ) ){ ?>
	<div class="post-category mtm fsm">
		<span class="ico blog-category">
			<?php $categoryName   = isset($row->category) ? $row->category : $row->getCategoryName(); ?>
			<?php echo JText::sprintf( 'COM_EASYBLOG_IN' , EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $row->category_id ), $categoryName ); ?>
		</span>
	</div>
	<?php } ?>
<?php } ?>