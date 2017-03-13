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

jimport('joomla.user.helper');

class JFBConnectControllerAuthenticate extends JFBConnectController
{

    public function display($cachable = false, $urlparams = false)
    {
    }

    public function login()
    {
        // Removing this session check in v5.2.2 due to complaints of session token failures
        // Can investigate adding later. Still doing a check on the callback.
        //if (!JSession::checkToken('get'))
        //    $this->redirect('index.php', JText::_('JLIB_ENVIRONMENT_SESSION_EXPIRED'));

        $provider = $this->getProvider();

        $this->saveReturnParameter();

        // Some providers will redirect here to go through the authentication flow, others will continue on.
        $provider->client->authenticate();

        $this->doLogin($provider);
    }

    public function callback()
    {
        $input = JFactory::getApplication()->input;

        // Have to do our own token checking here. Redirect shouldn't happen to normal users since token was just inserted
        // when they tried to authenticate
        $token = JSession::getFormToken();
        $returnToken = $input->get('state', '', 'alnum');
        if ($token != $returnToken)
        {
            $this->setRedirect('index.php', JText::_('JLIB_ENVIRONMENT_SESSION_EXPIRED'));
            $this->redirect();
        }

        $provider = $this->getProvider();
        try
        {
            $provider->client->authenticate();
        }
        catch (Exception $e)
        {
            $app = JFactory::getApplication();
            $app->enqueueMessage(JText::sprintf('COM_JFBCONNECT_UNABLE_TO_RETRIEVE_USER', ucwords($provider->name)), 'warning');
            if (JFBCFactory::config()->getSetting('facebook_display_errors'))
                $app->enqueueMessage($e->getMessage(), 'error');

            $this->redirectOnFailedAuth();
        }
        $this->doLogin($provider);

    }

    private function doLogin($provider)
    {
        if ($provider->client->isAuthenticated())
        {
            $provider->setSessionToken();
            require_once(JPATH_COMPONENT . '/controllers/login.php');
            $loginController = new JFBConnectControllerLogin();
            // This will redirect the user on successful login, or return false if not
            $loginController->login($provider);
        }

        // If we get here, something failed
        $this->redirectOnFailedAuth();
    }

    private function getProvider()
    {
        $input = JFactory::getApplication()->input;
        $provider = $input->getCmd('provider', null);
        if ($provider)
            return JFBCFactory::provider($provider);

        // No provider given, not a real call. Redirect to home
        $this->setRedirect('index.php');
    }

    private function saveReturnParameter()
    {
        //Save the current return parameter
        $returnParam = JRequest::getString('return', '');
        if ($returnParam != "")
        {
            $return = urlencode($returnParam); // Required for certain SEF extensions
            $return = rawurldecode($return);
            $return = base64_decode($return);
        }
        else
            $return = 'index.php';

        JFactory::getApplication()->setUserState('com_jfbconnect.login.return', $return);
    }

    private function redirectOnFailedAuth()
    {
        $redirect = JFactory::getApplication()->getUserState('com_jfbconnect.login.return', 'index.php');
        JFactory::getApplication()->setUserState('com_jfbconnect.login.return', null);

        $this->setRedirect($redirect);
        $this->redirect();
    }

}
