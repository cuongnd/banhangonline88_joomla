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

jimport('sourcecoast.utilities');

include_once(JPATH_SITE . '/components/com_jfbconnect/libraries/profile.php');

class JFBConnectProvider extends JObject
{
    var $name;
    var $appId;
    var $secretKey;

    var $auth;
    var $profile;
    var $client;
    var $usernamePrefix;

    protected $providerUserId;
    var $needsJavascript = false;
    var $widgetRendered = false;
    var $extraJS = array();

    function __construct()
    {
        $this->configModel = JFBCFactory::config();
        $this->appId = $this->configModel->getSetting($this->systemName . '_app_id');
        $this->secretKey = $this->configModel->getSetting($this->systemName . '_secret_key');

        $profileClass = 'JFBConnectProfile' . ucfirst($this->systemName);
        $this->profile = new $profileClass($this);
    }

    function __get($name)
    {
        switch ($name)
        {
            case 'systemName':
                return strtolower(str_replace(" ", "-", $this->name));
            default:
                return null;
        }
    }

    /* Override this to setup specialized parameters for the OAuth client.
        Necessary in cases where the scope is needed from the profile library.. which isn't fully initialized in the provider constructor.
    */
    public function setupAuthentication()
    {
    }

    /** Channels */
    public function channel($name, $options = null)
    {
        include_once(JPATH_SITE . '/components/com_jfbconnect/libraries/provider/' . $this->systemName . '/channel/' . strtolower($name) . '.php');
        $channelName = 'JFBConnectProvider' . $this->name . 'Channel' . ucfirst($name);
        $options = $options == null ? new JRegistry() : $options;
        $channel = new $channelName($this, $options);
        return $channel;
    }

    public function getChannelsOutbound()
    {
        $channels = array();
        $path = JPATH_SITE . '/components/com_jfbconnect/libraries/provider/' . $this->systemName . '/channel/';

        if (JFolder::exists($path))
        {
            $names = JFolder::files($path, '\.php$');
            foreach ($names as $name)
            {
                $name = str_replace(".php", "", $name);
                $channel = $this->channel($name);
                if ($channel->outbound)
                    $channels[] = $channel;
            }
        }
        return $channels;
    }

    /** End Channels */

    public function getUserAccessToken($jUserId)
    {
        return JFBCFactory::usermap()->getUserAccessToken($jUserId, $this->systemName);
    }

    var $initialRegistration = false;

    function setInitialRegistration()
    {
        $this->initialRegistration = true;
    }

    public function setSessionToken()
    {
        $token = $this->client->getToken();
        JFactory::getApplication()->setUserState('com_jfbconnect.' . $this->systemName . '.token', json_encode($token));
    }

    public function onAfterInitialise()
    {

    }

    public function onAfterDispatch()
    {

    }

    public function onAfterRender()
    {

    }

    /* Scope functions */
    public function hasScope($uid, $scope)
    {
        $currentScope = $this->getUserScope($uid, $scope);
        return in_array($scope, $currentScope);
    }

    public function getUserScope($uid)
    {
        return array();
    }

    private function checkRequiredScope($scope)
    {
        // get current scope for the user
        $currentScope = $this->getUserScope($this->getProviderUserId());
        $neededScope = array();
        foreach ($scope as $s)
        {
            if (!in_array($s, $currentScope))
                $neededScope[] = $s;
        }
        return $neededScope;
    }

    public function fetchNewScope($scope)
    {
        return true;
    }

    function logoutButton($params)
    {
        return "";
    }

    function avatar($params)
    {

    }

    // This call has been replaced in v5.1 with the getLoginButtonJavascript() function
    // The new function of this is to iterate through the login providers and echo their buttons
    // This allows automatically adding the FB/Google buttons to apps that would normally add only FB
    public function getLoginButton($text = null)
    {
        $params = array('text' => $text);
        return JFBCFactory::getLoginButtons($params);
    }

    /*
     *  Introduced in 6.0, deprecated in 6.1.
     *  Please use JFBCFactory::loginButtons() instead.
     *  Right now, this will:
     *   - Show all login buttons if the provider isn't set
     *   - Show
     */
    function loginButton($params = null)
    {
        if ($params == null && $this->systemName != 'facebook')
                return "";

        $loginWidget = JFBCFactory::widget(strtolower($this->name), 'login', $params);
        return $loginWidget->render();
    }

    function connectButton($params)
    {
        if (!isset($params['providers']))
            $params['providers'] = $this->systemName;
        $params['show_reconnect'] = 'true';
        return JFBCFactory::widget('facebook', 'login', $params)->render();
    }

    public function getLoginButtonWithImage($params, $loginClass, $loginID)
    {
        if (is_array($params))
        {
            $reg = new JRegistry();
            $reg->loadArray($params);
            $params = $reg;
        }
        else if (!$params)
            $params = new JRegistry();

        $alignment = $params->get('alignment', 'left');

        $image = $params->get('image', JFBCFactory::config()->get($this->systemName . '_login_button', 'icon_label.png'));
        SCStringUtilities::loadLanguage('com_jfbconnect');
        $buttonAltTitle = JText::_('COM_JFBCONNECT_LOGIN_USING_' . strtoupper($this->name));
        return '<div class="social-login ' . $this->systemName . ' ' . $loginClass . ' pull-' . $alignment . '">
        <a id="' . $loginID . '" href="javascript:void(0)" onclick="jfbc.login.provider(\'' . $this->systemName . '\');">
            <img src="' . JURI::root(true) . '/media/sourcecoast/images/provider/' . $this->systemName . '/' . $image . '" alt="' . $buttonAltTitle . '" title="' . $buttonAltTitle . '"/></a>
            </div>';
    }

    /* getProviderUserId
    * The new way to get the userId from Facebook as of v5.1
    */
    function getProviderUserId()
    {
    }

    /* getMappedUserId
     * Return the mapped userId from Joomla
     */
    public function getMappedUserId()
    {
        $jUser = JFactory::getUser();
        if ($jUser->guest)
            return null;

        $mappedUserId = JFactory::getApplication()->getUserState('com_jfbconnect.' . $this->systemName . '.mapped_user_id', null);
        if (!$mappedUserId)
        {
            $mappedUserId = $this->getProviderUserId();
            if (JFBCFactory::usermap()->getJoomlaUserId($mappedUserId, $this->systemName) != $jUser->get('id'))
                $mappedUserId = null;

            JFactory::getApplication()->setUserState('com_jfbconnect.' . $this->systemName . '.mapped_user_id', $mappedUserId);
        }
        return $mappedUserId;
    }

    /* onBeforeLogin
    * Any functional calls that the provider needs to make directly before the user is logged in (or automatically registered)
    */
    public function onBeforeLogin()
    {
        $userModel = JFBConnectModelUserMap::getUser(JFBCFactory::usermap()->getJoomlaUserId($this->getProviderUserId(), $this->systemName), $this->systemName);
        $requiredScope = $userModel->_data->params->get('required_scope', null);
        if ($requiredScope)
        {
            $checkScope = array();
            foreach ($requiredScope as $key => $val)
            {
                $checkScope[] = $key;
            }
            // This may redirect the user back through the login flow to get the specific permissions for them.
            $neededScope = $this->checkRequiredScope($checkScope);
            if (count($neededScope))
                $this->fetchNewScope($neededScope);
        }
    }

    public function checkNewMapping()
    {
        $newMappingEnabled = JFactory::getApplication()->getUserState('com_jfbconnect.' . $this->systemName . '.checkForNewMapping', false);
        if ($newMappingEnabled)
        {
            $providerId = $this->getProviderUserId();
            if ($providerId != null)
            {
                JFBCFactory::usermap()->map(JFactory::getUser()->get('id'), $providerId, $this->systemName);
            }
        }

        JFactory::getApplication()->setUserState('com_jfbconnect.' . $this->systemName . '.checkForNewMapping', null);
    }

    public function getSocialTagRenderKey()
    {
        return $this->configModel->getSetting('social_tag_admin_key');
    }

    public function getHeadData()
    {
    }

    public function addJavascriptInit()
    {
        return $this->extraJS;
    }
}