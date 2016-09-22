<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><table class="table">
	<tbody>
		<tr>
			<td class="key"><label for="data[plugin][plugin_params][attach_email]"><?php echo JText::_('ATTACH_IN_EMAILS');?></label></td>
			<td><?php
				echo JHTML::_('hikaselect.booleanlist', "data[plugin][plugin_params][attach_email]", '', @$this->element->plugin_params->attach_email);
			?></td>
		</tr>
		<tr>
			<td class="key"><label for="data[plugin][plugin_params][packs]"><?php echo JText::_('SERIAL_PACKS');?></label></td>
			<td><?php
				$packType = hikaserial::get('type.pack');
				echo $packType->displayMultiple('data[plugin][plugin_params][packs]', @$this->element->plugin_params->packs);
			?></td>
		</tr>
		<tr>
			<td class="key"><label for="data[plugin][plugin_params][attach_download]"><?php echo JText::_('ATTACH_AS_DOWNLOAD');?></label></td>
			<td><?php
				echo JHTML::_('hikaselect.booleanlist', "data[plugin][plugin_params][attach_download]", '', @$this->element->plugin_params->attach_download);
				if(!empty($this->element) && !empty($this->element->plugin_id)) {
					echo '<br/>#hikaserial:attachserial:'.(int)$this->element->plugin_id;
				}
			?></td>
		</tr>
	</tbody>
</table>
</fieldset></td></tr>
<?php if(!HIKASHOP_BACK_RESPONSIVE) { ?>
<tr><td colspan="2">
<?php } else { ?>
</div><div class="span16">
<?php } ?>
<fieldset class="adminform"><legend><?php echo JText::_('IMAGE_GENERATION'); ?></legend>
<table class="table" style="width:100%">
	<tbody>
		<tr>
			<td class="key"><label for="data[plugin][plugin_params][image_path]"><?php echo JText::_('IMAGE_PATH');?></label></td>
			<td>
				<input style="width:100%" type="text" name="data[plugin][plugin_params][image_path]" value="<?php echo @$this->element->plugin_params->image_path; ?>"/>
<?php
if(!empty($this->element->plugin_params->image_path)) {
	$file_path = $this->element->plugin_params->image_path;
	$realfilepath = HIKASERIAL_ROOT . $file_path;
	if(!JFile::exists($realfilepath)) {
		$realfilepath = null;
	}
	if(empty($realfilepath)) {
		$shopConfig = hikaserial::config(false);
		$uploadFolder = ltrim(JPath::clean(html_entity_decode($shopConfig->get('uploadfolder'))),DS);
		$realfilepath = JPATH_ROOT.DS.rtrim($uploadFolder,DS).DS.$file_path;
		if(!JFile::exists($realfilepath)) {
			$realfilepath = null;
		}
	}
	if(empty($realfilepath)) {
		$realfilepath = $file_path;
		if(!JFile::exists($file_path)) {
			$realfilepath = null;
		}
	}

	$display_filename = '<span class="attachserial_filename">' . $file_path . '</span>';
	$extension = strtolower(substr($file_path, strrpos($file_path, '.') + 1));
	if(!empty($realfilepath) && !in_array($extension, array('jpg','jpeg','png','gif'))) {
		echo '<br/><img src="'.HIKASERIAL_IMAGES.'icon-16/unpublish.png" alt="" style="vertical-align:middle;"/>'.JText::sprintf('ATTACHSERIAL_IMAGE_WRONG_FORMAT', $display_filename);
	} else if(!empty($realfilepath)) {
		echo '<br/><img src="'.HIKASERIAL_IMAGES.'icon-16/publish.png" alt="" style="vertical-align:middle;"/>'.JText::sprintf('ATTACHSERIAL_IMAGE_EXISTS', $display_filename);
	} else {
		echo '<br/><img src="'.HIKASERIAL_IMAGES.'icon-16/unpublish.png" alt="" style="vertical-align:middle;"/>'.JText::sprintf('ATTACHSERIAL_IMAGE_NOT_EXISTS', $display_filename);
	}
}
?>
			</td>
		</tr>
		<tr>
			<td class="key"><label for="data[plugin][plugin_params][default_font]"><?php echo JText::_('ATTACHSERIAL_FONT');?></label></td>
			<td><?php
				jimport('joomla.filesystem.folder');
				$fonts = JFolder::files(HIKASERIAL_MEDIA . 'ttf' . DS, '.ttf');
				$fonts = array_combine($fonts, $fonts);
				$default_font = @$this->element->plugin_params->default_font;
				if(empty($default_font))
					$default_font = 'opensans-regular.ttf';
				echo JHTML::_('select.genericlist', $fonts, 'data[plugin][plugin_params][default_font]', '', 'value', 'text', $default_font);

				$inheritFont = array(JHTML::_('select.option', '', JText::_('HIKA_INHERIT')));
				$fonts = array_merge($inheritFont, $fonts);
			?> <span style="font-size:0.9em"><?php echo JText::sprintf('ATTACHSERIAL_NOTICE_DOWNLOAD_UPLOAD_TTF', 'http://www.google.com/webfonts', 'media/com_hikaserial/ttf/'); ?></span></td>
		</tr>
	</tbody>
</table>
<?php
	$text_types = array(
		JHTML::_('select.option', 'serial.serial_data', JText::_('SERIAL_DATA')),
		JHTML::_('select.option', 'serial.', JText::_('ATTACHSERIAL_TYPE_SERIAL')),
		JHTML::_('select.option', 'order.', JText::_('ATTACHSERIAL_TYPE_ORDER')),
		JHTML::_('select.option', 'order_product.', JText::_('ATTACHSERIAL_TYPE_ORDER_PRODUCT')),
		JHTML::_('select.option', 'product.', JText::_('ATTACHSERIAL_TYPE_PRODUCT')),
		JHTML::_('select.option', 'customer.', JText::_('ATTACHSERIAL_TYPE_ORDER_CUSTOMER')),
		JHTML::_('select.option', 'shipping.', JText::_('ATTACHSERIAL_TYPE_ORDER_SHIPPING')),
		JHTML::_('select.option', 'billing.', JText::_('ATTACHSERIAL_TYPE_ORDER_BILLING')),
		JHTML::_('select.option', 'entry.', JText::_('ATTACHSERIAL_TYPE_ENTRY')),
		JHTML::_('select.option', 'rawtext.', JText::_('ATTACHSERIAL_TYPE_RAW_TEXT')),
		JHTML::_('select.option', 'dyntext.', JText::_('ATTACHSERIAL_TYPE_DYN_TEXT')),
		JHTML::_('select.option', 'translation.', JText::_('ATTACHSERIAL_TYPE_TRANSLATION')),
		JHTML::_('select.option', 'option_product.', JText::_('ATTACHSERIAL_TYPE_OPTION_PRODUCT')),
		JHTML::_('select.option', 'option_order.', JText::_('ATTACHSERIAL_TYPE_OPTION_ORDER')),
		JHTML::_('select.option', 'product_price.incvat', JText::_('ATTACHSERIAL_TYPE_PRODUCT_PRICE_INCVAT')),
		JHTML::_('select.option', 'product_price.excvat', JText::_('ATTACHSERIAL_TYPE_PRODUCT_PRICE_EXCVAT')),
		JHTML::_('select.option', 'full_product_price.incvat', JText::_('ATTACHSERIAL_TYPE_FULL_PRODUCT_PRICE_INCVAT')),
		JHTML::_('select.option', 'full_product_price.excvat', JText::_('ATTACHSERIAL_TYPE_FULL_PRODUCT_PRICE_EXCVAT')),
		JHTML::_('select.option', 'product.image', JText::_('ATTACHSERIAL_TYPE_PRODUCT_IMAGE')),
		JHTML::_('select.option', 'category.', JText::_('ATTACHSERIAL_TYPE_CATEGORY')),
		JHTML::_('select.option', 'manufacturer.', JText::_('ATTACHSERIAL_TYPE_MANUFACTURER')),
	);

	$marketHelper = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikamarket'.DS.'helpers'.DS.'helper.php';
	if(file_exists($marketHelper)) {
		$text_types[] = JHTML::_('select.option', 'vendor_product.', JText::_('ATTACHSERIAL_TYPE_VENDOR'));
	}

	$text_formats = array(
		JHTML::_('select.option', 'raw', JText::_('ATTACHSERIAL_FORMAT_RAW')),
		JHTML::_('select.option', 'date.', JText::_('ATTACHSERIAL_FORMAT_DATE')),
		JHTML::_('select.option', 'price', JText::_('ATTACHSERIAL_FORMAT_PRICE')),
		JHTML::_('select.option', 'qrcode.', JText::_('ATTACHSERIAL_FORMAT_QRCODE'), 'value', 'text', false),
		JHTML::_('select.option', 'barcode.', JText::_('ATTACHSERIAL_FORMAT_BARCODE'), 'value', 'text', false),
		JHTML::_('select.option', 'image.', JText::_('ATTACHSERIAL_FORMAT_IMAGE')),
	);
?>
<table class="adminlist pad5 table table-striped table-hover" style="width:100%">
	<thead>
		<tr>
			<th><?php echo JText::_('ATTACHSERIAL_TEXT_TYPE');?></th>
			<th><?php echo JText::_('ATTACHSERIAL_TEXT_FORMAT');?></th>
			<th><?php echo JText::_('SERIAL_TEXT_X_Y');?></th>
			<th><?php echo JText::_('SERIAL_TEXT_W_H');?></th>
			<th><?php echo JText::_('SERIAL_TEXT_SIZE_COLOR');?></th>
			<th><?php echo JText::_('ATTACHSERIAL_FONT');?></th>
			<th><a href="#add" onclick="return window.attachserial.attachserialAddText();"><img src="<?php echo HIKASERIAL_IMAGES;?>icon-16/add.png" alt="<?php echo JText::_('HIKA_ADD'); ?></a></th>
		</tr>
	</thead>
	<tbody id="attachserial_texts">
		<tr class="row0">
			<td><?php echo JText::_('SERIAL_DATA'); ?></td>
			<td><?php
				echo JHTML::_('select.genericlist', $text_formats, 'data[plugin][plugin_params][serial_text_format]', 'onchange="window.attachserial.changeFormat(this);"', 'value', 'text', @$this->element->plugin_params->serial_text_format, 'attserial_format_main');
			?><br/><input type="text" style="<?php if(substr(@$this->element->plugin_params->serial_text_format, -1) != '.') echo 'display:none';?>" id="attserial_format_ex_main" name="data[plugin][plugin_params][serial_text_format_ex]" value="<?php echo @$this->element->plugin_params->serial_text_format_ex; ?>"/></td>
			<td>
				<?php echo JText::_('SERIAL_TEXT_X');?> <input type="text" size="8" name="data[plugin][plugin_params][serial_text_x]" value="<?php echo $this->escape(@$this->element->plugin_params->serial_text_x); ?>"/>px<br/>
				<?php echo JText::_('SERIAL_TEXT_Y');?> <input type="text" size="8" name="data[plugin][plugin_params][serial_text_y]" value="<?php echo $this->escape(@$this->element->plugin_params->serial_text_y); ?>"/>px
			</td>
			<td>
				<?php echo JText::_('SERIAL_TEXT_W');?> <input type="text" size="8" name="data[plugin][plugin_params][serial_text_w]" value="<?php echo $this->escape(@$this->element->plugin_params->serial_text_w); ?>"/>px<br/>
				<?php echo JText::_('SERIAL_TEXT_H');?> <input type="text" size="8" name="data[plugin][plugin_params][serial_text_h]" value="<?php echo $this->escape(@$this->element->plugin_params->serial_text_h); ?>"/>px
			</td>
			<td>
				<input type="text" size="8" name="data[plugin][plugin_params][serial_text_size]" value="<?php echo $this->escape(@$this->element->plugin_params->serial_text_size); ?>"/>px<br/>
				#<input type="text" size="8" name="data[plugin][plugin_params][serial_text_color]" value="<?php echo $this->escape(@$this->element->plugin_params->serial_text_color); ?>"/>
			</td>
			<td><?php echo JHTML::_('select.genericlist', $fonts, 'data[plugin][plugin_params][serial_text_font]', '', 'value', 'text', @$this->element->plugin_params->serial_text_font); ?></td>
			<td></td>
		</tr>
<?php
	$k = 1;
	$i = 0;
	if(!empty($this->element->plugin_params->texts)) {
		foreach($this->element->plugin_params->texts as $text) {
			$map = 'data[plugin][plugin_params][texts][' . $i . ']';
?>
		<tr class="row<?php echo $k; ?>">
			<td><?php
				echo JHTML::_('select.genericlist', $text_types, $map.'[type]', 'onchange="window.attachserial.changeType(this);"', 'value', 'text', $text['type'], 'attserial_type_' . $i);
			?><br/><input size="30" type="text" style="<?php if(substr(@$text['type'], -1) != '.') echo 'display:none';?>" id="attserial_type_ex_<?php echo $i;?>" name="<?php echo $map.'[type_ex]'; ?>" value="<?php echo $this->escape(@$text['type_ex']); ?>"/></td>
			<td><?php
				echo JHTML::_('select.genericlist', $text_formats, $map.'[format]', 'onchange="window.attachserial.changeFormat(this);"', 'value', 'text', $text['format'], 'attserial_format_' . $i);
			?><br/><input type="text" style="<?php if(substr(@$text['format'], -1) != '.') echo 'display:none';?>" id="attserial_format_ex_<?php echo $i;?>" name="<?php echo $map.'[format_ex]'; ?>" value="<?php echo $this->escape(@$text['format_ex']); ?>"/></td>
			<td>
				<?php echo JText::_('SERIAL_TEXT_X');?> <input type="text" size="8" name="<?php echo $map.'[x]'; ?>" value="<?php echo $this->escape(@$text['x']); ?>"/>px<br/>
				<?php echo JText::_('SERIAL_TEXT_Y');?> <input type="text" size="8" name="<?php echo $map.'[y]'; ?>" value="<?php echo $this->escape(@$text['y']); ?>"/>px
			</td>
			<td>
				<?php echo JText::_('SERIAL_TEXT_W');?> <input type="text" size="8" name="<?php echo $map.'[w]'; ?>" value="<?php echo $this->escape(@$text['w']); ?>"/>px<br/>
				<?php echo JText::_('SERIAL_TEXT_H');?> <input type="text" size="8" name="<?php echo $map.'[h]'; ?>" value="<?php echo $this->escape(@$text['h']); ?>"/>px
			</td>
			<td>
				<input type="text" size="8" name="<?php echo $map.'[size]'; ?>" value="<?php echo $this->escape(@$text['size']); ?>"/>px<br/>
				#<input type="text" size="8" name="<?php echo $map.'[color]'; ?>" value="<?php echo $this->escape(@$text['color']); ?>"/>
			</td>
			<td><?php echo JHTML::_('select.genericlist', $fonts, $map.'[font]', '', 'value', 'text', @$text['font']); ?></td>
			<td>
				<a href="#delete" onclick="hikaserial.deleteRow(this); return false;"><img src="<?php echo HIKASERIAL_IMAGES; ?>icon-16/delete.png" alt="<?php echo JText::_('HIKA_DELETE');?>"/></a>
			</td>
		</tr>
<?php
			$k = 1 - $k;
			$i++;
		}
	}
?>
		<tr id="attachserial_tpl_line" class="row<?php echo $k;?>" style="display:none">
			<td><?php
				echo JHTML::_('select.genericlist', $text_types, '{text_type}', 'class="no-chzn" onchange="window.attachserial.changeType(this);"', 'value', 'text', '', 'attserial_type_{id}');
			?><br/><input size="30" type="text" style="display:none" id="attserial_type_ex_{id}"  name="{text_type_ex}" value=""/></td>
			<td><?php
				echo JHTML::_('select.genericlist', $text_formats, '{text_format}', 'class="no-chzn" onchange="window.attachserial.changeFormat(this);"', 'value', 'text', '', 'attserial_format_{id}');
			?><br/><input type="text" style="display:none" id="attserial_format_ex_{id}" name="{text_format_ex}" value=""/></td>
			<td>
				<?php echo JText::_('SERIAL_TEXT_X');?> <input type="text" size="8" name="{text_x}" value=""/>px<br/>
				<?php echo JText::_('SERIAL_TEXT_Y');?> <input type="text" size="8" name="{text_y}" value=""/>px
			</td>
			<td>
				<?php echo JText::_('SERIAL_TEXT_W');?> <input type="text" size="8" name="{text_w}" value=""/>px<br/>
				<?php echo JText::_('SERIAL_TEXT_H');?> <input type="text" size="8" name="{text_h}" value=""/>px
			</td>
			<td>
				<input type="text" size="8" name="{text_size}" value=""/>px<br/>
				#<input type="text" size="8" name="{text_color}" value=""/>
			</td>
			<td><?php echo JHTML::_('select.genericlist', $fonts, '{text_color}', '', 'value', 'text', ''); ?></td>
			<td>
				<a href="#delete" onclick="hikaserial.deleteRow(this); return false;"><img src="<?php echo HIKASERIAL_IMAGES; ?>icon-16/delete.png" alt="<?php echo JText::_('HIKA_DELETE');?>"/></a>
			</td>
		</tr>
	</tbody>
</table>
<script type="text/javascript">
window.attachserial = {
	cpt: <?php echo $i; ?>,
	attachserialAddText: function() {
		var t = this, d = document,
			tbody = d.getElementById('hikaserial_attachserial_texts'),
			htmlblocks = {
				id:t.cpt,
				text_type: 'data[plugin][plugin_params][texts]['+t.cpt+'][type]',
				text_type_ex: 'data[plugin][plugin_params][texts]['+t.cpt+'][type_ex]',
				text_format: 'data[plugin][plugin_params][texts]['+t.cpt+'][format]',
				text_format_ext: 'data[plugin][plugin_params][texts]['+t.cpt+'][format_ext]',
				text_x: 'data[plugin][plugin_params][texts]['+t.cpt+'][x]',
				text_y: 'data[plugin][plugin_params][texts]['+t.cpt+'][y]',
				text_w: 'data[plugin][plugin_params][texts]['+t.cpt+'][w]',
				text_h: 'data[plugin][plugin_params][texts]['+t.cpt+'][h]',
				text_size: 'data[plugin][plugin_params][texts]['+t.cpt+'][size]',
				text_color: 'data[plugin][plugin_params][texts]['+t.cpt+'][color]',
			};
		hikaserial.dupRow('attachserial_tpl_line', htmlblocks, 'attachserial_text_' + t.cpt);
<?php if(HIKASHOP_BACK_RESPONSIVE) { ?>
		try {
			jQuery('#attserial_type_'+t.cpt).removeClass("chzn-done").chosen();
			jQuery('#attserial_format_'+t.cpt).removeClass("chzn-done").chosen();
		}catch(e){}
<?php } ?>
		t.cpt++;
		return false;
	},
	changeType: function(el) { return this.dropdownChange(el, 'attserial_type'); },
	changeFormat: function(el) { return this.dropdownChange(el, 'attserial_format'); },
	dropdownChange: function(el, key) {
		var d = document,
			id = el.id.replace(key+'_', key+'_ex_'),
			dest = d.getElementById(id);
		if(el.value.substring(el.value.length - 1) == '.') {
			dest.style.display = '';
			dest.focus();
		} else {
			dest.style.display = 'none';
		}
	}
}
</script>
