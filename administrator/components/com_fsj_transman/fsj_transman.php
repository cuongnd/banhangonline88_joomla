<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
if (!defined("DS")) define('DS', DIRECTORY_SEPARATOR);
if (!JFactory::getUser()->authorise('core.manage', 'com_fsj_transman')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
jimport('fsj_core.lib.layout.page');
jimport('fsj_core.lib.utils.general');
jimport('fsj_core.lib.utils.settings');
jimport('fsj_core.lib.utils.lang');
jimport('fsj_core.admin.toolbars');
jimport('joomla.application.component.controller');
jimport('fsj_core.lib.utils.dbug');
// Register helper class
JLoader::register('fsj_transmanHelper', dirname(__FILE__) . '/helpers/fsj_transman.php');
// Base Settings
FSJ_Settings::LoadBaseSettings();
// Path for fields
JHTML::addIncludePath(array(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'html'));
// Load javascript and styles
FSJ_Page::StylesAndJS();
// Load language entries from com_content, as we share much of the text
FSJ_Lang_Helper::Load_Library('fsj_core');
FSJ_Lang_Helper::Load_Component('com_content');
FSJ_Lang_Helper::Load_Component('com_categories');
FSJ_Lang_Helper::Load_Component('com_fsj_main');
// load extra language files if needed
$mainframe = JFactory::getApplication();
$default = str_replace("com_fsj_","",JRequest::getVar('option'));
if ($default == "main")
{
	$admin_com = $mainframe->getUserState( "com_fsj_main.admin_com", $default );
	if ($admin_com != $default)
		FSJ_Lang_Helper::Load_Component('com_fsj_' . $admin_com);
}
// run page
$controller = JControllerLegacy::getInstance('fsj_transman');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
