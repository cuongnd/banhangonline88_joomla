<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
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
            <?php echo JText::_('APP_PAGE_FILES_WIDGET_TITLE'); ?>

            <span class="es-side-widget__label">(<?php echo $total ? $total : 0; ?>)</span>
        </div>
    </div>

    <div class="es-widget-body recent-files">
        <?php if ($files) { ?>
        <ol class="o-nav o-nav--stacked">
            <?php foreach ($files as $file) { ?>
            <li class="o-nav__item">
                <a href="<?php echo $file->getPreviewURI();?>" target="_blank">
                    <?php echo $file->name;?>
                </a>

                <div class="t-text--muted t-fs--sm">
                    <span><?php echo $file->getSize('kb'); ?> <?php echo JText::_('COM_EASYSOCIAL_UNIT_KILOBYTES'); ?></span>

                    <span><?php echo JText::sprintf('APP_PAGE_FILES_UPLOADED_BY', $this->html('html.user', $file->user_id, true)); ?></span>
                </div>
            </li>
            <?php } ?>
        </ul>
        <?php } else { ?>
            <?php echo $this->html('widget.emptyBlock', 'APP_PAGE_FILES_EMPTY_FILES'); ?>
        <?php } ?>
    </div>
</div>
