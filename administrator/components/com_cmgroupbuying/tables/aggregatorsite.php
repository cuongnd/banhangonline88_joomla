<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingTableAggregatorSite extends JTable
{
	public function __construct(& $db)
	{
		parent::__construct('#__cmgroupbuying_aggregator_sites', 'id', $db);
	}

	public function bind($array, $ignore = '')
	{
		return parent::bind($array, $ignore);
	}

	public function store($updateNulls = false)
	{
		$table = JTable::getInstance('AggregatorSite', 'CMGroupBuyingTable');

		if(($this->id ==0) || ($this->id != 0 && $this->ref == ''))
		{
			do {
				$ref = '';

				for($i = 0; $i < 10; $i++)
				{
					$ref .= chr(rand(ord('a'), ord('z')));
				}

			} while($table->load(array('ref'=>$ref)));

			$this->ref = $ref;
		}

		// Attempt to store the data.
		return parent::store($updateNulls);
	}

	function check()
	{
		// Check for valid name
		if(trim($this->name) == '')
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_AGG_SITE_WARNING_PROVIDE_VALID_NAME'));
			return false;
		}

		// Check for existing name
		$query = 'SELECT id FROM #__cmgroupbuying_aggregator_sites WHERE name = '.$this->_db->Quote($this->name);
		$this->_db->setQuery($query);
		$xid = intval($this->_db->loadResult());

		if($xid && $xid != intval($this->id))
		{
			$this->setError(JText::_('COM_CMGROUPBUYING_AGG_SITE_WARNING_SAME_NAME'));
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