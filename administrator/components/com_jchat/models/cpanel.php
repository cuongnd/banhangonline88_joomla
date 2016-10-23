<?php
// namespace administrator\components\com_jchat\models;
/**
 *
 * @package JCHAT::CPANEL::administrator::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
define ('SERVER_REMOTE_URI', 'http://storejextensions.org/dmdocuments/updates/');
define ('UPDATES_FORMAT', '.json');
define ('UPDATES_BRANCH', '_enterprise');
jimport ( 'joomla.application.component.model' );
 
/**
 * Messages model responsibilities contract
 *
 * @package JCHAT::MESSAGES::administrator::components::com_jchat
 * @subpackage models
 * @since 1.0
 */
interface ICPanelModel {
	 /**
	  * Main get data method
	  *
	  * @access public
	  * @return array
	  */
	 public function getData();
	 
	 /**
	  * Get by remote server informations for new updates of this extension
	  *
	  * @access public
	  * @param JChatHttp $httpClient
	  * @return mixed An object json decoded from server if update information retrieved correctly otherwise false
	  */
	 public function getUpdates(JChatHttp $httpClient);
	 
	/**
	 * Delete from file system all obsolete exchanged files
	 * @access public
	 * @return boolean
	 */
	public function purgeFileCache();
	
	/**
	 * Delete from all session status database tables
	 * @access public
	 * @return boolean
	 */
	public function purgeDbCache();
}
/**
 * CPanel model concrete implementation
 *
 * @package JCHAT::CPANEL::administrator::components::com_jchat
 * @subpackage models
 * @since 1.0
 */
class JChatModelCpanel extends JChatModel {
	/**
	 * Component configuration params
	 * @access protected
	 * @var Object
	 */
	protected $cParams = null;
	
	 /**
	  * Counter result set
	  * 
	  * @access protected
	  * @return int
	  */
	 protected function buildListQueryTotalUsers() {
	 	$query = "SELECT COUNT(*) FROM #__users" .
	 	 		 "\n WHERE " . $this->_db->quoteName('block') . " = 0";

	 	return $query;
	 }
	 
	 /**
	  * Counter result set
	  * 
	  * @access protected
	  * @return int
	  */
	 protected function buildListQueryLoggedUsers() {
	 	$query = "SELECT COUNT(*) FROM #__users AS u" .
	 	 		 "\n INNER JOIN #__session AS sess" .
	 	 		 "\n ON sess.userid = u.id" .
 				 "\n WHERE u.block = 0" .
 				 "\n AND sess.guest = 0" .
	 			 "\n AND sess.client_id = 0";
	 	
	 	return $query;
	 }
	 
	 /**
	  * Counter result set
	  *
	  * @access protected
	  * @return int
	  */
	 protected function buildListQueryBannedUsers() {
	 	$query = "SELECT COUNT(*) FROM #__session AS sess" .
	 			 "\n INNER JOIN #__jchat_banned_users AS ban" .
	 			 "\n ON sess.session_id = ban.banned" .
	 			 "\n WHERE sess.client_id = 0";

	 	return $query;
	 }
	 
	 /**
	  * Counter result set
	  *
	  * @access protected
	  * @return int
	  */
	 protected function buildListQueryVideochatSessions() {
	 	$query = "SELECT COUNT(*) FROM #__jchat_webrtc AS rtc1" .
	 			 "\n INNER JOIN #__jchat_webrtc AS rtc2" .
	 			 "\n ON rtc1.peer1 = rtc2.peer2";
	 
	 	return $query;
	 }
	 
	 /**
	  * Counter result set
	  * 
	  * @access protected
	  * @return int
	  */
	 protected function buildListQueryTotalMessages() {
	 	$query = "SELECT COUNT(*) FROM #__jchat AS c" .
	 			 "\n WHERE c.type = " . $this->_db->quote('message');
	 	 
	 	return $query;
	 }
	 
	 /**
	  * Counter result set
	  * 
	  * @access protected
	  * @return int
	  */
	 protected function buildListQueryTotalFileMessages() {
	 	$query = "SELECT COUNT(*) FROM #__jchat AS c" .
	 			 "\n WHERE c.type = " . $this->_db->quote('file');
	 	 
	 	return $query;
	 }
	 
	 /**
	  * Main get data method
	  *
	  * @access public
	  * @return array
	  */
	 public function getData() {
	 	$calculatedStats = array();
	 	// Build queries
	 	try {
	 		// Total users
	 		$query = $this->buildListQueryTotalUsers ();
	 		$this->_db->setQuery ( $query );
	 		$calculatedStats['chart_users_canvas']['totalusers'] = $this->_db->loadResult ();
	 		// Total global modules
	 		if($this->_db->getErrorNum()) {
	 			throw new JChatException(JText::_('COM_JCHAT_DBERROR_STATS') . $this->_db->getErrorMsg(), 'error');
	 		}
	 			
	 		// Logged in users
	 		$query = $this->buildListQueryLoggedUsers ();
	 		$this->_db->setQuery ( $query );
	 		$calculatedStats['chart_users_canvas']['loggedusers'] = $this->_db->loadResult ();
	 		// Total published modules
	 		if($this->_db->getErrorNum()) {
	 			throw new JChatException(JText::_('COM_JCHAT_DBERROR_STATS') . $this->_db->getErrorMsg(), 'error');
	 		}
	 		
	 		// Total messages exchanged
	 		$query = $this->buildListQueryTotalMessages ();
	 		$this->_db->setQuery ( $query );
	 		$calculatedStats['chart_messages_canvas']['totalmessages'] = $this->_db->loadResult ();
	 		// Total published modules
	 		if($this->_db->getErrorNum()) {
	 			throw new JChatException(JText::_('COM_JCHAT_DBERROR_STATS') . $this->_db->getErrorMsg(), 'error');
	 		}
	 		
	 		// Total file messages exchanged
	 		$query = $this->buildListQueryTotalFileMessages ();
	 		$this->_db->setQuery ( $query );
	 		$calculatedStats['chart_messages_canvas']['totalfilemessages'] = $this->_db->loadResult ();
	 		// Total published modules
	 		if($this->_db->getErrorNum()) {
	 			throw new JChatException(JText::_('COM_JCHAT_DBERROR_STATS') . $this->_db->getErrorMsg(), 'error');
	 		}
	 		
	 		// Total banned users
	 		$query = $this->buildListQueryBannedUsers ();
	 		$this->_db->setQuery ( $query );
	 		$calculatedStats['chart_videochat_canvas']['totalbannedusers'] = $this->_db->loadResult ();
	 		// Total published modules
	 		if($this->_db->getErrorNum()) {
	 			throw new JChatException(JText::_('COM_JCHAT_DBERROR_STATS') . $this->_db->getErrorMsg(), 'error');
	 		}
	 		
	 		// Total active videochat sessions
	 		$query = $this->buildListQueryVideochatSessions ();
	 		$this->_db->setQuery ( $query );
	 		$calculatedStats['chart_videochat_canvas']['totalvideochatsessions'] = $this->_db->loadResult () / 2;
	 		// Total published modules
	 		if($this->_db->getErrorNum()) {
	 			throw new JChatException(JText::_('COM_JCHAT_DBERROR_STATS') . $this->_db->getErrorMsg(), 'error');
	 		}
	 	} catch (JChatException $e) {
	 		$this->app->enqueueMessage($e->getMessage(), $e->getErrorLevel());
	 		$calculatedStats = array();
	 	} catch (Exception $e) {
	 		$jchatException = new JChatException($e->getMessage(), 'error');
	 		$this->app->enqueueMessage($jchatException->getMessage(), $jchatException->getErrorLevel());
	 		$calculatedStats = array();
	 	}
	 
	 	return $calculatedStats;
	 }
	 
	 /**
	  * Get by remote server informations for new updates of this extension
	  *
	  * @access public
	  * @param JChatHttp $httpClient
	  * @return mixed An object json decoded from server if update information retrieved correctly otherwise false
	  */
	 public function getUpdates(JChatHttp $httpClient) {
	 	// Updates server remote URI
	 	$option = $this->getState ( 'option', 'com_jchat' );
	 	if(!$option) {
	 		return false;
	 	}
	 	$url = SERVER_REMOTE_URI . $option . UPDATES_BRANCH . UPDATES_FORMAT;
	 
	 	// Try to get informations
	 	try {
	 		$response = $httpClient->get($url)->body;
	 		if($response) {
	 			$decodedUpdateInfos = json_decode($response);
	 		}
	 		return $decodedUpdateInfos;
	 	} catch(JChatException $e) {
	 		return false;
	 	}  catch(Exception $e) {
	 		return false;
	 	}
	 }

	 /**
	  * Delete from file system all obsolete exchanged files
	  * @access public
	  * @return boolean
	  */
	 public function purgeFileCache() {
	 	// Garbage files cache folder
	 	try {
	 		if(is_dir($this->cParams->get('cacheFolder'))) {
	 			$filenames = array();
	 			if(class_exists('DirectoryIterator', false)) {
	 				// Clear exchanged attachment cache
	 				$iterator = new DirectoryIterator($this->cParams->get('cacheFolder'));
	 				foreach ($iterator as $fileinfo) {
	 					if ($fileinfo->isFile() && $fileinfo->getFilename() != 'index.html') {
	 						unlink($fileinfo->getRealPath());
	 					}
	 				}
	 
	 				// Clear avatars cache
	 				$iterator = new DirectoryIterator($this->cParams->get('avatarFolder'));
	 				foreach ($iterator as $fileinfo) {
	 					if ($fileinfo->isFile() && $fileinfo->getFilename() != 'index.html' && strpos($fileinfo->getFilename(), 'gsid') === 0) {
	 						unlink($fileinfo->getRealPath());
	 					}
	 				}
	 			} else {
	 				throw new Exception(JText::_('COM_JCHAT_NO_SPL_SUPPORT'));
	 			}
	 		} else {
	 			throw new Exception(JText::_('COM_JCHAT_INVALID_CACHE_PATH'));
	 		}
	 	} catch (Exception $e) {
	 		$jchatException = new JChatException($e->getMessage(), 'error');
	 		$this->setError($jchatException);
	 		return false;
	 	}
	 	return true;
	 }
	 
	 /**
	  * Delete from all session status database tables
	  * @access public
	  * @return boolean
	  */
	 public function purgeDbCache() {
	 	$query = $this->_db->getQuery(true);
	 	try {
	 		// Delete session status still not active session for Joomla session lifetime
	 		$queryDeleteJoin = "DELETE status".
							   "\n FROM " . $this->_db->quoteName('#__jchat_sessionstatus') . " AS status" . 
							   "\n LEFT JOIN " . $this->_db->quoteName('#__session') . " AS sess" . 
							   "\n ON status.sessionid = sess.session_id" .
							   "\n WHERE sess.session_id IS NULL";
	 		// Purge session status
	 		$this->_db->setQuery($queryDeleteJoin)->execute();
	 		
	 		// Delete userstatus only for users no more existant inside system
 			$queryDeleteJoin = 	"DELETE userstatus".
						   		"\n FROM " . $this->_db->quoteName('#__jchat_userstatus') . " AS userstatus" . 
						   		"\n LEFT JOIN " . $this->_db->quoteName('#__users') . " AS user" . 
						   		"\n ON userstatus.userid = user.id" .
						   		"\n WHERE user.id IS NULL";
 			// Purge session status
 			$this->_db->setQuery($queryDeleteJoin)->execute();
 			
 			// Delete banned users still not active session for Joomla session lifetime
 			$queryDeleteJoin =  "DELETE ban".
			 					"\n FROM " . $this->_db->quoteName('#__jchat_banned_users') . " AS ban" .
			 					"\n LEFT JOIN " . $this->_db->quoteName('#__session') . " AS sess" .
			 					"\n ON ban.banning = sess.session_id" .
			 					"\n WHERE sess.session_id IS NULL";
 			// Purge session status
 			$this->_db->setQuery($queryDeleteJoin)->execute();
 			
 			// Delete dirty Webrtc session relations if any, still not active session for Joomla session lifetime
 			$queryDeleteJoin =  "DELETE webrtc".
								"\n FROM " . $this->_db->quoteName('#__jchat_webrtc') . " AS webrtc" .
								"\n LEFT JOIN " . $this->_db->quoteName('#__session') . " AS sess" .
								"\n ON webrtc.peer1 = sess.session_id" .
								"\n WHERE sess.session_id IS NULL";
 			// Purge session status
 			$this->_db->setQuery($queryDeleteJoin)->execute();
 			
 			// Delete no more available users after deletion in the core Joomla users table, this allows relogin and creation of a new login user
 			$queryDeleteJoin =  "DELETE login".
								"\n FROM " . $this->_db->quoteName('#__jchat_login') . " AS login" .
								"\n LEFT JOIN " . $this->_db->quoteName('#__users') . " AS u" .
								"\n ON login.j_uid = u.id" .
								"\n WHERE u.id IS NULL";
 			// Purge session status
 			$this->_db->setQuery($queryDeleteJoin)->execute();
 			
	 		// Delete session relations when public group chat is in invitation mode
	 		$query->delete('#__jchat_public_sessionrelations');
	 		// Purge session status
	 		$this->_db->setQuery($query)->execute();
	 	} catch ( JChatException $e ) {
	 		$this->setError ( $e );
	 		return false;
	 	} catch ( Exception $e ) {
	 		$JChatException = new JChatException ( $e->getMessage (), 'error' );
	 		$this->setError ( $JChatException );
	 		return false;
	 	}
	 	
	 	return true;
	 }
	 
	 /**
	  * Class constructor
	  * @access public
	  * @param array $config
	  * @return Object&
	  */
	 public function __construct($config = array()) {
	 	// Parent constructor
	 	parent::__construct($config);
	 	
	 	$this->cParams = JComponentHelper::getParams('com_jchat');
	 	$this->cParams->set('cacheFolder', JPATH_COMPONENT_SITE . '/cache/');
	 	$this->cParams->set('avatarFolder', JPATH_COMPONENT_SITE . '/images/avatars/');
	 }
	 
}