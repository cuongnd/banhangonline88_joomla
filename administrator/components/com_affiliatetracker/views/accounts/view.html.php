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
require_once(JPATH_SITE.DS.'components'.DS.'com_affiliatetracker'.DS.'helpers'.DS.'helpers.php');

class AccountsViewAccounts extends JViewLegacy
{

	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'ACCOUNT_MANAGER' ), 'accounts' );

		JToolBarHelper::addNew();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::deleteList(JText::_( 'SURE_ACCOUNTS' ));
		//JToolBarHelper::customX( 'send_email', 'send.png', 'publish_f2.png', 'SEND_INVOICES' );
		JToolBarHelper::preferences( 'com_affiliatetracker' , '500', '1100');
		
		$document	= JFactory::getDocument();
		
		// Get data from the model
		
		$pagination = $this->get('Pagination');
		$keywords = $this->get('keywords');
		
		$items = $this->get('Data');

		$status = $this->get('Status');	
		$status_id = $this->get('StatusId');	

		$user_id = $this->get('UserId');	
		
		// push data into the template
		$this->assignRef('items', $items);	
		$this->assignRef('pagination', $pagination);
		$this->assignRef('keywords', $keywords);

		$this->assignRef('user_id', $user_id);
	
		$lists['order_Dir'] = $this->get('FilterOrderDir') ;
		$lists['order']     = $this->get('FilterOrder') ;

		$javascript = "";
		
		$lists['status'] = "<option value=''>-- ".JText::_( 'SELECT_STATUS' )." --</option>";
		//print_r($artists);
		for($i = 0; $i < count($status); $i++){
			if($i == $status_id && $status_id != "") $selected = "selected";
			else $selected = "";
			$lists['status'] .= "<option value='".$i."' $selected>".JText::_($status[$i])."</option>";
		}
		$lists['status'] = "<select name='status_id' id='status_id' ".$javascript.">".$lists['status']."</select>";
		
		$this->assignRef('lists', $lists);	
		
		parent::display($tpl);
	}
	
}