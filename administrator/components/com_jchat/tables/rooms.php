<?php
// namespace administrator\components\com_jchat\tables;
/**
 *
 * @package JCHAT::FORM::components::com_jchat
 * @subpackage tables
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * ORM Table for rooms entities
 *
 * @package JCHAT::FORM::components::com_jchat
 * @subpackage tables
 * @since 1.0
 */
class TableRooms extends JTable {
	/**
	 *
	 * @var int
	 */
	public $id = null;
	
	/**
	 *
	 * @var string
	 */
	public $name = null;
	
	/**
	 *
	 * @var string
	 */
	public $description = null;
	
	/**
	 *
	 * @var int
	 */
	public $checked_out = 0;
	
	/**
	 *
	 * @var datetime
	 */
	public $checked_out_time = '0000-00-00 00:00:00';
	
	/**
	 *
	 * @var int
	 */
	public $published = 1;
	
	/**
	 *
	 * @var int
	 */
	public $ordering = null;
	
	/**
	 *
	 * @var int
	 */
	public $access = null;
	
	/**
	 * Check Table override
	 * @override
	 *
	 * @see JTable::check()
	 */
	public function check() {
		// Name required
		if (! $this->name) {
			$this->setError ( JText::_ ( 'COM_JCHAT_VALIDATION_ERROR' ) );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Class constructor
	 *
	 * @param Object& $_db
	 *        	return Object&
	 */
	public function __construct($_db) {
		parent::__construct ( '#__jchat_rooms', 'id', $_db );
	}
}