<?php
//namespace components\com_jchat;
/**
 * Entrypoint dell'application di frontend
 * @package JCHAT::components::com_jchat
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html    
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 
if(!JComponentHelper::getParams('com_jchat')->get('enable_debug', 0)) {
	ini_set('display_errors', false);
	ini_set('error_reporting', E_ERROR);
}

// Auto loader setup
// Register autoloader prefix
require_once JPATH_COMPONENT_ADMINISTRATOR . '/framework/loader.php';
JChatLoader::setup();
JChatLoader::registerPrefix('JChat', JPATH_COMPONENT_ADMINISTRATOR . '/framework');

// Main application object
$app = JFactory::getApplication();

// Manage partial language translations
$jLang = JFactory::getLanguage();
$jLang->load('com_jchat', JPATH_COMPONENT, 'en-GB', true, true);
if($jLang->getTag() != 'en-GB') {
	$jLang->load('com_jchat', JPATH_SITE, null, true, false);
	$jLang->load('com_jchat', JPATH_COMPONENT, null, true, false);
}

/*
 * All SMVC logic is based on controller.task correcting the wrong Joomla concept
 * of base execute on view names.
 * When task is not specified because Joomla force view query string such as menu
 * the view value is equals to controller and viewname = controller.display
 */
$viewName = $app->input->get('view', 'stream');
$controller_command = $app->input->get('task', $viewName . '.display');
if (strpos($controller_command, '.')) {
	list($controller_name, $controller_task) = explode('.', $controller_command);
}
// Defaults
if (!isset($controller_name)) {
	$controller_name = 'stream';
}
if (!isset($controller_task)) {
	$controller_task = 'display';
}

$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . strtolower($controller_name) . '.php';
if (file_exists($path)) {
	require_once $path;
} else {
	$app->enqueueMessage(JText::_('COM_JCHAT_ERROR_NO_CONTROLLER_FILE'), 'error');
	return false;
}

// Create the controller
$classname = 'JChatController' . ucfirst($controller_name);
if (class_exists($classname)) {
	$controller = new $classname();
	// Perform the Request task
	$controller->execute($controller_task);

	// Redirect if set by the controller
	$controller->redirect();
} else {
	$app->enqueueMessage(JText::_('COM_JCHAT_ERROR_NO_CONTROLLER'), 'error');
	return false;
}
