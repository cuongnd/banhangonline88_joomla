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

class JLiveChatModelPushClient extends CMSModel
{
	var $_serverUri = 'https://www.cmsfruit.com/index.php?option=com_cmsfruit&view=api';
	var $_settings = null;

	function __construct()
	{
		$this->JLiveChatModelPushClient();
	}

	function JLiveChatModelPushClient()
	{
		parent::__construct();

		$currentPath = dirname(__FILE__);

		require_once $currentPath.DIRECTORY_SEPARATOR.'misc.php';

		$this->_settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');
	}

	function registerWebsite()
	{
		$acctNumber = $this->_settings->getSetting('push_service_acct_number');

		if(empty($acctNumber))
		{
			// This website is not registered yet, register it for this service
			$postDataArray = array(
				'website' => JURI::root()
			);

			$misc = new JLiveChatModelMisc();

			$result = $misc->post($this->_serverUri.'&task=push_service_register_website', JLC_APP_NAME.' '.JLC_APP_VERSION, false, true, false, $postDataArray);

			// Check if we got a response back
			if(!empty($result))
			{
				$result = json_decode($result);

				if(isset($result->acct_number) && isset($result->acct_password))
				{
					$this->_settings->setSetting('push_service_acct_number', $result->acct_number);
					$this->_settings->setSetting('push_service_acct_password', $result->acct_password);

					$this->_settings->saveSettings();

					return true;
				}
			}

			return false;
		}
		else
		{
			return true;
		}
	}

	function sendNewChatRequestPushNotificationToOperator($operatorId, $chatSessionId, $expireInSeconds)
	{
		// Ensure push notifications are enabled
		if((int)$this->_settings->getSetting('use_pushservice') != 1 || !$this->_settings->getSetting('has_mobile_users') || $operatorId < 1 || $chatSessionId < 1) return false;

		$this->registerWebsite();

		$acctNumber = $this->_settings->getSetting('push_service_acct_number');
		$acctPassword = $this->_settings->getSetting('push_service_acct_password');

		if(empty($acctNumber) || empty($acctPassword)) return false;

		$sql = "SELECT chat_alt_id FROM #__jlc_chat 
                WHERE chat_session_id = ".$chatSessionId." 
                LIMIT 1;";
		$this->_db->setQuery($sql);

		$chatAltId = $this->_db->loadResult();

		// Get chat requestor record
		$sql = "SELECT 
                    member_display_name, 
                    member_city, 
                    member_country, 
                    member_country_code,
                    member_ip_address, 
                    cdate 
                FROM #__jlc_chat_member 
                WHERE chat_session_id = ".$chatSessionId." 
                AND operator_id != ".$operatorId."
                ORDER BY cdate ASC 
                LIMIT 1;";
		$this->_db->setQuery($sql);

		$chatMemberRecord = $this->_db->loadAssoc();

		$postDataArray = array(
			'push_acct_number' => $acctNumber,
			'push_acct_password' => $acctPassword,
			'send_to_operator_id' => $operatorId,
			'chat_session_id' => $chatSessionId,
			'chat_alt_id' => $chatAltId,
			'expire_in_seconds' => $expireInSeconds,
			'website_name' => $this->_settings->getSiteName(),
			'requestor_name' => $chatMemberRecord['member_display_name'],
			'requestor_city' => $chatMemberRecord['member_city'],
			'requestor_country' => $chatMemberRecord['member_country'],
			'requestor_country_code' => $chatMemberRecord['member_country_code'],
			'requestor_ip_address' => $chatMemberRecord['member_ip_address'],
			'request_cdate' => $chatMemberRecord['cdate']
		);

		$misc = new JLiveChatModelMisc();

		$result = $misc->post($this->_serverUri.'&task=push_service_jlc_new_chat_request', JLC_APP_NAME.' '.JLC_APP_VERSION, false, true, false, $postDataArray);

		// Check if we got a response back
		if(!empty($result))
		{
			$result = json_decode($result);

			if(isset($result->success)) return $result->success;
		}

		return false;
	}

	function sendNewMessagePushNotification($operatorId, $messageFullName, $messageEmail, $messageIP, $messageCity, $messageCountry, $messageCountryCode)
	{
		// Ensure push notifications are enabled
		if((int)$this->_settings->getSetting('use_pushservice') != 1 || !$this->_settings->getSetting('has_mobile_users')) return false;

		$this->registerWebsite();

		$acctNumber = $this->_settings->getSetting('push_service_acct_number');
		$acctPassword = $this->_settings->getSetting('push_service_acct_password');

		if(empty($acctNumber) || empty($acctPassword)) return false;

		$postDataArray = array(
			'push_acct_number' => $acctNumber,
			'push_acct_password' => $acctPassword,
			'website_name' => $this->_settings->getSiteName(),
			'message_fullname' => $messageFullName,
			'message_email' => $messageEmail,
			'message_ip' => $messageIP,
			'message_city' => $messageCity,
			'message_country' => $messageCountry,
			'message_country_code' => $messageCountryCode,
			'operator_id' => $operatorId
		);

		$misc = new JLiveChatModelMisc();

		$result = $misc->post($this->_serverUri.'&task=push_service_jlc_new_message', JLC_APP_NAME.' '.JLC_APP_VERSION, false, true, false, $postDataArray);

		// Check if we got a response back
		if(!empty($result))
		{
			$result = json_decode($result);

			if(isset($result->success)) return $result->success;
		}

		return false;
	}
}