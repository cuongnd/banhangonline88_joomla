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

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JLiveChatControllerPopup extends CMSController
{
	/**
	 * Display the view
	 */
	function display()
	{
		$mainframe = & JFactory::getApplication();

		$document = & JFactory::getDocument();
		$uri = & JFactory::getURI();

		// Get/Create the view
		$view = & $this->getView(JRequest::getCmd('view'), $document->getType());

		$popup = & $this->getModel('Popup');

		if($popup->useSSL() && $uri->toString(array('scheme')) != 'https://')
		{
			//  Use SSL
			$newUri = 'https://'.$uri->toString(array('user', 'pass', 'host', 'port', 'path', 'query', 'fragment'));
			return $mainframe->redirect($newUri);
		}

		// Set the layout
		$view->setLayout('default');

		// Display the view
		parent::display(false);
	}

	function display_status_img()
	{
		$settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');
		$routing = & CMSModel::getInstance('Routing', 'JLiveChatModel');

		$specificOperators = JRequest::getVar('operators', null, 'method');
		$specificDepartment = JRequest::getVar('department', null, 'method');
		$specificRouteId = JRequest::getInt('routeid', null, 'method');

		$size = JRequest::getVar('size', 'large', 'method');

		$imgPath = JPATH_SITE.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'mod_jlivechat';

		if($size == 'small')
		{
			$onlineImg = 'livechat-online'.$settings->getSetting('small_online_img_ext');
			$offlineImg = 'livechat-offline'.$settings->getSetting('small_offline_img_ext');
		}
		else
		{
			$onlineImg = 'livechat-large-online'.$settings->getSetting('large_online_img_ext');
			$offlineImg = 'livechat-large-offline'.$settings->getSetting('large_offline_img_ext');
		}

		if($routing->isOnline($specificOperators, $specificDepartment, $specificRouteId))
		{
			$displayImg = $imgPath.DIRECTORY_SEPARATOR.$onlineImg;

			if($size == 'small')
			{
				$displayImgExt = strtolower($settings->getSetting('small_online_img_ext'));
			}
			else
			{
				$displayImgExt = strtolower($settings->getSetting('large_online_img_ext'));
			}
		}
		else
		{
			$displayImg = $imgPath.DIRECTORY_SEPARATOR.$offlineImg;

			if($size == 'small')
			{
				$displayImgExt = strtolower($settings->getSetting('small_offline_img_ext'));
			}
			else
			{
				$displayImgExt = strtolower($settings->getSetting('large_offline_img_ext'));
			}
		}

		// Set the content type header - in this case image/jpeg
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header("Pragma: no-cache");

		switch($displayImgExt)
		{
			case '.jpg':
				header('Content-type: image/jpeg');

				// Create a blank image and add some text
				$im = imagecreatefromjpeg($displayImg);

				// Output the image
				imagejpeg($im, null, 100);
				break;
			case '.jpeg':
				header('Content-type: image/jpeg');

				// Create a blank image and add some text
				$im = imagecreatefromjpeg($displayImg);

				// Output the image
				imagejpeg($im, null, 100);

				break;

			case '.gif':
				header('Content-type: image/gif');

				// Create a blank image and add some text
				$im = imagecreatefromgif($displayImg);

				// Output the image
				imagegif($im, null);

				break;
			case '.png':
				header('Content-type: image/png');

				// Create a blank image and add some text
				$im = imagecreatefrompng($displayImg);

				// Output the image
				imagepng($im, null);

				break;
		}

		// Free up memory
		if(isset($im)) imagedestroy($im);

		jexit();
	}

	function start_session()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');

		$popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');
		$routing = & CMSModel::getInstance('Routing', 'JLiveChatModel');
		$settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');

		$chatName = trim(strip_tags(JRequest::getVar('name', '', 'method')));
		$specificOperators = JRequest::getVar('operators', null, 'method');
		$specificDepartment = JRequest::getVar('department', null, 'method');
		$specificRouteId = JRequest::getInt('routeid', null, 'method');

		$errorMsg = '';
		$success = 1;

		if($settings->getSetting('routeby') == 'department' && empty($specificDepartment))
		{
			$success = 0;
			$errorMsg = JText::_('PLEASE_SELECT_DEPARTMENT');
		}
		elseif($settings->getSetting('routeby') == 'operator' && empty($specificOperators))
		{
			$success = 0;
			$errorMsg = JText::_('PLEASE_SELECT_OPERATOR');
		}

		if(strlen($chatName) < 2 || strlen($chatName) > 50)
		{
			$success = 0;
			$errorMsg = JText::_('PLEASE_ENTER_A_VALID_NAME');
		}

		if(!$popup->checkSecurity())
		{
			$success = 0;
			$errorMsg = JText::_('TRYS_EXCEEDED');
		}

		if($success == 1)
		{
			// Start Chat Session
			$chatSessionId = $popup->startNewChatSession();

			$popup->joinChatSession($chatSessionId, $chatName, 1);

			if(!empty($specificOperators))
			{
				$routing->requestChatFromOperators($chatSessionId, $specificOperators);
			}
			elseif(!empty($specificDepartment))
			{
				$routing->requestChatFromDepartment($chatSessionId, $specificDepartment);
			}
			elseif($specificRouteId > 0)
			{
				$routing->requestChatFromRouteId($chatSessionId, $specificRouteId);
			}
			else
			{
				$routing->requestChat($chatSessionId);
			}
		}

		$output = array();
		$output['error'] = $errorMsg;
		$output['success'] = $success;

		$callback = JRequest::getVar('callback', null, 'get');

		if(empty($callback))
		{
			header('Content-type: application/json');

			echo json_encode($output);
		}
		else
		{
			header('Content-type: text/javascript');

			echo $callback.'('.json_encode($output).');';
		}

		jexit();
	}

	function check_session()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');

		$session = & JFactory::getSession();
		$popup = & CMSModel::getInstance('Popup', 'JLiveChatModel');
		$routing = & CMSModel::getInstance('Routing', 'JLiveChatModel');
		$visitor = & CMSModel::getInstance('Visitor', 'JLiveChatModel');
		$settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');

		$specificOperators = trim(JRequest::getVar('operators', '', 'method'));
		$specificDepartment = trim(JRequest::getVar('department', '', 'method'));
		$specificRouteId = JRequest::getInt('routeid', null, 'method');

		$popupMode = JRequest::getVar('popup_mode', 'popup', 'method');

		$output = array('chat_active' => 0, 'chat_accepted' => 0);

		// Start Chat Session
		$chatSessionId = $session->get('jlc_chat_session_id');

		if($chatSessionId)
		{
			if(strlen($specificOperators) > 0)
			{
				$routing->requestChatFromOperators($chatSessionId, $specificOperators);
			}
			elseif(strlen($specificDepartment) > 0)
			{
				$routing->requestChatFromDepartment($chatSessionId, $specificDepartment);
			}
			elseif($specificRouteId > 0)
			{
				$routing->requestChatFromRouteId($chatSessionId, $specificRouteId);
			}
			else
			{
				$routing->requestChat($chatSessionId);
			}

			$output['chat_active'] = (int)$routing->isChatActive($chatSessionId);
			$output['chat_accepted'] = (int)$routing->isChatAccepted($chatSessionId);

			if($output['chat_active'] == 1 && $output['chat_accepted'] == 1 && $popupMode == 'iframe')
			{
				// Create proactive chat record to keep iframe popup open between pages
				$visitor->requestChat($visitorId, null, $chatSessionId);
			}
		}

		$callback = JRequest::getVar('callback', null, 'get');

		if(empty($callback))
		{
			header('Content-type: application/json');

			echo json_encode($output);
		}
		else
		{
			header('Content-type: text/javascript');

			echo $callback.'('.json_encode($output).');';
		}

		jexit();
	}

	function leave_message()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');

		$model = & CMSModel::getInstance('Message', 'JLiveChatModel');
		$settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');

		if($settings->getSetting('enable_messaging') != 'y') jexit('Function Not Allowed');

		jimport('joomla.mail.helper');

		$specificOperators = trim(JRequest::getVar('operators', '', 'method'));
		$specificDepartment = trim(JRequest::getVar('department', '', 'method'));
		$specificRouteId = JRequest::getInt('routeid', 0, 'method');

		$messageName = trim(JRequest::getVar('message_name', '', 'method'));
		$messagePhone = trim(JRequest::getVar('message_phone', '', 'method'));
		$messageEmail = trim(JRequest::getVar('message_email', '', 'method'));
		$messageTxt = trim(JRequest::getVar('message_txt', '', 'method'));

		$errors = array();

		if(strlen($messageName) < 2 || strlen($messageName) > 50)
		{
			$errors[] = JText::_('ENTER_VALID_NAME');
		}

		if(!JMailHelper::isEmailAddress($messageEmail))
		{
			$errors[] = JText::_('ENTER_VALID_EMAIL');
		}

		if($settings->getSetting('ask_phone_number') == 'required' || strlen($messagePhone) > 0)
		{
			$checkPhoneNumber = true;
		}
		else
		{
			$checkPhoneNumber = false;
		}

		if($checkPhoneNumber)
		{
			if(!preg_match('@(^[-a-zA-Z0-9\.#\s)(]{5,20}$)@', $messagePhone))
			{
				$errors[] = JText::_('ENTER_VALID_PHONE');
			}
		}

		if(strlen($messageTxt) < 2 || strlen($messageTxt) > 1500 || substr_count($messageTxt, ' ') < 2)
		{
			$errors[] = JText::_('ENTER_VALID_MESSAGE');
		}

		if(!$model->visitorSecurityCheck())
		{
			$errors[] = JText::_('TOO_MANY_MESSAGES_TRY_BACK');
		}

		if(count($errors) > 0)
		{
			$success = 0;
		}
		else
		{
			$success = 1;
			$errors[] = JText::_('MESSAGE_SENT_SUCCESSFULLY');

			if(strlen($specificOperators) > 0)
			{
				$model->leaveMessageForOperators($specificOperators, $messageName, $messageEmail, $messageTxt, $messagePhone);
			}
			elseif(strlen($specificDepartment) > 0)
			{
				$model->leaveMessageForDepartment($specificDepartment, $messageName, $messageEmail, $messageTxt, $messagePhone);
			}
			elseif($specificRouteId > 0)
			{
				$model->leaveMessageForRoute($specificRouteId, $messageName, $messageEmail, $messageTxt, $messagePhone);
			}
			else
			{
				$model->leaveMessage($messageName, $messageEmail, $messageTxt, $messagePhone);
			}
		}

		$output = array('success' => $success, 'errors' => $errors);

		$callback = JRequest::getVar('callback', null, 'get');

		if(empty($callback))
		{
			header('Content-type: application/json');

			echo json_encode($output);
		}
		else
		{
			header('Content-type: text/javascript');

			echo $callback.'('.json_encode($output).');';
		}

		jexit();
	}

	function send_message()
	{
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past

		$session = & JFactory::getSession();

		$chatSessionId = $session->get('jlc_chat_session_id');
		$memberId = $session->get('jlc_chat_member_id');

		$msg = JRequest::getVar('m', '', 'method', 'string', JREQUEST_ALLOWRAW);

		if(!$chatSessionId || !$memberId || !$msg) jexit(); // Access Denied

		$model = & CMSModel::getInstance('Popup', 'JLiveChatModel');

		$callback = JRequest::getVar('callback', null, 'get');

		if(empty($callback))
		{
			header('Content-type: application/json');

			echo json_encode(array('result' => $model->postVisitorMessage($chatSessionId, $memberId, $msg)));
		}
		else
		{
			header('Content-type: text/javascript');

			echo $callback.'('.json_encode(array('result' => $model->postVisitorMessage($chatSessionId, $memberId, $msg))).');';
		}

		jexit();
	}

	function refresh_session()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');

		$session = & JFactory::getSession();

		$chatSessionId = $session->get('jlc_chat_session_id');
		$memberId = $session->get('jlc_chat_member_id');

		if(!$chatSessionId || !$memberId) jexit(); //    Access Denied, invalid session id

		$clientIsTyping = JRequest::getInt('client_is_typing', 0, 'method');
		$popupMode = JRequest::getVar('popup_mode', 'popup', 'method');

		$model = & CMSModel::getInstance('Popup', 'JLiveChatModel');

		$model->updateSessionTypingStatus($chatSessionId, $memberId, $clientIsTyping);
		$model->updatePopupMode($chatSessionId, $popupMode);

		$chatRecord = $model->getMemberChatSession($memberId, $chatSessionId, true);

		$output = array(
			'chat_mdate' => $chatRecord['chat_mdate'],
			'is_active' => $chatRecord['is_active'],
			'chat_content' => $chatRecord['chat_session_content'].'<br /><br />',
			'is_typing' => $model->isOperatorTyping($chatSessionId)
		);

		$callback = JRequest::getVar('callback', null, 'get');

		if(empty($callback))
		{
			header('Content-type: application/json');

			echo json_encode($output);
		}
		else
		{
			header('Content-type: text/javascript');

			echo $callback.'('.json_encode($output).');';
		}

		jexit();
	}

	function end_session()
	{
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past

		$session = & JFactory::getSession();

		$chatSessionId = $session->get('jlc_chat_session_id');
		$memberId = $session->get('jlc_chat_member_id');

		$session->clear('jlc_chat_session_id');
		$session->clear('jlc_chat_member_id');

		if(!$chatSessionId || !$memberId) jexit(); //    Access Denied, invalid session id

		$model = & CMSModel::getInstance('Popup', 'JLiveChatModel');
		$model->endChatSession($chatSessionId, null, $memberId);

		jexit();
	}

	function check_proactive()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');
		header('Access-Control-Allow-Origin: *'); // Allow cross domain requests, required for xhr2

		$settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');

		if((int)$settings->getSetting('proactive_chat') > 0)
		{
			$visitor = & CMSModel::getInstance('Visitor', 'JLiveChatModel');

			$proactiveChat = $visitor->anyProactiveChatRequests();

			if($proactiveChat)
			{
				$session = & JFactory::getSession();

				$session->set('jlc_chat_session_id', (int)$proactiveChat['chat_session_id']);
				$session->set('jlc_chat_member_id', (int)$proactiveChat['member_id']);
			}
		}
		else
		{
			$proactiveChat = false;
		}

		$output = array(
			'proactive' => $proactiveChat,
			'proactive_setting' => (int)$settings->getSetting('proactive_chat'),
		);

		$callback = JRequest::getVar('callback', null, 'get');

		if(empty($callback))
		{
			header('Content-type: application/json');

			echo json_encode($output);
		}
		else
		{
			header('Content-type: text/javascript');

			echo $callback.'('.json_encode($output).');';
		}

		jexit();
	}

	function check_autopopup()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');
		header('Access-Control-Allow-Origin: *'); // Allow cross domain requests, required for xhr2

		$settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');
		$routing = & CMSModel::getInstance('Routing', 'JLiveChatModel');
		$visitor = & CMSModel::getInstance('Visitor', 'JLiveChatModel');

		$showPopup = (int)$settings->getSetting('autopopup');

		if($settings->getSetting('autopopup_max_display') > 0 && $showPopup == 1)
		{
			// Maximum times to display per session is set
			$session = & JFactory::getSession();

			$currentDisplayTimesNum = (int)$session->get('autopopup_max_display', 0);

			if($currentDisplayTimesNum >= $settings->getSetting('autopopup_max_display'))
			{
				$showPopup = 0;
			}
			else
			{
				$session->set('autopopup_max_display', ($currentDisplayTimesNum + 1));
			}
		}

		// only show auto-popup if an operator is online
		if((int)$settings->getSetting('autopopup_only_online') == 1 && !$routing->isOnline())
		{
			$showPopup = 0;
		}

		$autoPopupHTML = $settings->getSetting('display_html');

		// Make absolute URL for popup window
		$autoPopupHTML = preg_replace('@([\'"]{1})/(index\.php\?option=com_jlivechat)@', '$1'.JURI::root().'$2', $autoPopupHTML);

		$output = array(
			'show_popup' => (int)$showPopup,
			'display_html_in_seconds' => (int)$settings->getSetting('display_html_in_seconds'),
			'display_html' => $autoPopupHTML,
			'display_html_on_uris' => $settings->getSetting('display_html_on_uris')
		);

		$callback = JRequest::getVar('callback', null, 'get');

		if(empty($callback))
		{
			header('Content-type: application/json');

			echo json_encode($output);
		}
		else
		{
			header('Content-type: text/javascript');

			echo $callback.'('.json_encode($output).');';
		}

		jexit();
	}

	function track_remote_visitor()
	{
		$userId = JRequest::getInt('user_id', 0, 'method');
		$fullName = JRequest::getVar('full_name', null, 'method');
		$userName = JRequest::getVar('username', null, 'method');
		$email = JRequest::getVar('email', null, 'method');
		$referrer = JRequest::getVar('referrer', null, 'method');
		$lastUri = JRequest::getVar('last_uri', JRequest::getVar('HTTP_REFERER', null, 'server'), 'method');

		$visitor = & CMSModel::getInstance('Visitor', 'JLiveChatModel');
		$visitor->setVisitorId(md5(JRequest::getVar('HTTP_USER_AGENT', '', 'server').JRequest::getVar('REMOTE_ADDR', '0.0.0.0', 'server').$userId));
		$visitor->track(false, $userId, $fullName, $userName, $email, $referrer, $lastUri);

		// Create a new image instance
		$im = imagecreatetruecolor(1, 1);

		// Make the background transparent
		imagecolortransparent($im, imagecolorallocate($im, 0, 0, 0));

		// Output the image to browser
		header('Content-type: image/gif');
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past

		imagegif($im);
		imagedestroy($im);

		jexit();
	}

	function close_iframe_popup()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');

		$session = & JFactory::getSession();
		$session->set('livechat_iframe_popup', 'close_window');

		jexit();
	}

	function restore_iframe_popup()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');

		$session = & JFactory::getSession();
		$session->set('livechat_iframe_popup', 'restore_window');

		jexit();
	}

	function minimize_iframe_popup()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');

		$session = & JFactory::getSession();
		$session->set('livechat_iframe_popup', 'minimize_window');

		jexit();
	}

	function monitor_iframe_popup()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');

		$callback = JRequest::getVar('callback', null, 'get');

		$session = & JFactory::getSession();

		$output = array('result' => $session->get('livechat_iframe_popup'));

		if(!empty($output['result'])) $session->clear('livechat_iframe_popup');

		if(empty($callback))
		{
			header('Content-type: application/json');

			echo json_encode($output);
		}
		else
		{
			header('Content-type: text/javascript');

			echo $callback.'('.json_encode($output).');';
		}

		jexit();
	}
}