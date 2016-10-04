<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/common.php");

class CMGroupBuyingViewCoupon extends JViewLegacy
{
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$couponList = $jinput->get('cid', array(), 'array');
		$coupon = $couponList[0];

		if($coupon == '')
		{
			$message = JText::_('COM_CMGROUPBUYING_COUPON_NO_COUPON');
			$redirectUrl = 'index.php?option=com_cmgroupbuying&view=coupons';
			$app->enqueueMessage( $message, 'error');
			$app->redirect($redirectUrl);
		}

		$coupon = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getCouponByCode($coupon);

		if(empty($coupon))
		{
			$message = JText::_('COM_CMGROUPBUYING_COUPON_NO_COUPON');
			$redirectUrl = 'index.php?option=com_cmgroupbuying&view=coupons';
			$app->enqueueMessage( $message, 'error');
			$app->redirect($redirectUrl);
		}

		$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($coupon['order_id']);

		if(empty($order))
		{
			$order['buyer_id'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
			$order['buyer_info'] = '{"first_name":"","last_name":"","address":"","city":"","state":"","zip_code":"","phone":"","email":""}';
			$order['friend_info'] = '{"full_name":"","email":""}';
			$order['points'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
			$order['referrer'] = '';
			$order['payment_name'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
			$order['transaction_info'] = '';
			$order['created_date'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
			$order['expired_date'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
			$order['paid_date'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
			$order['status'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
		}

		$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($coupon['deal_id']);

		if(empty($deal))
		{
			$order['deal_name'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
		}
		else
		{
			$order['deal_name'] = $deal['name'];
		}

		$option = JModelLegacy::getInstance('dealoption','cmgroupbuyingModel')->getOption($coupon['deal_id'], $coupon['option_id']);

		if(empty($option))
		{
			$order['option_name'] = JText::_('COM_CMGROUPBUYING_DATA_NOT_FOUND');
		}
		else
		{
			$order['option_name'] = $option['name'] . " (" . CMGroupBuyingHelperDeal::displayDealPrice($option['price']) . ")";
		}

		$this->assignRef('order', $order);
		$this->assignRef('coupon', $coupon);
		$this->addToolbar($coupon);
		parent::display($tpl);
	}

	protected function addToolbar($coupon)
	{
		JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_COUPON_VIEW_INFO_TITLE'), 'coupon.png');
		JToolBarHelper::back('COM_CMGROUPBUYING_BACK', 'index.php?option=com_cmgroupbuying&view=coupons');

		if($coupon['coupon_status'] == 1)
		{
			JToolBarHelper::divider();
			JToolBarHelper::custom('coupons.set_exchanged', 'publish.png', 'publish.png', 'COM_CMGROUPBUYING_COUPON_SET_EXCHANGED', false);
		}

		if($coupon['coupon_status'] == 2)
		{
			JToolBarHelper::divider();
			JToolBarHelper::custom('coupons.set_waiting', 'unpublish.png', 'unpublish.png', 'COM_CMGROUPBUYING_COUPON_SET_WAITING', false);
		}
	}
}