<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class JFormFieldCMGBCategory extends JFormField
{
	protected $product = 'CMGBCategory';

	protected function getInput()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, name')
			->from($db->quoteName('#__cmgroupbuying_categories'))
			->order('name ASC');
		$db->setQuery($query);
		$categories = $db->loadObjectList();

		if ($db->getErrorNum())
		{
			JError::raiseError(500, $db->getErrorMsg());
			return false;
		}

		$html = array();
		$options = array();
		$options[] = JHTML::_('select.option', '' , JText::_('COM_CMGROUPBUYING_SELECT_CATEGORY'));

		if (!empty($categories))
		{
			foreach ($categories as $category)
			{
				$options[] = JHTML::_('select.option', $category->id , $category->name);
			}
		}

		$html[] = JHTML::_('select.genericlist', $options, $this->name, null, 'value', 'text', $this->value, $this->id);

		return implode($html);
	}
}