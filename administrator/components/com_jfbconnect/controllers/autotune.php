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

class JFBConnectControllerAutoTune extends JFBConnectController
{
    public function __construct()
    {
        parent::__construct();
        $document = JFactory::getDocument();
        $viewType = $document->getType();
        $viewName = 'autotune';
        $this->view = $this->getView($viewName, $viewType);
    }

    function display($cachable = false, $urlparams = false)
    {
        $task = JRequest::getCmd('task', 'default');

        if ($task != 'default' && $task != 'basicinfo')
        {
            $downloadId = JFBCFactory::config()->getSetting('sc_download_id');
            if (!$downloadId)
            {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_JFBCONNECT_MSG_ERROR_BASIC_INFO'), 'error');

                $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=basicinfo');
                $this->redirect();
            }
        }

        $viewLayout = $task;
        $autotuneModel = $this->getModel('autotune');
        $this->view->setModel($autotuneModel, true);

        $configModel = $this->getModel('config');
        $this->view->setModel($configModel, false);

        if (defined('SC16')):
            $backIcon = 'back';
            $forwardIcon = 'forward';
        endif; //SC16
        if (defined('SC30')):
            $backIcon = 'arrow-left';
            $forwardIcon = 'arrow-right';
        endif; //SC30

        switch ($task)
        {
            case 'basicinfo':
                JToolBarHelper::custom('display', $backIcon, $backIcon, "Start", false);
                JToolBarHelper::custom('saveBasicInfo', $forwardIcon, $forwardIcon, "FB App", false);
                $this->getBasicInfo();
                break;
            case 'fbappRefresh':
            case 'fbapp':
                $viewLayout = 'fbapp';
                // First, check that a FB App ID and Key are set. If not, skip this step.
                $appId = JFBCFactory::config()->getSetting('facebook_app_id');
                $secretKey = JFBCFactory::config()->getSetting('facebook_secret_key');
                if (!$appId || !$secretKey)
                {
                    JFactory::getApplication()->enqueueMessage('Facebook keys not configured. Skipping the Application Setup step', 'error');
                    $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=siteconfig');
                    $this->redirect();
                }

                // Next, check that the app is valid
                if (!$autotuneModel->validateApp(JFBCFactory::config()->getSetting('facebook_app_id'), JFBCFactory::config()->getSetting('facebook_secret_key')))
                {
                    JFactory::getApplication()->enqueueMessage(JText::_('COM_JFBCONNECT_MSG_CHECK_KEYS'), 'error');
                    $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=basicinfo');
                    $this->redirect();
                }

                // Finally, check if we should redirect to the new app page
                if ($autotuneModel->isNewApp())
                {
                    $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=fbappnew');
                    $this->redirect();
                }

                $fields = $autotuneModel->getFieldDescriptors(false);
//                if ($fields == null) // Check if we should force load the data
//                    $autotuneModel->getFieldDescriptors(true);
                $appConfig = $autotuneModel->getAppConfig(false);
//                if ($appConfig == null)
//                    $autotuneModel->getFieldDescriptors(true);

                JToolBarHelper::custom('fbappRefresh', 'refresh', 'refresh', 'Refresh', false, false);
                JToolBarHelper::divider();
                JToolBarHelper::custom('basicinfo', $backIcon, $backIcon, 'API Keys', false);
                JToolBarHelper::custom('siteconfig', $forwardIcon, $forwardIcon, 'Site Config', false);
                break;
            case 'fbappnew':
                break;
            case 'siteconfig':
                JToolBarHelper::custom('fbapp', $backIcon, $backIcon, 'FB App', false);
                JToolBarHelper::custom('errors', $forwardIcon, $forwardIcon, 'Error Check', false);
                $this->setupSiteConfig();
                break;
            case 'errors':
                JToolBarHelper::custom('siteconfig', $backIcon, $backIcon, 'Site Config', false);
                JToolBarHelper::custom('finish', $forwardIcon, $forwardIcon, 'Finish', false);
                $this->setupCheckErrors();
                break;
            case 'finish':
                JToolBarHelper::custom('errors', $backIcon, $backIcon, 'Error Check', false);
                break;
            case 'default':
            default:
                $this->setupIntroPage();
                JToolBarHelper::custom('basicinfo', $forwardIcon, $forwardIcon, 'API Keys', false);
                break;
        }
        JToolBarHelper::divider();
        JToolBarHelper::cancel('exitAutoTune', 'Exit AutoTune');
        $this->view->setLayout($viewLayout);
        $this->view->display();
    }

    public function exitAutoTune()
    {
        $this->setRedirect('index.php?option=com_jfbconnect');
        $this->redirect();
    }

    public function fbappRefresh()
    {
        $autotuneModel = $this->getModel('autotune');
        $autotuneModel->getFieldDescriptors(true);
        $autotuneModel->getAppConfig(true);
        $this->display();
    }

    private function setupSiteConfig()
    {
        $autotuneModel = $this->getModel('autotune');
        $JFBCSystemEnabled = $autotuneModel->isPluginEnabled('jfbcsystem');
        $JFBCAuthenticationEnabled = $autotuneModel->isPluginEnabled('jfbconnectauth');
        $JFBCContentEnabled = $autotuneModel->isPluginEnabled('jfbccontent');
        $JFBCUserEnabled = $autotuneModel->isPluginEnabled('jfbconnectuser');

        $errors = $autotuneModel->getJoomlaErrors();

        $this->view->assignRef('JFBCSystemEnabled', $JFBCSystemEnabled);
        $this->view->assignRef('JFBCAuthenticationEnabled', $JFBCAuthenticationEnabled);
        $this->view->assignRef('JFBCContentEnabled', $JFBCContentEnabled);
        $this->view->assignRef('JFBCUserEnabled', $JFBCUserEnabled);
        $this->view->assignRef('joomlaErrors', $errors);
    }

    private function setupIntroPage()
    {
        $phpVersion = phpversion();
        $errorsFound = false;
        if (version_compare($phpVersion, '5.0.0') >= 0)
            $phpVersion .= '<td><img src="components/com_jfbconnect/assets/images/icon-16-allow.png" /></td>';
        else
        {
            $phpVersion .= '<td><img src="components/com_jfbconnect/assets/images/icon-16-deny.png" /></td>';
            $errorsFound = true;
        }
        $this->view->assignRef('phpVersion', $phpVersion);

        // cURL check
        $disableFunctions = ini_get('disable_functions');
        if (in_array('curl', get_loaded_extensions()) && strpos($disableFunctions, 'curl_exec') === false)
            $curlCheck = 'Enabled <td><img src="components/com_jfbconnect/assets/images/icon-16-allow.png" /></td>';
        else
        {
            $curlCheck = '<strong>Disabled</strong> <td><img src="components/com_jfbconnect/assets/images/icon-16-deny.png" /></td>';
            $errorsFound = true;
        }

        if ($errorsFound)
        {
            $app = JFactory::getApplication();
            $app->enqueueMessage(JText::_('COM_JFBCONNECT_MSG_ERROR_SERVER_CONFIG'), 'error');
        }
        $this->view->assignRef('curlCheck', $curlCheck);
        $this->view->assignRef('errorsFound', $errorsFound);
    }

    public function getBasicInfo()
    {
        $configModel = $this->getModel('config');
        $subscriberId = $configModel->getSetting('sc_download_id');

        $this->view->assignRef('subscriberId', $subscriberId);
    }

    public function saveBasicInfo()
    {
        $subscriberId = JRequest::getString('subscriberId');
        foreach (JFBCFactory::getAllProviders() as $provider)
        {
            $name = $provider->systemName;
            $appId = JRequest::getString($name . '_app_id');
            $secretKey = JRequest::getString($name . '_secret_key');
            JFBCFactory::config()->update($name . '_app_id', $appId);
            JFBCFactory::config()->update($name . '_secret_key', $secretKey);
        }

        JFBCFactory::config()->update('sc_download_id', $subscriberId);

        $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=fbapp');
    }

    private function setupCheckErrors()
    {
        if (defined('JFBCDEV'))
        {
            $domain = 'http://localhost/autotune/start.php';
            $baseUrl = 'http://www.sourcecoast.com';
        }
        else
        {
            $domain = 'https://www.sourcecoast.com/autotune/start.php';
            $baseUrl = JURI::root();
        }
        $baseUrl = base64_encode(urlencode($baseUrl));

        $autotuneModel = $this->getModel('autotune');
        $configModel = $this->getModel('config');
        $subscriptionId = $configModel->getSetting('sc_download_id');
        $query = '?baseUrl=' . $baseUrl . '&subscriptionId=' . $subscriptionId . '&task=jfbconnect.errorStart&format=html&' . $autotuneModel->getVersionURLQuery();

        $iframeUrl = $domain . $query;
        $this->view->assignRef('iframeUrl', $iframeUrl);
    }

    public function saveAppConfig()
    {
        $autotuneModel = $this->getModel('autotune');
        $settings = $autotuneModel->getAppValuesToSave(false);
        $autotuneModel->updateFBApplication($settings);

        // Always set the migrations
        $migrations = $autotuneModel->getAppMigrationsToSave();
        $autotuneModel->updateFBApplication($migrations);

        // Update the database with the new app info
        $autotuneModel->getAppConfig(true);
        $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=fbapp');
    }

    public function saveAppRecommendations()
    {
        $autotuneModel = $this->getModel('autotune');
        $settings = $autotuneModel->getAppValuesToSave(true);
        $autotuneModel->updateFBApplication($settings);

        // Always set the migrations
        $migrations = $autotuneModel->getAppMigrationsToSave();
        $autotuneModel->updateFBApplication($migrations);

        // Update the database with the new app info
        $autotuneModel->getAppConfig(true);
        $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=fbapp');
    }

    public function publishPlugin()
    {
        $name = JRequest::getString('pluginName');
        $status = JRequest::getInt('pluginStatus');
        $autotuneModel = $this->getModel('autotune');
        $autotuneModel->publishPlugin($name, $status);
        $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=siteconfig');
        $this->redirect();
    }
}