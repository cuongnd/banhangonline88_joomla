<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Vatgia
 * @author     cuong <nguyendinhcuong@gmail.com>
 * @copyright  2016 cuong
 * @license    bản quyền mã nguồn mở GNU phiên bản 2
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Vatgia', JPATH_COMPONENT);
JLoader::register('VatgiaController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = JControllerLegacy::getInstance('Vatgia');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
