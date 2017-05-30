<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CMGroupBuyingViewAggregationLinks extends JViewLegacy
{
	public function display($tpl = null)
	{
		$aggregatorSites	= JModelLegacy::getInstance('AggregatorSites','CMGroupBuyingModel')->getAggregatorSites();
		$categories			= JModelLegacy::getInstance('Categories','CMGroupBuyingModel')->getCategories();
		$locations			= JModelLegacy::getInstance('Locations','CMGroupBuyingModel')->getLocations();
		$partners			= JModelLegacy::getInstance('Partners','CMGroupBuyingModel')->getPartners();

		$this->assignRef('aggregatorSites', $aggregatorSites);
		$this->assignRef('categories', $categories);
		$this->assignRef('locations', $locations);
		$this->assignRef('partners', $partners);
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_DASHBOARD_AGGREGATION_LINKS'), 'aggregation_link.png');
		JToolBarHelper::back('COM_CMGROUPBUYING_BACK_TO_DASHBOARD', 'index.php?option=com_cmgroupbuying');
	}
}