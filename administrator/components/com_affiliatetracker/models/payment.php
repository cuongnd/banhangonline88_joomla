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

jimport('joomla.application.component.model');

class PaymentsModelPayment extends JModelLegacy
{
	 
	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
		
		$this->params =JComponentHelper::getParams( 'com_affiliatetracker' );
		
	}

	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;

	}

	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT pa.*, u.username  FROM #__affiliate_tracker_payments as pa '.
					//' LEFT JOIN #__affiliate_tracker_accounts as acc ON acc.id = pa.account_id ' .
					' LEFT JOIN #__users as u ON pa.user_id = u.id ' .
					'  WHERE pa.id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
			
			
		}
		//print_r( $this->_data);die();
		if (!$this->_data) {
			$this->_data = new stdClass();

			$this->_data->user_id = 0;
			$this->_data->username = "";
			
			$this->_data->payment_amount = 0;
			
			$this->_data->account_id = 0;
			
			$user_id = JRequest::getInt('fromuser');
			if($user_id){
				$query = ' SELECT SUM(pa.payment_amount) AS total_paid FROM #__affiliate_tracker_payments AS pa WHERE pa.user_id = '.$user_id.' AND pa.payment_status = 1 ' ;
				$this->_db->setQuery($query);
				$total_paid = $this->_db->loadResult();

				$query = ' SELECT SUM(at.comission) AS total_earned FROM #__affiliate_tracker_conversions AS at '
						.' LEFT JOIN #__affiliate_tracker_accounts as acc ON acc.id = at.atid '
						.' WHERE acc.user_id = '.$user_id.' AND at.approved = 1 ' ;
				$this->_db->setQuery($query);
				$total_earned = $this->_db->loadResult();

				$this->_data->payment_amount = $total_earned - $total_paid;

				$this->_data->user_id = $user_id;

				$query = ' SELECT u.username  FROM  #__users as u ' .
						'  WHERE u.id = '.$user_id;
				$this->_db->setQuery( $query );
				$this->_data->username = $this->_db->loadResult();
			}
			
			$this->_data->created_datetime = date("Y-m-d H:i:s");
			$this->_data->payment_duedate = "";
			$this->_data->payment_datetime = "";
			$this->_data->payment_id = "";
			$this->_data->payment_type = "";
			$this->_data->payment_details = "";
			$this->_data->payment_description = "";

			$this->_data->payment_status = 0;
			
			$this->_data->id = 0;
			
			//print_r($this->_data);die;
		}
		
		return $this->_data;
	}

	function store($payment)
	{	
		$row = $this->getTable('atpayment');

		if(empty($payment)) {
			$data = JRequest::get( 'post' );
		} else {
			$data = $payment;
		}
		
		// Bind the form fields to the album table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->store()) {
			
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		
		return $row->id;
	}
	

	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$row = $this->getTable('atpayment');

		if (count( $cids )) {
			foreach($cids as $cid) {
				
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;
	}
	
	function publish()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'default', 'array' );

		if (count( $cids )) {
			foreach($cids as $cid) {
				$query = ' UPDATE #__affiliate_tracker_payments SET payment_status = 1 WHERE id = '. $cid . ' LIMIT 1 ';
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}
		return true;
	}
	
	function unpublish()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'default', 'array' );

		if (count( $cids )) {
			foreach($cids as $cid) {
				$query = ' UPDATE #__affiliate_tracker_payments SET payment_status = 0 WHERE id = '. $cid . ' LIMIT 1 ';
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}
		return true;
	}

}