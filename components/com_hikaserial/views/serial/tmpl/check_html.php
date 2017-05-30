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
if(!empty($this->serials)) {
	foreach($this->serials as $serial) {
?>
<table class="hikaserial_checkserial">
	<tr>
		<td class="key"><?php echo JText::_('SERIAL_STATUS'); ?></td>
		<td><?php echo $serial->serial_status; ?></td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_('ASSIGN_DATE'); ?></td>
		<td><?php
			if(!empty($serial->serial_assign_date))
				echo hikaserial::getDate($serial->serial_assign_date);
		?></td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_('SERIAL_DATA'); ?></td>
		<td><?php echo $serial->serial_data; ?></td>
	</tr>
<?php
		if(!empty($serial->serial_extradata)) {
?>
	<tr>
		<td colspan="2" class="hikaserial_checkserial_sep"><?php echo JText::_('SERIAL_EXTRA_DATA'); ?></td>
	</tr>
<?php
			foreach($serial->serial_extradata as $key => $value) {
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
} else {
	$app = JFactory::getApplication();
	$app->enqueueMessage(JText::_('NO_SERIAL'));
}
