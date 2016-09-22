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
<div class="es-widget es-widget-borderless">
	<div class="es-widget-head">
        <div class="widget-title pull-left">
            <?php echo JText::_( 'COM_EASYSOCIAL_ACTIVITY_FILTER_BY_APPS' );?>
        </div>
	</div>

	<div class="es-widget-body">
		<?php if( $apps ) { ?>

			<ul class="fd-nav fd-nav-stacked activity-items" data-activity-apps>
				<?php foreach( $apps as $app ){ ?>
					<li class="<?php echo $app->element == $active ? ' active' : '';?>"
						data-sidebar-menu
						data-sidebar-item
						data-type="<?php echo $app->element; ?>"
						data-url="<?php echo FRoute::activities( array( 'type' => $app->element ) );?>"
						data-title="<?php echo JText::sprintf( 'COM_EASYSOCIAL_ACTIVITY_ITEM_TITLE', $app->title ); ?>"
						data-description=""
					>
						<a href="javascript:void(0);" class="es-app-filter<?php echo $app->favicon ? ' has-favicon' : '';?>">
							<?php if ($app->favicon) { ?>
								<span class="es-app-favicon" style="border: 1px solid <?php echo $app->favicon->color;?>;background:<?php echo $app->favicon->color;?>">
									<span>
										<i class="<?php echo $app->favicon->icon;?>"></i>
									</span>
								</span>
							<?php } else { ?>
								<img src="<?php echo $app->image;?>" alt="<?php echo $this->html('string.escape', $app->get('title'));?>" width="16" class="mr-5" />
							<?php } ?>

							<?php // We directly JText the app title here because we allow user to set the app title in the backend. This part shouldn't rely on language strings COM_EASYSOCIAL_STREAM_APP_FILTER_*. ?>
							<span class="app-title"><?php echo JText::_($app->title); ?></span>
							<div class="label label-notification pull-right mr-20"></div>
						</a>
					</li>
				<?php } ?>
			</ul>

		<?php } else { ?>
			<div class="fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_ACTIVITY_NO_APPS' ); ?></div>
		<?php } ?>
	</div>

</div>
