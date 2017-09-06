<?php
//namespace components\com_jchat\controllers;
/**
 * @package JCHAT::EXPORT::components::com_jchat 
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Controller to export conversations
 * Doesn't call view
 *
 * @package JCHAT::EXPORT::components::com_jchat
 * @subpackage controllers
 * @since 1.0
 */
class JChatControllerExport extends JChatController {
	/**
	 * Set model state always getting fresh vars from POST request
	 * 
	 * @access protected
	 * @param string $scope
	 * @param boolean $ordering
	 * @return void
	 */
	protected function setModelState($scope = 'default', $ordering = true, $explicitModel = null) {
		// Set model state for basic stream
		$explicitModel->setState('userChatId', $this->app->input->getString('chatid', null));
		$explicitModel->setState('option', $this->option);
	}
	
	/**
	 * Display data for browser client as download attachment
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
		$this->setModelState('chatexport', false, $model);
		
		// No view required but only streammy output file
		$model->getData();
	}
	
	/**
	 * Display data for browser client as download attachment
	 * for the private messaging conversation user ID based
	 *
	 * @access public
	 * @return void
	 */
	public function displayMessaging() {
		// Initialization
		$document = JFactory::getDocument();
		$viewType = $document->getType ();
		$coreName = $this->getNames ();
	
		// Instantiate model object with Dependency Injection
		$userSessionTable = JChatHelpersUsers::getSessiontable ();
		$model = $this->getModel($coreName, 'JChatmodel', array('sessiontable'=>$userSessionTable));
	
		$userID = $this->app->input->getInt('userid', null);
		$oldestMessageID = $this->app->input->getInt('oldestmessageid');
			
		// No view required but only streammy output file
		$model->getDataMessaging($userID, $oldestMessageID);
	}
}
