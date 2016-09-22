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
<div class="blog-meta<?php echo !empty( $row->source ) ? ' micro ' . $row->source : ''; ?>">
	<div class="in">

		<?php if( $this->getParam( 'show_created_date' ) ){ ?>
			<!-- Creation date -->
			<div class="blog-created">
				<?php //echo JText::_( 'COM_EASYBLOG_ON' ); ?>
				<!-- @php -->
				<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $row->{$this->getParam( 'creation_source')} ); ?>">
					<div class="blog-text-date"><?php echo $this->formatDate( '%d' , $row->{$this->getParam( 'creation_source')} );?></div>
					<div class="blog-text-mth"><?php echo $this->formatDate( '%b' , $row->{$this->getParam( 'creation_source')} );?></div>
				</time>
			</div>
		<?php } ?>

		<?php if( $this->getParam( 'show_hits' , true ) ){ ?>
		<div class="blog-text-hits">
			<?php echo $row->hits; ?>
			<i></i>
		</div>
		<?php } ?>


		<?php if( $system->config->get('main_comment') && $this->getParam( 'show_comments' ) ) { ?>
		<div class="blog-text-comments">
			<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$row->id); ?>#comments">
			<?php echo $row->totalComments; ?>
			<i></i>
		</a>
		</div>
		<?php } ?>
	</div>
</div>
<?php } ?>
