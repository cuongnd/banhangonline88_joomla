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

<?php echo $this->fetch( 'media.php' ); ?>

<script type="text/javascript">
EasyBlog.require()
	.script("media")
	.done(function($) {
		var mediaLauncher = $(".mediaLauncher");
		mediaLauncher.implement(EasyBlog.Controller.MediaLauncher, {});
	});
</script>

<div class="mediaLauncher clearfix">
	<?php if( $this->acl->rules->upload_image ) { ?>
	<a class="uploadImageButton launcher-button">
		<span><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_UPLOAD_A_MEDIA' ); ?></span>
	</a>
	<?php } ?>
	<a class="chooseImageButton launcher-button">
		<span><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_CHOOSE_A_MEDIA' ); ?></span>
	</a>
</div>
