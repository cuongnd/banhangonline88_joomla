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
<li class="es-app-item" data-apps-item data-id="<?php echo $app->id; ?>">
	<div class="es-app">
		<div class="media">
			<div class="media-object pull-left">
				<img class="app-image" alt="" src="<?php echo $app->getIcon( SOCIAL_APPS_ICON_LARGE );?>">
			</div>

			<div class="media-body">
				<div class="app-name">
					<?php echo $app->getAppTitle();?>
				</div>

				<div class="app-version fd-small">
					v<?php echo $app->getMeta()->version; ?>
				</div>

				<div class="app-description">
					<?php echo JText::_( $app->getUserDesc() ); ?>
				</div>

				<?php if( !$app->default ){ ?>
				<div class="app-actions">
					<a class="btn btn-medium btn-es btn-sm" <?php if( !$app->hasUserSettings() || !$app->isInstalled() ) { ?>style="display: none;"<?php } ?> data-apps-item-settings>
						<?php echo JText::_( 'COM_EASYSOCIAL_SETTINGS_BUTTON' ); ?>
					</a>

					<a class="btn btn-medium btn-es-inverse btn-sm" <?php if( !$app->isInstalled() ) { ?>style="display: none;"<?php } ?> href="javascript:void(0);" data-apps-item-installed>
						<?php echo JText::_( 'COM_EASYSOCIAL_UNINSTALL_BUTTON' ); ?>
					</a>

					<a class="btn btn-medium btn-es-primary btn-sm" <?php if( $app->isInstalled() ) { ?>style="display: none;"<?php } ?> href="javascript:void(0);" data-apps-item-install>
						<?php echo JText::_( 'COM_EASYSOCIAL_INSTALL_BUTTON' ); ?>
					</a>
				</div>
				<?php } ?>

			</div>
		</div>
	</div>
</li>
