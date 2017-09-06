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
<div data-page-events class="app-pages">
    <?php if ($page->canCreateEvent()) { ?>
    <div class="t-text--right">
        <a href="<?php echo FRoute::events(array('layout' => 'create', 'page_id' => $page->id));?>" class="btn btn-es-primary-o btn-sm">
            <?php echo JText::_('APP_PAGE_EVENTS_NEW_EVENT'); ?>
        </a>
    </div>
    <?php } ?>

    <div class="app-contents-wrap" data-page-events-list>
        <?php echo $this->includeTemplate('site/events/default/items'); ?>
    </div>
</div>
