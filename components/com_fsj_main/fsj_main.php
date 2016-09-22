<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
if (!defined("DS")) define('DS', DIRECTORY_SEPARATOR);
// Require the base controller
require_once( JPATH_COMPONENT.DS.'controller.php' );
jimport('fsj_core.lib.utils.general');
jimport('fsj_core.lib.utils.dbug');
// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
    $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}
FSJ_Settings::LoadBaseSettings();
FSJ_Settings::$powered_com = 'Freestyle Joomla Overview';
// Create the controller
$classname    = 'fsj_mainController'.$controller;
$controller   = new $classname( );
$document = JFactory::getDocument();
// Load javascript and styles
FSJ_Page::StylesAndJS();
FSJ_Page::Style("components/com_fsj_main/assets/css/fsj_main.less");
$task = JRequest::getVar( 'task' );
$lang = JFactory::getLanguage();
$lang->load("com_fsj_main");
// Perform the Request task
$controller->execute( $task );
// Redirect if set by the controller
$controller->redirect();
?>
