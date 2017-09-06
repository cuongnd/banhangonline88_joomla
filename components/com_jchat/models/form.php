<?php
//namespace components\com_jchat\models; 
/** 
 * @package JCHAT::FORM::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Group users chat model
 * 
 * @package JCHAT::FORM::components::com_jchat
 * @subpackage models
 * @since 1.0
 */ 
class JChatModelForm extends JChatModel {
	/**
	 * Main get data method
	 *
	 * @access public
	 * @return Object[]
	 */
	public function getData() {
		$data = new stdClass();
		
		// Get current guest user name
		$data->guestName = JChatHelpersUsers::generateRandomGuestNameSuffix(session_id(), $this->cparams);
		
		return $data;
	}
	
	/**
	 * Class constructor
	 * @access public
	 * @param Object& $wpdb
	 * @param Object& $userObject
	 * @return Object &
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
		
		// Component config
		$this->cparams = $this->app->getParams('com_jchat');
		$this->setState('cparams', $this->cparams);
	}
}