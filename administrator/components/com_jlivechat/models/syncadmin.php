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

class JLiveChatModelSyncAdmin extends CMSModel
{
	var $_settings = null;
	var $_operator = array();
	var $_syncRecord = null;
	var $_systemUUID = null;
	var $_syncPusherEnabled = true;
	var $_allOperatorSyncRecords = array();
	var $_curlMultiHandler = null;
	var $_curlConnections = array();
	var $_operatorResponses = array();
	var $_cronTimeout = 50;

	function __construct()
	{
		$this->JLiveChatModelSyncAdmin();
	}

	function JLiveChatModelSyncAdmin()
	{
		parent::__construct();

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jlcdateadmin.php';

		$this->_settings = & CMSModel::getInstance('SettingAdmin', 'JLiveChatModel');
	}

	function setOperator($operator)
	{
		if(empty($operator)) return false;

		$this->_operator = $operator;
	}

	function setUUID($uuid)
	{
		if(empty($uuid)) return false;

		$this->_systemUUID = $uuid;
	}

	function getOperatorsChecksum()
	{
		$sql = "SELECT SQL_NO_CACHE SUM(mdate)+SUM(last_auth_date) FROM #__jlc_operator;";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	function getMessagesChecksum()
	{
		$sql = "SELECT SQL_NO_CACHE SUM(mdate) FROM #__jlc_message
				WHERE operator_id = ".(int)$this->_operator['operator_id'].";";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	function getVisitorsChecksum()
	{
		$sql = "SELECT SQL_NO_CACHE SUM(visitor_mdate) FROM #__jlc_visitor;";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	function getResponsesChecksum()
	{
		$sql = "SELECT SQL_NO_CACHE SUM(response_mdate) FROM #__jlc_response;";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	function getRoutesChecksum()
	{
		$sql = "SELECT SQL_NO_CACHE SUM(route_mdate) FROM #__jlc_route;";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	function getIPBlockerChecksum()
	{
		$sql = "SELECT SQL_NO_CACHE SUM(mdate) FROM #__jlc_ipblocker
				WHERE operator_id = ".(int)$this->_operator['operator_id'].";";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	function getSettingsChecksum()
	{
		return $this->_settings->getSettingsChecksum();
	}

	function getOperatorChatDataChecksum()
	{
		$dateObj = new JLiveChatModelJLCDateAdmin();

		$sql = "SELECT SQL_NO_CACHE 
					SUM(c.mdate)+SUM(cm.mdate) 
				FROM #__jlc_chat_member cm

				INNER JOIN #__jlc_chat c
				USING(chat_session_id)

				WHERE c.chat_session_id IN (
					SELECT
					DISTINCT(chat_session_id) AS 'chat_session_id'
					FROM #__jlc_chat_member
					WHERE operator_id = ".(int)$this->_operator['operator_id']."
					AND (expire_time > ".$dateObj->toUnix()." OR expire_time IS NULL)
				) AND c.is_active = 1;";
		$this->_db->setQuery($sql);

		return $this->_db->loadResult();
	}

	function getChatChecksum()
	{
		$sql = "SELECT SQL_NO_CACHE
					SUM(c.mdate)
				FROM #__jlc_chat c;";
		$this->_db->setQuery($sql);

		return $this->_db->loadResult();
	}

	function getChatMemberChecksum()
	{
		$sql = "SELECT SQL_NO_CACHE 
					SUM(cm.mdate)
				FROM #__jlc_chat_member cm;";
		$this->_db->setQuery($sql);

		return $this->_db->loadResult();
	}

	function _loadAllOperators()
	{
		$sql = "SELECT 
					os.*,
					o.auth_key,
					o.operator_params
				FROM #__jlc_operator_sync os
				INNER JOIN #__jlc_operator o
				USING(operator_id)
				WHERE is_enabled = 1
				AND os.sync_mode = 'push';";
		$this->_db->setQuery($sql);

		$results = $this->_db->loadAssocList();

		$this->_allOperatorSyncRecords = array();

		if(!empty($results))
		{
			foreach($results as $a => $row)
			{
				if(!empty($row['operator_params'])) $row['operator_params'] = json_decode($row['operator_params']);

				$this->_allOperatorSyncRecords[$row['operator_id']] = $row;
			}
		}
	}
}