<?php
// namespace administrator\components\com_jchat\tables;
/**
 *
 * @package JCHAT::RECORDER::components::com_jchat
 * @subpackage tables
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * ORM Table for video recordings
 *
 * @package JCHAT::RECORDER::components::com_jchat
 * @subpackage tables
 * @since 2.9
 */
class TableRecorder extends JTable {
	/**
	 *
	 * @var int
	 */
	public $id = null;
	
	/**
	 *
	 * @var string
	 */
	public $title = null;
	
	/**
	 *
	 * @var string
	 */
	public $size = null;
	
	/**
	 *
	 * @var datetime
	 */
	public $timerecord = '0000-00-00 00:00:00';
	
	/**
	 *
	 * @var string
	 */
	public $peer1 = null;
	
	/**
	 *
	 * @var string
	 */
	public $peer2 = null;
	
	
	/**
	 * Class constructor
	 *
	 * @param Object& $_db
	 *        	return Object&
	 */
	public function __construct($_db) {
		parent::__construct ( '#__jchat_recordings', 'id', $_db );
	}
}