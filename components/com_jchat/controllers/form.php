<?php
//namespace components\com_jchat\controllers;
/**
 * @package JCHAT::FORM::components::com_jchat 
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
 * @package JCHAT::FORM::components::com_jchat
 * @subpackage controllers
 * @since 1.0
 */
class JChatControllerForm extends JChatController {
	/**
	 * Display the chat form
	 * @access public
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false) {
		// Check if the user has access to the chat app based on access level parameter
		if (! $this->allowDisplay()) {
			$this->app->enqueueMessage ( JText::_ ( 'COM_JCHAT_NOACCESS' ) );
			return;
		}
		
		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$coreName = $this->getNames();
		$viewLayout = $this->app->input->get ( 'layout', 'default' );
	
		$view = $this->getView($coreName, $viewType, '', array('base_path' => $this->basePath));
	
		// Get/Create the model
		if ($model = $this->getModel($coreName, 'JChatModel')) {
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}
	
		// Set the layout
		$view->setLayout($viewLayout);
	
		$view->display();
	}
	
	/**
	 * Save new contact user for public conversation
	 * 
	 * @access public
	 * @return void
	 */
	public function saveEntity() {
		// Initialization
		$document = JFactory::getDocument();
		$coreName = $this->getNames ();
		
		// Manage async posted data
		$postData = $this->app->input->post->getString('data');
		if(ini_get('magic_quotes_gpc') == 1) {
			$postData = stripslashes($postData);
		}
		
		if($postData) {
			$data = json_decode($postData);
			if(!empty($data->formFields)) {
				foreach ($data->formFields as $postVar) {
					// Exclude not needed ajax posted vars
					if(in_array($postVar->name, array('option', 'task', 'format'))) {
						continue;
					}
					// Set vars both in raw and JInput object 'post' instance
					$this->requestArray[$this->requestName][$postVar->name] = $postVar->value;
					$this->app->input->post->set($postVar->name, $postVar->value);
				}
			}
			// Force primary key as current session id
			$this->requestArray[$this->requestName]['sessionid'] = session_id();
		}
		
		// Load della model e bind store
		$model = $this->getModel ('Form', 'JChatModel', array('name'=>'SessionStatus'));
		
		if (!$model->storeEntity ()) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError ( null, false );
			$response['storing'] = array('status'=>false, 'details'=>$modelException->getMessage());
		} else {
			$response['storing'] = array('status'=>true);
		}
		
		// Get view and pushing model
		$view = $this->getView ( $coreName, 'json', '', array ('base_path' => $this->basePath ) );
		
		// Format response for JS client as requested
		$view->display($response);
	}
}