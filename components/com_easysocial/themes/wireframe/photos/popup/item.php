<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div data-photo-item="<?php echo $photo->uuid(); ?>"
	 data-photo-id="<?php echo $photo->id; ?>"
     class="layout-<?php echo $options['layout']; ?> es-media-item es-photo-item<?php echo $photo->isFeatured() ? ' featured' : '';?>" data-es-photo-disabled="1">

	<div data-photo-header class="es-media-header es-photo-header">
		<?php echo $this->includeTemplate('site/photos/popup/menu'); ?>
	</div>

	<div data-photo-content class="es-photo-content">
		<?php echo $this->render( 'module' , 'es-photos-before-photo' ); ?>

		<div style="position: absolute; width: 100%; height: 100%; top: 0; left: 0;">
			<div class="es-photo">
				<a data-photo-image-link
				   href="<?php echo $photo->getPermalink();?>"
				   title="<?php echo $this->html('string.escape', $photo->title . (($photo->caption!=='') ? ' - ' . $photo->caption : '')); ?>">
					<u data-photo-viewport>
						<b data-mode="<?php echo $options['resizeMode']; ?>"
						   data-threshold="<?php echo $options['resizeThreshold']; ?>">
							<img data-photo-image
								 src="<?php echo $photo->getSource($options['size']); ?>"
								 data-thumbnail-src="<?php echo $photo->getSource('thumbnail'); ?>"
								 data-featured-src="<?php echo $photo->getSource('featured'); ?>"
								 data-large-src="<?php echo $photo->getSource('large'); ?>"
								 data-width="<?php echo $photo->getWidth(); ?>"
								 data-height="<?php echo $photo->getHeight(); ?>"
								 onload="window.ESImage ? ESImage(this) : (window.ESImageList || (window.ESImageList=[])).push(this);" />
						</b>
						<?php if ($options['showNavigation']) { ?>
							<?php echo $this->includeTemplate('site/photos/navigation'); ?>
						<?php } ?>
					</u>
				</a>
				<?php if ($lib->taggable()) { ?>
				<div class="es-photo-hint tag-hint alert">
					<?php echo JText::_("COM_EASYSOCIAL_PHOTOS_TAGS_HINT"); ?>
					<button class="btn btn-es" href="javascript: void(0);" data-photo-tag-button="disable"><i class="ies-checkmark"></i> <span><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_TAGS_DONE"); ?></span></button>
				</div>
				<?php } ?>
			</div>
			<?php if ($options['showTags']) { ?>
				<?php echo $this->includeTemplate('site/photos/tags'); ?>
			<?php } ?>
		</div>

		<i class="loading-indicator fd-small"></i>
		<?php echo $this->render('module', 'es-photos-after-photo'); ?>
	</div>

	<div data-photo-footer class="es-photo-footer">

		<?php if ($options['showToolbar']) { ?>
		<div class="media">
			<div class="media-object pull-left">
				<div class="es-avatar es-avatar-fd-reset-list es-inset">
					<img src="<?php echo $photo->getCreator()->getAvatar(); ?>" />
				</div>
			</div>
			<div class="media-body">
				<div data-photo-owner class="es-photo-owner"><a href="<?php echo $photo->getCreator()->getPermalink(); ?>"><?php echo $photo->getCreator()->getName(); ?></a></div>
				<div data-photo-album class="es-photo-album"><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_FROM_ALBUM"); ?> <a href="<?php echo $album->getPermalink(); ?>"><?php echo $album->get( 'title' ); ?></a></div>
			</div>
		</div>
		<?php } ?>

		<a class="es-photo-close-button" href="javascript: void(0);" data-popup-close-button><i class="ies-cancel-2"></i></a>

		<?php echo $this->render( 'module' , 'es-photos-before-info' ); ?>

		<?php if ($options['showInfo']) { ?>
			<?php echo $this->includeTemplate('site/photos/info'); ?>
		<?php } ?>

		<?php if ($options['showForm'] && $album->editable()) { ?>
		<?php echo $this->includeTemplate('site/photos/form'); ?>
		<?php } ?>

		<div class="es-photo-interaction row">
			<?php if ($options['showResponse']) { ?>
				<?php echo $this->includeTemplate('site/photos/response'); ?>
			<?php } ?>
			</div>
		</div>

	</div>

	<div class="es-media-loader"></div>
</div>
