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
	if(empty($this->data) && !$this->show_refresh && !$this->manage_order_packs)
		return;

	if(empty($this->order_id))
		return;

	if(empty($this->ajax)) {
?>
<fieldset class="adminform" id="hikashop_order_field_serial">
<?php
	}
?>
	<legend><?php echo JText::_('HIKA_SERIALS')?></legend>
<?php if($this->show_refresh || $this->manage_order_packs) { ?>
	<div style="float:right">
<?php if($this->manage_order_packs) { ?>
		<a class="btn" href="#addPack" onclick="return window.orderMgr.showAddPack(this);">
			<img src="<?php echo HIKASHOP_IMAGES;?>add.png" style="vertical-align:middle;margin:0px 5px 0px 0px;padding:0px;"/><?php echo JText::_('ADD_PACK');?>
		</a>
<?php } ?>
<?php if($this->show_refresh) { ?>
		<a class="btn btn-info" href="<?php echo hikaserial::completeLink('orderserial&task=refresh&order_id='.$this->order_id, true); ?>">
			<img src="<?php echo HIKASHOP_IMAGES;?>refresh.png" style="vertical-align:middle;margin:0px 5px 0px 0px;padding:0px;"/><?php echo JText::_('REFRESH_ASSOCIATIONS');?>
		</a>
<?php } ?>
	</div>
<?php if($this->manage_order_packs) { ?>
	<div id="hikashop_order_field_serial_add_pack" class="hikaserial_dynamic_selector" style="display:none;clear:both;padding-top:5px;">
		<dl class="hika_options	">
			<dt><?php echo JText::_('PACK') ;?></dt>
			<dd><?php
				echo $this->nameboxType->display(
					'',
					'',
					hikashopNameboxType::NAMEBOX_MULTIPLE,
					'plg.hikaserial.pack',
					array(
						'delete' => true,
						'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
						'id' => 'hikaserial_add_packs_pack_'.(int)$this->order_id
					)
				);
			?></dd>
			<dt><?php echo JText::_('HIKASERIAL_FIXED_QUANTITY') ;?></dt>
			<dd>
				<input type="text" value="1" id="hikaserial_add_packs_qty_fix_<?php echo (int)$this->order_id; ?>" />
			</dd>
			<dt><?php echo JText::_('HIKASERIAL_VARIABLE_QUANTITY') ;?></dt>
			<dd>
				<input type="text" value="0" id="hikaserial_add_packs_qty_var_<?php echo (int)$this->order_id; ?>" />
			</dd>
		</dl>
		<div style="clear:both;margin-top:4px;"></div>
		<div style="float:right">
			<button onclick="return window.orderMgr.order_addPack(this);" class="btn btn-success"><img src="<?php echo HIKASHOP_IMAGES; ?>plus.png" alt="" style="vertical-align:middle;"/> <?php echo JText::_('ADD_PACK'); ;?></button>
		</div>
		<button onclick="return window.orderMgr.showAddPack(this);" class="btn btn-danger"><img src="<?php echo HIKASHOP_IMAGES; ?>cancel.png" alt="" style="vertical-align:middle;"/> <?php echo JText::_('HIKA_CANCEL'); ;?></button>
		<div style="clear:both"></div>
	</div>
<?php } ?>
<?php } ?>
	<table style="width:100%;cell-spacing:1px;" class="adminlist table table-striped">
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
if(!empty($this->data)) {
	$k = 0;
	foreach($this->data as $data) {
?>
			<tr class="row<?php echo $k; ?>">
				<td><a href="<?php echo hikaserial::completeLink('pack&task=edit&cid[]='.$data->pack_id);?>"><?php echo $data->pack_name;?></a></td>
				<td><a href="<?php echo hikaserial::completeLink('serial&task=edit&cid[]='.$data->serial_id);?>"><?php echo $data->serial_data; ?></a></td>
				<td><?php echo hikaserial::getDate($data->serial_assign_date);?></td>
				<td><?php
					if($data->order_product_name !== null) {
						echo $data->order_product_name;
					} else if(!empty($data->assignation)) {
						echo '<em>' . JText::_('HIKASERIAL_ORDER_ASSIGNATION') . '</em>';
					}
				?></td>
			</tr>
<?php
		$k = 1 - $k;
	}
}
?>
		</tbody>
	</table>
<?php
if($this->manage_order_packs && !empty($this->order_serial_params) && (!empty($this->order_serial_params['order']) || !empty($this->order_serial_params['product']) || !empty($this->order_serial_params['order_product']) || !empty($this->order_serial_params['serial']))) {
?>
<h3><?php echo JText::_('HIKASERIAL_ORDER_ASSIGNATION');?></h3>
<table style="width:100%;cell-spacing:1px;" class="adminlist table table-striped">
	<thead>
		<tr>
			<th><?php echo JText::_('HIKASERIAL_ORDER_ASSIGNATION_TYPE');?></th>
			<th><?php echo JText::_('PACK_NAME');?></th>
			<th><?php echo JText::_('HIKASERIAL_ASSIGNATION_TARGET');?></th>
			<th><?php echo JText::_('HIKASERIAL_ASSIGNATION_QUANTITY');?></th>
		</tr>
	</thead>
	<tbody>
<?php
	$k = 0;
	if(!empty($this->order_serial_params['order'])) {
		foreach($this->order_serial_params['order'] as $pack_id => $v) {
?>
		<tr class="row<?php echo $k; ?>">
			<td><?php echo JText::_('HIKASERIAL_ORDER'); ?></td>
			<td><a href="<?php echo hikaserial::completeLink('pack&task=edit&cid[]='.$pack_id);?>"><?php
				echo @$this->packs[ $pack_id ]->pack_name;
			?></a></td>
			<td>-</td>
			<td><?php
				if(!empty($v[0]))
					echo $v[0] . ' ('.JText::_('HIKASERIAL_VARIABLE_QUANTITY').')<br/>';
				if(!empty($v[1]))
					echo $v[1] . ' ('.JText::_('HIKASERIAL_FIXED_QUANTITY').')<br/>';
			?></td>
		</tr>
<?php
			$k = 1 - $k;
		}
	}
	if(!empty($this->order_serial_params['product'])) {
		foreach($this->order_serial_params['product'] as $product_id => $w) {
			foreach($w as $pack_id => $v) {
?>
		<tr class="row<?php echo $k; ?>">
			<td><?php echo JText::_('HIKA_PRODUCT'); ?></td>
			<td><a href="<?php echo hikaserial::completeLink('pack&task=edit&cid[]='.$pack_id);?>"><?php
				echo @$this->packs[ $pack_id ]->pack_name;
			?></a></td>
			<td><?php echo $product_id; ?></td>
			<td><?php
				if(!empty($v[0]))
					echo $v[0] . ' ('.JText::_('HIKASERIAL_VARIABLE_QUANTITY').')<br/>';
				if(!empty($v[1]))
					echo $v[1] . ' ('.JText::_('HIKASERIAL_FIXED_QUANTITY').')<br/>';
			?></td>
		</tr>
<?php
				$k = 1 - $k;
			}
		}
	}
	if(!empty($this->order_serial_params['order_product'])) {
		foreach($this->order_serial_params['order_product'] as $order_product_id => $w) {
			foreach($w as $pack_id => $v) {
?>
		<tr class="row<?php echo $k; ?>">
			<td><?php echo JText::_('HIKA_ORDER_PRODUCT'); ?></td>
			<td><a href="<?php echo hikaserial::completeLink('pack&task=edit&cid[]='.$pack_id);?>"><?php
				echo @$this->packs[ $pack_id ]->pack_name;
			?></a></td>
			<td><?php echo $order_product_id; ?></td>
			<td data-serial-qty="<?php echo (int)@$v[0]; ?>" data-serial-qtyvar="<?php echo (int)@$v[1]; ?>"><?php
				if(!empty($v[0]))
					echo $v[0] . ' ('.JText::_('HIKASERIAL_VARIABLE_QUANTITY').')<br/>';
				if(!empty($v[1]))
					echo $v[1] . ' ('.JText::_('HIKASERIAL_FIXED_QUANTITY').')<br/>';
			?></td>
		</tr>
<?php
				$k = 1 - $k;
			}
		}
	}
	if(!empty($this->order_serial_params['serial'])) {
		foreach($this->order_serial_params['serial'] as $serial_id) {
?>
		<tr class="row<?php echo $k; ?>">
			<td><?php echo JText::_('HIKA_SERIALS'); ?></td>
			<td>-</td>
			<td><?php echo $serial_id; ?></td>
			<td>-</td>
		</tr>
<?php
			$k = 1 - $k;
		}
	}
?>
	</tbody>
</table>
<?php
}
?>
<?php
	if(empty($this->ajax)) {
?>
</fieldset>
<script type="text/javascript">
window.Oby.registerAjax('hikashop.order_update',function(params){
	if(params.el === undefined) return;
	window.Oby.xRequest("<?php echo hikaserial::completeLink('orderserial&task=show&cid='.$this->order_id, true, false, true); ?>", {update: 'hikashop_order_field_serial'});
});
if(!window.orderMgr)
	window.orderMgr = {};
window.orderMgr.showAddPack = function(el) {
	var d = document, c = d.getElementById('hikashop_order_field_serial_add_pack');
	if(!c)
		return false;
	c.style.display = (c.style.display == 'none') ? '' : 'none';
	if(c.style.display != 'none')
		return false;
	var nb = window.oNameboxes['hikaserial_add_packs_pack_<?php echo (int)$this->order_id; ?>'];
	if(nb)
		nb.clear();
	var qty = d.getElementById('hikaserial_add_packs_qty_<?php echo (int)$this->order_id; ?>');
	if(qty)
		qty.value = "1";
	return false;
};
window.orderMgr.order_addPack = function(el) {
	var w = window, o = w.Oby, d = document,
		nb = w.oNameboxes['hikaserial_add_packs_pack_<?php echo (int)$this->order_id; ?>'],
		qtyFix = d.getElementById('hikaserial_add_packs_qty_fix_<?php echo (int)$this->order_id; ?>'),
		qtyVar = d.getElementById('hikaserial_add_packs_qty_var_<?php echo (int)$this->order_id; ?>');
	if(!nb)
		return false;
	var value = nb.get();
	if(value)
		value = value[0];
	var data = 'pack=' + parseInt(value.value) + '&qty=' + parseInt(qtyFix.value) + '&qtyvar=' + parseInt(qtyVar.value) + '&<?php echo hikaserial::getFormToken(); ?>=1';
	o.xRequest('<?php echo hikaserial::completeLink('orderserial&task=addpack&order_id='.$this->order_id, true, false, true); ?>', {mode: 'POST', data:data}, function(xhr) {
		if(xhr.reponseText == '1')
			window.Oby.xRequest("<?php echo hikaserial::completeLink('orderserial&task=show&cid='.$this->order_id, true, false, true); ?>", {update: 'hikashop_order_field_serial'});
	});
	return this.showAddPack(el);
};
</script>
<?php }
