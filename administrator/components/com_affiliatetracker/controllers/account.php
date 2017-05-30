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

class AccountsControllerAccount extends AccountsController
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
		JRequest::setVar( 'view', 'account' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		$model = $this->getModel('account');

		if ($id = $model->store()) {
			$msg = JText::_( 'ACCOUNT_SAVED' );
		} else {
			$msg = JText::_( 'ERROR_SAVING_ACCOUNT' );
		}

		$task = JRequest::getCmd( 'task' );
		
		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_affiliatetracker&controller=account&task=edit&cid[]='. $id ;
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_affiliatetracker&controller=accounts';
				break;
		}
		
		
		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		$model = $this->getModel('account');
		if(!$model->delete()) {
			$msg = JText::_( 'ERROR_DELETING_ACCOUNTS' );
		} else {
			$msg = JText::_( 'ACCOUNTS_DELETED' );
		}

		$this->setRedirect( 'index.php?option=com_affiliatetracker&controller=accounts', $msg );
	}

	function cancel()
	{
		$msg = JText::_( 'OPERATION_CANCELLED' );
		$this->setRedirect( 'index.php?option=com_affiliatetracker&controller=accounts', $msg );
	}
	
	function publish()
	{
		$model = $this->getModel('account');
		if(!$model->publish()) {
			$msg = JText::_( 'ERROR_PUBLISHING_ACCOUNTS' );
		} else {
			$msg = JText::_( 'ACCOUNTS_PUBLISHED' );
		}

		$this->setRedirect( 'index.php?option=com_affiliatetracker&controller=accounts', $msg );
	}
	
	function unpublish()
	{
		$model = $this->getModel('account');
		if(!$model->unpublish()) {
			$msg = JText::_( 'ERROR_UNPUBLISHING_ACCOUNTS' );
		} else {
			$msg = JText::_( 'ACCOUNTS_UNPUBLISHED' );
		}

		$this->setRedirect( 'index.php?option=com_affiliatetracker&controller=accounts', $msg );
	}
	
}