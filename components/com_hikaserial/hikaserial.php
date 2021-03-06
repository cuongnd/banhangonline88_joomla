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
jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');
JRequest::setVar('hikaserial_front_end_main',1);

if(!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);
include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaserial'.DS.'helpers'.DS.'helper.php');

$shopConfig = hikaserial::config(false);
if($shopConfig->get('store_offline')) {
	$app = JFactory::getApplication();
	$app->enqueueMessage(JText::_('SHOP_IN_MAINTENANCE'));
	return;
}

global $Itemid;
if(empty($Itemid)) {
	$urlItemid = JRequest::getInt('Itemid');
	if($urlItemid) {
		$Itemid = $urlItemid;
	}
}

$view = JRequest::getCmd('view');
if(!empty($view) && !JRequest::getCmd('ctrl')) {
	JRequest::setVar('ctrl', $view);
	$layout = JRequest::getCmd('layout');
	if(!empty($layout)) {
		JRequest::setVar('task', $layout);
	}
}

if(HIKASHOP_J30) {
	$token = hikaserial::getFormToken();
	$isToken = JRequest::getVar($token, '');
	if(!empty($isToken) && !JRequest::checkToken('request')) {
		$app = JFactory::getApplication();
		$app->input->request->set($token, 1);
	}
}

$session = JFactory::getSession();
if(is_null($session->get('registry'))) {
	jimport('joomla.registry.registry');
	$session->set('registry', new JRegistry('session'));
}
$taskGroup = JRequest::getCmd('ctrl', 'category');
$className = ucfirst($taskGroup).'Controller';

if(!class_exists($className) && !include(HIKASERIAL_CONTROLLER.$taskGroup.'.php')) {
	return JError::raiseError(404, 'Page not found : '.$taskGroup);
}
$classGroup = new $className();

JRequest::setVar('view', $classGroup->getName());

$classGroup->execute(JRequest::getCmd('task'));
$classGroup->redirect();
if(JRequest::getString('tmpl') !== 'component') {
	echo hikaserial::footer();
}

JRequest::setVar('hikaserial_front_end_main',0);
