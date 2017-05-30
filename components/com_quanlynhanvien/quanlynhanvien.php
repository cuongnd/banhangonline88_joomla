<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Quanlynhanvien
 * @author     cuong <nguyendinhcuong@gmail.com>
 * @copyright  2016 cuong
 * @license    bản quyền mã nguồn mở GNU phiên bản 2
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Quanlynhanvien', JPATH_COMPONENT);
JLoader::register('QuanlynhanvienController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = JControllerLegacy::getInstance('Quanlynhanvien');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
