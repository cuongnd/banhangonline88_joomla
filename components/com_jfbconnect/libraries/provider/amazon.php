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

class JFBConnectProviderAmazon extends JFBConnectProvider
{
    function __construct()
    {
        $this->name = "Amazon";
        $this->usernamePrefix = "am_";

        parent::__construct();
    }

    function setupAuthentication()
    {
        $options = new JRegistry();
        $options->set('authurl', 'https://www.amazon.com/ap/oa');
        $options->set('tokenurl', 'https://api.amazon.com/auth/o2/token');
        $options->set('authmethod', 'get');

        $headers = array();
        $headers['Content-Type'] = 'application/json';
        $options->set('headers', $headers);

        $options->set('scope', $this->profile->getRequiredScope());

        $this->client = new JFBConnectAuthenticationOauth2($options);

        $token = JFactory::getApplication()->getUserState('com_jfbconnect.' . strtolower($this->name) . '.token', null);
        if ($token)
        {
            $token = (array)json_decode($token);
            $this->client->setToken($token);
        }
        $this->client->initialize($this);

        $redirecturi = JURI::base() . 'index.php?option=com_jfbconnect&task=authenticate.callback&provider=' . strtolower($this->name);

        $uri = JURI::getInstance($redirecturi);
        $uri->setScheme('https');
        $redirecturi = $uri->toString();

        $this->client->setOption('redirecturi', $redirecturi);
    }

    /* getProviderUserId
    * Gets the provider User Id from the provider. This is regardless of whether they are mapped to an
    *  existing Joomla account.
    */
    function getProviderUserId()
    {
        if ($this->get('providerUserId', null) == null)
        {
            $profile = $this->profile->fetchProfile('user', 'user_id');
            $id = $profile->get('user_id');
            if (!empty($id))
                $this->set('providerUserId', $id);
            else
                $this->set('providerUserId', null);
        }
        return $this->get('providerUserId');
    }

}