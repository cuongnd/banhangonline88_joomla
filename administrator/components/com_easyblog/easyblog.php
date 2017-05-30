<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');


// Load constants
require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'constants.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

$lang 	= JFactory::getLanguage();

// Always load the english language file.
$lang->load( 'com_easyblog' , JPATH_ROOT . '/administrator', 'en-GB' , true );

// Load site's default language file.
$lang->load( 'com_easyblog' , JPATH_ROOT . '/administrator', $lang->getDefault() , true );

// Load user's preferred language file.
$lang->load( 'com_easyblog' , JPATH_ROOT . '/administrator', null , true );

EasyBlogHelper::getHelper( 'Ajax' )->process();

// Test for user access
if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
{
	if(!JFactory::getUser()->authorise('core.manage', 'com_easyblog') )
	{
		JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
		JFactory::getApplication()->close();
	}
}

$config	= EasyBlogHelper::getConfig();

// Compile javascripts
EasyBlogHelper::compileJS();

// Include AJAX response library
require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'ejax.php' );

$controllerFile = 'controller.php';

// Require the base controller
require_once( JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controllerFile );

$lang 	= JFactory::getLanguage();

// Always load the english language file.
$lang->load( 'com_easyblog' , JPATH_ROOT , 'en-GB' , true );

// Load site's default language file.
$lang->load( 'com_easyblog' , JPATH_ROOT , $lang->getDefault() , true );

// Load user's preferred language file.
$lang->load( 'com_easyblog' , JPATH_ROOT , null , true );

// Set the tables path
JTable::addIncludePath( EBLOG_TABLES );

// Get the task
$task	= JRequest::getCmd( 'task' , 'display' );

// We treat the view as the controller. Load other controller if there is any.
$controller	= JRequest::getWord( 'c' , '' );

$newControllers = array( 'themes' , 'lang' , 'media', 'foundry' );

if( in_array( JRequest::getWord( 'controller' ) , $newControllers ) )
{
	$controller =   JRequest::getWord( 'controller' );
}

// Bootstrap is using "controller" var not "c".
// if( JRequest::getWord( 'controller') == 'themes' && JRequest::getWord( 'task' ) == 'getAjaxTemplate' )
// {
// 	$controller = 'themes';
// }

if( !empty( $controller ) )
{
	$controller	= JString::strtolower( $controller );
	$path		= JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controller . '.php';

	jimport( 'joomla.filesystem.file' );

	// Test if the controller really exists
	if( JFile::exists( $path ) )
	{
		require_once( $path );
	}
	else
	{
		JError::raiseError( 500 , JText::_( 'Invalid Controller name "' . $controller . '".<br /> File "' . $path . '" does not exists in this context.' ) );
	}
}

$class	= 'EasyBlogController' . JString::ucfirst( $controller );

// Test if the object really exists in the current context
if( class_exists( $class ) )
{
	$controller	= new $class();
}
else
{
	JError::raiseError( 500 , 'Invalid Controller Object. Class definition does not exists in this context.' );
}

// Task's are methods of the controller. Perform the Request task
$controller->execute( $task );

// Redirect if set by the controller
$controller->redirect();
