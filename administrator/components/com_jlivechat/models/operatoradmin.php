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

class JLiveChatModelOperatorAdmin extends CMSModel
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
		$this->JLiveChatModelOperatorAdmin();
	}

	function JLiveChatModelOperatorAdmin()
	{
		parent::__construct();

		$mainframe = & JFactory::getApplication();
		
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jlcdateadmin.php';
		
		$this->_settings = & CMSModel::getInstance('SettingAdmin', 'JLiveChatModel');

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
					jlco.operator_id,
					jlco.operator_name,
					jlco.alt_name,
					jlco.department,
					jlco.accept_chat_timeout,
					jlco.sort_order,
					jlco.operator_params,
					jlco.is_enabled,
					jlco.cdate,
					jlco.mdate,
					jlco.last_auth_date,
					jlco.auth_key 
				FROM #__jlc_operator jlco ";

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
			$sql .= " ORDER BY jlco.sort_order ASC";
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
					$this->_data[$a]['operator_params'] = json_decode($this->_data[$a]['operator_params']);

					if(!is_object($this->_data[$a]['operator_params']))
					{
						$this->_data[$a]['operator_params'] = new stdClass();
					}

					if($this->_data[$a]['last_auth_date'] > 0)
					{
						$date = new JLiveChatModelJLCDateAdmin($this->_data[$a]['last_auth_date']);

						$this->_data[$a]['last_auth_date'] = $date->toFormat('%m/%d/%Y %H:%M:%S');
					}
					else
					{
						$this->_data[$a]['last_auth_date'] = JText::_('UNKNOWN');
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

	function getDepartments()
	{
		$sql = "SELECT 
					DISTINCT(department) AS 'department' 
				FROM #__jlc_operator
				ORDER BY department ASC;";
		$this->_db->setQuery($sql);

		return $this->_db->loadAssocList();
	}

	function createNewOperator($descName = null, $department = null)
	{
		$date = new JLiveChatModelJLCDateAdmin();

		// No double quotes allowed
		if($department) $department = str_replace('"', '', $department);

		$defaultParams = new stdClass();

		$defaultParams->website_monitor = 1;
		$defaultParams->operator2operator = 1;
		$defaultParams->messages = 1;
		$defaultParams->use_ssl = 0;

		$data = new stdClass();

		$data->alt_name = $descName;
		$data->department = $department;
		$data->accept_chat_timeout = 60; // Default is 1 minute
		$data->sort_order = $this->getLastSortOrder();
		$data->operator_params = json_encode($defaultParams);
		$data->is_enabled = 1;
		$data->cdate = $date->toUnix();
		$data->mdate = $data->cdate;
		$data->auth_key = $this->_generateAuthKey();

		$this->_db->insertObject('#__jlc_operator', $data, 'operator_id');

		$operatorId = $this->_db->insertid();

		return $operatorId;
	}

	function updateOperator($operatorId, $descName = null, $department = null)
	{
		if(!$operatorId) return false;

		$date = new JLiveChatModelJLCDateAdmin();

		// No double quotes allowed
		if($department) $department = str_replace('"', '', $department);

		$data = new stdClass();

		$data->operator_id = (int)$operatorId;
		$data->alt_name = $descName;
		$data->department = $department;
		$data->mdate = $date->toUnix();

		$this->_db->updateObject('#__jlc_operator', $data, 'operator_id');

		// Increment settings checksum so client is updated
		$this->_settings->saveSettings();

		return true;
	}

	function setPermission($operatorId, $name, $val)
	{
		$perms = $this->getPermissions($operatorId);

		if(!is_object($perms)) $perms = new stdClass();

		$perms->$name = $val;

		$data = new stdClass();

		$data->operator_id = $operatorId;
		$data->operator_params = json_encode($perms);

		$this->_db->updateObject('#__jlc_operator', $data, 'operator_id');

		return true;
	}

	function getPermissions($operatorId)
	{
		$sql = "SELECT operator_params FROM #__jlc_operator
				WHERE operator_id = ".(int)$operatorId."
				LIMIT 1;";
		$this->_db->setQuery($sql);

		$perms = $this->_db->loadResult();

		return json_decode($perms);
	}

	function getOperator($operatorId)
	{
		$sql = "SELECT
					jlco.operator_id,
					jlco.operator_name,
					jlco.alt_name,
					jlco.department,
					jlco.accept_chat_timeout,
					jlco.sort_order,
					jlco.operator_params,
					jlco.is_enabled,
					jlco.cdate,
					jlco.mdate,
					jlco.last_auth_date
				FROM #__jlc_operator jlco 
				WHERE jlco.operator_id = ".(int)$operatorId." 
				LIMIT 1;";

		$this->_db->setQuery($sql);

		$result = $this->_db->loadAssoc();

		if(isset($result['operator_params']))
		{
			$result['operator_params'] = json_decode($result['operator_params']);
		}

		return $result;
	}

	function getLastSortOrder()
	{
		$sql = "SELECT MAX(sort_order) FROM #__jlc_operator;";
		$this->_db->setQuery($sql);

		$maxSort = (int)$this->_db->loadResult();

		++$maxSort;

		return $maxSort;
	}

	function _generateAuthKey()
	{
		return md5(uniqid(rand(), true)).md5(uniqid(rand(), true));
	}

	function toggleStatus($operatorId = null)
	{
		if(!$operatorId) return false;

		$sql = "SELECT 
					is_enabled 
				FROM #__jlc_operator 
				WHERE operator_id = ".(int)$operatorId." 
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

		$data->operator_id = (int)$operatorId;
		$data->is_enabled = $newStatus;

		$this->_db->updateObject('#__jlc_operator', $data, 'operator_id');

		return true;
	}

	function downloadKeyFile($operatorId, $forceDownload = true)
	{
		$mainframe = & JFactory::getApplication();

		$authKey = $this->_getOperatorAuthKey($operatorId);

		$uri = & JFactory::getUri();

		$uri->setScheme('http'); // Force HTTP

		$settingsObj = & CMSModel::getInstance('SettingAdmin', 'JLiveChatModel');

		$hostedMode = $settingsObj->isHostedMode();

		if($hostedMode)
		{
			// Hosted Mode
			$callbackUri = $hostedMode['hosted_uri'].'/index.php?option=com_jlivechat&view=api&task=api&no_html=1&do_not_log=true';
		}
		else
		{
			// Stand-alone mode
			$callbackUri = preg_replace('@[^/]+/[^/]+$@', 'index.php', $uri->toString());
			$callbackUri .= '?option=com_jlivechat&view=api&task=api&no_html=1&do_not_log=true';
		}

		// required for IE, otherwise Content-disposition is ignored
		if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');

		$keyContents = $callbackUri.$this->_fieldSeperator.$authKey;
		$keyContents = base64_encode($keyContents);

		$keySize = strlen($keyContents);

		if($forceDownload)
		{
			$filename = $uri->toString(array('host'));
			$filename .= '_'.date('m_d_Y');
			$filename = preg_replace('@[^-a-zA-Z0-9_]+@', '_', $filename);
			$filename .= '.jkf';

			header('Content-Type: application/force-download');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Content-Transfer-Encoding: binary');
			header('Accept-Ranges: bytes');
			header('Cache-control: private');
			header('Pragma: private');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-Length: '.$keySize);

			echo $keyContents;

			flush();
			jexit();
		}
		else
		{
			return $keyContents;
		}
	}

	function _getOperatorAuthKey($operatorId)
	{
		$sql = "SELECT auth_key FROM #__jlc_operator
				WHERE operator_id = ".(int)$operatorId." 
				LIMIT 1;";
		$this->_db->setQuery($sql);

		return $this->_db->loadResult();
	}

	function moveUpSortOrder($operatorId)
	{
		$dateObj = new JLiveChatModelJLCDateAdmin();

		$newSortOrder = $this->getOperatorCallOrder($operatorId) - 1;

		if($newSortOrder < 1) $newSortOrder = 1;

		$sql = "UPDATE #__jlc_operator SET
					sort_order = sort_order+1,
					mdate = ".$dateObj->toUnix()." 
				WHERE sort_order >= ".(int)($newSortOrder).";";
		$this->_db->setQuery($sql);
		$this->_db->query();

		$sql = "UPDATE #__jlc_operator SET
					sort_order = ".$newSortOrder.",
					mdate = ".$dateObj->toUnix()." 
				WHERE operator_id = ".(int)$operatorId.";";
		$this->_db->setQuery($sql);
		$this->_db->query();

		return true;
	}

	function moveDownSortOrder($operatorId)
	{
		$dateObj = new JLiveChatModelJLCDateAdmin();

		$newSortOrder = $this->getOperatorCallOrder($operatorId) + 1;

		if($newSortOrder < 1) $newSortOrder = 1;

		$sql = "UPDATE #__jlc_operator SET
					sort_order = sort_order-1,
					mdate = ".$dateObj->toUnix()." 
				WHERE sort_order >= ".(int)$newSortOrder.";";
		$this->_db->setQuery($sql);
		$this->_db->query();

		$sql = "UPDATE #__jlc_operator SET
					sort_order = ".$newSortOrder.",
					mdate = ".$dateObj->toUnix()." 
				WHERE operator_id = ".(int)$operatorId.";";
		$this->_db->setQuery($sql);
		$this->_db->query();

		return true;
	}

	function getOperatorCallOrder($operatorId)
	{
		$sql = "SELECT sort_order FROM #__jlc_operator
				WHERE operator_id = ".(int)$operatorId."
				LIMIT 1;";
		$this->_db->setQuery($sql);

		return $this->_db->loadResult();
	}

	function saveSortOrders($sortArray)
	{
		if(empty($sortArray)) return false;

		$dateObj = new JLiveChatModelJLCDateAdmin();

		foreach($sortArray as $operatorId => $newSortOrder)
		{
			$data = new stdClass();

			$data->operator_id = (int)$operatorId;
			$data->sort_order = (int)$newSortOrder;
			$data->mdate = $dateObj->toUnix();

			$this->_db->updateObject('#__jlc_operator', $data, 'operator_id');
		}

		return true;
	}

	function deleteOperator($operatorId)
	{
		// Delete any IP Blocker rules
		$sql = "DELETE FROM #__jlc_ipblocker
				WHERE operator_id = ".(int)$operatorId.";";
		$this->_db->setQuery($sql);

		// Delete any messages
		$sql = "DELETE FROM #__jlc_message
				WHERE operator_id = ".(int)$operatorId.";";
		$this->_db->setQuery($sql);

		// Delete sync record
		$sql = "DELETE FROM #__jlc_operator_sync 
				WHERE operator_id = ".(int)$operatorId.";";
		$this->_db->setQuery($sql);

		$sql = "DELETE FROM #__jlc_operator
				WHERE operator_id = ".(int)$operatorId.";";
		$this->_db->setQuery($sql);
		$this->_db->query();

		return true;
	}

	function getGroups()
	{
		$sql = "SELECT
					id,
					title AS 'name'
				FROM #__usergroups;";

		$this->_db->setQuery($sql);

		return $this->_db->loadAssocList();
	}

	function getAllOperators()
	{
		$this->setState('limit', null);
		$this->setState('limitstart', null);

		return $this->getData();
	}

	function getAllInitializedOperators()
	{
		$sql = "SELECT DISTINCT
					operator_id  AS 'operator_id',
					operator_name AS 'operator_name' 
				FROM #__jlc_operator
				ORDER BY operator_name ASC;";
		$this->_db->setQuery($sql);

		return $this->_db->loadAssocList();
	}

	function operatorKeyLabel($operatorId)
	{
		$allOperators = $this->getData();

		if(count($allOperators) > 0)
		{
			foreach($allOperators as $operator)
			{
				if($operatorId == $operator['operator_id'])
				{
					$lbl = '';

					if(strlen($operator['operator_name']) > 0)
					{
						$lbl .= $operator['operator_name'].' ';
					}

					if(strlen($operator['alt_name']) > 0)
					{
						$lbl .= '('.$operator['alt_name'].')';
					}

					$lbl .= ' (ID:'.$operatorId.')';

					return $lbl;
				}
			}
		}

		return false;
	}

	function fixOrders()
	{
		$dateObj = new JLiveChatModelJLCDateAdmin();

		$sql = "SELECT SQL_NO_CACHE
					operator_id 
				FROM #__jlc_operator

				ORDER BY sort_order ASC;";
		$this->_db->setQuery($sql);

		$results = $this->_db->loadAssocList();

		for($a = 0; $a < count($results); $a++)
		{
			$data = new stdClass();

			$data->operator_id = $results[$a]['operator_id'];
			$data->sort_order = $a + 1;
			$data->mdate = $dateObj->toUnix();

			$this->_db->updateObject('#__jlc_operator', $data, 'operator_id');
		}

		return true;
	}

	function fixKeyOrders()
	{
		return $this->fixOrders();
	}
}