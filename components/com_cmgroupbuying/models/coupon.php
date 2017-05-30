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
	public function __construct($config = array())
	{
		parent::__construct($config);
		$jinput = JFactory::getApplication()->input;
		$app = JFactory::getApplication();
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $jinput->get('limitstart', 0, 'int');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$dealFilter = $app->getUserStateFromRequest('cmgroupbuying.partner_deal_filter', 'partner_deal_filter', 0);
		$statusFilter = $app->getUserStateFromRequest('cmgroupbuying.partner_status_filter', 'partner_status_filter', '-1');
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		$this->setState('partner_deal_filter', $dealFilter);
		$this->setState('partner_status_filter', $statusFilter);

		// Special stuff for Staff Area since 2.2.0
		$view = $jinput->get('view', '', 'word');

		if($view == 'staffmanagement')
		{
			$staffCodeFilter = $app->getUserStateFromRequest('cmgroupbuying.staff_coupon_code_filter', 'staff_coupon_code_filter', '');
			$staffBuyerFilter = $app->getUserStateFromRequest('cmgroupbuying.staff_coupon_buyer_filter', 'staff_coupon_buyer_filter', '');
			$staffDealFilter = $app->getUserStateFromRequest('cmgroupbuying.staff_coupon_deal_filter', 'staff_coupon_deal_filter', '');
			$staffStatusFilter = $app->getUserStateFromRequest('cmgroupbuying.staff_coupon_status_filter', 'staff_coupon_status_filter', '-1');
			$this->setState('staff_coupon_code_filter', $staffCodeFilter);
			$this->setState('staff_coupon_buyer_filter', $staffBuyerFilter);
			$this->setState('staff_coupon_deal_filter', $staffDealFilter);
			$this->setState('staff_coupon_status_filter', $staffStatusFilter);
		}
	}

	function getPagination($partnerId = null)
	{
		if(empty($this->_pagination))
		{
			require_once JPATH_COMPONENT.'/helpers/cmpagination.php';
			$total = $this->count($partnerId);

			$this->_pagination = new CMPagination($total, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	function getLimit($partnerId = null)
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__cmgroupbuying_coupons');

		// Special stuff for Staff Area since 2.2.0
		$view = JFactory::getApplication()->input->get('view', '', 'word');

		if($view == 'staffmanagement')
		{
			$staffCodeFilter = $this->getState('staff_coupon_code_filter');
			$staffBuyerFilter = $this->getState('staff_coupon_buyer_filter');
			$staffDealFilter = $this->getState('staff_coupon_deal_filter');
			$staffStatusFilter = $this->getState('staff_coupon_status_filter');

			if($staffCodeFilter != '')
				$query->where('coupon_code LIKE ' . $db->quote('%' . $staffCodeFilter . '%'));

			if($staffBuyerFilter != '')
			{
				$db2 = JFactory::getDbo();
				$query2 = $db2->getQuery(true);
				$query2->select('id');
				$query2->from('#__users');
				$query2->where('name LIKE ' . $db2->quote('%' . $staffBuyerFilter . '%') .
						' OR username LIKE ' . $db2->quote('%' . $staffBuyerFilter . '%') .
						' OR email LIKE ' . $db2->quote('%' . $staffBuyerFilter . '%'));
				$db2->setQuery($query2);
				$userIds = $db2->loadAssocList();
				$temp = array();

				foreach($userIds as $userId)
				{
					$temp[] = $userId['id'];
				}
				$userIdString = implode(',', $temp);

				$query2->clear();
				$query2->select('id,buyer_info,friend_info');
				$query2->from('#__cmgroupbuying_orders');
				$db2->setQuery($query2);
				$orders = $db2->loadAssocList();
				$temp = array();

				foreach($orders as $order)
				{
					$buyerInfo = json_decode($order['buyer_info']);
					$friendInfo = json_decode($order['friend_info']);
					if(strpos($buyerInfo->name, $staffBuyerFilter) !== false ||
							strpos($buyerInfo->first_name, $staffBuyerFilter) !== false ||
							strpos($buyerInfo->last_name, $staffBuyerFilter) !== false ||
							strpos($friendInfo->full_name, $staffBuyerFilter) !== false) {
						$temp[] = $order['id'];
					}
				}

				$orderIdString = implode(',', $temp);

				if($userIdString == '')
					$userIdString = -1;
				if($orderIdString == '')
					$orderIdString = -1;

				$query->where('(user_id IN (' .  $userIdString . ') OR order_id IN (' . $orderIdString . '))');
			}

			if($staffDealFilter != '')
			{
				$db3 = JFactory::getDbo();
				$query3 = $db3->getQuery(true);
				$query3->select('id');
				$query3->from('#__cmgroupbuying_deals');
				$query3->where('name LIKE ' . $db3->quote('%' . $staffDealFilter . '%'));
				$db3->setQuery($query3);
				$dealIds = $db3->loadAssocList();
				$temp = array();

				foreach($dealIds as $dealId)
				{
					$temp[] = $dealId['id'];
				}

				$dealIdString = implode(',', $temp);
				$query->where('(deal_id IN (' .  $dealIdString . '))');
			}

			if($staffStatusFilter >= 0)
				$query->where('coupon_status = ' . $db->quote($staffStatusFilter));
		}
		else
		{
			$dealFilter = $this->getState('partner_deal_filter');
			$statusFilter = $this->getState('partner_status_filter');

			if($dealFilter > 0)
				$query->where('deal_id = ' . $db->quote($dealFilter));

			if($statusFilter >= 0)
				$query->where('coupon_status = ' . $db->quote($statusFilter));

			if(is_numeric($partnerId))
				$query->where('partner_id = ' . $db->quote($partnerId));
		}

		$query->order('order_id DESC');
		$db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));

		$coupons = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $coupons;
	}

	function count($partnerId = null)
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__cmgroupbuying_coupons');

		// Special stuff for Staff Area since 2.2.0
		$view = JFactory::getApplication()->input->get('view', '', 'word');

		if($view == 'staffmanagement')
		{
			$staffCodeFilter = $this->getState('staff_coupon_code_filter');
			$staffBuyerFilter = $this->getState('staff_coupon_buyer_filter');
			$staffDealFilter = $this->getState('staff_coupon_deal_filter');
			$staffStatusFilter = $this->getState('staff_coupon_status_filter');

			if($staffCodeFilter != '')
				$query->where('coupon_code LIKE ' . $db->quote('%' . $staffCodeFilter . '%'));

			if($staffBuyerFilter != '')
			{
				$db2 = JFactory::getDbo();
				$query2 = $db2->getQuery(true);
				$query2->select('id');
				$query2->from('#__users');
				$query2->where('name LIKE ' . $db2->quote('%' . $staffBuyerFilter . '%') .
						' OR username LIKE ' . $db2->quote('%' . $staffBuyerFilter . '%') .
						' OR email LIKE ' . $db2->quote('%' . $staffBuyerFilter . '%'));
				$db2->setQuery($query2);
				$userIds = $db2->loadAssocList();
				$temp = array();

				foreach($userIds as $userId)
				{
					$temp[] = $userId['id'];
				}

				$userIdString = implode(',', $temp);

				$query2->clear();
				$query2->select('id,buyer_info,friend_info');
				$query2->from('#__cmgroupbuying_orders');
				$db2->setQuery($query2);
				$orders = $db2->loadAssocList();
				$temp   = array();

				foreach($orders as $order)
				{
					$buyerInfo = json_decode($order['buyer_info']);
					$friendInfo = json_decode($order['friend_info']);
					if(strpos($buyerInfo->name, $staffBuyerFilter) !== false ||
							strpos($buyerInfo->first_name, $staffBuyerFilter) !== false ||
							strpos($buyerInfo->last_name, $staffBuyerFilter) !== false ||
							strpos($friendInfo->full_name, $staffBuyerFilter) !== false) {
						$temp[] = $order['id'];
					}
				}

				$orderIdString = implode(',', $temp);

				if($userIdString == '')
					$userIdString = -1;
				if($orderIdString == '')
					$orderIdString = -1;

				$query->where('(user_id IN (' .  $userIdString . ') OR order_id IN (' . $orderIdString . '))');
			}

			if($staffDealFilter != '')
			{
				$db3 = JFactory::getDbo();
				$query3 = $db3->getQuery(true);
				$query3->select('id');
				$query3->from('#__cmgroupbuying_deals');
				$query3->where('name LIKE ' . $db3->quote('%' . $staffDealFilter . '%'));
				$db3->setQuery($query3);
				$dealIds = $db3->loadAssocList();
				$temp = array();

				foreach($dealIds as $dealId)
				{
					$temp[] = $dealId['id'];
				}

				$dealIdString = implode(',', $temp);
				$query->where('(deal_id IN (' .  $dealIdString . '))');
			}

			if($staffStatusFilter >= 0)
				$query->where('coupon_status = ' . $db->quote($staffStatusFilter));
		}
		else
		{
			$dealFilter = $this->getState('partner_deal_filter');
			$statusFilter = $this->getState('partner_status_filter');

			if($dealFilter > 0)
				$query->where('deal_id = ' . $db->quote($dealFilter));

			if($statusFilter >= 0)
				$query->where('coupon_status = ' . $db->quote($statusFilter));

			if(is_numeric($partnerId))
				$query->where('partner_id = ' . $db->quote($partnerId));
		}

		$db->setQuery($query);

		$count = $db->loadResult();
		return $count;
	}

	public function insertCoupon($orderId, $itemId, $partnerId, $dealId, $optionId, $userId, $couponCode, $expiredDate, $couponStatus)
	{
		$db = $this->getDbo();
		$__data = new stdClass();
		$__data->order_id = $orderId;
		$__data->item_id = $itemId;
		$__data->partner_id = $partnerId;
		$__data->deal_id = $dealId;
		$__data->option_id = $optionId;
		$__data->user_id = $userId;
		$__data->coupon_code = $couponCode;
		$__data->expired_date = $expiredDate;
		$__data->coupon_status = $couponStatus;

		$db->insertObject('#__cmgroupbuying_coupons', $__data);

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
	}

	public function getCouponByItemId($orderItemId)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_coupons WHERE item_id = " . $orderItemId;
		$db->setQuery($query);
		$coupons = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $coupons;
	}

	public function getCouponByCouponCode($couponCode)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_coupons WHERE coupon_code = " . $db->quote($couponCode);
		$db->setQuery($query);
		$coupon = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $coupon;
	}

	public function setCouponStatusByItemId($itemId, $statusId)
	{
		$db = JFactory::getDBO();
		$query = "UPDATE #__cmgroupbuying_coupons SET coupon_status = " . $statusId . " WHERE item_id = " . $itemId;
		$db->setQuery($query);
		$db->execute();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return true;
	}

	public function setCouponStatusByCouponCode($couponCode, $statusId)
	{
		$db = JFactory::getDBO();
		$query = "UPDATE #__cmgroupbuying_coupons SET coupon_status = " . $statusId . " WHERE coupon_code = '$couponCode'";
		$db->setQuery($query);
		$db->execute();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return true;
	}

	public function getCouponOfPartner($couponCode, $partnerId)
	{
		$db = JFactory::getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_coupons WHERE coupon_code = '" . $couponCode . "' AND partner_id = " . $partnerId;
		$db->setQuery($query);
		$coupon = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $coupon;
	}

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
}
?>