<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
$showMinus = isset( $showminus ) ? $showminus : true;
?>
<div class="form-inline<?php echo $isTemplate ? ' hide' : '';?>" data-adv-search-item <?php echo $isTemplate ? 'data-adv-search-criteria-template' : '';?>>

	<div class="form-plus-minus">
		<?php if( $showMinus || $isTemplate ) { ?>
		<a href="javascript:void(0);" data-criteria-remove-button>
			<i class="ies-minus-2"></i>
		</a>
		<?php } ?>
	</div>

    <div class="form-criteria form-group form-group-sm">
        <span class="" data-itemCriteria>
            <select autocomplete="off" class="form-control input-sm" name="criterias[]" style="min-width:100px">
                <option value="">
                    <?php echo JText::_( 'COM_EASYSOCIAL_ADVANCED_SEARCH_SELECT_CRITERIA' ); ?>
                </option>

                <?php foreach( $criteria->fields as $field ){ ?>
                    <option value="<?php echo $field->unique_key;?>|<?php echo $field->element;?>"<?php echo !$isTemplate && $criteria->selected == $field->unique_key . '|' . $field->element ? ' selected="selected"' : '';;?>>
                        <?php echo JText::_( $field->title );?>
                    </option>
                <?php } ?>
            </select>
        </span>
    </div>
	<div class="form-condition form-group form-group-sm">
        <div class="hide" data-itemConditionDiv></div>

		<?php echo $criteria->datakeys; ?>

		<?php echo $criteria->operator; ?>

		<?php echo $criteria->condition; ?>


	</div>

    <span data-criteria-notice class="help-block fd-small text-note hide"></span>
    <span data-location-label class="help-block fd-small text-note hide"></span>
</div>
