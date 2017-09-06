<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	05 November 2014
 * @file name	:	tables/service.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Class for table (jblance)
 */
defined('_JEXEC') or die( 'Restricted access' );

class TableService extends JTable {
	var $id = null;
	var $service_title = null;
	var $id_category = null;
	var $user_id = null;
	var $description = null;
	var $price = null;
	var $duration = null;
	var $instruction = null;
	var $extras = null;
	var $approved = 1;
	var $attachment = null;
	var $create_date = null;
	var $modify_date = null;
	var $publish_up = null;
	var $publish_down = null;
	var $disapprove_reason = null;
	var $hits = null;
	var $available = null;
	
	/**
	* @param database A database connector object
	*/
	function __construct(&$db){
		parent::__construct( '#__jblance_service', 'id', $db);
	}
}
?>