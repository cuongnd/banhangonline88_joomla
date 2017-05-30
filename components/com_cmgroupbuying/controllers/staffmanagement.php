<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/helpers/common.php';

jimport('joomla.application.component.controllerform');

class CMGroupBuyingControllerStaffManagement extends JControllerForm
{
	public function set_unpaid()
	{
		$jinput = JFactory::getApplication()->input;
		$settings = JModelLegacy::getInstance('Management', 'CMGroupBuyingModel')->getManagementSettings();

		if($settings['staff_change_order_unpaid'] == false)
		{
			$message = JText::_('COM_CMGROUPBUYING_ACCESS_DENIED');
			$redirectUrl = 'index.php';
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$orderId = $jinput->get('id', 0, 'int');
		$message = '';

		if($orderId > 0)
		{
			$status = JModelLegacy::getInstance('Order','CMGroupBuyingModel')->getOrderStatus($orderId);

			if($status != 0)
			{
				JModelLegacy::getInstance('Order','CMGroupBuyingModel')->setOrderStatus($orderId , 0);
				JModelLegacy::getInstance('Coupon','CMGroupBuyingModel')->setCouponStatus($orderId , 0);
				$message = JText::_('COM_CMGROUPBUYING_STAFF_ORDER_TO_UNPAID_MESSAGE');
			}
		}

		$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=order_list&id=' . $orderId);
		$this->setRedirect($redirectUrl, $message);
		$this->redirect();
	}

	public function set_paid()
	{
		$jinput = JFactory::getApplication()->input;
		$settings = JModelLegacy::getInstance('Management', 'CMGroupBuyingModel')->getManagementSettings();

		if($settings['staff_change_order_paid'] == false)
		{
			$message = JText::_('COM_CMGROUPBUYING_ACCESS_DENIED');
			$redirectUrl = 'index.php';
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$configuration  = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
		$orderId = $jinput->get('id', 0, 'int');
		$message = '';
		$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=order_list&id=' . $orderId);

		if($orderId > 0)
		{
			$status = JModelLegacy::getInstance('Order','CMGroupBuyingModel')->getOrderStatus($orderId);
			$order  = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);

			if($status != 1)
			{
				$items  = JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->getItemsOfOrder($orderId);
				$deals  = array();

				// Check for valid numbers of coupons first
				foreach($items as $item)
				{
					$deal = JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->getDealById($item['deal_id']);
					$deal[$item['id']] = $deal;
					$paidCoupons = CMGroupBuyingHelperDeal::countPaidCoupon($deal['id']);
					$couponsInOrder = CMGroupBuyingHelperCoupon::countCouponInOrder($orderId);
					$coupons = $couponsInOrder + $paidCoupons;

					if($deal['max_coupon'] != "-1" && $deal['max_coupon'] < $coupons)
					{
						$message = JText::sprintf('COM_CMGROUPBUYING_STAFF_ORDER_TO_PAID_FAILED_LIMIT_COUPON', $orderId, $deal['name'], $deal['max_coupon'], $orderId, $coupons);
						$this->setRedirect($redirectUrl, $message, 'error');
						$this->redirect();
					}
				}

				// All numbers of coupons are valid
				JModelLegacy::getInstance('Order','CMGroupBuyingModel')->setOrderStatus($orderId , 1);
				JModelLegacy::getInstance('Coupon','CMGroupBuyingModel')->setCouponStatus($orderId , 1);
				CMGroupBuyingHelperMail::sendMailForPaidOrder($order);
				$message = JText::_('COM_CMGROUPBUYING_STAFF_ORDER_TO_PAID_MESSAGE');

				foreach($items as $item)
				{
					if($deals[$item['id']]['tipped'] == 0) // Deal is not tipped yet
					{
						CMGroupBuyingHelperDeal::checkDealForTipping($deal);
					}
					// Deal is tipped already, send coupon
					elseif($deals[$item['id']]['tipped'] == 1)
					{
						CMGroupBuyingHelperMail::sendCoupon($order, $item);
					}
				}

				// Point system - Start
				if($configuration['point_system'] == "aup" || $configuration['point_system'] == "jomsocial")
				{
					if($configuration['deal_referral'] == 1 && $order['referrer'] != '')
					{
						if($configuration['point_system'] == "aup")
						{
							CMGroupBuyingHelperAlphauserpoints::rewardReferral($order['referrer']);
						}
						elseif($configuration['point_system'] == "jomsocial")
						{
							CMGroupBuyingHelperJomsocial::rewardReferral($order['referrer']);
						}
					}

					if($configuration['purchase_bonus'] == 1)
					{
						// Purchase bonus
						if($configuration['point_system'] == "aup" && CMGroupBuyingHelperAlphauserpoints::checkInstalled())
						{
							$aupId = CMGroupBuyingHelperAlphauserpoints::getAUPId($order['buyer_id']);
							CMGroupBuyingHelperAlphauserpoints::newBonusPoints($aupId, $order['value']);
						}
						elseif($configuration['point_system'] == "jomsocial" && CMGroupBuyingHelperJomsocial::checkInstalled())
						{
							CMGroupBuyingHelperJomsocial::newBonusPoints($order['buyer_id']);
						}
					}
				}
				// Point system - End
			}
		}

		$this->setRedirect($redirectUrl, $message);
		$this->redirect();
	}

	public function change_user_info()
	{
		$jinput = JFactory::getApplication()->input;
		$settings = JModelLegacy::getInstance('Management', 'CMGroupBuyingModel')->getManagementSettings();

		if($settings['staff_change_user_info'] == false)
		{
			$message = JText::_('COM_CMGROUPBUYING_ACCESS_DENIED');
			$redirectUrl = 'index.php';
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$orderId = $jinput->get('id', 0, 'int');
		$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);
		$message = '';

		if(empty($order))
		{
			$message = JText::_('COM_CMGROUPBUYING_ORDER_NO_ORDER');
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=order_list');
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$buyerInfo = array();
		$buyerInfo['name'] = $jinput->get('buyer_name', '' ,'string');
		$buyerInfo['first_name'] = $jinput->get('buyer_first_name', '' ,'string');
		$buyerInfo['last_name'] = $jinput->get('buyer_last_name', '' ,'string');
		$buyerInfo['address'] = $jinput->get('buyer_address', '' ,'string');
		$buyerInfo['city'] = $jinput->get('buyer_city', '' ,'string');
		$buyerInfo['state'] = $jinput->get('buyer_state', '' ,'string');
		$buyerInfo['zip_code'] = $jinput->get('buyer_zip_code', '' ,'string');
		$buyerInfo['phone'] = $jinput->get('buyer_phone', '' ,'string');
		$buyerInfo['email']  = $jinput->get('buyer_email', '' ,'string');

		$receiverInfo = array();
		$receiverInfo['full_name'] = $jinput->get('receiver_full_name', '' ,'string');
		$receiverInfo['email'] = $jinput->get('receiver_email', '' ,'string');
		
		$jsonBuyerInfo = json_encode($buyerInfo);
		$jsonReceiverInfo = json_encode($receiverInfo);

		$result = JModelLegacy::getInstance('Order','CMGroupBuyingModel')->updateUserInfo($jsonBuyerInfo, $jsonReceiverInfo, $orderId);

		if($result)
		{
			$message = JText::_('COM_CMGROUPBUYING_STAFF_ORDER_UPDATE_USER_INFO_SUCCESSFULLY');
			$type = 'success';
		}
		else
		{
			$message = JText::_('COM_CMGROUPBUYING_STAFF_ORDER_UPDATE_USER_INFO_FAILED');
			$type = 'error';
		}

		$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=order_list&id=' . $orderId);
		$this->setRedirect($redirectUrl, $message, $type);
		$this->redirect();
	}

	public function send_coupons()
	{
		$jinput = JFactory::getApplication()->input;
		$settings = JModelLegacy::getInstance('Management', 'CMGroupBuyingModel')->getManagementSettings();

		if($settings['staff_send_coupon'] == false)
		{
			$message = JText::_('COM_CMGROUPBUYING_ACCESS_DENIED');
			$redirectUrl = 'index.php';
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$orderId = $jinput->get('id', 0, 'int');
		$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);
		$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=order_list');

		if(empty($order))
		{
			$message = JText::_('COM_CMGROUPBUYING_ORDER_NO_ORDER');
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$items = JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->getItemsOfOrder($order['id']);

		if(empty($items))
		{
			$message = JText::_('COM_CMGROUPBUYING_ORDER_NO_ITEM_FOUND');
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		foreach($items as $item)
		{
			$deal = JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->getDealById($item['deal_id']);

			if(empty($deal))
			{
				$message = JText::sprintf('COM_CMGROUPBUYING_DEAL_ID_NOT_FOUND_MESSAGE', $item['deal_id']);
				$this->setRedirect($redirectUrl, $message, 'error');
				$this->redirect();
			}
		}

		foreach($items as $item)
		{
			CMGroupBuyingHelperMail::sendCoupon($order, $item);
		}

		$this->setRedirect($redirectUrl);
		$this->redirect();
	}

	public function send_coupon()
	{
		$jinput = JFactory::getApplication()->input;
		$settings = JModelLegacy::getInstance('Management', 'CMGroupBuyingModel')->getManagementSettings();

		if($settings['staff_send_coupon'] == false)
		{
			$message = JText::_('COM_CMGROUPBUYING_ACCESS_DENIED');
			$redirectUrl = 'index.php';
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$couponCode = $jinput->get('coupon', 'int');
		$coupon = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getCouponByCouponCode($couponCode);
		$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement&navigation=coupon_list');

		if(empty($coupon))
		{
			$message = JText::_('COM_CMGROUPBUYING_COUPON_NO_COUPON');
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($coupon['order_id']);

		if(empty($order))
		{
			$message = JText::_('COM_CMGROUPBUYING_ORDER_NO_ORDER');
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($coupon['deal_id']);

		if(empty($deal))
		{
			$message = JText::sprintf('COM_CMGROUPBUYING_DEAL_ID_NOT_FOUND_MESSAGE', $coupon['deal_id']);
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		$item = JModelLegacy::getInstance('OrderItem','CMGroupBuyingModel')->getItemById($coupon['item_id']);

		if(empty($item))
		{
			$message = JText::_('COM_CMGROUPBUYING_ORDER_NO_ITEM_FOUND');
			$this->setRedirect($redirectUrl, $message, 'error');
			$this->redirect();
		}

		CMGroupBuyingHelperMail::sendCoupon($order, $item);

		$this->setRedirect($redirectUrl);
		$this->redirect();
	}
}