<?php
// namespace administrator\components\com_jchat\tables;
/**
 *
 * @package JCHAT::administrator::components::com_jchat
 * @subpackage tables
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Login social users table
 *
 * @package JCHAT::administrator::components::com_jchat
 * @subpackage tables
 * @since 2.1
 */
class TableLogin extends JTable {
	/**
	 * @var int Primary key
	 */
	var $id = null;
	
	/**
	 * @var string
	 */
	var $j_uid = null;
	
	/**
	 * @var string
	 */
	var $fb_uid = null;
	
	/**
	 * @var string
	 */
	var $email = null;
	
	/**
	 * @var string
	 */
	var $picture = null;
	
	/**
	 * @var string
	 */
	var $first_name = null;
	
	/**
	 * @var string
	 */
	var $last_name = null;
	
	/**
	 * @var int
	 */
	var $name = null;
	
	/**
	 *
	 * @param
	 *        	database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct ( '#__jchat_login', 'id', $db );
	}
}