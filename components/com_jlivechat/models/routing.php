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

class JLiveChatModelRouting extends CMSModel
{
	var $_settings = null;
	var $_operator = null;
	var $_popup = null;
	var $_chatMembers = null;
	var $_ringType = null;

	function __construct()
	{
		$this->JLiveChatModelRouting();
	}

	function JLiveChatModelRouting()
	{
		parent::__construct();

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jlcdate.php';

		$this->_settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');
		$this->_operator = & CMSModel::getInstance('Operator', 'JLiveChatModel');
		$this->_popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

		$this->_ringType = $this->_settings->getSetting('ring_order');
	}

	function requestChatFromOperators($chatSessionId, $operators, $forceSend = false)
	{
		if(!is_array($operators))
		{
			if(strpos($operators, ','))
			{
				// Operator list
				$operators = explode(',', $operators);
			}
			else
			{
				$operators = array($operators);
			}
		}

		$this->_sendToOperators($chatSessionId, $operators, $forceSend);
	}

	function requestChatFromDepartment($chatSessionId, $department)
	{
		$date = new JLiveChatModelJLCDate();

		$offlineTime = $date->toUnix() - $this->_popup->getOfflineSeconds();

		$sql = "SELECT
		    operator_id
		FROM #__jlc_operator
		WHERE last_auth_date >= ".$offlineTime."
		AND is_enabled = 1
		AND department = ".$this->_db->Quote($department)."
		ORDER BY sort_order ASC;";
		$this->_db->setQuery($sql);

		$results = $this->_db->loadAssocList();

		if(!empty($results))
		{
			$allOperators = array();

			foreach($results as $row)
			{
				$allOperators[] = $row['operator_id'];
			}

			$this->_sendToOperators($chatSessionId, $allOperators);
		}
		else
		{
			$this->setChatOffline($chatSessionId);
		}
	}

	function requestChatFromRouteId($chatSessionId, $routeId)
	{
		$route = $this->getRoute($routeId);

		if($route['route_source_data']->ring_type != 'default')
		{
			$this->_ringType = $route['route_source_data']->ring_type;
		}

		if($route['route_action'] == 'send_to_selected_operators')
		{
			$sendToOperators = array();

			if(!empty($route['route_source_data']->selected_operators))
			{
				foreach($route['route_source_data']->selected_operators as $operatorId)
				{
					$sendToOperators[] = (int)$operatorId;
				}
			}

			$this->_sendToOperators($chatSessionId, $sendToOperators);
		}
		elseif($route['route_action'] == 'send_to_all_operators')
		{
			$this->requestChat($chatSessionId, false);
		}
		else
		{
			$this->setChatOffline($chatSessionId);
		}
	}

	function requestChat($chatSessionId, $checkRoutes = true)
	{
		$routeId = $this->_triggerRoute();

		if($routeId && $checkRoutes) return $this->requestChatFromRouteId($chatSessionId, $routeId);

		$date = new JLiveChatModelJLCDate();

		$offlineTime = $date->toUnix() - $this->_popup->getOfflineSeconds();

		$sql = "SELECT
		    operator_id
		FROM #__jlc_operator
		WHERE last_auth_date >= ".$offlineTime."
		AND is_enabled = 1 
		ORDER BY sort_order ASC;";
		$this->_db->setQuery($sql);

		$results = $this->_db->loadAssocList();

		if(!empty($results))
		{
			$allOperators = array();

			foreach($results as $row)
			{
				$allOperators[] = $row['operator_id'];
			}

			$this->_sendToOperators($chatSessionId, $allOperators);
		}
		else
		{
			$this->setChatOffline($chatSessionId);
		}
	}

	function _sendToOperators($chatSessionId, $operators, $forceSend = false)
	{
		if(empty($operators)) return false;

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'pushclient.php';

		$pushClient = & CMSModel::getInstance('PushClient', 'JLiveChatModel');

		$date = new JLiveChatModelJLCDate();

		$setOffline = true;

		$offlineTime = $date->toUnix() - $this->_popup->getOfflineSeconds();

		foreach($operators as $operatorId)
		{
			$operator = $this->_operator->getOperator($operatorId);

			// If operator doesnt exist
			if(!$operator) continue;

			// If operator has not successfully connected yet
			if(!isset($operator['operator_params']->operator_ip)) continue;

			// If operator is offline
			if($operator['last_auth_date'] < $offlineTime) continue;

			if($this->_ringType == 'ring_in_order' && $this->anyPendingChatRequests($chatSessionId) && !$forceSend)
			{
				// This operator answer is still pending
				break;
			}

			if($this->isOperatorInChatSession($chatSessionId, $operatorId) && !$forceSend)
			{
				// This operator is in chat session already
				continue;
			}

			// Check if chat is aleady accepted
			if($this->isChatAccepted($chatSessionId) && !$forceSend) break;

			$requestExpireTime = $date->toUnix() + (int)$operator['accept_chat_timeout'];

			$data = new stdClass();

			$data->chat_session_id = $chatSessionId;
			$data->operator_id = $operatorId;
			$data->user_id = 0;
			$data->member_display_name = $operator['operator_name'];
			$data->member_ip_address = $operator['operator_params']->operator_ip;
			$data->is_gone = 0;
			$data->is_typing = 0;
			$data->cdate = $date->toUnix();
			$data->mdate = $data->cdate;
			$data->expire_time = $requestExpireTime;

			$this->_db->insertObject('#__jlc_chat_member', $data, 'member_id');

			// Send request to Push Notification Server too for mobile users
			$pushClient->sendNewChatRequestPushNotificationToOperator($operatorId, $chatSessionId, $operator['accept_chat_timeout']);

			if($this->_ringType == 'ring_in_order' && !$forceSend) break;
		}

		if(!$this->anyPendingChatRequests($chatSessionId) && !$this->isChatAccepted($chatSessionId))
		{
			$this->setChatOffline($chatSessionId);
		}
	}

	function isChatAccepted($chatSessionId)
	{
		$sql = "SELECT count(*) FROM #__jlc_chat_member
		WHERE chat_session_id = ".(int)$chatSessionId."
		AND operator_id > 0 
		AND is_accepted = 1;";
		$this->_db->setQuery($sql);

		if($this->_db->loadResult() >= 1) return true;

		return false;
	}

	function isOperatorInChatSession($chatSessionId, $operatorId)
	{
		if(!$this->_chatMembers) $this->_loadChatMembers($chatSessionId);

		if(count($this->_chatMembers) > 0)
		{
			if(in_array((int)$operatorId, $this->_chatMembers)) return true;
		}

		return false;
	}

	function anyPendingChatRequests($chatSessionId)
	{
		$dateObj = new JLiveChatModelJLCDate();

		$sql = "SELECT count(*) FROM #__jlc_chat_member
		WHERE chat_session_id = ".(int)$chatSessionId." 
		AND expire_time >= ".$dateObj->toUnix();
		$this->_db->setQuery($sql);

		if($this->_db->loadResult() > 0) return true;

		return false;
	}

	function _loadChatMembers($chatSessionId)
	{
		$sql = "SELECT 
		    operator_id 
		FROM #__jlc_chat_member
		WHERE chat_session_id = ".(int)$chatSessionId."
		AND is_gone = 0;";
		$this->_db->setQuery($sql);

		$results = $this->_db->loadAssocList();

		$this->_chatMembers = array();

		if(!empty($results))
		{
			foreach($results as $row)
			{
				if((int)$row['operator_id'] < 1 || in_array((int)$row['operator_id'], $this->_chatMembers) !== FALSE) continue;

				$this->_chatMembers[] = (int)$row['operator_id'];
			}
		}
	}

	function getRoute($routeId)
	{
		$sql = "SELECT
		    r.route_id,
		    r.route_name,
		    r.route_source_data,
		    r.route_action,
		    r.route_enabled,
		    r.route_cdate,
		    r.route_mdate,
		    r.route_params,
		    r.route_sort_order
		FROM #__jlc_route r
		WHERE r.route_id = ".(int)$routeId." 
		LIMIT 1;";
		$this->_db->setQuery($sql);

		$result = $this->_db->loadAssoc();

		if(strlen($result['route_source_data']) > 0)
		{
			$result['route_source_data'] = json_decode($result['route_source_data']);
		}
		else
		{
			$result['route_source_data'] = new stdClass();
		}

		if(strlen($result['route_params']) > 0)
		{
			$result['route_params'] = json_decode($result['route_params']);
		}
		else
		{
			$result['route_params'] = new stdClass();
		}

		return $result;
	}

	function getRoutes()
	{
		$sql = "SELECT
		    r.route_id,
		    r.route_name,
		    r.route_source_data,
		    r.route_action,
		    r.route_enabled,
		    r.route_cdate,
		    r.route_mdate,
		    r.route_params,
		    r.route_sort_order
		FROM #__jlc_route r
		WHERE r.route_enabled = 1 
		ORDER BY r.route_sort_order ASC;";
		$this->_db->setQuery($sql);

		$results = $this->_db->loadAssocList();

		if(!empty($results))
		{
			foreach($results as $key => $row)
			{
				if(strlen($row['route_source_data']) > 0)
				{
					$results[$key]['route_source_data'] = json_decode($row['route_source_data']);
				}
				else
				{
					$results[$key]['route_source_data'] = new stdClass();
				}

				if(strlen($row['route_params']) > 0)
				{
					$results[$key]['route_params'] = json_decode($row['route_params']);
				}
				else
				{
					$results[$key]['route_params'] = new stdClass();
				}
			}
		}

		return $results;
	}

	function setChatOffline($chatSessionId)
	{
		$date = new JLiveChatModelJLCDate();

		$data = new stdClass();

		$data->chat_session_id = $chatSessionId;
		$data->is_active = 0;
		$data->mdate = $date->toUnix();

		return $this->_db->updateObject('#__jlc_chat', $data, 'chat_session_id');
	}

	function _triggerRoute()
	{
		$rules = $this->getRoutes();

		if(!empty($rules))
		{
			$routeId = false;

			$visitor = & CMSModel::getInstance('Visitor', 'JLiveChatModel');

			$location = $visitor->locateIp(JRequest::getVar('REMOTE_ADDR', '0.0.0.0', 'server'));

			foreach($rules as $a => $route)
			{
				$results = array();

				$sourceData = get_object_vars($route['route_source_data']);

				foreach($sourceData as $key => $val)
				{
					if(is_numeric($key))
					{
						preg_match('@^([^:]+):(.+?)$@', $val, $ruleParts);

						switch($ruleParts[1])
						{
							case '*':
								// Select * Traffic
								$routeId = $route['route_id'];

								break 3;
							case 'by_city':
								if(trim(strtolower($location['user_city'])) == trim(strtolower($ruleParts[2])))
								{
									$results['by_city'] = true;
								}
								else
								{
									$results['by_city'] = false;
								}

								break;
							case 'by_country':
								if(trim(strtolower($location['user_country_code'])) == trim(strtolower($ruleParts[2])))
								{
									$results['by_country'] = true;
								}
								else
								{
									$results['by_country'] = false;
								}

								break;
							case 'by_ip_address':
								$results['by_ip_address'] = false;

								$remoteIP = JRequest::getVar('REMOTE_ADDR', null, 'server');

								$filterIP = $ruleParts[2];

								$filterIP = str_replace('*', '[0-9]+', $filterIP);
								$filterIP = str_replace('.', '\.', $filterIP);

								if(preg_match('@(^'.$filterIP.')@', $remoteIP) || $remoteIP == $ruleParts[2])
								{
									$results['by_ip_address'] = true;
								}

								break;
							case 'by_time':
								$results['by_time'] = false;

								$timeParts = explode('-', $ruleParts[2]);

								$time1Parts = explode(':', $timeParts[0]);
								$time2Parts = explode(':', $timeParts[1]);

								$currentHour = (int)date('H');
								$currentMinute = (int)date('i');

								if(((int)$time1Parts[0] >= $currentHour && (int)$time1Parts[1] >= $currentMinute) &&
										((int)$time2Parts[0] <= $currentHour && (int)$time2Parts[1] <= $currentMinute))
								{
									$results['by_time'] = true;
								}

								break;
							case 'by_day':
								$results['by_day'] = false;

								$today = date('l');
								$daysSelected = explode(',', $ruleParts[2]);

								for($z = 0; $z < count($daysSelected); $z++)
								{
									if(strtolower($daysSelected[$z]) == strtolower($today))
									{
										$results['by_day'] = true;
									}
								}

								break;
							case 'by_group':
								$results['by_group'] = false;

								$user = & JFactory::getUser();

								if($user->get('gid') == $ruleParts[2])
								{
									$results['by_group'] = true;
								}

								break;
							case 'by_user_status':
								$results['by_user_status'] = false;

								$user = & JFactory::getUser();

								if(strtolower($ruleParts[2]) == 'registered')
								{
									//  Looking for registered users only
									if($user->get('id') > 0)
									{
										//  User is registered
										$results['by_user_status'] = true;
									}
								}
								else
								{
									//  Looking for un-registered users only
									if($user->get('id') < 1)
									{
										//  User is registered
										$results['by_user_status'] = true;
									}
								}

								break;
						}
					}
				}

				if(!empty($results))
				{
					$everyRulePasses = true;

					foreach($results as $key => $val)
					{
						if(!$val) $everyRulePasses = false;
					}

					if($everyRulePasses)
					{
						// This route matches
						if($route['route_source_data']->ring_type != 'default')
						{
							$this->_ringType = $rules[$a]['route_source_data']->ring_type;
						}

						return $route['route_id'];
					}
				}
			}

			return $routeId;
		}
	}

	function isChatActive($chatSessionId)
	{
		$sql = "SELECT is_active FROM #__jlc_chat
		WHERE chat_session_id = ".(int)$chatSessionId." 
		LIMIT 1;";
		$this->_db->setQuery($sql);

		$result = $this->_db->loadResult();

		if($result == 0)
		{
			return false;
		}
		elseif($result == 1)
		{
			return true;
		}
	}

	function isOnline($specificOperators = null, $specificDepartment = null, $specificRouteId = null)
	{
		$ipblocker = & CMSModel::getInstance('IPBlocker', 'JLiveChatModel');

		if($ipblocker->isBlockedFromLiveChat()) return false;

		$sendToOperators = array();

		if(!empty($specificOperators))
		{
			$tmpArray = explode(',', $specificOperators);

			if(!empty($tmpArray))
			{
				foreach($tmpArray as $operatorId)
				{
					if((int)$operatorId > 0)
					{
						$sendToOperators[] = (int)$operatorId;
					}
				}
			}
		}
		elseif(!empty($specificDepartment))
		{
			$sql = "SELECT
			operator_id
		    FROM #__jlc_operator
		    WHERE is_enabled = 1
		    AND department = ".$this->_db->Quote($specificDepartment)." 
		    ORDER BY sort_order ASC;";
			$this->_db->setQuery($sql);

			$results = $this->_db->loadAssocList();

			if(!empty($results))
			{
				foreach($results as $row)
				{
					$sendToOperators[] = (int)$row['operator_id'];
				}
			}
		}
		elseif($specificRouteId > 0)
		{
			$route = $this->getRoute($specificRouteId);

			if($route['route_action'] == 'send_to_selected_operators')
			{
				if(!empty($route['route_source_data']->selected_operators))
				{
					foreach($route['route_source_data']->selected_operators as $operatorId)
					{
						$sendToOperators[] = (int)$operatorId;
					}
				}
			}
			elseif($route['route_action'] == 'send_to_all_operators')
			{
				$sql = "SELECT
			    operator_id
			FROM #__jlc_operator
			WHERE is_enabled = 1
			ORDER BY sort_order ASC;";
				$this->_db->setQuery($sql);

				$results = $this->_db->loadAssocList();

				if(!empty($results))
				{
					foreach($results as $row)
					{
						$sendToOperators[] = (int)$row['operator_id'];
					}
				}
			}
		}
		else
		{
			$sql = "SELECT
			operator_id
		    FROM #__jlc_operator
		    WHERE is_enabled = 1 
		    ORDER BY sort_order ASC;";
			$this->_db->setQuery($sql);

			$results = $this->_db->loadAssocList();

			if(!empty($results))
			{
				foreach($results as $row)
				{
					$sendToOperators[] = (int)$row['operator_id'];
				}
			}
		}

		return $this->areOperatorsOnline($sendToOperators);
	}

	function areOperatorsOnline($operatorsArray)
	{
		if(empty($operatorsArray)) return false;

		$date = new JLiveChatModelJLCDate();

		$offlineTime = $date->toUnix() - $this->_popup->getOfflineSeconds();

		$sql = "SELECT
		    operator_id
		FROM #__jlc_operator 
		WHERE operator_id IN (".implode(',', $operatorsArray).") 
		AND is_enabled = 1
		AND last_auth_date >= ".$offlineTime." 
		ORDER BY sort_order ASC;";
		$this->_db->setQuery($sql);

		$results = $this->_db->loadAssocList();

		$operatorsOnlineArray = array();

		if(!empty($results))
		{
			foreach($results as $row)
			{
				$operatorsOnlineArray[] = (int)$row['operator_id'];
			}
		}

		return $operatorsOnlineArray;
	}
}