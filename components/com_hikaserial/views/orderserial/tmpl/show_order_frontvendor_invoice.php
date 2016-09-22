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
if(!empty($this->data)) {
?>
<tr>
	<td>
		<fieldset class="hikam_field">
			<legend><?php echo JText::_('HIKA_SERIALS')?></legend>
			<table style="width:100%;" class="hikam_listing hikam_table">
				<thead>
					<tr>
						<th><?php echo JText::_('PACK_NAME');?></th>
						<th><?php echo JText::_('SERIAL_DATA');?></th>
						<th><?php echo JText::_('ATTACHED_TO_PRODUCT');?></th>
					</tr>
				</thead>
				<tbody>
<?php
	foreach($this->data as $data) {
?>
					<tr>
						<td><?php echo $data->pack_name;?></td>
						<td><?php echo $data->serial_data; ?></td>
						<td><?php echo $data->order_product_name; ?></td>
					</tr>
<?php
	}
?>
				</tbody>
			</table>
		</fieldset>
	</td>
</tr>
<?php
}
?>
