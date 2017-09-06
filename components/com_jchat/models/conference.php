<?php
//namespace components\com_jchat\models; 
/** 
 * @package JCHAT::WEBRTC::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Group users chat model
 * 
 * @package JCHAT::WEBRTC::components::com_jchat
 * @subpackage models
 * @since 2.5
 */ 
class JChatModelConference extends JChatModel {
	/**
	 * This peer1 session ID
	 * @access private
	 * @var int
	 */
	private $peer1;
	
	/**
	 * Client response
	 * @access private
	 * @var array
	 */
	private $response;
	
	/**
	 * Restituisce la query string costruita per ottenere il wrapped set richiesto in base
	 * allo userstate, opzionalmente seleziona i campi richiesti
	 *
	 * @access private
	 * @return string
	 */
	protected function buildListQuery() {
		$queryParts = array();
		$queryParts['SELECT'] = '';
		$queryParts['JOIN'] = '';
		$accessLevels['JOIN'] = '';
		$myUsersGroups['JOIN'] = '';
		$accessLevelsAND = null;
		$myUsersGroupsAND = null;
	
		// get current time to evaluate if users are online or offline, AKA no more session refresh after maxinactivitytime seconds = users stripped out from buddylist
		$time = time();
		 
		// Evaluate ONLY FRIENDS 3PD integration option
		if($this->componentParams->get('3pdintegration', null) && $this->componentParams->get('filter_friendship', false)) {
			$queryPartsFriends = $this->streamModel->getQueryParts('buddylist');
		} else {
			$queryPartsFriends['JOIN'] = '';
			$queryPartsFriends['WHERE'] = '';
		}
		 
		// Manage live support mode filtering
		list($queryParts['JOIN'], $additionalAND) = $this->streamModel->getQueryLiveSupport ('u.id');
	
		// Manage chat filtering by access levels
		list($accessLevels['JOIN'], $accessLevelsAND) = $this->streamModel->getQueryAccessLevels('u.id');
	
		// Manage chat filtering by same users groups of the current user
		if($this->componentParams->get('limit_my_users_groups', 0)) {
			list($myUsersGroups['JOIN'], $myUsersGroupsAND) = $this->streamModel->getQueryMyUsersGroups('u.id');
		}
	
		$query = "SELECT u.id," .
				"\n u.{$this->userFieldName} AS " . $this->_db->quoteName('username') .
				"\n FROM #__users AS u" .
				$queryPartsFriends['JOIN'] .
				$queryParts['JOIN'] .
				$accessLevels['JOIN'] .
				$myUsersGroups['JOIN'] .
				"\n WHERE u.id <> " . (int)$this->myUser->id .
				"\n AND u.block = 0" .
				$additionalAND .
				$accessLevelsAND .
				$myUsersGroupsAND .
				$queryPartsFriends['WHERE'] .
				"\n ORDER BY u.{$this->userFieldName} ASC";
	
		return $query;
	}
	
	/**
	 * 
	 * Store contact user ID for current owner
	 * 
	 * @param string $otherPeer
	 * @param string $sdp
	 * @param string $iceCandidate
	 * @param int $videoCam
	 * @param int $isCaller
	 * @params string $otherPeers Used to chain calls with other participants
	 * 
	 * @access public
	 * @return boolean
	 */
	public function storeEntity($otherPeer = null, $sdp = null, $iceCandidate = null, $videoCam = null, $isCaller = 0, $otherPeers = null) {
		// Check if correct informations are feed to this server model
		if(!$otherPeer) {
			$this->response['storing'] = array('status'=>false, 'exception_message'=>JText::_('COM_JCHAT_NOPEER_SPECIFIED'));
			return $this->response;
		}
		// Missing informations sent by client, we can't go on
		if(!$sdp && !$iceCandidate) {
			$this->response['storing'] = array('status'=>false, 'exception_message'=>JText::_('COM_JCHAT_NODATA_SPECIFIED'));
			return $this->response;
		}
		
		// Check if callee has permissions to use videochat
		$aclParamValue = $this->componentParams->get('allow_videochat', array(0));
		// Ensure that the ACL parameter has no 'All groups' option selected
		if(is_array($aclParamValue) && !in_array(0, $aclParamValue, false)) {
			// Fetch current user id using session id of the remote callee peer
			$query = "SELECT " . 
					 $this->_db->quoteName('userid') .
				  	 "\n FROM " . 
				  	 $this->_db->quoteName('#__session') .
				  	 "\n WHERE " . 
					 $this->_db->quoteName('session_id') . " = " . $this->_db->quote($otherPeer);
			$otherPeerUserID = $this->_db->setQuery($query)->loadResult();
			$otherPeerUser = JUser::getInstance($otherPeerUserID);
			
			// If we are calling a super user skip the ACL permissions check, always allowed
			if(!$otherPeerUser->authorise('core.admin')) {
				$totalParamGroups = $aclParamValue;
				// Cycle on all the group selected and retrieve the child groups
				foreach ($aclParamValue as $aclParamGroup) {
					$totalParamGroups = array_merge($totalParamGroups, JChatHelpersUsers::getChildGroups($this->_db, $aclParamGroup));
				}
				// Remove duplicates
				$totalParamGroups = array_unique($totalParamGroups);
				
				// Fetch current user groups, but firstly user id using session id of the remote callee peer
				$userGroups = $otherPeerUser->getAuthorisedGroups();
				
				// Finally intersect to check ACL permission allowed
				if(!array_intersect($totalParamGroups, $userGroups)) {
					$this->response['storing'] = array('status'=>false, 'exception_message'=>JText::_('COM_JCHAT_OTHERPEER_NOTALLOWED'), 'usermessage'=>true);
					return $this->response;
				}
			}
		}
		
		// Avoid collision call based on concurrency model
		if(!$isCaller) {
			// Check if the current caller is not already a callee from other peer, respect concurrency model
			$queryNotCallee = "SELECT" .
							  $this->_db->quoteName('peer1') .
							  "\n FROM #__jchat_webrtc_conference" .
							  "\n WHERE " .
							  $this->_db->quoteName('peer2') . " = " .
							  $this->_db->quote($this->peer1);
			$imIACallee = $this->_db->setQuery($queryNotCallee)->loadResult();
			// This peer is no more a callee from other peer for example the caller hanged up, don't go on starting a collision call!
			if(!$imIACallee) {
				$this->response['storing'] = array('status'=>false, 'exception_message'=>JText::_('COM_JCHAT_NOMORE_A_CALLEE'));
				return $this->response;
			}
		}
		
		// Exchange field and value
		$field = $sdp ? 'sdp' : 'icecandidate';
		$message = $sdp ? $sdp : $iceCandidate;
		
		// Now execute insert/update query with feed data, the result will be a call start/accept 
		$query = "INSERT INTO #__jchat_webrtc_conference" .
				 "\n (" . $this->_db->quoteName('peer1') . "," .
				 $this->_db->quoteName('peer2') . "," .
				 $this->_db->quoteName($field) . "," .
				 $this->_db->quoteName('videocam') . "," .
				 $this->_db->quoteName('other_peers') . ")" .
				 "\n VALUES (" .
				 $this->_db->quote($this->peer1) . "," .
				 $this->_db->quote($otherPeer) . "," .
				 $this->_db->quote($message) . "," .
				 $this->_db->quote($videoCam) . "," .
				 $this->_db->quote($otherPeers) . ")" .
				 "\n ON DUPLICATE KEY UPDATE" .
				 $this->_db->quoteName($field) . "=" .
				 $this->_db->quote($message);
		
		$this->_db->setQuery($query);
		
		try {
			$this->_db->execute ();
			if($this->_db->getErrorNum()) {
				throw new JChatException(JText::_('COM_JCHAT_ERROR_STORE_WEBRTC') . $this->_db->getErrorMsg(), 'error');
			}
		} catch (JChatException $e) {
			$this->response['storing'] = array('status'=>false, 'exception_message'=>$e->getMessage());
			return $this->response;
				
		} catch (Exception $e) {
			$jchatException = new JChatException($e->getMessage(), 'error');
			$this->response['storing'] = array('status'=>false, 'exception_message'=>$jchatException->getMessage());
			return $this->response;
		}
		
		// All went well
		$this->response['storing'] = array('status'=>true);
		
		return $this->response;
	}
 
	/**
	 * Completely deletes a conversation call, this means that caller or callee
	 * will delete both records and reset status of both peers
	 * 
	 * @param int $ids
	 * @access public
	 * @return boolean
	 */
	public function deleteEntity($ids) {
		// End a peers conversation call
		$query = "DELETE FROM #__jchat_webrtc_conference" . 
				 "\n WHERE (" .
				 $this->_db->quoteName('peer1') . " = " . 
				 $this->_db->quote($this->peer1) .
				 "\n AND " .
				 $this->_db->quoteName('peer2') . " = " . 
				 $this->_db->quote($ids) . ")" .
				 "\n OR (" .
				 $this->_db->quoteName('peer1') . " = " . 
				 $this->_db->quote($ids) .
				 "\n AND " .
				 $this->_db->quoteName('peer2') . " = " . 
				 $this->_db->quote($this->peer1) . ")";
		$this->_db->setQuery($query);

		try {
			$this->_db->execute ();
			if($this->_db->getErrorNum()) {
				throw new JChatException($this->_db->getErrorMsg(), 'error');
			}
		} catch (JChatException $e) {
			$this->response['storing'] = array('status'=>false, 'exception_message'=>$e->getMessage());
			return $this->response;
				
		} catch (Exception $e) {
			$jchatException = new JChatException($e->getMessage(), 'error');
			$this->response['storing'] = array('status'=>false, 'exception_message'=>$jchatException->getMessage());
			return $this->response;
		}
		
		// All went well
		$this->response['storing'] = array('status'=>true);
		
		// Remove my own deleted call from the session to avoid false double
		if(isset($_SESSION['jchat_conference_webrtc_caller_sessions'][$ids])) {
			unset($_SESSION['jchat_conference_webrtc_caller_sessions'][$ids]);
		}
		
		return $this->response;
	}
	
	/**
	 *
	 * Store contact user ID for current owner
	 *
	 * @param int $videoCam
	 * @access public
	 * @return boolean
	 */
	public function updateEntity($videoCam) {
		// End a peers conversation call
		$query = "UPDATE #__jchat_webrtc_conference" .
				 "\n SET " .
				 $this->_db->quoteName('videocam') . " = " . (int)$videoCam .
				 "\n WHERE" .
				 $this->_db->quoteName('peer1') . " = " .
				 $this->_db->quote($this->peer1);
		$this->_db->setQuery($query);
		
		try {
			$this->_db->execute ();
			if($this->_db->getErrorNum()) {
				throw new JChatException($this->_db->getErrorMsg(), 'error');
			}
		} catch (JChatException $e) {
			$this->response['storing'] = array('status'=>false, 'exception_message'=>$e->getMessage());
			return $this->response;
		
		} catch (Exception $e) {
			$jchatException = new JChatException($e->getMessage(), 'error');
			$this->response['storing'] = array('status'=>false, 'exception_message'=>$jchatException->getMessage());
			return $this->response;
		}
		
		// All went well
		$this->response['storing'] = array('status'=>true);
		
		return $this->response;
	}
	
	/**
	 * Class constructor
	 * @access public
	 * @param Object& $wpdb
	 * @param Object& $userObject
	 * @return Object &
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
		
		// Store ssessionID peer1 caller
		$this->peer1 = $config['sessiontable']->session_id;
		$this->response = array();
		
		// Get component params
		$this->getComponentParams();
		
		// Reference to the stream model
		if(isset($config['streamModel'])) {
			$this->streamModel = $config['streamModel'];
		}
		
		// Store the owned user object instance
		$this->myUser = JFactory::getUser();
		
		$filter = JFilterInput::getInstance();
		$this->userFieldName = $filter->clean($this->componentParams->get('usefullname', 'username'), 'word');
		
	}
}