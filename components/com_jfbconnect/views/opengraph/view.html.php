<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.pagination');

class JFBConnectViewOpengraph extends JViewLegacy
{
    function display($tpl = null)
    {
        $actionModel = $this->getModel('OpenGraphAction', 'JFBConnectModel');
        $activityModel = $this->getModel('OpenGraphActivity', 'JFBConnectModel');
        $objectModel = $this->getModel('OpenGraphObject', 'JFBConnectModel');

        $user = JFactory::getUser();

        if ($this->getLayout() == 'activity')
        {
            $activityModel->setUserId($user->get('id'));
            $rows = $activityModel->getActivityForUser();
            $this->rows = $rows;
            $this->actionModel = $actionModel;
            $this->objectModel = $objectModel;

            $pagination = $activityModel->getPagination();
            $this->pagination = $pagination;
        } else if ($this->getLayout() == 'settings')
        {
            $user = JFactory::getUser();
            $userModel = JFBConnectModelUserMap::getUser($user->get('id'), 'facebook');
            $userData = $userModel->getData();
            $actionsDisabled = $userData->params->get('og_actions_disabled');
            $this->actionsDisabled = $actionsDisabled;

            $actionModel = $this->getModel('OpenGraphAction', 'JFBConnectModel');
            $actions = $actionModel->getActions(true);
            $editableActions = array();
            foreach ($actions as $action)
            {
                if ($action->can_disable)
                    $editableActions[] = $action;
            }
            $this->actions = $editableActions;
        }
        JFBCFactory::addStylesheet('jfbconnect.css');

        parent::display($tpl);
    }
}
