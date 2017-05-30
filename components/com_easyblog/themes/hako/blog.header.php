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

<!-- @template: Admin tools -->
<?php echo $this->fetch( 'blog.admin.tool.php' , array( 'row' => $row ) ); ?>

<div class="hakohead clearfix">
<?php if( $system->config->get( 'layout_avatar' ) && $this->getParam( 'show_avatar_frontpage' ) ){ ?>
	<!-- @template: Avatar -->
	<?php echo $this->fetch( 'blog.avatar.php' , array( 'row' => $row ) ); ?>
<?php } ?>

<!-- Post title -->
<h2 id="title-<?php echo $row->id; ?>" class="blog-title<?php echo ($row->isFeatured) ? ' featured' : '';?> rip mbs" itemprop="name">
	<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$row->id); ?>" title="<?php echo $this->escape( $row->title );?>" itemprop="url"><?php echo $row->title; ?></a>

	<?php if( $row->isFeatured ) { ?>
		<!-- Show a featured tag if the entry is featured -->
		<sup class="tag-featured"><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURED'); ?></sup>
	<?php } ?>
</h2>

<!-- Post metadata -->
<?php echo $this->fetch( 'blog.meta.php' , array( 'row' => $row, 'postedText' => JText::_( 'COM_EASYBLOG_POSTED' ) ) ); ?>

</div><!--/hakohead-->

<!-- Content wrappings -->
<div class="blog-content clearfix">



<!-- @Trigger onAfterDisplayTitle -->
<?php echo $row->event->afterDisplayTitle; ?>

<!-- Load social buttons -->
<?php if( in_array( $system->config->get( 'main_socialbutton_position' ) , array( 'top' , 'left' , 'right' ) ) ){ ?>
	<?php echo EasyBlogHelper::showSocialButton( $row , true ); ?>
<?php } ?>

<!-- blog-text -->
<div class="blog-text clearfix prel">
