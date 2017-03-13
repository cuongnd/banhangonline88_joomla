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

class JFBConnectProviderLinkedin extends JFBConnectProvider
{
    var $timestampOffset;

    function __construct()
    {
        $this->name = "LinkedIn";
        $this->usernamePrefix = "li_";

        parent::__construct();
    }

    function setupAuthentication()
    {
        $options = new JRegistry();
        $options->set('authurl', 'https://www.linkedin.com/uas/oauth2/authorization');
        $options->set('tokenurl', 'https://www.linkedin.com/uas/oauth2/accessToken');
        $options->set('authmethod', 'get');
        $options->set('getparam', 'oauth2_access_token');

        $headers = array();
        $headers['Content-Type'] = 'application/json';
        $headers['x-li-format'] = 'json';
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
    }

    function fetchNewScope($scope)
    {
        $session = JFactory::getSession();
        // With LinkedIn, we can't check if a scope is granted, so we need to always fetch new scope in addition to
        // previous scope. We also need to check if we're coming back from a previous scope fetch and just proceed if so
        // That prevents authentication looping.
        $alreadyFetching = $session->get('jfbconnect.linkedin.fetchingscope', false);
        if (!$alreadyFetching)
        {
            JFactory::getSession()->set('jfbconnect.linkedin.fetchingscope', true);
            // Clear the return code that may already be set, allowing for authentication to happen again
            JFactory::getApplication()->input->set('code', null);
            $this->client->setOption('scope', $this->profile->getRequiredScope() . ',' . implode(',', $scope));
            $this->client->authenticate();
        }
    }

    /* getProviderUserId
    * Gets the provider User IdFacebook. This is regardless of whether they are mapped to an
    *  existing Joomla account.
    */
    function getProviderUserId()
    {
        if ($this->get('providerUserId', null) == null)
        {
            $profile = $this->profile->fetchProfile('~', 'id');
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
            $initJS = array_flip(array_flip($this->addJavascriptInit())); //flip to avoid the same value

            $extraJS = '';
            if($count = count($initJS))
            {
                $extraJS = ",\n";
                $i = 0;
                foreach($initJS as $xtrajs)
                {
                    $extraJS .=  $xtrajs;
                    $extraJS .= $i == $count ? ",\n" : "\n";
                    $i++;
                }
            }

            $inJS = '<script type="text/javascript" src="//platform.linkedin.com/in.js?async=true"></script>' . "\n";
            $initJs = "IN.init({\n" .
                "api_key: '" . $this->appId . "',\n" .
                'authorize: false' .
                $extraJS .
                '});';

            $javascript = $inJS .
                '<script type="text/javascript">' .
                $initJs .
                "</script>";

            $head .= $javascript;
        }

        return $head;
    }

}