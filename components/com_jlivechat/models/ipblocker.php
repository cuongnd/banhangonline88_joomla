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

class JLiveChatModelIPBlocker extends CMSModel
{
	var $_settings = null;
	var $_pendingUpdates = array();
	var $_blockedMessage = 'You have been permanently blocked from our website due to violating our website policies or you have been flagged as a malicious visitor. All further activity on your part will be logged and reported!';

	function __construct()
	{
		$this->JLiveChatModelIPBlocker();
	}

	function JLiveChatModelIPBlocker()
	{
		parent::__construct();

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jlcdate.php';

		$this->_settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');
	}

	function addRule($operator, $descName, $sourceIPAddress, $ruleAction)
	{
		$allowIPBlocker = true;

		$operatorParams = json_decode($operator['operator_params']);

		if(is_object($operatorParams))
		{
			if(isset($operatorParams->ipblocker))
			{
				if($operatorParams->ipblocker == 1)
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

		$dateObj = new JLiveChatModelJLCDate();

		$data = new stdClass();

		$data->operator_id = (int)$operator['operator_id'];
		$data->source_ip = $sourceIPAddress;
		$data->rule_desc = $descName;
		$data->rule_action = $ruleAction;
		$data->cdate = $dateObj->toUnix();
		$data->mdate = $data->cdate;

		return $this->_db->insertObject('#__jlc_ipblocker', $data, 'rule_id');
	}

	function getOperatorRules($operatorId)
	{
		$sql = "SELECT 
		    ipb.rule_id,
		    ipb.source_ip,
		    ipb.rule_desc,
		    ipb.rule_action,
		    ipb.rule_params,
		    ipb.cdate,
		    ipb.mdate 
		FROM #__jlc_ipblocker ipb
		WHERE ipb.operator_id = ".(int)$operatorId.";";
		$this->_db->setQuery($sql);

		$results = $this->_db->loadObjectList();

		if(!empty($results))
		{
			foreach($results as $a => $row)
			{
				$cdateObj = new JLiveChatModelJLCDate($row->cdate);
				$results[$a]->cdate = $cdateObj->toUnix(true);

				$mdateObj = new JLiveChatModelJLCDate($row->mdate);
				$results[$a]->mdate = $mdateObj->toUnix(true);
			}
		}

		return $results;
	}

	function deleteRules($operatorId, $ruleIdArray)
	{
		if(!$operatorId || empty($ruleIdArray)) return false;

		$sql = "DELETE FROM #__jlc_ipblocker
		WHERE rule_id IN (".implode(',', $ruleIdArray).")
		AND operator_id = ".(int)$operatorId;
		$this->_db->setQuery($sql);

		return $this->_db->query();
	}

	function getAllActiveRules()
	{
		$sql = "SELECT
		    ipb.source_ip,
		    ipb.rule_action,
		    ipb.rule_params 
		FROM #__jlc_ipblocker ipb

		INNER JOIN #__jlc_operator o
		USING(operator_id)

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
					if($rule['rule_action'] == 'block_from_website')
					{
						echo $this->_blockedMessage;
						jexit();
					}
				}
			}
		}
	}

	function isBlockedFromLiveChat()
	{
		$sql = "SELECT count(*) FROM #__jlc_ipblocker ipb

		INNER JOIN #__jlc_operator o
		USING(operator_id)

		WHERE o.is_enabled = 1
		AND ipb.source_ip = ".$this->_db->Quote(JRequest::getVar('REMOTE_ADDR', '', 'server'))."
		AND ipb.rule_action = 'block_from_livechat';";
		$this->_db->setQuery($sql);

		if($this->_db->loadResult() > 0) return true;

		return false;
	}
}