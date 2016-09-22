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
	<table width="100%">
		<tr>
			<td style="font-weight:bold;background-color:#DDDDDD"><?php echo JText::_('SERIAL_DATA');?></td>
			<td style="font-weight:bold;background-color:#DDDDDD"><?php echo JText::_('ATTACHED_TO_PRODUCT');?></td>
		</tr>
<?php
	foreach($this->data as $data) {
?>
		<tr>
			<td><?php echo $data->serial_data; ?></td>
			<td><?php echo $data->order_product_name; ?></td>
		</tr>
<?php
	}
?>
	</table>
<?php
}
?>
