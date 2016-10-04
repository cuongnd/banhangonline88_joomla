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

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jlcdate.php';

define('JLC_APP_NAME', 'JLive! Chat');
define('JLC_APP_VERSION', '4.3.3');

class JLiveChatModelSettingFactory
{
	function getAppRecord()
	{
		static $jlcAppRecord;

		if(!isset($jlcAppRecord))
		{
			$db = & JFactory::getDBO();

			$sql = "SELECT SQL_CACHE
						app_id,
						app_data 
					FROM #__cms_app
					WHERE app_name = ".$db->Quote(JLC_APP_NAME)."
					LIMIT 1;";
			$db->setQuery($sql);

			$tmpResult = $db->loadAssoc();

			if(!isset($tmpResult['app_id']))
			{
				// App record doesn't exist yet, create it
				JLiveChatModelSettingFactory::createDefaultSettings();

				// Now recall this function
				return JLiveChatModelSettingFactory::getAppRecord();
			}
			else
			{
				$jlcAppRecord = $tmpResult;
			}
		}

		return $jlcAppRecord;
	}

	function getAppId()
	{
		static $jlcAppId;

		if(!isset($jlcAppId))
		{
			$appRecord = JLiveChatModelSettingFactory::getAppRecord();

			if(isset($appRecord['app_id']))
			{
				$jlcAppId = $appRecord['app_id'];
			}
			else
			{
				$jlcAppId = JLiveChatModelSettingFactory::createDefaultSettings();
			}
		}

		return $jlcAppId;
	}

	function getSettings()
	{
		static $jlcSettings;

		if(!isset($jlcSettings))
		{
			$appRecord = JLiveChatModelSettingFactory::getAppRecord();

			if(isset($appRecord['app_data']))
			{
				$jlcSettings = json_decode($appRecord['app_data']);

				// Settings should be an object
				if(!is_object($jlcSettings)) $jlcSettings = new stdClass();
			}
			else
			{
				// App record doesn't exist yet, create it
				JLiveChatModelSettingFactory::createDefaultSettings();

				// Now recall this function
				return JLiveChatModelSettingFactory::getSettings();
			}
		}

		return $jlcSettings;
	}

	function createDefaultSettings()
	{
		// App record doesn't exist yet, create it
		$db = & JFactory::getDBO();

		// Settings should be an object
		$defaultSettings = new stdClass();

		$date = new JLiveChatModelJLCDate();

		$nowUnixTime = $date->toUnix();

		$data = new stdClass();

		$data->app_name = JLC_APP_NAME;
		$data->app_data = json_encode($defaultSettings);
		$data->app_cdate = $nowUnixTime;
		$data->app_mdate = $nowUnixTime;

		$db->insertObject('#__cms_app', $data, 'app_id');

		return $db->insertid();
	}
}

class JLiveChatModelSetting extends CMSModel 
{
	var $_appId = null;
	var $_settings = null;

	function __construct()
	{
		$this->JLiveChatModelSetting();
	}

	function JLiveChatModelSetting()
	{
		parent::__construct();

		$this->_loadSettings();
	}

	function refreshSettings()
	{
		$this->_loadSettings();
	}

	function _loadSettings()
	{
		$this->_appId = JLiveChatModelSettingFactory::getAppId();
		$this->_settings = JLiveChatModelSettingFactory::getSettings();
	}

	function getAppName()
	{
		return JLC_APP_NAME;
	}

	function getAppId()
	{
		return $this->_appId;
	}

	function getAppVersion()
	{
		return JLC_APP_VERSION;
	}

	function getSetting($name)
	{
		if(isset($this->_settings->$name)) return $this->_settings->$name;

		$mainframe = & JFactory::getApplication();

		// Default Values
		if($name == 'popup_mode') return 'popup';
		if($name == 'ring_order') return 'ring_same_time';
		if($name == 'operator2operator') return 1;
		if($name == 'activity_monitor') return 1;
		if($name == 'routeby') return 'none';
		if($name == 'enable_messaging') return 'y';
		if($name == 'ask_phone_number') return 'optional';
		if($name == 'proactive_chat') return 1;
		if($name == 'display_html_in_seconds') return 1;
		if($name == 'display_html') return "<span class=\"autopopup-cloud-outer\"><span class=\"autopopup-cloud-inner\">&nbsp;</span></span><span class=\"autopopup-center\"><span class=\"autopopup-center-line1\">".JText::_('AUTOPOPUP_QUESTIONS')."</span><a class=\"autopopup-center-line2\" href=\"javascript:void(0);\" onclick=\"requestLiveChat('".rtrim(JURI::root(), '/')."/index.php?option=com_jlivechat&amp;view=popup&amp;tmpl=component&amp;popup_mode=popup','popup');\">".JText::_('AUTOPOPUP_TALK_TO_SPECIALIST_NOW')."</a></span><a class=\"autopopup-close-button\" href=\"javascript:void(0);\" onclick=\"javascript: AutoPopupChecker.close(1);\">&nbsp;</a><a class=\"autopopup-green-button\" href=\"javascript:void(0);\" onclick=\"requestLiveChat('".rtrim(JURI::root(), '/')."/index.php?option=com_jlivechat&amp;view=popup&amp;tmpl=component&amp;popup_mode=popup','popup');\">".JText::_('AUTOPOPUP_LIVECHAT')."</a>";
		if($name == 'display_html_on_uris') return array('/', '/index.php?option=com_virtuemart', '/index.php?option=com_kunena', 'support', 'help');
		if($name == 'activity_monitor_expiration') return 180;
		if($name == 'emails_from') return $mainframe->getCfg('mailfrom');
		if($name == 'route_display_format') return 'all_w_status';
		if($name == 'large_online_img_ext' || $name == 'large_offline_img_ext' || $name == 'small_online_img_ext' || $name == 'small_offline_img_ext') return '.jpg';
		if($name == 'popup_page_title') return $mainframe->getCfg('sitename');
		if($name == 'popup_ssl') return 0;
		if($name == 'use_proxy') return 0;
		if($name == 'use_socks') return 0;
		if($name == 'autopopup_max_display') return 0;
		if($name == 'autopopup') return 1;
		if($name == 'autopopup_only_online') return 0;
		if($name == 'timezone_offset') return 'sys';
		if($name == 'use_gzip') return 1;
		if($name == 'universal_messages') return 0;
		if($name == 'use_pushservice') return 1;

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
		// Prevent ultimatelivechat from going into hosted mode
		if(JRequest::getVar('SERVER_NAME', null, 'server') == 'www.ultimatelivechat.com') return false;

		if(isset($this->_settings->hosted_mode_api_key) && isset($this->_settings->hosted_mode_user_id) && isset($this->_settings->hosted_mode_path))
		{
			(JRequest::getVar('HTTPS', null, 'server') == 'on') ? $scheme = 'https://' : $scheme = 'http://';

			$hostedSettings = array(
				'hosted_uri' => $scheme.'www.ultimatelivechat.com/sites/'.$this->_settings->hosted_mode_user_id.'/'.$this->_settings->hosted_mode_path,
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
		$date = new JLiveChatModelJLCDate();

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

	function getDefaultLanguage()
	{
		jimport('joomla.application.component.helper');

		$params = JComponentHelper::getParams('com_languages');

		return $params->get('site');
	}

	function getCurrentLangCode()
	{
		// Check joomfish var first
		$activeLanguage = JRequest::getCmd('lang');

		if(empty($activeLanguage))
		{
			// Now try using the built-in translation manager
			jimport('joomla.language.helper');
			$languages = JLanguageHelper::getLanguages('lang_code');
			$lang_code = JFactory::getLanguage()->getTag();
			$activeLanguage = $languages[$lang_code]->sef;
		}

		return $activeLanguage;
	}
}