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

class CMGroupBuyingViewCheckOut extends JViewLegacy
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();

		// Check for valid cart
		$session = JFactory::getSession();
		$cart = $session->get('cart', array(), 'CMGroupBuying');

		if(empty($cart) || empty($cart['items']))
		{
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=cart');
			$message = JText::_('COM_CMGROUPBUYING_CART_IS_EMPTY');
			$app->enqueueMessage($message, 'error');
			$app->redirect($redirectUrl);
		}

		if($cart['order_id'] <= 0)
		{
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=cart');
			$message = JText::_('COM_CMGROUPBUYING_CHECK_OUT_ORDER_ID_NOT_FOUND');
			$app->enqueueMessage($message, 'error');
			$app->redirect($redirectUrl);
		}

		$order = JModelLegacy::getInstance('Order','CMGroupBuyingModel')->getOrderById($cart['order_id']);

		if(empty($order))
		{
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=cart');
			$message = JText::_('COM_CMGROUPBUYING_CHECK_OUT_ORDER_ID_NOT_FOUND');
			$app->enqueueMessage($message, 'error');
			$app->redirect($redirectUrl);
		}

		jimport('joomla.plugin.helper');
		JPluginHelper::importPlugin('cmpayment');
		$result = JFactory::getApplication()->triggerEvent('onCMPaymentNew', array($cart, $order));

		if(empty($result))
		{
			$message = JText::_('COM_CMGROUPBUYING_NEW_ORDER_FAILED');
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=cart');
			$app->enqueueMessage($message, 'error');
			$app->redirect($redirectUrl);
		}

		$paymentForm = '';

		foreach($result as $r)
		{
			if($r === false)
				continue;

			$paymentForm = $r;
		}

		$this->assignRef('paymentForm', $paymentForm);

		$pageTitle = JText::_('COM_CMGROUPBUYING_CHECK_OUT_PAGE_TITLE');
		$this->assignRef('pageTitle', $pageTitle);

		$document = JFactory::getDocument();
		$document->setTitle($pageTitle);

		$layout = CMGroupBuyingHelperCommon::getLayout();
		$this->_setPath('template', JPATH_SITE . '/components/com_cmgroupbuying/layouts/' . $layout . '/');
		$this->_layout = "checkout";
		parent::display($tpl);
	}
}