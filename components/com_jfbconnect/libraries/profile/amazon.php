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

// Probably should work toward extending the JFBConnectFacebookLibrary for this class.
// Too much intertwined in the root library right now.
class JFBConnectProfileAmazon extends JFBConnectProfile
{
    protected function setProviderFields()
    {
        $this->providerFields = array(
            '0' => 'None',
            'name' => 'Name',
            'email' => 'Email',
            'postal_code' => 'Postal Code'
        );
    }

    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    public function fetchProfile($user, $fields)
    {
        $profile = new JFBConnectProfileDataAmazon();
        $url = 'https://api.amazon.com/user/profile'; // get the current user
        try
        {
            $jdata = $this->provider->client->query($url);
            $data = json_decode($jdata->body, true);

            if (is_array($data))
            {
                $profile->loadObject($data);
            }
        }
        catch (Exception $e)
        {
            if (JFBCFactory::config()->get('facebook_display_errors'))
                JFactory::getApplication()->enqueueMessage($e->getMessage());
        }

        return $profile;
    }

    public function getPermissionsForFields($fields)
    {
        $perms = array();
        if (!$fields)
            return $perms;

        foreach ($fields as $field)
        {
            if ($field == "postal_code")
            {
                $perms[] = "postal_code";
                break;
            }
        }
        return $perms;
    }

    static private $requiredScope;
    public function getRequiredScope()
    {
        if (self::$requiredScope)
            return self::$requiredScope;

        self::$requiredScope = array();
        self::$requiredScope[] = "profile";

        JPluginHelper::importPlugin('socialprofiles');
        $app = JFactory::getApplication();
        $args = array('amazon');
        $perms = $app->triggerEvent('socialProfilesGetRequiredScope', $args);
        if ($perms)
        {
            foreach ($perms as $permArray)
                self::$requiredScope = array_merge(self::$requiredScope, $permArray);
        }

        return self::$requiredScope;
    }

    // nullForDefault - If the avatar is the default image for the social network, return null instead
    // Prevents the default avatars from being imported
/*    function getAvatarUrl($providerUserId, $nullForDefault = true, $params = null)
    {
        $avatarUrl = JFBCFactory::cache()->get('amazon.avatar.' . $providerUserId);
        if ($avatarUrl === false) {
            //get token from the usermap
            $token = $this->provider->client->getToken();
            if(empty($token)) {
                $token = $this->getUserAccessToken($providerUserId);
                $this->provider->client->setToken($token);
            }

            $data = $this->fetchProfile('user', 'avatar_url');
            $gravatar_id = $data->get('gravatar_id');
            $avatarUrl = !empty($gravatar_id) ? 'http://www.gravatar.com/avatar/'.$gravatar_id : $data->get('avatar_url');

            if (!$avatarUrl)
                $avatarUrl = null;

            JFBCFactory::cache()->store($avatarUrl, 'amazon.avatar.' . $providerUserId);
        }

        return $avatarUrl;
    }*/

}

class JFBConnectProfileDataAmazon extends JFBConnectProfileData
{
    function get($path, $default = null)
    {
        $data = null;
        if ($this->exists($path))
            $data = parent::get($path, $default);
        else
        {
            if ($path == 'full_name')
                $data = parent::get('name');
            else if ($path == 'first_name')
            {
                $data = parent::get('name');
                $data = substr($data, 0, strpos($data, " "));
            }
            else if ($path == 'last_name')
            {
                $data = parent::get('name');
                $data = substr($data, strpos($data, " ") + 1);
            }else if ($path == "middle_name")
            {
                $data =  "";
            }
        }

        if (!is_null($data))
        {
            // format or manipulate the data as necessary here
            return $data;
        }
        else
            return $default;
    }
}