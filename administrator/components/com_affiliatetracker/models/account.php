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

class AccountsModelAccount extends JModelLegacy
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
			$query = ' SELECT acc.*, u.username FROM #__affiliate_tracker_accounts as acc '.
					 ' LEFT JOIN #__users as u ON u.id = acc.user_id '.
					 ' WHERE acc.id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();

		}

		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;
			$this->_data->account_name = "";
			$this->_data->publish = 1;
			$this->_data->params = "";
			$this->_data->user_id = 0;
			$this->_data->username = "";

			$this->_data->type = "percent";

			$this->_data->comission = "";
			$this->_data->name = "";
			$this->_data->company = "";
			$this->_data->email = "";
			$this->_data->address = "";
			$this->_data->city = "";
			$this->_data->state = "";
			$this->_data->country = "";
			$this->_data->zipcode = "";
			$this->_data->phone = "";
			$this->_data->ref_word = "";
			$this->_data->refer_url = "";
			$this->_data->parent_id = 0;
			$this->_data->variable_comissions = "";

		}

		return $this->_data;
	}

	function store()
	{
		$row = $this->getTable();

		$data = JRequest::get( 'post' );

		$installedPlugins = AffiliateHelper::getInstalledPlugins(true);
		$variable_commissions = array();

		// If we've just created the user, we fill it's default commissions
		if ($data['id'] == 0) {
			foreach	($installedPlugins as $plugin) {
				$pluginCommission = new stdClass();
				$pluginCommission->extension = $plugin->name;
				$pluginCommission->type = $plugin->type;
				$pluginCommission->commission = $plugin->commissions[0];

				array_push($variable_commissions, $pluginCommission);
			}
		} else {
			for ($i = 0; $i < sizeof($installedPlugins); $i++) {
				$pluginCommission = new stdClass();
				$pluginCommission->extension = $installedPlugins[$i]->name;
				$pluginCommission->commission = $data['commission_'.$installedPlugins[$i]->name];
				$pluginCommission->type = $data['type_'.$installedPlugins[$i]->name];

				array_push($variable_commissions, $pluginCommission);
			}
		}

		$data['variable_comissions'] = json_encode($variable_commissions);

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

		if(!$data["id"] && $data['publish']){
			$this->sendMail($row->id);
		}

		return $row->id;
	}

	function sendMail($id){

		$this->setId($id) ;
		$data = $this->getData();

		$params =JComponentHelper::getParams( 'com_affiliatetracker' );

		if($params->get('email_account_user')){

			$from = $params->get('email_email') ;
			$from_name = $params->get('email_name') ;
			$subject = JText::_('AFFILIATE_ACCOUNT_APPROVED') ;

			$view		= $this->getAccountView();

			$view->account		= $data;
			$view->params		= $params;

			$view->_path['template'][1] = JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_affiliatetracker'.DS.'views'.DS.'account'.DS.'tmpl' ;

			$plantilla = 'email' ;

			//email to the admin
			$view->account->towho		= "user";
			$message = $view->loadTemplate( $plantilla );

			$to = $data->email;

			$sent = false;
			if (!empty($from) && !empty($to)) {
				$mail = JFactory::getMailer();
				$mail->addRecipient($to);
				$mail->setsender(array($from, $from_name));
				$mail->setSubject($subject);
				$mail->setbody($message);
				$mail->isHTML(true);

				$sent = $mail->Send();
			}

			return $sent ;
		}

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

				$query = ' SELECT publish FROM #__affiliate_tracker_accounts WHERE id = '. $cid . ' LIMIT 1 ';
				$this->_db->setQuery($query);
				$waspublished = $this->_db->loadResult();

				$query = ' UPDATE #__affiliate_tracker_accounts SET publish = 1 WHERE id = '. $cid . ' LIMIT 1 ';
				$this->_db->setQuery($query);
				$this->_db->query();

				if(!$waspublished){
					$this->sendMail($cid);
				}
			}
		}
		return true;
	}

	function unpublish()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'default', 'array' );

		if (count( $cids )) {
			foreach($cids as $cid) {
				$query = ' UPDATE #__affiliate_tracker_accounts SET publish = 0 WHERE id = '. $cid . ' LIMIT 1 ';
				$this->_db->setQuery($query);
				$this->_db->query();
			}
		}
		return true;
	}

	function getAccountView()
	{
		if (!class_exists( 'AffiliateViewAccount' ))
		{
			// Build the path to the model based upon a supplied base path
			$path = JPATH_SITE.DS.'components'.DS.'com_affiliatetracker'.DS.'views'.DS.'account'.DS.'view.html.php';
			$false = false;

			// If the model file exists include it and try to instantiate the object
			if (file_exists( $path )) {
				require_once( $path );
				if (!class_exists( 'AffiliateViewAccount' )) {
					JError::raiseWarning( 0, 'View class AffiliateViewAccount not found in file.' );
					return $false;
				}
			} else {
				JError::raiseWarning( 0, 'View AffiliateViewAccount not supported. File not found.' );
				return $false;
			}
		}

		$view = new AffiliateViewAccount();
		return $view;
	}

}
