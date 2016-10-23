<?php
// namespace administrator\components\com_jchat\tables;
/**
 *
 * @package JCHAT::MESSAGES::administrator::components::com_jchat
 * @subpackage tables
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport('joomla.utilities.date');

/**
 * Messages table
 *
 * @package JCHAT::LAMESSAGES::administrator::components::com_jchat
 * @subpackage tables
 * @since 1.0
 */
class TableLamessages extends JTable {
	/**
	 *
	 * @var int Primary key
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
	public $email = null;
	/**
	 *
	 * @var string
	 */
	public $message = null;
	/**
	 *
	 * @var string
	 */
	public $sentdate = null;
	/**
	 *
	 * @var int
	 */
	public $userid = null;
	/**
	 *
	 * @var int
	 */
	public $worked = null;
	/**
	 *
	 * @var string
	 */
	public $responses = array ();
	
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
	public $closed_ticket = null;
	
	/**
	 * Loads a row from the database and binds the fields to the object properties
	 *
	 * @override
	 *
	 * @access public
	 * @param
	 *        	mixed	Optional primary key. If not specifed, the value of current key is used
	 * @return boolean if successful
	 */
	public function load($keys = null, $reset = true) {
	// If not $idEntity set return empty object
		if($keys) {
			if(!parent::load ( $keys, $reset )) {
				return false;
			}
		}
		
		// Unserialize campo risposte/data
		if ($this->responses) {
			$this->responses = unserialize ( $this->responses );
		} else {
			// Init qui
			$this->responses = array ();
		}
		
		// Foreign key management
		$usersTable = JTable::getInstance ( 'user' );
		
		// Foreign key mapping #__users
		if ($usersTable->load ( $this->userid )) {
			$this->_username_logged = $usersTable->name;
		}
		
		return true;
	}
	
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
		
		// Email required and in valid format
		if (!$this->email || !filter_var(trim($this->email), FILTER_VALIDATE_EMAIL)) {
			$this->setError ( JText::sprintf( 'COM_JCHAT_VALIDATION_ERROR_SUBJECT', $this->email ));
			return false;
		}
		
		// Email required and in valid format
		if (! $this->sentdate) {
			$this->setError ( JText::sprintf( 'COM_JCHAT_VALIDATION_ERROR_SUBJECT', $this->sentdate ));
			return false;
		}
		
		return true;
	}
	
	/**
	 * Binds a named array/hash to this object
	 *
	 * @override
	 *
	 * @access public
	 * @param
	 *        	mixed	Optional primary key. If not specifed, the value of current key is used
	 * @param boolean $storeResponse        	
	 * @return boolean if successful
	 */
	function bind($from, $ignore = array(), $storeResponse = false) {
		// Parent std load
		parent::bind ( $from, $ignore = array () );
		
		// Unserialize campo risposte/data
		if (is_array ( $this->responses ) && $storeResponse) {
			$this->responses [] = array (
					JDate::getInstance()->toSql(),
					$from ['response'],
					$from ['ticket_sender']
			);
			$this->responses = serialize ( $this->responses );
		}
		
		return true;
	}
	
	/**
	 *
	 * @param
	 *        	database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct ( '#__jchat_lamessages', 'id', $db );
	}
}