<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');

class JFBConnectController extends JControllerLegacy
{
    var $view = null;

    function __construct()
    {
        parent::__construct();
        $input = JFactory::getApplication()->input;
        $viewName = JRequest::getCmd('view', $input->get('controller', ''));

        $this->_addSubMenus($viewName);
        $this->expireNotifications();

        if (!JFactory::getUser()->authorise('core.manage', 'com_jfbconnect'))
        {
            $this->setRedirect('index.php', 'You are not authorized to access the JFBConnect administration area.', 'error');
            $this->redirect();
        }

        // Anyone can access the home page or updates area
        if ($viewName == '' || strtolower($viewName) == "jfbconnect" || strtolower($viewName) == "updates")
        {
            if (JFactory::getUser()->authorise('core.admin', 'com_jfbconnect'))
                JToolBarHelper::preferences('com_jfbconnect');
        }
        else
        {
            if (!JFactory::getUser()->authorise('jfbconnect.' . strtolower($viewName) . '.manage', 'com_jfbconnect'))
            {
                $this->setRedirect('index.php?option=com_jfbconnect', 'You are not authorized to manage this section', 'error');
                $this->redirect();
            }
        }

    }

    function display($cachable = false, $urlparams = false)
    {
        parent::display();
    }

    public function autotune()
    {
        $this->setRedirect('index.php?option=com_jfbconnect&view=autotune');
        $this->redirect();
    }

    protected function _addSubMenus($vName = 'overview')
    {
        $vName = strtolower($vName);
        if ($vName == "autotune" || $vName == "jfbconnect" || $vName == "overview")
            return; // Don't show the sub-menus when inside AutoTune or the main overview page

        JSubMenuHelper::addEntry(
            JText::_('COM_JFBCONNECT_MENU_OVERVIEW'),
            'index.php?option=com_jfbconnect',
                $vName == 'overview'
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_JFBCONNECT_MENU_CONFIGURATION'),
            'index.php?option=com_jfbconnect&view=config',
                $vName == 'config'
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_JFBCONNECT_MENU_OPENGRAPH'),
            'index.php?option=com_jfbconnect&view=opengraph',
                $vName == 'opengraph'
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_JFBCONNECT_MENU_CHANNELS'),
            'index.php?option=com_jfbconnect&view=channels',
                $vName == 'channels'
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_JFBCONNECT_MENU_SOCIAL'),
            'index.php?option=com_jfbconnect&view=social',
                $vName == 'social'
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_JFBCONNECT_MENU_CANVAS'),
            'index.php?option=com_jfbconnect&view=canvas',
                $vName == 'canvas'
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_JFBCONNECT_MENU_REQUESTS'),
            'index.php?option=com_jfbconnect&view=request',
                $vName == 'request'
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_JFBCONNECT_MENU_PROFILES'),
            'index.php?option=com_jfbconnect&view=profiles',
                $vName == 'profiles'
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_JFBCONNECT_MENU_USER_MAP'),
            'index.php?option=com_jfbconnect&view=usermap',
                $vName == 'usermap'
        );
    }

    function expireNotifications()
    {
        $app = JFactory::getApplication();
        // Only run the expiration query once per session
        if (!$app->getUserState('com_jfbconnect.notifications.expired', false))
        {
            $notificationModel = $this->getModel('notification');
            $notificationModel->expireNotifications();
            $app->setUserState('com_jfbconnect.notifications.expired', true);
        }
    }
}