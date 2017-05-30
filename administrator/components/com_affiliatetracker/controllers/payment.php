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

class PaymentsControllerPayment extends PaymentsController
{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'apply',	'save' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'payment' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		$model = $this->getModel('payment');

		if ($id = $model->store($post)) {
			$msg = JText::_( 'PAYMENT_SAVED' );
		} else {
			$msg = JText::_( 'ERROR_SAVING_PAYMENT' );
		}

		$task = JRequest::getCmd( 'task' );
		
		$from = JRequest::getCmd( 'from' );
		
		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_affiliatetracker&controller=payment&task=edit&cid[]='. $id ;
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_affiliatetracker&controller=payments';
				if($from == "invoices") $link = 'index.php?option=com_affiliatetracker&controller=invoices';
				break;
		}
		
		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		$model = $this->getModel('payment');
		
		$frominvoice = JRequest::getInt( 'frominvoice' );
		
		if(!$model->delete()) {
			$msg = JText::_( 'ERROR_DELETING_PAYMENT' );
		} else {
			$msg = JText::_( 'PAYMENT_DELETED' );
		}

		$link = 'index.php?option=com_affiliatetracker&controller=payments';
		if($frominvoice) $link = 'index.php?option=com_affiliatetracker&controller=invoice&task=edit&cid[]='.$frominvoice;
		
		$this->setRedirect( $link, $msg );
	}

	function cancel()
	{
		$msg = JText::_( 'OPERATION_CANCELLED' );
		$from = JRequest::getCmd( 'from' );
		
		$link = 'index.php?option=com_affiliatetracker&controller=payments';
		
		if($from == "invoices") $link = 'index.php?option=com_affiliatetracker&controller=invoices';
		
		$this->setRedirect( $link, $msg );
	}
	
	function publish()
	{
		$model = $this->getModel('payment');
		if(!$model->publish()) {
			$msg = JText::_( 'ERROR_MARKING_PAYMENT_PAYED' );
		} else {
			$msg = JText::_( 'PAYMENT_PAYED' );
		}

		$this->setRedirect( 'index.php?option=com_affiliatetracker&controller=payments', $msg );
	}
	
	function unpublish()
	{
		$model = $this->getModel('payment');
		if(!$model->unpublish()) {
			$msg = JText::_( 'ERROR_MARKING_PAYMENT_UNPAYED' );
		} else {
			$msg = JText::_( 'PAYMENT_NOT_PAYED' );
		}

		$this->setRedirect( 'index.php?option=com_affiliatetracker&controller=payments', $msg );
	}

	function process_payment(){
		
		$ptype 		= JRequest::getVar( 'ptype' );
		$payment_id = JRequest::getInt( 'item_number' );
		$paction 	= JRequest::getVar( 'paction' );

		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		
		$query = ' SELECT pa.* FROM #__affiliate_tracker_payments AS pa WHERE pa.id = '.$payment_id;
		$db->setQuery($query);
		$payment = $db->loadObject();
		
		$import = JPluginHelper::importPlugin( strtolower( 'Affiliates' ), $ptype );
		
		$dispatcher = JDispatcher::getInstance();
		$results = $dispatcher->trigger( 'onProcessPayment', array( $payment, $user ) );
		
		//print_r($results);die;
		
		$params =JComponentHelper::getParams( 'com_affiliatetracker' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;

		$link = JRoute::_('index.php?option=com_affiliatetracker&controller=payment&task=edit&cid[]=' . $payment_id . $itemid, false) ;
		
		switch ($paction) {
			case "display_message":	
			
				$query = ' SELECT pa.* FROM #__affiliate_tracker_payments AS pa WHERE pa.id = '.$payment_id;
				$db->setQuery($query);
				$payment = $db->loadObject();
				
				switch ($payment->payment_status) {
					case 1:	
						$text = JText::_('PAYMENT_COMPLETED');			
					break;
					case 2:	
						$text = JText::_('PAYMENT_PENDING_VALIDATION');			
					break;
					case 0:	
						$text = JText::_('PAYMENT_NOT_COMPLETED');			
					break;
				}
						
			  break;
			case "process":

				$query = ' SELECT pa.* FROM #__affiliate_tracker_payments AS pa WHERE pa.id = '.$payment_id;
				$db->setQuery($query);
				$payment = $db->loadObject();
				print_r($payment);
				//we send the emails

				switch ($payment->payment_status) {
					case 1:
						$this->send_email_payment($payment_id);
						break;
					case 2:

						break;
					case 0:

						break;
				}

				$link = JRoute::_('index.php?option=com_affiliatetracker&controller=payment&task=edit&cid[]=' . $payment_id . $itemid, false) ;

			  break;
			case "cancel":
				$text = JText::_( 'PAYMENT_PROCESS_CANCELLED' );
				
			  break;
			default:
				$text = JText::_( 'INVALID_ACTION' );
				
			  break;
		}
		
		$this->setRedirect($link, $text);
					
	}

	function send_email_payment($payment_id = false)
	{
		//TODO: Implement send email notification

		if(!$payment_id) $payment_id = JRequest::getInt('id');

		$app = JFactory::getApplication();

		$db = JFactory::getDBO();

		$params =JComponentHelper::getParams( 'com_affiliatetracker' );

		//$app->enqueueMessage("");
	}
}