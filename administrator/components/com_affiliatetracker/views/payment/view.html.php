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
 
class PaymentsViewPayment extends JViewLegacy
{

	function display($tpl = null)
	{
		//cridem el CSS
		$document	= JFactory::getDocument();
		
		//get the invoice
		$payment			= $this->get('Data');
		
		$isNew		= ($payment->id < 1);

		$text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );
		$title = $isNew ? JText::_( 'PAYMENT' ) : JText::_( 'PAYMENT' ). " ". $payment->id;
		
		JToolBarHelper::title(   $title . ': <small><small>[ ' . $text.' ]</small></small>','payments' );
		JToolBarHelper::apply();
		
		JToolBarHelper::save();
		
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			
			JToolBarHelper::cancel( 'cancel', JText::_('CLOSE') );
			
		}
		
		// push data into the template
		$this->assignRef('payment',		$payment);
		
		JHtmlBehavior::framework();
		// JS
		$layout = JRequest::getVar('layout');
		if($layout != "form_payment") $document->addScript('components/com_affiliatetracker/assets/accounts.js');

		parent::display($tpl);
	}
	
	
}