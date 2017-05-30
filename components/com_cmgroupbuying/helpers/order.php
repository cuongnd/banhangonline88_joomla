<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelperOrder
{
	public static function updatePaidOrder($order, $transactionInfo)
	{
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('point_system, purchase_bonus, deal_referral');

		if($order['status'] == 0)
		{
			$result = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->updatePaidOrder($order['id'], json_encode($transactionInfo));

			if($result)
			{
				$order['paid_date'] = CMGroupBuyingHelperDateTime::getCurrentDateTime();

				CMGroupBuyingHelperMail::sendMailForPaidOrder($order);
				$items = JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->getItemsOfOrder($order['id']);

				foreach($items as $item)
				{
					$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($item['deal_id']);

					if(isset($deal['id']))
					{
						JModelLegacy::getInstance('Coupon','CMGroupBuyingModel')->setCouponStatusByItemId($item['id'], 1);
   
						if($deal['tipped'] == 0)
						{
							CMGroupBuyingHelperDeal::checkDealForTipping($deal);
						}
						elseif($deal['tipped'] == 1)
						{
							CMGroupBuyingHelperMail::sendCoupon($order, $item);
						}
					}
				}

				// Point system - Start
				if($configuration['point_system'] == "aup" || $configuration['point_system'] == "jomsocial")
				{
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

					if($configuration['point_system'] == "aup" || $configuration['point_system'] == "jomsocial")
					{
						if($configuration['deal_referral'] == 1 && $order['referrer'] != '')
						{
							if($configuration['point_system'] == "aup" && CMGroupBuyingHelperAlphauserpoints::checkInstalled())
							{
								CMGroupBuyingHelperAlphauserpoints::rewardReferral($order['referrer']);
							}
							elseif($configuration['point_system'] == "jomsocial" && CMGroupBuyingHelperJomsocial::checkInstalled())
							{
								CMGroupBuyingHelperJomsocial::rewardReferral($order['referrer']);
							}
						}
					}
				}
				// Point system - End

				return true;
			}
			else
			{
				return false;
			}
		}
	}

	public static function updateLatePaidOrder($order, $transactionInfo)
	{
		if($order['status'] == 0)
		{
			$result = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->updateLatePaidOrder($order['id'], json_encode($transactionInfo));

			if($result)
			{
				CMGroupBuyingHelperMail::sendMailForLatePaidOrder($order);
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	public static function generateOrderToken($dealId, $orderId, $orderDate)
	{
		// Solution: use md5 algorithm
		$token = md5($dealId . $orderId . $orderDate);
		return $token;
	}

	public static function checkValidTransaction($order, $currentDateTime)
	{
		$orderExpiredDate = strtotime($order['expired_date']);
		$currentDateTime  = strtotime($currentDateTime);

		if($orderExpiredDate < $currentDateTime)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public static function getOrderByItemId($orderItemId)
	{
		$db = JFactory::getDbo();
		$query = 'SELECT * FROM #__cmgroupbuying_orders WHERE id = (SELECT order_id FROM #__cmgroupbuying_order_items WHERE id = ' . $db->quote($orderItemId) . ')';
		$db->setQuery($query);
		$order = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $order;
	}


	public static function getPaidOrdersByDealId($dealId)
	{
		$db = JFactory::getDbo();
		$query = 'SELECT * FROM #__cmgroupbuying_orders WHERE status = 1 AND id IN (SELECT order_id FROM #__cmgroupbuying_order_items WHERE deal_id = ' . $db->quote($dealId) . ')';
		$db->setQuery($query);
		$orders = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $orders;
	}
}