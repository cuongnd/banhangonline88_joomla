<?php
//namespace components\com_jchat\controllers;
/**
 * @package JCHAT::ATTACHMENTS::components::com_jchat 
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Attachment files controller
 * Manage all actions, from show to upload/download 
 *
 * @package JCHAT::ATTACHMENTS::components::com_jchat
 * @subpackage controllers
 * @since 1.0
 */
class JChatControllerAttachments extends JChatController {
	/**
	 * Set model state always getting fresh vars from POST request
	 * 
	 * @access protected
	 * @param string $scope
	 * @param boolean $ordering
	 * @return void
	 */
	protected function setModelState($scope = 'default', $ordering = true) {
		// Get default model
		$defaultModel = $this->getModel();
		
		// Set model state for basic stream
		$defaultModel->setState('from', $this->app->input->getString('from', session_id()));
		$defaultModel->setState('to', $this->app->input->getString('to', -1));
		$defaultModel->setState('tologged', $this->app->input->getInt('tologged'));
		$defaultModel->setState('option', $this->option);
		$defaultModel->setState('idMessage', $this->app->input->getInt('idMessage', 0));
		
		return $defaultModel;
	}
	
	/**
	 * Display only form for users
	 * 
	 * @access public
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false) {
		// Populate model state and get model
		$model = $this->setModelState('chatattachments', false);
		
		// Get view and pushing model
		$view = $this->getView ();
		$view->setModel($model, true);
		
		// Show always form view
		parent::display();
	}
	
	/**
	 * Save uploaded file to cache folder to let it stream to target user
	 *
	 * @access public
	 * @return void
	 */
	public function saveEntity() {
		// Populate model state and get model
		$model = $this->setModelState('chatattachments', false);
	
		// Upload file
		$model->storeEntity();
		
		// Get view and pushing model
		$view = $this->getView ();
		$view->setModel($model, true);
		
		// Show always form view
		parent::display();
	}
	
	/**
	 * Download uploaded file sent by other user, view not needed in this case
	 *
	 * @access public
	 * @return void
	 */
	public function showEntity() {
		// Populate model state and get model
		$model = $this->setModelState('chatattachments', false);
	
		// Upload file
		$model->loadEntity();
	}
}
