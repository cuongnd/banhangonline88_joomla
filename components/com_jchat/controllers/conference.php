<?php
//namespace components\com_jchat\controllers;
/**
 * @package JCHAT::WEBRTC::components::com_jchat 
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Group public chat controller class
 * The entity in this MVC core is the contact user managed for group public conversation
 *
 * @package JCHAT::WEBRTC::components::com_jchat
 * @subpackage controllers
 * @since 2.5
 */
class JChatControllerConference extends JChatController {
	/**
	 * Display the video conference view
	 *
	 * @access public
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false) {
		// Check if the user has access to the chat app based on various access parameters
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
				'streamModel' => $streamModel,
				'sessiontable' => $userSessionTable
		) )) {
			// Push the model into the view (as default)
			$view->setModel ( $model, true );
		}
	
		// Check if the user is logged in
		if (!$model->getComponentParams()->get('conference_access_guest', 0) && !$this->user->id) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_JCHAT_MUST_BE_LOGGEDIN_FOR_CONFERENCE' ) );
			return;
		}
		
		// Set the layout
		$view->setLayout ( $viewLayout );
		$view->display ();
	}
	
	/**
	 * Save peer session data
	 * 
	 * @access public
	 * @return void
	 */
	public function saveEntity() {
		// Initialization
		$viewType = $this->document->getType ();
		$coreName = $this->getNames ();
		
		// Instantiate model object with Dependency Injection
		$userSessionTable = JChatHelpersUsers::getSessiontable ();
		$model = $this->getModel($coreName, 'JChatmodel', array('sessiontable'=>$userSessionTable));
		
		// Retrieve POST data
		$otherPeer = $this->app->input->getString('peer2', null);
		$sdp = $this->app->input->getString('sdp', null);
		$iceCandidate = $this->app->input->getString('icecandidate', null);
		$videoCam = $this->app->input->getInt('videocam', null);
		$isCaller = $this->app->input->getInt('caller', 0);
		$otherPeers = $this->app->input->getString('other_peers', '[]');
		
		// Try to store record using model
		$response = $model->storeEntity($otherPeer, $sdp, $iceCandidate, $videoCam, $isCaller, $otherPeers);
		
		// Get view
		$view = $this->getView ( $coreName, $viewType, '', array ('base_path' => $this->basePath ) );
		
		// Format response for JS client as requested
		$view->display($response);
	}
	
	/**
	 * Remove peer session data
	 * 
	 * @access public
	 * @return void
	 */
	public function deleteEntity() {
		// Initialization
		$viewType = $this->document->getType ();
		$coreName = $this->getNames ();
		
		// Instantiate model object with Dependency Injection
		$userSessionTable = JChatHelpersUsers::getSessiontable ();
		$model = $this->getModel($coreName, 'JChatmodel', array('sessiontable'=>$userSessionTable));
	
		$remotePeer = $this->app->input->getString('ids', null);
	
		// Try to load record from model
		$response = $model->deleteEntity($remotePeer);
	
		// Get view and pushing model
	
		$view = $this->getView ( $coreName, $viewType, '', array ('base_path' => $this->basePath ) );
	
		// Format response for JS client as requested
		$view->display($response);
	}
	
	/**
	 * Update the entity record if exists, update the session data for example with videocam variations
	 *
	 * @access public
	 * @return void
	 */
	public function updateEntity() {
		// Initialization
		$viewType = $this->document->getType ();
		$coreName = $this->getNames ();
	
		// Instantiate model object with Dependency Injection
		$userSessionTable = JChatHelpersUsers::getSessiontable ();
		$model = $this->getModel($coreName, 'JChatmodel', array('sessiontable'=>$userSessionTable));
	
		// Retrieve POST data
		$videoCam = $this->app->input->getInt('videocam', null);
	
		// Try to store record using model
		$response = $model->updateEntity($videoCam);
	
		// Get view
		$view = $this->getView ( $coreName, $viewType, '', array ('base_path' => $this->basePath ) );
	
		// Format response for JS client as requested
		$view->display($response);
	}
}