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

if(JRequest::getVar('debug', null, 'method'))
{
	ini_set('display_errors', 'On');
	ini_set('error_reporting', E_ALL);
}
else
{
	ini_set('display_errors', 'Off');
}

jimport('joomla.plugin.plugin');

// Joomla 3.0+ compatibility
jimport('joomla.application.component.model');
jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');

if(class_exists('JModelLegacy') && !class_exists('CMSModel'))
{
	class CMSModel extends JModelLegacy  
	{
		function __construct()
		{
			$this->CMSModel();
		}

		function CMSModel()
		{
			parent::__construct();
		}
	}
}
elseif(!class_exists('CMSModel'))
{
	class CMSModel extends JModel  
	{
		function __construct()
		{
			$this->CMSModel();
		}

		function CMSModel()
		{
			parent::__construct();
		}
	}
}

if(class_exists('JControllerLegacy') && !class_exists('CMSController'))
{
	class CMSController extends JControllerLegacy  
	{
		function __construct()
		{
			$this->CMSController();
		}

		function CMSController()
		{
			parent::__construct();
		}
	}
}
elseif(!class_exists('CMSController'))
{
	class CMSController extends JController 
	{
		function __construct()
		{
			$this->CMSController();
		}

		function CMSController()
		{
			parent::__construct();
		}
	}
}

if(class_exists('JViewLegacy') && !class_exists('CMSView'))
{
	class CMSView extends JViewLegacy  
	{
		function __construct()
		{
			$this->CMSView();
		}

		function CMSView()
		{
			parent::__construct();
		}
	}
}
elseif(!class_exists('CMSView'))
{
	class CMSView extends JView 
	{
		function __construct()
		{
			$this->CMSView();
		}

		function CMSView()
		{
			parent::__construct();
		}
	}
}

class plgSystemJLiveChat extends JPlugin
{
	var $_initialized = false;
	var $_processPlugin = false;
	var $_comPath = null;
	var $_setting = null;
	var $_tracked = false;
	var $_languageFileLoaded = false;
	
	function __construct(&$subject, $config)
	{
		$this->plgSystemJLiveChat($subject, $config);
	}
	
	function plgSystemJLiveChat(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->_loadLanguageFile();

		jimport('joomla.application.helper');
		jimport('joomla.application.component.model');
	}

	function _initPlugin()
	{
		if($this->_initialized) return true;

		$this->_initialized = true;

		$this->_comPath = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jlivechat';

		$app = & JFactory::getApplication();

		if($app->getName() != 'site') return false;

		$settingsPath = $this->_comPath.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'setting.php';

		if(!file_exists($settingsPath)) return false;

		require_once $settingsPath;

		if($app->getName() == 'site')
		{
			$this->_processPlugin = true;
		}
		else
		{
			$this->_processPlugin = false;
		}

		$this->_setting = & CMSModel::getInstance('Setting', 'JLiveChatModel');

		//  If the settings object was not found, don't continue
		if(!is_object($this->_setting)) $this->_processPlugin = false;

		$uri = & JFactory::getURI();

		if(strpos($uri->toString(), 'do_not_log')) $this->_processPlugin = false;
	}

	function onAfterRoute()
	{
		$app = & JFactory::getApplication();

		if($app->getName() != 'site') return false;

		if(JRequest::getCmd('option') == 'com_jlivechat' && JRequest::getCmd('view') == 'api' && JRequest::getCmd('task') == 'api')
		{
			$this->_loadLanguageFile();

			$modelPath = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jlivechat'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'server.php';

			if(!file_exists($modelPath)) return false;

			require_once $modelPath;

			$model = & CMSModel::getInstance('Server', 'JLiveChatModel');
			$model->api_serve();
		}
	}

	function onAfterInitialise()
	{
		$this->_checkIPBlockerRules();
		$this->_fixBrokenMenuItems();
	}

	function _injectMedia()
	{
		$hostedMode = $this->_setting->isHostedMode();

		$lazyJSUri = JURI::root(true).'/components/com_jlivechat/js/lazyload-min.js';
		$jsUri = JURI::root(true).'/components/com_jlivechat/js/jlivechat.min.js';
		$cssUri = JURI::root(true).'/components/com_jlivechat/assets/css/jlivechat.min.css';

		// Now add CSS and JS
		$body = JResponse::getBody();

		$tmpJS = '';
		$tmpJS .= '<link rel="stylesheet" href="'.$cssUri.'" type="text/css" />';
		$tmpJS .= "\n";
		$tmpJS .= '<script type="text/javascript" src="'.$lazyJSUri.'"></script>';
		$tmpJS .= "\n";
		$tmpJS .= '<script type="text/javascript" src="'.$jsUri.'"></script>';
		$tmpJS .= "\n";
		$tmpJS .= '<script type="text/javascript">';
		$tmpJS .= "\n";

		if($hostedMode)
		{
			$tmpJS .= "JLiveChat.hostedModeURI='".$hostedMode['hosted_uri']."';\n";
			$tmpJS .= "JLiveChat.websiteRoot='".$hostedMode['hosted_uri']."';\n";
		}
		else
		{
			$tmpJS .= "JLiveChat.websiteRoot='".JURI::root(true)."';\n";
		}

		if(JRequest::getCmd('option') == 'com_jlivechat' && JRequest::getCmd('view') == 'popup')
		{
			// This IS the livechat popup window
			// This part intentionally left blank
		}
		else
		{
			// This page is not the livechat popup window
			$tmpJS .= "setTimeout('JLiveChat.initialize();', 100);";
		}

		$tmpJS .= "\n";
		$tmpJS .= '</script>';

		$body = preg_replace('@(</head>)@i', $tmpJS.'$1', $body, 1);

		JResponse::setBody($body);
	}

	function _checkIPBlockerRules()
	{
		$this->_initPlugin();

		$modelPath = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jlivechat'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'ipblocker.php';

		if(!file_exists($modelPath)) return false;

		require_once $modelPath;

		$ipblocker = & CMSModel::getInstance('IPBlocker', 'JLiveChatModel');
		$ipblocker->enforce();
	}

	function _trackVisitor()
	{
		if($this->_tracked) return true;

		if(!is_object($this->_setting)) return false;

		$this->_loadLanguageFile();

		$this->_tracked = true;

		$uri = & JFactory::getURI();
		$user = & JFactory::getUser();

		$hostedMode = $this->_setting->isHostedMode();

		if(!$hostedMode)
		{
			// Running in stand-alone mode
			$modelPath = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jlivechat'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'visitor.php';

			if(!file_exists($modelPath)) return false;

			require_once $modelPath;

			$visitor = & CMSModel::getInstance('Visitor', 'JLiveChatModel');
			$visitor->track();
		}
		else
		{
			// Running in hosted mode
			// We are in hosted mode
			$trackerUri = $hostedMode['hosted_uri'].'/index.php?option=com_jlivechat&amp;no_html=1&amp;tmpl=component';

			$trackerUri .= '&amp;view=popup';
			$trackerUri .= '&amp;task=track_remote_visitor';
			$trackerUri .= '&amp;user_id='.$user->get('id');
			$trackerUri .= '&amp;full_name='.urlencode($user->get('name'));
			$trackerUri .= '&amp;username='.urlencode($user->get('username'));
			$trackerUri .= '&amp;email='.urlencode($user->get('email'));
			$trackerUri .= '&amp;referrer='.urlencode(JRequest::getVar('HTTP_REFERER', '', 'server'));
			$trackerUri .= '&amp;last_uri='.urlencode($uri->toString());

			$trackerImg = '<img src="'.$trackerUri.'" width="1" height="1" alt="" border="0" />';

			JResponse::setBody(preg_replace('@(</body>)@i', $trackerImg.'$1', JResponse::getBody(), 1));
		}
	}

	function onAfterRender()
	{
		$this->_initPlugin();

		if(!$this->_processPlugin) return false;

		$this->_loadLanguageFile();

		$this->_trackVisitor();

		$mainframe = & JFactory::getApplication();

		$db = & JFactory::getDBO();
		$menu = & JSite::getMenu();

		jimport('joomla.application.component.helper');

		$com = & JComponentHelper::getComponent('com_jlivechat');

		if(!is_object($com) || !isset($com->id)) return false;

		$liveChatMenus = $menu->getItems('component_id', (int)$com->id);

		if(!empty($liveChatMenus))
		{
			$body = JResponse::getBody();

			foreach($liveChatMenus as $menuItem)
			{
				// Use popup window method
				if($menuItem->component != 'com_jlivechat') continue;

				if((int)$mainframe->getCfg('sef') == 1)
				{
					$menuLink = $menuItem->alias;
				}
				else
				{
					$menuLink = 'com_jlivechat';
				}

				preg_match('@<a[^>]+href[\s]*=[\s]*"([^"]*(?:'.preg_quote($menuLink, '@').'|com_jlivechat(?![\._]))[^"]*)"@i', $body, $linkMatch);

				if(!isset($linkMatch[1])) continue;

				$menuPopupMode = $menuItem->params->get('popup_mode');

				if(empty($menuPopupMode))
				{
					// Use Default
					$menuPopupMode = $this->_setting->getSetting('popup_mode');
				}

				$livechatUri = $linkMatch[1];

				$hostedMode = $this->_setting->isHostedMode();

				if(!$hostedMode)
				{
					// Stand-alone mode
					$popupUri = JURI::root(true).'/index.php?option=com_jlivechat&amp;view=popup&amp;tmpl=component&amp;popup_mode='.$menuPopupMode;
				}
				else
				{
					// Hosted mode
					$popupUri = $hostedMode['hosted_uri'].'/index.php?option=com_jlivechat&amp;view=popup&amp;tmpl=component&amp;popup_mode='.$menuPopupMode;
				}

				$activeLanguage = $this->_setting->getCurrentLangCode();
				$specificOperators = $menuItem->params->get('specific_operators');
				$specificDepartment = $menuItem->params->get('specific_department');
				$specificRouteId = (int)$menuItem->params->get('specific_route_id');

				if(!empty($activeLanguage)) $popupUri .= '&amp;lang='.$activeLanguage;

				if(!empty($specificOperators)) $popupUri .= '&amp;operators='.$specificOperators;

				if(!empty($specificDepartment)) $popupUri .= '&amp;department='.$specificDepartment;

				if($specificRouteId > 0) $popupUri .= '&amp;routeid='.$specificRouteId;

				$body = preg_replace('@(<a[^>]+href[\s]*=[\s]*")([^"]*)('.preg_quote($menuLink, '@').'|com_jlivechat(?![\._]))([^"]*)(")@i', '$1javascript:void(0);$5 onclick="requestLiveChat(\''.$popupUri.'\',\''.$menuPopupMode.'\');"', $body);
			}

			JResponse::setBody($body);
		}

		$this->_injectMedia();

		return true;
	}

	function _fixBrokenMenuItems()
	{
		$app = & JFactory::getApplication();

		if($app->getName() != 'site' || JRequest::getVar('mode', 'xmlrpc', 'method') == 'regular') return false;

		jimport('joomla.application.component.helper');

		$db = & JFactory::getDBO();
		$com = & JComponentHelper::getComponent('com_jlivechat');

		$sql = "UPDATE #__menu SET
					component_id = ".(int)$com->id."
				WHERE link = 'index.php?option=com_jlivechat&view=popup'
				AND component_id <> ".(int)$com->id.";";
		$db->setQuery($sql);

		return $db->query();
	}

	function _loadLanguageFile()
	{
		if(!$this->_languageFileLoaded && JRequest::getVar('mode', 'xmlrpc', 'method') != 'regular')
		{
			$this->_languageFileLoaded = true;

			$lang = & JFactory::getLanguage();
			$lang->load('com_jlivechat');
		}
	}
}