<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelOrder extends JModelLegacy
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getLimit($buyerId)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_orders WHERE buyer_id = " . $buyerId . " ORDER BY id DESC";
		$db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$orders = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $orders;
	}

	function getPagination($buyerId)
	{
		if(empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$total = $this->count($buyerId);
			$this->_pagination = new JPagination($total, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	function count($buyerId)
	{
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__cmgroupbuying_orders WHERE buyer_id = " . $buyerId;
		$db->setQuery($query);
		$count = $db->loadResult();
		return $count;
	}

	public function getOrderById($orderId)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_orders WHERE id = " . $orderId;
		$db->setQuery($query);
		$order = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $order;
	}

	public function getOrdersByBuyerId($buyerId)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_orders WHERE buyer_id = " . $buyerId . " ORDER BY id ASC";
		$db->setQuery($query);
		$orders = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $orders;
	}

	public function updatePaidOrder($orderId, $transactionInfo)
	{
		$orderStatus = $this->getOrderStatus($orderId);

		if($orderStatus == 0)
		{
			$db = JFactory::getDBO();
			$query = "UPDATE #__cmgroupbuying_orders SET status = 1, transaction_info = " . $db->quote($transactionInfo) . ", paid_date = '" . CMGroupBuyingHelperDateTime::getCurrentDateTime() . "' WHERE id = " . $orderId;
			$db->setQuery($query);
			$db->execute();

			if($db->getErrorNum())
			{
				//JError::raiseError(500, implode('<br />', $errors));
				return false;
			}

			return true;
		}
		else
		{
			return false;
		}
	}

	public function updateLatePaidOrder($orderId, $transactionInfo)
	{
		$orderStatus = $this->getOrderStatus($orderId);

		if($orderStatus == 0)
		{
			$db = JFactory::getDBO();
			$query = "UPDATE #__cmgroupbuying_orders SET status = 2, transaction_info = " . $db->quote($transactionInfo) . ", paid_date = '" . CMGroupBuyingHelperDateTime::getCurrentDateTime() . "' WHERE id = " . $orderId;
			$db->setQuery($query);
			$db->execute();

			if($db->getErrorNum())
			{
				//JError::raiseError(500, implode('<br />', $errors));
				return false;
			}

			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function getOrderStatus($orderId)
	{
		$db = JFactory::getDbo();
		$query = "SELECT status FROM #__cmgroupbuying_orders WHERE id = " . $orderId;
		$db->setQuery($query);
		$orderStatus = $db->loadResult();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $orderStatus;
	}

	public function setOrderStatus($orderId , $statusId)
	{
		$db = JFactory::getDbo();
		$query = "UPDATE #__cmgroupbuying_orders SET status = " . $statusId;

		if($statusId == 1)
		{
			$query .= ", paid_date = '" . CMGroupBuyingHelperDateTime::getCurrentDateTime() . "'";
		}

		$query .= " WHERE id = " . $orderId;
		$db->setQuery($query);
		$db->execute();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return true;
	}

	public function updateUserInfo($buyerInfo, $receiverInfo, $orderId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__cmgroupbuying_orders'))
			->set($db->quoteName('buyer_info')	. ' = ' . $db->quote($buyerInfo))
			->set($db->quoteName('friend_info')	. ' = ' . $db->quote($receiverInfo))
			->where($db->quoteName('id')		. ' = ' . $db->quote($orderId));

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