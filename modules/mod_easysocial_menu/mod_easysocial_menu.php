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
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

// Include main engine
$engine = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';

if (!JFile::exists($engine)) {
	return;
}

// Include the engine file.
require_once($engine);

// Check if Foundry exists
if (!FD::exists()) {
	FD::language()->loadSite();
	echo JText::_( 'COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING' );
	return;
}

$my = FD::user();

// If the user is not logged in, don't show the menu
if ($my->guest) {
	return;
}

// Load up the module engine
$modules = FD::modules( 'mod_easysocial_menu' );

// We need these packages
$modules->loadComponentScripts();
$modules->loadComponentStylesheets();
$modules->addDependency('css');
$modules->loadScript('script.js');

// Get the layout to use.
$layout = $params->get( 'layout' , 'default' );
$suffix = $params->get( 'suffix' , '' );

// Determines if EasyBlog exists
$eblogFile = JPATH_ROOT . '/components/com_easyblog/easyblog.php';
$eblogExists = JFile::exists( $eblogFile );

// Get the logout return value
$logoutMenu = FD::config()->get( 'general.site.logout' );
$logoutReturn = FD::get( 'toolbar' )->getRedirectionUrl( $logoutMenu );
$logoutReturn = base64_encode( $logoutReturn );

require(JModuleHelper::getLayoutPath('mod_easysocial_menu', $layout));
