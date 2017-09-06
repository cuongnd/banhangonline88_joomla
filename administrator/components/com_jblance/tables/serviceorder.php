<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	11 November 2014
 * @file name	:	tables/serviceorder.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Class for table (jblance)
 */
defined('_JEXEC') or die('Restricted access');

class TableServiceOrder extends JTable {
	var $id = null;
	var $user_id = null;
	var $service_id = null;
	var $price = null;
	var $duration = null;
	var $order_date = null;
	var $status = null;
	var $extras = null;
	var $p_status =  null;
	var $p_percent =  null;
	var $p_started =  null;
	var $p_updated =  null;
	var $p_ended =  null;
	
	
	/**
	* @param database A database connector object
	*/
	function __construct(&$db){
		parent::__construct( '#__jblance_service_order', 'id', $db);
	}
}
?>