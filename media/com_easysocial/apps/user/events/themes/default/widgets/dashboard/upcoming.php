<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-widget">
    <div class="es-widget-head">
        <div class="pull-left widget-title">
            <?php echo JText::_('APP_USER_EVENTS_WIDGET_UPCOMING_EVENTS'); ?>
        </div>
    </div>
    <div class="es-widget-body">
        <?php if ($events) { ?>
        <ul class="fd-nav fd-nav-stacked">
            <?php foreach ($events as $event) {?>
            <li>
                <div class="media">
                    <div class="media-object pull-left">
                        <span class="es-avatar es-avatar-sm">
                            <img src="<?php echo $event->getAvatar(); ?>">
                        </span>
                    </div>
                    <div class="media-body">
                        <a href="<?php echo $event->getPermalink(); ?>"><?php echo $event->getName(); ?></a>
                        <div class="mb-10">
                            <span class="fd-small es-muted"><?php echo $event->getStartEndDisplay(array('end' => false));?></span>
                        </div>
                    </div>
                </div>

            </li>
            <li class="divider"></li>
            <?php } ?>
        </ul>
        <?php } else { ?>
        <div class="fd-small"><?php echo JText::_('APP_USER_EVENTS_WIDGET_NO_EVENTS'); ?></div>
        <?php } ?>
    </div>
</div>
