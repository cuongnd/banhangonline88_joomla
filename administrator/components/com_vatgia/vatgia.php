<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Vatgia
 * @author     cuong <nguyendinhcuong@gmail.com>
 * @copyright  2016 cuong
 * @license    bản quyền mã nguồn mở GNU phiên bản 2
 */

// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_vatgia'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Vatgia', JPATH_COMPONENT_ADMINISTRATOR);

$controller = JControllerLegacy::getInstance('Vatgia');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
