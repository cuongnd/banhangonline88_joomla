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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

@set_time_limit(0);

jimport('joomla.application.component.model');

class JAdminModelRestfulServer
{
	var $_deviceUUID = null;

	function setUUID($uuid)
	{
		$this->_deviceUUID = $uuid;
	}

	function add_ipblocker_rule(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'ipblocker.php';

		$_ipBlocker = & CMSModel::getInstance('IPBlocker', 'JAdminModel');

		$descName = $params[0];

		if(!empty($params[0]))
		{
			$descName = base64_decode($params[0]);
		}
		else
		{
			$descName = null;
		}

		return $_ipBlocker->addRule($operator, $descName, $params[1]);
	}

	function delete_ipblocker_rule(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'ipblocker.php';

		$_ipBlocker = & CMSModel::getInstance('IPBlocker', 'JAdminModel');

		$deleteRuleIds = explode(',', rtrim($params[0], ','));

		return $_ipBlocker->deleteRules($operator['user_id'], $deleteRuleIds);
	}

	function update_operator_sync_method(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'sync.php';

		$syncObj = & CMSModel::getInstance('Sync', 'JAdminModel');
		$syncObj->setUser($operator);
		$syncObj->setUUID($this->_deviceUUID);

		$syncMode = $params[0];

		$operatorIP = null;
		$operatorListenPort = null;

		if($syncMode == 'push')
		{
			$operatorIP = $params[1];
			$operatorListenPort = $params[2];
		}

		return $syncObj->updateOperatorSyncMethod($syncMode, $operatorIP, $operatorListenPort);
	}

	function get_gateway_authkey(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'user.php';

		$_user = & CMSModel::getInstance('User', 'JAdminModel');

		return $_user->getGatewayAuthKey($operator['user_id']);
	}

}

class JAdminModelServer
{

	function api_serve()
	{
		$currentPath = dirname(__FILE__);

		$mode = JRequest::getVar('mode', 'regular', 'method');

		switch($mode)
		{
			case 'regular':
				header('Content-type: application/json; charset=utf-8'); // utf-8 encoding
				break;
			case 'restful':
				header('Content-type: application/json; charset=utf-8'); // utf-8 encoding
				break;
			default:
				header('Content-type: text/plain; charset=utf-8'); // utf-8 encoding
				break;
		}

		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past

		$session = & JFactory::getSession();
		$session->destroy();
		$session->close();

		switch($mode)
		{
			case 'regular':
				require_once $currentPath.DIRECTORY_SEPARATOR.'user.php';
				require_once $currentPath.DIRECTORY_SEPARATOR.'sync.php';

				$key = JRequest::getVar('k', null, 'method');
				$systemUUID = JRequest::getVar('uuid', null, 'method');

				$userObj = & CMSModel::getInstance('User', 'JAdminModel');

				$adminUser = $userObj->isKeyValid($key);

				if(!$adminUser || empty($key)) jexit('Access Denied');

				$syncObj = & CMSModel::getInstance('Sync', 'JAdminModel');
				$syncObj->setUser($adminUser);
				$syncObj->setUUID($systemUUID);

				$userObj->applyPendingChanges();

				echo json_encode($syncObj->getUserData());

				break;

			case 'restful':
				require_once $currentPath.DIRECTORY_SEPARATOR.'user.php';
				require_once $currentPath.DIRECTORY_SEPARATOR.'sync.php';

				$key = JRequest::getVar('k', null, 'method');

				$userObj = & CMSModel::getInstance('User', 'JAdminModel');

				$operator = $userObj->isKeyValid($key);

				if(!$operator || empty($key)) jexit('Access Denied');

				$systemUUID = JRequest::getVar('uuid', null, 'method');
				$task = strtolower(JRequest::getVar('rest_task', '', 'method'));
				$numOfParams = JRequest::getVar('num_of_params', 0, 'method');

				// Get gateway authkey is not allowed on mobile devices
				if(strlen($key) == 15 && $task == 'get_gateway_authkey') jexit('Access Denied');

				$params = array();

				for($a = 0; $a < $numOfParams; $a++)
				{
					$params[$a] = JRequest::getVar('param'.($a + 1), null, 'method');
				}

				$restServerObj = new JAdminModelRestfulServer();
				$restServerObj->setUUID($systemUUID);

				if(method_exists($restServerObj, $task))
				{
					$output = array('result' => $restServerObj->$task($operator, $params));
				}
				else
				{
					$output = array('result' => 'Invalid task specified');
				}

				$syncObj = & CMSModel::getInstance('Sync', 'JAdminModel');
				
				echo json_encode($output);

				break;

			default:
				break;
		}

		jexit();
	}
}