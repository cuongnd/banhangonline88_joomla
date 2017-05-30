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

class CMGroupBuyingViewOrders extends JViewLegacy
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();

		if($user->guest)
		{
			$message = JText::_('COM_CMGROUPBUYING_LOGIN_FIRST');
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=orders');
			$redirectUrl = base64_encode($redirectUrl);
			$redirectUrl = JRoute::_("index.php?option=com_easysocial&view=login&return=" . $redirectUrl, false);
			$app->enqueueMessage($message, 'error');
			$app->redirect($redirectUrl);
		}

		$orders = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getLimit($user->id);
		$this->assignRef('orders', $orders);

		$pageNav = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getPagination($user->id);
		$this->assignRef('pageNav', $pageNav);

		$pageTitle = JText::_('COM_CMGROUPBUYING_ORDERS_PAGE_TITLE');
		$this->assignRef('pageTitle', $pageTitle);

		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
		$this->assignRef('configuration', $configuration);

		$layout = CMGroupBuyingHelperCommon::getLayout();
		$this->_setPath('template', JPATH_SITE . '/components/com_cmgroupbuying/layouts/' . $layout . '/');
		$this->_layout = "orders";
		parent::display($tpl);
	}
}