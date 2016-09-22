<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
if(!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);
include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaserial'.DS.'helpers'.DS.'helper.php');

$taskGroup = JRequest::getCmd('ctrl', 'dashboard');
$hikaSerialConfig = hikaserial::config();
JHTML::_('behavior.tooltip');
$bar = JToolBar::getInstance('toolbar');
$bar->addButtonPath(HIKASERIAL_BUTTON);

if($taskGroup != 'update' && !$hikaSerialConfig->get('installcomplete')) {
	$url = hikaserial::completeLink('update&task=install', false, true);
	echo '<script>document.location.href="'.$url.'";</script>'."\r\n".
		'Install not finished... You will be redirected to the second part of the install screen<br/>'.
		'<a href="'.$url.'">Please click here if you are not automatically redirected within 3 seconds</a>';
	return;
}

$currentuser = JFactory::getUser();
if($taskGroup != 'update' && HIKASHOP_J16 && !$currentuser->authorise('core.manage', 'com_hikaserial'))
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
if($taskGroup == 'config' && HIKASHOP_J16 && !$currentuser->authorise('core.admin', 'com_hikaserial'))
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));

if(!include(HIKASERIAL_CONTROLLER.$taskGroup.'.php')) {
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
	echo hikaserial::footer();
}
echo '<div id="hikaserial_main_content">'.ob_get_clean().'</div>';
