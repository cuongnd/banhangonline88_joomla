<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class JFormFieldDLGroup extends JFormField
{
	protected function getInput()
	{
		$db = JFactory::getDBO();

		$sql = 'SELECT `id`, `gname` AS title
				FROM `#__support_dl_group`
				ORDER BY `gname`';
		$db->setQuery($sql);
		$options = $db->loadObjectList();
		array_unshift($options, JHTML::_('select.option', '0', '- ' . JText::_('Select') . ' -', 'id', 'title'));

		return JHTML::_('select.genericlist', $options, $this->name, 'class="inputbox"', 'id', 'title', $this->value, $this->name);
	}
}
