<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class JFormFieldCMGBPartner extends JFormField
{
	protected $product = 'CMGBPartner';

	protected function getInput()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, name')
			->from($db->quoteName('#__cmgroupbuying_partners'))
			->order('name ASC');
		$db->setQuery($query);
		$partners = $db->loadObjectList();

		if ($db->getErrorNum())
		{
			JError::raiseError(500, $db->getErrorMsg());
			return false;
		}

		$html = array();
		$option = array();
		$option[] = JHTML::_('select.option', '' , JText::_('COM_CMGROUPBUYING_SELECT_PARTNER'));

		if (!empty($partners))
		{
			foreach ($partners as $partner)
			{
				$option[] = JHTML::_('select.option', $partner->id , $partner->name);
			}
		}

		$html[] = JHTML::_('select.genericlist', $option, $this->name, null, 'value', 'text', $this->value, $this->id);

		return implode($html);
	}
}