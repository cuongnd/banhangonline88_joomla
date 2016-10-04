<?php
/**
 * @package JAdmin!
 * @version 1.5.4.3
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

class JAdminModelSync extends CMSModel
{
	var $_settings = null;
	var $_user = array();
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
		$this->JAdminModelSync();
	}

	function JAdminModelSync()
	{
		parent::__construct();

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jadmdate.php';

		$this->_settings = & CMSModel::getInstance('Setting', 'JAdminModel');
	}

	function setUser($user)
	{
		if(empty($user)) return false;

		$this->_user = $user;
	}

	function setUUID($uuid)
	{
		if(empty($uuid)) return false;

		$this->_systemUUID = $uuid;
	}

	function _loadSyncRecord()
	{
		if(empty($this->_user) || empty($this->_systemUUID)) return false;

		$sql = "SELECT * FROM #__jadm_user_sync
		WHERE user_id = ".(int)$this->_user['user_id']."
		AND system_uuid = ".$this->_db->Quote($this->_systemUUID)." 
		LIMIT 1;";
		$this->_db->setQuery($sql);

		$this->_syncRecord = $this->_db->loadAssoc();

		if(empty($this->_syncRecord) || !$this->_syncRecord)
		{
			// No sync record exists, create one
			$dateObj = new JAdminModelJADMDate();

			$data = new stdClass();

			$data->user_id = (int)$this->_user['user_id'];
			$data->sync_mode = 'poll';
			$data->user_ip = JRequest::getVar('REMOTE_ADDR', 'Unknown', 'server');
			$data->system_uuid = $this->_systemUUID;
			$data->cdate = $dateObj->toUnix();
			$data->mdate = $data->cdate;

			$this->_db->insertObject('#__jadm_user_sync', $data, 'sync_id');

			$sql = "SELECT * FROM #__jadm_user_sync 
		    WHERE user_id = ".(int)$this->_user['user_id']."
		    AND system_uuid = ".$this->_db->Quote($this->_systemUUID)."
		    LIMIT 1;";
			$this->_db->setQuery($sql);

			$this->_syncRecord = $this->_db->loadAssoc();
		}

		return $this->_syncRecord;
	}

	function updateOperatorSyncMethod($syncMode, $operatorIP = null, $operatorListenPort = null)
	{
		if(empty($this->_user['user_id']) || empty($this->_systemUUID)) return false;

		$this->_loadSyncRecord();

		$dateObj = new JAdminModelJADMDate();

		$data = new stdClass();
		$data->sync_id = $this->_syncRecord['sync_id'];
		$data->user_id = $this->_user['user_id'];
		$data->sync_mode = $syncMode;
		$data->mdate = $dateObj->toUnix();

		if($syncMode == 'push')
		{
			if(empty($operatorIP) || empty($operatorListenPort)) return false;

			$data->user_ip = $operatorIP;
			$data->user_port = $operatorListenPort;
		}
		elseif($syncMode == 'poll')
		{
			$data->user_ip = JRequest::getVar('REMOTE_ADDR', '0.0.0.0', 'server');
			$data->user_port = null;
		}

		if($this->_syncRecord)
		{
			// Record already exists for this operator
			return $this->_db->updateObject('#__jadm_user_sync', $data, 'sync_id');
		}
		else
		{
			// Record doesn't already exists for this operator
			$data->cdate = $dateObj->toUnix();

			return $this->_db->insertObject('#__jadm_user_sync', $data, 'sync_id');
		}
	}

	function getUsersChecksum()
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'user.php';

		$model = & CMSModel::getInstance('User', 'JAdminModel');

		return md5(json_encode($model->getAll()));
	}

	function getMenusChecksum()
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'menus.php';

		$model = & CMSModel::getInstance('Menus', 'JAdminModel');

		return md5(json_encode($model->getAll()));
	}

	function getComponentsChecksum()
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'components.php';

		$model = & CMSModel::getInstance('Components', 'JAdminModel');

		return md5(json_encode($model->getAll()));
	}

	function getVisitorsChecksum()
	{
		$sql = "SELECT SQL_NO_CACHE SUM(visitor_mdate) FROM #__jadm_visitor;";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	function getIPBlockerChecksum()
	{
		$sql = "SELECT SQL_NO_CACHE SUM(mdate) FROM #__jadm_ipblocker
		WHERE user_id = ".(int)$this->_user['user_id'].";";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	function getSettingsChecksum()
	{
		return $this->_settings->getSettingsChecksum();
	}

	function getUserData()
	{
		if(empty($this->_user) || empty($this->_systemUUID)) return false;

		$this->_loadSyncRecord();

		$visitorsChecksum = $this->getVisitorsChecksum();
		$ipblockerChecksum = $this->getIPBlockerChecksum();
		$settingsChecksum = $this->getSettingsChecksum();
		$menusChecksum = $this->getMenusChecksum();
		$componentsChecksum = $this->getComponentsChecksum();
		$usersChecksum = $this->getUsersChecksum();

		$newSyncData = new stdClass();
		$operatorData = array();

		if($visitorsChecksum != $this->_syncRecord['visitors_checksum'])
		{
			// There has been a change to the data, include it
			$operatorData['visitors'] = array();
			$newSyncData->visitors_checksum = $visitorsChecksum;
		}

		if($ipblockerChecksum != $this->_syncRecord['ipblocker_checksum'])
		{
			// There has been a change to the data, include it
			$operatorData['ipblocker'] = array();
			$newSyncData->ipblocker_checksum = $ipblockerChecksum;
		}

		if($usersChecksum != $this->_syncRecord['users_checksum'])
		{
			// There has been a change to the data, include it
			$operatorData['users'] = array();
			$newSyncData->users_checksum = $usersChecksum;
		}

		if($componentsChecksum != $this->_syncRecord['components_checksum'])
		{
			// There has been a change to the data, include it
			$operatorData['components'] = array();
			$newSyncData->components_checksum = $componentsChecksum;
		}

		if($menusChecksum != $this->_syncRecord['menus_checksum'])
		{
			// There has been a change to the data, include it
			$operatorData['menus'] = array();
			$newSyncData->menus_checksum = $menusChecksum;
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

		if(is_string($this->_user['params']))
		{
			$operatorData['settings'] = json_decode($this->_user['params']);
		}
		else
		{
			$operatorData['settings'] = $this->_user['params'];
		}

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'user.php';

		$userObj = & CMSModel::getInstance('User', 'JAdminModel');

		$operatorData['settings']->user_id = $this->_user['user_id'];
		$operatorData['settings']->site_name = $this->_settings->getSiteName();
		$operatorData['settings']->available_permissions = $userObj->getAvailablePermissions();

		if($operatorData['settings']->ipblocker == 1 && isset($operatorData['ipblocker']))
		{
			$ipblockerObj = & CMSModel::getInstance('IPBlocker', 'JAdminModel');
			$operatorData['ipblocker'] = $ipblockerObj->getUserRules($this->_user['user_id']);
		}

		// Check if this user has User permissions
		if(in_array(0, $operatorData['settings']->permissions) && isset($operatorData['users']))
		{
			$operatorData['users'] = $userObj->getAll();
		}

		// Check if this user has Menu permissions
		if(in_array(3, $operatorData['settings']->permissions) && isset($operatorData['menus']))
		{
			require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'menus.php';

			$menusObj = & CMSModel::getInstance('Menus', 'JAdminModel');

			$operatorData['menus'] = $menusObj->getAll();
		}

		if(isset($operatorData['components']))
		{
			require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'components.php';

			$componentsObj = & CMSModel::getInstance('Components', 'JAdminModel');

			$operatorData['components'] = $componentsObj->getAll();
		}

		if($this->_settings->getSetting('activity_monitor') == 0)
		{
			$operatorData['settings']->website_monitor = 0;
		}

		if($this->_settings->getSetting('activity_monitor') > 0 && $operatorData['settings']->website_monitor == 1 && isset($operatorData['visitors']))
		{
			require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'visitor.php';

			$visitor = & CMSModel::getInstance('Visitor', 'JAdminModel');
			$operatorData['visitors'] = $visitor->getActivity();
		}

		// If Settings hasn't changed, dont send it
		if(!$settingsHasChanged) unset($operatorData['settings']);

		$newSyncDataVars = get_object_vars($newSyncData);

		if(!empty($newSyncDataVars))
		{
			$dateObj = new JAdminModelJADMDate();
			$newSyncData->sync_id = $this->_syncRecord['sync_id'];
			$newSyncData->mdate = $dateObj->toUnix();
			$this->_db->updateObject('#__jadm_user_sync', $newSyncData, 'sync_id');
		}

		return $operatorData;
	}

	function _loadAllOperators()
	{
		$sql = "SELECT 
		    os.*,
		    o.auth_key,
		    o.params
		FROM #__jadm_user_sync os
		INNER JOIN #__jadm_user o
		USING(user_id)
		WHERE o.is_enabled = 1
		AND os.sync_mode = 'push';";
		$this->_db->setQuery($sql);

		$results = $this->_db->loadAssocList();

		$this->_allOperatorSyncRecords = array();

		if(!empty($results))
		{
			foreach($results as $a => $row)
			{
				if(!empty($row['params']))
				{
					$row['params'] = json_decode($row['params']);
				}

				$this->_allOperatorSyncRecords[$row['user_id']] = $row;
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

		$dateObj = new JAdminModelJADMDate();
		$cronLastExecuteTime = $this->_settings->getSetting('cron_last_execute_time');

		if($cronLastExecuteTime > ($dateObj->toUnix() - $this->_cronTimeout) && $cronLastExecuteTime)
		{
			echo "JAdmin! cron already running! Exiting now...\r\n";
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
					$this->queueOperatorDataPacket($row['user_id']);
				}

				$this->_blastPackets();
			}

			$dateObj = new JAdminModelJADMDate();

			$this->_settings->refreshSettings();
			$this->_settings->setSetting('cron_last_execute_time', $dateObj->toUnix());
			$this->_settings->saveSettings();

			sleep(2);
		}
	}

	function queueOperatorDataPacket($userId)
	{
		if(!isset($this->_allOperatorSyncRecords[$userId])) return false;

		if(function_exists('curl_multi_init'))
		{
			// Using PHP 5 with cURL multi-handler enabled
			if(!$this->_curlMultiHandler) $this->_curlMultiHandler = curl_multi_init();
		}

		$useHTTPS = false;

		if(is_object($this->_allOperatorSyncRecords[$userId]['params']))
		{
			if(isset($this->_allOperatorSyncRecords[$userId]['params']->use_ssl))
			{
				if($this->_allOperatorSyncRecords[$userId]['params']->use_ssl == 1)
				{
					$useHTTPS = true;
				}
			}
		}

		if($useHTTPS)
		{
			$userServerUri = 'https://'.$this->_allOperatorSyncRecords[$userId]['user_ip'].':'.($this->_allOperatorSyncRecords[$userId]['user_port'] + 1);
		}
		else
		{
			$userServerUri = 'http://'.$this->_allOperatorSyncRecords[$userId]['user_ip'].':'.$this->_allOperatorSyncRecords[$userId]['user_port'];
		}

		if(!isset($this->_curlConnections[$userId]))
		{
			$this->_curlConnections[$userId] = curl_init($userServerUri);

			curl_setopt($this->_curlConnections[$userId], CURLOPT_URL, $userServerUri);
			curl_setopt($this->_curlConnections[$userId], CURLOPT_RETURNTRANSFER, 1); //return data as string
			curl_setopt($this->_curlConnections[$userId], CURLOPT_FOLLOWLOCATION, 1); //follow redirects
			curl_setopt($this->_curlConnections[$userId], CURLOPT_MAXREDIRS, 2); //maximum redirects
			curl_setopt($this->_curlConnections[$userId], CURLOPT_CONNECTTIMEOUT, 3); //timeout
			curl_setopt($this->_curlConnections[$userId], CURLOPT_HEADER, 0);
			curl_setopt($this->_curlConnections[$userId], CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($this->_curlConnections[$userId], CURLOPT_HTTPHEADER, array('Expect:'));

			$this->_user = array();

			$this->_user['user_id'] = $userId;
			$this->_user['params'] = $this->_allOperatorSyncRecords[$userId]['params'];
			$this->_user['auth_key'] = $this->_allOperatorSyncRecords[$userId]['auth_key'];

			$this->_syncRecord = $this->_allOperatorSyncRecords[$userId];

			$operatorData = $this->getUserData();

			$operatorData['auth_key'] = $this->_user['auth_key'];

			curl_setopt($this->_curlConnections[$userId], CURLOPT_POST, 1);
			curl_setopt($this->_curlConnections[$userId], CURLOPT_POSTFIELDS, json_encode($operatorData));

			if($this->_settings->getSetting('use_proxy') == 1)
			{
				// Use proxy server
				curl_setopt($this->_curlConnections[$userId], CURLOPT_PROXY, $this->_settings->getSetting('proxy_uri'));

				$proxyAuth = $this->_settings->getSetting('proxy_auth');

				if($this->_settings->getSetting('proxy_port') > 0) curl_setopt($this->_curlConnections[$userId], CURLOPT_PROXYPORT, $this->_settings->getSetting('proxy_port'));
				if(!empty($proxyAuth)) curl_setopt($this->_curlConnections[$userId], CURLOPT_PROXYUSERPWD, $this->_settings->getSetting('proxy_auth'));
				if($this->_settings->getSetting('use_socks') > 0) curl_setopt($this->_curlConnections[$userId], CURLOPT_PROXYTYPE, 5);
			}

			if(function_exists('curl_multi_init'))
			{
				// PHP 5
				curl_multi_add_handle($this->_curlMultiHandler, $this->_curlConnections[$userId]);
			}
		}
	}

	function _blastPackets()
	{
		if(empty($this->_curlConnections)) return false;

		if(function_exists('curl_multi_init'))
		{
			// PHP 5
			do
			{
				$n = curl_multi_exec($this->_curlMultiHandler, $active);
			}
			while($active);
		}

		foreach($this->_curlConnections as $userId => $curlObj)
		{
			if(function_exists('curl_multi_init'))
			{
				// PHP 5
				$this->_operatorResponses[$userId] = curl_multi_getcontent($curlObj);

				curl_multi_remove_handle($this->_curlMultiHandler, $curlObj);
			}
			else
			{
				// PHP 4
				$this->_operatorResponses[$userId] = curl_exec($curlObj);
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
			foreach($this->_operatorResponses as $operatorId => $operatorResponse)
			{
				if(!empty($operatorResponse))
				{
					$operatorObj = & CMSModel::getInstance('User', 'JAdminModel');

					$operatorResponse = json_decode($operatorResponse);

					if(!is_object($operatorResponse)) continue;

					// Skip this record, because its missing info
					if(!isset($operatorResponse->k)) continue;

					$operator = $operatorObj->isKeyValid($operatorResponse->k, $this->_allOperatorSyncRecords[$operatorId]['user_ip']);
					$operatorObj->applyPendingChanges();
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
