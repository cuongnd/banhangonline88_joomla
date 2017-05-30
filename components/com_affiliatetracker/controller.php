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

jimport('joomla.application.component.controller');

class AffiliateController extends JControllerLegacy
{

	function display( $cachable = false, $urlparams = array())
	{

		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$id = JRequest::getInt( 'id' ) ;
		$view = JRequest::getVar( 'view' ) ;

		$params =JComponentHelper::getParams( 'com_affiliatetracker' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;

		$hasaccount = AffiliateHelper::hasAccounts($user->id);

		$can_view = true;

		if($user->id && $hasaccount){
			switch($view){
				case "conversions":
				//$can_view = $this->check_conversion_permission($id);
				$can_view = true;
				$msg = JText::_('NOT_AUTHORIZED_CONVERSIONS');
				$link = JRoute::_('index.php?option=com_affiliatetracker&view=conversions' . $itemid);
				break;
				case "logs":
				//$can_view = $this->check_conversion_permission($id);
				$can_view = true;
				$msg = JText::_('NOT_AUTHORIZED_CONVERSIONS');
				$link = JRoute::_('index.php?option=com_affiliatetracker&view=conversions' . $itemid);
				break;
				case "accounts":
				//$can_view = $this->check_conversion_permission($id);
				$can_view = true;
				$msg = JText::_('NOT_AUTHORIZED_CONVERSIONS');
				$link = JRoute::_('index.php?option=com_affiliatetracker&view=conversions' . $itemid);
				break;
				case "account":
				$can_view = $this->check_account_permission($id);
				//$can_view = true;
				$msg = JText::_('NOT_AUTHORIZED_ACCOUNT');
				$link = JRoute::_('index.php?option=com_affiliatetracker&view=accounts' . $itemid);
				if(!$user->id){
					$can_view = false;
					$msg = JText::_('PLEASE_LOGIN_FIRST');
					$link = JRoute::_('index.php?option=com_easysocial&view=login');
				}
				break;
				case "payments":
				//$can_view = $this->check_conversion_permission($id);
				$can_view = true;
				$msg = JText::_('NOT_AUTHORIZED_PAYMENTS');
				$link = JRoute::_('index.php?option=com_affiliatetracker&view=conversions' . $itemid);
				break;
				case "marketings":
				//$can_view = $this->check_conversion_permission($id);
				$can_view = true;
				$msg = JText::_('NOT_AUTHORIZED_MARKETINGS');
				$link = JRoute::_('index.php?option=com_affiliatetracker&view=marketings' . $itemid);
				break;
			}
		}
		elseif($view != "account" && $view != "accounts"){
			$link = JRoute::_('index.php?option=com_affiliatetracker&view=accounts' . $itemid);
			$can_view = false;

		}
		elseif(!$user->id && $view == "account"){
			$can_view = true; // Unified new account
			$msg = JText::_('PLEASE_LOGIN_FIRST');
			$link = JRoute::_('index.php?option=com_easysocial&view=login');
		}

		if(!$can_view){

			$this->setRedirect($link, $msg);
		}
		else parent::display($cachable,$urlparams);
	}

	function check_conversion_permission($conversion_id){

		$db = JFactory::getDBO();
		$user = JFactory::getUser();

		if(in_array(3, $user->getAuthorisedViewLevels())){
			return true;
		}

		$now = date('Y-m-d H:i:s') ;

		$query = ' SELECT co.user_id FROM #__affiliatetracker_affiliatetracker AS i '
				.' LEFT JOIN #__affiliatetracker_contacts AS co ON co.id = i.user_id '
				.' WHERE i.id = ' . $conversion_id
				.' AND ( i.start_publish <= "'. $now .'" OR i.start_publish = "0000-00-00 00:00:00") '
				.' AND ( i.end_publish >= "'. $now .'" OR i.end_publish = "0000-00-00 00:00:00") '
				.' AND i.publish = 1 '
				;

		$db->setQuery($query);
		$conversion_owner = $db->loadResult();

		if($conversion_owner == $user->id) return true;
		else return false;

	}

	function check_account_permission($id){

		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$params =JComponentHelper::getParams( 'com_affiliatetracker' );

		if(in_array(3, $user->getAuthorisedViewLevels())){
			return true;
		}

		if(!$params->get('newaccounts') && !$id) return false;
		if(!$id) return true;

		$query = ' SELECT acc.user_id FROM #__affiliate_tracker_accounts AS acc '
				.' WHERE acc.id = ' . $id  ;

		$db->setQuery($query);
		$account_owner = $db->loadResult();

		if($account_owner == $user->id) return true;
		else return false;

	}

	function save_account()
	{
		$model = $this->getModel('account');

		$params =JComponentHelper::getParams( 'com_affiliatetracker' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;

		$link = JRoute::_('index.php?option=com_affiliatetracker&view=accounts' . $itemid);
		$type = "notice";

		$id = JRequest::getInt('id'); // 0 if the account is new

		$needsActivation = false;
		$newUserData = JRequest::get( 'post' );
		$error = null;

		if ($id == 0 && JFactory::getUser()->id == 0) {
			//Create a new joomla user
			$newUser = $this->addNewJoomlaUser($newUserData['account_name'], $newUserData['account_email'], $newUserData['account_username'], $newUserData['account_password'], $error);

			if (!$newUser) {
				$msg = $error;
				$type = "error";
				$this->setRedirect($link, $msg, $type);
				return false;
			} else {
				if ($newUser != "useractivate" && $newUser != "adminactivate") $id = $newUser;
				else $needsActivation = true;
			}
		}

		$can_view = $this->check_account_permission($id);
		$affiliate_exists = AffiliateHelper::hasAccounts($id);

		if ($affiliate_exists) {
			$msg = JText::_( 'REQUEST_SUCCESS' );
			$saved_ok = true;
			$type = "message";
		}
		else if($can_view || $needsActivation){
			if ($needsActivation) {
				//Get the id of the new user
				$id = JUserHelper::getUserId($newUserData["account_username"]);
			} else {
				$id = JFactory::getUser()->id;
			}
			// Get post data and add parent_id if necessary
			$data = JRequest::get( 'post' );
			$data['user_id'] = $id;
			$parent_atid = AffiliateHelper::get_atid_from_userid($id);
			if (!empty($parent_atid)) {
				$data['parent_id'] = $parent_atid;
			}

			if ($id = $model->store($data)) {
				if ($needsActivation) {
					$msg = JText::_( 'REQUEST_SUCCESS_WAITING_ACTIVATION' );
					$saved_ok = true;
					$type = "message";
				} else {
					$msg = JText::_( 'REQUEST_SUCCESS' );
					$saved_ok = true;
					$type = "message";
				}
			} else {
				$msg = JText::_( 'ERROR_SAVING_REQUEST' );
				$saved_ok = false;
				$type = "error";
			}
		}
		else{
			$msg = JText::_('NOT_AUTHORIZED_ACCOUNT');
			$saved_ok = false;
		}

		$this->setRedirect($link, $msg, $type);
	}

	function send_email_payment($payment_id = false)
	{
		//TODO: Implement send email notification

		if(!$payment_id) $payment_id = JRequest::getInt('id');

		$app = JFactory::getApplication();

		$db = JFactory::getDBO();

		$params =JComponentHelper::getParams( 'com_affiliatetracker' );

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

		$link = JRoute::_('index.php?option=com_affiliatetracker&view=payment&id=' . $payment_id . $itemid) ;

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

				$this->send_email_payment($payment_id);

				$app = JFactory::getApplication();
				$app->close();
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

	function cancel(){

		$params =JComponentHelper::getParams( 'com_affiliatetracker' );
		$itemid = $params->get('itemid');
		if($itemid != "") $itemid = "&Itemid=" . $itemid;

		$link = JRoute::_('index.php?option=com_affiliatetracker&view=accounts' . $itemid) ;
		$text = JText::_( 'OPERATION_CANCELLED' );
		$this->setRedirect($link, $text);
	}

	private function addNewJoomlaUser($firstname, $email, $username, $password, &$error) {
		JFactory::getLanguage()->load('com_users');
		$model = AffiliateHelper::getUserRegistrationModel();

		$affiliateParams = JComponentHelper::getParams('com_affiliatetracker');
		$usertype = $affiliateParams->get( 'new_usertype' );
		if (!$usertype) {
			$usertype = 2;
		}

		$groups = array($usertype);

		$userData = array(
				"groups" => $groups,
				"name" => $firstname,
				"username" => $username,
				"password1" => $password,
				"password2" => $password,
				"email1" => $email,
				"email2" => $email
		);

		$result = $model->register($userData);

		$error = $model->getError();

		return $result;

	}

}
