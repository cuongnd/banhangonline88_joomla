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
<form action="index.php" method="post" name="adminForm" class="esForm" id="adminForm" data-table-grid>
    <div class="filter-bar form-inline">
        <div class="form-group">
            <?php echo $this->html('filter.search', $search); ?>
        </div>

        <?php if ($this->tmpl != 'component') { ?>
        <div class="form-group">
            <strong><?php echo JText::_('COM_EASYSOCIAL_FILTER_BY'); ?> :</strong>
            <div>
                <?php echo $this->html('filter.published', 'state', $state); ?>
                <select class="form-control input-sm" name="type" id="filterType" data-table-grid-filter>
                    <option value="all"<?php echo $type == 'all' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYSOCIAL_FILTER_EVENT_TYPE'); ?></option>
                    <option value="1"<?php echo $type == 1 ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYSOCIAL_EVENTS_OPEN_EVENT'); ?></option>
                    <option value="2"<?php echo $type == 2 ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYSOCIAL_EVENTS_CLOSED_EVENT'); ?></option>
                    <option value="3"<?php echo $type == 3 ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYSOCIAL_EVENTS_INVITE_EVENT'); ?></option>
                </select>
            </div>
        </div>
        <?php } ?>

        <div class="form-group pull-right">
            <div><?php echo $this->html('filter.limit', $limit); ?></div>
        </div>
    </div>

    <table class="table table-striped table-es table-hover">
        <thead>
            <tr>
                <th width="1%" class="center">
                    <input type="checkbox" name="toggle" data-table-grid-checkall />
                </th>

                <th>
                    <?php echo $this->html('grid.sort', 'a.title', JText::_('COM_EASYSOCIAL_TABLE_COLUMN_TITLE'), $ordering, $direction); ?>
                </th>

                <?php if ($this->tmpl != 'component') { ?>
                <th class="center" width="15%">
                    <?php echo $this->html('grid.sort', 'b.title', JText::_('COM_EASYSOCIAL_TABLE_COLUMN_CATEGORY'), $ordering, $direction); ?>
                </th>

                <th class="center" width="5%">
                    <?php echo $this->html( 'grid.sort' , 'a.featured' , JText::_('COM_EASYSOCIAL_TABLE_COLUMN_FEATURED') , $ordering , $direction ); ?>
                </th>

                <th width="5%" class="center">
                    <?php echo $this->html('grid.sort', 'a.state', JText::_('COM_EASYSOCIAL_TABLE_COLUMN_STATUS'), $ordering, $direction); ?>
                </th>
                

                <th class="center" width="5%">
                    <?php echo $this->html('grid.sort', 'a.created_by', JText::_('COM_EASYSOCIAL_TABLE_COLUMN_CREATED_BY'), $ordering, $direction); ?>
                </th>

                <th width="5%" class="center">
                    <?php echo JText::_('COM_EASYSOCIAL_TABLE_COLUMN_USERS'); ?>
                </th>
                <?php } ?>

                <th class="center" width="10%">
                    <?php echo $this->html('grid.sort', 'a.created', JText::_('COM_EASYSOCIAL_TABLE_COLUMN_CREATED'), $ordering, $direction); ?>
                </th>

                <th width="5%" class="center">
                    <?php echo $this->html('grid.sort', 'a.id', JText::_('COM_EASYSOCIAL_TABLE_COLUMN_ID'), $ordering, $direction); ?>
                </th>
        </thead>
        <tbody>
        <?php if (!empty($events)) { ?>
            <?php $i = 0;?>
            <?php foreach ($events as $event) { ?>
                <tr class="row<?php echo $i; ?>" data-grid-row data-id="<?php echo $event->id; ?>">
                    <td align="center">
                        <?php echo $this->html('grid.id', $i, $event->id); ?>
                    </td>

                    <td>
                        <div class="media">
                            <div class="media-object pull-left">
                                <img src="<?php echo $event->getAvatar();?>" class="es-avatar" />
                            </div>

                            <div class="media-body">
                                <a href="<?php echo FRoute::url(array('view' => 'events', 'layout' => 'form', 'id' => $event->id));?>" style="font-size: 16px;font-weight:700;"
                                    data-event-insert
                                    data-id="<?php echo $event->id;?>"
                                    data-avatar="<?php echo $event->getAvatar();?>"
                                    data-title="<?php echo $this->html('string.escape', $event->getName());?>"
                                    data-alias="<?php echo $event->getAlias();?>"
                                    style="font-size: 16px;font-weight:700;"
                                >
                                    <?php echo JText::_($event->title); ?>
                                </a>

                                <p class="mt-5 mb-10 fd-small">
                                    <?php if ($event->description){ ?>
                                        <?php echo $this->html('string.truncater', $event->description, 180);?>
                                    <?php } else { ?>
                                        <?php echo JText::_('COM_EASYSOCIAL_EVENTS_NO_DESCRIPTION'); ?>
                                    <?php } ?>
                                </p>

                                <div class="es-event-labels">
                                    <?php if ($event->isOpen()){ ?>
                                    <span class="label label-success" data-original-title="<?php echo JText::_('COM_EASYSOCIAL_EVENTS_OPEN_EVENT_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="top">
                                        <i class="ies-earth"></i> <?php echo JText::_('COM_EASYSOCIAL_EVENTS_OPEN_EVENT'); ?>
                                    </span>
                                    <?php } ?>

                                    <?php if ($event->isClosed()){ ?>
                                    <span class="label label-danger" data-original-title="<?php echo JText::_('COM_EASYSOCIAL_EVENTS_CLOSED_EVENT_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="top">
                                        <i class="ies-locked"></i> <?php echo JText::_('COM_EASYSOCIAL_EVENTS_CLOSED_EVENT'); ?>
                                    </span>
                                    <?php } ?>

                                    <?php if ($event->isInviteOnly()){ ?>
                                    <span class="label label-warning" data-original-title="<?php echo JText::_('COM_EASYSOCIAL_EVENTS_INVITE_EVENT_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="top">
                                        <i class="ies-locked"></i> <?php echo JText::_('COM_EASYSOCIAL_EVENTS_INVITE_EVENT'); ?>
                                    </span>
                                    <?php } ?>

                                    <?php if ($event->isOver()) { ?>
                                    <span class="label label-warning" data-original-title="<?php echo JText::_('COM_EASYSOCIAL_EVENTS_OVER_EVENT_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="top">
                                        <i class="ies-flag"></i> <?php echo JText::_('COM_EASYSOCIAL_EVENTS_OVER_EVENT'); ?>
                                    </span>
                                    <?php } ?>

                                    <?php if ($event->isOngoing()) { ?>
                                    <span class="label label-info" data-original-title="<?php echo JText::_('COM_EASYSOCIAL_EVENTS_ONGOING_EVENT_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="top">
                                        <i class="ies-flag"></i> <?php echo JText::_('COM_EASYSOCIAL_EVENTS_ONGOING_EVENT'); ?>
                                    </span>
                                    <?php } ?>

                                    <?php if ($event->isUpcoming()) { ?>
                                    <span class="label label-success" data-original-title="<?php echo JText::_('COM_EASYSOCIAL_EVENTS_UPCOMING_EVENT_TOOLTIP', true);?>" data-es-provide="tooltip" data-placement="top">
                                        <i class="ies-alarm"></i> <?php echo JText::_('COM_EASYSOCIAL_EVENTS_UPCOMING_EVENT'); ?>
                                    </span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </td>

                    <?php if ($this->tmpl != 'component') { ?>
                    <td class="center">
                        <a href="<?php echo FRoute::url(array('view' => 'events', 'layout' => 'category', 'id' => $event->category_id)); ?>" target="_blank"><?php echo JText::_($event->getCategory()->title); ?></a>
                    </td>

                    <td class="center">
                        <?php echo $this->html('grid.featured', $event, 'events', 'featured'); ?>
                    </td>

                    <td class="center">
                        <?php echo $this->html('grid.published', $event, 'events', 'state', array(2 => 'approve'), array(2 => 'COM_EASYSOCIAL_GRID_TOOLTIP_APPROVE_ITEM'), array(2 => 'pending')); ?>
                    </td>

                    <td class="center">
                        <a href="<?php echo FRoute::url(array('view' => 'users', 'layout' => 'form', 'id' => $event->getCreator()->id)); ?>" target="_blank"><?php echo $event->getCreator()->getName(); ?></a>
                    </td>

                    <td class="center">
                        <?php echo $event->getTotalGuests(); ?>
                    </td>
                    <?php } ?>

                    <td class="center">
                        <?php echo $event->created; ?>
                    </td>

                    <td class="center">
                        <?php echo $event->id;?>
                    </td>
                </tr>
            <?php $i++; ?>
            <?php } ?>
        <?php } else { ?>
            <tr class="is-empty">
                <td colspan="9" class="center empty">
                    <?php echo JText::_('COM_EASYSOCIAL_EVENTS_NO_EVENT_CREATED_YET');?>
                </td>
            </tr>
        <?php } ?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="9" class="center">
                    <div class="footer-pagination"><?php echo $pagination->getListFooter(); ?></div>
                </td>
            </tr>
        </tfoot>
    </table>

    <?php echo JHTML::_('form.token'); ?>
    <input type="hidden" name="ordering" value="<?php echo $ordering;?>" data-table-grid-ordering />
    <input type="hidden" name="direction" value="<?php echo $direction;?>" data-table-grid-direction />
    <input type="hidden" name="boxchecked" value="0" data-table-grid-box-checked />
    <input type="hidden" name="task" value="" data-table-grid-task />
    <input type="hidden" name="option" value="com_easysocial" />
    <input type="hidden" name="view" value="events" />
    <input type="hidden" name="controller" value="events" />
</form>
