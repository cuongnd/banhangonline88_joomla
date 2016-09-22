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

// Ensure that the Joomla sections don't appear.
JRequest::setVar( 'tmpl' , 'component' );

// Determines if the current mode is re-install
$reinstall 	= JRequest::getBool( 'reinstall' , false ) || JRequest::getBool( 'install' , false );

// If the mode is update, we need to get the latest version
$update 	= JRequest::getBool( 'update' , false );

// Determines if we are now in developer mode.
$developer 	= JRequest::getBool( 'developer' );

if( $developer )
{
	$session 	= JFactory::getSession();
	$session->set( 'easysocial.developer' , true );
}

############################################################
#### Constants
############################################################
define('ES_PACKAGES', dirname( __FILE__ ) . '/packages' );
define('ES_CONFIG', dirname( __FILE__ ) . '/config' );
define('ES_THEMES', dirname( __FILE__ ) . '/themes' );
define('ES_LIB', dirname( __FILE__ ) . '/libraries' );
define('ES_CONTROLLERS', dirname( __FILE__ ) . '/controllers' );
define('ES_SERVER', 'http://stackideas.com' );
define('ES_VERIFIER', 'http://stackideas.com/updater/verify' );
define('ES_MANIFEST', 'http://stackideas.com/updater/manifests/easysocial' );
define('ES_TMP', dirname(__FILE__) . '/tmp');
define('ES_BETA', false);

############################################################
#### Dependencies
############################################################
jimport( 'joomla.filesystem.file' );
require_once( ES_LIB . '/json.php' );


############################################################
#### Process ajax calls
############################################################
if( JRequest::getBool( 'ajax' ) )
{
	// Perform ajax methods here.
	$controller 	= JRequest::getCmd( 'controller' );
	$task 			= JRequest::getCmd( 'task' );

	$controllerFile 	= ES_CONTROLLERS . '/' . strtolower( $controller ) . '.php';

	require_once( $controllerFile );

	$controllerName 	= 'EasySocialController' . ucfirst( $controller );
	$controller 		= new $controllerName();

	return $controller->$task();
}

############################################################
#### Process controller
############################################################
$controller 	= JRequest::getCmd( 'controller' , '' );

if( !empty( $controller ) )
{
	$controllerFile 	= ES_CONTROLLERS . '/' . strtolower( $controller ) . '.php';

	require_once( $controllerFile );

	$controllerName 	= 'EasySocialController' . ucfirst( $controller );
	$controller 		= new $controllerName();
	return $controller->execute();
}

#####################################

############################################################
#### Initialization
############################################################
$contents 	= JFile::read( ES_CONFIG . '/installation.json' );
$json 		= new Services_JSON();
$steps 		= $json->decode( $contents );


############################################################
#### Workflow
############################################################
$active 	= JRequest::getInt( 'active' , 0 );

if( $active == 0 )
{
	$active 	= 1;
	$stepIndex 	= 0;
}
else
{
	$active 	+= 1;
	$stepIndex 	= $active - 1;
}

if( $active > count( $steps ) )
{
	$active 		= 'complete';
	$activeStep 	= new stdClass();

	$activeStep->title 		= JText::_( 'COM_EASYSOCIAL_INSTALLATION_COMPLETED' );
	$activeStep->template	= 'complete';

	// Assign class names to the step items.
	if ($steps) {

		foreach ($steps as $step) {
			$step->className 	= ' active past';
		}
	}
}
else
{
	// Get the active step object.
	$activeStep 	= $steps[ $stepIndex ];

	// Assign class names to the step items.
	foreach( $steps as $step )
	{
		$step->className 	= $step->index == $active || $step->index < $active ? ' active' : '';
		$step->className 	.= $step->index < $active ? ' past' : '';
	}
}

require( ES_THEMES . '/default.php' );
