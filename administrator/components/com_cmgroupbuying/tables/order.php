<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingTableOrder extends JTable
{
	public function __construct(& $db)
	{
		parent::__construct('#__cmgroupbuying_orders', 'id', $db);
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
		return true;
	}
}