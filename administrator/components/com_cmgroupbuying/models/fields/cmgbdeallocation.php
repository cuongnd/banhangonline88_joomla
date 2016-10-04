<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class JFormFieldCMGBDealLocation extends JFormField
{
	protected $product = 'CMGBDealLocation';

	protected function getInput()
	{
		$locationsOfDeal = array();
		$dealId = $this->form->getValue('id', 0);

		if($dealId > 0)
		{
			$locationsOfDeal = JModelLegacy::getInstance('Deallocation','CMGroupBuyingModel')->getLocations($dealId);
		}

		if(!empty($locationsOfDeal))
		{
			$this->value = $locationsOfDeal;
		}

		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1'|| (string) $this->disabled == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
		}
		else
		// Create a regular list.
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}

		return implode($html);
	}

	protected function getOptions()
	{
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, name')
			->from($db->quoteName('#__cmgroupbuying_locations'))
			->order('name ASC');
		$db->setQuery($query);
		$locations = $db->loadObjectList();

		if ($db->getErrorNum())
		{
			JError::raiseError(500, $db->getErrorMsg());
			return false;
		}

		// Build the options list from the list of folders.
		if (is_array($locations))
		{
			foreach ($locations as $location)
			{
				$options[] = JHtml::_('select.option', $location->id, $location->name);
			}
		}

		return $options;
	}
}