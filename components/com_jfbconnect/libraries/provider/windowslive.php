<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2013 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class JFBConnectProviderWindowsLive extends JFBConnectProvider
{
    function __construct()
    {
        $this->name = "WindowsLive";
        $this->usernamePrefix = "wl_";

        parent::__construct();
    }

    function setupAuthentication()
    {
        $options = new JRegistry();
        $options->set('authurl', 'https://login.live.com/oauth20_authorize.srf');
        $options->set('tokenurl', 'https://login.live.com/oauth20_token.srf');
        $options->set('authmethod', 'get');

        $options->set('scope', 'wl.signin wl.basic wl.emails wl.birthday');

        $this->client = new JFBConnectAuthenticationOauth2($options);

        $token = JFactory::getApplication()->getUserState('com_jfbconnect.' . strtolower($this->name) . '.token', null);
        if ($token)
        {
            $token = (array)json_decode($token);
            $this->client->setToken($token);
        }
        $this->client->initialize($this);
    }

    /* getProviderUserId
    * Gets the provider User Id from the provider. This is regardless of whether they are mapped to an
    *  existing Joomla account.
    */
    function getProviderUserId()
    {
        if ($this->get('providerUserId', null) == null)
        {
            $profile = $this->profile->fetchProfile('me', 'id');
            $id = $profile->get('id');
            if (!empty($id))
                $this->set('providerUserId', $id);
            else
                $this->set('providerUserId', null);
        }
        return $this->get('providerUserId');
    }

    /*function setTimestampOffset($offset)
    {
        $this->timestampOffset = $offset;
    }*/

    function getHeadData()
    {
        $head = '';
        if ($this->needsJavascript)
        {
            // Add any Javascript files here
        }

        return $head;
    }

}