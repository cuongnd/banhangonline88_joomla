<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldCmgbpagination extends JFormField
{
	protected $type = 'Cmgbpagination';

	protected function getInput()
	{
		$configuration = JModelLegacy::getInstance('Configuration','CMGroupBuyingModel')->getConfiguration();
		$html = array();
		$option = array();

		for($limit = 6; $limit <= 50; $limit++)
		{
			$option[] = JHTML::_('select.option', $limit , $limit);
		}

		$html[] = JHTML::_('select.genericlist', $option, $this->name, null, 'value', 'text', $configuration['pagination_limit']);

		return implode($html);
	}
}