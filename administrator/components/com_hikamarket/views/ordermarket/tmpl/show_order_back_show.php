<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
if(!empty($this->data)) {
	if(empty($this->ajax)) {
?>
<fieldset class="adminform" id="hikashop_order_field_market">
<?php } ?>
	<legend><?php echo JText::_('HIKAMARKET_ORDERS')?></legend>
	<table style="width:100%;cell-spacing:1px;" class="adminlist table table-striped">
		<thead>
			<tr>
				<th><?php echo JText::_('ORDER_NUMBER');?></th>
				<th><?php echo JText::_('INVOICE_NUMBER');?></th>
				<th><?php echo JText::_('HIKA_VENDOR');?></th>
				<th><?php echo JText::_('ORDER_STATUS');?></th>
				<th><?php echo JText::_('HIKASHOP_TOTAL');?></th>
				<th><?php echo JText::_('VENDOR_TOTAL');?></th>
				<th><?php echo JText::_('VENDOR_PAID');?></th>
			</tr>
		</thead>
		<tbody>
<?php
	foreach($this->data as $data) {
?>
			<tr>
<?php
		if($data->order_type == 'subsale') {
?>
				<td><a href="<?php echo hikamarket::completeLink('shop.order&task=edit&cid[]='.$data->order_id);?>"><?php echo $data->order_number;?></a></td>
				<td><?php echo $data->order_invoice_number; ?></td>
<?php
		} else {
?>
				<td colspan="2"><em><?php echo JText::_('HIKAM_ORDER_ADJUSTMENT'); ?></em></td>
<?php
		}
?>
				<td><a href="<?php echo hikamarket::completeLink('vendor&task=edit&cid[]='.$data->order_vendor_id);?>"><?php echo $data->vendor_name; ?></a></td>
				<td><?php echo $data->order_status; ?></td>
				<td><?php echo $this->currencyHelper->format($data->order_full_price, $data->order_currency_id);?></td>
				<td><?php
					echo $this->currencyHelper->format($data->order_vendor_price, $data->order_currency_id);
					if(isset($data->order_vendor_price_with_refunds) && $data->order_vendor_price_with_refunds !== null) {
						echo ' (' . $this->currencyHelper->format($data->order_vendor_price_with_refunds, $data->order_currency_id) . ')';
					}
				?></td>
				<td style="text-align:center"><?php if($data->order_vendor_paid > 0) echo '<img src="'.HIKAMARKET_IMAGES.'icon-16/save2.png" alt="X"/>'; ?></td>
			</tr>
<?php
	}
?>
		</tbody>
	</table>
<?php
	if(empty($this->ajax)) {
?>
</fieldset>
<?php
	}
}

if(empty($this->ajax)) {
?>
<script type="text/javascript">
window.Oby.registerAjax('hikashop.order_update', function(params){
	if(params.el === undefined) return;
	window.Oby.xRequest("<?php echo hikamarket::completeLink('order&task=show&cid='.$this->order_id, true, false, true); ?>", {update: 'hikashop_order_field_market'});
});
</script>
<?php
}
