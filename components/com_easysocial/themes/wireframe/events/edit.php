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
<div class="es-container es-events-edit" data-events-edit>

    <a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
        <i class="ies-grid-view ies-small mr-5"></i> <?php echo JText::_('COM_EASYSOCIAL_SIDEBAR_TOGGLE');?>
    </a>

    <div class="es-sidebar" data-sidebar>
        <?php echo $this->render('module', 'es-events-edit-sidebar-top'); ?>

        <div class="es-widget es-widget-borderless">
            <div class="es-widget-head"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_SIDEBAR_ABOUT');?></div>

            <div class="es-widget-body">
                <ul class="fd-nav fd-nav-stacked feed-items">
                    <?php $i = 0; ?>
                    <?php foreach($steps as $step){ ?>
                        <li data-for="<?php echo $step->id;?>" class="step-item<?php echo $i == 0 ? ' active' :'';?>" data-step-nav>
                            <a href="javascript:void(0);"><?php echo $step->get('title'); ?></a>
                        </li>
                        <?php $i++; ?>
                    <?php } ?>
                </ul>
            </div>
        </div>

        <?php echo $this->render('module', 'es-events-edit-sidebar-bottom'); ?>
    </div>

    <div class="es-content">

        <?php echo $this->render('module', 'es-events-edit-before-contents'); ?>

        <div data-events-edit-fields>
            <form method="post" action="<?php echo JRoute::_('index.php'); ?>" class="form-horizontal" data-form>
                <div class="edit-form">
                    <div class="tab-content profile-content">
                        <?php $i = 0; ?>
                        <?php foreach($steps as $step){ ?>
                        <div class="step-content step-<?php echo $step->id;?> <?php if ($i == 0) { ?>active<?php } ?>"
                            data-step-content data-id="<?php echo $step->id; ?>"
                            <?php if($i > 0) { ?>style="display: none;"<?php } ?>
                        >
                            <?php if($step->fields){ ?>
                                <?php foreach($step->fields as $field){ ?>
                                    <?php if(!empty($field->output)) { ?>
                                    <div data-edit-field data-element="<?php echo $field->element; ?>" data-id="<?php echo $field->id; ?>" data-required="<?php echo $field->required; ?>" data-fieldname="<?php echo SOCIAL_FIELDS_PREFIX . $field->id; ?>">
                                        <?php echo $field->output; ?>
                                    </div>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <?php $i++; ?>
                        <?php } ?>
                    </div>
                </div>
                <div class="form-actions">
                    <div class="pull-left">
                        <a href="<?php echo $event->getPermalink();?>" class="btn btn-sm btn-es-danger"><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON'); ?></a>
                    </div>

                    <div class="pull-right">
                        <button type="button" class="btn btn-sm btn-es-primary" data-edit-save>
                            <?php echo JText::_('COM_EASYSOCIAL_UPDATE_EVENT_BUTTON');?> &rarr;
                        </button>
                    </div>
                </div>

                <input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid');?>" />
                <input type="hidden" name="option" value="com_easysocial" />
                <input type="hidden" name="controller" value="events" />
                <input type="hidden" name="task" value="update" />
                <input type="hidden" name="id" value="<?php echo $event->id;?>" />
                <?php echo JHTML::_('form.token'); ?>
            </form>
        </div>

        <?php echo $this->render('module', 'es-events-edit-after-contents'); ?>
    </div>
</div>
