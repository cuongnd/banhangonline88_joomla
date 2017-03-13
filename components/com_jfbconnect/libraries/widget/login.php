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

class JFBConnectProviderWidgetLogin extends JFBConnectWidget
{
    var $name = 'Login';
    var $systemName = "login";

    var $providerLoginInfo;

    function __construct($provider, $fields, $className)
    {
        parent::__construct($provider, $fields);

        $this->className = $className;

        $this->providerLoginInfo = array();
        $this->providerLoginInfo['Amazon'] = new LoginButtonInfo('scAmazonLoginTag', 'SCAmazonLogin', 'sc_amazonlogin', 'COM_JFBCONNECT_LOGIN_USING_AMAZON', 'scAmazonLogin', 'scAmazonLogoutButton');
        $this->providerLoginInfo['Facebook'] = new LoginButtonInfo('jfbcLogin', 'JFBCLogin', 'sc_fblogin', 'COM_JFBCONNECT_LOGIN_USING_FACEBOOK', 'jfbcLogin', 'jfbcLogoutButton');
        $this->providerLoginInfo['Github'] = new LoginButtonInfo('scGithubLoginTag', 'SCGithubLogin', 'sc_githublogin', 'COM_JFBCONNECT_LOGIN_USING_GITHUB', 'scGithubLogin', 'scGithubLogoutButton');
        $this->providerLoginInfo['Google'] = new LoginButtonInfo('scGoogleLoginTag', 'SCGoogleLogin', 'sc_gologin', 'COM_JFBCONNECT_LOGIN_USING_GOOGLE', 'scGoogleLogin', 'scGoogleLogoutButton');
        $this->providerLoginInfo['Instagram'] = new LoginButtonInfo('scInstagramLoginTag', 'SCInstagramLogin', 'sc_instagramlogin', 'COM_JFBCONNECT_LOGIN_USING_INSTAGRAM', 'scInstagramLogin', 'scInstagramLogoutButton');
        $this->providerLoginInfo['LinkedIn'] = new LoginButtonInfo('jLinkedLogin', 'JLinkedLogin', 'sc_lilogin', 'COM_JFBCONNECT_LOGIN_USING_LINKEDIN', 'scLinkedInLogin', 'scLinkedInLogoutButton');
        $this->providerLoginInfo['Meetup'] = new LoginButtonInfo('scMeetupLoginTag', 'SCMeetupLogin', 'sc_melogin', 'COM_JFBCONNECT_LOGIN_USING_MEETUP', 'scMeetupLogin', 'scMeetupLogoutButton');
        $this->providerLoginInfo['Twitter'] = new LoginButtonInfo('scTwitterLoginTag', 'SCTwitterLogin', 'sc_twlogin', 'COM_JFBCONNECT_LOGIN_USING_TWITTER', 'scTwitterLogin', 'scTwitterLogoutButton');
        $this->providerLoginInfo['VK'] = new LoginButtonInfo('scVkLoginTag', 'SCVkLogin', 'sc_vklogin', 'COM_JFBCONNECT_LOGIN_USING_VK', 'scVkLogin', 'scVkLogoutButton');
        $this->providerLoginInfo['WindowsLive'] = new LoginButtonInfo('scWindowsLiveLoginTag', 'SCWindowsLiveLogin', 'sc_wllogin', 'COM_JFBCONNECT_LOGIN_USING_WINDOWSLIVE', 'scWindowsLiveLogin', 'scWindowsLiveLogoutButton');
    }

    public function render()
    {
        return $this->getTagHtml();
    }

    protected function getTagHtml()
    {
        $user = JFactory::getUser();
        $buttonHtml = '';

        $providers = $this->getSelectedProviders($this->getParamValue('providers'));
        if ($user->guest) // Only show login button if user isn't logged in (no remapping for now)
        {
            $buttonHtml = $this->getLoginButtons($providers);
        }
        else // logged in. Show logout button and/or reconnect buttons as configured
        {
            $showReconnect = $this->getParamValueEx('show_reconnect', null, 'boolean', 'false');
            $showLogoutButton = $this->getParamValueEx('logout', null, 'boolean', 'false');
            $logoutUrl = $this->getParamValueEx('logout_url', null, null, JURI::root());

            if ($showLogoutButton == 'true')
            {
                $logoutUrl = base64_encode(JRoute::_($logoutUrl, false));
                $logoutButtonId = $this->providerLoginInfo[$this->provider->name]->logoutButtonId;
                $buttonHtml = '<input type="submit" name="Submit" id="' . $logoutButtonId . '" class="button btn btn-primary" value="'
                    . JText::_('JLOGOUT') . "\" onclick=\"javascript:jfbc.login.logout('" . $logoutUrl . "')\" />";
            }
            if ($showReconnect == "true")
            {
                // get all providers and check if mapping exists. Then, get the buttons for providers the user hasn't connected with
                $mapProviders = array();
                foreach ($providers as $provider)
                {
                    $userData = JFBCFactory::usermap()->getUser(JFactory::getUser()->id, $provider->systemName)->_data;
                    if (empty($userData->provider_user_id))
                        $mapProviders[] = $provider;
                }
                $buttonHtml = $this->getLoginButtons($mapProviders, ' reconnect');
            }
        }
        return $buttonHtml;
    }

    private function getSelectedProviders($providerList)
    {
        if (!is_array($providerList))
        {
            $providerList = str_replace("\r\n", ",", $providerList);
            $providerList = explode(',', $providerList);
        }
        $providers = array();
        foreach ($providerList as $p)
        {
            if ($p)
                $providers[] = JFBCFactory::provider(trim($p));
        }

        //For backwards compatibility:
        // - For Facebook, if no providers are specified, then show all providers
        // - For Others, if no providers are specified, just show the login button for that provider
        if (empty($providers))
        {
            if ($this->provider->name == 'Facebook')
                $providers = JFBCFactory::getAllProviders();
            else
                $providers[] = $this->provider;
        }

        return $providers;
    }

    // The new function of this is to iterate through the login providers and echo their buttons
    // This allows automatically adding the FB/Google buttons to apps that would normally add only FB
    private function getLoginButtons($providers, $extraClass = null)
    {
        $html = "";

        if ($this->fields->get('loginbuttonstype', 'default') == 'custom')
            $customImages = $this->fields->get('loginbuttons');
        else
            $customImages = null;

        foreach ($providers as $p)
        {
            if (!$p->appId) // Don't load provider if AppId isn't set as it won't allow logins anyways
                continue;

            $pName = $p->systemName;

            if ($customImages && isset($customImages->$pName))
            {
                $this->fields->set('image', $customImages->$pName);
            }
            else
            {
                $name = $this->getParamValueEx('image', null, null, null);
                $this->fields->set('image', $name);
            }

            $html .= $p->getLoginButtonWithImage($this->fields, $this->providerLoginInfo[$p->name]->loginButtonClass . $extraClass, $this->providerLoginInfo[$p->name]->loginButtonId);
        }

        $text = $this->getParamValueEx('text', null, null, null);
        /*        if ($text == null)
                {
                    list(, $caller) = debug_backtrace(false);
                    // Check if this is the JomSocial homepage calling us, and if so, add the JText'ed Login with string
                    if (array_key_exists('class', $caller) && $caller['class'] == 'CommunityViewFrontpage')
                    {
                        SCStringUtilities::loadLanguage('com_jfbconnect');
                        $text = JText::_('COM_JFBCONNECT_LOGIN_WITH');
                    }
                }*/

        if(!empty($html))
        {
            $text = empty($text) ? "" : '<div class="pull-left intro">' . $text . '</div>';
            $html = '<span class="sourcecoast login"><div class="row-fluid">' . $text . $html . '</div></span>';
        }
        return $html;
    }

}

class LoginButtonInfo
{

    //Need to set these in widget render divs
    var $className;
    var $tagName;
    var $examples;

    var $loginButtonId;
    var $loginButtonText;
    var $loginButtonClass;
    var $logoutButtonId;

    function __construct($className, $tagName, $id, $loginTextKey, $loginButtonClass, $logoutButtonId)
    {
        $this->className = $className;
        $this->tagName = strtolower($tagName);
        $this->loginButtonId = $id;
        $this->loginButtonClass = $loginButtonClass;
        $this->logoutButtonId = $logoutButtonId;

        SCStringUtilities::loadLanguage('com_jfbconnect');
        $this->loginButtonText = JText::_($loginTextKey);

        $this->examples = array(
            '{' . $tagName . '}',
            '{' . $tagName . ' logout=true logout_url=http://www.sourcecoast.com}'
        );

    }
}
