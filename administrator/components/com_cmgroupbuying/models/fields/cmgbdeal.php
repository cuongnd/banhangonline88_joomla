<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

if(version_compare(JVERSION, '3.0', 'lt'))
{
	jimport('joomla.html.parameter.element');
	class JElementCmgbdeal extends JElement
	{
		var $_name = 'Cmgbdeal';

		function fetchElement($name, $value, &$node, $control_name)
		{
			$fieldName = $control_name.'['.$name.']';
			$dealOption = array();

			$db = JFactory::getDbo();
			$query = 'SELECT id, name FROM #__cmgroupbuying_deals ORDER BY name ASC';
			$db->setQuery($query);
			$deals = $db->loadAssocList();

			foreach($deals as $deal)
			{
				$dealOption[] = JHTML::_('select.option', $deal['id'], $deal['name']);
			}

			$html[] = JHTML::_('select.genericlist', $dealOption, $fieldName, null, 'value', 'text', $value);

			return implode($html);
		}
	}
}
else
{
	jimport('joomla.html.html');
	jimport('joomla.form.formfield');

	class JFormFieldCmgbdeal extends JFormField
	{
		protected $type = 'Cmgbdeal';

		protected function getInput()
		{
			$dealOption = array();
			$db = JFactory::getDbo();
			$query = 'SELECT id, name FROM #__cmgroupbuying_deals ORDER BY name ASC';
			$db->setQuery($query);
			$deals = $db->loadAssocList();

			foreach($deals as $deal)
			{
				$dealOption[] = JHTML::_('select.option', $deal['id'], $deal['name']);
			}

			$html[] = JHTML::_('select.genericlist', $dealOption, $this->name, null, 'value', 'text', $this->value);

			return implode($html);
		}
	}
}
