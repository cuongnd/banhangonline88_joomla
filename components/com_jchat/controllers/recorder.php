<?php
//namespace components\com_jchat\controllers;
/**
 * @package JCHAT::RECORDER::components::com_jchat 
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @Copyright (C) 2016 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Recording media upload manager
 *
 * @package JCHAT::ATTACHMENTS::components::com_jchat
 * @subpackage controllers
 * @since 2.9
 */
class JChatControllerRecorder extends JChatController {
	/**
	 * Set model state always getting fresh vars from POST/FILES request
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
		$defaultModel->setState('peer1', $this->app->input->getString('peer1'));
		$defaultModel->setState('peer2', $this->app->input->getString('peer2'));
		$defaultModel->setState('timerecord', $this->app->input->getString('timerecord'));
		$defaultModel->setState('blobfile', $this->app->input->files->get('blob'));
		$defaultModel->setState('blobfilename', $this->app->input->getString('filename'));
		$defaultModel->setState('blobfilesize', $this->app->input->getString('filesize'));
		
		return $defaultModel;
	}
	
	/**
	 * Save uploaded media files to the media folder for jchat
	 *
	 * @access public
	 * @return void
	 */
	public function saveEntity() {
		// Populate model state and get model
		$model = $this->setModelState('chatrecordings', false);
	
		// Upload file
		$result = $model->storeEntity();
		
		// Get view and pushing model
		$coreName = $this->getNames ();
		$view = $this->getView ($coreName, 'json', '');
		$view->display($result);
	}
}
