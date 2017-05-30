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
	public function getItemById($itemId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_order_items'))
			->where($db->quoteName('id') . ' = ' . $db->quote($itemId));

		$db->setQuery($query);
		$item = $db->loadAssoc();
		return $item;
	}

	public function getItemsOfOrder($orderId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_order_items'))
			->where($db->quoteName('order_id') . ' = ' . $db->quote($orderId));

		$db->setQuery($query);
		$items = $db->loadAssocList();
		return $items;
	}

	public function getItemByToken($token)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_order_items'))
			->where($db->quoteName('token') . ' = ' . $db->quote($token));

		$db->setQuery($query);
		$item = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $item;
	}

	public function updateToken($itemId, $token)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__cmgroupbuying_order_items'))
			->set($db->quoteName('token') . ' = ' . $db->quote($token))
			->where($db->quoteName('id') . ' = ' . $db->quote($itemId));

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