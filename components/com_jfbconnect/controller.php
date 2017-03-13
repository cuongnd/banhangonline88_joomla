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

jimport('joomla.application.component.controller');
jimport('sourcecoast.utilities');

class JFBConnectController extends JControllerLegacy
{
    public function __construct($config = array())
    {
        if (!class_exists('JFBCFactory'))
        {
            $app = JFactory::getApplication();
            $app->redirect('index.php', "JFBCSystem plugin is not enabled.");
        }
        parent::__construct($config);
    }

    function display($cachable = false, $urlparams = false)
    {
        parent::display();
    }

    function deauthorizeUser()
    {
        $fbClient = JFBCFactory::provider('facebook')->getFbClient();

        $signedRequest = JRequest::getString('signed_request', null, 'POST');
        if ($signedRequest)
        {
            $parsed = $fbClient->parseSignedRequest($signedRequest);
            $fbUserId = $parsed['user_id'];
            if ($fbUserId)
            {
                JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/' . 'models');
                $userModel = JModelLegacy::getInstance('UserMap', 'JFBConnectModel');
                $userModel->setAuthorized($fbUserId, '0');
            }
        }
        exit;
    }

    /*  Not ready for primetime yet. The setInitialRegistration causes issues.
    function updateProfile()
    {
        $jUser = JFactory::getUser();
        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        $jfbcLibrary->setInitialRegistration();
        $fbUserId = $jfbcLibrary->getMappedFbUserId();
        $args = array($jUser->get('id'), $fbUserId);

        $app = JFactory::getApplication();
        JPluginHelper::importPlugin('jfbcprofiles');
        $app->triggerEvent('scProfilesImportProfile', $args);
        $app->enqueueMessage('Profile Imported!');
        $app->redirect('index.php');
    }*/
}
