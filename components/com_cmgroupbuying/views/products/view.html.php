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

class CMGroupBuyingViewProducts extends JViewLegacy
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

		$categoryId = $params->get('category_id', 0);

		$products = JModelLegacy::getInstance('Product', 'CMGroupBuyingModel')->getLimit($categoryId);
		$this->assignRef('products', $products);

		$pageNav = JModelLegacy::getInstance('Product', 'CMGroupBuyingModel')->getPagination($categoryId);
		$this->assignRef('pageNav', $pageNav);

		$pageTitle = JFactory::getDocument()->getTitle();
		$this->assignRef('pageTitle', $pageTitle);

		$imageWidth = $params->get('image_width', 250);
		$imageHeight = $params->get('image_height', 0);

		$this->assignRef('imageWidth', $imageWidth);
		$this->assignRef('imageHeight', $imageHeight);

		$layout = CMGroupBuyingHelperCommon::getLayout();
		$this->_setPath('template', JPATH_SITE . '/components/com_cmgroupbuying/layouts/' . $layout . '/');
		$this->_layout = "products";
		parent::display($tpl);
	}
}