<?php
/**
 * @package        JFBConnect
 * @copyright (C) 2009-2013 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Probably should work toward extending the JFBConnectFacebookLibrary for this class.
// Too much intertwined in the root library right now.
class JFBConnectProfileWindowsLive extends JFBConnectProfile
{
    protected function setProviderFields()
    {
        $this->providerFields = array(
            '0' => 'None',
            'name' => 'Name',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'gender' => 'Gender',
            'birth_day' => 'Birthday - Day',
            'birth_month' => 'Birthday - Month',
            'birth_year' => 'Birthday - Year',
            'emails.preferred' => 'Email - Preferred',
            'emails.account' => 'Email - Account',
            'emails.personal' => 'Email - Personal',
            'emails.business' => 'Email - Business',
            'emails.other' => 'Email - Other'
        );
    }

    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    public function fetchProfile($userId, $fields)
    {
        $profile = new JFBConnectProfileDataWindowsLive();

        $url = 'https://apis.live.net/v5.0/'.$userId;
        try
        {
            $jdata = $this->provider->client->query($url);

            if ($jdata->code == 200)
            {
                $data = json_decode($jdata->body, true);
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
    function getAvatarUrl($providerId, $nullForDefault = true, $params = null)
    {
        $nullString = $nullForDefault ? 'null' : 'notnull';
        $avatarUrl = JFBCFactory::cache()->get('windowslive.avatar.' . $nullString . '.' . $providerId);
        if ($avatarUrl === false)
        {
            $avatarUrl =  "https://apis.live.net/v5.0/{$providerId}/picture";
            JFBCFactory::cache()->store($avatarUrl, 'windowslive.avatar.' . $nullString . '.' . $providerId);
        }
        return $avatarUrl;
    }

    function getProfileUrl($providerId)
    {
        return "http://cid-{$providerId}.profile.live.com/";
    }

}

class JFBConnectProfileDataWindowslive extends JFBConnectProfileData
{
    public function get($path, $default = "")
    {
        $data = null;
        if ($this->exists($path))
            $data = parent::get($path, $default);
        else
        {
            if ($path == 'full_name')
            {
                $data =  parent::get('name');
            }
            else if ($path == 'middle_name')
            {
                $data =  "";
            }
            else if($path == 'birthday')
            {
                $birth_day = parent::get('birth_day');
                $birth_month = parent::get('birth_month');
                $birth_year = parent::get('birth_year');
                $data = sprintf("%s-%s-%s",$birth_year, $birth_day, $birth_month);
            }
            else if($path == 'email'){
                $emails =  parent::get('emails');
                $data = $emails->preferred;
            }
            else
            {
                $pos = strpos($path, '.');
                if($pos !== false) //found
                {
                    $parts = explode('.', $path);

                    if($parts[0] == 'emails')
                    {
                        $emails = parent::get($parts[0]);
                        $data = isset($emails->$parts[1]) ? $emails->$parts[1] : $default;
                    }
                }
            }
        }

        return $data;
    }

}