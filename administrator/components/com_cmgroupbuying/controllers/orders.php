<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/common.php");

class CMGroupBuyingControllerOrders extends JControllerAdmin
{
	protected $text_prefix = 'COM_CMGROUPBUYING_ORDER';

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getModel($name = 'Order', $prefix = 'CMGroupBuyingModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function set_unpaid()
	{
		$jinput = JFactory::getApplication()->input;
		$orderIdList = $jinput->post->get('cid', array(), 'array');

		foreach($orderIdList as $orderId)
		{
			$status = JModelLegacy::getInstance('Order','CMGroupBuyingModel')->getOrderStatus($orderId);

			if($status != 0)
			{
				JModelLegacy::getInstance('Order','CMGroupBuyingModel')->setOrderStatus($orderId , 0);
				JModelLegacy::getInstance('Coupon','CMGroupBuyingModel')->setCouponStatus($orderId , 0);
			}
		}

		$redirectUrl = 'index.php?option=com_cmgroupbuying&view=orders';
		$this->setRedirect($redirectUrl);
		$this->redirect();
	}

	public function set_paid()
	{
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
		$orderIdList = JFactory::getApplication()->input->post->get('cid', array(), 'array');

		foreach($orderIdList as $orderId)
		{
			$status = JModelLegacy::getInstance('Order','CMGroupBuyingModel')->getOrderStatus($orderId);

			if($status != 1)
			{
				$items = JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->getItemsOfOrder($orderId);
				$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);
				$deals = array();

				foreach($items as $item)
				{
					$deal = JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->getDealById($item['deal_id']);
					$deals[$item['id']] = $deal;
					$paidCoupons = CMGroupBuyingHelperDeal::countPaidCoupon($deal['id']);
					$couponsInOrder = CMGroupBuyingHelperCoupon::countCouponInOrder($orderId);
					$coupons = $couponsInOrder + $paidCoupons;

					if($deal['max_coupon'] != "-1" && $deal['max_coupon'] < $coupons)
					{
						$message = JText::sprintf('COM_CMGROUPBUYING_ORDER_SET_PAID_FAILED_LIMIT_COUPON', $orderId, $deal['name'], $deal['max_coupon'], $orderId, $coupons);
						$redirectUrl = 'index.php?option=com_cmgroupbuying&view=orders';
						$type = 'error';
						$this->setRedirect($redirectUrl, $message, $type);
						$this->redirect();
					}
				}

				$order['paid_date'] = CMGroupBuyingHelperDateTime::getCurrentDateTime();
				JModelLegacy::getInstance('Order','CMGroupBuyingModel')->setOrderStatus($orderId , 1);
				JModelLegacy::getInstance('Coupon','CMGroupBuyingModel')->setCouponStatus($orderId , 1);
				CMGroupBuyingHelperMail::sendMailForPaidOrder($order);

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

		$redirectUrl = 'index.php?option=com_cmgroupbuying&view=orders';
		$this->setRedirect($redirectUrl);
		$this->redirect();
	}

	public function set_delivered()
	{
		$orderIdList = JFactory::getApplication()->input->post->get('cid', array(), 'array');

		foreach($orderIdList as $orderId)
		{
			$status = JModelLegacy::getInstance('Order','CMGroupBuyingModel')->getOrderStatus($orderId);

			if($status != 3)
			{
				JModelLegacy::getInstance('Order','CMGroupBuyingModel')->setOrderStatus($orderId , 3);
				$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);
				$items = JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->getItemsOfOrder($order['id']);

				foreach($items as $item)
				{
					CMGroupBuyingHelperMail::sendCoupon($order, $item);
				}
			}
		}

		$redirectUrl = 'index.php?option=com_cmgroupbuying&view=orders';
		$this->setRedirect($redirectUrl);
		$this->redirect();
	}

	public function set_refunded()
	{
		$orderIdList = JFactory::getApplication()->input->post->get('cid', array(), 'array');

		foreach($orderIdList as $orderId)
		{
			$status = JModelLegacy::getInstance('Order','CMGroupBuyingModel')->getOrderStatus($orderId);

			if($status != 4)
			{
				JModelLegacy::getInstance('Order','CMGroupBuyingModel')->setOrderStatus($orderId , 4);
				JModelLegacy::getInstance('Coupon','CMGroupBuyingModel')->setCouponStatus($orderId , 0);

				// Since 1.6.0, point is refunded manually because user's points are decreased right after user buys
				/*
				$configuration  = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
				$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);
				if($order['points'] > 0)
				{
					// Increase user's points
					$points = $order['points'];

					// Descrease user's points
					if($configuration['point_system'] == "aup" && CMGroupBuyingHelperAlphauserpoints::checkInstalled())
					{
						$aupId = CMGroupBuyingHelperAlphauserpoints::getAUPId($order['buyer_id']);
						CMGroupBuyingHelperAlphauserpoints::newPoints($aupId, $points);
					}
					elseif($configuration['point_system'] == "jomsocial" && CMGroupBuyingHelperJomsocial::checkInstalled())
					{
						CMGroupBuyingHelperJomsocial::newPoints($order['buyer_id'], $points);
					}
				}
				*/
			}
		}

		$redirectUrl = 'index.php?option=com_cmgroupbuying&view=orders';
		$this->setRedirect($redirectUrl);
		$this->redirect();
	}
}
