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

//new for Joomla 3.0
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

if (!JFactory::getUser()->authorise('core.manage', 'com_affiliatetracker'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Require the base controller

require_once(JPATH_SITE.DS.'components'.DS.'com_affiliatetracker'.DS.'helpers'.DS.'helpers.php');
require_once( JPATH_COMPONENT.DS.'controller.php' );

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {

	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

$conversions = false;
$accounts = false;
$logs = false;
$payments = false;
$marketings = false;

switch($controller){
	
	case "conversions": 
	case "conversion":
		$conversions = true;
		$prefix	= 'Conversions';
		break;
	
	case "accounts": 
	case "account":
		
		$accounts = true;
		$prefix	= 'Accounts';
		break;
		
	case "logs": 
	case "log":
		
		$logs = true;
		$prefix	= 'Logs';
		break;

	case "payments": 
	case "payment":
		
		$payments = true;
		$prefix	= 'Payments';
		break;

	case "marketings":
	case "marketing":

		$marketings = true;
		$prefix = 'Marketings';
		break;
	
	default:
		$conversions = true ;
		$prefix	= 'Conversions';
		break;
}

$lang = JFactory::getLanguage();
$lang->load('com_affiliatetracker', JPATH_SITE);


JSubMenuHelper::addEntry(JText::_('CONVERSIONS'), 'index.php?option=com_affiliatetracker&controller=conversions', $conversions );
JSubMenuHelper::addEntry(JText::_('ACCOUNTS'), 'index.php?option=com_affiliatetracker&controller=accounts', $accounts);
JSubMenuHelper::addEntry(JText::_('LOGS'), 'index.php?option=com_affiliatetracker&controller=logs', $logs);
JSubMenuHelper::addEntry(JText::_('PAYMENTS'), 'index.php?option=com_affiliatetracker&controller=payments', $payments);
JSubMenuHelper::addEntry(JText::_('MARKETING_MATERIAL'), 'index.php?option=com_affiliatetracker&controller=marketings', $marketings);

JSubMenuHelper::addEntry(JText::_('PAYMENT_OPTIONS'), 'index.php?option=com_plugins&filter_folder=affiliates');

$document	= JFactory::getDocument();		

//LOAD BOOTSTRAP
//$document->addStyleSheet('components/com_affiliatetracker/assets/bootstrap/css/bootstrap.css');	
$document->addStyleSheet('components/com_affiliatetracker/assets/affiliate.css');

JHTML::_('jquery.framework');
JHTML::_('bootstrap.framework');
//$document->addScript('components/com_affiliatetracker/assets/jquery.min.js');

$mainframe = JFactory::getApplication();

/*
if(false){
	$mainframe->enqueueMessage(JText::sprintf('ALERT_PDF_MAGIC_QUOTES', php_ini_loaded_file()));
}
*/
		
// Create the controller
$classname	= $prefix.'Controller'.$controller;

$controller	= new $classname( );

// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );

// Redirect if set by the controller
$controller->redirect();