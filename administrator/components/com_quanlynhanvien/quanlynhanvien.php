<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Quanlynhanvien
 * @author     cuong <nguyendinhcuong@gmail.com>
 * @copyright  2016 cuong
 * @license    bản quyền mã nguồn mở GNU phiên bản 2
 */

// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_quanlynhanvien'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Quanlynhanvien', JPATH_COMPONENT_ADMINISTRATOR);

$controller = JControllerLegacy::getInstance('Quanlynhanvien');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
