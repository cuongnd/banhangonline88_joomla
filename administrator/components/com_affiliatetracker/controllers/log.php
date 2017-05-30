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

class LogsControllerLog extends LogsController
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
		JRequest::setVar( 'view', 'log' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		$model = $this->getModel('log');

		if ($id = $model->store($post)) {
			$msg = JText::_( 'LOG_SAVED' );
		} else {
			$msg = JText::_( 'ERROR_SAVING_LOG' );
		}

		$task = JRequest::getCmd( 'task' );
		$from = JRequest::getCmd( 'from' );
		
		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_affiliatetracker&controller=log&task=edit&cid[]='. $id ;
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_affiliatetracker&controller=logs';
				
				break;
		}
		
		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		$model = $this->getModel('log');
		if(!$model->delete()) {
			$msg = JText::_( 'ERROR_DELETING_LOG' );
		} else {
			$msg = JText::_( 'LOG_DELETED' );
		}

		$this->setRedirect( 'index.php?option=com_affiliatetracker&controller=logs', $msg );
	}

	function cancel()
	{
		$msg = JText::_( 'OPERATION_CANCELLED' );
		$from = JRequest::getCmd( 'from' );
		
		$link = 'index.php?option=com_affiliatetracker&controller=logs';
		
		$this->setRedirect( $link, $msg );
	}
	/*
	function publish()
	{
		$model = $this->getModel('log');
		if(!$model->publish()) {
			$msg = JText::_( 'ERROR_MARKING_LOG_PAYED' );
		} else {
			$msg = JText::_( 'LOG_PAYED' );
		}

		$this->setRedirect( 'index.php?option=com_affiliatetracker&controller=logs', $msg );
	}
	
	function unpublish()
	{
		$model = $this->getModel('log');
		if(!$model->unpublish()) {
			$msg = JText::_( 'ERROR_MARKING_LOG_UNPAYED' );
		} else {
			$msg = JText::_( 'LOG_NOT_PAYED' );
		}

		$this->setRedirect( 'index.php?option=com_affiliatetracker&controller=logs', $msg );
	}
	*/
	
}