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

class JAdminModelUser extends CMSModel
{
	var $_settings = null;
	var $_pendingUpdates = array();
	var $_availablePermissions = array(
		25 => array(
			'label' => 'USER_MANAGER',
			'option' => 'com_users',
			'task' => 'view'
		),
		23 => array(
			'label' => 'LOGIN_AS_USER'
		),
		24 => array(
			'label' => 'IP_BLOCKER'
		),
		1 => array(
			'label' => 'MEDIA_MANAGER',
			'option' => 'com_media'
		),
		2 => array(
			'label' => 'GLOBAL_CONFIG',
			'option' => 'com_config'
		),
		3 => array(
			'label' => 'MENU_MANAGER',
			'option' => 'com_menus',
		),
		4 => array(
			'label' => 'MENU_TRASH',
			'option' => 'com_trash',
			'task' => 'viewMenu'
		),
		5 => array(
			'label' => 'ARTICAL_MANAGER',
			'option' => 'com_content'
		),
		6 => array(
			'label' => 'ARTICAL_TRASH',
			'option' => 'com_trash',
			'task' => 'viewContent'
		),
		7 => array(
			'label' => 'SECTION_MANAGER',
			'option' => 'com_sections',
			'scope' => 'content'
		),
		8 => array(
			'label' => 'CATEGORY_MANAGER',
			'option' => 'com_categories'
		),
		9 => array(
			'label' => 'FRONTPAGE_MANAGER',
			'option' => 'com_frontpage',
		),
		10 => array(
			'label' => 'FRONTPAGE_MANAGER',
			'option' => 'com_frontpage',
		),
		11 => array(
			'label' => 'INSTALL_UNINSTALL',
			'option' => 'com_installer',
		),
		12 => array(
			'label' => 'MODULE_MANAGER',
			'option' => 'com_modules',
		),
		13 => array(
			'label' => 'PLUGIN_MANAGER',
			'option' => 'com_plugins',
		),
		14 => array(
			'label' => 'TEMPLATE_MANAGER',
			'option' => 'com_templates',
		),
		15 => array(
			'label' => 'LANGUAGE_MANAGER',
			'option' => 'com_languages',
		),
		16 => array(
			'label' => 'READ_MESSAGES',
			'option' => 'com_messages',
		),
		17 => array(
			'label' => 'WRITE_MESSAGE',
			'option' => 'com_messages',
			'task' => 'add'
		),
		18 => array(
			'label' => 'MASS_MAIL',
			'option' => 'com_massmail'
		),
		19 => array(
			'label' => 'GLOBAL_CHECKIN',
			'option' => 'com_checkin'
		),
		20 => array(
			'label' => 'CLEAN_CACHE',
			'option' => 'com_cache'
		),
		21 => array(
			'label' => 'JOOMLA_HELP',
			'option' => 'com_admin',
			'task' => 'help'
		),
		22 => array(
			'label' => 'SYSTEM_INFO',
			'option' => 'com_admin',
			'task' => 'sysinfo'
		)
	);

	function __construct()
	{
		$this->JAdminModelUser();
	}

	function JAdminModelUser()
	{
		parent::__construct();

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jadmdate.php';

		$this->_settings = & CMSModel::getInstance('Setting', 'JAdminModel');

		$this->_loadPermissions();
	}

	function _getOptions()
	{
		$allOptions = array();

		foreach($this->_availablePermissions as $permId => $perm)
		{
			if(isset($perm['option'])) $allOptions[] = "'".$perm['option']."'";
		}

		$allOptions = array_unique($allOptions);

		return $allOptions;
	}

	function _loadPermissions()
	{
		foreach($this->_availablePermissions as $permId => $perm)
		{
			$this->_availablePermissions[$permId]['label'] = JText::_($this->_availablePermissions[$permId]['label']);
		}

		$sql = "SELECT
		    `name`,
		    element AS 'option'
		FROM #__extensions
		WHERE element NOT IN (".implode(',', $this->_getOptions()).",'com_user','com_wrapper','com_mailto','com_cpanel')
		AND type = 'component';";
		$this->_db->setQuery($sql);
		$results = $this->_db->loadAssocList();

		if(!empty($results))
		{
			foreach($results as $row)
			{
				$this->_availablePermissions[$row['option']] = array(
					'label' => $row['name'],
					'option' => $row['option']
				);
			}
		}
	}

	function getAvailablePermissions()
	{
		return $this->_availablePermissions;
	}

	function isKeyValid($authKey, $remoteIP = null)
	{
		if(!$remoteIP) $remoteIP = JRequest::getVar('REMOTE_ADDR', '0.0.0.0', 'server');

		if(strlen($authKey) == 15)
		{
			$whereClause = 'UPPER(SUBSTRING(u.auth_key, LENGTH(u.auth_key)*-1, 15))';
		}
		else
		{
			$whereClause = 'u.auth_key';
		}

		$sql = "SELECT
		    u.user_id,
		    u.fullname,
		    u.department,
		    u.sort_order,
		    u.params,
		    u.is_enabled,
		    u.cdate,
		    u.mdate,
		    u.last_auth_date,
		    u.internal_user_id
		FROM #__jadm_user u 
		WHERE ".$whereClause." = ".$this->_db->Quote($authKey)." 
		AND u.is_enabled = 1
		LIMIT 1;";
		$this->_db->setQuery($sql);

		$result = $this->_db->loadAssoc();

		if(isset($result['user_id']))
		{
			$date = new JAdminModelJADMDate();

			$nowUnixTime = $date->toUnix();

			$params = json_decode($result['params']);

			if(isset($params->ip_restrict))
			{
				if(!empty($params->ip_restrict))
				{
					// This user is IP restricted
					if(!in_array($remoteIP, $params->ip_restrict)) return false;
				}
			}

			if(isset($params->user_ip))
			{
				if($params->user_ip != $remoteIP)
				{
					$params->user_ip = $remoteIP;
				}
			}
			else
			{
				$params->user_ip = $remoteIP;
			}

			$data = new stdClass();

			$data->user_id = $result['user_id'];
			$data->params = json_encode($params);
			$data->last_auth_date = $nowUnixTime;
			$data->mdate = $nowUnixTime;

			$this->_pendingUpdates[] = $data;
		}

		return $result;
	}

	function isGatewayAuthKeyValid($authKey)
	{
		$dateObj = new JAdminModelJADMDate();

		$expireTime = $dateObj->toUnix() - 100;

		// Remove Expired Rows First
		$sql = "DELETE FROM #__jadm_user_keys
		WHERE is_expired = 1
		OR cdate <= ".$expireTime.";";
		$this->_db->setQuery($sql);
		$this->_db->query();

		$sql = "SELECT
		    j.`key_id`, 
		    j.`user_id`,
		    j.`auth_key`,
		    j.`is_expired`,
		    j.`cdate`,
		    j.`mdate` 
		FROM #__jadm_user_keys j 
		WHERE j.auth_key = ".$this->_db->Quote($authKey)."
		AND j.is_expired = 0
		LIMIT 1;";
		$this->_db->setQuery($sql);

		$result = $this->_db->loadAssoc();

		$sql = "UPDATE #__jadm_user_keys SET 
		    is_expired = 1,
		    mdate = ".$dateObj->toUnix()." 
		WHERE auth_key = ".$this->_db->Quote($authKey).";";
		$this->_db->setQuery($sql);
		$this->_db->query();

		return $result;
	}

	function applyPendingChanges()
	{
		if(!empty($this->_pendingUpdates))
		{
			// Perform updates last for performance reasons
			foreach($this->_pendingUpdates as $data)
			{
				$this->_db->updateObject('#__jadm_user', $data, 'user_id');
			}

			$this->_pendingUpdates = array();
		}
	}

	function getAll()
	{
		$sql = "SELECT
		    u.`id`,
		    u.`name`,
		    u.`username`,
		    u.`email`,
		    '' AS `usertype`,
		    u.`block`,
		    u.`sendEmail`,
		    '0' AS 'gid',
		    u.`registerDate`,
		    u.`lastvisitDate`,
		    u.`activation`,
		    u.`params`
		FROM #__users u;";
		$this->_db->setQuery($sql);

		return $this->_db->loadAssocList();
	}

	function getGatewayAuthKey($adminId)
	{
		$tmpAuthKey = $this->_generateAuthKey();

		$dateObj = new JAdminModelJADMDate();

		$data = new stdClass();

		$data->user_id = $adminId;
		$data->auth_key = $tmpAuthKey;
		$data->is_expired = 0;
		$data->cdate = $dateObj->toUnix();
		$data->mdate = $data->cdate;

		$this->_db->insertObject('#__jadm_user_keys', $data, 'key_id');

		return $tmpAuthKey;
	}

	function _generateAuthKey()
	{
		return substr(md5(uniqid(rand(), true)).md5(uniqid(rand(), true)), 0, rand(52, 64));
	}
}