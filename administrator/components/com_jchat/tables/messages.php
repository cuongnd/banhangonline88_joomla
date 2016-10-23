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

/**
 * Messages table
 *
 * @package JCHAT::MESSAGES::administrator::components::com_jchat
 * @subpackage tables
 * @since 1.0
 */
class TableMessages extends JTable {
	/**
	 * @var int Primary key
	 */
	public $id = null;
	/**
	 * @var string
	 */
	public $from = null;
	/**
	 * @var string
	 */
	public $to = null;
	/**
	 * @var string
	 */
	public $message = null;
	/**
	 * @var string
	 */
	public $sent = null;
	/**
	 * @var int
	 */
	public $read = null;
	/**
	 * @var string
	 */
	public $type = null;
	/**
	 * @var int
	 */
	public $status = null;
	/**
	 * @var int
	 */
	public $clientdeleted = null;
	/**
	 * @var string
	 */
	public $actualfrom = null;
	/**
	 * @var string
	 */
	public $actualto = null;
	/**
	 * @var string
	 */
	public $ipaddress = null;
	
	/**
	 * Delete Table override
	 * @override
	 *
	 * @see JTable::delete()
	 */
	public function delete($pk = null) {
		$messageDeleted = parent::delete($pk);
	
		if($messageDeleted) {
			// Delete reference table messages by foreign key
			$query = $this->_db->getQuery(true)->delete('#__jchat_public_readmessages');
			$query->where('messageid = ' . (int)$pk);
			$this->_db->setQuery($query);
			// Check for a database error.
			$this->_db->execute();
			if ($this->_db->getErrorNum()) {
				$messageDeleted = false;
			}
		}
	
		return $messageDeleted;
	}
	
	/**
	 *
	 * @param
	 *        	database A database connector object
	 */
	function __construct(&$db) {
		parent::__construct ( '#__jchat', 'id', $db );
	}
}