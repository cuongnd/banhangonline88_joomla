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
if(!defined('HIKAMARKET_COMPONENT'))
	return;
?><dt class="hikamarket_product_plugin_hikaserial"><label><?php echo JText::_('HIKA_SERIALS'); ?></label></dt>
<dd class="hikamarket_product_plugin_hikaserial">
	<table class="adminlist table table-striped" style="width:100%">
		<thead>
			<tr>
				<th class="title"><?php echo JText::_('HIKA_NAME');?></th>
				<th class="title"><?php echo JText::_('PRODUCT_QUANTITY');?></th>
				<th class="title"><?php echo JText::_('ID');?></th>
				<th class="title" style="width:14%">
					<button class="btn" onclick="return window.productMgr.serial_togglePacks(this, <?php echo (int)$this->product_id; ?>);" type="button" style="margin:0px;">
						<img src="<?php echo HIKASHOP_IMAGES;?>add.png" style="vertical-align:middle"/><?php echo JText::_('ADD');?>
					</button>
				</th>
			</tr>
		</thead>
		<tbody>
<?php
$k = 0;
if(empty($this->data))
	$this->data = array();
foreach($this->data as $data) {
?>
			<tr class="row<?php echo $k; ?>" id="serial_pack_<?php echo $data->pack_id; ?>_<?php echo (int)$this->product_id; ?>">
				<td><?php echo $data->pack_name;?></td>
				<td><input type="text" value="<?php echo $data->quantity;?>" name="data[hikaserial][<?php echo (int)$this->product_id; ?>][pack_qty][]" size="5" style="width:auto;"/></td>
				<td align="center"><?php echo $data->pack_id;?><input type="hidden" value="<?php echo $data->pack_id;?>" name="data[hikaserial][<?php echo (int)$this->product_id; ?>][pack_id][]"/></td>
				<td align="center"><a href="#" onclick="window.hikaserial.deleteRow(this); return false;"><img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/delete.png" alt="<?php echo JText::_('DELETE'); ?>"/></a></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
			<tr class="row<?php echo $k; ?>" style="display:none" id="hikaserial_tpl_pack_line_<?php echo (int)$this->product_id; ?>">
				<td>{pack_name}</td>
				<td><input type="text" value="1" name="{input_qty_name}" size="5" style="width:auto;"/></td>
				<td align="center">{id}<input type="hidden" value="{id}" name="{input_id_name}"/></td>
				<td align="center"><a href="#" onclick="window.hikaserial.deleteRow(this); return false;"><img src="<?php echo HIKAMARKET_IMAGES;?>icon-16/delete.png" alt="<?php echo JText::_('DELETE'); ?>"/></a></td>
			</tr>
		</tbody>
	</table>
	<div style="display:none;" id="hikaserial_selector_pack_line_<?php echo (int)$this->product_id; ?>">
		<?php
			echo $this->nameboxType->display(
				'',
				'',
				hikashopNameboxType::NAMEBOX_MULTIPLE,
				'plg.hikaserial.pack',
				array(
					'delete' => true,
					'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
					'id' => 'hikaserial_add_packs_'.(int)$this->product_id
				)
			);
		?>
		<div style="clear:both;margin-top:4px;"></div>
		<div style="float:right">
			<button onclick="return window.productMgr.serial_addPack(this, <?php echo (int)$this->product_id; ?>);" class="btn btn-success"><img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/plus.png" alt="" style="vertical-align:middle;"/> <?php echo JText::_('ADD_PACKS'); ;?></button>
		</div>
		<button onclick="return window.productMgr.serial_togglePacks(this, <?php echo (int)$this->product_id; ?>);" class="btn btn-danger"><img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/cancel.png" alt="" style="vertical-align:middle;"/> <?php echo JText::_('HIKA_CANCEL'); ;?></button>
		<div style="clear:both"></div>
	</div>
<script type="text/javascript">
if(!window.productMgr) window.productMgr = {};
window.productMgr.serial_togglePacks = function(el, id) {
	var d = document, element = d.getElementById('hikaserial_selector_pack_line_' + id);
	if(element)
		element.style.display = (element.style.display == 'none' ? '' : 'none');
	if(element && element.style.display == 'none') {
		var box = window.oNameboxes['hikaserial_add_packs_' + id];
		if(box)
			box.clear();
	}
	return false;
};
window.productMgr.serial_addPack = function(el, id) {
	var box = window.oNameboxes['hikaserial_add_packs_' + id];
	if(!box)
		return window.productMgr.serial_togglePacks(el, id);
	var values = box.get(), htmlData = null;
	box.clear();
	if(values && values.length > 0) {
		for(var i = 0; i < values.length; i++) {
			htmlData = { 'input_qty_name': 'data[hikaserial]['+id+'][pack_qty][]', 'input_id_name': 'data[hikaserial]['+id+'][pack_id][]', 'id': values[i].value, 'pack_name': values[i].name };
			window.hikaserial.dupRow('hikaserial_tpl_pack_line_' + id, htmlData, 'serial_pack_'+values[i].value+'_<?php echo (int)$this->product_id; ?>');
		}
	}
	return window.productMgr.serial_togglePacks(el, id);
};
</script>
<input type="hidden" name="data[hikaserial][form]" value="1"/>
</dd>
