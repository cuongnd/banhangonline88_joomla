<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class CMGroupBuyingModelOrder extends JModelAdmin
{
	public function getTable($type = 'Order', $prefix = 'CMGroupBuyingTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		jimport('joomla.form.form');

		// Get the form.
		$form = $this->loadForm('com_cmgroupbuying.order', 'order', array('control' => 'jform', 'load_data' => $loadData));

		if(empty($form))
		{
			return false;
		}

		return $form;
	}

	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		return $item;
	}
	
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_cmgroupbuying.edit.order.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	public function save($data)
	{
		// Initialise variables;
		// $dispatcher = JDispatcher::getInstance();
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int)$this->getState($this->getName().'.id');
		$isNew = true;
		// Include the content plugins for the on save events.
		//JPluginHelper::importPlugin('content');
		// Allow an exception to be thrown.
		try
		{
			// Load the row if saving an existing record.
			if($pk > 0)
			{
				$table->load($pk);
				$isNew = false;
			}

			// Bind the data.
			if(!$table->bind($data))
			{
				$this->setError($table->getError());
				return false;
			}

			// Check the data.
			if(!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			// Trigger the onContentBeforeSave event.
			/* $result = $dispatcher->trigger($this->event_before_save, array($this->option.'.'.$this->name, &$table, $isNew));

			if(in_array(false, $result, true))
			{
				$this->setError($table->getError());
				return false;
			} */

			//Store the data.
			if(!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}

			//Clean the cache.
			$this->cleanCache();

			// Trigger the onContentAfterSave event.
			// $dispatcher->trigger($this->event_after_save, array($this->option.'.'.$this->name, &$table, $isNew));
		}
		catch(Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		$pkName = $table->getKeyName();

		if(isset($table->$pkName))
		{
			$this->setState($this->getName().'.id', $table->$pkName);
		}

		$this->setState($this->getName().'.new', $isNew);
		return true;
	}

	public function getOrderStatus($orderId)
	{
		$db = JFactory::getDbo();
		$query = "SELECT status FROM #__cmgroupbuying_orders WHERE id = " . $orderId;
		$db->setQuery($query);
		$status = $db->loadResult();
		return $status;
	}

	public function setOrderStatus($orderId , $statusId)
	{
		$db = JFactory::getDbo();
		$query = "UPDATE #__cmgroupbuying_orders SET status = " . $statusId;

		if($statusId == 1)
		{
			$query .= ", paid_date = '" . CMGroupBuyingHelperDateTime::getCurrentDateTime() . "'";
		}
		elseif($statusId == 0)
		{
			 $query .= ", paid_date = ''";
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

	public function delete(&$pks)
	{
		// Initialise variables.
		// $dispatcher = JDispatcher::getInstance();
		$pks = (array) $pks;
		$table = $this->getTable();

		// Include the content plugins for the on delete events.
		JPluginHelper::importPlugin('content');

		// Iterate the items to delete each one.
		foreach($pks as $i => $pk)
		{

			if($table->load($pk))
			{

				if($this->canDelete($table))
				{
					$context = $this->option . '.' . $this->name;

					// Trigger the onContentBeforeDelete event.
					/* $result = $dispatcher->trigger($this->event_before_delete, array($context, $table));

					if(in_array(false, $result, true))
					{
						$this->setError($table->getError());
						return false;
					} */

					if(!$table->delete($pk))
					{
						$this->setError($table->getError());
						return false;
					}
					else // Delete order successfully
					{
						$db = JFactory::getDbo();
						$query  = "SELECT coupon_code FROM #__cmgroupbuying_coupons WHERE order_id = " . $pk;
						$db->setQuery($query);
						$result = $db->loadColumn();

						if(!empty($result))
						{
							JModelAdmin::getInstance('Coupon', 'CMGroupBuyingModel')->delete($result, true);
						}

						JModelAdmin::getInstance('OrderItem', 'CMGroupBuyingModel')->delete($pks);
					}

					// Trigger the onContentAfterDelete event.
					// $dispatcher->trigger($this->event_after_delete, array($context, $table));

				}
				else
				{

					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();

					if($error)
					{
						JError::raiseWarning(500, $error);
						return false;
					}
					else
					{
						JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
						return false;
					}
				}
			}
			else
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}

	public function updateUserInfo($buyerInfo, $receiverInfo, $orderId)
	{
		$buyerInfo = addslashes($buyerInfo);
		$receiverInfo = addslashes($receiverInfo);

		$db = JFactory::getDbo();
		$query = 'UPDATE #__cmgroupbuying_orders SET buyer_info = "' . $buyerInfo . '", friend_info = "' . $receiverInfo . '" WHERE id = ' . $orderId;
		$db->setQuery($query);
		$db->execute();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return true;
	}

	public function getPaidOrdersByDealId($dealId)
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
}