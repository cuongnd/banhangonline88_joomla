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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );
require_once(JPATH_SITE.DS.'components'.DS.'com_affiliatetracker'.DS.'helpers'.DS.'helpers.php');

class PaymentsViewPayments extends JViewLegacy
{

	function display($tpl = null)
	{
		JToolBarHelper::title(   JText::_( 'PAYMENT_MANAGER' ), 'payments' );
		
		JToolBarHelper::addNew();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::deleteList(JText::_( 'SURE_PAYMENTS' ));
		//JToolBarHelper::custom( 'send_email', 'send.png', 'publish_f2.png', 'Send invoices' );
		JToolBarHelper::preferences( 'com_affiliatetracker' , '500', '700');
		
		$document	= JFactory::getDocument();
		
		// Get data from the model
		
		$pagination = $this->get('Pagination');
		$keywords = $this->get('keywords');
		$cal_start = $this->get('Calstart');
		$cal_end = $this->get('Calend');
		
		$items = $this->get('Data');
		
		$status = $this->get('Status');	
		$status_id = $this->get('StatusId');	
		$user_id = $this->get('UserId');	
	
		// push data into the template
		$this->assignRef('items', $items);	
		$this->assignRef('pagination', $pagination);
		$this->assignRef('keywords', $keywords);
		
		$this->assignRef('cal_start', $cal_start);
		$this->assignRef('cal_end', $cal_end);
		
		$this->assignRef('user_id', $user_id);
		
		//JHtmlBehavior::framework();
		//JHTML::_('behavior.modal');
		JHtml::_('bootstrap.framework');
		
		$lists['order_Dir'] = $this->get('FilterOrderDir') ;
		$lists['order']     = $this->get('FilterOrder') ;
		
		$lists['status'] = "<option value=''>-- ".JText::_( 'SELECT_STATUS' )." --</option>";
		$javascript ="";
		foreach($status as $key => $value){
			if($key == $status_id) $selected = "selected";
			else $selected = "";
			$lists['status'] .= "<option value='".$key."' $selected>".$value."</option>";
		}
		$lists['status'] = "<select  class='smallselect' name='status_id' ".$javascript.">".$lists['status']."</select>";
		
		$this->assignRef('lists', $lists);	
		
		parent::display($tpl);
	}
	
}