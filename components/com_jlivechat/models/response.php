<?php

/**
 * @package JLive! Chat
 * @version 4.3.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU Lesser General Public License as published by
  the Free Software Foundation; either version 3 of the License, or (at your
  option) any later version.

  This program is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
  or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
  License for more details.

  You should have received a copy of the GNU Lesser General Public License
  along with this program.  If not, see http://www.gnu.org/licenses/.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class JLiveChatModelResponse extends CMSModel
{
	/**
	 * Items total
	 * @var integer
	 */
	var $_total = null;
	var $_data = array();

	/**
	 * Pagination object
	 * @var object
	 */
	var $_pagination = null;
	var $_whereColumn = null;
	var $_whereValue = null;
	var $_sortColumn = null;
	var $_sortOrder = null;
	var $_settings = null;
	var $_fieldSeperator = "\t";

	function __construct()
	{
		$this->JLiveChatModelResponse();
	}

	function JLiveChatModelResponse()
	{
		parent::__construct();

		$mainframe = & JFactory::getApplication();

		$this->_settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function setFilter($columnName, $value)
	{
		$this->_whereColumn = $columnName;
		$this->_whereValue = $value;
	}

	function setSort($columnName, $order)
	{
		$this->_sortColumn = $columnName;
		$this->_sortOrder = $order;
	}

	function _buildQuery()
	{
		$sql = "SELECT 
		    jlcr.response_id,
		    jlcr.response_category,
		    jlcr.response_name,
		    jlcr.response_txt,
		    jlcr.response_cdate,
		    jlcr.response_mdate,
		    jlcr.response_enabled,
		    jlcr.response_sort_order,
		    jlcr.response_params
		FROM #__jlc_response jlcr ";

		if($this->_whereColumn && $this->_whereValue)
		{
			$sql .= " WHERE ".$this->_whereColumn." = '".$this->_whereValue."' ";
		}

		if($this->_sortColumn && $this->_sortOrder)
		{
			$sql .= " ORDER BY ".$this->_sortColumn." ".$this->_sortOrder;
		}
		else
		{
			$sql .= " ORDER BY jlcr.response_sort_order ASC";
		}

		return $sql;
	}

	function getData()
	{
		$mainframe = & JFactory::getApplication();

		// if data hasn't already been obtained, load it
		if(empty($this->_data))
		{
			$query = $this->_buildQuery();

			$this->_db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));

			$this->_data = $this->_db->loadAssocList();

			if(count($this->_data) > 0)
			{
				foreach($this->_data as $a => $row)
				{
					$this->_data[$a]['response_params'] = json_decode($this->_data[$a]['operator_params']);

					if(!is_object($this->_data[$a]['response_params']))
					{
						$this->_data[$a]['response_params'] = new stdClass();
					}
				}
			}
		}

		return $this->_data;
	}

	function getTotal()
	{
		// Load the content if it doesn't already exist
		if(empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	function getPagination()
	{
		// Load the content if it doesn't already exist
		if(empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	function getCategories()
	{
		$sql = "SELECT 
		    DISTINCT(response_category) AS 'response_category'
		FROM #__jlc_response
		ORDER BY response_category ASC;";
		$this->_db->setQuery($sql);

		return $this->_db->loadAssocList();
	}

	function getResponse($responseId)
	{
		$sql = "SELECT
		    jlcr.response_id,
		    jlcr.response_category,
		    jlcr.response_name,
		    jlcr.response_txt,
		    jlcr.response_cdate,
		    jlcr.response_mdate,
		    jlcr.response_enabled,
		    jlcr.response_sort_order,
		    jlcr.response_params
		FROM #__jlc_response jlcr
		WHERE jlcr.response_id = ".(int)$responseId."
		LIMIT 1;";

		$this->_db->setQuery($sql);

		$result = $this->_db->loadAssoc();

		if(isset($result['response_params']))
		{
			$result['response_params'] = json_decode($result['response_params']);
		}

		return $result;
	}

	function getLastSortOrder()
	{
		$sql = "SELECT MAX(response_sort_order) FROM #__jlc_response;";
		$this->_db->setQuery($sql);

		$maxSort = (int)$this->_db->loadResult();

		++$maxSort;

		return $maxSort;
	}

	function getSortOrder($responseId)
	{
		$sql = "SELECT response_sort_order FROM #__jlc_response
		WHERE response_id = ".(int)$responseId."
		LIMIT 1;";
		$this->_db->setQuery($sql);

		return $this->_db->loadResult();
	}

	function getAll()
	{
		$this->setState('limit', null);
		$this->setState('limitstart', null);

		return $this->getData();
	}

	function getAllActiveResponses()
	{
		$sql = "SELECT
		    jlcr.response_id,
		    jlcr.response_category,
		    jlcr.response_name,
		    jlcr.response_txt,
		    jlcr.response_cdate,
		    jlcr.response_mdate,
		    jlcr.response_sort_order,
		    jlcr.response_params
		FROM #__jlc_response jlcr
		WHERE jlcr.response_enabled = 1;";
		$this->_db->setQuery($sql);

		return $this->_db->loadObjectList();
	}
}