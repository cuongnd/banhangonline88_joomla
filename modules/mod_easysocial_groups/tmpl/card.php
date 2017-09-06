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
<div id="es" class="mod-es mod-es-groups <?php echo $lib->getSuffix();?>">
    <?php foreach ($groups as $group) { ?>
        <div class="mod-card mod-card--no-avatar-holder">
            <div class="mod-card__cover-wrap">
                <div style="background-image : url(<?php echo $group->getCover()?>);
                background-position : <?php echo $group->getCoverPosition();?>" class="mod-card__cover">
                </div>
            </div>    
            <div class="mod-card__context">
                <?php if ($params->get('display_avatar', true)) { ?>
                    <div class="mod-card__avatar-holder">
                        <div class="mod-card__avatar">
                            <?php echo $lib->html('avatar.cluster', $group, 'md'); ?>
                        </div>
                    </div>
                <?php } ?>
                <a class="es-card__title" href="<?php echo $group->getPermalink();?>"><?php echo $group->getName();?></a>
                
                <?php if ($params->get('display_category', true)) { ?>
                    <div class="es-card__meta">
                        <a href="<?php echo $group->getCategory()->getPermalink(); ?>" alt="<?php echo $lib->html('string.escape', $group->getCategory()->get('title'));?>">
                            <?php echo $lib->html('string.escape', $group->getCategory()->get('title'));?>
                        </a>
                    </div>
                <?php } ?>

                <?php if ($params->get('display_member_counter', true)) { ?>
                    <div class="es-card__meta">
                       <?php echo JText::sprintf(ES::string()->computeNoun('MOD_EASYSOCIAL_GROUPS_MEMBERS_COUNT', $group->getTotalMembers()), $group->getTotalMembers()); ?>
                    </div>
                <?php } ?>
                
                <?php if ($params->get('display_actions', true) && !$group->isMember()) { ?>
                    <div class="mod-es-action">
                        <?php echo $lib->html('group.action', $group); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
    <div class="mod-es-action">
        <a href="<?php echo ESR::groups();?>" class="btn btn-es-default-o btn-sm"><?php echo JText::_('MOD_EASYSOCIAL_GROUPS_ALL_GROUP'); ?></a>
    </div>
</div>