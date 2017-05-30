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

class JFBConnectProviderInstagram extends JFBConnectProvider
{
    function __construct()
    {
        $this->name = "Instagram";
        $this->usernamePrefix = "instagram_";

        parent::__construct();
    }

    function setupAuthentication()
    {
        $options = new JRegistry();
        $options->set('authurl', 'https://api.instagram.com/oauth/authorize');
        $options->set('tokenurl', 'https://api.instagram.com/oauth/access_token');
        $options->set('authmethod', 'get');

        $headers = array();
        $headers['Content-Type'] = 'application/json';
        $options->set('headers', $headers);

        $options->set('scope', '');

        $this->client = new JFBConnectAuthenticationOauth2($options);

        $token = JFactory::getApplication()->getUserState('com_jfbconnect.' . strtolower($this->name) . '.token', null);
        if ($token)
        {
            $token = (array)json_decode($token);
            $this->client->setToken($token);
        }
        $this->client->initialize($this);
        // Need to override the callback URL to force http or https
        $origRedirect = $this->client->getOption('redirecturi');
        if (JFBCFactory::config()->get('instagram_callback_ssl'))
            $redirect = str_replace('http://', 'https://', $origRedirect);
        else
            $redirect = str_replace('https://', 'http://', $origRedirect);
        $this->client->setOption('redirecturi', $redirect);

    }

    /* getProviderUserId
    * Gets the provider User Id from the provider. This is regardless of whether they are mapped to an
    *  existing Joomla account.
    */
    function getProviderUserId()
    {
        $token = $this->client->getToken();
        $user = (array) $token['user'];
        $userId = isset($user['id']) ? $user['id'] : null;

        return $userId;
    }

}