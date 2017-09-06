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
<div class="dropdown_" data-popdown>
    <button class="btn-popdown dropdown-toggle_" type="button" data-bs-toggle="dropdown" data-popdown-button>
        
        <i class="fa fa-caret-down t-lg-pull-right t-lg-mt--md t-lg-mr--md"></i>

        <div data-popdown-active>
            <?php if ($value == 1 || !$value) { ?>
                <b>
                    <i class="fa fa-globe"></i>&nbsp; <?php echo JText::_('PLG_FIELDS_PAGE_TYPE_PUBLIC'); ?>
                </b>
                <div class="btn-popdown__desp"><?php echo JText::_('PLG_FIELDS_PAGE_TYPE_PUBLIC_DESC');?></div>
            <?php } ?>

            <?php if ($value == 2) { ?>
                <b>
                    <i class="fa fa-user"></i>&nbsp; <?php echo JText::_('PLG_FIELDS_PAGE_TYPE_PRIVATE'); ?>
                </b>
                <div class="btn-popdown__desp" ><?php echo JText::_('PLG_FIELDS_PAGE_TYPE_PRIVATE_DESC');?></div>
            <?php } ?>

            <?php if ($value == 3) { ?>
                <b>
                    <i class="fa fa-lock"></i>&nbsp; <?php echo JText::_('PLG_FIELDS_PAGE_TYPE_INVITE_ONLY'); ?>
                </b>

                <div class="btn-popdown__desp"><?php echo JText::_('PLG_FIELDS_PAGE_TYPE_INVITE_ONLY_DESC');?></div>
            <?php } ?>
        </div>

        <input type="hidden" value="<?php echo !$value ? 1 : $value;?>" name="page_type" />
    </button>

    <ul class="dropdown-menu dropdown-menu--popdown">
        <li class="<?php echo !$value || $value == 1 ? 'active' : '';?>" data-popdown-option="1">
            <a href="javascript:void(0);" style="padding: 12px 20px;">
                <b>
                    <i class="fa fa-globe"></i>&nbsp; <?php echo JText::_('PLG_FIELDS_PAGE_TYPE_PUBLIC'); ?>
                </b>
                <div class="dropdown-menu--popdown__desp"><?php echo JText::_('PLG_FIELDS_PAGE_TYPE_PUBLIC_DESC');?></div>
            </a>
        </li>
        <li class="<?php echo $value == 2 ? 'active' : '';?>" style="border-top: 1px solid #d7d7d7;" data-popdown-option="2">
            <a href="javascript:void(0);" style="padding: 12px 20px;">
                <b>
                    <i class="fa fa-user"></i>&nbsp; <?php echo JText::_('PLG_FIELDS_PAGE_TYPE_PRIVATE'); ?>
                </b>
                <div class="dropdown-menu--popdown__desp"><?php echo JText::_('PLG_FIELDS_PAGE_TYPE_PRIVATE_DESC');?></div>
            </a>
        </li>
        <li class="<?php echo $value == 3 ? 'active' : '';?>" style="border-top: 1px solid #d7d7d7;" data-popdown-option="3">
            <a href="javascript:void(0);" style="padding: 12px 20px;">
                <b>
                    <i class="fa fa-lock"></i>&nbsp; <?php echo JText::_('PLG_FIELDS_PAGE_TYPE_INVITE_ONLY'); ?>
                </b>

                <div class="dropdown-menu--popdown__desp"><?php echo JText::_('PLG_FIELDS_PAGE_TYPE_INVITE_ONLY_DESC');?></div>
            </a>
        </li>
    </ul>
</div>

