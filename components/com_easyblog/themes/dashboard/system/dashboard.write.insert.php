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

<script type="text/javascript">
	EasyBlog.require()
		.script("dashboard/medialink")
		.done(function($) {
			$(".ui-medialink").implement(EasyBlog.Controller.Dashboard.MediaLink);
		});
</script>

<div id="editor-content" class="clearfix">

	<div class="ui-medialink">
		<div class="ui-togmenugroup clearfix pas">
			<select id="published" name="published" class="input select float-r">
				<option value="1"<?php echo $blog->published ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_PUBLISHED');?></option>
				<option value="0"<?php echo !$blog->published ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_UNPUBLISHED');?></option>
			</select>

				<?php echo $this->fetch( 'dashboard.write.insert.post.menu.php' ); ?>

			<?php if( $this->acl->rules->upload_image || $this->acl->rules->media_places_shared) { ?>
				<i></i>
				<?php echo $this->fetch( 'dashboard.write.insert.media.menu.php' ); ?>
			<?php } ?>
				<i></i>
				<?php echo $this->fetch( 'dashboard.write.insert.video.menu.php' ); ?>

			<?php if( $system->config->get( 'layout_dashboard_zemanta' ) ) { ?>
				<i></i>
				<?php echo $this->fetch( 'dashboard.write.insert.zemanta.menu.php' ); ?>
			<?php } ?>
		</div>

			<div class="ui-togbox olderPosts">
				<?php echo $this->fetch( 'dashboard.write.insert.post.content.php' ); ?>
			</div>

			<?php if( $this->acl->rules->upload_image || $this->acl->rules->media_places_shared) { ?>
			<div class="ui-togbox insertMedia">
				<?php echo $this->fetch( 'dashboard.write.insert.media.content.php' ); ?>
			</div>
			<?php } ?>
			<div class="ui-togbox embedVideo">
				<?php echo $this->fetch( 'dashboard.write.insert.video.content.php' ); ?>
			</div>
		<?php if( $system->config->get( 'layout_dashboard_zemanta' ) ){ ?>
			<div class="ui-togbox zemantaPanel">
				<?php echo $this->fetch( 'dashboard.write.insert.zemanta.content.php' ); ?>
			</div>
		<?php } ?>
	</div>
</div>
