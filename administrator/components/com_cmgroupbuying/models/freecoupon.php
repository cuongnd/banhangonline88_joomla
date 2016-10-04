<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class CMGroupBuyingModelFreeCoupon extends JModelAdmin
{
	public function getTable($type = 'FreeCoupon', $prefix = 'CMGroupBuyingTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		jimport('joomla.form.form');

		// Get the form.
		$form = $this->loadForm('com_cmgroupbuying.freecoupon', 'freecoupon', array('control' => 'jform', 'load_data' => $loadData));

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
		$data = JFactory::getApplication()->getUserState('com_cmgroupbuying.edit.freecoupon.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	public function save($data)
	{
		// Initialise variables;
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int)$this->getState($this->getName().'.id');
		$isNew = true;

		// Alter the title for save as copy
		if(JFactory::getApplication()->input->get('task', '', 'word') == 'save2copy')
		{
			list($title, $alias) = $this->cmGenerateNewTitle($data['alias'], $data['name']);
			$data['name'] = $title;
			$data['alias'] = $alias;
			$data['published'] = 0;
		}

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

			// Store the data.
			if(!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}

			// Clean the cache.
			$this->cleanCache();
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

		// Save the coupon succesfully
		$couponId = $table->id;
		$data = $_POST['jform'];
		$db = JFactory::getDbo();

		// Now save its location
		$locationIdList = $data['location_id'];

		$query = "DELETE FROM #__cmgroupbuying_free_coupon_location WHERE coupon_id = $couponId";
		$db->setQuery($query);
		$db->execute();

		foreach($locationIdList as $locationId)
		{
			$query = "INSERT INTO #__cmgroupbuying_free_coupon_location VALUES ($couponId, $locationId)";
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}

	protected function cmGenerateNewTitle($alias, $title)
	{
		// Alter the title & alias
		$table = $this->getTable();

		while($table->load(array('alias' => $alias)))
		{
			$title = JString::increment($title);
			$alias = JString::increment($alias, 'dash');
		}

		return array($title, $alias);
	}

	public function getApprovedStatus($couponId)
	{
		$db = JFactory::getDbo();
		$query = "SELECT approved FROM #__cmgroupbuying_free_coupons WHERE id = " . $couponId;
		$db->setQuery($query);
		$status = $db->loadResult();
		return $status;
	}

	public function setApprovedStatus($couponId, $status)
	{
		$db = JFactory::getDbo();
		$query = "UPDATE #__cmgroupbuying_free_coupons SET approved = " . $status . ", published = " . $status . " WHERE id = " . $couponId;
		$db->setQuery($query);
		$db->execute();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return true;
	}

	public function getFreeCouponById($couponId)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_free_coupons WHERE id = " . $couponId;
		$db->setQuery($query);
		$coupon = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $coupon;
	}

	public function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if(empty($pks))
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_NO_ITEM_SELECTED'));
			return false;
		}

		$table = $this->getTable('FreeCoupon', 'CMGroupBuyingTable');

		try
		{
			$db = $this->getDbo();

			if($value == 1)
			{
				$db->setQuery(
					'UPDATE #__cmgroupbuying_free_coupons AS a' .
					' SET a.featured = 0'
				);

				if(!$db->execute())
				{
					throw new Exception($db->getErrorMsg());
				}
			}

			$db->setQuery(
				'UPDATE #__cmgroupbuying_free_coupons AS a' .
				' SET a.featured = '.(int) $value.
				' WHERE a.id IN ('.implode(',', $pks).')'
			);

			if(!$db->execute())
			{
				throw new Exception($db->getErrorMsg());
			}

		}
		catch(Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		$table->reorder();

		$this->cleanCache();

		return true;
	}
}