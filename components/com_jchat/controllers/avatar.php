<?php
//namespace components\com_jchat\controllers;
/**
 * @package JCHAT::AVATAR::components::com_jchat 
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Avatars files controller
 * Manage all actions, from upload to delete
 *
 * @package JCHAT::AVATAR::components::com_jchat
 * @subpackage controllers
 * @since 1.0
 */
class JChatControllerAvatar extends JChatController {
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
		$defaultModel->setState('option', $this->option);
		
		return $defaultModel;
	}
	
	/**
	 * Display data for JS client on stream read
	 * 
	 * @access public
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false) {
		// Populate model state and get model
		$model = $this->setModelState('chatavatar', false);
		
		// Get view and pushing model
		$view = $this->getView ();
		$view->setModel($model, true);
		
		// Show always form view
		parent::display();
	}
	
	/**
	 * Save data from JS clilent on stream write
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
	 * Save data from JS clilent on stream write
	 *
	 * @access public
	 * @return void
	 */
	public function deleteEntity() {
		// Populate model state and get model
		$model = $this->setModelState('chatattachments', false);
	
		// Upload file
		$model->deleteEntity();
		
		// Get view and pushing model
		$view = $this->getView ();
		$view->setModel($model, true);
		
		// Show always form view
		parent::display();
	}
}
