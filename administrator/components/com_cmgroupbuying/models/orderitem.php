<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelOrderItem extends JModelLegacy
{
	public function getItemsOfOrder($orderId)
	{
		$db = JFactory::getDbo();
		$query = 'SELECT * FROM #__cmgroupbuying_order_items WHERE order_id = ' . $orderId;
		$db->setQuery($query);
		$items = $db->loadAssocList();
		return $items;
	}

	public function getItemByToken($token)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_order_items WHERE token = '$token'";
		$db->setQuery($query);
		$item = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $item;
	}

	public function getItemForReport($dealId)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_order_items WHERE deal_id = " . $dealId;
		$db->setQuery($query);
		$items = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $items;
	}

	public function getOrdersByDealId($dealId)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_orders WHERE deal_id = " . $dealId;
		$db->setQuery($query);
		$orders = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $orders;
	}

	public function getPaidItemsByDealId($dealId)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_orders WHERE status = 1 AND deal_id = " . $dealId;
		$db->setQuery($query);
		$orders = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $orders;
	}

	public function delete(&$pks)
	{
		$db = JFactory::getDbo();

		foreach($pks as $orderId)
		{
			$query = 'DELETE FROM #__cmgroupbuying_order_items WHERE order_id =' . $orderId;
			$db->setQuery($query);
			$db->execute();
		}

		if($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
			return false;
		}

		return true;
	}
}