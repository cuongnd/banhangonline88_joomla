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

class JAdminModelIPBlocker extends CMSModel
{
	var $_settings = null;
	var $_pendingUpdates = array();
	var $_blockedMessage = 'You have been permanently blocked from our website due to violating our website policies or you have been flagged as a malicious visitor. All further activity on your part will be logged and reported!';

	function __construct()
	{
		$this->JAdminModelIPBlocker();
	}

	function JAdminModelIPBlocker()
	{
		parent::__construct();

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jadmdate.php';

		$this->_settings = & CMSModel::getInstance('Setting', 'JAdminModel');
	}

	function addRule($adminUser, $descName, $sourceIPAddress)
	{
		$allowIPBlocker = true;

		$params = json_decode($adminUser['params']);

		if(is_object($params))
		{
			if(isset($params->ipblocker))
			{
				if($params->ipblocker == 1)
				{
					$allowIPBlocker = true;
				}
				else
				{
					$allowIPBlocker = false;
				}
			}
		}

		if(!$allowIPBlocker) return false;

		$dateObj = new JAdminModelJADMDate();

		$data = new stdClass();

		$data->user_id = $adminUser['user_id'];
		$data->source_ip = $sourceIPAddress;
		$data->rule_desc = $descName;
		$data->cdate = $dateObj->toUnix();
		$data->mdate = $data->cdate;

		return $this->_db->insertObject('#__jadm_ipblocker', $data, 'rule_id');
	}

	function getUserRules($userId)
	{
		$sql = "SELECT 
		    ipb.rule_id,
		    ipb.source_ip,
		    ipb.rule_desc,
		    ipb.rule_params,
		    ipb.cdate,
		    ipb.mdate 
		FROM #__jadm_ipblocker ipb
		WHERE ipb.user_id = ".(int)$userId.";";
		$this->_db->setQuery($sql);

		return $this->_db->loadObjectList();
	}

	function deleteRules($userId, $ruleIdArray)
	{
		if(!$userId || empty($ruleIdArray)) return false;

		$sql = "DELETE FROM #__jadm_ipblocker
		WHERE rule_id IN (".implode(',', $ruleIdArray).")
		AND user_id = ".(int)$userId;
		$this->_db->setQuery($sql);

		return $this->_db->query();
	}

	function getAllActiveRules()
	{
		$sql = "SELECT
		    ipb.source_ip,
		    ipb.rule_params 
		FROM #__jadm_ipblocker ipb

		INNER JOIN #__jadm_user o
		USING(user_id)

		WHERE o.is_enabled = 1;";
		$this->_db->setQuery($sql);

		$results = $this->_db->loadAssocList();

		if(!empty($results))
		{
			foreach($results as $a => $row)
			{
				if(!empty($row['rule_params']))
				{
					$results[$a]['rule_params'] = json_decode($row['rule_params']);
				}
			}
		}

		return $results;
	}

	function enforce()
	{
		if(!JRequest::getVar('REQUEST_METHOD', null, 'server') || !JRequest::getVar('HTTP_HOST', null, 'server') || !JRequest::getVar('REMOTE_ADDR', null, 'server')) return false;

		$activeRules = $this->getAllActiveRules();

		if(!empty($activeRules))
		{
			$sourceIP = JRequest::getVar('REMOTE_ADDR', null, 'server');

			foreach($activeRules as $rule)
			{
				if($sourceIP == $rule['source_ip'])
				{
					// This rule matches, enforce it
					echo $this->_blockedMessage;
					jexit();
				}
			}
		}
	}
}