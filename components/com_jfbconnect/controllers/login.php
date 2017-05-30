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

class JFBConnectControllerLogin extends JFBConnectController
{

    function display($cachable = false, $urlparams = false)
    {
        JFactory::getApplication()->redirect('index.php');
    }

    function login($provider = null)
    {
        if (!is_object($provider))
            $provider = JFBCFactory::provider(JRequest::getCmd('provider'));
        // Let the provider do anything it wants before we try to login.
        $provider->onBeforeLogin();

        $loginRegisterModel = $this->getModel('LoginRegister', 'JFBConnectModel');

        // Set a cookie to prevent auto-logging in for the remainder of the session time
        $config = JFactory::getConfig();
        $lifetime = $config->get('lifetime', 15);
        setcookie('jfbconnect_autologin_disable', 1, time() + ($lifetime * 60));
        // Not a perfect solution, but fixes autologin loops..

        $app = JFactory::getApplication();
        $providerUserId = $provider->getProviderUserId();

        if (!$providerUserId)
        { # Facebook isn't returning information about this user.  Redirect them.
            $app->enqueueMessage(JText::sprintf('COM_JFBCONNECT_UNABLE_TO_RETRIEVE_USER', $provider->name));
            $app->redirect('index.php');
        }

        $userMapModel = JFBCFactory::usermap();

        $jUser = JFactory::getUser();
        if (!$jUser->guest) # User is already logged into Joomla. Update their mapping
        {
            SCSocialUtilities::clearJFBCNewMappingEnabled();
            if ($userMapModel->map($jUser->get('id'), $providerUserId, strtolower($provider->name), $provider->client->getToken()))
                $app->enqueueMessage(JText::sprintf('COM_JFBCONNECT_MAP_USER_SUCCESS', $provider->name));
            else
                $app->enqueueMessage(JText::sprintf('COM_JFBCONNECT_MAP_USER_FAIL', $provider->name));

            $return = $loginRegisterModel->getLoginRedirect($provider);
            $app->redirect($return);
        }

        // They're not logged in. Check if they have a Joomla user and log that user in. If not, create them one
        $jUserId = $userMapModel->getJoomlaUserId($providerUserId, strtolower($provider->name));

        if (!$jUserId)
        {
            $profile = $provider->profile->fetchProfile($providerUserId, array('email'));
            $providerEmail = $profile->get('email', null);

            # Check if automatic email mapping is allowed, and see if that email is registered
            # AND the Facebook user doesn't already have a Joomla account
            if (!$provider->initialRegistration && JFBCFactory::config()->getSetting('facebook_auto_map_by_email'))
            {
                if ($providerEmail != null)
                {
                    $jUserEmailId = $userMapModel->getJoomlaUserIdFromEmail($providerEmail);
                    if (!empty($jUserEmailId))
                    {
                        // Found a user with the same email address
                        // do final check to make sure there isn't a FB account already mapped to it
                        $tempId = $userMapModel->getProviderUserId($jUserEmailId, strtolower($provider->name));
                        if (!$tempId)
                        {
                            SCSocialUtilities::clearJFBCNewMappingEnabled();
                            if ($userMapModel->map($jUserEmailId, $providerUserId, strtolower($provider->name), $provider->client->getToken()))
                            {
                                $app->enqueueMessage(JText::sprintf('COM_JFBCONNECT_MAP_USER_SUCCESS', $provider->name));
                                $jUserId = $jUserEmailId; // Update the temp jId so that we login below
                            }
                            else
                                $app->enqueueMessage(JText::sprintf('COM_JFBCONNECT_MAP_USER_FAIL', $provider->name));

                        }
                    }
                }
            }

            // Check if no mapping, and Automatic Registration is set. If so, auto-create the new user.
            if (!$jUserId && JFBCFactory::config()->getSetting('automatic_registration'))
            { # User is not in system, should create their account automatically
                if ($loginRegisterModel->autoCreateUser($providerUserId, $provider))
                    $jUserId = $userMapModel->getJoomlaUserId($providerUserId, strtolower($provider->name));
            }

            // At this point, we have nothing left to do but redirect the user to the registration page
            if (!$jUserId)
            {
                $return = $loginRegisterModel->getLoginRedirect($provider);
                $app = JFactory::getApplication();
                $regComponent = JFBCFactory::config()->get('registration_component');
                if ($regComponent == 'jfbconnect')
                    $app->redirect(JRoute::_('index.php?option=com_jfbconnect&view=loginregister&provider=' . strtolower($provider->name) . '&return=' . base64_encode($return), false));

                else
                {
                    $app->setUserState('com_jfbconnect.registration.alternateflow', true);
                    $app->setUserState('com_jfbconnect.registration.provider.name', strtolower($provider->name));
                    $app->setUserState('com_jfbconnect.registration.provider.user_id', $providerUserId);

                    $plugins = $app->triggerEvent('socialProfilesGetPlugins');
                    foreach ($plugins as $plugin)
                    {
                        if ($plugin->getName() == $regComponent)
                            $redirect = $plugin->registration_url;
                    }
                    if ($redirect)
                        $app->redirect(JRoute::_($redirect, false));
                }
            }
        }

        $jUser = JUser::getInstance($jUserId);

        $loginSuccess = false;
        // Try to log the user, but not if blocked and initial registration (then there will be a pretty message on how to activate)
        if (!$provider->initialRegistration || ($jUser->get('block') == 0 && $provider->initialRegistration))
        {
            $options = array('silent' => 1, 'provider' => $provider, 'provider_user_id' => $providerUserId); // Disable other authentication messages
            // hack for J3.2.0 bug. Should remove after 3.2.1 is available.
            $password = $provider->secretKey;
            $loginSuccess = $app->login(array('username' => $provider->appId, 'password' => $password), $options);
        }

        if ($loginSuccess)
        {
            // lets update the user's access token with whatever we just received
            $jUser = JFactory::getUser();
            $userMapModel->updateUserToken($jUser->get('id'), $provider->systemName, $provider->client->getToken());

            // Store Profile URL and Avatar URL
            $userModel = JFBConnectModelUserMap::getUser($jUser->get('id'), $provider->name);
            $userModel->saveParameter('profile_url', JFBCFactory::provider($provider->name)->profile->getProfileUrl($providerUserId));
            $avatarSettings = new JRegistry();
            $avatarSettings->set('width', 50);
            $avatarSettings->set('height', 50);
            $userModel->saveParameter('avatar_thumb', JFBCFactory::provider($provider->name)->profile->getAvatarUrl($providerUserId, false, $avatarSettings));

            if (!$provider->initialRegistration)
            {
                $args = array(strtolower($provider->name), $jUser->get('id'), $providerUserId);
                $app->triggerEvent('socialProfilesOnLogin', $args);
            }
        }

        JFactory::getApplication()->setUserState('com_jfbconnect.' . strtolower($provider->name) . '.checkForNewMapping', null);
        $allProviders = JFBCFactory::getAllProviders();
        foreach ($allProviders as $p)
            $p->checkNewMapping();

        $redirect = $loginRegisterModel->getLoginRedirect($provider);

        // Clear the 'alternate flow' session bit to prevent further alterations to reg pages for this user/session
        $app->setUserState('com_jfbconnect.registration.alternateflow', false);

        $app->redirect($redirect);
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

    /*
* Send message to user notifying them of the new account and if they have to activate.
* If default Mail email and name are not set, grab it from a super admin in the DB.
*/

}
