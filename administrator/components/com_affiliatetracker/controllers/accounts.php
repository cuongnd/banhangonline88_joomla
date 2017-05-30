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

class AccountsControllerAccounts extends AccountsController
{
	
	function __construct()
	{
		JRequest::setVar('view', 'accounts');
		parent::__construct();

	}
	
	function is_refWord_available() {
		$app = JFactory::getApplication();
		$searchword = JRequest::getVar("searchword");
		$aId = JRequest::getInt("aId");
		$db = JFactory::getDBO();

		$searchword = $db->escape($searchword);

		if (!empty($searchword)) {
			$query = ' SELECT acc.id FROM #__affiliate_tracker_accounts as acc WHERE acc.ref_word = "' . $searchword . '" AND acc.id <> ' . $aId;
			$db->setQuery($query);
			$accId = $db->loadResult();

			if ($accId) echo "0";
			else echo "1";
		} else {
			echo "1";
		}

		$app->close();
	}
  
}
