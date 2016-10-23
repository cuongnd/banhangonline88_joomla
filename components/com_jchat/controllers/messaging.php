<?php
// namespace components\com_jchat\controllers;
/**
 *
 * @package JCHAT::FORM::components::com_jchat
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Group public chat controller class
 * The entity in this MVC core is the contact user managed for group public conversation
 *
 * @package JCHAT::FORM::components::com_jchat
 * @subpackage controllers
 * @since 1.0
 */
class JChatControllerMessaging extends JChatController {
	/**
	 * Setta il model state a partire dallo userstate di sessione
	 *
	 * @access protected
	 * @param string $scope        	
	 * @param boolean $ordering        	
	 * @return void
	 */
	protected function setModelState($scope = 'default') {
		$option = $this->option;
		
		$msgType = $this->getUserStateFromRequest ( "$option.$scope.msg_type", 'msg_type' );
		
		// Set model state
		// Get default model
		$defaultModel = $this->getModel ();
		$defaultModel->setState ( 'option', $option );
	}
	
	/**
	 * Display the side users list of only logged in registered users and the empty messages area
	 *
	 * @access public
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false) {
		// Check if the user is logged in
		if (! $this->user->id) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_JCHAT_MUST_BE_LOGGEDIN' ) );
			return;
		}
		
		// Check if the user has access to the chat app based on access level parameter
		if (! $this->allowDisplay()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_JCHAT_NOACCESS' ) );
			return;
		}
		
		$viewType = $this->document->getType ();
		$coreName = $this->getNames ();
		$viewLayout = $this->app->input->get ( 'layout', 'default' );
		
		$view = $this->getView ( $coreName, $viewType, '', array (
				'base_path' => $this->basePath 
		) );
		
		// Instantiate models object with Dependency Injection chain
		$userSessionTable = JChatHelpersUsers::getSessiontable ();
		$streamModel = $this->getModel ( 'Stream', 'JChatmodel', array (
				'sessiontable' => $userSessionTable 
		) );
		
		// Get/Create the model
		if ($model = $this->getModel ( $coreName, 'JChatmodel', array (
				'streamModel' => $streamModel 
		) )) {
			// Push the model into the view (as default)
			$view->setModel ( $model, true );
		}
		
		// Set model state
		$this->setModelState ( 'messaging' );
		
		// Set the layout
		$view->setLayout ( $viewLayout );
		$view->display ();
	}
	
	/**
	 * Load user messages by AJAX request on demand clicking on userlist side tabs
	 * Addresses the user ID and not the session ID
	 *
	 * @access public
	 * @return void
	 */
	public function showEntity() {
		// Initialization
		$coreName = $this->getNames ();
		
		// Get view and pushing model
		$view = $this->getView ( $coreName, 'json', '', array (
				'base_path' => $this->basePath 
		) );
		
		// Manage async posted data
		$postData = json_decode ( $this->app->input->post->getString ( 'data' ) );
		
		// Instantiate models object with Dependency Injection chain
		$userSessionTable = JChatHelpersUsers::getSessiontable ();
		$streamModel = $this->getModel ( 'Stream', 'JChatmodel', array (
				'sessiontable' => $userSessionTable 
		) );
		
		// Get/Create the model
		$model = $this->getModel ( $coreName, 'JChatmodel', array (
				'streamModel' => $streamModel 
		));
		
		$messages = $model->loadEntity ( $postData );
		if ($messages === false) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError ( null, false );
			$response ['loading'] = array (
					'status' => false,
					'exception_message' => $modelException->getMessage () 
			);
		} else {
			$response ['loading'] = array (
					'status' => true 
			);
			$response ['messages'] = $messages;
		}
		
		// Format response for JS client as requested
		$view->display ( $response );
	}
	
	/**
	 * Delete conversation messages based on messages ids
	 *
	 * @access public
	 * @return void
	 */
	public function deleteEntity() {
		// Initialization
		$viewType = $this->document->getType ();
		$coreName = $this->getNames ();
		
		// Get view and pushing model
		$view = $this->getView ( $coreName, 'json', '', array (
				'base_path' => $this->basePath
		) );
		
		// Get post data
		$messagesIds = $this->app->input->get ( 'ids', array (
				0 
		), 'array' );
		
		// Instantiate models object with Dependency Injection chain
		$userSessionTable = JChatHelpersUsers::getSessiontable ();
		$streamModel = $this->getModel ( 'Stream', 'JChatmodel', array (
				'sessiontable' => $userSessionTable
		) );
		// Get/Create the model
		$model = $this->getModel ( $coreName, 'JChatmodel', array (
				'streamModel' => $streamModel
		));
		
		// Try to load record from model
		$messagesDeleted = $model->deleteEntity ( $messagesIds );
		if ($messagesDeleted === false) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError ( null, false );
			$response ['deleting'] = array (
					'status' => false,
					'exception_message' => $modelException->getMessage () 
			);
		} else {
			$response ['deleting'] = array (
					'status' => true 
			);
		}
		
		// Format response for JS client as requested
		$view->display ( $response );
	}
}