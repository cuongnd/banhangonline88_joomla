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

class JFBConnectProviderGoogle extends JFBConnectProvider
{
    function __construct()
    {
        $this->name = "Google";
        $this->usernamePrefix = "g_";

        parent::__construct();
    }

    public function setupAuthentication()
    {
        $options = new JRegistry();
        $options->set('authurl', 'https://accounts.google.com/o/oauth2/auth');
        $options->set('tokenurl', 'https://accounts.google.com/o/oauth2/token');

        $scope = 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/plus.me';
        if (JFBCFactory::config()->getSetting('google_openid_fallback'))
            $scope .= " email profile";
        $options->set('scope', $scope);

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
    * Gets the provider User IdFacebook. This is regardless of whether they are mapped to an
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

    public function onAfterRender()
    {
        if (!$this->needsJavascript)
            return;

        $body = JResponse::getBody();
        $javascript = "<script type=\"text/javascript\">
              (function() {
                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = 'https://apis.google.com/js/plusone.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
              })();
            function plusone_callback(response) {
              jfbc.social.google.plusone(response);
            };
            </script>";

        $newBody = str_ireplace("</body>", $javascript . "</body>", $body, $count);
        if ($count == 1)
            JResponse::setBody($newBody);
    }

}