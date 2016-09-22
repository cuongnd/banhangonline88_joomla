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
<!-- Protected blog wrapper -->
<div id="entry-<?php echo $row->id; ?>" class="blog-post clearfix prel blog-protected">
	<div class="blog-post-in">

		<!-- Admin tools -->
		<?php echo $this->fetch( 'blog.admin.tool.php' , array( 'row' => $row ) ); ?>

		<div class="blog-header clearfix">

			<?php if( $system->config->get( 'layout_avatar' ) && $this->getParam( 'show_avatar_frontpage' ) ){ ?>
				<!-- @template: Avatar -->
				<?php echo $this->fetch( 'blog.avatar.php' , array( 'row' => $row ) ); ?>
			<?php } ?>


			<div class="blog-cap">
				<h2 id="title-<?php echo $row->id; ?>" class="blog-title<?php echo ($row->isFeatured) ? ' featured' : '';?> rip mbs blog-title-protected">
					<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$row->id); ?>" title="<?php echo $this->escape( $row->title );?>"><?php echo $row->title; ?></a>
					<?php if( $row->isFeatured ) { ?>
						<sup class="tag-featured"><?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURED'); ?></sup>
					<?php } ?>
				</h2>

				<!-- Post metadata -->
				<?php echo $this->fetch( 'blog.meta.php' , array( 'row' => $row, 'postedText' => JText::_( 'COM_EASYBLOG_POSTED' ) ) ); ?>
			</div>

		</div>

		<div class="blog-content clearfix">

			<div class="blog-text clearfix prel">
				<div id="ezblog-protected">
					<?php if(!empty($errmsg)) :?>
					<div class="eblog-message warning"><?php echo $errmsg; ?></div>
					<?php endif;?>

					<div id="blog-protected">
						<form method="post" action="index.php">				
							<div class="eblog-message warning"><?php echo JText::_('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_AUTHENTICATION_REQUIRE'); ?></div>
							<div class="blog-password-inst small"><?php echo JText::_('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_AUTHENTICATION_INSTRUCTION'); ?></div>

							<div class="blog-password-input ptm">
								<input type="password" name="blogpassword_<?php echo $row->id; ?>" id="blogpassword_<?php echo $row->id; ?>" value="">
								<input type="submit" value="<?php echo JText::_('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_READ');?>">
								<input type="hidden" name="option" value="com_easyblog">
								<input type="hidden" name="controller" value="entry">
								<input type="hidden" name="task" value="setProtectedCredentials">
								<input type="hidden" name="id" value="<?php echo $row->id; ?>">
								<input type="hidden" name="return" value="<?php echo base64_encode( EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$row->id, false) ); ?>">
							</div>
						</form>
					</div>

				</div>
			</div>

		</div>

	</div>

</div>
