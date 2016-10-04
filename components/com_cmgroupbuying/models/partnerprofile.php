<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class CMGroupBuyingModelPartnerProfile extends JModelAdmin
{
	public function getTable($type = 'Partner', $prefix = 'CMGroupBuyingTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		jimport('joomla.form.form');

		$form = $this->loadForm('com_cmgroupbuying.partnerprofile',
			'partnerprofile',
			array('control' => 'jform','load_data' => $loadData)
		);

		if(empty($form))
			return false;

		return $form;
	}

	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		return $item;
	}

	protected function loadFormData()
	{
		$user = JFactory::getUser();
		$partnerId = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerIdByUserId($user->id);

		if(empty($partnerId))
		{
			return JError::raiseWarning(404, JText::_('COM_CMGROUPBUYING_ACCESS_DENIED'));
		}

		// TODO: load partner only once
		$partner = $this->getItem($partnerId);
		$state = JFactory::getApplication()->getUserState('com_cmgroupbuying.edit.partnerprofile.data', array());

		if(empty($state) && !empty($partner))
		{
			return $partner;
		}
		elseif(!empty($state['id']) && !empty($partner) && $state['id'] != $partner->id)
		{
			JFactory::getApplication()->setUserState('com_cmgroupbuying.edit.partnerprofile.data', null);
			return $partner;
		}
		else
		{
			return $state;
		}
	}

	public function save($data)
	{
		$user = JFactory::getUser();
		$partnerId = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerIdByUserId($user->id);

		if(empty($partnerId))
		{
			return JError::raiseWarning(404, JText::_('COM_CMGROUPBUYING_ACCESS_DENIED'));
		}

		$data['user_id'] = $user->id;

		// Initialise variables;
		$table = $this->getTable();
		$key = $table->getKeyName('id');
		$pk = $partnerId;
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
		catch(Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		return true;
	}
}