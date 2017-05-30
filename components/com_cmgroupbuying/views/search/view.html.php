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

class CMGroupBuyingViewSearch extends JViewLegacy
{
	function display($tpl = null)
	{
		$jinput = JFactory::getApplication()->input;

		// Slashes cause errors, <> get stripped anyway later on. # causes problems.
		$badchars = array('#', '>', '<', '\\');
		$keyword = trim(str_replace($badchars, '', $jinput->getString('keyword', null, 'get')));

		// If searchword enclosed in quotes, strip quotes.
		if (substr($keyword, 0, 1) == '"' && substr($keyword, -1) == '"')
		{
			$keyword = substr($keyword, 1, -1);
		}

		if (substr($keyword, 0, 1) == "'" && substr($keyword, -1) == "'")
		{
			$keyword = substr($keyword, 1, -1);
		}

		$this->assignRef('keyword', $keyword);

		$locationId = $jinput->get('location_id', 0, 'int');
		$this->assignRef('locationId', $locationId);

		$categoryId = $jinput->get('category_id', 0, 'int');
		$this->assignRef('categoryId', $categoryId);

		$document = JFactory::getDocument();

		$items = JModelLegacy::getInstance('Search', 'CMGroupBuyingModel')->getLimit($keyword, $locationId, $categoryId);
		$this->assignRef('items', $items);

		$pageNav = JModelLegacy::getInstance('Search', 'CMGroupBuyingModel')->getPagination($keyword, $locationId, $categoryId);
		$this->assignRef('pageNav', $pageNav);

		if (!empty($keyword))
		{
			$pageTitle = JText::sprintf('COM_CMGROUPBUYING_SEARCH_FOR_TITLE', $keyword);
		}
		else
		{
			$pageTitle = JText::_('COM_CMGROUPBUYING_SEARCH_PAGE_TITLE');
		}

		$this->assignRef('pageTitle', $pageTitle);
		$document->setTitle(JText::_('COM_CMGROUPBUYING_SEARCH_PAGE_TITLE'));

		$noDeal = JText::_('COM_CMGROUPBUYING_SEARCH_NO_RESULT');
		$this->assignRef('noDeal', $noDeal);

		$locations = JModelLegacy::getInstance('Location', 'CMGroupBuyingModel')->getPublishedLocations();
		$locationList = array();
		$locationList[] = array('id' => 0, 'name' => JText::_('COM_CMGROUPBUYING_SEARCH_ALL_LOCATIONS'));

		if (!empty($locations))
		{
			foreach ($locations as $location)
			{
				$locationList[] = array('id' => $location['id'], 'name' => $location['name']);
			}
		}

		$this->assignRef('locationList', $locationList);

		$categories = JModelLegacy::getInstance('Category', 'CMGroupBuyingModel')->getPublishedCategories();
		$categoryList = array();
		$categoryList[] = array('id' => 0, 'name' => JText::_('COM_CMGROUPBUYING_SEARCH_ALL_CATEGORIES'));

		if (!empty($categories))
		{
			foreach ($categories as $category)
			{
				$categoryList[] = array('id' => $category['id'], 'name' => $category['name']);
			}
		}

		$this->assignRef('categoryList', $categoryList);

		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('jquery_loading, deal_list_effect, deal_list_slideshow_timing,
				currency_decimals, currency_dec_point, currency_thousands_sep,
				currency_postfix, currency_prefix');
		$this->assignRef('configuration', $configuration);

		$layout = CMGroupBuyingHelperCommon::getLayout();
		$this->_setPath('template', JPATH_SITE . '/components/com_cmgroupbuying/layouts/' . $layout . '/');
		$this->_layout = 'search'; 
		parent::display($tpl);
	}
}