<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingTablePartner extends JTable
{
	public function __construct(& $db)
	{
		parent::__construct('#__cmgroupbuying_partners', 'id', $db);
	}

	public function bind($array, $ignore = '')
	{
		return parent::bind($array, $ignore);
	}

	public function store($updateNulls = false)
	{
		// Verify that the alias is unique
		$table = JTable::getInstance('Partner', 'CMGroupBuyingTable');

		if($table->load(array('alias'=>$this->alias)) && ($table->id != $this->id || $this->id==0))
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_PARTNER_ERROR_UNIQUE_ALIAS'));
			return false;
		}

		// Attempt to store the data.
		return parent::store($updateNulls);
	}

	function check()
	{
		// Check for valid name
		if (trim($this->name) == '')
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_PARTNER_WARNING_PROVIDE_VALID_NAME'));
			return false;
		}

		// Check for existing name
		$query = 'SELECT id FROM #__cmgroupbuying_partners WHERE name = ' . $this->_db->Quote($this->name);
		$this->_db->setQuery($query);
		$xid = intval($this->_db->loadResult());

		if($xid && $xid != intval($this->id))
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_PARTNER_WARNING_SAME_NAME'));
			return false;
		}

		// Check for Joomla userID
		if(trim($this->user_id) == 0)
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_PARTNER_WARNING_CHOOSE_USER'));
			return false;
		}

		// Check for existing Joomla userId
		$query = 'SELECT id FROM #__cmgroupbuying_partners WHERE user_id = '. $this->user_id;
		$this->_db->setQuery($query);
		$xid = intval($this->_db->loadResult());

		if($xid && $xid != intval($this->id))
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_PARTNER_WARNING_SAME_JOOMLA_USERID'));
			return false;
		}

		if(empty($this->alias))
		{
			$this->alias = $this->name;
		}

		$this->alias = JApplication::stringURLSafe($this->alias);

		if(trim(str_replace('-','',$this->alias)) == '')
		{
			$this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}

		return true;
	}
}