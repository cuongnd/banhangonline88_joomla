<?php
//namespace components\com_jchat\controllers;
/**
 * @package JCHAT::LAMESSAGES::components::com_jchat 
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Tickets controller class
 *
 * @package JCHAT::LAMESSAGES::components::com_jchat
 * @subpackage controllers
 * @since 1.0
 */
class JChatControllerTicket extends JChatController {
	/**
	 * Save new contact user for public conversation
	 * 
	 * @access public
	 * @return void
	 */
	public function saveEntity() {
		// Initialization
		$coreName = $this->getNames ();
		
		// Load della model e bind store
		$model = $this->getModel ('Ticket', 'JChatModel', array('name'=>'SessionStatus'));
		
		// Root controller -> dependency injection
		$mailer = JChatHelpersMailer::getInstance('Joomla');
		if (!$model->storeEntity ($mailer)) {
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