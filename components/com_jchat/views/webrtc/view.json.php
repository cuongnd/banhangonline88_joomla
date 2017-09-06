<?php 
// namespace administrator\components\com_jchat\views\webrtc;

/**
 * @author Joomla! Extensions Store
 * @package JCHAT::WEBRTC::administrator::components::com_jchat
 * @subpackage views
 * @subpackage groupchat
 * @Copyright (C) 2015 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * View for JS client
 *
 * @package JCHAT::WEBRTC::administrator::components::com_jchat
 * @subpackage views
 * @subpackage groupchat
 * @since 1.0
 */
class JChatViewWebrtc extends JChatView {
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