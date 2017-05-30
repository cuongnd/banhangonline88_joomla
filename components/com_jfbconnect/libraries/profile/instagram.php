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
class JFBConnectProfileInstagram extends JFBConnectProfile
{
    protected function setProviderFields()
    {
        $this->providerFields = array(
            '0' => 'None',
            'full_name' => 'Name',
            'bio' => 'Bio',
            'website' => 'Website'
        );
    }

    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    public function fetchProfile($userId, $fields)
    {
        $profile = new JFBConnectProfileDataInstagram();
        $url = 'https://api.instagram.com/v1/users/'.$userId; // get the current user
        try
        {
            $jdata = $this->provider->client->query($url);

            if ($jdata->code == 200)
            {
                $data = json_decode($jdata->body, true);
                $profile->loadObject($data['data']);
            }
        }
        catch (Exception $e)
        {
            if (JFBCFactory::config()->get('facebook_display_errors'))
                JFactory::getApplication()->enqueueMessage($e->getMessage());
        }

        return $profile;
    }

    // nullForDefault - If the avatar is the default image for the social network, return null instead
    // Prevents the default avatars from being imported
    function getAvatarUrl($providerId, $nullForDefault = true, $params = null)
    {
        $nullString = $nullForDefault ? 'null' : 'notnull';
        $avatarUrl = JFBCFactory::cache()->get('instagram.avatar.' . $nullString . '.' . $providerId);
        if ($avatarUrl === false)
        {
            $token = $this->provider->client->getToken();

            if(!empty($token))
            {
                //instragram token includes user data
                //get username from token
                $user = (array) $token['user'];
                $avatarUrl = $user['profile_picture'];

                //http://images.ak.instagram.com/profiles/anonymousUser.jpg
                if ($nullForDefault && (!$avatarUrl || strpos($avatarUrl, 'instagram.com/profiles/anonymousUser')))
                    $avatarUrl = null;
                JFBCFactory::cache()->store($avatarUrl, 'instagram.avatar.' . $nullString . '.' . $providerId);
            }
        }
        return $avatarUrl;
    }

    function getProfileUrl($providerUserId)
    {
        $profileUrl = JFBCFactory::cache()->get('instagram.profile.'.$providerUserId);
        $token = $this->provider->client->getToken();

        if(!empty($token))
        {
            //instragram token includes user data
            //get username from token
            $user = (array) $token['user'];

            $profileUrl = 'https://instagram.com/' . $user['username'];
            JFBCFactory::cache()->store($profileUrl, 'instagram.profile.' . $providerUserId);
        }
        return $profileUrl;
    }

}

class JFBConnectProfileDataInstagram extends JFBConnectProfileData
{
    public function get($path, $default = "")
    {
        $data = null;
        if ($this->exists($path))
            $data = parent::get($path, $default);
        else
        {
            if ($path == 'first_name')
            {
                $data = parent::get('full_name');
                $data = substr($data, 0, strpos($data, " "));
            }
            else if ($path == 'last_name')
            {
                $data = parent::get('full_name');
                $data = substr($data, strpos($data, " ") + 1);
            }else if ($path == "middle_name")
            {
                $data =  "";
            }
        }

        return $data;
    }

}