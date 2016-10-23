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
 * @since 1.0
 */
class JChatControllerWebrtc extends JChatController {
	/**
	 * Save new contact user for public conversation
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
		
		// Try to store record using model
		$response = $model->storeEntity($otherPeer, $sdp, $iceCandidate, $videoCam, $isCaller);
		
		// Get view
		$view = $this->getView ( $coreName, $viewType, '', array ('base_path' => $this->basePath ) );
		
		// Format response for JS client as requested
		$view->display($response);
	}
	
	/**
	 * Remove existing contact user from public conversation
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
	 * Update the entity record if exists
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