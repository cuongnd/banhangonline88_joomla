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

class LogsViewLogs extends JViewLegacy
{

	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'LOG_MANAGER' ), 'logs' );
		
		//JToolBarHelper::addNewX();
		//JToolBarHelper::publishList();
		//JToolBarHelper::unpublishList();
		JToolBarHelper::deleteList(JText::_( 'SURE_LOGS' ));
		//JToolBarHelper::customX( 'send_email', 'send.png', 'publish_f2.png', 'SEND_INVOICES' );
		JToolBarHelper::preferences( 'com_affiliatetracker' , '500', '700');
		
		$document	= JFactory::getDocument();
		
		// Get data from the model
		
		$pagination = $this->get('Pagination');
		$keywords = $this->get('keywords');
		$cal_start = $this->get('Calstart');
		$cal_end = $this->get('Calend');

		$user_id = $this->get('UserId');	
		$account_id = $this->get('AccountId');	
		
		$items = $this->get('Data');
		
		// push data into the template
		$this->assignRef('items', $items);	
		$this->assignRef('pagination', $pagination);
		$this->assignRef('keywords', $keywords);
		
		$this->assignRef('cal_start', $cal_start);
		$this->assignRef('cal_end', $cal_end);

		$this->assignRef('user_id', $user_id);
		$this->assignRef('account_id', $account_id);
	
		$lists['order_Dir'] = $this->get('FilterOrderDir') ;
		$lists['order']     = $this->get('FilterOrder') ;
		
		$this->assignRef('lists', $lists);	
		
		parent::display($tpl);
	}
	
}