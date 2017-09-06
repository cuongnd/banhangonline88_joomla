<?php 
// namespace administrator\components\com_jchat\views\ajaxserver;

/**
 * @author Joomla! Extensions Store
 * @package JCHAT::AJAXSERVER::administrator::components::com_jchat
 * @subpackage views
 * @subpackage ajaxserver
 * @copyright (C)2014 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Config view
 *
 * @package JCHAT::AJAXSERVER::administrator::components::com_jchat
 * @subpackage views
 * @subpackage ajaxserver
 * @since 2.9
 */
class JChatViewAjaxserver extends JChatView {
	/**
	 * Return application/json response to JS client APP
	 * Replace $tpl optional param with $userData contents to inject
	 *        	
	 * @access public
	 * @param string $tpl
	 * @return void
	 */
	public function display($userData = null) {
		echo json_encode($userData);  
	}
}