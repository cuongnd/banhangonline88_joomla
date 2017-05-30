<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CMGroupBuyingViewGoogleMaps extends JViewLegacy
{
	public function display($tpl = null)
	{
		$jinput = JFactory::getApplication()->input;
		$latitude = $jinput->get('latitude', 0, 'FLOAT');
		$longitude = $jinput->get('longitude', 0, 'FLOAT');
		$zoom = $jinput->get('zoom', 1, 'INT');
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();

		$this->assignRef('latitude', $latitude);
		$this->assignRef('longitude', $longitude);
		$this->assignRef('zoom', $zoom);
		$this->assignRef('configuration', $configuration);
		
		parent::display($tpl);
	}
}