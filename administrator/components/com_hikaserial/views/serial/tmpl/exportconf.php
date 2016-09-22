<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><form action="<?php echo hikaserial::completeLink('serial'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="admintable table" style="width:100%">
		<tr>
			<td class="key">
				<label><?php echo JText::_('SERIAL_PACK'); ?></label>
			</td>
			<td><?php
				$values = array(
					JHTML::_('select.option', '0', JText::_('HIKASHOP_NO')),
					JHTML::_('select.option', 's', JText::_('PACK_NAME')),
					JHTML::_('select.option', '1', JText::_('ID'))
				);
				echo JHTML::_('hikaselect.radiolist', $values, 'data[export][pack]', '', 'value', 'text', '0');
			?></td>
		</tr>
		<tr>
			<td class="key">
				<label><?php echo JText::_('SERIAL_STATUS'); ?></label>
			</td>
			<td><?php
				echo JHTML::_('hikaselect.booleanlist', 'data[export][status]', 0);
			?></td>
		</tr>
		<tr>
			<td class="key">
				<label><?php echo JText::_('HIKASERIAL_ORDER'); ?></label>
			</td>
			<td><?php
				$values = array(
					JHTML::_('select.option', '0', JText::_('HIKASHOP_NO')),
					JHTML::_('select.option', 's', JText::_('ORDER_NUMBER')),
					JHTML::_('select.option', '1', JText::_('ID'))
				);
				echo JHTML::_('hikaselect.radiolist', $values, 'data[export][order]', '', 'value', 'text', '0');
			?></td>
		</tr>
		<tr>
			<td class="key">
				<label><?php echo JText::_('HIKA_USER'); ?></label>
			</td>
			<td><?php
				$values = array(
					JHTML::_('select.option', '0', JText::_('HIKASHOP_NO')),
					JHTML::_('select.option', 's', JText::_('HIKA_USERNAME')),
					JHTML::_('select.option', '1', JText::_('HKASHOP_USER_ID'))
				);
				echo JHTML::_('hikaselect.radiolist', $values, 'data[export][user]', '', 'value', 'text', '0');
			?></td>
		</tr>
		<tr>
			<td class="key">
				<label><?php echo JText::_('SERIAL_ID'); ?></label>
			</td>
			<td><?php
				echo JHTML::_('hikaselect.booleanlist', 'data[export][id]', 0);
			?></td>
		</tr>
		<tr>
			<td class="key">
				<label><?php echo JText::_('SERIAL_EXTRA_DATA'); ?></label>
			</td>
			<td><?php
				echo JHTML::_('hikaselect.booleanlist', 'data[export][extra]', 0);
			?></td>
		</tr>
		<tr>
			<td class="key">
				<label><?php echo JText::_('ASSIGN_DATE'); ?></label>
			</td>
			<td><?php
				echo JHTML::_('hikaselect.booleanlist', 'data[export][date]', 0);
			?></td>
		</tr>
		<tr>
			<td class="key">
				<label><?php echo JText::_('EXPORT_FORMAT'); ?></label>
			</td>
			<td><?php
				$values = array(
					JHTML::_('select.option', 'csv', JText::_('SERIAL_EXPORT_CSV')),
					JHTML::_('select.option', 'xls', JText::_('SERIAL_EXPORT_XLS'))
				);
				echo JHTML::_('hikaselect.radiolist', $values, 'data[export][format]', '', 'value', 'text', 'csv');
			?></td>
		</tr>
	</table>
	<input type="hidden" name="exportconfiguration" value="1" />
	<input type="hidden" name="option" value="<?php echo HIKASERIAL_COMPONENT; ?>" />
	<input type="hidden" name="task" value="applyexportconf" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
</form>
