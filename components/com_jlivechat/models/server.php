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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

@set_time_limit(0);

jimport('joomla.application.component.model');

class JLiveChatModelXMLRPCServer
{
	function acceptChatRequest($xmlRpcMsg)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'operator.php';
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'popup.php';

		$_operator = & CMSModel::getInstance('Operator', 'JLiveChatModel');
		$_popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

		//////////////////////////////////////////////////////
		//    Check security
		//////////////////////////////////////////////////////
		$authKeyParam = $xmlRpcMsg->getParam(0);
		$userAuthkey = $authKeyParam->me['string'];

		$operator = $_operator->isKeyValid($userAuthkey);

		if(!$operator) jexit('Access Denied');
		//////////////////////////////////////////////////////

		$chatSessionIdParam = $xmlRpcMsg->getParam(1);

		$chatSessionId = $chatSessionIdParam->me['int'];

		return new xmlrpcresp(new xmlrpcval((bool)$_popup->acceptChatRequest($operator['operator_id'], $chatSessionId), 'boolean'));
	}

	function declineChatRequest($xmlRpcMsg)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'operator.php';
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'popup.php';

		$_operator = & CMSModel::getInstance('Operator', 'JLiveChatModel');
		$_popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

		//////////////////////////////////////////////////////
		//    Check security
		//////////////////////////////////////////////////////
		$authKeyParam = $xmlRpcMsg->getParam(0);
		$userAuthkey = $authKeyParam->me['string'];

		$operator = $_operator->isKeyValid($userAuthkey);

		if(!$operator) jexit('Access Denied');
		//////////////////////////////////////////////////////

		$chatSessionIdParam = $xmlRpcMsg->getParam(1);

		$chatSessionId = $chatSessionIdParam->me['int'];

		return new xmlrpcresp(new xmlrpcval((bool)$_popup->declineChatRequest($operator['operator_id'], $chatSessionId), 'boolean'));
	}

	function sendMsg($xmlRpcMsg)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'operator.php';
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'popup.php';

		$_operator = & CMSModel::getInstance('Operator', 'JLiveChatModel');
		$_popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

		//////////////////////////////////////////////////////
		//    Check security
		//////////////////////////////////////////////////////
		$authKeyParam = $xmlRpcMsg->getParam(0);
		$userAuthkey = $authKeyParam->me['string'];

		$operator = $_operator->isKeyValid($userAuthkey);

		if(!$operator) jexit('Access Denied');
		//////////////////////////////////////////////////////

		$chatSessionIdParam = $xmlRpcMsg->getParam(1);
		$operatorMsgParam = $xmlRpcMsg->getParam(2);

		$chatSessionId = $chatSessionIdParam->me['int'];
		$operatorMsg = base64_decode($operatorMsgParam->me['string']);

		return new xmlrpcresp(new xmlrpcval((bool)$_popup->postOperatorMessage($chatSessionId, $operator['operator_id'], $operatorMsg), 'boolean'));
	}

	function endChatSession($xmlRpcMsg)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'operator.php';
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'popup.php';

		$_operator = & CMSModel::getInstance('Operator', 'JLiveChatModel');
		$_popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

		//////////////////////////////////////////////////////
		//    Check security
		//////////////////////////////////////////////////////
		$authKeyParam = $xmlRpcMsg->getParam(0);
		$userAuthkey = $authKeyParam->me['string'];

		$operator = $_operator->isKeyValid($userAuthkey);

		if(!$operator) jexit('Access Denied');
		//////////////////////////////////////////////////////

		$chatSessionIdParam = $xmlRpcMsg->getParam(1);
		$chatSessionId = $chatSessionIdParam->me['int'];

		return new xmlrpcresp(new xmlrpcval((bool)$_popup->endChatSession($chatSessionId, $operator['operator_id']), 'boolean'));
	}

	function transferChatSession($xmlRpcMsg)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'operator.php';
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'popup.php';

		$_operator = & CMSModel::getInstance('Operator', 'JLiveChatModel');
		$_popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

		//////////////////////////////////////////////////////
		//    Check security
		//////////////////////////////////////////////////////
		$authKeyParam = $xmlRpcMsg->getParam(0);
		$userAuthkey = $authKeyParam->me['string'];

		$operator = $_operator->isKeyValid($userAuthkey);

		if(!$operator) jexit('Access Denied');
		//////////////////////////////////////////////////////

		$sessionIdParam = $xmlRpcMsg->getParam(1);
		$sessionId = $sessionIdParam->me['int'];

		$transferToKeyIdParam = $xmlRpcMsg->getParam(2);
		$transferToKey = $transferToKeyIdParam->me['int'];

		return new xmlrpcresp(new xmlrpcval((int)$_popup->transferChatSession($sessionId, $transferToKey), 'int'));
	}

	function emailTranscript($xmlRpcMsg)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'operator.php';
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'popup.php';

		$_operator = & CMSModel::getInstance('Operator', 'JLiveChatModel');
		$_popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

		//////////////////////////////////////////////////////
		//    Check security
		//////////////////////////////////////////////////////
		$authKeyParam = $xmlRpcMsg->getParam(0);
		$userAuthkey = $authKeyParam->me['string'];

		$operator = $_operator->isKeyValid($userAuthkey);

		if(!$operator) jexit('Access Denied');
		//////////////////////////////////////////////////////

		$sessionIdParam = $xmlRpcMsg->getParam(1);
		$sessionId = $sessionIdParam->me['int'];

		$sendToEmailParam = $xmlRpcMsg->getParam(2);
		$sendToEmailAddress = $sendToEmailParam->me['string'];

		return new xmlrpcresp(new xmlrpcval((bool)$_popup->emailTranscript($sessionId, $sendToEmailAddress), 'boolean'));
	}

	function deleteMessage($xmlRpcMsg)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'operator.php';
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'message.php';

		$_operator = & CMSModel::getInstance('Operator', 'JLiveChatModel');
		$_message = & CMSModel::getInstance('Message', 'JLiveChatModel');

		//////////////////////////////////////////////////////
		//    Check security
		//////////////////////////////////////////////////////
		$authKeyParam = $xmlRpcMsg->getParam(0);
		$userAuthkey = $authKeyParam->me['string'];

		$operator = $_operator->isKeyValid($userAuthkey);

		if(!$operator) jexit('Access Denied');
		//////////////////////////////////////////////////////

		$messageIdParam = $xmlRpcMsg->getParam(1);
		$messageId = $messageIdParam->me['int'];

		return new xmlrpcresp(new xmlrpcval((bool)$_message->deleteMessage($messageId, $operator['operator_id']), 'boolean'));
	}

	function markMessageAsRead($xmlRpcMsg)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'operator.php';
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'message.php';

		$_operator = & CMSModel::getInstance('Operator', 'JLiveChatModel');
		$_message = & CMSModel::getInstance('Message', 'JLiveChatModel');

		//////////////////////////////////////////////////////
		//    Check security
		//////////////////////////////////////////////////////
		$authKeyParam = $xmlRpcMsg->getParam(0);
		$userAuthkey = $authKeyParam->me['string'];

		$operator = $_operator->isKeyValid($userAuthkey);

		if(!$operator) jexit('Access Denied');
		//////////////////////////////////////////////////////

		$messageIdParam = $xmlRpcMsg->getParam(1);
		$messageId = $messageIdParam->me['int'];

		return new xmlrpcresp(new xmlrpcval((bool)$_message->markAsRead($messageId, $operator['operator_id']), 'boolean'));
	}

	function requestOperatorChat($xmlRpcMsg)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'operator.php';
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'popup.php';

		$_operator = & CMSModel::getInstance('Operator', 'JLiveChatModel');
		$_popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

		//////////////////////////////////////////////////////
		//    Check security
		//////////////////////////////////////////////////////
		$authKeyParam = $xmlRpcMsg->getParam(0);
		$userAuthkey = $authKeyParam->me['string'];

		$operator = $_operator->isKeyValid($userAuthkey);

		if(!$operator) jexit('Access Denied');
		//////////////////////////////////////////////////////

		$operatorIdParam = $xmlRpcMsg->getParam(1);
		$operatorId = $operatorIdParam->me['int'];

		$chatSessionId = $_popup->requestOperatorChat($operator['operator_id'], $operatorId);

		return new xmlrpcresp(new xmlrpcval($chatSessionId, 'int'));
	}

	function requestVisitorChat($xmlRpcMsg)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'operator.php';
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'visitor.php';

		$_operator = & CMSModel::getInstance('Operator', 'JLiveChatModel');
		$_visitor = & CMSModel::getInstance('Visitor', 'JLiveChatModel');

		//////////////////////////////////////////////////////
		//    Check security
		//////////////////////////////////////////////////////
		$authKeyParam = $xmlRpcMsg->getParam(0);
		$userAuthkey = $authKeyParam->me['string'];

		$operator = $_operator->isKeyValid($userAuthkey);

		if(!$operator) jexit('Access Denied');
		//////////////////////////////////////////////////////

		$visitorIdParam = $xmlRpcMsg->getParam(1);
		$visitorId = $visitorIdParam->me['string'];

		$chatSessionId = $_visitor->requestChat($visitorId, $operator['operator_id']);

		return new xmlrpcresp(new xmlrpcval($chatSessionId, 'int'));
	}

	function addIPBlockerRule($xmlRpcMsg)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'operator.php';
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'ipblocker.php';

		$_operator = & CMSModel::getInstance('Operator', 'JLiveChatModel');
		$_ipBlocker = & CMSModel::getInstance('IPBlocker', 'JLiveChatModel');

		//////////////////////////////////////////////////////
		//    Check security
		//////////////////////////////////////////////////////
		$authKeyParam = $xmlRpcMsg->getParam(0);
		$userAuthkey = $authKeyParam->me['string'];

		$operator = $_operator->isKeyValid($userAuthkey);

		if(!$operator) jexit('Access Denied');
		//////////////////////////////////////////////////////

		$descNameParam = $xmlRpcMsg->getParam(1);
		$descName = $descNameParam->me['string'];

		if(!empty($descName))
		{
			$descName = base64_decode($descName);
		}
		else
		{
			$descName = null;
		}

		$sourceIPAddressParam = $xmlRpcMsg->getParam(2);
		$sourceIPAddress = $sourceIPAddressParam->me['string'];

		$ruleActionParam = $xmlRpcMsg->getParam(3);
		$ruleAction = $ruleActionParam->me['string'];

		return new xmlrpcresp(new xmlrpcval((bool)$_ipBlocker->addRule($operator, $descName, $sourceIPAddress, $ruleAction), 'boolean'));
	}

	function deleteIPBlockerRules($xmlRpcMsg)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'operator.php';
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'ipblocker.php';

		$_operator = & CMSModel::getInstance('Operator', 'JLiveChatModel');
		$_ipBlocker = & CMSModel::getInstance('IPBlocker', 'JLiveChatModel');

		//////////////////////////////////////////////////////
		//    Check security
		//////////////////////////////////////////////////////
		$authKeyParam = $xmlRpcMsg->getParam(0);
		$userAuthkey = $authKeyParam->me['string'];

		$operator = $_operator->isKeyValid($userAuthkey);

		if(!$operator) jexit('Access Denied');
		//////////////////////////////////////////////////////

		$ruleIdsParam = $xmlRpcMsg->getParam(1);
		$ruleIds = $ruleIdsParam->me['array'];

		$deleteRuleIds = array();

		if(!empty($ruleIds))
		{
			foreach($ruleIds as $xmlRpcValObj)
			{
				$deleteRuleIds[] = (int)php_xmlrpc_decode($xmlRpcValObj);
			}
		}

		return new xmlrpcresp(new xmlrpcval((bool)$_ipBlocker->deleteRules($operator['operator_id'], $deleteRuleIds), 'boolean'));
	}

	function updateOperatorSyncMethod($xmlRpcMsg)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'operator.php';
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'sync.php';

		$_operator = & CMSModel::getInstance('Operator', 'JLiveChatModel');

		//////////////////////////////////////////////////////
		//    Check security
		//////////////////////////////////////////////////////
		$authKeyParam = $xmlRpcMsg->getParam(0);
		$userAuthkey = $authKeyParam->me['string'];

		$operator = $_operator->isKeyValid($userAuthkey);

		if(!$operator) jexit('Access Denied');
		//////////////////////////////////////////////////////

		$syncObj = & CMSModel::getInstance('Sync', 'JLiveChatModel');
		$syncObj->setOperator($operator);

		$uuidParam = $xmlRpcMsg->getParam(1);
		$deviceUUID = $uuidParam->me['string'];

		$syncObj->setUUID($deviceUUID);

		$syncModeParam = $xmlRpcMsg->getParam(2);
		$syncMode = $syncModeParam->me['string'];

		$operatorIP = null;
		$operatorListenPort = null;

		if($syncMode == 'push')
		{
			$operatorIPParam = $xmlRpcMsg->getParam(3);
			$operatorIP = $operatorIPParam->me['string'];

			$operatorListenPortParam = $xmlRpcMsg->getParam(4);
			$operatorListenPort = $operatorListenPortParam->me['int'];
		}

		return new xmlrpcresp(new xmlrpcval((bool)$syncObj->updateOperatorSyncMethod($syncMode, $operatorIP, $operatorListenPort), 'boolean'));
	}

}

class JLiveChatModelRestfulServer
{
	var $_deviceUUID = null;

	function setUUID($uuid)
	{
		$this->_deviceUUID = $uuid;
	}

	function accept_chat_request(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'popup.php';

		$_popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

		return $_popup->acceptChatRequest($operator['operator_id'], $params[0]);
	}

	function decline_chat_request(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'popup.php';

		$_popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

		return $_popup->declineChatRequest($operator['operator_id'], $params[0]);
	}

	function send_msg(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'popup.php';

		$_popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

		return $_popup->postOperatorMessage($params[0], $operator['operator_id'], base64_decode($params[1]));
	}

	function end_chat_session(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'popup.php';

		$_popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

		return $_popup->endChatSession($params[0], $operator['operator_id']);
	}

	function transfer_chat_session(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'popup.php';

		$_popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

		return $_popup->transferChatSession($params[0], $params[1]);
	}

	function email_transcript(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'popup.php';

		$_popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

		return $_popup->emailTranscript($params[0], $params[1]);
	}

	function delete_message(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'message.php';

		$_message = & CMSModel::getInstance('Message', 'JLiveChatModel');

		$msgIds = explode(',', rtrim($params[0], ','));

		if(!empty($msgIds))
		{
			foreach($msgIds as $msgId)
			{
				$_message->deleteMessage($msgId, $operator['operator_id']);
			}
		}

		return true;
	}

	function mark_message_as_read(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'message.php';

		$_message = & CMSModel::getInstance('Message', 'JLiveChatModel');

		return $_message->markAsRead($params[0], $operator['operator_id']);
	}

	function request_operator_chat(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'popup.php';

		$_popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');
		$_popup->setRestfulAPI();

		return $_popup->requestOperatorChat($operator['operator_id'], $params[0]);
	}

	function request_visitor_chat(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'visitor.php';

		$_visitor = & CMSModel::getInstance('Visitor', 'JLiveChatModel');
		$_visitor->setRestfulAPI();

		return $_visitor->requestChat($params[0], $operator['operator_id']);
	}

	function add_ipblocker_rule(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'ipblocker.php';

		$_ipBlocker = & CMSModel::getInstance('IPBlocker', 'JLiveChatModel');

		$descName = $params[0];

		if(!empty($params[0]))
		{
			$descName = base64_decode($params[0]);
		}
		else
		{
			$descName = null;
		}

		return $_ipBlocker->addRule($operator, $descName, $params[1], $params[2]);
	}

	function delete_ipblocker_rule(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'ipblocker.php';

		$_ipBlocker = & CMSModel::getInstance('IPBlocker', 'JLiveChatModel');

		$deleteRuleIds = explode(',', rtrim($params[0], ','));

		return $_ipBlocker->deleteRules($operator['operator_id'], $deleteRuleIds);
	}

	function update_operator_sync_method(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'sync.php';

		$syncObj = & CMSModel::getInstance('Sync', 'JLiveChatModel');
		$syncObj->setOperator($operator);
		$syncObj->setUUID($this->_deviceUUID);

		$syncMode = $params[0];

		$operatorIP = null;
		$operatorListenPort = null;

		if($syncMode == 'push')
		{
			//$operatorIP = $params[1];
			$operatorIP = JRequest::getVar('REMOTE_ADDR', '0.0.0.0', 'server');
			$operatorListenPort = $params[2];
		}

		return $syncObj->updateOperatorSyncMethod($syncMode, $operatorIP, $operatorListenPort);
	}

	function send_message_reply(&$operator, &$params)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'message.php';

		$_model = & CMSModel::getInstance('Message', 'JLiveChatModel');

		$messageId = & $params[0];
		$messageEmail = & $params[1];
		$messageSubject = str_replace("\n", '<br>', base64_decode($params[2]));
		$messageBody = str_replace("\n", '<br>', base64_decode($params[3]));

		return $_model->sendMessageReply($messageId, $messageEmail, $messageSubject, $messageBody);
	}
}

class JLiveChatModelServer
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
				require_once $currentPath.DIRECTORY_SEPARATOR.'operator.php';
				require_once $currentPath.DIRECTORY_SEPARATOR.'popup.php';
				require_once $currentPath.DIRECTORY_SEPARATOR.'sync.php';

				$key = JRequest::getVar('k', null, 'method');
				$online = JRequest::getInt('currently_online', 0, 'method');
				$operatorName = JRequest::getVar('operator_name', null, 'method');
				$operatorAcceptTimeout = JRequest::getInt('operator_accept_timeout', 60, 'method');
				$systemUUID = JRequest::getVar('uuid', null, 'method');

				$operatorObj = & CMSModel::getInstance('Operator', 'JLiveChatModel');

				$operator = $operatorObj->isKeyValid($key, $online, $operatorName, $operatorAcceptTimeout, null);

				if(!$operator || empty($key)) jexit('Access Denied');

				$popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');

				$syncObj = & CMSModel::getInstance('Sync', 'JLiveChatModel');
				$syncObj->setOperator($operator);
				$syncObj->setUUID($systemUUID);

				$chatSessionsCurrentlyTyping = JRequest::getVar('typing_status', array(), 'method');

				if(!empty($chatSessionsCurrentlyTyping)) $chatSessionsCurrentlyTyping = explode(',', $chatSessionsCurrentlyTyping);

				$operatorObj->applyPendingChanges();
				$popup->updateOperatorTypingStatus($operator['operator_id'], $chatSessionsCurrentlyTyping);

				$callback = JRequest::getVar('callback', null, 'get');

				if(empty($callback))
				{
					echo json_encode($syncObj->getOperatorData());
				}
				else
				{
					echo $callback.'('.json_encode($syncObj->getOperatorData()).');';
				}

				break;
			case 'restful':
				require_once $currentPath.DIRECTORY_SEPARATOR.'operator.php';
				require_once $currentPath.DIRECTORY_SEPARATOR.'sync.php';

				$key = JRequest::getVar('k', null, 'method');

				$operatorObj = & CMSModel::getInstance('Operator', 'JLiveChatModel');

				$operator = $operatorObj->isKeyValid($key);

				if(!$operator || empty($key)) jexit('Access Denied');

				$systemUUID = JRequest::getVar('uuid', null, 'method');
				$task = JRequest::getVar('rest_task', null, 'method');
				$numOfParams = JRequest::getVar('num_of_params', 0, 'method');

				$params = array();

				for($a = 0; $a < $numOfParams; $a++)
				{
					$params[$a] = JRequest::getVar('param'.($a + 1), null, 'method');
				}

				$restServerObj = new JLiveChatModelRestfulServer();
				$restServerObj->setUUID($systemUUID);

				if(method_exists($restServerObj, $task))
				{
					$output = array('result' => $restServerObj->$task($operator, $params));
				}
				else
				{
					$output = array('result' => 'Invalid task specified');
				}

				$syncObj = & CMSModel::getInstance('Sync', 'JLiveChatModel');

				$callback = JRequest::getVar('callback', null, 'get');

				if(empty($callback))
				{
					echo json_encode($output);
				}
				else
				{
					echo $callback.'('.json_encode($output).');';
				}

				break;
			default:
				break;
		}

		jexit();
	}
}