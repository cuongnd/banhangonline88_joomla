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
<?php echo $lib->heading(); ?>

<div class="es-container es-media-browser layout-album"
	data-layout="album"
	data-album-browser="<?php echo $uuid; ?>">

	<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
		<i class="ies-grid-view ies-small mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
	</a>

	<div data-album-browser-sidebar class="es-sidebar" data-sidebar>

		<?php echo $this->render( 'module' , 'es-albums-sidebar-top' ); ?>

		<div class="es-widget es-widget-borderless">
			<div class="es-widget-head">
				<div
					data-album-all-button
					class="btn btn-es-primary btn-sm btn-media">
					<a href="<?php echo $lib->getViewAlbumsLink();?>"><i class="ies-pictures"></i> <?php echo JText::_("COM_EASYSOCIAL_ALBUMS_VIEW_ALL_ALBUMS"); ?></a>
				</div>
			</div>
			<div class="es-widget-body">

				<?php if( $coreAlbums ){ ?>
				<ul class="fd-nav fd-nav-stacked es-album-list-item-group-core" data-album-list-item-group="core">
					<?php foreach( $coreAlbums as $album ){ ?>
						<li data-album-list-item data-album-id="<?php echo $album->id; ?>" class="<?php if ($album->id==$id) { ?>active<?php } ?>">
							<a href="<?php echo $album->getPermalink();?>" title="<?php echo $album->get('title'); ?>"><i data-album-list-item-cover style="background-image: url(<?php echo $album->getCover(); ?>);"></i> <span data-album-list-item-title><?php echo $album->get( 'title' ); ?></span> <b data-album-list-item-count><?php echo $album->getTotalPhotos(); ?></b></a>
						</li>
					<?php } ?>
				</ul>
				<?php } ?>

				<?php if ($lib->canCreateAlbums()) { ?>
				<div class="es-media-sidebar-actions">
					<div
						class="es-media-new-button btn btn-es btn-sm"
						data-album-create-button>
							<a href="<?php echo $lib->getCreateLink();?>"><i class="ies-plus"></i> <?php echo JText::_("COM_EASYSOCIAL_ALBUMS_CREATE_ALBUM"); ?></a>
					</div>
				</div>
				<?php } ?>

				<ul class="fd-nav fd-nav-stacked es-album-list-item-group-regular" data-album-list-item-group="regular">

					<?php if ( $layout=="form" && empty($id) ) { ?>
					<li
						data-album-list-item
						class="active new">
						<a href="javascript: void(0);"><i data-album-list-item-cover></i> <span data-album-list-item-title><?php echo JText::_('COM_EASYSOCIAL_ALBUMS_NEW_ALBUM'); ?></span> <b data-album-list-item-count>0</b></a>
					</li>
					<?php } ?>
					<?php if( $albums ){ ?>
						<?php foreach( $albums as $album ){ ?>
						<li data-album-list-item
						    data-album-id="<?php echo $album->id; ?>"
						    class="<?php if ($album->id==$id) { ?>active<?php } ?>">
							<a href="<?php echo $album->getPermalink(); ?>" title="<?php echo $this->html( 'string.escape' , $album->get('title') ); ?>"><i data-album-list-item-cover style="background-image: url(<?php echo $album->getCover(); ?>);"></i> <span data-album-list-item-title><?php echo $album->get( 'title' ); ?></span> <b data-album-list-item-count><?php echo $album->getTotalPhotos(); ?></b></a>
						</li>
						<?php } ?>
					<?php } ?>
				</ul>
			</div>
		</div>

		<?php echo $this->render( 'module' , 'es-albums-sidebar-bottom' ); ?>

	</div>

	<div data-album-browser-content class="es-content">
		<?php echo $this->render( 'module' , 'es-albums-before-contents' ); ?>
		<?php echo $content; ?>
		<?php echo $this->render( 'module' , 'es-albums-after-contents' ); ?>
	</div>

	<i class="loading-indicator fd-small"></i>
</div>
