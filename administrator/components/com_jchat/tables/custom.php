<?php
// namespace administrator\components\com_jchat\tables;
/**
 *
 * @package INSTANTFBLOGIN::USERS::administrator::components::com_jchat
 * @subpackage tables
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport ( 'joomla.application.component.model' );

/**
 * Custom table on the fly for 3PD extensions user store
 *
 * @package INSTANTFBLOGIN::USERS::administrator::components::com_jchat
 * @subpackage tables
 * @since 2.1
 */
class TableCustom extends JTable {
	/**
	 *
	 * @param
	 *        	database A database connector object
	 */
	public function __construct($table, $key, &$db) {
		parent::__construct ( $table, $key, $db );
	}
}