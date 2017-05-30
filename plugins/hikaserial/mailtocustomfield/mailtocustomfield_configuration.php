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
			<td class="key"><label for="data[plugin][plugin_params][packs]"><?php echo JText::_('SERIAL_PACKS');?></label></td>
			<td><?php
				$packType = hikaserial::get('type.pack');
				echo $packType->displayMultiple('data[plugin][plugin_params][packs]', @$this->element->plugin_params->packs);
			?></td>
		</tr>
		<tr>
			<td class="key"><label for="data[plugin][plugin_params][custom_field]"><?php echo JText::_('SERIAL_PLUGIN_CUSTOM_FIELD'); ?></label></td>
			<td><?php
	$db = JFactory::getDBO();
	$query = 'SELECT field.field_namekey, field.field_realname, field.field_table '.
		' FROM ' . hikaserial::table('shop.field') . ' AS field '.
		' WHERE field.field_table IN (\'order\', \'item\') AND field.field_type IN (\'text\',\'textarea\',\'radio\',\'checkbox\',\'singledropdown\',\'multipledropdown\') AND field_published = 1 AND field_frontcomp = 1'.
		' ORDER BY field.field_table, field.field_realname';
	$db->setQuery($query);
	$customfields = $db->loadObjectList();

	$fields = array();
	foreach($customfields as $customfield) {
		$fields[] = JHTML::_('select.option', $customfield->field_table.'.'.$customfield->field_namekey, $customfield->field_table . ' - ' . $customfield->field_realname);
	}
	unset($query);
	unset($customfields);

	echo JHTML::_('select.genericlist', $fields, 'data[plugin][plugin_params][custom_field]', '', 'value', 'text', @$this->element->plugin_params->custom_field);
			?></td>
		</tr>
		<tr>
			<td class="key"><label for="data[plugin][plugin_params][call_attachserial]"><?php echo JText::_('SERIAL_PLUGIN_USE_ATTACHSERIAL');?></label></td>
			<td><?php
				echo JHTML::_('hikaselect.booleanlist', "data[plugin][plugin_params][call_attachserial]", '', @$this->element->plugin_params->call_attachserial);
			?></td>
		</tr>
	</tbody>
</table>
