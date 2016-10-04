<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelperCoupon
{
	public static function generateCouponCode($orderId, $itemId, $dealId, $i)
	{
		// Coupon code structure:
		// $dealId + 1 random letter + 2 random characters + 1 random letter + $i + 
		// 1 random letter + $orderId + 1 random letter
		$couponCode = $dealId;
		$couponCode .= CMGroupBuyingHelperCommon::generateRandomLetter();
		$couponCode .= CMGroupBuyingHelperCommon::generateRandomString(2);
		$couponCode .= CMGroupBuyingHelperCommon::generateRandomLetter();
		$couponCode .= $i;
		$couponCode .= CMGroupBuyingHelperCommon::generateRandomLetter();
		$couponCode .= $orderId;
		$couponCode .= $itemId;
		$couponCode .= CMGroupBuyingHelperCommon::generateRandomLetter();
		$couponCode = strtoupper($couponCode);
		return $couponCode;
	}

	public static function createOrderCoupon($orderId, $itemId, $partnerId, $dealId, $optionId, $userId, $quantity, $expiredDate, $couponStatus)
	{
		for($i = 1; $i <= $quantity; $i++)
		{
			$couponCode = CMGroupBuyingHelperCoupon::generateCouponCode($orderId, $itemId, $dealId, $i);
			JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->insertCoupon($orderId, $itemId, $partnerId, $dealId, $optionId, $userId, $couponCode, $expiredDate, $couponStatus);
		}
	}

	public static function countCouponInOrder($orderId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('COUNT(*)')
			->from($db->quoteName('#__cmgroupbuying_coupons'))
			->where($db->quoteName('order_id') . ' = ' . $db->quote($orderId));

		$db->setQuery($query);
		$count = $db->loadResult();

		return $count;
	}
}