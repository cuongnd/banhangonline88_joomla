<?php
// namespace administrator\components\com_jchat\tables;
/**
 *
 * @package JCHAT::EMOTICONS::administrator::components::com_jchat
 * @subpackage tables
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Messages table
 *
 * @package JCHAT::EMOTICONS::administrator::components::com_jchat
 * @subpackage tables
 * @since 2.7
 */
class TableEmoticons extends JTable {
	/**
	 * @var int Primary key
	 */
	public $id = null;
	/**
	 * @var string
	 */
	public $linkurl = null;
	/**
	 * @var string
	 */
	public $keycode = null;
	/**
	 * @var int
	 */
	public $ordering = null;
	/**
	 * @var int
	 */
	public $published = 1;
	
	/**
	 *
	 * @param
	 *        	database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct ( '#__jchat_emoticons', 'id', $db );
	}
}