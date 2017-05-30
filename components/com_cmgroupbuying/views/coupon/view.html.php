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

class CMGroupBuyingViewCoupon extends JViewLegacy
{
	function display($tpl = null)
	{
		$app			= JFactory::getApplication();
		$jinput			= $app->input;
		$token			= $jinput->get('token', '', 'string');
		$couponCode		= $jinput->get('download', '', 'string');
		$tmpl			= $jinput->get('tmpl', '', 'string');
		$configuration	= JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('currency_decimals, currency_dec_point, currency_thousands_sep,
				currency_postfix, currency_prefix, coupon_format');

		if($token != '')
		{
			$orderItem = JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->getItemByToken($token);
			$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($orderItem['deal_id']);
			$order = CMGroupBuyingHelperOrder::getOrderByItemId($orderItem['id']);

			if(!empty($order))
			{
				// If the order is paid and deal is tipped, user is able to view the coupons
				if($deal['tipped'] == 1 && $order['status'] == 1)
				{
					$coupons = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getCouponByItemId($orderItem['id']);
				}
				else
				{
					$coupons = array();
				}

				if(count($coupons) == 0)
				{
					$message = JText::_('COM_CMGROUPBUYING_COUPON_NO_COUPON');
					$redirectUrl = JRoute::_('index.php');
					$app->enqueueMessage($message, 'error');
					$app->redirect($redirectUrl);
				}

				$action = 'list';
				$pageTitle = JText::_('COM_CMGROUPBUYING_COUPON_LIST');
				$this->assignRef('pageTitle', $pageTitle);
				$this->assignRef('coupons', $coupons);
			}
			else
			{
				$message = JText::_('COM_CMGROUPBUYING_COUPON_INVALID_TOKEN');
				$redirectUrl = JRoute::_('index.php');
				$app->enqueueMessage($message, 'error');
				$app->redirect($redirectUrl);
			}
		}
		elseif($couponCode != '' && $tmpl == 'component')
		{
			$coupon = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getCouponByCouponCode($couponCode);

			if(isset($coupon['item_id']))
			{
				$order = CMGroupBuyingHelperOrder::getOrderByItemId($coupon['item_id']);
				$item = JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->getItemById($coupon['item_id']);
				$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($item['deal_id']);

				if($deal['tipped'] != 1)
				{
					$message = JText::_('COM_CMGROUPBUYING_COUPON_NO_COUPON');
					$redirectUrl = JRoute::_('index.php');
					$app->enqueueMessage($message, 'error');
					$app->redirect($redirectUrl);
				}
				else
				{
					$this->assignRef('coupon', $coupon);
					$this->assignRef('order', $order);;
					$this->assignRef('item', $item);
					$this->assignRef('deal', $deal);

					$pageTitle = JText::_('COM_CMGROUPBUYING_COUPON_DOWNLOAD');
					JFactory::getDocument()->setTitle($pageTitle);
					$action = 'download';

					$this->assignRef('coupon', $coupon);
					$this->assignRef('format', $configuration['coupon_format']);
					$this->assignRef('configuration', $configuration);
					$this->assignRef('pageTitle', $pageTitle);
				}
			}
			else
			{
				$message = JText::_('COM_CMGROUPBUYING_COUPON_NO_COUPON');
				$redirectUrl = JRoute::_('index.php');
				$app->enqueueMessage($message, 'error');
				$app->redirect($redirectUrl);
			}
		}
		else
		{
			$message = JText::_('COM_CMGROUPBUYING_COUPON_NO_COUPON');
			$redirectUrl = JRoute::_('index.php');
			$app->enqueueMessage($message, 'error');
			$app->redirect($redirectUrl);
		}

		$this->assignRef('action', $action);
		parent::display($tpl);
	}
}