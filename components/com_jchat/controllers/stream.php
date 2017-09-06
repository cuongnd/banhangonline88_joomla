<?php
//namespace components\com_jchat\controllers;
/**
 * @package JCHAT::STREAM::components::com_jchat 
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Stream data controller class
 * The entity in this MVC core is the stream
 * The stream is a bidirectional entity: it can be for reading data through display method,
 * or to write data through the saveEntity method
 *
 * @package JCHAT::STREAM::components::com_jchat
 * @subpackage controllers
 * @since 1.0
 */
class JChatControllerStream extends JChatController {
	/**
	 * Set model state always getting fresh vars from POST request
	 * 
	 * @access protected
	 * @param string $scope
	 * @param boolean $ordering
	 * @param Object $explicitModel
	 * @return void
	 */
	protected function setModelState($scope = 'default', $ordering = true, $explicitModel = null) {
		// Set model state for basic stream
		$explicitModel->setState('getparams', $this->app->input->getString('getParams', null));
		$explicitModel->setState('chatbox', $this->app->input->getString('chatbox', null));
		$explicitModel->setState('wall', $this->app->input->getString('wall', null));
		$explicitModel->setState('buddylist', $this->app->input->getInt('buddylist', null));
		$explicitModel->setState('initialize', $this->app->input->getInt('initialize', null));
		$explicitModel->setState('updatesession', $this->app->input->getInt('updatesession', null));
		$explicitModel->setState('sessionvars', $this->app->input->get('sessionvars', array(), 'array'));
		$explicitModel->setState('wallhistory', $this->app->input->getBool('download_history', false));
		$explicitModel->setState('conferenceview', $this->app->input->getBool('conferenceview', false));
		
		// Set model state for get buddy list
		$explicitModel->setState('searchfilter', $this->app->input->getString('searchfilter', null));
		$explicitModel->setState('force_refresh', $this->app->input->getInt('force_refresh', 0));
		
		// Set model state for fetch messages
		$explicitModel->setState('last_received_msg_id', $this->app->input->getInt('last_received_msg_id', 0));
	}
	
	/**
	 * Display data for JS client on stream read
	 * 
	 * @access public
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false) {
		// Initialization
	 	$document = JFactory::getDocument();
		$viewType = $document->getType ();
		$coreName = $this->getNames ();
		
		// Instantiate model object with Dependency Injection
		$userSessionTable = JChatHelpersUsers::getSessiontable ();
		$model = $this->getModel($coreName, 'JChatmodel', array('sessiontable'=>$userSessionTable));
		
		// Populate model state
		$this->setModelState('streamread', false, $model);
		
		// Try to load record from model
		$chatData = $model->getChatData();
		
		// Get view and pushing model
		
		$view = $this->getView ( $coreName, $viewType, '', array ('base_path' => $this->basePath ) );
		
		// Format response for JS client as requested
		$view->display($chatData);
	}
	
	/**
	 * Save data from JS client on stream write
	 *
	 * @access public
	 * @return void
	 */
	public function saveEntity() {
		// Initialization
		$document = JFactory::getDocument();
		$viewType = $document->getType ();
		$coreName = $this->getNames ();
		
		// Based on old task detection from request params 
		$response = array('storing'=>array('status'=>false, 'details'=>JText::_('COM_JCHAT_ERROR_NOACTION_DETECTED_ONSTREAM')));
		$status = $this->app->input->getString('status');
		$skypeID = $this->app->input->getString('skypeid');
		$userStatus = $this->app->input->getString('user_status');
		$overrideName = $this->app->input->getString('override_name');
		$roomID = $this->app->input->getInt('roomid');
		$roomName = $this->app->input->getString('newroom_name');
		$toDeleteRoomID = $this->app->input->getInt('todeleteroomid');
		$roomDescription = $this->app->input->getString('newroom_description');
		$roomAccess = $this->app->input->getInt('newroom_access');
		$bannedUserInfo = $this->app->input->getString('bannedinfo');
		$bannedModeratorUserInfo = $this->app->input->getString('bannedmoderatorinfo');
		$message = $this->app->input->get('message', '', 'raw');
		
		// Change string for allowed tags to let in media objects
		$componentParams = JComponentHelper::getParams ( 'com_jchat' );
		$allowedTags = '<img>,<br>,<a>';
		if($componentParams->get('allow_media_objects', true)) {
			$allowedTags .= ',<iframe>,<video>,<source>,<object>,<embed>,<param>';
		}
		// Add first security layer for only allowed tags
		$message = strip_tags($message, $allowedTags); 
		
		// Add a second security layer for XSS
		$filteredWords = array( 'onAbort',
								'onBlur',
								'onChange',
								'onClick',
								'onDblClick',
								'onDragDrop',
								'onError',
								'onFocus',
								'onKeyDown',
								'onKeyPress',
								'onKeyUp',
								'onLoad',
								'onMouseDown',
								'onMouseMove',
								'onMouseOut',
								'onMouseOver',
								'onMouseUp',
								'onMove',
								'onReset',
								'onResize',
								'onSelect',
								'onSubmit',
								'onUnload');
		$message = str_ireplace($filteredWords, '*banned*', $message);
		
		$to = $this->app->input->getString('to');
		$from = $this->app->input->getString('from');
		$tologged = $this->app->input->getInt('tologged', 0);
	
		// Instantiate model object with Dependency Injection
		$userSessionTable = JChatHelpersUsers::getSessiontable ();
		$model = $this->getModel($coreName, 'JChatmodel', array('sessiontable'=>$userSessionTable));
	
		// Model action detection
		// Save status
		if(!empty($status)) {
			$response = $model->storeUserStatus('status', $status);
		}
		
		// Save override name
		if (isset($overrideName)) {
			$response = $model->storeUserStatus('override_name', $overrideName);
		}
		
		// Save user status
		if (isset($userStatus)) {
			$response = $model->storeUserStateFromRequest('status', $userStatus);
		}
		
		// Save SkypeID
		if (isset($skypeID)) {
			$response = $model->storeUserStateFromRequest('skypeid', $skypeID);
		}
		
		// Save chatroom id
		if (isset($roomID)) {
			$response = $model->storeUserStateFromRequest('roomid', $roomID);
		}

		// Add a new chatroom
		if (isset($roomName)) {
			$response = $model->storeNewChatroom($roomName, $roomDescription, $roomAccess);
		}
		
		// Delete an existing chatroom
		if (isset($toDeleteRoomID)) {
			$response = $model->deleteChatroom($toDeleteRoomID);
		}

		// Change banned user state for this me user
		if(!empty($bannedUserInfo)) {
			$bannedUserInfo = json_decode($bannedUserInfo);
			$response = $model->storeBannedUsersState($bannedUserInfo);
		}

		// Change banned and blocked permanently by moderators
		if(!empty($bannedModeratorUserInfo)) {
			$bannedModeratorUserInfo = json_decode($bannedModeratorUserInfo);
			$response = $model->storeBannedModeratedUsersState($bannedModeratorUserInfo);
		}

		// Save private chat message
		if (!empty($to) && !empty($message) && $to != 'wall') {
			$mailer = JChatHelpersMailer::getInstance('Joomla');
			$response = $model->storePrivateMessage($to, $tologged, $message, $mailer);
		}
		
		// Save private chat message using the private messaging to offline users
		if (empty($to) && !empty($tologged) && !empty($message) && $to != 'wall') {
			$mailer = JChatHelpersMailer::getInstance('Joomla');
			$response = $model->storePrivateMessage(false, $tologged, $message, $mailer);
		}
		
		// Save group chat message
		if(!empty($to) && !empty($message) && $to == 'wall') {
			$response = $model->storeGroupMessage($to, $message);
		} 
		
		// Delete conversation
		if (!empty($from)) {
			$response = $model->deleteConversation($from);
		}
		
		// Get view and pushing model
		$view = $this->getView ( $coreName, $viewType, '', array ('base_path' => $this->basePath ) );
		
		// Format response for JS client as requested
		$view->display($response);
	}
	
	/**
	 * Read data from JS client from stream read
	 *
	 * @access public
	 * @return void
	 */
	public function showEntity() {
		// Initialization
		$document = JFactory::getDocument();
		$viewType = $document->getType ();
		$coreName = $this->getNames ();
		
		$response = array('status'=>false, 'details'=>JText::_('COM_JCHAT_ERROR_NOACTION_DETECTED_ONSTREAM'));

		// Posted vars needed to show info
		$infoGuestSession = $this->app->input->getString('guestsession', null);
		
		// Instantiate model object with Dependency Injection
		$userSessionTable = JChatHelpersUsers::getSessiontable ();
		$model = $this->getModel($coreName, 'JChatmodel', array('sessiontable'=>$userSessionTable));
		
		// Save group chat message
		if(!empty($infoGuestSession)) {
			$response = $model->getInfoGuest($infoGuestSession);
		}
		
		// Get view and pushing model
		$view = $this->getView ( $coreName, $viewType, '', array ('base_path' => $this->basePath ) );
		
		// Format response for JS client as requested
		$view->display($response);
	}
	
	/**
	 * Read data from JS client from stream read
	 *
	 * @access public
	 * @return void
	 */
	public function showHistory() {
		// Initialization
		$document = JFactory::getDocument();
		$viewType = $document->getType ();
		$coreName = $this->getNames ();
	
		$response = array('status'=>false, 'details'=>JText::_('COM_JCHAT_ERROR_NOVALID_ACTION'));

		// Posted vars needed to messages historical recovery
		$period = $this->app->input->getString('msgs_period', '1d');
		$minMessageId = $this->app->input->getInt('min_message_id', 0);
		$fromUserid = $this->app->input->getString('from_userid', null);
		$fromLoggedId = $this->app->input->getInt('from_loggedid', 0);
	
		// Instantiate model object with Dependency Injection
		$userSessionTable = JChatHelpersUsers::getSessiontable ();
		$model = $this->getModel($coreName, 'JChatmodel', array('sessiontable'=>$userSessionTable));
	
		// Save group chat message
		if($fromLoggedId) {
			$response = $model->fetchHistoryMessages($fromLoggedId, $fromUserid, $period, $minMessageId);
		}
	
		// Get view and pushing model
		$view = $this->getView ( $coreName, $viewType, '', array ('base_path' => $this->basePath ) );
	
		// Format response for JS client as requested
		$view->display($response);
	}
}