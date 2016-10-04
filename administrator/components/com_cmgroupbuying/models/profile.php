<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class CMGroupBuyingModelProfile extends JModelAdmin
{
	public function getTable($type = 'Profile', $prefix = 'CMGroupBuyingTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		jimport('joomla.form.form');

		$form = $this->loadForm('com_cmgroupbuying.profile', 'profile', array('control' => 'jform', 'load_data' => $loadData));

		if(empty($form))
			return false;

		return $form;
	}

	public function getItem($pk = null)
	{
		$pk = 1;
		$item = parent::getItem($pk);
		return $item;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_cmgroupbuying.edit.profile.data', array());

		if(empty($data))
			$data = $this->getItem();

		return $data;
	}

	public function save($data)
	{
		//$dispatcher = JDispatcher::getInstance();
		$table = $this->getTable();
		$key = $table->getKeyName();
		// $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$pk = 1;
		$isNew = true;

		// Include the content plugins for the on save events.
		// JPluginHelper::importPlugin('content');

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

			// Prepare the row for saving
			$this->prepareTable($table);

			// Check the data.
			if(!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			/*
			// Trigger the onContentBeforeSave event.
			$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, $table, $isNew));
			if (in_array(false, $result, true))
			{
				$this->setError($table->getError());
				return false;
			}
			*/

			// Store the data.
			if(!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}

			// Clean the cache.
			$this->cleanCache();

			// Trigger the onContentAfterSave event.
			// $dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew));
		}
		catch(Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		$pkName = $table->getKeyName();

		if(isset($table->$pkName))
		{
			$this->setState($this->getName() . '.id', $table->$pkName);
		}
		
		$this->setState($this->getName() . '.new', $isNew);

		return true;
	}

	public function getProfile()
	{
		$db = JFactory::getDBO();
		$query = 'SELECT * FROM #__cmgroupbuying_user_profile WHERE id = 1';
		$db->setQuery($query);
		$configuration = $db->loadAssoc();

		if($this->_db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $configuration;
	}
}
