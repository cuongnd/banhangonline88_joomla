<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('sourcecoast.utilities');

/**
 * Facebook User Plugin
 */
class plgUserJfbconnectUser extends JPlugin
{
    function __construct(& $subject, $config)
    {
        // Don't even register this plugin if JFBCFactory isn't loaded and available (the jfbcsystem plugin likely isn't enabled)
        if (class_exists('JFBCFactory'))
            parent::__construct($subject, $config);
    }

    function onUserAfterSave($user, $isnew, $success, $msg)
    {
        if (!$isnew)
            return true;

        $app = JFactory::getApplication();
        if ($app->getUserState('com_jfbconnect.registration.alternateflow', false))
        {
            $provider = $app->getUserState('com_jfbconnect.registration.provider.name', null);
            $providerUserId = $app->getUserState('com_jfbconnect.registration.provider.user_id', null);

            if ($provider && $providerUserId)
            {
                $provider = JFBCFactory::provider($provider);
                if ($user['id'] && $provider->getProviderUserId() == $providerUserId) // Sanity check
                {
                    JFBCFactory::usermap()->map($user['id'], $providerUserId, $provider->name, $provider->client->getToken());
                    // If that worked, now call the originating plugin and tell it to finalize anything with the new user
                    $args = array($provider->name, $user['id'], $providerUserId);
                    $app->triggerEvent('socialProfilesOnNewUserSave', $args);
                }
            }
        }
    }

    function onUserLogout($user, $options = array())
    {
        // Disable auto-logins for session length after a logout. Prevents auto-logins
        $config = JFactory::getConfig();
        $lifetime = $config->get('lifetime', 15);
        setcookie('jfbconnect_autologin_disable', 1, time() + ($lifetime * 60));
        setcookie('jfbconnect_permissions_granted', '', time() - 10000, "/"); // clear the granted permissions cookie

        // Tell Facebook to delete session information stored for this user.
        $factoryFile = JPATH_ROOT . '/components/com_jfbconnect/libraries/factory.php';
        require_once($factoryFile);
        JFBCFactory::provider('facebook')->client->destroySession();

        return true;
    }

    function onUserBeforeDelete($user)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete($db->qn('#__jfbconnect_user_map'))
                ->where($db->qn('j_user_id') . "=" . $db->q($user['id']));
        $db->setQuery($query);
        $db->execute();

        // Remove other user data from open graph tables
        $query = $db->getQuery(true);
        $query->delete($db->qn('#__opengraph_activity'))
                ->where($db->qn('user_id') . "=" . $db->q($user['id']));
        $db->setQuery($query);
        $db->execute();
    }

}
