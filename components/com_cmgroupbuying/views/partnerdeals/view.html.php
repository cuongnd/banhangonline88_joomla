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

class CMGroupBuyingViewPartnerDeals extends JViewLegacy
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

		$view = $jinput->get('view', '', 'word');
		$this->assignRef('view', $view);

		$partnerId = $jinput->get('id', 0 , 'int');
		$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($partnerId);

		//getLimit($active = true, $upcoming = false, $expired = false, $categoryId = null, $partnerId = null, $locationString = null, $rss = false)
		$deals = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getLimit(false, false, false, null, $partnerId, $locationString);
		$this->assignRef('deals', $deals);

		//getPagination($numberOfDeals = null, $active = false, $upcoming = false, $expired = false, $categoryId = null, $partnerId = null, $locationString = '')
		$pageNav = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getPagination(null, false, false, false, null, $partnerId, $locationString);
		$this->assignRef('pageNav', $pageNav);

		$pageTitle = JFactory::getDocument()->getTitle();
		$this->assignRef('pageTitle', $pageTitle);

		$noDeal = JText::sprintf('COM_CMGROUPBUYING_PARTNER_DEALS_NO_DEAL', $partner['name']);
		$this->assignRef('noDeal', $noDeal);

		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('jquery_loading, deal_list_effect, deal_list_slideshow_timing,
				currency_decimals, currency_dec_point, currency_thousands_sep,
				currency_postfix, currency_prefix');
		$this->assignRef('configuration', $configuration);

		if($params->get('deal_image_width'))
			$imageWidth = $params->get('deal_image_width');
		if($params->get('deal_image_height'))
			$imageHeight = $params->get('deal_image_height');
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
		$this->_layout = "partnerdeals"; 
		parent::display($tpl);
	}
}