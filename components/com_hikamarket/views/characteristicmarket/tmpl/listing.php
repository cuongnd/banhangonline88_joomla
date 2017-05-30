<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div>
<form action="<?php echo hikamarket::completeLink('characteristic&task=listing'); ?>" method="post" id="adminForm" name="adminForm">
<?php if(!HIKASHOP_RESPONSIVE) { ?>
	<table class="hikam_filter">
		<tr>
			<td width="100%">
				<?php echo JText::_( 'FILTER' ); ?>:
				<input type="text" name="search" id="hikamarket_characteristic_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class=""/>
				<button class="btn" onclick="this.form.submit();"><?php echo JText::_( 'GO' ); ?></button>
				<button class="btn" onclick="document.getElementById('hikamarket_characteristic_listing_search').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
			</td>
			<td nowrap="nowrap">
<?php } else {?>
	<div class="row-fluid">
		<div class="span8">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-filter"></i></span>
				<input type="text" name="search" id="hikamarket_characteristic_listing_search" value="<?php echo $this->escape($this->pageInfo->search);?>" class=""/>
				<button class="btn" onclick="this.form.submit();"><i class="icon-search"></i></button>
				<button class="btn" onclick="document.getElementById('hikamarket_characteristic_listing_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="span4">
			<div class="expand-filters" style="width:auto;float:right">
<?php }

	if(!empty($this->vendorType))
		echo $this->vendorType->display('filter_vendors', @$this->pageInfo->filter->vendors);

if(!HIKASHOP_RESPONSIVE) { ?>
			</td>
		</tr>
	</table>
<?php } else {?>
			</div>
			<div style="clear:both"></div>
		</div>
	</div>
<?php } ?>
	<table class="hikam_listing <?php echo (HIKASHOP_RESPONSIVE)?'table table-striped table-hover':'hikam_table'; ?>" style="width:100%">
		<thead>
			<tr>
				<th class="hikamarket_characteristic_name_title title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_NAME'), 'characteristic.characteristic_value', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
				<th class="hikamarket_characteristic_alias_title title"><?php
					echo JHTML::_('grid.sort', JText::_('HIKA_ALIAS'), 'characteristic.characteristic_alias', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value);
				?></th>
<?php if($this->show_vendor) { ?>
				<th class="hikamarket_characteristic_brnfot_title title"><?php
					echo JText::_('HIKA_VENDOR');
				?></th>
<?php } ?>
				<th class="hikamarket_characteristic_valuecounter_title title titlenum"><?php
					echo JText::_('HIKAM_NB_OF_VALUES');
				?></th>
				<th class="hikamarket_characteristic_usedcounter_title title titlenum"><?php
					echo JText::_('HIKAM_NB_OF_USED');
				?></th>
<?php if($this->characteristic_actions) { ?>
				<th class="hikamarket_characteristic_actions_title title titlenum"><?php
					echo JText::_('HIKA_ACTIONS');
				?></th>
<?php } ?>
				<th class="hikamarket_characteristic_id_title title titlenum">
					<?php echo JHTML::_('grid.sort', JText::_( 'ID' ), 'characteristic.characteristic_id', $this->pageInfo->filter->order->dir, $this->pageInfo->filter->order->value ); ?>
				</th>
			</tr>
		</thead>
<?php if(!isset($this->embbed)) {
	$columns = 5;
	if($this->characteristic_actions)
		$columns++;
	if($this->show_vendor)
		$columns++;
?>
		<tfoot>
			<tr>
				<td colspan="<?php echo $columns; ?>">
					<?php echo $this->pagination->getListFooter(); ?>
					<?php echo $this->pagination->getResultsCounter(); ?>
				</td>
			</tr>
		</tfoot>
<?php } ?>
		<tbody>
<?php
$k = 0;
$i = 0;
foreach($this->characteristics as $characteristic) {
	$rowId = 'market_characteristic_'.$characteristic->characteristic_id;
	if($this->manage)
		$url = hikamarket::completeLink('characteristic&task=show&cid='.$characteristic->characteristic_id);
?>
			<tr class="row<?php echo $k; ?>" id="<?php echo $rowId; ?>">
				<td class="hikamarket_characteristic_name_value"><?php
					if(!empty($url)) echo '<a href="'.$url.'"><img src="'.HIKAMARKET_IMAGES.'icon-16/edit.png" style="vertical-align:top;margin-right:4px;"/>';
					echo $this->escape($characteristic->characteristic_value);
					if(!empty($url)) echo '</a>';
				?></td>
				<td class="hikamarket_characteristic_alias_value"><?php
					if(!empty($url)) echo '<a href="'.$url.'">';
					echo $this->escape($characteristic->characteristic_alias);
					if(!empty($url)) echo '</a>';
				?></td>
<?php if($this->show_vendor) { ?>
				<td class="hikamarket_characteristic_vendor_value"><?php
					if(empty($characteristic->characteristic_vendor_id))
						echo '<em>'.JText::_('HIKA_NONE').'</em>';
					else
						echo $characteristic->vendor;
				?></td>
<?php } ?>
				<td class="hikamarket_characteristic_valuecounter_value"><?php
					echo (int)$characteristic->counter;
				?></td>
				<td class="hikamarket_characteristic_usedcounter_value"><?php
					echo (int)$characteristic->used;
				?></td>
<?php if($this->characteristic_actions) { ?>
				<td class="hikamarket_characteristic_actions_value"><?php
					if($this->characteristic_action_delete && ($this->vendor->vendor_id <= 1 || $this->vendor->vendor_id == $characteristic->characteristic_vendor_id) && empty($characteristic->used))
						echo $this->toggleClass->delete($rowId, (int)$characteristic->characteristic_id, 'characteristic', true);
					else
						echo '-';
				?></td>
<?php } ?>
				<td class="hikamarket_characteristic_id_value"><?php echo $characteristic->characteristic_id; ?></td>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="listing" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
