<?php
/**
 * @package JAdmin!
 * @version 1.5.4.3
 * @copyright (C) Copyright 2008-2011 CMS Fruit, CMSFruit.com. All rights reserved.
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
	class CMSModel extends CMSModel  
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

class plgSystemJAdmin extends JPlugin
{
	var $_initialized = false;
	var $_processPlugin = false;
	var $_comPath = null;
	var $_setting = null;
	var $_tracked = false;

	function plgSystemJAdmin(&$subject, $config)
	{
		parent::__construct($subject, $config);

		jimport('joomla.application.component.model');
	}

	function _initPlugin()
	{
		if($this->_initialized) return true;

		$this->_initialized = true;

		$this->_comPath = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jadmin';

		$app = & JFactory::getApplication();

		if($app->getName() != 'site') return false;

		$settingsPath = $this->_comPath.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'setting.php';

		if(!file_exists($settingsPath)) return false;

		require_once $settingsPath;

		if($app->getName() != 'site')
		{
			$this->_processPlugin = false;
		}
		else
		{
			$this->_processPlugin = true;
		}

		$this->_setting = & CMSModel::getInstance('Setting', 'JAdminModel');

		//  If the settings object was not found, don't continue
		if(!is_object($this->_setting)) $this->_processPlugin = false;

		$uri = & JFactory::getURI();

		if(strpos($uri->toString(), 'do_not_log')) $this->_processPlugin = false;
	}

	function onAfterRoute()
	{
		$app = & JFactory::getApplication();

		if($app->getName() == 'administrator')
		{
			// For admin site
			$modelPath = JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jadmin'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'useradmin.php';

			if(!file_exists($modelPath)) return false;

			require_once $modelPath;

			$userObj = & CMSModel::getInstance('UserAdmin', 'JAdminModel');

			$userObj->enforceAdminPermissions();

			$action = JRequest::getVar('action', null, 'method');

			switch($action)
			{
				case 'login_as_admin':
					$adminAuthKey = JRequest::getVar('k', null, 'method');

					$authKeyDetails = $userObj->isGatewayAuthKeyValid($adminAuthKey);

					if(!$authKeyDetails || empty($adminAuthKey) || empty($action)) jexit('Access Denied');

					$userObj->loginAsAdminUser($authKeyDetails['user_id']);

					$returnUrl = urldecode(JRequest::getVar('r', null, 'method'));

					$mainframe = & JFactory::getApplication();

					$mainframe->redirect($returnUrl);

					jexit();
			}
		}
		elseif($app->getName() == 'site')
		{
			if(JRequest::getCmd('option') == 'com_jadmin' && JRequest::getCmd('view') == 'api' && JRequest::getCmd('task') == 'api')
			{
				$lang = & JFactory::getLanguage();
				$lang->load('com_jadmin');

				$modelPath = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jadmin'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'server.php';

				if(!file_exists($modelPath)) return false;

				require_once $modelPath;

				$model = & CMSModel::getInstance('Server', 'JAdminModel');
				$model->api_serve();
			}
			elseif(JRequest::getCmd('option') == 'com_jadmin' && JRequest::getCmd('view') == 'api' && JRequest::getCmd('task') == 'gateway')
			{
				$mainframe = & JFactory::getApplication();

				$modelPath = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_jadmin'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'user.php';

				if(!file_exists($modelPath)) return false;

				require_once $modelPath;

				$userObj = & CMSModel::getInstance('User', 'JAdminModel');

				$adminAuthKey = JRequest::getVar('k', null, 'method');
				$action = JRequest::getVar('action', null, 'method');

				if(!$userObj->isGatewayAuthKeyValid($adminAuthKey) || empty($adminAuthKey) || empty($action)) jexit('Access Denied');

				switch($action)
				{
					case 'login_as_user':
						$userId = JRequest::getInt('uid', null, 'method');

						if(!empty($userId))
						{
							$user = & JUser::getInstance($userId);

							// Now login user
							$options = array();

							$response = new stdClass();
							$response->email = $user->email;
							$response->username = $user->username;
							$response->fullname = $user->name;
							$response->status = 1;
							$response->error_message = '';
							$response->type = 'Joomla';
							$response->password = '';

							// Import the user plugin group
							jimport('joomla.plugin.helper');

							JPluginHelper::importPlugin('user');

							// OK, the credentials are authenticated.  Lets fire the onLogin event
							$mainframe->triggerEvent('onUserLogin', array((array)$response, $options));
							////

							$mainframe->redirect('index.php', 'You are now logged in as '.$response->username.'!');

							jexit();
						}

						break;
				}
			}
		}
	}

	function onAfterInitialise()
	{
		$this->_checkIPBlockerRules();
	}

	function _checkIPBlockerRules()
	{
		$this->_initPlugin();

		$modelPath = $this->_comPath.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'ipblocker.php';

		if(!file_exists($modelPath)) return false;

		require_once $modelPath;

		$ipblocker = & CMSModel::getInstance('IPBlocker', 'JAdminModel');

		if(is_object($ipblocker)) $ipblocker->enforce();
	}

	function _trackVisitor()
	{
		if($this->_tracked) return true;

		if(!is_object($this->_setting)) return false;

		$this->_tracked = true;

		$modelPath = $this->_comPath.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'visitor.php';

		if(!file_exists($modelPath)) return false;

		require_once $modelPath;

		$visitor = & CMSModel::getInstance('Visitor', 'JAdminModel');
		$visitor->track();
	}

	function onAfterRender()
	{
		$this->_initPlugin();

		if(!$this->_processPlugin) return false;

		$this->_trackVisitor();
	}
}
