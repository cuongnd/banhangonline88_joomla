<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-side-widget is-module">
    <div class="es-side-widget__hd">
        <div class="es-side-widget__title">
            <?php echo JText::_('APP_EVENT_PHOTOS_WIDGET_TITLE_ALBUMS'); ?>

            <span class="es-side-widget__label">(<?php echo $total; ?>)</span>
        </div>
    </div>

    <div class="es-side-widget__bd">
        <?php echo $this->html('widget.albums', $albums, 'COM_EASYSOCIAL_EVENTS_WIDGET_NO_ALBUMS_UPLOADED_YET'); ?>

        <?php if ($albums) { ?>
        <div>
            <?php echo $this->html('widget.viewAll', 'APP_EVENT_PHOTOS_WIDGET_VIEW_ALL_ALBUMS', ESR::albums(array('uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT))); ?>
        </div>
        <?php } ?>
    </div>
</div>
