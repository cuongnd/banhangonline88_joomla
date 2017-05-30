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

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

require_once(JPATH_COMPONENT.DS.'helpers'.DS.'helpers.php');

$prefix	= 'Affiliate';

// Create the controller
$classname	= $prefix.'Controller';

$controller	= new $classname( );

$document = JFactory::getDocument();

$params = JComponentHelper::getParams( 'com_affiliatetracker' );
$load_jquery = $params->get('load_jquery', 1);
$load_bootstrap = $params->get('load_bootstrap', 1);

if ($load_jquery == 1){
    JHtml::_('jquery.framework');
}

if ($load_bootstrap == 1){
    $document->addStyleSheet("media/jui/css/bootstrap.min.css");
    $document->addScript("media/jui/js/bootstrap.min.js");
}

// Perform the Request task
$controller->execute( JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();

?>
