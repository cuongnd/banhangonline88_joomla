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

class JLiveChatModelSettingAdmin extends CMSModel
{
	var $_appId = null;
	var $_appName = 'JLive! Chat';
	var $_appVersion = '4.3.3';
	var $_settings = null;

	function __construct()
	{
		$this->JLiveChatModelSettingAdmin();
	}

	function JLiveChatModelSettingAdmin()
	{
		parent::__construct();

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jlcdateadmin.php';

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

			$date = new JLiveChatModelJLCDateAdmin();

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

	function getModuleVersion()
	{
		$mobj = $this->getModuleXMLObject();

		return $mobj->version;
	}

	function getModuleXMLObject()
	{
		$moduleManifestFilePath = JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_jlivechat'.DIRECTORY_SEPARATOR.'mod_jlivechat.xml';

		if(file_exists($moduleManifestFilePath))
		{
			// Module is installed
			return simplexml_load_file($moduleManifestFilePath);
		}
		else
		{
			$data = new stdClass();

			$data->version = null;

			return $data;
		}
	}

	function getPluginXMLObject()
	{
		$manifestFilePath = JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'jlivechat'.DIRECTORY_SEPARATOR.'jlivechat.xml';

		if(file_exists($manifestFilePath))
		{
			// Module is installed
			return simplexml_load_file($manifestFilePath);
		}
		else
		{
			$data = new stdClass();

			$data->version = null;

			return $data;
		}
	}

	function getPluginVersion()
	{
		$obj = $this->getPluginXMLObject();

		return $obj->version;
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

	function setLanguageStrings($languageCode, $languageStrings)
	{
		if(strlen($languageStrings) > 5)
		{
			// Ensure line breaks are saved correctly
			if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $languageStrings = str_replace("\r\n", "\n", $languageStrings);

			$mainframe = & JFactory::getApplication();

			jimport('joomla.filesystem.file');

			$languageFilePath = JPATH_SITE.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$languageCode.DIRECTORY_SEPARATOR.$languageCode.'.com_jlivechat.ini';

			if(file_exists($languageFilePath) && is_writable($languageFilePath))
			{
				JFile::write($languageFilePath, $languageStrings);

				return true;
			}
			else
			{
				$mainframe->enqueueMessage(JText::_('LANGUAGE_FILE_WRITE_FAIL'), 'error');
				$mainframe->enqueueMessage($languageFilePath, 'error');
			}
		}

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
				$siteName = str_replace('www.', '', $uri->toString(array('host')));
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
				'hosted_user_id' => $this->_settings->hosted_mode_user_id,
				'hosted_path_base' => 'www.ultimatelivechat.com/sites/'.$this->_settings->hosted_mode_user_id.'/'.$this->_settings->hosted_mode_path
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

	function deleteSetting($name)
	{
		if(isset($this->_settings->$name)) unset($this->_settings->$name);
	}

	function saveSettings()
	{
		$date = new JLiveChatModelJLCDateAdmin();

		$data = new stdClass();

		$data->app_id = $this->_appId;
		$data->app_data = json_encode($this->_settings);
		$data->app_mdate = $date->toUnix();

		$this->_db->updateObject('#__cms_app', $data, 'app_id');

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->refreshSettings();
		$hostedModeObj->queueForceSync('#__cms_app');
		$hostedModeObj->forceSyncToRemote();

		return true;
	}

	/**
	 * Publish, or make current, the selected language
	 */
	function setDefaultLanguage($language)
	{
		$modelPath = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_languages'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'installed.php';

		if(!file_exists($modelPath)) return false;

		require_once $modelPath;

		$mainframe = & JFactory::getApplication();

		$model = & CMSModel::getInstance('Installed', 'LanguagesModel');
		$model->publish($language);
		$model->getClient();

		return true;
	}

	function getDefaultLanguage()
	{
		jimport('joomla.application.component.helper');

		$params = JComponentHelper::getParams('com_languages');

		return $params->get('site');
	}

	function setHostedModeAPIKey($key)
	{
		$mainframe = & JFactory::getApplication();

		if(strpos($key, '******') !== FALSE || strpos($mainframe->getCfg('tmp_path'), 'ultimatelivechat.com') !== FALSE)
		{
			// Leave unchanged
		}
		elseif(!$key || strlen($key) < 3)
		{
			$this->setSetting('old_hosted_mode_api_key', $this->getSetting('hosted_mode_api_key'));
			$this->setSetting('hosted_mode_api_key', null);
			$this->setSetting('hosted_mode_user_id', null);
			$this->setSetting('hosted_mode_path', null);
		}
		else
		{
			// Validate key
			$postData = array(
				'task' => 'validate_api_key',
				'k' => $key
			);

			$keyDetails = $this->doPOST('https://www.ultimatelivechat.com/index.php?option=com_ultimatelivechat&view=api&format=raw', $postData, 'JLive! Chat');
			$keyDetails = json_decode($keyDetails);

			if($keyDetails->success && isset($keyDetails->user_id) && isset($keyDetails->path))
			{
				// Api Key was successfully validated
				$this->setSetting('hosted_mode_api_key', $key);
				$this->setSetting('hosted_mode_user_id', $keyDetails->user_id);
				$this->setSetting('hosted_mode_path', $keyDetails->path);

				$mainframe->enqueueMessage(JText::_('HOSTED_MODE_API_SUCCESS_VALIDATE'));
				$mainframe->enqueueMessage(JText::_('NOW_IN_HOSTED_MODE'));
			}
			else
			{
				$mainframe->enqueueMessage(JText::_('HOSTED_MODE_API_FAIL_VALIDATE'), 'error');

				$this->setSetting('hosted_mode_api_key', null);
				$this->setSetting('hosted_mode_user_id', null);
				$this->setSetting('hosted_mode_path', null);
			}
		}

		return true;
	}

	function doPOST($url, $postData, $useragent = 'cURL', $headers = false, $follow_redirects = false, $debug = false)
	{
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
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

		# return headers as requested
		if($headers == true) curl_setopt($ch, CURLOPT_HEADER, 1);

		# only return headers
		if($headers == 'headers only') curl_setopt($ch, CURLOPT_NOBODY, 1);

		# follow redirects - note this is disabled by default in most PHP installs from 4.4.4 up
		if($follow_redirects == true) curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		if($this->getSetting('use_proxy') == 1)
		{
			// Use proxy server
			curl_setopt($ch, CURLOPT_PROXY, $this->getSetting('proxy_uri'));

			if($this->getSetting('proxy_port') > 0) curl_setopt($ch, CURLOPT_PROXYPORT, $this->getSetting('proxy_port'));
			if(strlen($this->getSetting('proxy_auth')) > 0) curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->getSetting('proxy_auth'));
			if($this->getSetting('use_socks') > 0) curl_setopt($ch, CURLOPT_PROXYTYPE, 5);
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

	function getRemoteURL($uri)
	{
		if(function_exists('curl_init'))
		{
			$content = $this->url_get_contents($uri, 'JLive! Chat Component');
		}
		elseif(ini_get('allow_url_fopen') == 1 || ini_get('allow_url_fopen') == 'On')
		{
			$content = file_get_contents($uri);
		}
		else
		{
			$content = false;
		}

		return $content;
	}

	function url_get_contents($url, $useragent = 'cURL', $headers = false, $follow_redirects = false, $debug = false)
	{
		# initialise the CURL library
		$ch = curl_init();

		# specify the URL to be retrieved
		curl_setopt($ch, CURLOPT_URL, $url);

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

		if($this->getSetting('use_proxy') == 1)
		{
			// Use proxy server
			curl_setopt($ch, CURLOPT_PROXY, $this->getSetting('proxy_uri'));

			if($this->getSetting('proxy_port') > 0) curl_setopt($ch, CURLOPT_PROXYPORT, $this->getSetting('proxy_port'));
			if(strlen($this->getSetting('proxy_auth')) > 0) curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->getSetting('proxy_auth'));
			if($this->getSetting('use_socks') > 0) curl_setopt($ch, CURLOPT_PROXYTYPE, 5);
		}

		# if debugging, return an array with CURL's debug info and the URL contents
		if($debug == true)
		{
			$result['contents'] = curl_exec($ch);
			$result['info'] = curl_getinfo($ch);
		}

		# otherwise just return the contents as a variable
		else $result = curl_exec($ch);

		# free resources
		curl_close($ch);

		# send back the data
		return $result;
	}

	function setWindowTitle($languageCode, $text)
	{
		if(!is_object($this->_settings->window_titles)) $this->_settings->window_titles = new stdClass();

		$this->_settings->window_titles->$languageCode = $text;
	}

	function setWindowIntro($languageCode, $text)
	{
		if(!is_object($this->_settings->window_intros)) $this->_settings->window_intros = new stdClass();

		$this->_settings->window_intros->$languageCode = $text;
	}

	function setWindowOffline($languageCode, $text)
	{
		if(!is_object($this->_settings->offline_messages)) $this->_settings->offline_messages = new stdClass();

		$this->_settings->offline_messages->$languageCode = $text;
	}

	function getSettingsChecksum()
	{
		$sql = "SELECT SUM(app_mdate) FROM #__cms_app
				WHERE app_id = ".(int)$this->_appId.";";
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}
}
