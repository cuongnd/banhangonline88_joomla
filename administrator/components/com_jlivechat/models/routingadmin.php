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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class JLiveChatModelRoutingAdmin extends CMSModel
{
	/**
	 * Items total
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 * @var object
	 */
	var $_pagination = null;
	var $_whereColumn = null;
	var $_whereValue = null;
	var $_sortColumn = null;
	var $_sortOrder = null;
	var $_fieldSeperator = null;
	var $_crlf = "\n";

	function __construct($options = array())
	{
		$this->JLiveChatModelRoutingAdmin($options);
	}

	function JLiveChatModelRoutingAdmin($options = array())
	{
		parent::__construct();

		$mainframe = & JFactory::getApplication();
		
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jlcdateadmin.php';
		
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
		    lr.route_id,
		    lr.route_name,
		    lr.route_source_data,
		    lr.route_action,
		    lr.route_enabled,
		    lr.route_cdate,
		    lr.route_mdate,
		    lr.route_params,
		    lr.route_sort_order
		FROM #__jlc_route lr ";

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
			$sql .= " ORDER BY lr.route_sort_order ASC";
		}

		return $sql;
	}

	function getData($displayFriendly = true)
	{
		$mainframe = & JFactory::getApplication();

		// if data hasn't already been obtained, load it
		if(empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

			for($a = 0; $a < count($this->_data); $a++)
			{
				if(strlen($this->_data[$a]->route_source_data) > 2)
				{
					$this->_data[$a]->route_source_data = json_decode($this->_data[$a]->route_source_data);

					if(count($this->_data[$a]->route_source_data) > 0 && $displayFriendly)
					{
						foreach($this->_data[$a]->route_source_data as $key => $val)
						{
							if(is_numeric($key))
							{
								$this->_data[$a]->route_source_data->$key = $val;

								preg_match('@^([^:]+):(.+?)$@', $this->_data[$a]->route_source_data->$key, $ruleParts);

								$maxLength = 20;

								if(strlen($ruleParts[2]) > $maxLength)
								{
									$ruleParts[2] = substr($ruleParts[2], 0, $maxLength).'...';
								}

								switch($ruleParts[1])
								{
									case 'by_city':
										$this->_data[$a]->route_source_data->$key = JText::_('City').':'.$ruleParts[2];
										break;
									case 'by_country':
										$this->_data[$a]->route_source_data->$key = JText::_('Country').':'.$ruleParts[2];
										break;
									case 'by_ip_address':
										$this->_data[$a]->route_source_data->$key = JText::_('IP Address').':'.$ruleParts[2];
										break;
									case 'by_time':
										$this->_data[$a]->route_source_data->$key = JText::_('Time').':'.$ruleParts[2];
										break;
									case 'by_day':
										$this->_data[$a]->route_source_data->$key = JText::_('Day of Week').':'.$ruleParts[2];
										break;
									case 'by_group':
										$this->_data[$a]->route_source_data->$key = JText::_('User Group').':'.$ruleParts[2];
										break;
									case 'by_user_status':
										$this->_data[$a]->route_source_data->$key = JText::_('User Status').':'.$ruleParts[2];
										break;
									default:
										$this->_data[$a]->route_source_data->$key = $ruleParts[1].':'.$ruleParts[2];
										break;
								}
							}
						}
					}
				}

				if(strlen($this->_data[$a]->route_params) > 2)
				{
					$this->_data[$a]->route_params = json_decode($this->_data[$a]->route_params);
				}

				$cdateObj = new JLiveChatModelJLCDateAdmin($this->_data[$a]->route_cdate);
				$this->_data[$a]->route_cdate = $cdateObj->toUnix(true);
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

	function getMaxSort()
	{
		$sql = "SELECT MAX(route_sort_order) FROM #__jlc_route ";
		$this->_db->setQuery($sql);

		$max = (int)$this->_db->loadResult();

		$max += 1;

		return $max;
	}

	function addRoute($sourceData, $name = null, $action = null)
	{
		$date = new JLiveChatModelJLCDateAdmin();

		$final = $this->validateRoute($sourceData, $action);

		$data = new stdClass();

		$data->route_name = $name;
		$data->route_source_data = json_encode($final['source_data']);
		$data->route_action = $final['action'];
		$data->route_enabled = 1;
		$data->route_cdate = $date->toUnix();
		$data->route_sort_order = $this->getMaxSort();

		$this->_db->insertObject('#__jlc_route', $data, 'route_id');

		$routeId = $this->_db->insertid();

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->sync();

		return $routeId;
	}

	function updateRoute($routeId, $sourceData, $name = null, $action = null)
	{
		$date = new JLiveChatModelJLCDateAdmin();

		$final = $this->validateRoute($sourceData, $action);

		$data = new stdClass();

		$data->route_id = (int)$routeId;
		$data->route_name = $name;
		$data->route_source_data = json_encode($final['source_data']);
		$data->route_action = $final['action'];
		$data->route_mdate = $date->toUnix();

		$this->_db->updateObject('#__jlc_route', $data, 'route_id');

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->sync();

		return true;
	}

	function validateRoute($sourceData, $action)
	{
		$final = array(
			'source_data' => $sourceData,
			'action' => $action
		);

		if($action == 'send_to_selected_operators')
		{
			if(count($sourceData['selected_operators']) < 1)
			{
				$final['action'] = 'send_to_all_operators';
			}
		}

		return $final;
	}

	function updateRouteStatus($routeId, $newStatus)
	{
		$data = new stdClass();

		$data->route_id = (int)$routeId;
		$data->route_enabled = (int)$newStatus;

		$this->_db->updateObject('#__jlc_route', $data, 'route_id');

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->sync();

		return true;
	}

	function copyRoute($routes = array())
	{
		for($a = 0; $a < count($routes); $a++)
		{
			$route = $this->getRoute($routes[$a]);

			$this->addRoute($route['route_source_data'], $route['route_name'].' '.JText::_('Copy'), $route['route_action']);
		}

		return true;
	}

	function deleteRoutes($routes = array())
	{
		$routeList = '0,';

		if(count($routes) > 0)
		{
			foreach($routes as $routeId)
			{
				$routeList .= $routeId.',';
			}
		}

		$routeList = preg_replace('@[,\s]+$@', '', $routeList);

		$sql = "DELETE FROM #__jlc_route
		WHERE route_id IN (".$routeList.")";
		$this->_db->setQuery($sql);
		$this->_db->query();

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->queueForceSync('#__jlc_route');
		$hostedModeObj->forceSyncToRemote();

		return true;
	}

	function deleteRoute($routeId)
	{
		$sql = "DELETE FROM #__jlc_route 
		WHERE route_id = ".(int)$routeId;
		$this->_db->setQuery($sql);
		$this->_db->query();

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->queueForceSync('#__jlc_route');
		$hostedModeObj->forceSyncToRemote();

		return true;
	}

	function getRoute($routeId, $displayFriendly = false)
	{
		$sql = "SELECT
		    lr.route_id,
		    lr.route_name,
		    lr.route_source_data,
		    lr.route_action,
		    lr.route_enabled,
		    lr.route_cdate,
		    lr.route_mdate,
		    lr.route_params,
		    lr.route_sort_order
		FROM #__jlc_route lr

		WHERE lr.route_id = ".(int)$routeId."
		LIMIT 1;";
		$this->_db->setQuery($sql);

		$result = $this->_db->loadAssoc();

		$result['route_rules'] = array();

		if(strlen($result['route_source_data']) > 0)
		{
			$result['route_source_data'] = json_decode($result['route_source_data']);

			foreach($result['route_source_data'] as $key => $val)
			{
				if(is_numeric($key))
				{
					preg_match('@^([^:]+):(.+?)$@', $val, $ruleParts);

					$result['route_rules'][$ruleParts[1]] = $ruleParts[2];
				}
			}
		}

		if(strlen($result['route_params']) > 0)
		{
			$result['route_params'] = json_decode($result['route_params']);
		}

		if(count($result['route_rules']) > 0 && $displayFriendly)
		{
			$finalRouteRules = array();

			foreach($result['route_rules'] as $key => $val)
			{
				$valueArray = array('rule_type' => $key);

				switch($key)
				{
					case 'by_city':
						$key = JText::_('CITY');
						break;
					case 'by_country':
						$key = JText::_('COUNTRY');
						break;
					case 'by_ip_address':
						$key = JText::_('IP_ADDRESS');
						break;
					case 'by_time':
						$key = JText::_('TIME');
						break;
					case 'by_day':
						$key = JText::_('DAY_OF_WEEK');
						break;
					case 'by_group':
						$key = JText::_('USER_GROUP');
						break;
					case 'by_user_status':
						$key = JText::_('USER_STATUS');
						break;
					default:
						//$key = $key;
						break;
				}

				$valueArray['rule_display_name'] = $key;
				$valueArray['rule_value'] = $val;

				$finalRouteRules[] = $valueArray;
			}

			$result['route_rules'] = $finalRouteRules;
		}

		return $result;
	}

	function moveUpSortOrder($routeId)
	{
		$route = $this->getRoute($routeId);

		$newSortOrder = $route['route_sort_order'] - 1;

		if($newSortOrder < 1) $newSortOrder = 1;

		$sql = "UPDATE #__jlc_route SET
		    route_sort_order = route_sort_order+1
		WHERE route_sort_order >= ".(int)$newSortOrder.";";
		$this->_db->setQuery($sql);
		$this->_db->query();

		$sql = "UPDATE #__jlc_route SET
		    route_sort_order = ".$newSortOrder."
		WHERE route_id = ".(int)$routeId.";";
		$this->_db->setQuery($sql);
		$this->_db->query();

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->sync();

		return true;
	}

	function moveDownSortOrder($routeId)
	{
		$route = $this->getRoute($routeId);

		$newSortOrder = $route['route_sort_order'] + 1;

		if($newSortOrder < 1) $newSortOrder = 1;

		$sql = "UPDATE #__jlc_route SET
		    route_sort_order = route_sort_order-1
		WHERE route_sort_order >= ".(int)$newSortOrder.";";
		$this->_db->setQuery($sql);
		$this->_db->query();

		$sql = "UPDATE #__jlc_route SET
		    route_sort_order = ".$newSortOrder."
		WHERE route_id = ".(int)$routeId.";";
		$this->_db->setQuery($sql);
		$this->_db->query();

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->sync();

		return true;
	}

	function saveSortOrders($sortArray)
	{
		if(count($sortArray) < 1) return false;

		foreach($sortArray as $routeId => $newSortOrder)
		{
			$data = new stdClass();

			$data->route_id = (int)$routeId;
			$data->route_sort_order = (int)$newSortOrder;

			$this->_db->updateObject('#__jlc_route', $data, 'route_id');
		}

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->sync();

		return true;
	}

	function fixOrders()
	{
		$sql = "SELECT SQL_NO_CACHE
		    route_id 
		FROM #__jlc_route
		
		ORDER BY route_sort_order ASC;";
		$this->_db->setQuery($sql);

		$results = $this->_db->loadAssocList();

		for($a = 0; $a < count($results); $a++)
		{
			$data = new stdClass();

			$data->route_id = $results[$a]['route_id'];
			$data->route_sort_order = $a + 1;

			$this->_db->updateObject('#__jlc_route', $data, 'route_id');
		}

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->sync();

		return true;
	}

	function toggleStatus($routeId = null)
	{
		if(!$routeId) return false;

		$sql = "SELECT
		    route_enabled
		FROM #__jlc_route
		WHERE route_id = ".(int)$routeId."
		LIMIT 1;";
		$this->_db->setQuery($sql);

		if($this->_db->loadResult() == 1)
		{
			// Currently enabled, disable
			$newStatus = 0;
		}
		else
		{
			// Currently disabled, enable
			$newStatus = 1;
		}

		$data = new stdClass();

		$data->route_id = (int)$routeId;
		$data->route_enabled = $newStatus;

		$this->_db->updateObject('#__jlc_route', $data, 'route_id');

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->sync();

		return true;
	}
}