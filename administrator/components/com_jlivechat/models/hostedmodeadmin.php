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

class JLiveChatModelHostedModeAdmin extends CMSModel
{
	var $_settings = null;
	var $_apiKey = null;
	var $_apiUri = 'https://www.ultimatelivechat.com/index.php?option=com_ultimatelivechat&view=api&format=raw';
	var $_allowedTables = array(
		'#__cms_app',
		'#__jlc_chat',
		'#__jlc_chat_member',
		'#__jlc_operator',
		'#__jlc_response',
		'#__jlc_route'
	);
	var $_forceSyncArray = array();

	function __construct()
	{
		$this->JLiveChatModelHostedModeAdmin();
	}

	function JLiveChatModelHostedModeAdmin()
	{
		parent::__construct();

		$mainframe = & JFactory::getApplication();

		$this->_settings = & CMSModel::getInstance('SettingAdmin', 'JLiveChatModel');

		$this->_apiKey = $this->_settings->getSetting('hosted_mode_api_key');

		$oldHostedeModeAPIKeyCheck = $this->_settings->getSetting('old_hosted_mode_api_key');

		if(!empty($oldHostedeModeAPIKeyCheck))
		{
			// DO last sync and then remove hosted mode info
			$this->_apiKey = $oldHostedeModeAPIKeyCheck;

			$this->_settings->setSetting('old_hosted_mode_api_key', null);
			$this->_settings->saveSettings();
		}

		// Check if we are being executed on UltimateLiveChat.com
		if(strpos($mainframe->getCfg('tmp_path'), 'ultimatelivechat.com') !== FALSE)
		{
			$this->_apiKey = null;
		}
	}

	function refreshSettings()
	{
		$this->_settings->refreshSettings();
	}

	function sync()
	{
		$mainframe = & JFactory::getApplication();

		// Check if in hosted mode
		if(empty($this->_apiKey) || strpos($mainframe->getCfg('tmp_path'), 'ultimatelivechat.com') !== FALSE) return false;

		$sync = & CMSModel::getInstance('SyncAdmin', 'JLiveChatModel');

		$packet = array();

		$packet['k'] = $this->_apiKey;
		$packet['task'] = 'compare_datasets';

		$packet['#__cms_app'] = $sync->getSettingsChecksum();
		$packet['#__jlc_chat'] = $sync->getChatChecksum();
		$packet['#__jlc_chat_member'] = $sync->getChatMemberChecksum();
		$packet['#__jlc_operator'] = $sync->getOperatorsChecksum();
		$packet['#__jlc_response'] = $sync->getResponsesChecksum();
		$packet['#__jlc_route'] = $sync->getRoutesChecksum();

		$response = $this->doPOST($this->_apiUri, $packet, 'JLive! Chat');
		$response = json_decode($response);

		$localDatasets = array();
		$requestDatasets = array();

		if(is_object($response))
		{
			$tables = get_object_vars($response);

			if(!empty($tables))
			{
				foreach($tables as $tableName => $syncMethod)
				{
					if($syncMethod == 'get')
					{
						// Local dataset is newer
						$localDatasets[$tableName] = $this->getLocalDataset($tableName);
					}
					elseif($syncMethod == 'put')
					{
						// Remote dataset is newer
						$requestDatasets[] = $tableName;
					}
				}
			}
		}

		if(!empty($localDatasets) || !empty($requestDatasets))
		{
			$packet = array();

			$packet['k'] = $this->_apiKey;
			$packet['task'] = 'sync_datasets';
			$packet['request_datasets'] = json_encode($requestDatasets);
			$packet['local_datasets'] = json_encode($localDatasets);

			$response = $this->doPOST($this->_apiUri, $packet, 'JLive! Chat');
			$response = json_decode($response);

			if(is_object($response))
			{
				$datasets = get_object_vars($response);

				if(!empty($datasets))
				{
					foreach($datasets as $tableName => $records)
					{
						$this->importTableData($tableName, $records);
					}
				}
			}
		}
	}

	function queueForceSync($tableName)
	{
		// Check if in hosted mode
		if(empty($this->_apiKey)) return false;

		$this->_forceSyncArray[] = $tableName;
	}

	function forceSyncToRemote()
	{
		// Check if in hosted mode
		if(empty($this->_apiKey)) return false;

		if(!empty($this->_forceSyncArray))
		{
			$localDatasets = array();

			foreach($this->_forceSyncArray as $tableName)
			{
				$localDatasets[$tableName] = $this->getLocalDataset($tableName);
			}

			$packet = array();

			$packet['k'] = $this->_apiKey;
			$packet['task'] = 'sync_datasets';
			$packet['local_datasets'] = json_encode($localDatasets);

			$this->doPOST($this->_apiUri, $packet, 'JLive! Chat');

			$this->_forceSyncArray = array();
		}
	}

	function importTableData($tableName, $dataset)
	{
		$sql = "TRUNCATE TABLE ".$tableName.";";
		$this->_db->setQuery($sql);
		$this->_db->query();

		if(!empty($dataset))
		{
			$tableFields = get_object_vars($dataset[0]);
			$realTableFields = array();

			foreach($tableFields as $fieldName => $fieldVal)
			{
				$realTableFields[] = $fieldName;
			}

			$sql = "INSERT IGNORE INTO ".$tableName;
			$sql .= "(".implode(',', $realTableFields).") VALUES ";

			foreach($dataset as $row)
			{
				$sql .= '(';
				foreach($row as $fieldName => $fieldVal)
				{
					if(is_numeric($fieldVal))
					{
						$sql .= $fieldVal.',';
					}
					else
					{
						$sql .= $this->_db->Quote($fieldVal).',';
					}
				}

				$sql = rtrim($sql, ',');
				$sql .= "),\n";
			}

			$sql = rtrim($sql, ",\n");

			$sql .= ";";

			$this->_db->setQuery($sql);
			$this->_db->query();

			return true;
		}

		return false;
	}

	function getLocalDataset($tableName)
	{
		if(!in_array($tableName, $this->_allowedTables)) return array();

		$sql = "SELECT * FROM ".$tableName;
		$this->_db->setQuery($sql);

		return $this->_db->loadObjectList();
	}

	function doPOST($url, $postData, $useragent = 'cURL', $headers = false, $follow_redirects = false, $debug = false)
	{
		if(!function_exists('curl_init')) return false;

		$fields_string = '';

		foreach($postData as $key => $value)
		{
			$fields_string .= $key.'='.urlencode($value).'&';
		}

		rtrim($fields_string, '&');

		# initialise the CURL library
		$ch = curl_init();

		# specify the URL to be retrieved
		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch, CURLOPT_POST, count($postData));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

		# we want to get the contents of the URL and store it in a variable
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		# specify the useragent: this is a required courtesy to site owners
		curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

		# ignore SSL errors
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

		# return headers as requested
		if($headers == true) curl_setopt($ch, CURLOPT_HEADER, 1);

		# only return headers
		if($headers == 'headers only') curl_setopt($ch, CURLOPT_NOBODY, 1);

		# follow redirects - note this is disabled by default in most PHP installs from 4.4.4 up
		if($follow_redirects == true) curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		if($this->_settings->getSetting('use_proxy') == 1)
		{
			// Use proxy server
			curl_setopt($ch, CURLOPT_PROXY, $this->_settings->getSetting('proxy_uri'));

			$proxyAuth = $this->_settings->getSetting('proxy_auth');

			if($this->_settings->getSetting('proxy_port') > 0) curl_setopt($ch, CURLOPT_PROXYPORT, $this->_settings->getSetting('proxy_port'));
			if(!empty($proxyAuth)) curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->_settings->getSetting('proxy_auth'));
			if($this->_settings->getSetting('use_socks') > 0) curl_setopt($ch, CURLOPT_PROXYTYPE, 5);
		}

		# if debugging, return an array with CURL's debug info and the URL contents
		if($debug)
		{
			$result['contents'] = curl_exec($ch);
			$result['info'] = curl_getinfo($ch);
		}
		else
		{
			# otherwise just return the contents as a variable
			$result = curl_exec($ch);
		}

		# free resources
		curl_close($ch);

		# send back the data
		return $result;
	}
}