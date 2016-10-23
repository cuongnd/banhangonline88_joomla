<?php

/*------------------------------------------------------------------------
# com_affiliatetracker - Affiliate Tracker for Joomla
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2014 JoomlaThat.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.JoomlaThat.com
# Technical Support:	Forum - http://www.JoomlaThat.com/support
-------------------------------------------------------------------------*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

class TableAtpayment extends JTable
{
	var $id						= null;
	var $user_id				= null;
	var $created_datetime 		= null;	
	var $payment_id		 		= null;
	var $payment_type	 		= null;
	var $payment_status	 		= null;
	var $payment_amount	 		= null;
	var $payment_details 		= null;
	var $payment_datetime 		= null;
	var $payment_duedate 		= null;
	var $payment_description 	= null;
	
	var $ordering 	= null;
	
	var $_suffix = 'payment';
	
	function TableAtpayment( &$db ) {
		parent::__construct( '#__affiliate_tracker_payments', 'id', $db );
	}

	function check()
	{
        if($this->id == 0){
			$this->created_datetime =  date('Y-m-d H:i:s') ;
			
		}
		       
        return true;
	}
}