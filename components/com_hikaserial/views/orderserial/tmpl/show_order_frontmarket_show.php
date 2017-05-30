<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
if(empty($this->data))
	return;

?>
<div id="hikamarket_order_block_serials">
<h3 style="display:inline-block"><?php echo JText::_('HIKA_SERIALS')?></h3>
	<div class="hikamarket_ajax_loading_elem"></div>
	<div class="hikamarket_ajax_loading_spinner"></div>

	<table class="hikam_listing <?php echo (HIKASHOP_RESPONSIVE)?'table table-striped table-hover table-bordered':'hikam_table'; ?>" id="hikamarket_order_product_listing" style="width:100%">
		<thead>
			<tr>
				<th><?php echo JText::_('PACK_NAME');?></th>
				<th><?php echo JText::_('SERIAL_DATA');?></th>
				<th><?php echo JText::_('ASSIGN_DATE');?></th>
				<th><?php echo JText::_('ATTACHED_TO_PRODUCT');?></th>
			</tr>
		</thead>
		<tbody>
<?php
	foreach($this->data as $data) {
?>
		<tr>
			<td><?php echo $this->escape($data->pack_name);?></td>
			<td><?php echo $data->serial_data; ?></td>
			<td><?php echo hikaserial::getDate($data->serial_assign_date);?>
			<td><?php echo $data->order_product_name; ?></td>
		</tr>
<?php
	}
?>
		</tbody>
	</table>
</div>
