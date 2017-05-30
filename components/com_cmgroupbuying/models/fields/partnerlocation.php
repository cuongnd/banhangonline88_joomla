<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

class JFormFieldPartnerLocation extends JFormField
{
	protected $type = 'PartnerLocation';

	protected function getInput()
	{
		$size = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$readonly = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		$locationElementsJSON = $this->value;
		$locationElementsArray = json_decode($locationElementsJSON);

		if(empty($locationElementsArray))
		{
			$locationElementsJSON = '{"name":"","address":"","latitude":"","longitude":"","phone":""}';
			$locationElementsArray = json_decode($locationElementsJSON);
		}

		$locationElementsJSON = htmlspecialchars($locationElementsJSON, ENT_QUOTES);
		$name = $locationElementsArray->name;

		$hiddenJSON = '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
			. $locationElementsJSON . '"' . $class . $size . $disabled . $readonly . $onchange . $maxLength . '/>';
		$locationName = '<input type="text" id="' . $this->id . '_name"' . ' value="' . $name . '" size="50" />';

		return $hiddenJSON . $locationName;
	}
}