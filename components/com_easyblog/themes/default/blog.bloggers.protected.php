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
<li>
	<h3 class="blog-title rip">
		<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$entry->id); ?>"><?php echo $entry->title; ?></a>
		<?php if( $entry->isFeatured ) { ?><sup class="tag-featured"><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURED'); ?></sup><?php } ?>
	</h3>

	<div class="blog-meta fsm mts">
		<div class="in">
			<span class="blog-date"><?php echo JText::sprintf('COM_EASYBLOG_IN', EasyBlogRouter::_('index.php?option=com_easyblog&view=categories&layout=listings&id='.$entry->category_id), $entry->category); ?></span>
			<?php if( EasyBlogHelper::getHelper( 'Comment' )->isBuiltin() && $system->config->get('main_comment') && $entry->totalComments !== false ){ ?>
			- <span class="blog-comments"><a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$entry->id); ?>#comments"><?php echo $this->getNouns( 'COM_EASYBLOG_COMMENT_COUNT' , $entry->totalComments , true );?></a></span>
			<?php } ?>
		</div>
	</div>

	<div class="blog-content mts">
		<div id="ezblog-protected">
			<?php if(!empty($errmsg)) :?>
			<div class="eblog-message warning"><?php echo $errmsg; ?></div>
			<?php endif;?>

			<div id="blog-protected">
				<form method="post" action="index.php">				
					<div class="eblog-message warning"><?php echo JText::_('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_AUTHENTICATION_REQUIRE'); ?></div>
					<div class="blog-password-inst small"><?php echo JText::_('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_AUTHENTICATION_INSTRUCTION'); ?></div>

					<div class="blog-password-input ptm">
						<input type="password" name="blogpassword_<?php echo $entry->id; ?>" id="blogpassword_<?php echo $entry->id; ?>" value="">
						<input type="submit" value="<?php echo JText::_('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_READ');?>">
						<input type="hidden" name="option" value="com_easyblog">
						<input type="hidden" name="controller" value="entry">
						<input type="hidden" name="task" value="setProtectedCredentials">
						<input type="hidden" name="id" value="<?php echo $entry->id; ?>">
						<input type="hidden" name="return" value="<?php echo base64_encode( EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$entry->id, false) ); ?>">
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="mts fsm in-block width-full">
		<span class="float-r small">
		<?php echo $this->formatDate( $system->config->get('layout_shortdateformat', '%b %d'), $entry->created ); ?>
		</span>

		<?php echo $this->fetch( 'tags.item.php' , array( 'tags' => $entry->tags ) ); ?>
	</div>
</li>