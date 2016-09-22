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

jimport('joomla.filesystem.file');

// Include main engine
$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';

if (!JFile::exists($file)) {
    return;
}

// Include the engine file.
require_once($file);

// Check if Foundry exists
if (!FD::exists()) {
	FD::language()->loadSite();
	echo JText::_('COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING');
	return;
}

// Load up the module engine
$modules = FD::modules('mod_easysocial_photos');

// We need these packages
$modules->loadComponentScripts();
$modules->loadComponentStylesheets();
$modules->addDependency('css');

// Get the layout to use
$layout = $params->get('layout', 'default');
$suffix = $params->get('suffix', '');

$options = array('ordering' => 'random', 'limit' => $params->get('limit', 20));

$model = FD::model('Photos');
$photos = $model->getPhotos($options);

require(JModuleHelper::getLayoutPath('mod_easysocial_photos', $layout));