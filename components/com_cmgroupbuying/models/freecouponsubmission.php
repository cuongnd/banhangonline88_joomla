<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class CMGroupBuyingModelFreeCouponSubmission extends JModelAdmin
{
	public function getTable($type = 'FreeCoupon', $prefix = 'CMGroupBuyingTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		jimport('joomla.form.form');

		// Get the form.
		$form = $this->loadForm('com_cmgroupbuying.freecouponsubmission', 'freecouponsubmission', array('control' => 'jform', 'load_data' => $loadData));

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
		$jinput = JFactory::getApplication()->input;
		$layout = $jinput->get('layout', '', 'word');
		$navigation = $jinput->get('navigation', '', 'word');

		if($layout != "edit" && $navigation == '')
		{
			$data = $this->getItem();
		}
		else
		{
			// Check the session for previously entered form data.
			$data = JFactory::getApplication()->getUserState('com_cmgroupbuying.edit.freecouponsubmission.data', array());

			if(empty($data))
			{
				$data = $this->getItem();
			}
		}

		return $data;
	}

	public function save($data)
	{
		$user = JFactory::getUser();

		// We do not check for unpublished partner here. Checking is done in controller and view already.
		$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerByUserId($user->id);

		$data['partner_id'] = $partner['id'];
		$data['approved'] = 0;
		$data['published'] = 0;

		// Initialise variables;
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int)$this->getState($this->getName().'.id');
		$isNew = true;

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
		catch (Exception $e)
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

		// Save the deal succesfully
		$couponId = $table->id;
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');
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
}