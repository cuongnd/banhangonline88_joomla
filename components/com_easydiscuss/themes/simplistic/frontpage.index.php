<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<div class="discuss-main-list">
	<div class="tab-content">
		<div class="tab-pane active discussPostsList" data-view="index" data-id="<?php echo is_array( $categories ) ? implode( ',' , $categories ) : $categories; ?>">
			<?php if( !empty( $featured ) ){ ?>
				<h4 class="discuss-featured-title"><?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_TOPICS' );?></h4>
				<hr />
				
				<ul class="unstyled discuss-list featured clearfix" itemscope itemtype="http://schema.org/ItemList">
					<?php foreach( $featured as $featuredPost ){ ?>
						<?php echo $this->loadTemplate( 'frontpage.post.php' , array( 'post' => $featuredPost ) ); ?>
					<?php } ?>
				</ul>
			<?php } ?>

			<div class="categoryFilters">
				<?php if( !JRequest::getVar('layout') == 'user' ){ ?>
					<?php echo $this->loadTemplate( 'frontpage.index.filters.php' ); ?>
				<?php } ?>
			</div>

			<div class="clearfix"></div>

			<?php if( $posts ){ ?>
			<ul class="unstyled discuss-list normal clearfix" itemscope itemtype="http://schema.org/ItemList">
				<div class="loading-bar loader" style="display:none;">
					<div class="discuss-loader"><?php echo JText::_( 'COM_EASYDISCUSS_LOADING'); ?></div>
				</div>

				<?php foreach( $posts as $post ){ ?>
					<?php echo $this->loadTemplate( 'frontpage.post.php' , array( 'post' => $post ) ); ?>
				<?php } ?>
			</ul>
			<?php } else { ?>
				<div class="empty">
					<div><?php echo JText::_( 'COM_EASYDISCUSS_EMPTY_DISCUSSION_LIST' );?></div>
				</div>
				<div class="hr-style1"></div>
			<?php } ?>

			<?php if( $pagination ){ ?>
				<div class="dc-pagination">
				<?php echo $pagination->getPagesLinks();?>
				</div>
			<?php } else { ?>
				<div class="pull-right">
					<a href="<?php echo DiscussRouter::getCategoryRoute( $category->id );?>"><?php echo JText::_( 'COM_EASYDISCUSS_SHOW_MORE_POSTS' ); ?> <i class="icon-chevron-right"></i> </a>
				</div>
				<div class="clearfix"></div>
			<?php } ?>

		</div>
	</div>
</div>

<?php if( $system->config->get( 'layout_board_stats' ) ){ ?>
	<?php echo DiscussHelper::getBoardStatistics(); ?>
<?php } ?>
