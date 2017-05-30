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

class JLiveChatModelHistoryAdmin extends CMSModel 
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

	function setSort($columnName, $order)
	{
		$this->_sortColumn = $columnName;
		$this->_sortOrder = $order;
	}

	function _buildQuery()
	{
		$mainframe = & JFactory::getApplication();

		$sql = "SELECT 
		    c.chat_session_id,
		    c.chat_alt_id,
		    c.chat_session_content,
		    c.chat_session_params,
		    c.cdate,
		    c.mdate,
		    c.is_active,
		    c.is_multimember
		FROM #__jlc_chat c
		WHERE c.chat_session_id IS NOT NULL ";

		if(JRequest::getVar('filter_by_department', null, 'method'))
		{
			$operatorsInDepartment = $this->getOperatorsByDepartment(JRequest::getVar('filter_by_department', '', 'method'));

			if(!empty($operatorsInDepartment))
			{
				$operatorChatSessionIds = $this->getChatSessionsByOperators($operatorsInDepartment);

				if(empty($operatorChatSessionIds)) $operatorChatSessionIds[] = 0;

				$sql .= " AND c.chat_session_id IN (".implode(',', $operatorChatSessionIds).") ";
			}
		}

		if(JRequest::getInt('filter_by_operator', null, 'method'))
		{
			$operatorChatSessionIds = $this->getChatSessionsByOperators(array(JRequest::getInt('filter_by_operator', 0, 'method')));

			if(empty($operatorChatSessionIds)) $operatorChatSessionIds[] = 0;

			$sql .= " AND c.chat_session_id IN (".implode(',', $operatorChatSessionIds).") ";
		}

		if(JRequest::getVar('filter_fromdate', null, 'method'))
		{
			$fromDate = JRequest::getVar('filter_fromdate', date('Y-m-d'), 'method').' 00:00:00';

			$fromDateObj = new JLiveChatModelJLCDateAdmin($fromDate);

			$sql .= " AND c.cdate >= ".$fromDateObj->toUnix(true).' ';
		}

		if(JRequest::getVar('filter_todate', null, 'method'))
		{
			$toDate = JRequest::getVar('filter_todate', date('Y-m-d'), 'method').' 23:59:59';

			$toDateObj = new JLiveChatModelJLCDateAdmin($toDate);

			$sql .= ' AND c.cdate <= '.$toDateObj->toUnix(true).' ';
		}

		$searchTxt = JRequest::getVar('search_txt', null, 'method');

		if($searchTxt)
		{
			if(is_numeric($searchTxt))
			{
				$sql .= " AND c.chat_session_id = ".(int)JRequest::getInt('search_txt', 0, 'method').' ';
			}
			else
			{
				$sql .= " AND c.chat_alt_id = ".$this->_db->Quote(JRequest::getVar('search_txt', '', 'method')).' ';
			}
		}

		if($this->_sortColumn && $this->_sortOrder)
		{
			$sql .= ' ORDER BY '.$this->_sortColumn.' '.$this->_sortOrder;
		}
		else
		{
			$sql .= ' ORDER BY c.cdate DESC';
		}

		return $sql;
	}

	function getChatSessionsByOperators($operators)
	{
		$sql = "SELECT DISTINCT chat_session_id FROM #__jlc_chat_member
				WHERE operator_id IN (".implode(',', $operators).")
				AND is_accepted = 1;";
		$this->_db->setQuery($sql);

		$results = $this->_db->loadAssocList();

		$chatSessions = array();

		if(!empty($results))
		{
			foreach($results as $row)
			{
				$chatSessions[] = $row['chat_session_id'];
			}
		}

		return $chatSessions;
	}

	function getOperatorsByDepartment($department)
	{
		$sql = "SELECT DISTINCT operator_id FROM #__jlc_operator 
				WHERE department = ".$this->_db->Quote($department);
		$this->_db->setQuery($sql);

		$results = $this->_db->loadAssocList();

		$operatorsInDepartment = array();

		if(!empty($results))
		{
			foreach($results as $row)
			{
				$operatorsInDepartment[] = $row['operator_id'];
			}
		}

		return $operatorsInDepartment;
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

			if(!empty($this->_data))
			{
				foreach($this->_data as $a => $row)
				{
					$this->_data[$a]['chat_session_params'] = json_decode($this->_data[$a]['chat_session_params']);
				}

				$this->_data = $this->addAdditionalData($this->_data);
			}
		}

		return $this->_data;
	}

	function addAdditionalData($data)
	{
		if(!empty($data))
		{
			$allChatSessionIds = array();

			foreach($data as $a => $row)
			{
				$allChatSessionIds[$a] = (int)$row['chat_session_id'];
			}

			$sql = "SELECT
						cm.member_id,
						cm.chat_session_id,
						cm.operator_id,
						cm.member_display_name,
						cm.member_ip_address
					FROM #__jlc_chat_member cm 
					WHERE cm.chat_session_id IN (".implode(',', $allChatSessionIds).")
					AND cm.is_accepted = 1;";
			$this->_db->setQuery($sql);

			$results = $this->_db->loadAssocList();

			if(!empty($results))
			{
				foreach($results as $row)
				{
					$mainArrIndex = array_search((int)$row['chat_session_id'], $allChatSessionIds);

					if(!isset($data[$mainArrIndex]['operators']))
					{
						$data[$mainArrIndex]['operators'] = '';
					}

					if(!isset($data[$mainArrIndex]['client_name']))
					{
						$data[$mainArrIndex]['client_name'] = '';
					}

					if($row['operator_id'] > 0)
					{
						$data[$mainArrIndex]['operators'] .= $row['member_display_name'].', ';
					}
					else
					{
						$data[$mainArrIndex]['client_name'] .= $row['member_display_name'].', ';
					}
				}

				foreach($data as $a => $row)
				{
					$data[$a]['client_name'] = rtrim($row['client_name'], " ,");
					$data[$a]['operators'] = rtrim($row['operators'], " ,");
				}
			}
		}

		return $data;
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

	function deleteHistory()
	{
		$sql = "TRUNCATE TABLE #__jlc_chat;";
		$this->_db->setQuery($sql);
		$this->_db->query();

		$sql = "TRUNCATE TABLE #__jlc_chat_member;";
		$this->_db->setQuery($sql);
		$this->_db->query();

		$sql = "TRUNCATE TABLE #__jlc_chat_proactive;";
		$this->_db->setQuery($sql);
		$this->_db->query();

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->queueForceSync('#__jlc_chat');
		$hostedModeObj->queueForceSync('#__jlc_chat_member');
		$hostedModeObj->forceSyncToRemote();

		return true;
	}

	function getSessionContent($chatSessionId)
	{
		$sql = "SELECT chat_session_content FROM #__jlc_chat 
				WHERE chat_session_id = ".(int)$chatSessionId."
				LIMIT 1";
		$this->_db->setQuery($sql);

		return $this->_db->loadResult();
	}
}