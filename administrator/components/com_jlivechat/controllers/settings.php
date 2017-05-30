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
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JLiveChatControllerSettings extends CMSController
{
	/**
	 * Display the view
	 */
	function display()
	{
		$mainframe = & JFactory::getApplication();

		if(!function_exists('curl_init')) $mainframe->enqueueMessage(JText::_('CURL_NOT_INSTALLED'), 'error');

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->sync();

		header('Content-type: text/html; charset=utf-8'); // utf-8 encoding
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past

		$this->checkPlugin();

		$viewName = JRequest::getCmd('view', 'settings');

		$document = & JFactory::getDocument();
		$vType = $document->getType();

		// Get/Create the view
		$view = & $this->getView($viewName, $vType);

		// Get/Create the model
		$model = & $this->getModel('SettingsAdmin', 'JLiveChatModel');

		if($model)
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout('default');

		$this->isSystemProblems();

		// Display the view
		parent::display(false);
	}

	function checkPlugin()
	{
		$db = & JFactory::getDBO();

		$sql = "UPDATE #__extensions SET
					enabled = 1
				WHERE element = 'jlivechat'
				AND type = 'plugin';";

		$db->setQuery($sql);

		return $db->query();
	}

	function save()
	{
		$mainframe = & JFactory::getApplication();

		JRequest::checkToken() or die('Invalid Token');

		$siteName = JRequest::getVar('site_name', '', 'method');
		$hostedModeAPIKey = trim(JRequest::getVar('hosted_mode_api_key', null, 'method', 'string', JREQUEST_ALLOWRAW));
		$offset = JRequest::getVar('offset_value', 'sys', 'method');
		$languageSelected = JRequest::getVar('language_value', 'en-GB', 'method');
		$ringOrder = JRequest::getVar('ring_order_value', 'ring_same_time', 'method');
		$operator2operator = JRequest::getInt('operator2operator_value', 1, 'method');
		$activityMonitor = JRequest::getInt('activity_monitor_value', 1, 'method');
		$activityMonitorExpiration = JRequest::getInt('activity_monitor_expiration', 180, 'method');
		$popupMode = JRequest::getVar('popup_mode', 'popup', 'method');
		$routeby = JRequest::getVar('routeby', 'department', 'method');
		$routeDisplayFormat = JRequest::getVar('route_display_format', 'all_w_status', 'method');
		$enableMessaging = JRequest::getVar('enable_messaging', 'y', 'method');
		$emailsFrom = JRequest::getVar('emails_from', $mainframe->getCfg('mailfrom'), 'method');
		$askPhoneNumber = JRequest::getVar('ask_phone_number', 'optional', 'method');
		$emailMessagesTo = JRequest::getVar('email_messages_to', '', 'method');
		$proactiveChat = JRequest::getInt('proactive_chat', 1, 'method');
		$displayHTML = JRequest::getVar('display_html', '', 'method', 'string', JREQUEST_ALLOWRAW);
		$displayHTMLInSeconds = JRequest::getInt('display_html_in_seconds', 0, 'method');
		$displayHTMLONUris = JRequest::getVar('display_html_on_uris', null, 'method', 'string', JREQUEST_ALLOWRAW);
		$customCSS = JRequest::getVar('custom_css', null, 'method', 'string', JREQUEST_ALLOWRAW);
		$popupPageTitle = JRequest::getVar('popup_page_title', null, 'method');
		$popupUseSSL = JRequest::getInt('popup_ssl', 0, 'method');
		$useProxy = JRequest::getInt('use_proxy', 0, 'method');
		$useSocks = JRequest::getInt('use_socks', 0, 'method');
		$proxyUri = JRequest::getVar('proxy_uri', null, 'method');
		$proxyPort = JRequest::getInt('proxy_port', null, 'method');
		$proxyAuth = JRequest::getVar('proxy_auth', null, 'method');
		$autoPopupMaxDisplay = JRequest::getInt('autopopup_max_display', 0, 'method');
		$autoPopup = JRequest::getInt('autopopup', 1, 'method');
		$autoPopupOnlyOnline = JRequest::getInt('autopopup_only_online', 0, 'method');
		$useGZip = JRequest::getInt('use_gzip', 1, 'method');
		$universalMessages = JRequest::getInt('universal_messages', 0, 'method');
		$pushNotifications = JRequest::getInt('use_pushservice', 1, 'method');

		if($autoPopupMaxDisplay < 0) $autoPopupMaxDisplay = 0;

		$settings = & CMSModel::getInstance('SettingAdmin', 'JLiveChatModel');

		$settings->setHostedModeAPIKey($hostedModeAPIKey);

		// Display HTML on URIs
		preg_match_all('@([^\s\t\r\n\,]+)@', $displayHTMLONUris, $displayHTMLONUrisArray);

		$finalDisplayHTMLOnURIsArray = array();

		if(isset($displayHTMLONUrisArray[1]))
		{
			if(count($displayHTMLONUrisArray[1]) > 0)
			{
				// URIs found
				$finalDisplayHTMLOnURIsArray = $displayHTMLONUrisArray[1];
			}
		}

		// Offline message emails to
		preg_match_all('@([^\s\t\r\n\,]+)@', $emailMessagesTo, $allMatches);

		$finalEmailMessagesTo = array();

		if(isset($allMatches[1]))
		{
			if(count($allMatches[1]) > 0)
			{
				// URIs found
				$finalEmailMessagesTo = $allMatches[1];
			}
		}

		$settings->setSetting('site_name', $siteName);
		$settings->setSetting('ring_order', $ringOrder);
		$settings->setSetting('operator2operator', $operator2operator);
		$settings->setSetting('activity_monitor', $activityMonitor);
		$settings->setSetting('activity_monitor_expiration', $activityMonitorExpiration);
		$settings->setSetting('popup_mode', $popupMode);
		$settings->setSetting('routeby', $routeby);
		$settings->setSetting('route_display_format', $routeDisplayFormat);
		$settings->setSetting('enable_messaging', $enableMessaging);
		$settings->setSetting('emails_from', $emailsFrom);
		$settings->setSetting('ask_phone_number', $askPhoneNumber);
		$settings->setSetting('email_messages_to', $finalEmailMessagesTo);
		$settings->setSetting('proactive_chat', $proactiveChat);
		$settings->setSetting('display_html', $displayHTML);
		$settings->setSetting('display_html_in_seconds', $displayHTMLInSeconds);
		$settings->setSetting('display_html_on_uris', $finalDisplayHTMLOnURIsArray);
		$settings->setSetting('popup_page_title', $popupPageTitle);
		$settings->setSetting('popup_ssl', $popupUseSSL);
		$settings->setSetting('use_proxy', $useProxy);
		$settings->setSetting('use_socks', $useSocks);
		$settings->setSetting('proxy_uri', $proxyUri);
		$settings->setSetting('proxy_port', $proxyPort);
		$settings->setSetting('proxy_auth', $proxyAuth);
		$settings->setSetting('autopopup_max_display', $autoPopupMaxDisplay);
		$settings->setSetting('autopopup', $autoPopup);
		$settings->setSetting('autopopup_only_online', $autoPopupOnlyOnline);
		$settings->setSetting('timezone_offset', $offset);
		$settings->setSetting('use_gzip', $useGZip);
		$settings->setSetting('universal_messages', $universalMessages);
		$settings->setSetting('use_pushservice', $pushNotifications);

		$isError = false;

		$customCSSPath = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jlivechat'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'custom.css';
		$configPath = JPATH_SITE.DIRECTORY_SEPARATOR.'configuration.php';
		$modPath = JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_jlivechat';
		$lockFile = JPATH_SITE.DIRECTORY_SEPARATOR.'ulc.lock';

		jimport('joomla.filesystem.file');

		// Check if free account
		if(!file_exists($lockFile))
		{
			if(is_writable($customCSSPath)) JFile::write($customCSSPath, $customCSS);
		}
		else
		{
			$mainframe->enqueueMessage(JText::_('CSS_COULD_NOT_SAVE_UPGRADE'), 'error');
		}

		if(!file_exists($modPath) || !is_writable($modPath))
		{
			$isError = true;
		}
		else
		{
			$this->upload_images($settings);
		}

		$allVars = JRequest::get('method', JREQUEST_ALLOWRAW);

		foreach($allVars as $fieldName => $fieldVal)
		{
			if(strpos($fieldName, 'language_strings_') !== FALSE)
			{
				// This is a language string field
				preg_match('@([^_]+)$@', $fieldName, $match);

				$settings->setLanguageStrings($match[1], $fieldVal);
			}
			elseif(strpos($fieldName, 'window_title-') !== FALSE)
			{
				preg_match('@([a-zA-Z]+-[a-zA-Z]+)$@', $fieldName, $match);

				$settings->setWindowTitle($match[1], $fieldVal);
			}
			elseif(strpos($fieldName, 'window_intro-') !== FALSE)
			{
				preg_match('@([a-zA-Z]+-[a-zA-Z]+)$@', $fieldName, $match);

				$settings->setWindowIntro($match[1], $fieldVal);
			}
			elseif(strpos($fieldName, 'offline_msg-') !== FALSE)
			{
				preg_match('@([a-zA-Z]+-[a-zA-Z]+)$@', $fieldName, $match);

				$settings->setWindowOffline($match[1], $fieldVal);
			}
		}

		if($settings->getDefaultLanguage() != $languageSelected)
		{
			$settings->saveSettings();

			$settings->setDefaultLanguage($languageSelected);

			return $mainframe->redirect('index.php?option=com_jlivechat&view=settings', JText::_('SETTINGS_SAVED_SUCCESS'));
		}

		if($settings->saveSettings() && !$isError)
		{
			return $mainframe->redirect('index.php?option=com_jlivechat&view=settings', JText::_('SETTINGS_SAVED_SUCCESS'));
		}
		else
		{
			return $mainframe->redirect('index.php?option=com_jlivechat&view=settings', JText::_('SETTINGS_SAVED_FAIL'), 'error');
		}
	}

	function upload_images(&$settings)
	{
		// Online/Offline Img Upload
		$mainframe = & JFactory::getApplication();

		$errors = array();
		$modRootPath = JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_jlivechat';
		$siteImgRootPath = JPATH_SITE.DIRECTORY_SEPARATOR.'images';
		$imgWasUploaded = false;
		$largeOnlineImg = JRequest::getVar('large_online_img', null, 'files');
		$largeOfflineImg = JRequest::getVar('large_offline_img', null, 'files');
		$smallOnlineImg = JRequest::getVar('small_online_img', null, 'files');
		$smallOfflineImg = JRequest::getVar('small_offline_img', null, 'files');

		if(!defined('IMAGETYPE_GIF')) define('IMAGETYPE_GIF', 1);

		if(!defined('IMAGETYPE_JPEG')) define('IMAGETYPE_JPEG', 2);

		if(!defined('IMAGETYPE_PNG')) define('IMAGETYPE_PNG', 3);

		// Start Large img upload
		if(isset($largeOnlineImg['tmp_name']))
		{
			if(strlen($largeOnlineImg['tmp_name']) > 1)
			{
				// Img was uploaded
				$imgWasUploaded = true;

				list($width, $height, $type, $attr) = getimagesize($largeOnlineImg['tmp_name']);

				// Ensure its a compatible image
				if($type != IMAGETYPE_GIF && $type != IMAGETYPE_JPEG && $type != IMAGETYPE_PNG)
				{
					$errors[] = JText::_('UPLOAD_VALID_LARGE_ONLINE_IMG');
				}
				else
				{
					// Img is good
					if($type == IMAGETYPE_JPEG)
					{
						// Img is a jpeg
						$imgObj = imagecreatefromjpeg($largeOnlineImg['tmp_name']);

						$imgExt = '.jpg';
					}
					elseif($type == IMAGETYPE_GIF)
					{
						// Img is a gif
						$imgObj = imagecreatefromgif($largeOnlineImg['tmp_name']);

						$imgExt = '.gif';
					}
					elseif($type == IMAGETYPE_PNG)
					{
						// Img is a gif
						$imgObj = imagecreatefrompng($largeOnlineImg['tmp_name']);

						$imgExt = '.png';
					}

					$saveAs = $modRootPath.DIRECTORY_SEPARATOR.'livechat-large-online'.$imgExt;

					if($imgExt == '.jpg')
					{
						imagejpeg($imgObj, $saveAs, 100);
					}
					elseif($imgExt == '.gif')
					{
						imagegif($imgObj, $saveAs);
					}
					elseif($imgExt == '.png')
					{
						imagepng($imgObj, $saveAs);
					}

					$settings->setSetting('large_online_img_ext', $imgExt);
				}
			}
		}
		// End Large img upload
		// Start Large offline img upload
		if(isset($largeOfflineImg['tmp_name']))
		{
			if(strlen($largeOfflineImg['tmp_name']) > 1)
			{
				// Img was uploaded
				$imgWasUploaded = true;

				list($width, $height, $type, $attr) = getimagesize($largeOfflineImg['tmp_name']);

				// Ensure its a compatible image
				if($type != IMAGETYPE_GIF && $type != IMAGETYPE_JPEG && $type != IMAGETYPE_PNG)
				{
					$errors[] = JText::_('UPLOAD_VALID_LARGE_OFFLINE_IMG');
				}
				else
				{
					// Img is good
					if($type == IMAGETYPE_JPEG)
					{
						// Img is a jpeg
						$imgObj = imagecreatefromjpeg($largeOfflineImg['tmp_name']);

						$imgExt = '.jpg';
					}
					elseif($type == IMAGETYPE_GIF)
					{
						// Img is a gif
						$imgObj = imagecreatefromgif($largeOfflineImg['tmp_name']);

						$imgExt = '.gif';
					}
					elseif($type == IMAGETYPE_PNG)
					{
						// Img is a png
						$imgObj = imagecreatefrompng($largeOfflineImg['tmp_name']);

						$imgExt = '.png';
					}

					$saveAs = $modRootPath.DIRECTORY_SEPARATOR.'livechat-large-offline'.$imgExt;

					if($imgExt == '.jpg')
					{
						imagejpeg($imgObj, $saveAs, 100);
					}
					elseif($imgExt == '.gif')
					{
						imagegif($imgObj, $saveAs);
					}
					elseif($imgExt == '.png')
					{
						imagepng($imgObj, $saveAs);
					}

					$settings->setSetting('large_offline_img_ext', $imgExt);
				}
			}
		}
		// End Large offline img upload
		// Start Small online img upload
		if(isset($smallOnlineImg['tmp_name']))
		{
			if(strlen($smallOnlineImg['tmp_name']) > 1)
			{
				// Img was uploaded
				$imgWasUploaded = true;

				list($width, $height, $type, $attr) = getimagesize($smallOnlineImg['tmp_name']);

				// Ensure its a compatible image
				if($type != IMAGETYPE_GIF && $type != IMAGETYPE_JPEG && $type != IMAGETYPE_PNG)
				{
					$errors[] = JText::_('UPLOAD_VALID_SMALL_ONLINE_IMG');
				}
				else
				{
					// Img is good
					if($type == IMAGETYPE_JPEG)
					{
						// Img is a jpeg
						$imgObj = imagecreatefromjpeg($smallOnlineImg['tmp_name']);

						$imgExt = '.jpg';
					}
					elseif($type == IMAGETYPE_GIF)
					{
						// Img is a gif
						$imgObj = imagecreatefromgif($smallOnlineImg['tmp_name']);

						$imgExt = '.gif';
					}
					elseif($type == IMAGETYPE_PNG)
					{
						// Img is a gif
						$imgObj = imagecreatefrompng($smallOnlineImg['tmp_name']);

						$imgExt = '.png';
					}

					$saveAs = $modRootPath.DIRECTORY_SEPARATOR.'livechat-online'.$imgExt;

					if($imgExt == '.jpg')
					{
						imagejpeg($imgObj, $saveAs, 100);
					}
					elseif($imgExt == '.gif')
					{
						imagegif($imgObj, $saveAs);
					}
					elseif($imgExt == '.png')
					{
						imagepng($imgObj, $saveAs);
					}

					$settings->setSetting('small_online_img_ext', $imgExt);
				}
			}
		}
		// End Small online img upload
		// Start Small offline img upload
		if(isset($smallOfflineImg['tmp_name']))
		{
			if(strlen($smallOfflineImg['tmp_name']) > 1)
			{
				// Img was uploaded
				$imgWasUploaded = true;

				list($width, $height, $type, $attr) = getimagesize($smallOfflineImg['tmp_name']);

				// Ensure its a compatible image
				if($type != IMAGETYPE_GIF && $type != IMAGETYPE_JPEG && $type != IMAGETYPE_PNG)
				{
					$errors[] = JText::_('UPLOAD_VALID_SMALL_OFFLINE_IMG');
				}
				else
				{
					// Img is good
					if($type == IMAGETYPE_JPEG)
					{
						// Img is a jpeg
						$imgObj = imagecreatefromjpeg($smallOfflineImg['tmp_name']);

						$imgExt = '.jpg';
					}
					elseif($type == IMAGETYPE_GIF)
					{
						// Img is a gif
						$imgObj = imagecreatefromgif($smallOfflineImg['tmp_name']);

						$imgExt = '.gif';
					}
					elseif($type == IMAGETYPE_PNG)
					{
						// Img is a gif
						$imgObj = imagecreatefrompng($smallOfflineImg['tmp_name']);

						$imgExt = '.png';
					}

					$saveAs = $modRootPath.DIRECTORY_SEPARATOR.'livechat-offline'.$imgExt;

					if($imgExt == '.jpg')
					{
						imagejpeg($imgObj, $saveAs, 100);
					}
					elseif($imgExt == '.gif')
					{
						imagegif($imgObj, $saveAs);
					}
					elseif($imgExt == '.png')
					{
						imagepng($imgObj, $saveAs);
					}

					$settings->setSetting('small_offline_img_ext', $imgExt);
				}
			}
		}
		// End Small offline img upload

		if($imgWasUploaded)
		{
			$settings->saveSettings();

			if(count($errors) > 0)
			{
				// An error occurred with the img upload
				foreach($errors as $errorMsg)
				{
					$mainframe->enqueueMessage($errorMsg, 'error');
				}
			}
		}

		return $imgWasUploaded;
	}

	function isSystemProblems()
	{
		$mainframe = & JFactory::getApplication();

		$isError = false;

		jimport('joomla.filesystem.file');

		$customCSSPath = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jlivechat'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'custom.css';

		if(!file_exists($customCSSPath) || !is_writable($customCSSPath))
		{
			$mainframe->enqueueMessage(JText::_('CSS_NOT_WRITABLE'), 'error');
			$mainframe->enqueueMessage($customCSSPath, 'error');

			$isError = true;
		}

		$modPath = JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_jlivechat';

		if(!file_exists($modPath) || !is_writable($modPath))
		{
			$mainframe->enqueueMessage(JText::_('MODULE_NOT_WRITABLE'), 'error');
			$mainframe->enqueueMessage($modPath, 'error');

			$isError = true;
		}

		return $isError;
	}

	function restore_original_language_file()
	{
		$mainframe = & JFactory::getApplication();

		$langToRestore = JRequest::getVar('restore_lang', null, 'method');

		$msg = null;
		$msgType = null;

		if($langToRestore)
		{
			jimport('joomla.filesystem.file');

			$originalPath = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jlivechat'.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$langToRestore.'.com_jlivechat.ini';
			$destPath = JPATH_SITE.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$langToRestore.DIRECTORY_SEPARATOR.$langToRestore.'.com_jlivechat.ini';

			if(@JFile::copy($originalPath, $destPath))
			{
				$mainframe->enqueueMessage(JText::_('LANGUAGE_FILE_RESTORED_SUCCUSSFULLY'));
				$mainframe->enqueueMessage($destPath);
			}
			else
			{
				$mainframe->enqueueMessage(JText::_('LANGUAGE_FILE_RESTORED_FAILED'), 'error');
				$mainframe->enqueueMessage($destPath, 'error');
			}
		}

		return $mainframe->redirect('index.php?option=com_jlivechat&view=settings');
	}
}