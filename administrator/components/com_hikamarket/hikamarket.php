<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
if(!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);
include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikamarket'.DS.'helpers'.DS.'helper.php');

$taskGroup = JRequest::getCmd('ctrl','dashboard');
$hikaMarketConfig = hikamarket::config();
JHTML::_('behavior.tooltip');
$bar = JToolBar::getInstance('toolbar');
$bar->addButtonPath(HIKAMARKET_BUTTON);

if($taskGroup != 'update' && !$hikaMarketConfig->get('installcomplete')) {
	$url = hikamarket::completeLink('update&task=install', false, true);
	echo '<script>document.location.href="'.$url.'";</script>'."\r\n".
		'Install not finished... You will be redirected to the second part of the install screen<br/>'.
		'<a href="'.$url.'">Please click here if you are not automatically redirected within 3 seconds</a>';
	return;
}

$currentuser = JFactory::getUser();
if($taskGroup != 'update' && HIKASHOP_J16 && !$currentuser->authorise('core.manage', 'com_hikamarket'))
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
if($taskGroup == 'config' && HIKASHOP_J16 && !$currentuser->authorise('core.admin', 'com_hikamarket'))
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));

$className = ucfirst($taskGroup).'MarketController';
$overrideClassName = ucfirst($taskGroup).'MarketControllerOverride';
if(class_exists($overrideClassName)) {
	$className = $overrideClassName;
} elseif(file_exists(HIKAMARKET_CONTROLLER.$taskGroup.'.override.php')) {
	include_once(HIKAMARKET_CONTROLLER.$taskGroup.'.override.php');
}

if(!class_exists($className) && (!file_exists(HIKAMARKET_CONTROLLER.$taskGroup.'.php') || !include_once(HIKAMARKET_CONTROLLER.$taskGroup.'.php'))) {
	if(!hikamarket::getPluginController($taskGroup))
		return JError::raiseError(404, 'Controller not found : '.$taskGroup);
}
ob_start();
if(!class_exists($className))
	return JError::raiseError(500, JText::sprintf('JLIB_APPLICATION_ERROR_INVALID_CONTROLLER_CLASS', $className));

$classGroup = new $className();
JRequest::setVar('view', $classGroup->getName());
$classGroup->execute( JRequest::getCmd('task', 'listing'));
$classGroup->redirect();
if(JRequest::getString('tmpl') !== 'component') {
	echo hikamarket::footer();
}
echo '<div id="hikamarket_main_content">'.ob_get_clean().'</div>';
