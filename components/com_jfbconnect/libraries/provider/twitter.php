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

class JFBConnectProviderTwitter extends JFBConnectProvider
{
    function __construct()
    {
        $this->name = "Twitter";
        $this->usernamePrefix = "t_";
        parent::__construct();
    }

    function setupAuthentication()
    {
        $this->options = new JRegistry();
        $this->options->def('accessTokenURL', 'https://api.twitter.com/oauth/access_token');
        $this->options->def('authenticateURL', 'https://api.twitter.com/oauth/authenticate');
        $this->options->def('authoriseURL', 'https://api.twitter.com/oauth/authorize');
        $this->options->def('requestTokenURL', 'https://api.twitter.com/oauth/request_token');
        $this->options->def('api.url', 'https://api.twitter.com/1.1/');
        $this->options->set('consumer_key', $this->appId);
        $this->options->set('consumer_secret', $this->secretKey);
        $this->options->set('callback', JURI::base() . 'index.php?option=com_jfbconnect&task=authenticate.callback&provider=twitter&state=' . JSession::getFormToken());
        $this->options->set('sendheaders', true); // Enabled for now. Should probably switch to force the redirect so we can just detect if user is logged in

        $this->client = new JFBConnectProviderTwitterOauth1($this->options);

        $token = JFactory::getApplication()->getUserState('com_jfbconnect.' . strtolower($this->name) . '.token', null);
        if ($token)
        {
            $token = (array)json_decode($token);
            $this->client->setToken($token);
        }
    }

    /* getProviderUserId
    * Gets the provider User IdFacebook. This is regardless of whether they are mapped to an
    *  existing Joomla account.
    */
    function getProviderUserId()
    {
        $app = JFactory::getApplication();
        if ($app->getUserState('com_jfbconnect.twitter.provider_id', null) == null)
        {
            $token = $this->client->getToken();
            if (empty($token))
                return false;
            $creds = $this->client->verifyCredentials();
            if ($creds->code == 200)
            {
                $id = $creds->body->id;
            }

            if (!empty($id))
                $app->setUserState('com_jfbconnect.twitter.provider_id', $id);
//                $this->set('providerUserId', $id);
            else
                $app->setUserState('com_jfbconnect.twitter.provider_id', null);
        }
        return $app->getUserState('com_jfbconnect.twitter.provider_id');
    }

    public function getUserScope($uid)
    {
        $scope = array();
        $creds = $this->client->verifyCredentials();
        if ($creds->code == 200)
        {
            $access = $creds->headers['x-access-level'];
            if ($access == 'read-write')
            {
                $scope[] = 'read';
                $scope[] = 'write';
            }
            else
                $scope[] = $access;
        }
        return $scope;
    }

    public function fetchNewScope($scope)
    {
        if (count($scope) > 0)
        {
            // Start the authentication process over and get the newly requested scope
            $this->client->resetConnection();
            // Use authorize which will re-prompt the user
            $this->client->setOption('authenticateURL', 'https://api.twitter.com/oauth/authorize');

            JFactory::getApplication()->setUserState('com_jfbconnect.' . strtolower($this->name) . '.token', null);

            $this->options->set('x_auth_access_type', $scope[0]); // Really, this can only be 'write' since we already request read

            $this->client->authenticate();
        }
    }

    public function onAfterRender()
    {
        if (!$this->needsJavascript)
            return;

        $body = JResponse::getBody();
        $javascript =
                <<<EOT
        <script>
        window.twttr = (function (d,s,id) {
          var t, js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;
          js.src="https://platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);
          return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });
        }(document, "script", "twitter-wjs"));
        twttr.ready(function (twttr) {
            // Now bind our custom intent events
            twttr.events.bind('tweet', jfbc.social.twitter.tweet);
        });
        </script>
EOT;
        if (preg_match('/\<body[\s\S]*?\>/i', $body, $matches))
        {
            $newBody = str_replace($matches[0], $matches[0] . $javascript, $body);
            JResponse::setBody($newBody);
        }
    }
    /** End System trigger calls */

}