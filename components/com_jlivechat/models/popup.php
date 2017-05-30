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

class JLiveChatModelPopup extends CMSModel
{
	var $_settings = null;
	var $_offlineSeconds = 20;
	var $_isRestful = false;

	function __construct()
	{
		$this->JLiveChatModelPopup();
	}

	function JLiveChatModelPopup()
	{
		parent::__construct();

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jlcdate.php';

		$this->_settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');
	}

	function setRestfulAPI()
	{
		$this->_isRestful = true;
	}

	function getOfflineSeconds()
	{
		return $this->_offlineSeconds;
	}

	function getWindowTitle($language)
	{
		$titles = $this->_settings->getSetting('window_titles');

		if(isset($titles->$language)) return $titles->$language;

		return JText::_('WINDOW_TITLE_DEFAULT');
	}

	function getWindowIntro($language)
	{
		$intros = $this->_settings->getSetting('window_intros');

		if(isset($intros->$language)) return $intros->$language;

		return '<p>'.JText::_('WINDOW_INTRO_MSG').'</p>';
	}

	function getWindowOffline($language)
	{
		$offlineMessages = $this->_settings->getSetting('offline_messages');

		if(isset($offlineMessages->$language)) return $offlineMessages->$language;

		return '<p>'.JText::_('WINDOW_OFFLINE_MSG').'</p>';
	}

	function useSSL()
	{
		if($this->_settings->getSetting('popup_ssl') == 0)
		{
			return false;
		}
		elseif($this->_settings->getSetting('popup_ssl') == 1)
		{
			return true;
		}
	}

	function startNewChatSession($isRestfulAPI = false)
	{
		$mainframe = & JFactory::getApplication();

		$date = new JLiveChatModelJLCDate();

		$newAltChatSessionId = $this->_getUniqueChatId();

		$sessionContent = '<div class="jlc-system-msg">'.JText::sprintf('YOUR_CHAT_SESSION_ID_IS', $newAltChatSessionId).' <span class="timestamp">('.$date->toFormat('%m/%d/%Y %H:%M:%S').')</span></div><br />';

		$data = new stdClass();

		$data->chat_alt_id = $newAltChatSessionId;
		$data->chat_session_content = $sessionContent;
		$data->cdate = $date->toUnix();
		$data->mdate = $data->cdate;
		$data->is_active = 1;
		$data->is_multimember = 0;

		$this->_db->insertObject('#__jlc_chat', $data, 'chat_session_id');

		$chatSessionId = $this->_db->insertid();

		if($isRestfulAPI)
		{
			return array('chat_session_id' => $chatSessionId, 'chat_alt_id' => $newAltChatSessionId);
		}
		else
		{
			return $chatSessionId;
		}
	}

	function joinChatSession($chatSessionId, $displayName, $isAccepted = null, $operatorId = 0, $isSessionBased = true, $discreet = false, $location = null, $memberIp = null, $memberWebBrowser = null)
	{
		$mainframe = & JFactory::getApplication();

		$user = & JFactory::getUser();

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'visitor.php';

		$visitor = & CMSModel::getInstance('Visitor', 'JLiveChatModel');

		$date = new JLiveChatModelJLCDate();

		if(!$memberIp)
		{
			$memberIp = JRequest::getVar('REMOTE_ADDR', '0.0.0.0', 'server');
		}

		if(!$memberWebBrowser)
		{
			$memberWebBrowser = JRequest::getVar('HTTP_USER_AGENT', 'Unknown', 'server');
		}

		$data = new stdClass();

		$data->chat_session_id = $chatSessionId;
		$data->operator_id = $operatorId;
		$data->user_id = $user->get('id');
		$data->member_display_name = $displayName;
		$data->member_ip_address = $memberIp;
		$data->member_web_browser = $memberWebBrowser;

		if(!$location) $location = $visitor->locateIp(JRequest::getVar('REMOTE_ADDR', '0.0.0.0', 'server'));

		$data->member_city = $location['city'];
		$data->member_country = $location['country'];
		$data->member_country_code = $location['country_code'];

		$data->is_accepted = $isAccepted;
		$data->is_gone = 0;
		$data->is_typing = 0;
		$data->cdate = $date->toUnix();
		$data->mdate = $data->cdate;

		if($data->user_id > 0)
		{
			// Include registration information in member params
			$userObj = & JFactory::getUser($data->user_id);

			$userDetails = array(
				'registered_fullname' => $userObj->get('name'),
				'registered_username' => $userObj->get('username'),
				'registered_email' => $userObj->get('email')
			);

			$data->member_params = json_encode($userDetails);
		}

		$this->_db->insertObject('#__jlc_chat_member', $data, 'member_id');

		$memberId = $this->_db->insertid();

		if($isAccepted && !$discreet)
		{
			if($operatorId)
			{
				$chatRecord = $this->getOperatorChatSession($operatorId, $chatSessionId);
			}
			else
			{
				$chatRecord = $this->getOperatorChatSession(0, $chatSessionId);
			}

			if($operatorId > 0)
			{
				$chatRecord['chat_session_content'] .= '<span class="jlc-system-notes">'.JText::sprintf('HAS_JOINED_CHATROOM', $displayName).' <span class="timestamp">('.$date->toFormat('%H:%M:%S').')</span></span><div class="jlc-clr">&nbsp;</div><br /><br />';
			}
			else
			{
				$chatRecord['chat_session_content'] .= '<span class="jlc-system-notes">'.JText::sprintf('VISITOR_HAS_JOINED_CHATROOM', $displayName, JRequest::getVar('REMOTE_ADDR', '0.0.0.0', 'server')).' <span class="timestamp">('.$date->toFormat('%H:%M:%S').')</span></span><br />';
			}

			$this->updateChatSessionContent($chatSessionId, $chatRecord['chat_session_content']);
		}
		else
		{
			// Just update the chat session checksum
			$this->updateChatSessionChecksum($chatSessionId);
		}

		if($isSessionBased)
		{
			$session = & JFactory::getSession();
			$session->set('jlc_chat_session_id', $chatSessionId);
			$session->set('jlc_chat_member_id', $memberId);
		}

		return $memberId;
	}

	function updateChatSessionChecksum($chatSessionId)
	{
		$dateObj = new JLiveChatModelJLCDate();

		$data = new stdClass();
		$data->chat_session_id = (int)$chatSessionId;
		$data->mdate = $dateObj->toUnix();

		return $this->_db->updateObject('#__jlc_chat', $data, 'chat_session_id');
	}

	function checkSecurity()
	{
		$date = new JLiveChatModelJLCDate();

		$interval = $date->toUnix() - 300; // 5 minutes

		$sql = "SELECT count(*) FROM #__jlc_chat_member
		WHERE member_ip_address = ".$this->_db->Quote(JRequest::getVar('REMOTE_ADDR', '0.0.0.0', 'server'))."
		AND cdate >= ".$interval.";";

		$this->_db->setQuery($sql);

		$result = $this->_db->loadResult();

		if($result > 10) return false;

		return true;
	}

	function _getUniqueChatId()
	{
		$randomLength = rand(3, 7);

		$chatId = md5(uniqid(rand(), true));
		$chatId = strtoupper(substr($chatId, 0, $randomLength));

		$sql = "SELECT count(*) FROM #__jlc_chat
		WHERE chat_alt_id = ".$this->_db->Quote($chatId);
		$this->_db->setQuery($sql);

		if($this->_db->loadResult() > 0)
		{
			// Already exists, try another
			return $this->_getUniqueChatId();
		}

		return $chatId;
	}

	function getChatDataForOperator($operatorId)
	{
		$dateObj = new JLiveChatModelJLCDate();

		$sql = "SELECT
		    c.chat_session_id,
		    c.chat_alt_id,
		    c.chat_session_content,
		    c.chat_session_params,
		    c.cdate,
		    c.mdate,
		    c.is_active,
		    c.is_multimember, 
		    cm.member_id,
		    cm.operator_id,
		    cm.user_id,
		    cm.member_display_name,
		    cm.member_ip_address,
		    cm.member_web_browser,
		    cm.member_city,
		    cm.member_country,
		    cm.member_country_code,
		    cm.is_accepted,
		    cm.is_gone,
		    cm.is_typing,
		    cm.cdate AS 'member_cdate',
		    cm.mdate AS 'member_mdate',
		    cm.member_params,
		    cm.expire_time
		FROM #__jlc_chat_member cm

		INNER JOIN #__jlc_chat c 
		USING(chat_session_id) 

		WHERE c.chat_session_id IN (
		    SELECT
			DISTINCT(chat_session_id) AS 'chat_session_id'
		    FROM #__jlc_chat_member
		    WHERE operator_id = ".(int)$operatorId." 
		    AND (expire_time > ".$dateObj->toUnix()." OR expire_time IS NULL) 
		) AND c.is_active = 1;";
		$this->_db->setQuery($sql);

		return $this->_db->loadObjectList();
	}

	function declineChatRequest($operatorId, $chatSessionId)
	{
		$dateObj = new JLiveChatModelJLCDate();

		$sql = "UPDATE #__jlc_chat_member SET
		    is_accepted = 0,
		    mdate = ".$dateObj->toUnix().",
		    expire_time = ".$dateObj->toUnix()." 
		WHERE chat_session_id = ".(int)$chatSessionId."
		AND operator_id = ".(int)$operatorId;
		$this->_db->setQuery($sql);

		return $this->_db->query();
	}

	function isAccepted($chatSessionId)
	{
		$sql = "SELECT count(*) FROM #__jlc_chat_member
		WHERE chat_session_id = ".(int)$chatSessionId."
		AND operator_id = 0;";
		$this->_db->setQuery($sql);

		if($this->_db->loadResult() < 1) return false;

		$sql = "SELECT count(*) FROM #__jlc_chat_member
		WHERE chat_session_id = ".(int)$chatSessionId." 
		AND operator_id > 0
		AND is_accepted = 1;";
		$this->_db->setQuery($sql);

		if($this->_db->loadResult() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function acceptChatRequest($operatorId, $chatSessionId)
	{
		$mainframe = & JFactory::getApplication();

		$dateObj = new JLiveChatModelJLCDate();

		$mdate = $dateObj->toUnix();

		$operatorChatRecord = $this->getOperatorChatSession($operatorId, $chatSessionId);

		if($this->isAccepted($chatSessionId) && $operatorChatRecord['is_multimember'] == 0) return false; // Already accepted

		$operatorChatRecord['chat_session_content'] .= '<span class="jlc-system-notes">'.JText::sprintf('HAS_JOINED_CHATROOM', $operatorChatRecord['member_display_name']).' <span class="timestamp">('.$dateObj->toFormat('%H:%M:%S').')</span></span><div class="jlc-clr">&nbsp;</div><br /><br />';

		$this->updateChatSessionContent($chatSessionId, $operatorChatRecord['chat_session_content']);

		$sql = "UPDATE #__jlc_chat_member SET
		    is_accepted = 1,
		    mdate = ".$mdate.",
		    expire_time = NULL 
		WHERE chat_session_id = ".(int)$chatSessionId."
		AND operator_id = ".(int)$operatorId;
		$this->_db->setQuery($sql);

		return $this->_db->query();
	}

	function getChatSession($chatSessionId)
	{
		$sql = "SELECT
		    c.chat_session_id,
		    c.chat_alt_id,
		    c.chat_session_content,
		    c.chat_session_params,
		    c.cdate AS 'chat_cdate',
		    c.mdate AS 'chat_mdate',
		    c.is_active
		FROM #__jlc_chat c

		WHERE c.chat_session_id = ".(int)$chatSessionId."
		LIMIT 1;";
		$this->_db->setQuery($sql);

		$result = $this->_db->loadAssoc();

		if(!empty($result['chat_session_params']))
		{
			$result['chat_session_params'] = json_decode($result['chat_session_params']);
		}

		return $result;
	}

	function getChatSessionParams($chatSessionId)
	{
		$sql = "SELECT
		    c.chat_session_params 
		FROM #__jlc_chat c

		WHERE c.chat_session_id = ".(int)$chatSessionId."
		LIMIT 1;";
		$this->_db->setQuery($sql);

		$result = $this->_db->loadResult();

		if(!empty($result))
		{
			$result = json_decode($result);
		}
		else
		{
			$result = new stdClass();
		}

		return $result;
	}

	function getOperatorChatSession($operatorId, $chatSessionId)
	{
		$sql = "SELECT
		    c.chat_session_id,
		    c.chat_alt_id,
		    c.chat_session_content,
		    c.chat_session_params,
		    c.cdate AS 'chat_cdate',
		    c.mdate AS 'chat_mdate',
		    c.is_active,
		    c.is_multimember,
		    cm.member_id,
		    cm.operator_id,
		    cm.user_id,
		    cm.member_display_name,
		    cm.member_ip_address,
		    cm.member_web_browser,
		    cm.member_city,
		    cm.member_country,
		    cm.member_country_code,
		    cm.is_accepted,
		    cm.is_gone,
		    cm.is_typing,
		    cm.cdate AS 'member_cdate',
		    cm.mdate AS 'member_mdate',
		    cm.member_params,
		    cm.expire_time
		FROM #__jlc_chat c

		INNER JOIN #__jlc_chat_member cm
		USING(chat_session_id)

		WHERE cm.operator_id = ".(int)$operatorId."
		AND c.chat_session_id = ".(int)$chatSessionId."
		LIMIT 1;";
		$this->_db->setQuery($sql);

		$result = $this->_db->loadAssoc();

		if(!empty($result['chat_session_params']))
		{
			$result['chat_session_params'] = json_decode($result['chat_session_params']);
		}

		if(!empty($result['member_params']))
		{
			$result['member_params'] = json_decode($result['member_params']);
		}

		return $result;
	}

	function getMemberChatSession($memberId, $chatSessionId, $replaceSmilies = false)
	{
		$sql = "SELECT
		    c.chat_session_id,
		    c.chat_alt_id,
		    c.chat_session_content,
		    c.chat_session_params,
		    c.cdate AS 'chat_cdate',
		    c.mdate AS 'chat_mdate',
		    c.is_active,
		    cm.member_id,
		    cm.operator_id,
		    cm.user_id,
		    cm.member_display_name,
		    cm.member_ip_address,
		    cm.member_web_browser,
		    cm.member_city,
		    cm.member_country,
		    cm.member_country_code,
		    cm.is_accepted,
		    cm.is_gone,
		    cm.is_typing,
		    cm.cdate AS 'member_cdate',
		    cm.mdate AS 'member_mdate',
		    cm.member_params,
		    cm.expire_time
		FROM #__jlc_chat c

		INNER JOIN #__jlc_chat_member cm
		USING(chat_session_id)

		WHERE cm.member_id = ".(int)$memberId."
		AND c.chat_session_id = ".(int)$chatSessionId."
		LIMIT 1;";
		$this->_db->setQuery($sql);

		$result = $this->_db->loadAssoc();

		if(!empty($result['chat_session_params']))
		{
			$result['chat_session_params'] = json_decode($result['chat_session_params']);
		}

		if(!empty($result['member_params']))
		{
			$result['member_params'] = json_decode($result['member_params']);
		}

		if($replaceSmilies)
		{
			$result['chat_session_content'] = $this->_replaceSmilies($result['chat_session_content']);
		}

		return $result;
	}

	function _replaceSmilies($content)
	{
		$smiliesFolder = JURI::root(true).'/components/com_jlivechat/assets/images/smilies_set1/';

		$coolSmiley = $smiliesFolder.'001_cool.gif';
		$huhSmiley = $smiliesFolder.'001_huh.gif';
		$rollEyesSmiley = $smiliesFolder.'001_rolleyes.gif';
		$smileSmiley = $smiliesFolder.'001_smile.gif';
		$toungeSmiley = $smiliesFolder.'001_tongue.gif';
		$tt1Smiley = $smiliesFolder.'001_tt1.gif';
		$tt2Smiley = $smiliesFolder.'001_tt2.gif';
		$unsureSmiley = $smiliesFolder.'001_unsure.gif';
		$wubSmiley = $smiliesFolder.'001_wub.gif';
		$angrySmiley = $smiliesFolder.'angry.gif';
		$bigGrinSmiley = $smiliesFolder.'biggrin.gif';
		$blinkSmiley = $smiliesFolder.'blink.gif';
		$blushSmiley = $smiliesFolder.'blush.gif';
		$blushingSmiley = $smiliesFolder.'blushing.gif';
		$boredSmiley = $smiliesFolder.'bored.gif';
		$closedEyesSmiley = $smiliesFolder.'closedeyes.gif';
		$confused1Smiley = $smiliesFolder.'confused1.gif';
		$cool2Smiley = $smiliesFolder.'cool.gif';
		$cryingSmiley = $smiliesFolder.'crying.gif';
		$cursingSmiley = $smiliesFolder.'cursing.gif';
		$droolSmiley = $smiliesFolder.'drool.gif';
		$glareSmiley = $smiliesFolder.'glare.gif';
		$huh2Smiley = $smiliesFolder.'huh.gif';
		$laughSmiley = $smiliesFolder.'laugh.gif';
		$lolSmiley = $smiliesFolder.'lol.gif';
		$madSmiley = $smiliesFolder.'mad.gif';
		$mellowSmiley = $smiliesFolder.'mellow.gif';
		$ohmySmiley = $smiliesFolder.'ohmy.gif';
		$sadSmiley = $smiliesFolder.'sad.gif';
		$scaredSmiley = $smiliesFolder.'scared.gif';
		$sleepSmiley = $smiliesFolder.'sleep.gif';
		$tongue2Smiley = $smiliesFolder.'tongue_smilie.gif';
		$w00tSmiley = $smiliesFolder.'w00t.gif';
		$winkSmiley = $smiliesFolder.'wink.gif';
		$twoThumbsUpSmiley = $smiliesFolder.'thumbup.gif';
		$thumbDownSmiley = $smiliesFolder.'thumbdown.gif';

		$imgPrefix = '<img src="';
		$imgSuffix = '" alt="" border="0" align="absmiddle">';

		$content = str_replace(':-)', $imgPrefix.$smileSmiley.$imgSuffix, $content);
		$content = str_replace(':)', $imgPrefix.$smileSmiley.$imgSuffix, $content);

		$content = str_replace('B-)', $imgPrefix.$coolSmiley.$imgSuffix, $content);

		$content = str_replace(':-/', $imgPrefix.$huhSmiley.$imgSuffix, $content);

		$content = str_replace('8-|', $imgPrefix.$rollEyesSmiley.$imgSuffix, $content);

		$content = str_replace(':P', $imgPrefix.$toungeSmiley.$imgSuffix, $content);

		$content = str_replace(':UU', $imgPrefix.$tt1Smiley.$imgSuffix, $content);

		$content = str_replace(':^', $imgPrefix.$tt2Smiley.$imgSuffix, $content);

		$content = str_replace(':\\', $imgPrefix.$unsureSmiley.$imgSuffix, $content);

		$content = str_replace(':x', $imgPrefix.$wubSmiley.$imgSuffix, $content);

		$content = str_replace('X(', $imgPrefix.$angrySmiley.$imgSuffix, $content);
		$content = str_replace('x(', $imgPrefix.$angrySmiley.$imgSuffix, $content);

		$content = str_replace(':D', $imgPrefix.$bigGrinSmiley.$imgSuffix, $content);

		$content = str_replace(';~)', $imgPrefix.$blinkSmiley.$imgSuffix, $content);

		$content = str_replace(':">', $imgPrefix.$blushSmiley.$imgSuffix, $content);

		$content = str_replace(':"">', $imgPrefix.$blushingSmiley.$imgSuffix, $content);

		$content = str_replace(':-!', $imgPrefix.$boredSmiley.$imgSuffix, $content);

		$content = str_replace('|-(', $imgPrefix.$closedEyesSmiley.$imgSuffix, $content);

		$content = str_replace(':~/', $imgPrefix.$confused1Smiley.$imgSuffix, $content);
		$content = str_replace(':-?', $imgPrefix.$confused1Smiley.$imgSuffix, $content);

		$content = str_replace('BB-)', $imgPrefix.$cool2Smiley.$imgSuffix, $content);

		$content = str_replace(":'-(", $imgPrefix.$cryingSmiley.$imgSuffix, $content);

		$content = str_replace(":-@!", $imgPrefix.$cursingSmiley.$imgSuffix, $content);

		$content = str_replace(":-B", $imgPrefix.$droolSmiley.$imgSuffix, $content);

		$content = str_replace("8*)", $imgPrefix.$glareSmiley.$imgSuffix, $content);

		$content = str_replace(":-//", $imgPrefix.$huh2Smiley.$imgSuffix, $content);

		$content = str_replace(":-D", $imgPrefix.$laughSmiley.$imgSuffix, $content);

		$content = str_replace("LOL", $imgPrefix.$lolSmiley.$imgSuffix, $content);
		$content = str_replace("%OD", $imgPrefix.$lolSmiley.$imgSuffix, $content);

		$content = str_replace("X-(", $imgPrefix.$madSmiley.$imgSuffix, $content);

		$content = str_replace(":-<", $imgPrefix.$mellowSmiley.$imgSuffix, $content);

		$content = str_replace("=D>", $imgPrefix.$ohmySmiley.$imgSuffix, $content);

		$content = str_replace(":(", $imgPrefix.$sadSmiley.$imgSuffix, $content);
		$content = str_replace(":-(", $imgPrefix.$sadSmiley.$imgSuffix, $content);

		$content = str_replace("#:-S", $imgPrefix.$scaredSmiley.$imgSuffix, $content);

		$content = str_replace("I-)", $imgPrefix.$sleepSmiley.$imgSuffix, $content);

		$content = str_replace(":p", $imgPrefix.$tongue2Smiley.$imgSuffix, $content);

		$content = str_replace(":00t", $imgPrefix.$w00tSmiley.$imgSuffix, $content);

		$content = str_replace(";)", $imgPrefix.$winkSmiley.$imgSuffix, $content);
		$content = str_replace(";-)", $imgPrefix.$winkSmiley.$imgSuffix, $content);

		$content = str_replace(":-bd", $imgPrefix.$twoThumbsUpSmiley.$imgSuffix, $content);

		$content = str_replace(":-q", $imgPrefix.$thumbDownSmiley.$imgSuffix, $content);

		return $content;
	}

	function updateChatSessionContent($chatSessionId, $newContent)
	{
		$dateObj = new JLiveChatModelJLCDate();

		$sql = "UPDATE #__jlc_chat SET
		    chat_session_content = ".$this->_db->Quote($newContent).",
		    mdate = ".$dateObj->toUnix()." 
		WHERE chat_session_id = ".(int)$chatSessionId.";";
		$this->_db->setQuery($sql);

		return $this->_db->query();
	}

	function postOperatorMessage($chatSessionId, $operatorId, $msg)
	{
		$mainframe = & JFactory::getApplication();

		$msg = trim($this->_formatMsg($msg, true));

		if(strlen($msg) < 1) return false;

		$dateObj = new JLiveChatModelJLCDate();

		$operatorChatRecord = $this->getOperatorChatSession($operatorId, $chatSessionId);

		$operatorChatRecord['chat_session_content'] .= '<div class="operator-name">'.$operatorChatRecord['member_display_name'].' <span class="timestamp">('.$dateObj->toFormat('%H:%M:%S').')</span>:</div><div class="operator-msg">'.$msg.' </div><div class="jlc-clr">&nbsp;</div><br />';

		return $this->updateChatSessionContent($chatSessionId, $operatorChatRecord['chat_session_content']);
	}

	function _formatMsg($msg, $isOperator = false)
	{
		if(!$isOperator)
		{
			$msg = preg_replace('@javascript[\s\t\r\n]*:@i', '', $msg);
			$msg = preg_replace('@src[\s\t\r\n]*=@i', '', $msg);
			$msg = preg_replace('@<script.+?</script>@is', '', $msg);
			$msg = preg_replace('@<img[\s]+[^>]+>@i', '', $msg);
		}

		if(preg_match('@(<a[\s]+)@i', $msg))
		{
			if(!preg_match('@(<a[^>]+target[\s]*=)@i', $msg))
			{
				$msg = preg_replace('@(<a[^>]+)>@i', '$1 target="_blank">', $msg);
			}
		}

		$msg = preg_replace('@(?<!["\'])(https?://[^\s\t\r\n><]+)@i', '<a href="$1" target="_blank">$1</a>', $msg);

		return $msg;
	}

	function endChatSession($chatSessionId, $operatorId = null, $memberId = null)
	{
		$mainframe = & JFactory::getApplication();

		$dateObj = new JLiveChatModelJLCDate();

		if($operatorId)
		{
			$chatRecord = $this->getOperatorChatSession($operatorId, $chatSessionId);
		}
		elseif($memberId)
		{
			$chatRecord = $this->getMemberChatSession($memberId, $chatSessionId);
		}

		$chatRecord['chat_session_content'] .= '<br /><br /><div class="jlc-system-notes">'.JText::sprintf('HAS_LEFT_CHATROOM', $chatRecord['member_display_name']).' <span class="timestamp">('.$dateObj->toFormat('%H:%M:%S').')</span></div><br />';

		$sql = "UPDATE #__jlc_chat SET 
		    chat_session_content = ".$this->_db->Quote($chatRecord['chat_session_content']);

		if($this->howManyPeopleInChatSession($chatSessionId) == 2)
		{
			// Last person in room, close session
			$sql .= ", is_active = 0 ";
		}

		$sql .= ", mdate = ".$dateObj->toUnix();
		$sql .= " WHERE chat_session_id = ".(int)$chatSessionId.";";

		$this->_db->setQuery($sql);
		$this->_db->query();

		$sql = "UPDATE #__jlc_chat_member SET 
		    is_gone = 1,
		    mdate = ".$dateObj->toUnix().",
		    expire_time = ".$dateObj->toUnix()." 
		WHERE chat_session_id = ".(int)$chatSessionId;

		if($operatorId)
		{
			$sql .= " AND operator_id = ".(int)$operatorId.";";
		}
		elseif($memberId)
		{
			$sql .= " AND member_id = ".(int)$memberId.";";
		}

		$this->_db->setQuery($sql);

		return $this->_db->query();
	}

	function howManyPeopleInChatSession($chatSessionId)
	{
		$sql = "SELECT count(*) FROM #__jlc_chat_member 
		WHERE chat_session_id = ".(int)$chatSessionId."
		AND is_gone = 0 AND is_accepted = 1;";
		$this->_db->setQuery($sql);

		return $this->_db->loadResult();
	}

	function postVisitorMessage($chatSessionId, $memberId, $msg)
	{
		$mainframe = & JFactory::getApplication();

		$msg = trim($this->_formatMsg($msg));

		if(strlen($msg) < 1) return false;

		$dateObj = new JLiveChatModelJLCDate();

		$chatRecord = $this->getMemberChatSession($memberId, $chatSessionId);

		$chatRecord['chat_session_content'] .= '<div class="visitor-name">'.$chatRecord['member_display_name'].' <span class="timestamp">('.$dateObj->toFormat('%H:%M:%S').')</span>:</div><div class="visitor-msg">'.$msg.' </div><div class="jlc-clr">&nbsp;</div><br />';

		return $this->updateChatSessionContent($chatSessionId, $chatRecord['chat_session_content']);
	}

	function isOperatorTyping($chatSessionId)
	{
		$sql = "SELECT
		    cm.member_id,
		    o.operator_id, 
		    o.operator_name 
		FROM #__jlc_chat_member cm

		INNER JOIN #__jlc_operator o
		USING(operator_id)
		
		WHERE cm.operator_id > 0
		AND cm.chat_session_id = ".(int)$chatSessionId."
		AND cm.is_typing = 1;";
		$this->_db->setQuery($sql);

		return $this->_db->loadAssocList();
	}

	function updateOperatorTypingStatus($operatorId, $chatSessionsCurrentlyTyping)
	{
		$dateObj = new JLiveChatModelJLCDate();

		$sql = "SELECT
		    chat_session_id,
		    is_typing
		FROM #__jlc_chat_member
		WHERE operator_id = ".(int)$operatorId."
		AND (expire_time >= ".$dateObj->toUnix()." OR expire_time IS NULL)
		AND is_gone = 0;";
		$this->_db->setQuery($sql);

		$currentStatus = $this->_db->loadAssocList();

		$sessionsTyping = array();
		$sessionsNotTyping = array();
		$updateTyping = array();
		$updateNotTyping = array();

		if(!empty($currentStatus))
		{
			foreach($currentStatus as $row)
			{
				$chatSessionId = (int)$row['chat_session_id'];

				$updateNotTyping[$chatSessionId] = true;

				if($row['is_typing'] == 1)
				{
					$sessionsTyping[$chatSessionId] = true;
				}
				else
				{
					$sessionsNotTyping[$chatSessionId] = true;
				}
			}
		}

		if(!empty($chatSessionsCurrentlyTyping))
		{
			foreach($chatSessionsCurrentlyTyping as $chatSessionIdTyping)
			{
				$chatSessionIdTyping = (int)$chatSessionIdTyping;

				if($chatSessionIdTyping > 0)
				{
					unset($updateNotTyping[$chatSessionIdTyping]);

					if(!isset($sessionsTyping[$chatSessionIdTyping]))
					{
						$updateTyping[$chatSessionIdTyping] = true;
					}
				}
			}
		}

		if(!empty($updateNotTyping))
		{
			foreach($updateNotTyping as $chatSessionId => $val)
			{
				if(isset($sessionsNotTyping[$chatSessionId]))
				{
					unset($updateNotTyping[$chatSessionId]);
				}
			}
		}

		if(!empty($updateTyping))
		{
			$commaList = '';
			foreach($updateTyping as $chatSessionId => $val)
			{
				$commaList .= $chatSessionId.',';
			}

			$commaList = rtrim($commaList, ",");

			$sql = "UPDATE #__jlc_chat_member SET
			is_typing = 1,
			mdate = ".$dateObj->toUnix()."
		    WHERE operator_id = ".(int)$operatorId."
		    AND chat_session_id IN (".$commaList.");";
			$this->_db->setQuery($sql);
			$this->_db->query();
		}

		if(!empty($updateNotTyping))
		{
			$commaList = '';
			foreach($updateNotTyping as $chatSessionId => $val)
			{
				$commaList .= $chatSessionId.',';
			}

			$commaList = rtrim($commaList, ",");

			$sql = "UPDATE #__jlc_chat_member SET
			is_typing = 0,
			mdate = ".$dateObj->toUnix()."
		    WHERE operator_id = ".(int)$operatorId."
		    AND chat_session_id IN (".$commaList.");";
			$this->_db->setQuery($sql);
			$this->_db->query();
		}
	}

	function updateSessionTypingStatus($chatSessionId, $memberId, $clientIsTyping)
	{
		$sql = "SELECT is_typing FROM #__jlc_chat_member
		WHERE member_id = ".(int)$memberId."
		AND chat_session_id = ".(int)$chatSessionId;
		$this->_db->setQuery($sql);

		$currentStatus = $this->_db->loadResult();

		if((int)$currentStatus == (int)$clientIsTyping) return false;

		$dateObj = new JLiveChatModelJLCDate();

		$sql = "UPDATE #__jlc_chat_member SET
		    is_typing = ".(int)$clientIsTyping.",
		    mdate = ".$dateObj->toUnix()."
		WHERE member_id = ".(int)$memberId."
		AND chat_session_id = ".(int)$chatSessionId;
		$this->_db->setQuery($sql);

		return $this->_db->query();
	}

	function emailTranscript($chatSessionId, $sendToEmail)
	{
		jimport('joomla.mail.mail');

		$filename = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'popup.css');

		if(file_exists($filename))
		{
			jimport('joomla.filesystem.file');

			$css = file_get_contents($filename);

			$css .= "\r\n\r\nbody {\r\nmargin: 0 !important; \r\n padding: 10px !important;\r\n color: black;\r\n}\r\n\r\n";
		}
		else
		{
			$css = '';
		}

		$sessionData = $this->getChatSession($chatSessionId);

		$email = & JFactory::getMailer();

		$from = array(
			0 => $this->_settings->getSetting('emails_from'),
			1 => $this->_settings->getSiteName()
		);

		$email->setSender($from);

		$email->setSubject(JText::sprintf('SESSION_TRANSCRIPT_EMAIL_SUBJECT', $sessionData['chat_alt_id']));

		$email->addRecipient($sendToEmail);

		$email->IsHTML(true);

		// Base64 encode email to prevent problems
		$email->Encoding = 'base64';

		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\r\n";
		$html .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" >'."\r\n";
		$html .= "<head>\r\n";

		$html = "<style type=\"text/css\">\r\n";
		$html .= $css;
		$html .= "</style>\r\n";
		$html .= "</head>\r\n";
		$html .= "<body>\r\n";

		$html .= '<span style="color: black;">'.$sessionData['chat_session_content'].'</span>';

		$html .= "</body>\r\n";
		$html .= "</html>\r\n";

		$email->setBody($html);

		return $email->Send();
	}

	function transferChatSession($chatSessionId, $transferToOperatorId)
	{
		// First make sure the operator is not already in this chat session
		$sql = "SELECT
		    is_accepted,
		    is_gone
		FROM #__jlc_chat_member
		WHERE chat_session_id = ".(int)$chatSessionId."
		AND operator_id = ".(int)$transferToOperatorId."
		LIMIT 1;";
		$this->_db->setQuery($sql);

		$checkResult = $this->_db->loadAssoc();

		if($checkResult)
		{
			// This operator record does exist, check it
			if((int)$checkResult['is_accepted'] == 1 && (int)$checkResult['is_gone'] == 0)
			{
				// This operator is already in this chat session and hasn't left yet, ignore transfer request
				return false;
			}
		}

		$this->switchToMultiMemberMode($chatSessionId);

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'routing.php';

		$routingObj = & CMSModel::getInstance('Routing', 'JLiveChatModel');

		$this->resetOperatorAnswer($chatSessionId, $transferToOperatorId);

		return $routingObj->requestChatFromOperators($chatSessionId, array($transferToOperatorId), true);
	}

	function resetOperatorAnswer($chatSessionId, $operatorId)
	{
		$dateObj = new JLiveChatModelJLCDate();

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'operator.php';

		$operatorObj = & CMSModel::getInstance('Operator', 'JLiveChatModel');

		$operator = $operatorObj->getOperator($operatorId);

		$nowTime = $dateObj->toUnix();
		$expireTime = $nowTime + $operator['accept_chat_timeout'];

		// Reset Operator Answer, if any
		$sql = "UPDATE #__jlc_chat_member SET
		    is_accepted = NULL,
		    is_gone = 0,
		    mdate = ".$nowTime.",
		    expire_time = ".$expireTime."
		WHERE chat_session_id = ".(int)$chatSessionId."
		AND operator_id = ".(int)$operatorId;
		$this->_db->setQuery($sql);
		$this->_db->query();

		// Update chat session mdate
		$sql = "UPDATE #__jlc_chat SET
		    mdate = ".$nowTime."
		WHERE chat_session_id = ".(int)$chatSessionId.";";
		$this->_db->setQuery($sql);
		$this->_db->query();

		return true;
	}

	function requestOperatorChat($operatorId, $requestFromOperatorId)
	{
		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'operator.php';

		$operatorObj = & CMSModel::getInstance('Operator', 'JLiveChatModel');

		$operator = $operatorObj->getOperator($operatorId);
		$requestFromOperator = $operatorObj->getOperator($requestFromOperatorId);

		$chatSessionId = $this->startNewChatSession($this->_isRestful);

		if($this->_isRestful)
		{
			$chatSessionDetails = $chatSessionId;
			$chatSessionId = $chatSessionDetails['chat_session_id'];
		}

		$this->joinChatSession($chatSessionId, $operator['operator_name'], 1, $operator['operator_id'], false);
		$this->joinChatSession($chatSessionId, $requestFromOperator['operator_name'], null, $requestFromOperator['operator_id'], false);

		if($this->_isRestful)
		{
			return $chatSessionDetails;
		}
		else
		{
			return $chatSessionId;
		}
	}

	function switchToMultiMemberMode($chatSessionId)
	{
		$dateObj = new JLiveChatModelJLCDate();

		$data = new stdClass();

		$data->chat_session_id = (int)$chatSessionId;
		$data->is_multimember = 1;
		$data->mdate = $dateObj->toUnix();

		return $this->_db->updateObject('#__jlc_chat', $data, 'chat_session_id');
	}

	function updatePopupMode($chatSessionId, $newPopupMode)
	{
		$currentPopupMode = $this->getPopupMode($chatSessionId);

		// Popup mode Hasn't changed, skip update
		if($currentPopupMode == $newPopupMode) return false;

		if($currentPopupMode == 'popup' && $newPopupMode == 'iframe') return false;

		$chatSessionParams = $this->getChatSessionParams($chatSessionId);

		$chatSessionParams->popup_mode = $newPopupMode;

		$data = new stdClass();

		$data->chat_session_id = (int)$chatSessionId;
		$data->chat_session_params = json_encode($chatSessionParams);

		return $this->_db->updateObject('#__jlc_chat', $data, 'chat_session_id');
	}

	function getPopupMode($chatSessionId)
	{
		$chatSessionParams = $this->getChatSessionParams($chatSessionId);

		if(isset($chatSessionParams->popup_mode))
		{
			return $chatSessionParams->popup_mode;
		}

		return false;
	}

	function isActive($chatSessionId, $memberId)
	{
		if(empty($chatSessionId) || empty($memberId) || !$chatSessionId || !$memberId) return false;

		$sql = "SELECT count(*) FROM #__jlc_chat c
		INNER JOIN #__jlc_chat_member cm
		USING(chat_session_id) 
		WHERE c.chat_session_id = ".(int)$chatSessionId."
		AND c.is_active = 1 
		AND cm.member_id != ".(int)$memberId."
		AND cm.is_accepted = 1 
		AND cm.is_gone = 0;";
		$this->_db->setQuery($sql);

		if($this->_db->loadResult() > 0) return true;

		return false;
	}
}