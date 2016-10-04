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

class JLiveChatModelSync extends CMSModel 
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
		$this->JLiveChatModelSync();
	}

	function JLiveChatModelSync()
	{
		parent::__construct();

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jlcdate.php';

		$this->_settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');
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

	function _loadSyncRecord()
	{
		if(empty($this->_operator) || empty($this->_systemUUID)) return false;

		$sql = "SELECT * FROM #__jlc_operator_sync 
				WHERE operator_id = ".(int)$this->_operator['operator_id']." 
				AND system_uuid = ".$this->_db->Quote($this->_systemUUID)." 
				LIMIT 1;";
		$this->_db->setQuery($sql);

		$this->_syncRecord = $this->_db->loadAssoc();

		if(empty($this->_syncRecord) || !$this->_syncRecord)
		{
			// No sync record exists, create one
			$dateObj = new JLiveChatModelJLCDate();

			$data = new stdClass();

			$data->operator_id = (int)$this->_operator['operator_id'];
			$data->sync_mode = 'poll';
			$data->operator_ip = JRequest::getVar('REMOTE_ADDR', 'Unknown', 'server');
			$data->system_uuid = $this->_systemUUID;
			$data->cdate = $dateObj->toUnix();
			$data->mdate = $data->cdate;

			$this->_db->insertObject('#__jlc_operator_sync', $data, 'sync_id');

			$sql = "SELECT * FROM #__jlc_operator_sync 
					WHERE operator_id = ".(int)$this->_operator['operator_id']." 
					AND system_uuid = ".$this->_db->Quote($this->_systemUUID)." 
					LIMIT 1;";
			$this->_db->setQuery($sql);

			$this->_syncRecord = $this->_db->loadAssoc();
		}

		return $this->_syncRecord;
	}

	function updateOperatorSyncMethod($syncMode, $operatorIP = null, $operatorListenPort = null)
	{
		if(empty($this->_operator['operator_id']) || empty($this->_systemUUID)) return false;

		$this->_loadSyncRecord();

		$dateObj = new JLiveChatModelJLCDate();

		$data = new stdClass();
		$data->sync_id = $this->_syncRecord['sync_id'];
		$data->operator_id = $this->_operator['operator_id'];
		$data->sync_mode = $syncMode;
		$data->mdate = $dateObj->toUnix();

		if($syncMode == 'push')
		{
			if(empty($operatorIP) || empty($operatorListenPort)) return false;

			$data->operator_ip = $operatorIP;
			$data->operator_port = $operatorListenPort;
		}
		elseif($syncMode == 'poll')
		{
			$data->operator_ip = JRequest::getVar('REMOTE_ADDR', '0.0.0.0', 'server');
			$data->operator_port = null;
		}

		return $this->_db->updateObject('#__jlc_operator_sync', $data, 'sync_id');
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
		$dateObj = new JLiveChatModelJLCDate();

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
	
	function getOperatorDataChecksums()
	{
		$dateObj = new JLiveChatModelJLCDate();
		
		$sql = "SELECT SQL_NO_CACHE (
					SELECT SUM(mdate)+SUM(last_auth_date) FROM #__jlc_operator
				) AS `operators_checksum`, (
					SELECT SUM(mdate) FROM #__jlc_message 
					WHERE operator_id = ".(int)$this->_operator['operator_id']." 
				) AS `messages_checksum`, (
					SELECT SUM(visitor_mdate) FROM #__jlc_visitor
				) AS `visitors_checksum`, (
					SELECT SUM(response_mdate) FROM #__jlc_response 
				) AS `responses_checksum`, (
					SELECT SUM(mdate) FROM #__jlc_ipblocker 
					WHERE operator_id = ".(int)$this->_operator['operator_id']." 
				) AS `ipblocker_checksum`, (
					SELECT SUM(app_mdate) FROM #__cms_app 
					WHERE app_id = ".(int)$this->_settings->getAppId()." 
				) AS `settings_checksum`, (
					SELECT SUM(c.mdate)+SUM(cm.mdate) FROM #__jlc_chat_member cm

					INNER JOIN #__jlc_chat c
					USING(chat_session_id) 

					WHERE c.chat_session_id IN (
						SELECT DISTINCT(chat_session_id) AS 'chat_session_id' FROM #__jlc_chat_member 
						WHERE operator_id = ".(int)$this->_operator['operator_id']." 
						AND (expire_time > ".$dateObj->toUnix()." OR expire_time IS NULL) 
					) AND c.is_active = 1 
				) AS `chats_checksum`;";
		$this->_db->setQuery($sql);
		
		return $this->_db->loadAssoc();
	}

	function getOperatorData()
	{
		if(empty($this->_operator) || empty($this->_systemUUID)) return false;

		$this->_loadSyncRecord();
		
		$dataChecksums = $this->getOperatorDataChecksums();
		
		$operatorsChecksum = $dataChecksums['operators_checksum'];
		$messagesChecksum = $dataChecksums['messages_checksum'];
		$visitorsChecksum = $dataChecksums['visitors_checksum'];
		$responsesChecksum = $dataChecksums['responses_checksum'];
		$ipblockerChecksum = $dataChecksums['ipblocker_checksum'];
		$settingsChecksum = $dataChecksums['settings_checksum'];
		$chatDataChecksum = $dataChecksums['chats_checksum'];

		$newSyncData = new stdClass();
		$operatorData = array();

		if($operatorsChecksum != $this->_syncRecord['operators_checksum'])
		{
			// There has been a change to the data, include it
			$operatorData['operators'] = array();
			$newSyncData->operators_checksum = $operatorsChecksum;
		}

		if($messagesChecksum != $this->_syncRecord['messages_checksum'])
		{
			// There has been a change to the data, include it
			$operatorData['messages'] = array();
			$newSyncData->messages_checksum = $messagesChecksum;
		}

		if($visitorsChecksum != $this->_syncRecord['visitors_checksum'])
		{
			// There has been a change to the data, include it
			$operatorData['visitors'] = array();
			$newSyncData->visitors_checksum = $visitorsChecksum;
		}

		if($responsesChecksum != $this->_syncRecord['responses_checksum'])
		{
			// There has been a change to the data, include it
			$operatorData['responses'] = array();
			$newSyncData->responses_checksum = $responsesChecksum;
		}

		if($chatDataChecksum != $this->_syncRecord['chat_checksum'])
		{
			// There has been a change to the data, include it
			$operatorData['chat_data'] = array();
			$newSyncData->chat_checksum = $chatDataChecksum;
		}

		if($ipblockerChecksum != $this->_syncRecord['ipblocker_checksum'])
		{
			// There has been a change to the data, include it
			$operatorData['ipblocker'] = array();
			$newSyncData->ipblocker_checksum = $ipblockerChecksum;
		}

		if($settingsChecksum != $this->_syncRecord['settings_checksum'])
		{
			// There has been a change to the data, include it
			$operatorData['settings'] = array();
			$newSyncData->settings_checksum = $settingsChecksum;
			$settingsHasChanged = true;
		}
		else
		{
			$settingsHasChanged = false;
		}

		if(isset($operatorData['chat_data']))
		{
			require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'popup.php';

			$popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');
			$operatorData['chat_data'] = $popup->getChatDataForOperator($this->_operator['operator_id']);
		}

		if(isset($operatorData['responses']))
		{
			require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'response.php';

			$responseObj = & CMSModel::getInstance('Response', 'JLiveChatModel');
			$operatorData['responses'] = $responseObj->getAllActiveResponses();
		}

		if(is_string($this->_operator['operator_params']))
		{
			$operatorData['settings'] = json_decode($this->_operator['operator_params']);
		}
		else
		{
			$operatorData['settings'] = $this->_operator['operator_params'];
		}

		$operatorData['settings']->operator_id = $this->_operator['operator_id'];
		$operatorData['settings']->site_name = $this->_settings->getSiteName();
		$operatorData['settings']->hosted_mode = $this->_settings->isHostedMode();

		// Push Notification Settings
		$operatorData['settings']->use_pushservice = $this->_settings->getSetting('use_pushservice');
		$operatorData['settings']->push_service_acct_number = $this->_settings->getSetting('push_service_acct_number');

		if($operatorData['settings']->ipblocker == 1 && isset($operatorData['ipblocker']))
		{
			require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'ipblocker.php';

			$ipblockerObj = & CMSModel::getInstance('IPBlocker', 'JLiveChatModel');
			$operatorData['ipblocker'] = $ipblockerObj->getOperatorRules($this->_operator['operator_id']);
		}

		if($this->_settings->getSetting('activity_monitor') == 0) $operatorData['settings']->website_monitor = 0;
		if($this->_settings->getSetting('operator2operator') == 0) $operatorData['settings']->operator2operator = 0;
		if($this->_settings->getSetting('enable_messaging') != 'y') $operatorData['settings']->messages = 0;

		if($this->_settings->getSetting('enable_messaging') == 'y' && $operatorData['settings']->messages > 0 && isset($operatorData['messages']))
		{
			require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'message.php';

			$messageObj = & CMSModel::getInstance('Message', 'JLiveChatModel');
			$operatorData['messages'] = $messageObj->getOperatorMessages($this->_operator['operator_id']);
		}

		if((bool)$this->_settings->getSetting('activity_monitor') && $operatorData['settings']->website_monitor == 1 && isset($operatorData['visitors']))
		{
			require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'visitor.php';

			$visitor = & CMSModel::getInstance('Visitor', 'JLiveChatModel');
			$visitor->expireOldVisitorRecords();

			$operatorData['visitors'] = $visitor->getActivity();
		}

		if((bool)$this->_settings->getSetting('operator2operator') && $operatorData['settings']->operator2operator == 1 && isset($operatorData['operators']))
		{
			require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'operator.php';

			$operatorObj = & CMSModel::getInstance('Operator', 'JLiveChatModel');
			$operatorData['operators'] = $operatorObj->getOperators(true, false);
		}

		// If Settings hasn't changed, dont send it
		if(!$settingsHasChanged) unset($operatorData['settings']);

		$newSyncDataVars = get_object_vars($newSyncData);

		if(!empty($newSyncDataVars))
		{
			$dateObj = new JLiveChatModelJLCDate();

			$newSyncData->sync_id = $this->_syncRecord['sync_id'];
			$newSyncData->mdate = $dateObj->toUnix();

			$this->_db->updateObject('#__jlc_operator_sync', $newSyncData, 'sync_id');
		}

		return $operatorData;
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
				WHERE o.is_enabled = 1
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

	function startSyncPusher()
	{
		if(!function_exists('curl_init'))
		{
			echo "You do not have PHP cURL installed! cURL is required. Exiting now...\r\n";
			return false;
		}

		$dateObj = new JLiveChatModelJLCDate();
		$cronLastExecuteTime = $this->_settings->getSetting('cron_last_execute_time');

		if($cronLastExecuteTime > ($dateObj->toUnix() - $this->_cronTimeout) && $cronLastExecuteTime)
		{
			echo "JLive! Chat cron already running! Exiting now...\r\n";
			echo "NOTE: Please allow ".$this->_cronTimeout." seconds between starting and stopping this script.\r\n";
			echo "You will be able to start this script in ".($this->_cronTimeout - ($dateObj->toUnix() - $cronLastExecuteTime))." seconds.\r\n";
			return false;
		}

		while($this->_syncPusherEnabled)
		{
			$this->_loadAllOperators();

			if(!empty($this->_allOperatorSyncRecords))
			{
				foreach($this->_allOperatorSyncRecords as $a => $row)
				{
					$this->queueOperatorDataPacket($row['operator_id']);
				}

				$this->_blastPackets();
			}

			$dateObj = new JLiveChatModelJLCDate();

			$this->_settings->refreshSettings();
			$this->_settings->setSetting('cron_last_execute_time', $dateObj->toUnix());
			$this->_settings->saveSettings();

			sleep(2);
		}
	}

	function queueOperatorDataPacket($operatorId)
	{
		if(!isset($this->_allOperatorSyncRecords[$operatorId])) return false;

		if(function_exists('curl_multi_init'))
		{
			// Using PHP 5 with cURL multi-handler enabled
			if(!$this->_curlMultiHandler) $this->_curlMultiHandler = curl_multi_init();
		}

		$useHTTPS = false;

		if(is_object($this->_allOperatorSyncRecords[$operatorId]['operator_params']))
		{
			if(isset($this->_allOperatorSyncRecords[$operatorId]['operator_params']->use_ssl))
			{
				if($this->_allOperatorSyncRecords[$operatorId]['operator_params']->use_ssl == 1)
				{
					$useHTTPS = true;
				}
			}
		}

		if($useHTTPS)
		{
			$operatorServerUri = 'https://'.$this->_allOperatorSyncRecords[$operatorId]['operator_ip'].':'.($this->_allOperatorSyncRecords[$operatorId]['operator_port'] + 1);
		}
		else
		{
			$operatorServerUri = 'http://'.$this->_allOperatorSyncRecords[$operatorId]['operator_ip'].':'.$this->_allOperatorSyncRecords[$operatorId]['operator_port'];
		}

		if(!isset($this->_curlConnections[$operatorId]))
		{
			$this->_curlConnections[$operatorId] = curl_init($operatorServerUri);

			curl_setopt($this->_curlConnections[$operatorId], CURLOPT_URL, $operatorServerUri);
			curl_setopt($this->_curlConnections[$operatorId], CURLOPT_RETURNTRANSFER, 1); //return data as string
			curl_setopt($this->_curlConnections[$operatorId], CURLOPT_FOLLOWLOCATION, 1); //follow redirects
			curl_setopt($this->_curlConnections[$operatorId], CURLOPT_MAXREDIRS, 2); //maximum redirects
			curl_setopt($this->_curlConnections[$operatorId], CURLOPT_CONNECTTIMEOUT, 5); //timeout
			curl_setopt($this->_curlConnections[$operatorId], CURLOPT_HEADER, 0);
			curl_setopt($this->_curlConnections[$operatorId], CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($this->_curlConnections[$operatorId], CURLOPT_HTTPHEADER, array('Expect:'));

			$this->_operator = array();

			$this->_operator['operator_id'] = $operatorId;
			$this->_operator['operator_params'] = $this->_allOperatorSyncRecords[$operatorId]['operator_params'];
			$this->_operator['auth_key'] = $this->_allOperatorSyncRecords[$operatorId]['auth_key'];

			$this->_syncRecord = $this->_allOperatorSyncRecords[$operatorId];

			$operatorData = $this->getOperatorData();

			$operatorData['auth_key'] = $this->_operator['auth_key'];

			curl_setopt($this->_curlConnections[$operatorId], CURLOPT_POST, 1);
			curl_setopt($this->_curlConnections[$operatorId], CURLOPT_POSTFIELDS, json_encode($operatorData));

			if($this->_settings->getSetting('use_proxy') == 1)
			{
				// Use proxy server
				curl_setopt($this->_curlConnections[$operatorId], CURLOPT_PROXY, $this->_settings->getSetting('proxy_uri'));

				$proxyAuth = $this->_settings->getSetting('proxy_auth');

				if($this->_settings->getSetting('proxy_port') > 0) curl_setopt($this->_curlConnections[$operatorId], CURLOPT_PROXYPORT, $this->_settings->getSetting('proxy_port'));
				if(!empty($proxyAuth)) curl_setopt($this->_curlConnections[$operatorId], CURLOPT_PROXYUSERPWD, $this->_settings->getSetting('proxy_auth'));
				if($this->_settings->getSetting('use_socks') > 0) curl_setopt($this->_curlConnections[$operatorId], CURLOPT_PROXYTYPE, 5);
			}

			if(function_exists('curl_multi_init'))
			{
				// PHP 5
				curl_multi_add_handle($this->_curlMultiHandler, $this->_curlConnections[$operatorId]);
			}
		}
	}

	function _blastPackets()
	{
		if(empty($this->_curlConnections)) return false;

		if(function_exists('curl_multi_init'))
		{
			// PHP 5
			do {
				$n = curl_multi_exec($this->_curlMultiHandler, $active);
			} while($active);
		}

		foreach($this->_curlConnections as $operatorId => $curlObj)
		{
			if(function_exists('curl_multi_init'))
			{
				// PHP 5
				$this->_operatorResponses[$operatorId] = curl_multi_getcontent($curlObj);

				curl_multi_remove_handle($this->_curlMultiHandler, $curlObj);
			}
			else
			{
				// PHP 4
				$this->_operatorResponses[$operatorId] = curl_exec($curlObj);
			}

			curl_close($curlObj);
		}

		if(function_exists('curl_multi_init'))
		{
			// PHP 5
			curl_multi_close($this->_curlMultiHandler);

			$this->_curlMultiHandler = null;
		}

		$this->_curlConnections = array();

		$this->_parseOperatorResponses();
	}

	function _parseOperatorResponses()
	{
		if(!empty($this->_operatorResponses))
		{
			$popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

			foreach($this->_operatorResponses as $operatorId => $operatorResponse)
			{
				if(!empty($operatorResponse))
				{
					$operatorObj = & CMSModel::getInstance('Operator', 'JLiveChatModel');

					$operatorResponse = json_decode($operatorResponse);

					if(!is_object($operatorResponse)) continue;

					if(!isset($operatorResponse->k) || !isset($operatorResponse->currently_online) || !isset($operatorResponse->operator_name) || !isset($operatorResponse->operator_accept_timeout) || !isset($operatorResponse->typing_status))
					{
						// Skip this record, because its missing info
						continue;
					}

					$operator = $operatorObj->isKeyValid($operatorResponse->k, (int)$operatorResponse->currently_online, $operatorResponse->operator_name, (int)$operatorResponse->operator_accept_timeout, $this->_allOperatorSyncRecords[$operatorId]['operator_ip']);
					$operatorObj->applyPendingChanges();

					if(!empty($operatorResponse->typing_status))
					{
						$operatorResponse->typing_status = explode(',', $operatorResponse->typing_status);
					}

					$popup->updateOperatorTypingStatus($operatorId, $operatorResponse->typing_status);
				}
			}

			$this->_operatorResponses = array();
		}
	}

	function outputBuffer($buffer)
	{
		echo $buffer;
	}
}
