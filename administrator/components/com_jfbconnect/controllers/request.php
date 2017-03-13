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

class JFBConnectControllerRequest extends JFBConnectController
{
    function __construct()
    {
        parent::__construct();
        $document = JFactory::getDocument();
        $viewType = $document->getType();
        $viewName = 'request';
        $this->view = $this->getView($viewName, $viewType);
    }

    public function display($cachable = false, $urlparams = false)
    {
        $canvasSettings = $this->getCanvasSettings();
        $viewLayout = JRequest::getCmd('layout', 'default');
        $this->view->setLayout($viewLayout);

        if (!empty($canvasSettings))
            $canvasEnabled = ($canvasSettings->get('canvas_url', null) != null && $canvasSettings->get('secure_canvas_url', null) != null) ? true : false;
        else
        {
            if ($viewLayout == 'edit')
                JFactory::getApplication()->enqueueMessage("Canvas and Facebook Application configuration data could not be loaded. Please re-run Autotune", 'error');
            $canvasEnabled = false;
        }

        $this->view->set('canvasEnabled', $canvasEnabled);

        if ($viewLayout == "default")
        {
            JToolBarHelper::addNew('add', JText::_('COM_JFBCONNECT_BUTTON_NEW'));
            JToolBarHelper::deleteList(JText::_('COM_JFBCONNECT_REQUEST_DELETE_CONFIRMATION'));
        }
        $task = JRequest::getCmd('task', "display");
        if ($task == "")
            $task = 'display'; // Needed for ordering tasks

        $requestModel = $this->getModel('request');
        $this->view->setModel($requestModel, true);
        $this->view->$task();
    }

    public function add()
    {
        JRequest::setVar('task', 'edit');
        $this->edit();
    }

    public function remove()
    {
        $app = JFactory::getApplication();
        $model = $this->getModel('request');

        if ($model->delete())
            $app->enqueueMessage(JText::_('COM_JFBCONNECT_MSG_REQUEST_DELETE_SUCCESS'));

        $this->setRedirect('index.php?option=com_jfbconnect&controller=request');
    }

    public function edit()
    {
        JRequest::setVar('layout', 'edit');
        if (defined('SC16')):
            $icon = 'send';
        endif; //SC16
        if (defined('SC30')):
            $icon = 'envelope';
        endif; //SC30
        JToolBarHelper::custom('previewSend', $icon, $icon, JText::_('COM_JFBCONNECT_BUTTON_SEND_TO_ALL_USERS'), false);
        JToolBarHelper::save('apply', JText::_('COM_JFBCONNECT_BUTTON_SAVE'));
        JToolBarHelper::cancel('cancel', JText::_('COM_JFBCONNECT_BUTTON_CANCEL'));
        $this->display();
    }

    public function cancel()
    {
        JRequest::setVar('layout', 'default');
        JRequest::setVar('task', 'display');
        $this->display();
    }

    public function apply()
    {
        $canvasSettings = $this->getCanvasSettings();
        $app = JFactory::getApplication();
        if (!empty($canvasSettings))
        {
            $model = $this->getModel('request');
            $model->store();
            $app->enqueueMessage(JText::_('COM_JFBCONNECT_MSG_REQUEST_SAVE_SUCCESS'));
        }
        else
            $app->enqueueMessage("Request could not be saved. Please re-run Autotune to fetch your Facebook Application configuration.", 'error');

        $this->setRedirect('index.php?option=com_jfbconnect&controller=request');
    }

    public function publish()
    {
        $model = $this->getModel('request');
        $model->setPublished(true);
        $this->setRedirect('index.php?option=com_jfbconnect&controller=request');
    }

    public function unpublish()
    {
        $model = $this->getModel('request');
        $model->setPublished(false);
        $this->setRedirect('index.php?option=com_jfbconnect&controller=request');
    }

    public function enable_breakout_canvas()
    {
        $model = $this->getModel('request');
        $model->setBreakoutCanvas(true);
        $this->setRedirect('index.php?option=com_jfbconnect&controller=request');
    }

    public function disable_breakout_canvas()
    {
        $model = $this->getModel('request');
        $model->setBreakoutCanvas(false);
        $this->setRedirect('index.php?option=com_jfbconnect&controller=request');
    }

    public function previewSend()
    {
        $app = JFactory::getApplication();
        $inProgress = $app->getUserState('jfbconnect.request.inProgress', false);
        if ($inProgress)
            $app->redirect('index.php?option=com_jfbconnect&controller=request', JText::_('COM_JFBCONNECT_REQUEST_SEND_IN_PROGRESS'), 'error');

        JRequest::setVar('hidemainmenu', 1); // Hide the menus
        $usermapModel = $this->getModel('usermap');
        $this->view->setModel($usermapModel, false);

        JRequest::setVar('layout', 'send');
        JToolBarHelper::cancel('cancel', JText::_('COM_JFBCONNECT_BUTTON_CANCEL'));

        // Reset stuff
        $app = JFactory::getApplication();
        $app->setUserState('jfbconnect.request.requestId', null);
        $app->setUserState('jfbconnect.request.fbIds', null);
        $app->setUserState('jfbconnect.request.sendToAll', null);
        $app->setUserState('jfbconnect.request.sendSuccess', 0);
        $app->setUserState('jfbconnect.request.sendFail', 0);
        $app->setUserState('jfbconnect.request.sendCount', 0);

        $this->display();
    }

    public function send()
    {
        $app = JFactory::getApplication();
        $app->setUserState('jfbconnect.request.inProgress', true);

        $sendLimit = 15;
        $jfbcRequestId = $app->getUserState('jfbconnect.request.requestId');
        $sendToAll = $app->getUserState('jfbconnect.request.sendToAll');
        $sendCount = $app->getUserState('jfbconnect.request.sendCount', 0);
        $sendSuccess = $app->getUserState('jfbconnect.request.sendSuccess', 0);
        $sendFail = $app->getUserState('jfbconnect.request.sendFail', 0);

        $model = $this->getModel('request');
        $model->setId($jfbcRequestId);
        $request = $model->getData();

        $usermapModel = $this->getModel('usermap');

        if (!$sendToAll)
        {
            $fbIds = $app->getUserState('jfbconnect.request.fbIds');
            $toUsers = array_slice($fbIds, 0, $sendLimit);
            $app->setUserState('jfbconnect.request.fbIds', array_slice($fbIds, $sendLimit));
        }
        else
            $toUsers = $usermapModel->getActiveUserFbIds($sendCount, $sendLimit);

        $message = $request->message;
        $utf8message = utf8_encode($message);
        $params['message'] = $utf8message;

        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/tables');
        $app = JFactory::getApplication();
        $data = array();
        $data['fb_user_from'] = -1;
        $data['modified'] = null;
        $data['jfbc_request_id'] = $jfbcRequestId;
        foreach ($toUsers as $toUser)
        {
            $result = JFBCFactory::provider('facebook')->api('/' . $toUser . '/apprequests', $params, false, null, true);
            //$result = array('request'=> '12345', 'to' => array(0=>$toUser));
            if (isset($result['request']) && $result['to'][0] == $toUser)
            {
                // Not using the model, as we're doing a simple store.
                $data['fb_request_id'] = $result['request'];
                $data['created'] = JFactory::getDate()->toSql();
                $data['fb_user_to'] = $toUser;

                $row = JTable::getInstance('JFBConnectNotification', 'Table');
                $row->save($data);
                $sendSuccess++;
            }
            else
            {
                $usermapModel->setAuthorized($toUser, false);
                $sendFail++;
            }

            $sendCount++;
        }

        if (count($toUsers) < $sendLimit)
            $inProgress = false;
        else
            $inProgress = true;

        $app->setUserState('jfbconnect.request.sendSuccess', $sendSuccess);
        $app->setUserState('jfbconnect.request.sendFail', $sendFail);
        $app->setUserState('jfbconnect.request.sendCount', $sendCount);
        $app->setUserState('jfbconnect.request.inProgress', $inProgress);

        $return = array('sendCount' => $sendCount, 'sendSuccess' => $sendSuccess, 'sendFail' => $sendFail, 'inProgress' => $inProgress, 'sentIds' => $toUsers, 'requestId' => $request->id);
        echo json_encode($return);
        exit;
//        $this->setRedirect('index.php?option=com_jfbconnect&controller=request&view=edit&id=' . $jfbcRequestId);
    }

    private function getCanvasSettings()
    {
        // Saving an object or action
        $autotune = JFBCFactory::config()->getSetting('autotune_app_config', null);
        if (empty($autotune))
            return null;
        $appConfig = new JRegistry();
        $appConfig->loadArray($autotune);
        return $appConfig;
    }
}