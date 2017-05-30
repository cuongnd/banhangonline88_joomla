<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
jimport( 'joomla.application.component.view' );
jimport('joomla.filesystem.file');
jimport('fsj_core.admin.update');
class fsj_mainViewupdate extends JViewLegacy
{
    function display($tpl = null)
    {
		if (FSJ_Helper::IsJ3())
		{
			fsj_ToolbarsHelper::addSubmenu(JRequest::getCmd('view', 'fsj_main'));
		}
        JToolBarHelper::title( JText::_("FSJ_VAL_TITLE"), 'fss_admin' );
        JToolBarHelper::cancel('cancellist');
		if (!JFactory::getUser()->authorise('core.manager', 'com_fsj_main')) {
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		//FSJ_AdminHelper::DoSubToolbar();
		$updater = new FSJ_Updater();
		$this->log = $updater->Process();
		parent::display();
    }
}
