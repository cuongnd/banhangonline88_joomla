<?php

/*------------------------------------------------------------------------
# com_invoices - Invoices for Joomla
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2012 JoomlaFinances.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.JoomlaFinances.com
# Technical Support:	Forum - http://www.JoomlaFinances.com/forum
-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Restricted access.');

jimport( 'joomla.application.component.view');
	
class AffiliateViewPayments extends JViewLegacy
{

	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$uri	= JFactory::getURI();
		
		$pathway	= $mainframe->getPathway();
		$document	= JFactory::getDocument();
		
		$params =JComponentHelper::getParams( 'com_affiliatetracker' );
		
		$items		=  $this->get( 'Data');
		
		$pagination =  $this->get('Pagination');
		
		$this->assignRef('items',		$items);
		
		$this->assignRef('pagination', $pagination);
		
		$this->assignRef('params', $params);
		
		$filter_order     = $mainframe->getUserStateFromRequest( 'filter_order', 'filter_order', 'pa.payment_datetime', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( 'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
		
		$lists['date_in']     = $this->get( 'DateIn');
		$lists['date_out']    = $this->get( 'DateOut');
		
		$lists['account_id']    = $this->get( 'AccountId');
		$lists['type_id']    = $this->get( 'TypeId');
	
		$lists['order']     = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;
		
		$this->assignRef( 'lists',	$lists );
		
		$payments = $this->get( 'Payments');
		
		$this->assignRef( 'payments',	$payments );
		
		$timespan = $this->get( 'Timespan');
		
		$this->assignRef( 'timespan',	$timespan );
		
		$document->addStyleSheet('components/com_affiliatetracker/assets/styles.css');
		//$document->addScript('components/com_affiliatetracker/assets/conversions.js');

		parent::display($tpl);
	}
	

}
?>
