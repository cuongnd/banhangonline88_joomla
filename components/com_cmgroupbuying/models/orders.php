<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelOrders extends JModelLegacy
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		$app  = JFactory::getApplication();
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$buyerFilter = $app->getUserStateFromRequest('cmgroupbuying.staff_order_buyer_filter', 'staff_order_buyer_filter', '');
		$gatewayFilter = $app->getUserStateFromRequest('cmgroupbuying.staff_order_gateway_filter', 'staff_order_gateway_filter', '');
		$statusFilter = $app->getUserStateFromRequest('cmgroupbuying.staff_order_status_filter', 'staff_order_status_filter', -1);
		$dateFilter = $app->getUserStateFromRequest('cmgroupbuying.staff_order_date_filter', 'staff_order_date_filter', '');
		$fromFilter = $app->getUserStateFromRequest('cmgroupbuying.staff_order_from_filter', 'staff_order_from_filter', '');
		$toFilter = $app->getUserStateFromRequest('cmgroupbuying.staff_order_to_filter', 'staff_order_to_filter', '');
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		$this->setState('staff_order_buyer_filter', $buyerFilter);
		$this->setState('staff_order_status_filter', $statusFilter);
		$this->setState('staff_order_date_filter', $dateFilter);
		$this->setState('staff_order_gateway_filter', $gatewayFilter);
		$this->setState('staff_order_from_filter', $fromFilter);
		$this->setState('staff_order_to_filter', $toFilter);
	}

	function getPagination()
	{
		if(empty($this->_pagination))
		{
			require_once JPATH_COMPONENT.'/helpers/cmpagination.php';
			$total = $this->count();
			$this->_pagination = new CMPagination($total, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	function getLimit()
	{
		$db = JFactory::getDBO();

		$buyerFilter = $this->getState('staff_order_buyer_filter');
		$gatewayFilter = $this->getState('staff_order_gateway_filter');
		$statusFilter = $this->getState('staff_order_status_filter');
		$dateFilter = $this->getState('staff_order_date_filter');
		$fromFilter = $this->getState('staff_order_from_filter');
		$toFilter = $this->getState('staff_order_to_filter');
		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__cmgroupbuying_orders');

		if($buyerFilter != '')
		{
			$db2 = JFactory::getDbo();
			$query2 = $db2->getQuery(true);
			$query2->select('id');
			$query2->from('#__users');
			$query2->where('name LIKE ' . $db2->quote('%' . $buyerFilter . '%') .
					' OR username LIKE ' . $db2->quote('%' . $buyerFilter . '%') .
					' OR email LIKE ' . $db2->quote('%' . $buyerFilter . '%'));
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

				if(strpos($buyerInfo->name, $buyerFilter) !== false ||
						strpos($buyerInfo->first_name, $buyerFilter) !== false ||
						strpos($buyerInfo->last_name, $buyerFilter) !== false ||
						strpos($friendInfo->full_name, $buyerFilter) !== false) {
					$temp[] = $order['id'];
				}
			}

			$orderIdString = implode(',', $temp);

			if($userIdString == '')
				$userIdString = -1;
			if($orderIdString == '')
				$orderIdString = -1;

			$query->where('(buyer_id IN (' .  $userIdString . ') OR id IN (' . $orderIdString . '))');
		}

		if($statusFilter >= 0)
			$query->where('status = ' . $db->quote($statusFilter));

		if($gatewayFilter != '')
			$query->where('payment_id = ' . $db->quote($gatewayFilter));

		if($dateFilter == 'created_date' || $dateFilter == 'paid_date')
		{
			$fromFilter = date('Y-m-d', strtotime($fromFilter)) . ' 00:00:00';
			$toFilter   = date('Y-m-d', strtotime($toFilter)). ' 23:59:59';
		}

		if($dateFilter == 'created_date')
		{
			$query->where('created_date >= ' . $db->quote($fromFilter));
			$query->where('created_date <= ' . $db->quote($toFilter ));
		}
		elseif($dateFilter == 'paid_date')
		{
			$query->where('paid_date >= ' .$db->quote($fromFilter));
			$query->where('paid_date <= ' . $db->quote($toFilter));
		}

		$query->order('id DESC');
		$db->setQuery($query, $limitstart, $limit);

		$orders = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $orders;
	}

	function count()
	{
		$db = JFactory::getDBO();

		$buyerFilter = $this->getState('staff_order_buyer_filter');
		$gatewayFilter  = $this->getState('staff_order_gateway_filter');
		$statusFilter = $this->getState('staff_order_status_filter');
		$dateFilter = $this->getState('staff_order_date_filter');
		$fromFilter = $this->getState('staff_order_from_filter');
		$toFilter = $this->getState('staff_order_to_filter');

		$query = $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__cmgroupbuying_orders');

		if($buyerFilter != '')
		{
			$db2 = JFactory::getDbo();
			$query2 = $db2->getQuery(true);
			$query2->select('id');
			$query2->from('#__users');
			$query2->where('name LIKE ' . $db2->quote('%' . $buyerFilter . '%') .
					' OR username LIKE ' . $db2->quote('%' . $buyerFilter . '%') .
					' OR email LIKE ' . $db2->quote('%' . $buyerFilter . '%'));
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
				$buyerInfo  = json_decode($order['buyer_info']);
				$friendInfo = json_decode($order['friend_info']);

				if(strpos($buyerInfo->name, $buyerFilter) !== false ||
						strpos($buyerInfo->first_name, $buyerFilter) !== false ||
						strpos($buyerInfo->last_name, $buyerFilter) !== false ||
						strpos($friendInfo->full_name, $buyerFilter) !== false) {
					$temp[] = $order['id'];
				}
			}

			$orderIdString = implode(',', $temp);

			if($userIdString == '')
				$userIdString = -1;
			if($orderIdString == '')
				$orderIdString = -1;

			$query->where('(buyer_id IN (' .  $userIdString . ') OR id IN (' . $orderIdString . '))');
		}

		if($statusFilter >= 0)
			$query->where('status = ' . $db->quote($statusFilter));

		if($gatewayFilter != '')
			$query->where('payment_id = ' . $db->quote($gatewayFilter));

		if($dateFilter == 'created_date' || $dateFilter == 'paid_date')
		{
			$fromFilter = date('Y-m-d', strtotime($fromFilter)) . ' 00:00:00';
			$toFilter   = date('Y-m-d', strtotime($toFilter)). ' 23:59:59';
		}

		if($dateFilter == 'created_date')
		{
			$query->where('created_date >= ' . $db->quote($fromFilter . ' 00:00:00'));
			$query->where('created_date <= ' . $db->quote($toFilter . ' 23:59:59'));
		}
		elseif($dateFilter == 'paid_date')
		{
			$query->where('paid_date >= ' .$db->quote($fromFilter . ' 00:00:00'));
			$query->where('paid_date <= ' . $db->quote($toFilter . ' 23:59:59'));
		}

		$db->setQuery($query);

		$count = $db->loadResult();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $count;
	}

}