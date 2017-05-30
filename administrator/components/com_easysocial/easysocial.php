<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Test for installation requests.
jimport( 'joomla.filesystem.folder' );

$setupFolder	= dirname( __FILE__ ) . '/setup';
$viewExists 	= JFolder::exists( dirname( __FILE__ ) . '/views' );
$isInstall		= JRequest::getBool( 'install' ) || JRequest::getBool( 'reinstall' ) || JRequest::getBool( 'update' );

if ($isInstall || !$viewExists) {
	require_once( dirname( __FILE__ ) . '/setup/bootstrap.php' );
	exit;
}


// Check if we need to synchronize the database columns
$sync	= JRequest::getBool( 'sync' , false );

if ($sync) {
	JRequest::setVar( 'task' , 'sync' );
	JRequest::setVar( 'controller' , 'easysocial' );
}

// Engine is required anywhere EasySocial is used.
require_once(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php');

// Check if Foundry exists
if (!FD::exists()) {
	FD::language()->loadSite();
	echo JText::_('COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING');
	return;
}

// Toggle super mode
$superdev = JRequest::getVar('superdev');

if( isset($superdev) )
{
	$super = (bool) $superdev;

	$config = FD::config();
	$config->set( 'general.super', $super );

	$jsonString = $config->toString();

	$configTable = FD::table( 'Config' );

	if( !$configTable->load( 'site' ) )
	{
		$configTable->type = 'site';
	}

	$configTable->set( 'value' , $jsonString );
	$configTable->store();

	echo 'Super developer mode: ' . (($super) ? 'ON' : 'OFF');
	return;
}

// Load language.
FD::language()->loadAdmin();

// Start collecting page objects.
FD::page()->start();

// @rule: Process AJAX calls
FD::ajax()->listen();

// Get the task
$task		= JRequest::getCmd( 'task' , 'display' );

// We treat the view as the controller. Load other controller if there is any.
$controller	= JRequest::getWord( 'controller' , '' );

// We need the base controller
FD::import( 'admin:/controllers/controller' );

if( !empty( $controller ) )
{
	$controller	= JString::strtolower( $controller );

	if( !FD::import( 'admin:/controllers/' . $controller ) )
	{
		JError::raiseError( 500 , JText::sprintf( 'COM_EASYSOCIAL_INVALID_CONTROLLER' , $controller ) );
	}
}

$class	= 'EasySocialController' . JString::ucfirst( $controller );

// Test if the object really exists in the current context
if( !class_exists( $class ) )
{
	JError::raiseError( 500 , JText::sprintf( 'COM_EASYSOCIAL_INVALID_CONTROLLER_CLASS_ERROR' , $class ) );
}

$controller	= new $class();

// Task's are methods of the controller. Perform the Request task
$controller->execute( $task );

// Redirect if set by the controller
$controller->redirect();

// End page
FD::page()->end();
