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

//no direct access
defined('_JEXEC') or die('Restricted access.');



jimport('joomla.filesystem.file');

class TableConversion extends JTable
{

	var $id = null;

	var $atid = null;
	var $user_id = null;
	var $name = null;
	var $date_created = null;
	var $extended_name = null;
	var $value = null;
	var $comission = null;
	var $approved = null;
	var $type = null;
	var $reference_id = null;
	var $notes = null;
	var $params = null;
	var $component = null;
	
	function TableConversion(& $db) {
		parent::__construct('#__affiliate_tracker_conversions', 'id', $db);
	}
	
	function check(){
		if($this->id == 0){
			if(!$this->date_created) $this->date_created =  date('Y-m-d H:i:s') ;
			
			$user = JFactory::getUser();
			
			if(!$this->user_id) $this->user_id = $user->id;
			//$this->created_by = $user->id;
			
		}
		
		return true;
	}
	  
}