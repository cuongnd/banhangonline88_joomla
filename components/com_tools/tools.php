<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Tools
 * @author     cuong <nguyendinhcuong@gmail.com>
 * @copyright  2016 cuong
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Tools', JPATH_COMPONENT);
JLoader::register('ToolsController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = JControllerLegacy::getInstance('Tools');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
