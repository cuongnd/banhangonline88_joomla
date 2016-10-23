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

class TableLog extends JTable
{

	var $id = null;
	var $datetime = null;
	var $sessionid = null;
	var $atid = null;
	var $account_id = null;
	var $refer = null;
	var $ip = null;
	var $user_id = null;

	function TableLog(& $db) {
		parent::__construct('#__affiliate_tracker_logs', 'id', $db);
	}
	
	function check(){
		
		$user = JFactory::getUser();
		
		$this->ip = $_SERVER['REMOTE_ADDR'] ;
		$this->refer = $_SERVER['HTTP_REFERER'] ;
		$this->user_id = $user->id ;
		
		$this->account_id = $this->atid ;
		
		$session = JFactory::getSession();
        $this->sessionid = $session->getId();
		
		$this->datetime = date('Y-m-d H:i:s') ;
		
		return true;
	}
	  
}