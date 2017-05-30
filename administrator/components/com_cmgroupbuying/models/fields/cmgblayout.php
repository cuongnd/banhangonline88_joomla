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

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/common.php");

class JFormFieldCmgblayout extends JFormField
{
	protected $type = 'Cmgblayout';

	protected function getInput()
	{
		$configuration = JModelLegacy::getInstance('Configuration','CMGroupBuyingModel')
			->getConfiguration('layout');
		$html = array();
		$layoutFolderOption = array();

		foreach(CMGroupBuyingHelperCommon::getFolders('layouts') as $key=>$value)
		{
			$layoutFolderOption[] = JHTML::_('select.option', $value, JText::_($value));
		}

		$html[] = JHTML::_('select.genericlist', $layoutFolderOption,
			$this->name, null, 'value', 'text', $configuration['layout']);

		return implode($html);
	}
}