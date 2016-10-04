<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelCoupon extends JModelLegacy
{
	public function setCouponStatus($orderId, $statusId)
	{
		$db = JFactory::getDbo();
		$query = "UPDATE #__cmgroupbuying_coupons SET coupon_status = " . $statusId . " WHERE order_id = " . $orderId;
		$db->setQuery($query);
		$db->execute();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return true;
	}

	public function delete($couponCodeList = null, $call = false)
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;

		if($call == false)
		{
			$couponList = $jinput->post->get('cid', array(), 'array');
		}
		else
		{
			$couponList = $couponCodeList;
		}

		foreach($couponList as $coupon)
		{
			$db = JFactory::getDbo();
			$query = "DELETE FROM #__cmgroupbuying_coupons WHERE coupon_code = '" . $coupon . "'";
			$db->setQuery($query);
			$db->execute();

			if($db->getErrorNum())
			{
				//JError::raiseError(500, implode('<br />', $errors));
				return false;
			}
		}

		if(count($couponList) == 1)
		{
			$message = JText::sprintf('COM_CMGROUPBUYING_COUPON_N_ITEMS_DELETED_1', count($couponList));
			$app->enqueueMessage($message);
		}
		elseif(count($couponList) > 1)
		{
			$message = JText::sprintf('COM_CMGROUPBUYING_COUPON_N_ITEMS_DELETED', count($couponList));
			$app->enqueueMessage($message);
		}
	}

	public function setCouponStatusByCode($coupon, $statusId)
	{
		$db = JFactory::getDbo();
		$query = "UPDATE #__cmgroupbuying_coupons SET coupon_status = " . $statusId . " WHERE coupon_code = '" . $coupon . "'";
		$db->setQuery($query);
		$db->execute();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return true;
	}

	public function getCouponByOrderId($orderId)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_coupons WHERE order_id = " . $orderId;
		$db->setQuery($query);
		$coupons = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $coupons;
	}

	public function getCouponByItemId($itemId)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_coupons WHERE item_id = " . $itemId;
		$db->setQuery($query);
		$coupons = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $coupons;
	}

	public function getCouponByCode($coupon)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_coupons WHERE coupon_code = '" . $coupon . "'";
		$db->setQuery($query);
		$coupon = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $coupon;
	}
}