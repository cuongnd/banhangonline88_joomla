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

class JFormFieldCMOrdering extends JFormField
{
	protected $type = 'CMOrdering';

	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		// Get some field values from the form.
		$id = (int) $this->form->getValue('id');

		// Build the query for the ordering list.
		$query = 'SELECT ordering AS value, name AS text';

		switch($this->form->getName()):
			case 'com_cmgroupbuying.category':
				$query .= ' FROM #__cmgroupbuying_categories';
				break;
			case 'com_cmgroupbuying.location':
				$query .= ' FROM #__cmgroupbuying_locations';
				break;
			case 'com_cmgroupbuying.product':
				$query .= ' FROM #__cmgroupbuying_products';
				break;
			case 'com_cmgroupbuying.deal':
				$query .= ' FROM #__cmgroupbuying_deals';
				break;
			case 'com_cmgroupbuying.freecoupon':
				$query .= ' FROM #__cmgroupbuying_free_coupons';
				break;
			case 'com_cmgroupbuying.payment':
				$query .= ' FROM #__cmgroupbuying_payments';
				break;
			case 'com_cmgroupbuying.aggregatorsite':
				$query .= ' FROM #__cmgroupbuying_aggregator_sites';
				break;
		endswitch;

		$query .= ' ORDER BY ordering';

		// Create a read-only list (no name) with a hidden input to store the value.
		if((string) $this->element['readonly'] == 'true')
		{
			$html[] = JHtml::_('list.ordering', '', $query, trim($attr), $this->value, $id ? 0 : 1);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}

		// Create a regular list.
		else
		{
			$html[] = JHtml::_('list.ordering', $this->name, $query, trim($attr), $this->value, $id ? 0 : 1);
		}

		return implode($html);
	}
}