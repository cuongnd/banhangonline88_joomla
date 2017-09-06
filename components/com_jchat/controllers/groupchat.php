<?php
//namespace components\com_jchat\controllers;
/**
 * @package JCHAT::GROUPCHAT::components::com_jchat 
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
 * @package JCHAT::GROUPCHAT::components::com_jchat
 * @subpackage controllers
 * @since 1.0
 */
class JChatControllerGroupchat extends JChatController {
	/**
	 * Save new contact user for public conversation
	 * 
	 * @access public
	 * @return void
	 */
	public function saveEntity() {
		// Initialization
		$document = JFactory::getDocument();
		$viewType = $document->getType ();
		$coreName = $this->getNames ();
		
		// Instantiate model object with Dependency Injection
		$userSessionTable = JChatHelpersUsers::getSessiontable ();
		$model = $this->getModel($coreName, 'JChatmodel', array('sessiontable'=>$userSessionTable));
		
		$contactID = $this->app->input->getString('id', null);
		
		// Try to load record from model
		$response = $model->storeEntity($contactID);
		
		// Get view and pushing model
		
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
		$document = JFactory::getDocument();
		$viewType = $document->getType ();
		$coreName = $this->getNames ();
		
		// Instantiate model object with Dependency Injection
		$userSessionTable = JChatHelpersUsers::getSessiontable ();
		$model = $this->getModel($coreName, 'JChatmodel', array('sessiontable'=>$userSessionTable));
	
		$contactID = $this->app->input->getString('id', null);
	
		// Try to load record from model
		$response = $model->deleteEntity($contactID);
	
		// Get view and pushing model
	
		$view = $this->getView ( $coreName, $viewType, '', array ('base_path' => $this->basePath ) );
	
		// Format response for JS client as requested
		$view->display($response);
	}
}