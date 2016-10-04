<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class JFormFieldCMGBProduct extends JFormField
{
	protected $product = 'CMGBProduct';

	protected function getInput()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, name')
			->from($db->quoteName('#__cmgroupbuying_products'))
			->order('name ASC');
		$db->setQuery($query);
		$products = $db->loadObjectList();

		if ($db->getErrorNum())
		{
			JError::raiseError(500, $db->getErrorMsg());
			return false;
		}

		$html = array();
		$option = array();
		$option[] = JHTML::_('select.option', '' , JText::_('COM_CMGROUPBUYING_SELECT_PRODUCT'));

		if (!empty($products))
		{
			foreach ($products as $product)
			{
				$option[] = JHTML::_('select.option', $product->id , $product->name);
			}
		}

		$html[] = JHTML::_('select.genericlist', $option, $this->name, null, 'value', 'text', $this->value);

		return implode($html);
	}
}