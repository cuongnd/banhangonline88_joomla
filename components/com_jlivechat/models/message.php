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

class JLiveChatModelMessage extends CMSModel
{
	var $_settings = null;
	var $_visitorLocation = null;
	var $_emailsSent = null;
	var $_dateObj = null;

	function __construct()
	{
		$this->JLiveChatModelMessage();
	}

	function JLiveChatModelMessage()
	{
		parent::__construct();

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jlcdate.php';

		$this->_dateObj = new JLiveChatModelJLCDate();
		$this->_settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');
	}

	function leaveMessageForDepartment($department, $name, $email, $msgTxt, $phone = null)
	{
		$sql = "SELECT
		    operator_id
		FROM #__jlc_operator
		WHERE is_enabled = 1
		AND department = ".$this->_db->Quote($department)."
		ORDER BY sort_order ASC;";
		$this->_db->setQuery($sql);

		$results = $this->_db->loadAssocList();

		if(!empty($results))
		{
			foreach($results as $row)
			{
				$this->sendMessageToOperator($row['operator_id'], $name, $email, $msgTxt, $phone);
			}
		}

		return true;
	}

	function leaveMessageForOperators($operators, $name, $email, $msgTxt, $phone = null)
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

		if(!empty($operators))
		{
			foreach($operators as $operator)
			{
				$operatorId = (int)$operator;

				if($operatorId > 0)
				{
					$this->sendMessageToOperator($operatorId, $name, $email, $msgTxt, $phone);
				}
			}
		}

		return true;
	}

	function leaveMessageForRoute($routeId, $name, $email, $msgTxt, $phone = null)
	{
		$routing = & CMSModel::getInstance('Routing', 'JLiveChatModel');

		$route = $routing->getRoute($routeId);

		if(!$route) return false;

		if($route['route_action'] == 'send_to_selected_operators' && count($route['route_params']->selected_operators) > 0)
		{
			$sendToOperators = array();

			foreach($route['route_params']->selected_operators as $operatorId)
			{
				$this->sendMessageToOperator($operatorId, $name, $email, $msgTxt, $phone);
			}
		}
		elseif($route['route_action'] == 'send_to_all_operators')
		{
			$this->leaveMessage($name, $email, $msgTxt, $phone);
		}

		return true;
	}

	function leaveMessage($name, $email, $msgTxt, $phone = null)
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
				$this->sendMessageToOperator($row['operator_id'], $name, $email, $msgTxt, $phone);
			}
		}

		return true;
	}

	function sendMessageToOperator($operatorId, $name, $email, $msgTxt, $phone = null)
	{
		$mainframe = & JFactory::getApplication();

		$date = new JLiveChatModelJLCDate();
		$user = & JFactory::getUser();
		$visitor = & CMSModel::getInstance('Visitor', 'JLiveChatModel');

		$clientIP = JRequest::getVar('REMOTE_ADDR', '0.0.0.0', 'server');

		if(!$this->_visitorLocation) $this->_visitorLocation = $visitor->locateIp($clientIP);

		// Leave a message
		$data = new stdClass();

		$data->operator_id = (int)$operatorId;
		$data->user_id = (int)$user->get('id');

		if($data->user_id > 0)
		{
			$data->registered_name = $user->get('name');
			$data->registered_email = $user->get('email');
		}

		$data->message_name = $name;
		$data->message_email = $email;
		$data->message_txt = $msgTxt;
		$data->guest_city = $this->_visitorLocation['city'];
		$data->guest_country = $this->_visitorLocation['country'];
		$data->guest_country_code = $this->_visitorLocation['country_code'];
		$data->guest_web_browser = JRequest::getVar('HTTP_USER_AGENT', JText::_('UNKNOWN'), 'server');
		$data->guest_ip_address = $clientIP;
		$data->is_read = 0;
		$data->cdate = $this->_dateObj->toUnix();
		$data->mdate = $data->cdate;

		if($phone)
		{
			if(strlen($phone) > 0) $data->message_phone = $phone;
		}

		$this->_db->insertObject('#__jlc_message', $data, 'message_id');

		$messageId = $this->_db->insertid();

		$emailMessagesTo = $this->_settings->getSetting('email_messages_to');

		if(!empty($emailMessagesTo))
		{
			//  Email messages as well
			jimport('joomla.mail.mail');

			$mailer = & JFactory::getMailer();
			$lang = & JFactory::getLanguage();

			$css = "body {\r\nmargin: 0; \r\n padding: 10px;\r\n font-size: 12px;\r\n font-family: arial, helvetica;\r\n}\r\n\r\nstrong { color: #00426C; }";

			$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\r\n";
			$html .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$lang->getTag().'" lang="'.$lang->getTag().'" >'."\r\n";
			$html .= "<head>\r\n";

			$html = "<style type=\"text/css\">\r\n";
			$html .= $css;
			$html .= "</style>\r\n";
			$html .= "</head>\r\n";
			$html .= "<body>\r\n";
			$html .= '<span style="color: black;">';

			$html .= JText::_('NEW_MESSAGE_FOLLOWING_DETAILS')."<br /><br />\r\n";

			$html .= "<strong>".JText::_('DATETIME_LBL')."</strong> ".$date->toFormat('%m/%d/%Y %H:%M:%S')."\r\n<br />";
			$html .= "<strong>".JText::_('NAME_LBL')."</strong> ".$name."\r\n<br />";
			$html .= "<strong>".JText::_('EMAIL_LBL')."</strong> ".$email."\r\n<br />";
			$html .= "<strong>".JText::_('LOCATION_LBL')."</strong> ".$this->_visitorLocation['city'].', '.$this->_visitorLocation['country']."\r\n<br />";

			if($phone)
			{
				if(strlen($phone) > 0)
				{
					$html .= "<strong>".JText::_('PHONE_LBL')."</strong> ".$phone."\r\n<br />";
				}
			}

			if($user->get('id') > 0)
			{
				$userRegDate = new JLiveChatModelJLCDate(strtotime($user->get('registerDate')));

				$html .= "<strong>".JText::_('REGISTERED_NAME_LBL')."</strong> ".$user->get('name')."\r\n<br />";
				$html .= "<strong>".JText::_('REGISTERED_EMAIL_LBL')."</strong> ".$user->get('email')."\r\n<br />";
				$html .= "<strong>".JText::_('REGISTERED_DATE')."</strong> ".$userRegDate->toFormat('%m/%d/%Y')."\r\n<br />";
			}

			$html .= "<strong>".JText::_('IPADDRESS_LBL')."</strong> ".$clientIP."\r\n<br />";
			$html .= "<strong>".JText::_('WEBBROWSER_LBL')."</strong> ".JRequest::getVar('HTTP_USER_AGENT', JText::_('UNKNOWN'), 'server')."\r\n<br /><br />";
			$html .= "<strong>".JText::_('MESSAGE_LBL')."</strong>\r\n<br />";
			$html .= str_replace("\n", '<br />', $msgTxt);
			$html .= '</span>';

			$html .= "</body>\r\n";
			$html .= "</html>\r\n";

			$from = array(
				0 => $this->_settings->getSetting('emails_from'),
				1 => $this->_settings->getSiteName()
			);

			$replyTo = array(
				0 => $email,
				1 => $name
			);

			$mailer->AddReplyTo($replyTo);

			$mailer->setSender($from);

			$mailer->setSubject(JText::sprintf('NEW_MESSAGE_FROM', $name));

			$mailer->IsHTML(true);

			$mailer->setBody($html);

			// Base64 encode email to prevent problems
			$mailer->Encoding = 'base64';

			$msgHash = md5($msgTxt);

			if(!$this->_emailsSent || !is_array($this->_emailsSent))
			{
				$this->_emailsSent = array();
			}

			foreach($this->_settings->getSetting('email_messages_to') as $sendToEmail)
			{
				if(isset($this->_emailsSent[$sendToEmail]))
				{
					if($this->_emailsSent[$sendToEmail] == $msgHash)
					{
						// Skip this message has already been emailed to this person
						continue;
					}
				}

				$this->_emailsSent[$sendToEmail] = $msgHash;

				$mailer->ClearAddresses(); // Clear receipents

				$mailer->addRecipient($sendToEmail);

				$mailer->Send();
			}
		}

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'pushclient.php';

		$pushClient = & CMSModel::getInstance('PushClient', 'JLiveChatModel');
		$pushClient->sendNewMessagePushNotification($operatorId, $name, $email, $clientIP, $this->_visitorLocation['city'], $this->_visitorLocation['country'], $this->_visitorLocation['country_code']);

		return $messageId;
	}

	function visitorSecurityCheck()
	{
		$date = new JLiveChatModelJLCDate();

		$interval = $date->toUnix() - 300; // 5 minutes

		$sql = "SELECT message_id FROM #__jlc_message
		WHERE guest_ip_address = ".$this->_db->Quote(JRequest::getVar('REMOTE_ADDR', '0.0.0.0', 'server'))."
		AND cdate >= ".$interval."
		GROUP BY cdate;";

		$this->_db->setQuery($sql);

		$results = $this->_db->loadAssocList();

		if(count($results) >= 4)
		{
			// Too many messages
			return false;
		}

		return true;
	}

	function getOperatorMessages($operatorId)
	{
		$sql = "SELECT
		    m.message_id,
		    m.operator_id,
		    m.user_id,
		    m.registered_name, 
		    m.registered_email,
		    m.message_name,
		    m.message_email,
		    m.message_phone,
		    m.message_txt,
		    m.guest_city,
		    m.guest_country,
		    m.guest_country_code,
		    m.guest_web_browser,
		    m.guest_ip_address,
		    m.is_read,
		    m.params,
		    m.cdate,
		    m.mdate
		FROM #__jlc_message m
		WHERE m.operator_id = ".(int)$operatorId.";";
		$this->_db->setQuery($sql);

		return $this->_db->loadObjectList();
	}

	function getAllRelatedMessageIds($messageId)
	{
		$result = array();

		$sql = "SELECT
		    m.user_id,
		    m.message_name,
		    m.message_email,
		    m.cdate,
		    m.guest_web_browser,
		    m.guest_ip_address 
		FROM #__jlc_message m
		WHERE m.message_id = ".(int)$messageId."
		LIMIT 1;";
		$this->_db->setQuery($sql);

		$message = $this->_db->loadAssoc();

		if(empty($message)) return $result; // Something went wrong

		$sql = "SELECT
		    DISTINCT m.message_id
		FROM #__jlc_message m
		WHERE m.user_id = ".(int)$message['user_id']."
		AND m.message_name = ".$this->_db->Quote($message['message_name'])."
		AND m.message_email = ".$this->_db->Quote($message['message_email'])."
		AND (m.cdate >= ".$message['cdate']." AND m.cdate <= ".($message['cdate'] + 10).")
		AND m.guest_web_browser = ".$this->_db->Quote($message['guest_web_browser'])." 
		AND m.guest_ip_address = ".$this->_db->Quote($message['guest_ip_address']).";";
		$this->_db->setQuery($sql);

		$rows = $this->_db->loadAssocList();

		if(!empty($rows))
		{
			foreach($rows as $row)
			{
				$result[] = $row['message_id'];
			}
		}

		return $result;
	}

	function deleteMessage($messageId, $operatorId)
	{
		if($this->_settings->getSetting('universal_messages') == 1)
		{
			// Universal message treatment is enabled
			$messageIds = $this->getAllRelatedMessageIds($messageId);

			if(empty($messageIds)) return false; // soemthing went wrong

			$sql = "DELETE FROM #__jlc_message
		    WHERE message_id IN (".implode(',', $messageIds).");";
		}
		else
		{
			$sql = "DELETE FROM #__jlc_message
		    WHERE message_id = ".(int)$messageId."
		    AND operator_id = ".(int)$operatorId;
		}

		$this->_db->setQuery($sql);

		return $this->_db->query();
	}

	function markAsRead($messageId, $operatorId)
	{
		$dateObj = new JLiveChatModelJLCDate();

		if($this->_settings->getSetting('universal_messages') == 1)
		{
			// Universal message treatment is enabled
			$messageIds = $this->getAllRelatedMessageIds($messageId);

			if(empty($messageIds)) return false; // soemthing went wrong

			$sql = "UPDATE #__jlc_message SET
			is_read = 1,
			mdate = ".$dateObj->toUnix()."
		    WHERE message_id IN (".implode(',', $messageIds).");";
		}
		else
		{
			$sql = "UPDATE #__jlc_message SET
			is_read = 1,
			mdate = ".$dateObj->toUnix()."
		    WHERE message_id = ".(int)$messageId."
		    AND operator_id = ".(int)$operatorId;
		}

		$this->_db->setQuery($sql);

		return $this->_db->query();
	}

	function sendMessageReply($messageId, $messageToEmail, $messageSubject, $messageBody)
	{
		jimport('joomla.mail.mail');

		$filename = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'popup.css');

		if(file_exists($filename))
		{
			jimport('joomla.filesystem.file');

			$css = file_get_contents($filename);

			$css .= "\r\n\r\nbody {\r\nmargin: 0 !important; \r\n padding: 10px !important;\r\n}\r\n\r\n";
		}
		else
		{
			$css = '';
		}

		$email = & JFactory::getMailer();

		$from = array(
			0 => $this->_settings->getSetting('emails_from'),
			1 => $this->_settings->getSiteName()
		);

		$email->setSender($from);
		$email->setSubject($messageSubject);
		$email->addRecipient($messageToEmail);
		$email->IsHTML(true);

		// Base64 encode email to prevent problems
		$email->Encoding = 'base64';

		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">'."\r\n";
		$html .= '<html>'."\r\n";
		$html .= "<head>\r\n";

		$html = "<style type=\"text/css\">\r\n";
		$html .= $css;
		$html .= "</style>\r\n";
		$html .= "</head>\r\n";
		$html .= "<body>\r\n";

		$html .= '<span style="color: black;">'.$messageBody.'</span>';

		$html .= "</body>\r\n";
		$html .= "</html>\r\n";

		$email->setBody($html);

		return $email->Send();
	}
}