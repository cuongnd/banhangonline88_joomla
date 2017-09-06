<?php
//namespace components\com_jchat\models; 
/** 
 * @package JCHAT::FORM::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Group users chat model
 * 
 * @package JCHAT::FORM::components::com_jchat
 * @subpackage models
 * @since 1.0
 */ 
class JChatModelMessaging extends JChatModel {
	/**
	 * Reference to the stream model
	 * 
	 * @access private
	 * @var Object &
	 */
	private $streamModel;
	
	/**
	 * User Object
	 * @access private
	 * @var Object &
	 */
	private $myUser;
	
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

  		$query = "SELECT DISTINCT(u.id)," .
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
	 * Main get data method to retrieve the users list
	 *
	 * @access public
	 * @return Object[]
	 */
	public function getData() {
		// Build query
		$query = $this->buildListQuery ();
		$this->_db->setQuery ( $query );
		try {
			$result = $this->_db->loadObjectList ();
			if ($this->_db->getErrorNum ()) {
				throw new JChatException ( JText::sprintf ( 'COM_JCHAT_ERROR_RECORDS', $this->_db->getErrorMsg () ), 'error' );
			}
		} catch ( JChatException $e ) {
			$this->app->enqueueMessage ( $e->getMessage (), $e->getErrorLevel () );
			$result = array ();
		} catch ( Exception $e ) {
			$jchatException = new JChatException ( $e->getMessage (), 'error' );
			$this->app->enqueueMessage ( $jchatException->getMessage (), $jchatException->getErrorLevel () );
			$result = array ();
		}
		return $result;
	}
	
	/**
	 * Main get data method to retrieve the users list
	 *
	 * @access public
	 * @return Object[]
	 */
	public function getPendingMessages() {
		// Build query
		$query = "SELECT COUNT(*) AS newmessages, cchat.fromuser" .
				 "\n FROM #__jchat AS cchat" .
				 "\n WHERE cchat.touser = " . (int)$this->myUser->id .
				 "\n AND cchat.read = 0" .
				 "\n GROUP BY cchat.fromuser";
		
		$this->_db->setQuery ( $query );
		try {
			$result = $this->_db->loadAssocList ('fromuser');
			if ($this->_db->getErrorNum ()) {
				throw new JChatException ( JText::sprintf ( 'COM_JCHAT_ERROR_RECORDS', $this->_db->getErrorMsg () ), 'error' );
			}
		} catch ( JChatException $e ) {
			$this->app->enqueueMessage ( $e->getMessage (), $e->getErrorLevel () );
			$result = array ();
		} catch ( Exception $e ) {
			$jchatException = new JChatException ( $e->getMessage (), 'error' );
			$this->app->enqueueMessage ( $jchatException->getMessage (), $jchatException->getErrorLevel () );
			$result = array ();
		}
		return $result;
	}
	
	/**
	 * Load entity from ORM table and manages session data post error redirect
	 *
	 * @access public
	 * @param int $id The target user id in the conversation
	 * @return Object&
	 */
	public function loadEntity($id) {
		$fromLoggedID = $id->fromLoggedId;
		$limitQueryMessages = null;
		
		if(isset($id->oldestMsgId)) {
			$oldestMessageID = $id->oldestMsgId;
			$limitQueryMessages = $oldestMessageID ? "\n AND cchat.id < " . (int)$oldestMessageID  : null;
		}
		
		$limitMessages = "\n LIMIT "  . (int)$this->componentParams->get('pm_num_loading_msgs', 100);
		
		try {
			$sql = 	"SELECT cchat.id, cchat.from," .
					"\n cchat.message, cchat.sent, cchat.read, cchat.type, cchat.status, u.id AS userid, " .
					"\n u.{$this->userFieldName} AS " . $this->_db->quoteName('fromusername') .
					"\n FROM #__jchat AS cchat" .
					"\n INNER JOIN #__users AS u ON cchat.fromuser = u.id" .
					"\n WHERE ((cchat.touser = ". $this->_db->quote($this->myUser->id) .
					"\n AND cchat.fromuser = " . $this->_db->quote($fromLoggedID) . ")" .
					"\n OR (cchat.fromuser = ". $this->_db->quote($this->myUser->id) .
					"\n AND cchat.touser = " . $this->_db->quote($fromLoggedID) . "))" .
					"\n AND cchat.id NOT IN(SELECT " . $this->_db->quoteName('messageid') . 
					"\n FROM " . $this->_db->quoteName('#__jchat_messaging_deletedmessages') .
					"\n WHERE " . $this->_db->quoteName('userid') . " = " . $this->_db->quote($this->myUser->id) . ")" .
					$limitQueryMessages .
					"\n ORDER BY cchat.id DESC" .
					$limitMessages;
				
			$this->_db->setQuery($sql);
			$rows = $this->_db->loadAssocList();

			// Now update status of all messages received till the latest new message as read status
			$sql = "UPDATE" .
					"\n " . $this->_db->quoteName('#__jchat') . " AS cchat" .
					"\n SET " . $this->_db->quoteName('read') . " = 1 " .
					"\n WHERE " . $this->_db->quoteName('touser') . " = " . (int)($this->myUser->id) .
					"\n AND " . $this->_db->quoteName('fromuser') . " = " . (int)($fromLoggedID) .
					"\n AND " . $this->_db->quoteName('read') . " = 0" .
					$limitQueryMessages .
					"\n ORDER BY cchat.id DESC" .
					$limitMessages;
				
			$this->_db->setQuery($sql);
			$this->_db->execute();
			
			return $rows;
		} catch ( JChatException $e ) {
			$this->setError ( $e );
			return false;
		} catch ( Exception $e ) {
			$jchatException = new JChatException ( $e->getMessage (), 'error' );
			$this->setError ( $jchatException );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Delete conversation messages based on messages ids but not deleting records
	 * It only flags the messages as deleted from the current user on demand to avoid redownload
	 * The physical deletion must be done in admin
	 *
	 * @param int $ids
	 * @access public
	 * @return boolean
	 */
	public function deleteEntity($ids) {
		$chunksQuery = array();
		
		// Now update status of all messages received till latest new message as read status
		foreach ($ids as $messageId) {
			$chunksQuery[] = "(" .
					(int)($messageId) . ","  .
					(int)($this->myUser->id) . ")";
		}
		
		try {
			$sql = "INSERT IGNORE INTO #__jchat_messaging_deletedmessages (" .
					$this->_db->quoteName('messageid') . ", " .
					$this->_db->quoteName('userid') . 
					") VALUES " . implode(",\n", $chunksQuery);
			
			$this->_db->setQuery($sql);
			if(!$this->_db->query()) {
				throw new JChatException(JText::_('COM_JCHAT_ERRORSYNC_INSERT'), 'notice');
			}
			
			$this->_db->setQuery($sql);
			$this->_db->execute();
				
		} catch ( JChatException $e ) {
			$this->setError ( $e );
			return false;
		} catch ( Exception $e ) {
			$jchatException = new JChatException ( $e->getMessage (), 'error' );
			$this->setError ( $jchatException );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Class constructor
	 * @access public
	 * @param Object& $wpdb
	 * @param Object& $userObject
	 * @return Object &
	 */
	public function __construct($config = array()) {
		// Parent model construct
		parent::__construct($config);
		
		$this->getComponentParams();
		
		// Reference to the stream model
		$this->streamModel = $config['streamModel'];
		
		// Store the owned user object instance
		$this->myUser = JFactory::getUser();
		
		$filter = JFilterInput::getInstance();
		$this->userFieldName = $filter->clean($this->componentParams->get('usefullname', 'username'), 'word');
	}
}