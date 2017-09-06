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
 * @since 1.0
 */ 
class JChatModelWebrtc extends JChatModel {
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
	 * 
	 * Store contact user ID for current owner
	 * 
	 * @param string $otherPeer
	 * @param string $sdp
	 * @param string $iceCandidate
	 * @param int $videoCam
	 * @access public
	 * @return boolean
	 */
	public function storeEntity($otherPeer = null, $sdp = null, $iceCandidate = null, $videoCam = null, $isCaller = 0) {
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
		
		// Check if the current caller is not already a callee from other peer, respect concurrency model
		$queryNotCallee = "SELECT" .
						  $this->_db->quoteName('peer1') .
						  "\n FROM #__jchat_webrtc" .
						  "\n WHERE " .
						  $this->_db->quoteName('peer2') . " = " .
						  $this->_db->quote($this->peer1);
		$imIACallee = $this->_db->setQuery($queryNotCallee)->loadResult();
		
		if($isCaller) {
			// This peer is already a callee from other peer, don't go on starting a collision call!
			if($imIACallee) {
				$this->response['storing'] = array('status'=>false, 'exception_message'=>JText::_('COM_JCHAT_ALREADY_A_CALLEE'));
				return $this->response;
			}
			
			// Check if the current callee is not already busy in another call, AKA is a callee of another user AKA has already a caller, respect concurrency model
			$queryNotCallee = "SELECT" .
							  $this->_db->quoteName('peer1') .
							  "\n FROM #__jchat_webrtc" .
							  "\n WHERE " .
							  $this->_db->quoteName('peer2') . " = " .
							  $this->_db->quote($otherPeer);
			$hasAlreadyACaller = $this->_db->setQuery($queryNotCallee)->loadResult();
			if($hasAlreadyACaller && $hasAlreadyACaller != $this->peer1) {
				// The other peer is already a callee from another peer not me, don't go on starting a collision call!
				$this->response['storing'] = array('status'=>false, 'exception_message'=>JText::_('COM_JCHAT_HAS_ALREADY_A_CALLER'), 'usermessage'=>true);
				return $this->response;
			}
			
			// Check if the current callee is not already busy in another call, AKA is a caller of another user AKA has already a callee, respect concurrency model
			$queryNotCaller = "SELECT" .
							  $this->_db->quoteName('peer2') .
							  "\n FROM #__jchat_webrtc" .
							  "\n WHERE " .
							  $this->_db->quoteName('peer1') . " = " .
							  $this->_db->quote($otherPeer);
			$isAlreadyACaller = $this->_db->setQuery($queryNotCaller)->loadResult();
			if($isAlreadyACaller) {
				// The other peer is already a callee from another peer not me, don't go on starting a collision call!
				$this->response['storing'] = array('status'=>false, 'exception_message'=>JText::_('COM_JCHAT_HAS_ALREADY_A_CALLER'), 'usermessage'=>true);
				return $this->response;
			}
		} else {
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
		$query = "INSERT INTO #__jchat_webrtc" .
				 "\n (" . $this->_db->quoteName('peer1') . "," .
				 $this->_db->quoteName('peer2') . "," .
				 $this->_db->quoteName($field) . "," .
				 $this->_db->quoteName('videocam') . ")" .
				 "\n VALUES (" .
				 $this->_db->quote($this->peer1) . "," .
				 $this->_db->quote($otherPeer) . "," .
				 $this->_db->quote($message) . "," .
				 $this->_db->quote($videoCam) . ")" .
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
		// Check if a specific peer remote ID has been specified. At last this is mandatory
		$where = null;
		if($ids) {
			$where = "\n OR " . 
					 $this->_db->quoteName('peer1') . " = " . 
				 	 $this->_db->quote($ids);
		}
		
		// End a peers conversation call
		$query = "DELETE FROM #__jchat_webrtc" . 
				 "\n WHERE" .
				 $this->_db->quoteName('peer1') . " = " . 
				 $this->_db->quote($this->peer1) .
				 $where;
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
	 *
	 * Store contact user ID for current owner
	 *
	 * @param int $videoCam
	 * @access public
	 * @return boolean
	 */
	public function updateEntity($videoCam) {
		// End a peers conversation call
		$query = "UPDATE #__jchat_webrtc" .
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
		// Store ssessionID peer1 caller
		$this->peer1 = $config['sessiontable']->session_id;
		
		// Get component params
		$this->getComponentParams();
		
		parent::__construct($config);
	}
}