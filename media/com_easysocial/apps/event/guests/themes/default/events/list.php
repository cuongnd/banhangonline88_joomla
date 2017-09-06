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
<div>
    <?php foreach ($guests as $guest) { ?>
    <div class="es-users-item" data-item data-id="<?php echo $guest->id;?>" data-return="<?php echo $returnUrl;?>">
        <div class="o-grid">
            <div class="o-grid__cell">
                <div class="o-flag">
                    <div class="o-flag__image">
                        <?php echo $this->html('avatar.user', $guest->user, 'md', false, true); ?>
                    </div>

                    <div class="o-flag__body">
                        <a href="<?php echo $guest->user->getPermalink();?>" class="es-user-name"><?php echo $guest->user->getName();?></a>
                        <div class="es-user-meta">
                            <ol class="g-list-inline g-list-inline--delimited es-user-item-meta">            
                                <li>
                                    <?php if ($event->isOwner($guest->uid)) { ?>
                                    <span class="o-label o-label--primary-o label-owner"><?php echo JText::_('APP_EVENT_GUESTS_OWNER'); ?></span>
                                    <?php } ?>

                                    <?php if ($event->isAdmin($guest->uid) && !$event->isOwner($guest->uid)) { ?>
                                    <span class="o-label o-label--danger-o label-admin"><?php echo JText::_('APP_EVENT_GUESTS_ADMIN'); ?></span>
                                    <?php } ?>

                                    <?php if ($guest->isGoing()) { ?>
                                    <span class="o-label o-label--success-o label-going"><?php echo JText::_('APP_EVENT_GUESTS_GOING'); ?></span>
                                    <?php } ?>

                                    <?php if ($guest->isNotGoing()) { ?>
                                    <span class="o-label o-label--warning-o label-not-going"><?php echo JText::_('APP_EVENT_GUESTS_NOT_GOING'); ?></span>
                                    <?php } ?>

                                    <?php if ($guest->isMaybe()) { ?>
                                    <span class="o-label o-label--info-o label-maybe"><?php echo JText::_('APP_EVENT_GUESTS_MAYBE'); ?></span>
                                    <?php } ?>

                                    <?php if ($guest->isPending()) { ?>
                                    <span class="o-label o-label--warning-o label-pending"><?php echo JText::_('APP_EVENT_GUESTS_PENDING'); ?></span>
                                    <?php } ?>

                                    <?php if ($guest->isInvited()) { ?>
                                    <span class="o-label o-label--warning-o label-pending-invitation"><?php echo JText::_('APP_EVENT_GUESTS_INVITED'); ?></span>
                                    <?php } ?>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($this->access->allowed('reports.submit') || $filter == 'list' || $filter == 'all') { ?>
            <div class="o-grid__cell o-grid__cell--auto-size">
                <div role="toolbar" class="btn-toolbar">

                    <?php echo $this->html('user.conversation', $guest->user); ?>
                    
                    <?php if (($event->isAdmin() || $this->my->isSiteAdmin()) && !$event->isOwner($guest->user->id)) { ?>
                    <div role="group" class="btn-group">
                        <button data-bs-toggle="dropdown" class="btn btn-es-default-o btn-sm dropdown-toggle_" type="button">
                             <i class="fa fa-ellipsis-h"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-right">
                            <?php if (($myGuest->isOwner() || $this->my->isSiteAdmin()) && $guest->isStrictlyAdmin()) { ?>
                            <li>
                                <a href="javascript:void(0);" data-guest-demote><?php echo JText::_('APP_EVENT_GUESTS_REVOKE_ADMIN'); ?></a>
                            </li>
                            <li class="divider"></li>
                            <?php } ?>

                            <?php if (($myGuest->isOwner() || $this->my->isSiteAdmin()) && $guest->isStrictlyAdmin()) { ?>
                            <li>
                                <a href="javascript:void(0);" data-guest-promote><?php echo JText::_('APP_EVENT_GUESTS_MAKE_ADMIN'); ?></a>
                            </li>
                            <li class="divider"></li>
                            <?php } ?>

                            <?php if ($guest->isPending()) { ?>
                            <li>
                                <a href="javascript:void(0);" data-guest-approve><?php echo JText::_('APP_EVENT_GUESTS_APPROVE_REQUEST'); ?></a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" data-guest-reject><?php echo JText::_('APP_EVENT_GUESTS_REJECT_REQUEST'); ?></a>
                            </li>
                            <li class="divider"></li>
                            <?php } ?>

                            <?php if ($myGuest->isOwner() || $this->my->isSiteAdmin() || ($myGuest->isAdmin() && !$guest->isAdmin())) { ?>
                            <li>
                                <a href="javascript:void(0);" data-guest-remove><?php echo JText::_('APP_EVENT_GUESTS_REMOVE_FROM_EVENT'); ?></a>
                            </li>
                            <?php } ?>
                        </ul>
                     </div>
                     <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>

    </div>
    <?php } ?>

    <?php echo $this->html('html.emptyBlock', 'APP_EVENT_GUESTS_EMPTY', 'fa-users', true); ?>
</div>

<?php if ($pagination) { ?>
<div class="es-pagination-footer">
    <?php echo $pagination->getListFooter('site');?>
</div>
<?php } ?>