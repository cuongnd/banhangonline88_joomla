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

class CMGroupBuyingViewUpcomingFreeCoupons extends JViewLegacy
{
	function display($tpl = null)
	{
		$jinput = JFactory::getApplication()->input;
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

		if($params->get('location_id'))
		{
			$locationId = $params->get('location_id');
		}
		else
		{
			$locationId = JFactory::getApplication()->input->cookie->get('locationSubscription', '', 'int');
		}

		$locationString = null;

		if($locationId > 0)
		{
			$locationString = CMGroupBuyingHelperDeal::getDealsInLocation($locationId);
		}

		$view = $jinput->get('view', '', 'word');
		$this->assignRef('view', $view);

		$coupons = JModelLegacy::getInstance('FreeCoupon', 'CMGroupBuyingModel')->getLimit($locationString, 'upcoming');
		$this->assignRef('coupons', $coupons);

		$pageNav = JModelLegacy::getInstance('FreeCoupon', 'CMGroupBuyingModel')->getPagination(null, $locationString, 'upcoming');
		$this->assignRef('pageNav', $pageNav);

		$pageTitle = JFactory::getDocument()->getTitle();
		$this->assignRef('pageTitle', $pageTitle);

		$noCoupon = JText::_('COM_CMGROUPBUYING_FREE_COUPONS_NO_COUPON');
		$this->assignRef('noCoupon', $noCoupon);

		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('jquery_loading, deal_list_effect, deal_list_slideshow_timing,
				currency_decimals, currency_dec_point, currency_thousands_sep,
				currency_postfix, currency_prefix');
		$this->assignRef('configuration', $configuration);
		// Default values
		$imageWidth = 280;
		$imageHeight = 150;
		$numOfColumns = 3;
		$rowSpace = 10;
		$colSpace = 10;

		if($params->get('coupon_image_width'))
			$imageWidth = $params->get('coupon_image_width');
		if($params->get('coupon_image_height'))
			$imageHeight = $params->get('coupon_image_height');
		if($params->get('number_of_columns'))
			$numOfColumns = $params->get('number_of_columns');
		if($params->get('row_space'))
			$rowSpace = $params->get('row_space');
		if($params->get('column_space'))
			$colSpace = $params->get('column_space');

		$this->assignRef('imageWidth', $imageWidth);
		$this->assignRef('imageHeight', $imageHeight);
		$this->assignRef('numOfColumns', $numOfColumns);
		$this->assignRef('rowSpace', $rowSpace);
		$this->assignRef('colSpace', $colSpace);

		$layout = CMGroupBuyingHelperCommon::getLayout();
		$this->_setPath('template', JPATH_SITE . '/components/com_cmgroupbuying/layouts/' . $layout . '/');
		$this->_layout = "upcomingfreecoupons";
		parent::display($tpl);
	}
}