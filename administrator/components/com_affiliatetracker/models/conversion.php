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

jimport('joomla.application.component.model');

class ConversionsModelConversion extends JModelLegacy
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
		//if (empty( $this->_data )) {
		$query = ' SELECT at.*, acc.account_name, u.username AS actor_username, u.name AS actor_name, u.email AS actor_email, acc.user_id AS owner_id, acc.email, acc.name as owner_name, u2.username FROM #__affiliate_tracker_conversions as at '.
				 ' LEFT JOIN #__affiliate_tracker_accounts AS acc ON acc.id = at.atid '.
				 ' LEFT JOIN #__users as u ON u.id = at.user_id '.
				 ' LEFT JOIN #__users as u2 ON u2.id = acc.user_id '.
				 ' WHERE at.id = '.$this->_id;
		$this->_db->setQuery( $query );
		$this->_data = $this->_db->loadObject();
			
		//}
		//print_r( $this->_data);die();
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;
			
			$this->_data->account_name = "";
			$this->_data->params = "";
			$this->_data->publish = 1;
		
			$this->_data->user_id = 0;
			$this->_data->username = "";

			$this->_data->name = "";
			$this->_data->extended_name = "";
			$this->_data->approved = 0;
			$this->_data->value = "";
			$this->_data->comission = "";
			$this->_data->type = "";
			$this->_data->component = "";
			$this->_data->reference_id = "";
			$this->_data->date_created = date('Y-m-d');

			$this->_data->atid = 0;
			
			
		}
		
		return $this->_data;
	}

	
	function getStatus(){

		if (empty( $this->status )){
			$this->status = AffiliateHelper::getStatus();
			
		}
		//print_r($this->songs);die();
		return $this->status;
	
	}
	
	function store($data = false)
	{	
		$mainframe = JFactory::getApplication();
		$row = $this->getTable();

		if(!$data) $data = JRequest::get( 'post' );
		
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
			//print_r($row);die();
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		//new plugin access
		$dispatcher	= JDispatcher::getInstance();
		$plugin_ok = JPluginHelper::importPlugin('affiliates');
		$results = $dispatcher->trigger('onSubmitConversion', array ($row->id, $data, $data['id']));
		
		if(!$data['id']){
			//it's new. We send the emails
			$this->sendMail($row->id);
		}		
			
		return $row->id;
	}

	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$row = $this->getTable();

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
				$query = ' UPDATE #__affiliate_tracker_conversions SET approved = 1 WHERE id = '. $cid . ' LIMIT 1 ';
				$this->_db->setQuery($query);
				$this->_db->query();

				$this->sendMail($cid);
			}
		}
		return true;
	}
	
	function unpublish()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'default', 'array' );

		if (count( $cids )) {
			foreach($cids as $cid) {
				$query = ' UPDATE #__affiliate_tracker_conversions SET approved = 0 WHERE id = '. $cid . ' LIMIT 1 ';
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}
		return true;
	}

	function sendMail($id){
		$this->setId($id) ;
		$data = $this->getData();
		//print_r($data);die;
		$params =JComponentHelper::getParams( 'com_affiliatetracker' );
		
		$from = $params->get('email_email') ;
		$from_name = $params->get('email_name') ;
		$subject = JText::_('NEW_CONVERSION_APPROVED') ;

		$view		= $this->getConversionView();
		
		$view->conversion		= $data;
		$view->params		= $params;
		
		$view->_path['template'][1] = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_affiliatetracker'.DS.'views'.DS.'conversion'.DS.'tmpl' ;
		
		$plantilla = 'email' ;
		
		$mail = JFactory::getMailer();
		$mail->setsender(array($from, $from_name));
		$mail->setSubject($subject);
		$mail->isHTML(true);

		$success = false;
		$success2 = false;

		if($params->get('email_conversion_admin')){
			//email to the admin
			$view->conversion->towho		= "admin";
			$message = $view->loadTemplate( $plantilla );
			$to = $from;

			if(!empty($to)) {
				$mail->addRecipient($to);
				$mail->setbody($message);

				$success = $mail->Send();
				//$success = $mail->sendMail($from, $from_name, $to, $subject, $message, true ); // true is for HTML
			}

		}
		if($params->get('email_conversion_user') && $data->approved){
			//email to the user
			$view->conversion->towho		= "user";
			$message = $view->loadTemplate( $plantilla );
			$to = $data->email;

			if (!empty($to)) {
				$mail->addRecipient($to);
				$mail->setbody($message);

				$success2 = $mail->Send();
				//$success2 = $mail->sendMail($from, $from_name, $to, $subject, $message, true ); // true is for HTML
			}
		}
		
		if($success && $success2) return true;
		else return false;

	}
	
	function getConversionView()
	{
		if (!class_exists( 'ConversionsViewConversion' ))
		{
			// Build the path to the model based upon a supplied base path
			$path = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_affiliatetracker'.DS.'views'.DS.'conversion'.DS.'view.html.php';
			$false = false;

			// If the model file exists include it and try to instantiate the object
			if (file_exists( $path )) {
				require_once( $path );
				if (!class_exists( 'ConversionsViewConversion' )) {
					JError::raiseWarning( 0, 'View class ConversionsViewConversion not found in file.' );
					return $false;
				}
			} else {
				JError::raiseWarning( 0, 'View ConversionsViewConversion not supported. File not found.' );
				return $false;
			}
		}

		$view = new ConversionsViewConversion();
		return $view;
	}

}