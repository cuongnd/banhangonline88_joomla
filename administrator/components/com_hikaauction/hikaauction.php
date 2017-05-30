<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
if(!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);
include(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaauction'.DS.'helpers'.DS.'helper.php');

$taskGroup = JRequest::getCmd('ctrl', 'dashboard');
$__COMPONENT_LNAME__Config = hikaauction::config();
JHTML::_('behavior.tooltip');
$bar = JToolBar::getInstance('toolbar');
$bar->addButtonPath(HIKAAUCTION_BUTTON);

if($taskGroup != 'update' && !$__COMPONENT_LNAME__Config->get('installcomplete')) {
	$url = hikaauction::completeLink('update&task=install', false, true);
	echo '<script>document.location.href="'.$url.'";</script>'."\r\n".
		'Install not finished... You will be redirected to the second part of the install screen<br/>'.
		'<a href="'.$url.'">Please click here if you are not automatically redirected within 3 seconds</a>';
	return;
}

$currentuser = JFactory::getUser();
if($taskGroup != 'update' && HIKAAUCTION_J16 && !$currentuser->authorise('core.manage', 'com_hikaauction'))
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
if($taskGroup == 'config' && HIKAAUCTION_J16 && !$currentuser->authorise('core.admin', 'com_hikaauction'))
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));

if(!file_exists(HIKAAUCTION_CONTROLLER.$taskGroup.'.php') || !include(HIKAAUCTION_CONTROLLER.$taskGroup.'.php')) {
	echo 'controller '.$taskGroup.' not found';
	return;
}
ob_start();
$className = ucfirst($taskGroup).'Controller';
$classGroup = new $className();
JRequest::setVar('view', $classGroup->getName());
$classGroup->execute( JRequest::getCmd('task', 'listing'));
$classGroup->redirect();
if(JRequest::getString('tmpl') !== 'component') {
	echo hikaauction::footer();
}
echo '<div id="hikaauction_main_content">'.ob_get_clean().'</div>';
