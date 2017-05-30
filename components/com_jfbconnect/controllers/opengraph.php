<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class JFBConnectControllerOpenGraph extends JFBConnectController
{
    function display($cachable = false, $urlparams = false)
    {
        $userId = JFBCFactory::provider('facebook')->getMappedUserId();
        if (!$userId)
        {
            $app = JFactory::getApplication();
            $app->redirect('index.php?option=com_users&view=login', "You are not currently logged in via Facebook.");
        }
        $document = JFactory::getDocument();
        $viewType = $document->getType();
        $viewLayout = JRequest::getCmd('layout', 'activity');
        $view = $this->getView('opengraph', $viewType, 'JFBConnectView', array('base_path' => $this->basePath, 'layout' => $viewLayout));

        $view->assignRef('document', $document);

        $ogActivityModel = $this->getModel('opengraphactivity', 'JFBConnectModel');
        $view->setModel($ogActivityModel, false);

        $ogActionModel = $this->getModel('opengraphaction', 'JFBConnectModel');
        $view->setModel($ogActionModel, false);

        $ogObjectModel = $this->getModel('opengraphobject', 'JFBConnectModel');
        $view->setModel($ogObjectModel, false);

        if ($viewLayout == 'settings')
        {
            $userMapModel = $this->getModel('usermap', 'JFBConnectModel');
            $view->setModel($userMapModel, false);
        }
        $view->display();

        return $this;
    }

    public function ajaxAction()
    {
        JSession::checkToken('get') or die();
        $actionId = JRequest::getInt('action');
        $href = JRequest::getVar('href');
        $href = urldecode($href);
        $params = JRequest::getVar('params');
        if (is_array($params))
        {
            foreach ($params as $key => $value)
                $params[$key] = rawurldecode($value);
        } else
            $params = array();

        JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_jfbconnect/models');
        $jfbcOgActionModel = JModelLegacy::getInstance('OpenGraphAction', 'JFBConnectModel');

        $action = $jfbcOgActionModel->getAction($actionId);

        $response = $jfbcOgActionModel->triggerAction($action, $href, $params);

        if ($response->status)
            echo $response->message;
        else
        {
            if (JFBCFactory::config()->getSetting('facebook_display_errors') && $response->message != "")
                echo "Error: " . $response->message;
        }

        exit;
    }

    public function saveSettings()
    {
        JSession::checkToken('post') or jexit(JText::_('JInvalid_Token'));
        $user = JFactory::getUser();
        $userModel = JFBConnectModelUserMap::getUser($user->get('id'), 'facebook');

        $allowedActions = JRequest::getVar('allowed_actions', array(), 'POST', 'ARRAY');
        // Need to invert the checkboxes and save the opposite (only the disabled ones)
        // That allows for users who have never set their preferences to be defaulted to an opt-in
        $actionModel = $this->getModel('OpenGraphAction', 'JFBConnectModel');
        $actions = $actionModel->getActions(true);
        $disabledActions = new stdClass();
        foreach ($actions as $action)
        {
            if ($action->can_disable && !array_key_exists($action->id, $allowedActions))
            {
                $actionId = $action->id;
                $disabledActions->$actionId = 1;
            }
        }

        $userModel->saveParameter('og_actions_disabled', $disabledActions);
        $this->setRedirect(JRoute::_('index.php?option=com_jfbconnect&view=opengraph&layout=settings'));
    }

    // Popup appeared and user wants to delete the just-posted action as well as disable future actions
    public function undoAndDisableAction()
    {
        JSession::checkToken('get') or jexit(JText::_('JInvalid_Token'));
        $actionId = JRequest::getInt('action');
        $activityId = JRequest::getInt('activity');
        // First, delete the just-posted action. Then, disable the action for the future
        $this->deleteAction($activityId);
        $this->disableAction($actionId);

        $this->setRedirect(JRoute::_('index.php?option=com_jfbconnect&view=opengraph&layout=settings'));
    }

    // User is choosing to delete a specific action from their Timeline
    public function userdelete()
    {
        JSession::checkToken('get') or jexit(JText::_('JInvalid_Token'));
        $actionId = JRequest::getInt('actionid');

        $this->deleteAction($actionId);
        $this->setRedirect(JRoute::_('index.php?option=com_jfbconnect&view=opengraph'));
    }

    private function disableAction($id)
    {
        $user = JFactory::getUser();
        $userModel = JFBConnectModelUserMap::getUser($user->get('id'), 'facebook');
        $userData = $userModel->getData();
        $ogDisabledActions = $userData->params->get('og_actions_disabled', new stdClass());

        $actionModel = $this->getModel('OpenGraphAction', 'JFBConnectModel');
        $action = $actionModel->getAction($id);
        $actionId = $action->id;
        if ($action->can_disable)
            $ogDisabledActions->$actionId = 1;
        $userModel->saveParameter('og_actions_disabled', $ogDisabledActions);
    }

    private function deleteAction($id)
    {
        $app = JFactory::getApplication();
        $ogActivityModel = $this->getModel('opengraphactivity', 'JFBConnectModel');
        $activity = $ogActivityModel->getActivity($id);

        $user = JFactory::getUser();
        if ($activity->user_id == $user->get('id'))
        {
            // Delete the action from Facebook and then from the database
            $result = JFBCFactory::provider('facebook')->api('/' . $activity->response, null, false, 'DELETE');
            $error = JFBCFactory::provider('facebook')->getLastError();
            $ogActivityModel->userdelete($id);

            if ($result)
                $app->enqueueMessage(JText::_('COM_JFBCONNECT_TIMELINE_EVENT_DELETE_SUCCESS'));
            else
                $app->enqueueMessage(JText::sprintf('COM_JFBCONNECT_TIMELINE_EVENT_DELETE_FAIL', $error), 'error');
        }
        return $result;
    }

}
