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
class JFBConnectProfileGoogle extends JFBConnectProfile
{
    protected function setProviderFields()
    {
        $this->providerFields = array(
                '0' => 'None',
                'displayName' => 'Full Name',
                'name.givenName' => 'First Name',
                'name.familyName' => 'Last Name',
                'gender' => 'Gender',
                'aboutMe' => 'About Me',
                'birthday' => 'Birthday',
                'braggingRights' => 'Bragging Rights',
                'url' => 'Profile URL',
                'relationshipStatus' => 'Relationship Status',
                'tagline' => 'Tagline'
        );
    }

    /**
     *  Get all permissions that are required by Facebook for email, status, and/or profile, regardless
     *    of whether they're set to required in JFBConnect
     * @return string Comma separated list of FB permissions that are required
     */
    static private $requiredScope;

    public function getRequiredScope()
    {
        if (self::$requiredScope)
            return self::$requiredScope;

        JPluginHelper::importPlugin('socialprofiles');
        $app = JFactory::getApplication();
        $args = array('google');
        $perms = $app->triggerEvent('socialProfilesGetRequiredScope', $args);
        if ($perms)
        {
            foreach ($perms as $permArray)
                self::$requiredScope = array_merge(self::$requiredScope, $permArray);
        }

        $configModel = JFBCFactory::config();

        $customPermsSetting = $configModel->getSetting('google_custom_scope');
        if ($customPermsSetting != '')
        {
            $customPermsSetting = str_replace("\r\n", ',', $customPermsSetting);
            //Separate into an array to be able to merge and then take out duplicates
            $customPerms = explode(',', $customPermsSetting);
            foreach ($customPerms as $customPerm)
                self::$requiredScope[] = trim($customPerm);
        }

        self::$requiredScope = array_unique(self::$requiredScope);
        self::$requiredScope = implode(",", self::$requiredScope);

        return self::$requiredScope;
    }

    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    public function fetchProfile($socialId, $fields)
    {
        if (!is_array($fields))
            $fields = array($fields);
        $profile = new JFBConnectProfileDataGoogle();
        if (!empty($fields))
        {
            // Email has it's own custom endpoint we need to service separately
            if (in_array('email', $fields))
            {
                if ($this->provider->client->isAuthenticated())
                {
                    $url = 'https://www.googleapis.com/userinfo/email?alt=json';
                    $data = $this->provider->client->query($url);
                    $email = json_decode($data->body);
                    if (is_object($email) && property_exists($email, 'data') && property_exists($email->data, 'email'))
                    {
                        $email = $email->data->email;
                        $profile->set('email', $email);
                    }
                }
                unset($fields[array_search('email', $fields)]);
            }

            // We must always implement calls for: id, first_name, last_name, full_name and email if the provider uses different terminology
            if (in_array('first_name', $fields))
            {
                unset($fields[array_search('first_name', $fields)]);
                $fields[] = 'name';
            }
            if (in_array('middle_name', $fields))
            {
                unset($fields[array_search('middle_name', $fields)]);
                $fields[] = 'name';
            }
            if (in_array('last_name', $fields))
            {
                unset($fields[array_search('last_name', $fields)]);
                $fields[] = 'name';
            }
            if (in_array('full_name', $fields))
            {
                unset($fields[array_search('full_name', $fields)]);
                $fields[] = 'displayName';
            }

            if (!empty($fields))
            {
                $fields = array_unique($fields);
                try
                {
                	if ($this->provider->client->isAuthenticated())
                	{
                    		$url = 'https://www.googleapis.com/plus/v1/people/' . $socialId;
                    		$url .= '?fields=' . implode(',', $fields);

                    		$jdata = $this->provider->client->query($url);
                    		$data = json_decode($jdata->body, true);

                    		$profile->loadArray($data);
			        }
                }
                catch (Exception $e)
                {
                    if (JFBCFactory::config()->getSetting('google_openid_fallback'))
                    {
                        // Only time an exception should happen is if the user doesn't have a plus profile
                        $url = 'https://www.googleapis.com/oauth2/v3/userinfo';
                        //$url = 'https://www.googleapis.com/userinfo/email?alt=json';
                        $data = $this->provider->client->query($url);
                        $data = json_decode($data->body);
                        if (is_object($data))
                        {
                            if (isset($data->email))
                                $profile->set('email', $data->email);
                            else // If no email is available, we're done.. bail now.
                                return $profile;
                            if (isset($data->sub))
                                $profile->set('id', $data->sub);
                            if (isset($data->name))
                                $profile->set('full_name', $data->name);
                            if (isset($data->family_name))
                                $profile->set('last_name', $data->family_name);
                            if (isset($data->given_name) && $data->given_name != '')
                                $profile->set('first_name', $data->given_name);
                            else
                            {
                                // No given name, need to return something so Joomla doesn't choke
                                // In this case, we're just using their email handle (before the @) as their name. Not ideal, but it lets them register
                                $profile->set('first_name', substr($profile->get('email'), 0, strpos($profile->get('email'), '@')));
                                // If a name isn't set, then the 'name' field from Google is the email address.
                                // Can't use this as the full name or we'll have irate users.
                                $profile->set('full_name', $profile->get('first_name') . ' ' . $profile->get('last_name'));
                            }
                        }
                    }
                    else
                    {
//                        SCStringUtilities::loadLanguage('com_jfbconnect');
//                        JFactory::getApplication()->enqueueMessage(JText::_("COM_JFBCONNECT_GOOGLE_NO_PLUS_PROFILE"), 'error');
                    }
                }
            }
        }
        return $profile;
    }


    // Created for parity with JLinked/SourceCoast library
    // nullForDefault - If the avatar is the default image for the social network, return null instead
    // Prevents the default avatars from being imported
    function getAvatarUrl($providerUserId, $nullForDefault = false, $params = null)
    {
        if (!$params)
            $params = new JRegistry();

        $width = $params->get('width', 300);
        $nullString = $nullForDefault ? 'null' : 'notnull';
        $avatarUrl = JFBCFactory::cache()->get('google.avatar.' . $nullString . '.' . $providerUserId . '.' . $width);
        if ($avatarUrl === false)
        {
            $profile = $this->fetchProfile($providerUserId, 'image');
            $avatarUrl = $profile->get('image.url', null);
            // Check for default image
            if ($avatarUrl)
            {
                if ($nullForDefault)
                {
                    $http = new JHttp();
                    $avatar = $http->get($avatarUrl);
                    if ($avatar->code == 200 && $avatar->headers['Content-Length'] == 946)
                        $avatarUrl = null;
                }
                if ($avatarUrl)
                    $avatarUrl = str_replace("?sz=50", "?sz=" . $width, $avatarUrl); // get a suitably large image to be resized

                JFBCFactory::cache()->store($avatarUrl, 'google.avatar.' . $nullString . '.' . $providerUserId . '.' . $width);
            }
        }

        return $avatarUrl;
    }

    function getCoverPhoto($providerId)
    {
        $response = $this->fetchProfile($providerId, 'cover');
        if ($response->get('cover.coverPhoto.url', null))
        {
            $cover = new JRegistry();

            $url = $response->get('cover.coverPhoto.url');
            $cover->set('url', $url);
            $cover->set('offsetY', $response->get('cover.coverInfo.topImageOffset', 0));
            $cover->set('offsetX', $response->get('cover.coverInfo.leftImageOffset', 0));
            return $cover;
        }

        return null;
    }

    function getProfileURL($providerUserId)
    {
        return 'https://plus.google.com/' . $providerUserId;
    }
}

class JFBConnectProfileDataGoogle extends JFBConnectProfileData
{
    var $fieldMap;

    function get($path, $default = null)
    {
        if ($this->exists($path))
            $data = parent::get($path, $default);
        else
        {
            if ($path == 'full_name')
                $data = parent::get('displayName');
            else if ($path == 'first_name')
                $data = parent::get('name.givenName');
            else if ($path == 'middle_name')
                $data = parent::get('name.middleName');
            else if ($path == 'last_name')
                $data = parent::get('name.familyName');
        }

        if (!empty($data))
        {
            // format or manipulate the data as necessary here
            return $data;
        }
        else
            return $default;

    }

}
