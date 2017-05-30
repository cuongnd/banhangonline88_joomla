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
if(!empty($this->serial)) {
?>
<div class="hikaserial_serial_consume_page">
	<fieldset class="input">
		<h2><?php echo JText::_('HIKASERIAL_SERIAL_CONSUMED'); ?></h2>
		<h3><?php echo $this->serial->serial_data; ?></h3>
<?php
$config = hikaserial::config();
if($config->get('consume_display_details', 0)) {
?>
<table class="hikaserial_checkserial" style="width:100%;">
<?php
	if(!empty($this->serial->serial_assign_date)) {
?>
		<tr>
			<td class="key"><?php echo JText::_('ASSIGN_DATE'); ?></td>
			<td><?php echo hikaserial::getDate($this->serial->serial_assign_date); ?></td>
		</tr>
<?php
	}

	if(!empty($this->serial->serial_order_id) && !empty($this->serial->order)) {
?>
		<tr>
			<td class="key"><?php echo JText::_('ORDER_ID'); ?></td>
			<td><?php echo $this->serial->order->order_number; ?></td>
		</tr>
<?php
	}

	if(!empty($this->serial->serial_order_product_id) && !empty($this->serial->orderproduct)) {
?>
		<tr>
			<td class="key"><?php echo JText::_('PRODUCT'); ?></td>
			<td><?php echo $this->serial->orderproduct->order_product_name; ?></td>
		</tr>
<?php
	}

	if(!empty($this->serial->serial_user_id) && !empty($this->serial->user)) {
?>
		<tr>
			<td class="key"><?php echo JText::_('USER'); ?></td>
			<td><?php echo $this->serial->user->user_email; ?></td>
		</tr>
<?php
	}

	if(!empty($this->serial->serial_extradata)) {
?>
		<tr>
			<td colspan="2" class="hikaserial_checkserial_sep"><?php echo JText::_('SERIAL_EXTRA_DATA'); ?></td>
		</tr>
<?php
		foreach($this->serial->serial_extradata as $key => $value) {
?>
		<tr>
			<td class="key"><?php echo $key; ?></td>
			<td><?php echo $value; ?></td>
		</tr>
<?php
		}
	}
?>
	</table>
<?php
}
?>
		<p><?php echo JText::_('CONSUME_THANK_YOU'); ?></p>
	</fieldset>
</div>
<?php
} else {
	global $Itemid;
	$url_itemid='';
	if(!empty($Itemid)){
		$url_itemid='&Itemid='.$Itemid;
	}
?>
<div class="hikaserial_serial_consume_page">
	<fieldset class="input">
		<p class="hikaserial_serial_consume_error"><?php echo JText::_('CONSUME_INVALID_SERIAL') ?></p>
		<p class="hikaserial_serial_consume_back"><a href="<?php echo hikaserial::completeLink('serial&task=consume'.$url_itemid); ?>"><?php echo JText::_('HIKASERIAL_GO_BACK'); ?></a></p>
	</fieldset>
</div>
<?php
}
