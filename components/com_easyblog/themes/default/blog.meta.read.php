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

// $extraClass     = 'blog-meta';

// Altium
// If view is entry, use 'fsm'
// If view is latest or other, use mbm
// if type is micro use micro , else twitter or email

// Default
// If view is not entry use 'mbm'
// If view is not entry and microtype use class 'micro'
// If twitter use 'twitter'
?>
<?php if( $this->getParam( 'show_author') || $this->getParam( 'show_created_date') || $this->getParam( 'show_category') ) { ?>
<div class="blog-meta<?php echo !empty( $row->source ) ? ' micro ' . $row->source : ''; ?>">
	<div class="in">		
		<?php if( $this->getParam( 'show_author') ){ ?>
			<span><?php echo $postedText; ?></span>
			<!-- Author information -->
			<span class="blog-author">
				<?php echo JText::_( 'COM_EASYBLOG_BY' );?>
				<a href="<?php echo $row->blogger->getProfileLink(); ?>" itemprop="author"><?php echo $row->blogger->getName(); ?></a>
			</span>
		<?php } ?>

		<?php if( $this->getParam( 'show_created_date' ) ){ ?>
			<!-- Creation date -->
			<span class="blog-created">
				<?php echo JText::_( 'COM_EASYBLOG_ON' ); ?>
				<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $row->{$this->getParam( 'creation_source')} ); ?>">
					<span><?php echo $this->formatDate( $system->config->get('layout_dateformat') , $row->{$this->getParam( 'creation_source')} ); ?></span>
				</time>
			</span>
		<?php } ?>

		<?php if( $this->getParam( 'show_category' ) ){ ?>
			<!-- Category info -->
			<span class="blog-category">
				<?php $categoryName   = isset($row->category) ? $row->category : $row->getCategoryName(); ?>
				<?php echo JText::sprintf( 'COM_EASYBLOG_IN' , EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $row->category_id ), $categoryName ); ?>
			</span>
		<?php } ?>
	</div>
</div>
<?php } ?>