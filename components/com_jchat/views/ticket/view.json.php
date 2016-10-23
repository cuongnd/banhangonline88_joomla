<?php 
// namespace administrator\components\com_jchat\views\ticket;

/**
 * @author Joomla! Extensions Store
 * @package JCHAT::LAMESSAGES::administrator::components::com_jchat
 * @subpackage views
 * @subpackage ticket
 * @Copyright (C) 2015 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * View for JS client
 *
 * @package JCHAT::LAMESSAGES::administrator::components::com_jchat
 * @subpackage views
 * @subpackage ticket
 * @since 1.0
 */
class JChatViewTicket extends JChatView {
	/**
	 * Return application/json response to JS client APP
	 * Replace $tpl optional param with $userData contents to inject
	 *        	
	 * @access public
	 * @param string $userData
	 * @return void
	 */
	public function display($userData = null) {
		echo json_encode($userData);  
	}
}