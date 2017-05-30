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

class AffiliateModelAccount extends JModelLegacy
{
	function __construct()
	{
		parent::__construct();

		$id = JRequest::getVar('id');
		$this->setId((int)$id);

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
			$query = 	' SELECT acc.*, u.username '.
						' FROM #__affiliate_tracker_accounts as acc '.
						' LEFT JOIN #__users AS u ON u.id = acc.user_id '.
						' WHERE acc.id = ' . $this->_id
						;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();

			if (!$this->_data) {
				$user = JFactory::getUser();

				$this->_data = new stdClass();
				$this->_data->id = 0;

				$this->_data->name = $user->name;
				$this->_data->email = $user->email;

				$this->_data->account_name = "";
				$this->_data->company = "";
				$this->_data->address = "";
				$this->_data->zipcode = "";
				$this->_data->city = "";
				$this->_data->state = "";
				$this->_data->country = "";
				$this->_data->phone = "";

				$user = JFactory::getUser();

				$query = 	' SELECT acc.payment_options '.
							' FROM #__affiliate_tracker_accounts as acc '.
							' WHERE acc.user_id = ' . $user->id
							;
				$this->_db->setQuery( $query );
				$this->_data->payment_options = $this->_db->loadResult();

			}

		}

		return $this->_data;
	}


	function store($data = null)
	{
		$row = $this->getTable();

		if (empty($data)) $data = JRequest::get( 'post' );

		$data["payment_options"] = json_encode($data["payment_options"]);

		if (!$data['publish']) $data["publish"] = NULL;

		$params = JComponentHelper::getParams( 'com_affiliatetracker' );
		if ($params->get('autoacceptaccounts')) {
			$data['publish'] = 1;
		}

		// If we've just created the user, we fill it's default commissions
		if ($data['id'] == 0) {

			$installedPlugins = AffiliateHelper::getInstalledPlugins(true);
			$variable_commissions = array();

			foreach	($installedPlugins as $plugin) {
				$pluginCommission = new stdClass();
				$pluginCommission->extension = $plugin->name;
				$pluginCommission->type = $plugin->type;
				$pluginCommission->commission = $plugin->commissions[0];

				array_push($variable_commissions, $pluginCommission);
			}

			$data['variable_comissions'] = json_encode($variable_commissions);
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

		//new plugin access
		$dispatcher	= JDispatcher::getInstance();
		$plugin_ok = JPluginHelper::importPlugin('affiliates');
		$results = $dispatcher->trigger('onSubmitAccount', array ($row->id));

		if(!$data["id"]){
			$this->sendMail($row->id);
		}

		return $row->id;
	}

	function sendMail($id){
		$this->setId($id) ;
		$data = $this->getData();

		$params =JComponentHelper::getParams( 'com_affiliatetracker' );

		$from = $params->get('email_email') ;
		$from_name = $params->get('email_name') ;
		$subject = JText::_('NEW_ACCOUNT_REQUEST') ;

		$view		= $this->getAccountView();

		$view->account		= $data;
		$view->params		= $params;

		$view->_path['template'][1] = JPATH_SITE.DS.'components'.DS.'com_affiliatetracker'.DS.'views'.DS.'account'.DS.'tmpl' ;

		$plantilla = 'email' ;

		//email to the admin
		$view->account->towho		= "admin";
		$message = $view->loadTemplate( $plantilla );
		$to = $from;

		$sent = false;
		if (!empty($to)) {
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
