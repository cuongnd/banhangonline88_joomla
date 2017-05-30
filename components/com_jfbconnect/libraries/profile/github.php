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
class JFBConnectProfileGithub extends JFBConnectProfile
{
    protected function setProviderFields()
    {
        $this->providerFields = array(
            '0' => 'None',
            'name' => 'Name',
            'company' => 'Company',
            'blog' => 'Blog',
            'location' => 'Location',
            'hireable' => 'Hireable',
            'created_at' => 'Registration Date'
        );
    }

    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    public function fetchProfile($user, $fields)
    {
        $profile = new JFBConnectProfileDataGithub();
        $url = 'https://api.github.com/user'; // get the current user			
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

    // nullForDefault - If the avatar is the default image for the social network, return null instead
    // Prevents the default avatars from being imported
    function getAvatarUrl($providerUserId, $nullForDefault = true, $params = null)
    {
        $avatarUrl = JFBCFactory::cache()->get('github.avatar.' . $providerUserId);
        if ($avatarUrl === false) {
            $data = $this->fetchProfile('user', 'avatar_url');
            $gravatar_id = $data->get('gravatar_id');
            $avatarUrl = !empty($gravatar_id) ? 'http://www.gravatar.com/avatar/'.$gravatar_id : $data->get('avatar_url');

            if (!$avatarUrl)
                $avatarUrl = null;

            JFBCFactory::cache()->store($avatarUrl, 'github.avatar.' . $providerUserId);
        }

        return $avatarUrl;
    }

    function getProfileUrl($providerUserId)
    {
        $profileUrl = JFBCFactory::cache()->get('github.profile.' . $providerUserId);
        if ($profileUrl === false) {
            $profile = $this->fetchProfile('user', 'login');
            $username = $profile->get('login');
            $profileUrl = 'https://github.com/' . $username;

            if (!$profileUrl)
                $profileUrl = null;

            JFBCFactory::cache()->store($profileUrl, 'github.profile.' . $providerUserId);
        }

        return $profileUrl;
    }
}

class JFBConnectProfileDataGithub extends JFBConnectProfileData
{
    function get($path, $default = null)
    {
        $data = null;
        if ($this->exists($path))

            if ($path == 'created_at') {
                $data = parent::get('created_at');
                $jdate = new JDate($data);
                $data = $jdate->format(JText::_('DATE_FORMAT_LC'));
            }else{
                $data = parent::get($path, $default);
            }
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