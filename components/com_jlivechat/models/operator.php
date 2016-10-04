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

class JLiveChatModelOperator extends CMSModel
{

	var $_settings = null;
	var $_pendingUpdates = array();

	function __construct()
	{
		$this->JLiveChatModelOperator();
	}

	function JLiveChatModelOperator()
	{
		parent::__construct();

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jlcdate.php';

		$this->_settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');
	}

	function getDepartments()
	{
		$sql = "SELECT
		    department,
		    last_auth_date 
		FROM #__jlc_operator 
		WHERE is_enabled = 1
		AND department IS NOT NULL
		AND department != '' 
		ORDER BY department ASC, last_auth_date DESC;";
		$this->_db->setQuery($sql);

		$results = $this->_db->loadAssocList();

		$final = array();

		if(!empty($results))
		{
			foreach($results as $row)
			{
				if(isset($final[$row['department']])) continue;

				$final[$row['department']] = $row;
			}
		}

		return $final;
	}

	function getOperators($onlineOnly = false, $order = true)
	{
		$sql = "SELECT
		    o.operator_id,
		    o.operator_name,
		    o.alt_name,
		    o.department,
		    o.accept_chat_timeout,
		    o.sort_order,
		    o.operator_params,
		    o.is_enabled,
		    o.cdate,
		    o.mdate,
		    o.last_auth_date
		FROM #__jlc_operator o 
		WHERE o.is_enabled = 1";

		if($onlineOnly)
		{
			$date = new JLiveChatModelJLCDate();

			require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'popup.php';

			$popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

			$offlineTime = $date->toUnix() - $popup->getOfflineSeconds();

			$sql .= ' AND o.last_auth_date >= '.$offlineTime;
		}

		if($order) $sql .= ' ORDER BY o.operator_name ASC';

		$this->_db->setQuery($sql);

		return $this->_db->loadAssocList();
	}

	function getOperator($operatorId)
	{
		$sql = "SELECT
		    o.operator_id,
		    o.operator_name,
		    o.alt_name,
		    o.department,
		    o.accept_chat_timeout,
		    o.sort_order,
		    o.operator_params,
		    o.is_enabled,
		    o.cdate,
		    o.mdate,
		    o.last_auth_date
		FROM #__jlc_operator o 
		WHERE o.operator_id = ".(int)$operatorId." 
		LIMIT 1;";
		$this->_db->setQuery($sql);

		$result = $this->_db->loadAssoc();

		if(strlen($result['operator_params']) > 1)
		{
			$result['operator_params'] = json_decode($result['operator_params']);
		}
		else
		{
			$result['operator_params'] = new stdClass();
		}

		return $result;
	}

	function isKeyValid($authKey, $isOnline = null, $operatorName = null, $operatorAcceptTimeout = null, $remoteIP = null)
	{
		$date = new JLiveChatModelJLCDate();

		$nowUnixTime = $date->toUnix();
		$lastAuthTime = $nowUnixTime + JRequest::getInt('add_online_time', 0, 'method');

		if(empty($remoteIP)) $remoteIP = JRequest::getVar('REMOTE_ADDR', '0.0.0.0', 'server');

		if(strlen($authKey) == 15)
		{
			// This is a mobile user
			$whereClause = 'UPPER(SUBSTRING(o.auth_key, LENGTH(o.auth_key)*-1, 15))';

			// Register for mobile notifications if enabled
			if(!$this->_settings->getSetting('has_mobile_users'))
			{
				$this->_settings->setSetting('has_mobile_users', true);
				$this->_settings->saveSettings();
			}

			if((int)$this->_settings->getSetting('use_pushservice') == 1 && !$this->_settings->getSetting('push_service_acct_number'))
			{
				require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'pushclient.php';

				$pushClient = & CMSModel::getInstance('PushClient', 'JLiveChatModel');
				$pushClient->registerWebsite();
			}
		}
		else
		{
			// This is the desktop app
			$whereClause = 'o.auth_key';
		}

		$sql = "SELECT SQL_CACHE
		    o.operator_id,
		    o.operator_name,
		    o.alt_name,
		    o.department,
		    o.accept_chat_timeout,
		    o.sort_order,
		    o.operator_params,
		    o.is_enabled,
		    o.cdate,
		    o.mdate,
		    o.last_auth_date
		FROM #__jlc_operator o
		WHERE ".$whereClause." = ".$this->_db->Quote($authKey)."
		AND o.is_enabled = 1
		LIMIT 1;";
		$this->_db->setQuery($sql);

		$operator = $this->_db->loadAssoc();

		if(isset($operator['operator_id']))
		{
			$somethingChanged = false;
			$operatorParams = json_decode($operator['operator_params']);

			if(isset($operatorParams->operator_ip))
			{
				if($operatorParams->operator_ip != $remoteIP)
				{
					$operatorParams->operator_ip = $remoteIP;
					$somethingChanged = true;
				}
			}
			else
			{
				$operatorParams->operator_ip = $remoteIP;
				$somethingChanged = true;
			}

			$updateRecord = false;

			$data = new stdClass();

			$data->operator_id = $operator['operator_id'];

			if($somethingChanged)
			{
				$data->operator_params = json_encode($operatorParams);
				$updateRecord = true;
			}

			if($operatorName && $operatorAcceptTimeout && ($operatorName != $operator['operator_name'] || (int)$operatorAcceptTimeout != $operator['accept_chat_timeout']))
			{
				// Operator record has changed, update the mdate
				$data->operator_name = $operatorName;
				$data->accept_chat_timeout = (int)$operatorAcceptTimeout;
				$data->mdate = $nowUnixTime;
				$updateRecord = true;
			}

			$timeLeftOnline = $operator['last_auth_date'] - $nowUnixTime;

			// Operator is available online
			if((int)$isOnline > 0)
			{
				$data->last_auth_date = $lastAuthTime;
				$data->mdate = $nowUnixTime;
				$updateRecord = true;
			}
			elseif($timeLeftOnline > 60)
			{
				// Still has over 1 minutes left
				// This user is still online when they shouldn't be, log'em off
				$data->last_auth_date = $nowUnixTime - 65;
				$data->mdate = $nowUnixTime;
				$updateRecord = true;
			}

			if($updateRecord) $this->_pendingUpdates[] = $data; // Something changed, update record
		}

		return $operator;
	}

	function applyPendingChanges()
	{
		if(!empty($this->_pendingUpdates))
		{
			// Perform updates last for performance reasons
			foreach($this->_pendingUpdates as $data)
			{
				$this->_db->updateObject('#__jlc_operator', $data, 'operator_id');
			}

			$this->_pendingUpdates = array();
		}
	}
}