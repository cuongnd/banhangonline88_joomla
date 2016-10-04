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

class JFormFieldCmgbtemplate extends JFormField
{
	protected $type = 'Cmgbtemplate';

	protected function getInput()
	{
		$configuration = JModelLegacy::getInstance('Configuration','CMGroupBuyingModel')->getConfiguration();
		$html = array();
		$templates = CMGroupBuyingHelper::getTemplateNames();
		$option = array();

		foreach($templates as $template)
		{
			$option[] = JHTML::_('select.option', $template['name'], $template['name']);
		}

		$html[] = JHTML::_('select.genericlist', $option, $this->name, null, 'value', 'text', $configuration['mobile_template']);

		return implode($html);
	}
}