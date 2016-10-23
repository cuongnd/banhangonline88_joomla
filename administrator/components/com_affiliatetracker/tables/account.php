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

class TableAccount extends JTable
{

	var $id = null;

	var $account_name = null;
	var $publish = null;
	var $params = null;
	var $user_id = null;
	var $comission = null;
	var $type = null;
	var $payment_options = null;

	var $name = null;
	var $company = null;
	var $address = null;
	var $zipcode = null;
	var $city = null;
	var $state = null;
	var $country = null;
	var $phone = null;
	var $email = null;
	var $ref_word = null;
	var $variable_comissions = null;
	var $refer_url = null;
	var $parent_id = null;
	
	function TableAccount(& $db) {
		parent::__construct('#__affiliate_tracker_accounts', 'id', $db);
	}
	
	function check(){
		if($this->id == 0){
			$app = JFactory::getApplication();

			$params =JComponentHelper::getParams( 'com_affiliatetracker' );

			if($app->isSite()){
				if (empty($this->user_id)) {
					$user = JFactory::getUser();
					$this->user_id = $user->id ;
				}

				$this->type = $params->get('default_type') ;
				$this->comission = $params->get('default_amount') ;
			}

		}
		
		return true;
	}
	  
}