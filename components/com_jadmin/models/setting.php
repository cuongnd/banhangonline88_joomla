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

class JAdminModelSetting extends CMSModel
{
	var $_appId = null;
	var $_appName = 'JAdmin!';
	var $_appVersion = '1.5.4.3';
	var $_settings = null;

	function __construct()
	{
		$this->JAdminModelSetting();
	}

	function JAdminModelSetting()
	{
		parent::__construct();

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jadmdate.php';

		$this->_loadSettings();
	}

	function refreshSettings()
	{
		$this->_loadSettings();
	}

	function _loadSettings()
	{
		$sql = "SELECT 
		    app_id,
		    app_data
		FROM #__cms_app
		WHERE app_name = ".$this->_db->Quote($this->_appName)."
		LIMIT 1;";
		$this->_db->setQuery($sql);

		$result = $this->_db->loadAssoc();

		if(isset($result['app_id']))
		{
			$this->_appId = $result['app_id'];

			if(isset($result['app_data']))
			{
				$this->_settings = json_decode($result['app_data']);
			}

			if(!is_object($this->_settings))
			{
				// Settings should be an object
				$this->_settings = new stdClass();
			}
		}
		else
		{
			// App record doesn't exist yet, create it
			if(!is_object($this->_settings))
			{
				// Settings should be an object
				$this->_settings = new stdClass();
			}

			$date = new JAdminModelJADMDate();

			$nowUnixTime = $date->toUnix();

			$data = new stdClass();

			$data->app_name = $this->_appName;
			$data->app_data = json_encode($this->_settings);
			$data->app_cdate = $nowUnixTime;
			$data->app_mdate = $nowUnixTime;

			$this->_db->insertObject('#__cms_app', $data, 'app_id');

			$this->_appId = $this->_db->insertid();

			return $this->_loadSettings();
		}

		return true;
	}

	function getAppName()
	{
		return $this->_appName;
	}

	function getAppId()
	{
		return $this->_appId;
	}

	function getAppVersion()
	{
		return $this->_appVersion;
	}

	function getSetting($name)
	{
		if(isset($this->_settings->$name)) return $this->_settings->$name;

		// Default Values
		if($name == 'activity_monitor') return 1;
		if($name == 'activity_monitor_expiration') return 180;
		if($name == 'use_proxy') return 0;
		if($name == 'use_socks') return 0;
		if($name == 'use_gzip') return 1;

		return false;
	}

	function getSiteName()
	{
		$siteName = $this->getSetting('site_name');

		if(!$siteName)
		{
			$uri = & JFactory::getURI();

			$hostedMode = $this->isHostedMode();

			if(!$hostedMode)
			{
				if(!JRequest::getVar('HTTP_HOST', null, 'server'))
				{
					// Running from cli
					$mainframe = & JFactory::getApplication();

					$siteName = $mainframe->getCfg('sitename');
				}
				else
				{
					$siteName = str_replace('www.', '', $uri->toString(array('host')));
				}
			}
			else
			{
				// This is ultimatelivechat.com
				$siteName = 'utlimatelivechat.com/'.$hostedMode['hosted_path'];
			}
		}

		return $siteName;
	}

	function isHostedMode()
	{
		if(isset($this->_settings->hosted_mode_api_key) && isset($this->_settings->hosted_mode_user_id) && isset($this->_settings->hosted_mode_path))
		{
			$hostedSettings = array(
				'hosted_uri' => 'http://www.ultimatelivechat.com/sites/'.$this->_settings->hosted_mode_user_id.'/'.$this->_settings->hosted_mode_path,
				'hosted_path' => $this->_settings->hosted_mode_path,
				'hosted_user_id' => $this->_settings->hosted_mode_user_id
			);

			return $hostedSettings;
		}
		else
		{
			return false;
		}
	}

	function setSetting($name, $value)
	{
		$this->_settings->$name = $value;
	}

	function saveSettings()
	{
		$date = new JAdminModelJADMDate();

		$data = new stdClass();

		$data->app_id = $this->_appId;
		$data->app_data = json_encode($this->_settings);
		$data->app_mdate = $date->toUnix();

		$this->_db->updateObject('#__cms_app', $data, 'app_id');

		return true;
	}

	function getSettingsChecksum()
	{
		$sql = "SELECT SUM(app_mdate) FROM #__cms_app 
		WHERE app_id = ".(int)$this->_appId.";";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
}
