<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Facebook Authentication Plugin
 */
class plgAuthenticationJFBConnectAuth extends JPlugin
{
    var $configModel;

    function __construct(& $subject, $config)
    {
        // Don't even register this plugin if JFBCFactory isn't loaded and available (the jfbcsystem plugin likely isn't enabled)
        if (class_exists('JFBCFactory'))
            parent::__construct($subject, $config);
    }

    function onUserAuthenticate($credentials, $options, &$response)
    {
        $response->type = 'JFBConnectAuth';

        # authentication via facebook for Joomla always uses the FB API and secret keys
        # When this is present, the user's FB uid is used to look up their Joomla uid and log that user in
        jimport('joomla.filesystem.file');
        $provider = null;
        if (isset($options['provider']))
            $provider = $options['provider'];

        if (class_exists('JFBCFactory') && $provider)
        {
            # always check the secret username and password to indicate this is a JFBConnect login
            #echo "Entering JFBConnectAuth<br>";
            if (($credentials['username'] != $provider->appId) ||
                    ($credentials['password'] != $provider->secretKey)
            )
            {
                $response->status = JAuthentication::STATUS_FAILURE;
                return false;
            }

            #echo "Passed API/Secret key check, this is a FB login<br>";
            include_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/models/usermap.php');
            $userMapModel = new JFBConnectModelUserMap();

            $providerUserId = $provider->getProviderUserId();
            $app = JFactory::getApplication();

            #echo "Facebook user = ".$fbUserId;
            # test if user is logged into Facebook
            if ($providerUserId)
            {
                # Test if user has a Joomla mapping
                $jUserId = $userMapModel->getJoomlaUserId($providerUserId, $provider->name);
                if ($jUserId)
                {
                    $jUser = JUser::getInstance($jUserId);
                    if ($jUser->id == null) // Usermapping is wrong (likely, user was deleted)
                    {
                        $userMapModel->deleteMapping($providerUserId, $provider->name);
                        return false;
                    }

                    if ($jUser->block)
                    {
                        $isAllowed = false;
                        $app->enqueueMessage(JText::_('JERROR_NOLOGIN_BLOCKED'), 'error');
                    }
                    else
                    {
                        JPluginHelper::importPlugin('socialprofiles');
                        $args = array($provider->name, $jUserId, $providerUserId);
                        $responses = $app->triggerEvent('socialProfilesOnAuthenticate', $args);
                        $isAllowed = true;
                        foreach ($responses as $prResponse)
                        {
                            if (is_object($prResponse) && !$prResponse->status)
                            {
                                $isAllowed = false;
                                $app->enqueueMessage($prResponse->message, 'error');
                            }
                        }
                    }

                    if ($isAllowed)
                    {
                        $response->status = JAuthentication::STATUS_SUCCESS;
                        $response->username = $jUser->username;
                        $response->language = $jUser->getParam('language');
                        $response->email = $jUser->email;
                        $response->fullname = $jUser->name;
                        $response->error_message = '';
                        return true;
                    }
                }

            }
        }

        # catch everything else as an authentication failure
        $response->status = JAuthentication::STATUS_FAILURE;
        return false;
    }

}
