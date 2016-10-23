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

jimport( 'joomla.application.component.view' );
 
class AccountsViewAccount extends JViewLegacy
{

	function display($tpl = null)
	{
		//cridem el CSS
		$document	= JFactory::getDocument();
		
		//get the account
		$account			= $this->get('Data');
		
		$params =JComponentHelper::getParams( 'com_affiliatetracker' );
		
		$isNew		= ($account->id < 1);

		$text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );
		$title = $isNew ? JText::_( 'ACCOUNT' ) : $account->account_name;
		
		JToolBarHelper::title(   $title . ': <small><small>[ ' . $text.' ]</small></small>','accounts' );
		
		JToolBarHelper::apply();
		JToolBarHelper::save();
		
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			
			JToolBarHelper::cancel( 'cancel', 'Close' );
			
		}
		
		// push data into the template
		$this->assignRef('account',		$account);
		
		// JS
		JHtmlBehavior::framework();
		$document->addScript('components/com_affiliatetracker/assets/accounts.js');

		parent::display($tpl);
	}
	
	
}