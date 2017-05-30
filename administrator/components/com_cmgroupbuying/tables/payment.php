<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingTablePayment extends JTable
{
	public function __construct(& $db)
	{
		parent::__construct('#__cmgroupbuying_payments', 'id', $db);
	}

	public function bind($array, $ignore = '')
	{
		return parent::bind($array, $ignore);
	}

	public function store($updateNulls = false)
	{
		// Attempt to store the data.
		return parent::store($updateNulls);
	}

	function check()
	{
		// Check for valid name
		if (trim($this->name) == '')
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_PAYMENT_WARNING_PROVIDE_VALID_NAME'));
			return false;
		}

		// Check for valid lock_time
		if ($this->lock_time <= 0)
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_PAYMENT_WARNING_PROVIDE_VALID_LOCK_TIME'));
			return false;
		}

		// Check for existing name
		$query = 'SELECT id FROM #__cmgroupbuying_payments WHERE name = '.$this->_db->Quote($this->name);
		$this->_db->setQuery($query);
		$xid = intval($this->_db->loadResult());

		if ($xid && $xid != intval($this->id))
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_PAYMENT_WARNING_SAME_NAME'));
			return false;
		}

		return true;
	}
}