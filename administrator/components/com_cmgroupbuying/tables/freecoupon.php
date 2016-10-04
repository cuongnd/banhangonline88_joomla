<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingTableFreeCoupon extends JTable
{
	public function __construct(& $db)
	{
		parent::__construct('#__cmgroupbuying_free_coupons', 'id', $db);
	}

	public function bind($array, $ignore = '')
	{
		return parent::bind($array, $ignore);
	}

	public function store($updateNulls = false)
	{
		// Verify that the alias is unique
		$table = JTable::getInstance('FreeCoupon', 'CMGroupBuyingTable');

		if($table->load(array('alias'=>$this->alias)) && ($table->id != $this->id || $this->id==0))
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_FREE_COUPON_ERROR_UNIQUE_ALIAS'));
			return false;
		}

		// Attempt to store the data.
		return parent::store($updateNulls);
	}

	function check()
	{
		$app = JFactory::getApplication();
		// Check for valid name
		if (trim($this->name) == '')
		{
			$app->enqueueMessage(JText::_('COM_CMGROUPBUYING_FREE_COUPON_WARNING_PROVIDE_VALID_NAME'));
			return false;
		}

		// Check for existing name
		$query = 'SELECT id FROM #__cmgroupbuying_free_coupons WHERE name = '.$this->_db->Quote($this->name);
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());

		if($xid && $xid != intval($this->id))
		{
			$app->enqueueMessage(JText::_('COM_CMGROUPBUYING_FREE_COUPON_WARNING_SAME_NAME'));
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

		// Check for start date and end date
		if(strtotime($this->start_date) >= strtotime($this->end_date))
		{
			$app->enqueueMessage(JText::_('COM_CMGROUPBUYING_FREE_COUPON_WARNING_PROVIDE_VALID_DATE'));
			return false;
		}

		// Check for valid coupon code, only allows alpha numeric characters
		if($this->type == 'code' && !preg_match('/^[A-Za-z0-9]+$/',$this->coupon_code))
		{
			$app->enqueueMessage(JText::_('COM_CMGROUPBUYING_FREE_COUPON_WARNING_PROVIDE_VALID_COUPON_CODE'));
			return false;
		}

		// Check for valid coupon image
		if($this->type == 'printable' && $this->coupon_path == '')
		{
			$app->enqueueMessage(JText::_('COM_CMGROUPBUYING_FREE_COUPON_WARNING_PROVIDE_VALID_COUPON_PATH'));
			return false;
		}

		// Set ordering
		if(empty($this->ordering))
		{
			// Set ordering to last if ordering was 0
			$this->ordering = self::getNextOrder();
		}

		return true;
	}
}