<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

require_once JPATH_COMPONENT.'/helpers/common.php';

class CMGroupBuyingViewDealmap extends JViewLegacy
{
	function display($tpl = null)
	{
		$params = JFactory::getApplication()->getParams();
		$document = JFactory::getDocument();

		if ($params->get('menu-meta_description'))
		{
			$document->setMetaData('description', $params->get('menu-meta_description'));
		}

		if ($params->get('menu-meta_keywords'))
		{
			$document->setMetaData('keywords', $params->get('menu-meta_keywords'));
		}

		$categories = JModelLegacy::getInstance("Category", "CMGroupBuyingModel")->getCategories();
		$this->assignRef('categories', $categories);

		$configuration  = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
		$this->assignRef('configuration', $configuration);

		if($configuration["geotargeting"] == "maxmind" || $configuration["geotargeting"] == "ipinfodb")
		{
			$coordinate = CMGroupBuyingHelperGeotargeting::getGeoCookie();

			if($coordinate == null) // If there is no cookie, check and create one
			{
				$coordinate = CMGroupBuyingHelperGeotargeting::getUserCoordinate($configuration["geotargeting"]);
				$defaultLatitude = $coordinate['latitude'];
				$defaultLongitude = $coordinate['longitude'];
			}
			else // But if there is a cookie, get the city name and check with available cities in CMGroupBuying
			{
				$location = JModelLegacy::getInstance('Location', 'CMGroupBuyingModel')->getLocationByGeotargetingName($coordinate["city_name"]);

				if(empty($location)) // If there is no city matched, use the default coordinate in CMGroupBuying configuration
				{
					$defaultLatitude = $configuration['deal_map_latitude'];
					$defaultLongitude = $configuration['deal_map_longitude'];
				}
				else
				{
					$defaultLatitude = $coordinate['latitude'];
					$defaultLongitude = $coordinate['longitude'];
				}
			}
		}
		else
		{
			$defaultLatitude = $configuration['deal_map_latitude'];
			$defaultLongitude = $configuration['deal_map_longitude'];
		}

		if($defaultLatitude == '')
		{
			$defaultLatitude = 0;
		}

		if($defaultLongitude == '')
		{
			$defaultLongitude = 0;
		}
		
		$this->assignRef('defaultLatitude', $defaultLatitude);
		$this->assignRef('defaultLongitude', $defaultLongitude);
		$layout = CMGroupBuyingHelperCommon::getLayout();
		$this->_setPath('template', JPATH_SITE . '/components/com_cmgroupbuying/layouts/' . $layout . '/');
		$this->_layout = "dealmap";
		parent::display($tpl);
	}
}
