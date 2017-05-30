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

class CMGroupBuyingViewProduct extends JViewLegacy
{
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$jinput = JFactory::getApplication()->input;
		$productId = $jinput->get('id', 0, 'integer');
		$productAlias = $jinput->get('alias', '');

		$product = JModelLegacy::getInstance('Product', 'CMGroupBuyingModel')->getProductByIdAndAlias($productId, $productAlias);

		if (empty($product))
		{
			JError::raiseError(404, JText::_('JERROR_LAYOUT_PAGE_NOT_FOUND'));
		}

		$params = JFactory::getApplication()->getParams();

		$view = JFactory::getApplication()->input->get('view', '', 'word');
		$this->assignRef('view', $view);

		//getLimit($active = true, $upcoming = false, $expired = false, $categoryId = null, $locationString = null, $rss = false, $orderBy = 'ordering ASC', $published = true, $productId = null)
		$deals = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getLimit(true, false, false, null, null, null, false, 'ordering ASC', true, $productId);
		$this->assignRef('deals', $deals);

		$pageNav = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getPagination(null, true, false, false, null, null, null, $productId);
		$this->assignRef('pageNav', $pageNav);

		$pageTitle = JText::sprintf('COM_CMGROUPBUYING_PRODUCT_PAGE_TITLE', $product['name']);
		$this->assignRef('pageTitle', $pageTitle);
		$document->setTitle($pageTitle);

		$document->setMetaData('description', $product['metadesc']);
		$document->setMetadata('keywords', $product['metakey']);

		$noDeal = JText::_('COM_CMGROUPBUYING_PRODUCT_NO_DEAL_FOUND');
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
		$this->_layout = "activedeals";
		parent::display($tpl);
	}
}