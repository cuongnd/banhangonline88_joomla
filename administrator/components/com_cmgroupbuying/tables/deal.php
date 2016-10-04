<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingTableDeal extends JTable
{
	public function __construct(& $db)
	{
		parent::__construct('#__cmgroupbuying_deals', 'id', $db);
	}

	public function bind($array, $ignore = '')
	{
		return parent::bind($array, $ignore);
	}

	public function store($updateNulls = false)
	{
		// Verify that the alias is unique
		$table = JTable::getInstance('Deal', 'CMGroupBuyingTable');

		if($table->load(array('alias'=>$this->alias)) && ($table->id != $this->id || $this->id==0))
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_DEAL_ERROR_UNIQUE_ALIAS'));
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
			$this->setError(JText::_('COM_CMGROUPBUYING_DEAL_WARNING_PROVIDE_VALID_NAME'));
			return false;
		}

		// Check for existing name
		$query = 'SELECT id FROM #__cmgroupbuying_deals WHERE name = '.$this->_db->Quote($this->name);
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());

		if($xid && $xid != intval($this->id))
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_DEAL_WARNING_SAME_NAME'));
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

		$jform = JFactory::getApplication()->input->get('jform', array(), 'raw');

		if($jform['option_name_1'] == '')
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_DEAL_WARNING_PROVIDE_VALID_OPTION_NAME'));
			return false;
		}

		// Check for valid deal price and original price
		for($i = 1; $i <= 10; $i++)
		{
			if($jform['option_name_' . $i] != ''
					&& $jform['option_original_price_' . $i] != ''
					&& $jform['option_price_' . $i] != '')
			{
				if(!is_numeric($jform['option_original_price_' . $i]) || $jform['option_original_price_' . $i] <= 0)
				{
					$this->setError(JText::_('COM_CMGROUPBUYING_DEAL_WARNING_PROVIDE_VALID_ORIGINAL_PRICE'));
					return false;
				}

				if($jform['option_original_price_' . $i] < $jform['option_price_' . $i])
				{
					$this->setError(JText::_('COM_CMGROUPBUYING_DEAL_WARNING_ORIGINAL_GREATER'));
					return false;
				}
			}
		}

		/* Commented for allowing zero price
		// Check for advance payment
		if($this->advance_payment == 1)
		{
			for($i = 1; $i <= 5; $i++)
			{
				if($jform['option_name_' . $i] != ''
						&& $jform['option_original_price_' . $i] != ''
						&& $jform['option_price_' . $i] != '')
				{
					if(!is_numeric($jform['option_advance_price_' . $i]) || $jform['option_advance_price_' . $i] <= 0)
					{
						$this->setError(JText::_('COM_CMGROUPBUYING_DEAL_WARNING_PROVIDE_ADVANCE_PRICE'));
						return false;
					}

					if($jform['option_advance_price_' . $i] >= $jform['option_price_' . $i])
					{
						$this->setError(JText::_('COM_CMGROUPBUYING_DEAL_WARNING_PROVIDE_VALID_ADVANCE_PRICE'));
						return false;
					}
				}
			}
		}
		*/

		// Check for valid min quatity to tip deal
		if(!is_numeric($this->min_bought) || $this->min_bought <= 0)
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_DEAL_WARNING_PROVIDE_VALID_MIN_BOUGHT'));
			return false;
		}

		// Check for valid max bought quantity for each user
		if(!is_numeric($this->max_bought) || $this->max_bought == 0 || $this->max_bought < -1)
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_DEAL_WARNING_PROVIDE_VALID_MAX_BOUGHT'));
			return false;
		}

		// Check for valid max coupon quantity
		if(!is_numeric($this->max_coupon) || $this->max_coupon == 0 || $this->max_coupon < -1)
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_DEAL_WARNING_PROVIDE_VALID_MAX_COUPON'));
			return false;
		}

		// Check for valid max coupon quantity and min quatity to tip deal
		if($this->max_coupon != -1 && $this->max_coupon < $this->min_bought)
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_DEAL_WARNING_PROVIDE_VALID_MAX_COUPON_MIN_BOUGHT'));
			return false;
		}

		// Check for start date and end date
		if(strtotime($this->start_date) >= strtotime($this->end_date))
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_DEAL_WARNING_PROVIDE_VALID_DATE'));
			return false;
		}

		// Check for valid shipping cost
		if(!is_numeric($this->shipping_cost) || $this->shipping_cost < 0)
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_DEAL_WARNING_PROVIDE_VALID_SHIPPING_COST'));
			return false;
		}

		// Check for coupon image
		if (trim($this->coupon_path) == '')
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_DEAL_WARNING_CHOOSE_COUPON_IMAGE'));
			return false;
		}

		// Check for coupon elements
		if (trim($this->coupon_elements) == '')
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_DEAL_WARNING_DESIGN_COUPON'));
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
