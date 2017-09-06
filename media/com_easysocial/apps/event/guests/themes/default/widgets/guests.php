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
            <?php echo JText::_('APP_EVENT_GUESTS_WIDGET_GUESTS_TITLE'); ?>

            <span class="es-side-widget__label">(<?php echo $event->getTotalGuests(); ?>)</span>
        </div>
    </div>

    <div class="es-side-widget__bd">
        <ul class="g-list-inline g-list-inline--dashed t-lg-mb--md">
            <li class="active"><a href="#es-going-guests" role="tab" data-bs-toggle="tab"><?php echo JText::_('APP_EVENT_GUESTS_WIDGET_GUESTS_TAB_GOING'); ?>
                <?php if ($totalGoing > 0){ ?>
                <span class="widget-label">(<?php echo $totalGoing;?>)</span>
                <?php } ?>
            </a></li>
            <?php if ($allowMaybe) { ?>
            <li><a href="#es-maybe-guests" role="tab" data-bs-toggle="tab"><?php echo JText::_('APP_EVENT_GUESTS_WIDGET_GUESTS_TAB_MAYBE'); ?>
                <?php if ($totalMaybe > 0){ ?>
                <span class="widget-label">(<?php echo $totalMaybe;?>)</span>
                <?php } ?>
            </a></li>
            <?php } ?>
            <?php if ($allowNotGoing) { ?>
            <li><a href="#es-notgoing-guests" role="tab" data-bs-toggle="tab"><?php echo JText::_('APP_EVENT_GUESTS_WIDGET_GUESTS_TAB_NOTGOING'); ?>
                <?php if ($totalNotGoing > 0){ ?>
                <span class="widget-label">(<?php echo $totalNotGoing;?>)</span>
                <?php } ?>
            </a></li>
            <?php } ?>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="es-going-guests">
                <?php echo $this->html('widget.users', $goingGuests, 'APP_EVENT_GUESTS_WIDGET_GUESTS_NO_GUESTS_YET'); ?>
            </div>

            <div class="tab-pane" id="es-maybe-guests">
                <?php echo $this->html('widget.users', $maybeGuests, 'APP_EVENT_GUESTS_WIDGET_GUESTS_NO_GUESTS_YET'); ?>
            </div>

            <div class="tab-pane" id="es-notgoing-guests">
                <?php echo $this->html('widget.users', $notGoingGuests, 'APP_EVENT_GUESTS_WIDGET_GUESTS_NO_GUESTS_YET'); ?>
            </div>
        </div>

        <?php if (!empty($goingGuests) || !empty($maybeGuests) || !empty($notGoingGuests)) { ?>
        <div>
            <?php echo $this->html('widget.viewAll', 'APP_EVENT_GUESTS_VIEW_ALL_GUESTS', $link); ?>
        </div>
        <?php } ?>

    </div>
</div>
