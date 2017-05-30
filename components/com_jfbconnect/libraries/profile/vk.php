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
class JFBConnectProfileVk extends JFBConnectProfile
{
    protected function setProviderFields()
    {
        $this->providerFields = array(
            '0' => 'None',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'nickname' => 'Nickname',
            'screen_name' => 'Screen Name',
            'sex' => 'Gender',
            'bdate' => 'Birthdate',
            'site' => 'Website',
            'home_phone' => 'Home Phone Number',
            'home_town' => 'Home Town',
            'relation' => 'Relationship Status',
            'status' => 'Current Status Text',
            'activities' => 'Activities',
            'interests' => 'Interests',
            'music' => 'Music',
            'movies' => 'Movies',
            'tv' => 'TV',
            'books' => 'Books',
            'games' => 'Games',
            'about' => 'About',
        );
    }

    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    public function fetchProfile($userId, $fields)
    {
        if (!is_array($fields))
            $fields = array($fields);

        if (in_array('full_name', $fields))
        {
            $fields[] = 'first_name';
            $fields[] = 'last_name';
            unset($fields[array_search('full_name', $fields)]);
        }
        $fields = array_unique($fields);

        $profile = new JFBConnectProfileDataVk();
        if (!empty($fields))
        {
            $url = 'https://api.vk.com/method/users.get';
            $url .= '?uid=' . $userId;
            $url .= '&fields=' . implode(',', $fields);
            try
            {
                $data = $this->provider->client->query($url);
                if ($data->code == 200)
                {
                    $data = json_decode($data->body, true);
                    $profile->loadObject($data['response'][0]);
                }
            }
            catch (Exception $e)
            {
                if (JFBCFactory::config()->get('facebook_display_errors'))
                    JFactory::getApplication()->enqueueMessage($e->getMessage());
            }
        }
        return $profile;
    }

    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    public function fetchStatus($providerUserId)
    {
        $status = "";
        $url = 'https://api.vk.com/method/wall.get';
        $url .= '?owner_id=' . $providerUserId;
        $url .= '&count=10&extended=0&filter=owner';
        try
        {
            $data = $this->provider->client->query($url);
            if ($data->code == 200)
            {
                $posts = json_decode($data->body, true);
                foreach ($posts['response'] as $post)
                {
                    if (is_array($post))
                    {
                        if ($post['post_type'] == 'post' && $post['text'] !== "")
                        {
                            $status = $post['text'];
                            break;
                        }
                    }
                }

            }

        }
        catch (Exception $e)
        {
            if (JFBCFactory::config()->get('facebook_display_errors'))
                JFactory::getApplication()->enqueueMessage($e->getMessage());
        }

        return $status;
    }


    // nullForDefault - If the avatar is the default image for the social network, return null instead
    // Prevents the default avatars from being imported
    function getAvatarUrl($providerId, $nullForDefault = true, $params = null)
    {
        $nullString = $nullForDefault ? 'null' : 'notnull';
        $avatarUrl = JFBCFactory::cache()->get('vk.avatar.' . $nullString . '.' . $providerId);
        if ($avatarUrl === false)
        {
            $data = $this->fetchProfile($providerId, "photo_big");
            $avatarUrl = $data->get('photo_big', null);
            if ($nullForDefault && (!$avatarUrl || strpos($avatarUrl, 'vk.com/images/camera_')))
                $avatarUrl = null;
            JFBCFactory::cache()->store($avatarUrl, 'vk.avatar.' . $nullString . '.' . $providerId);
        }
        return $avatarUrl;
    }

    function getProfileUrl($memberId)
    {
        return 'http://vk.com/id' . $memberId;
    }

}

class JFBConnectProfileDataVk extends JFBConnectProfileData
{
    public function get($path, $default = "")
    {
        $value = $default;

        if ($path == 'full_name')
            return parent::get('first_name') . ' ' . parent::get('last_name');
        else if ($path == 'email')
            return null; // This isn't available from VK
        else if ($path == "middle_name")
            return ""; // This isn't available from VK

        $pathParts = explode('.', $path);
        if ($this->exists($pathParts[0]))
        {
            $value = parent::get($pathParts[0], $default);

            if ($path == 'sex')
            {
                if ($value == 0)
                    return "Not specified";
                else if ($value == 1)
                    return "Female";
                else if ($value == 2)
                    return "Male";
            }
            else if ($path == 'relation')
            {
                if ($value == 1)
                    return "Single";
                else if($value == 2)
                    return "In a Relationship";
                else if($value == 3)
                    return "Engaged";
                else if($value == 4)
                    return "Married";
                else if($value == 5)
                    return "It's Complicated";
                else if($value == 6)
                    return "Actively Searching";
                else if($value == 7)
                    return "In Love";
            }
            else if ($path == 'bdate')
            {
                // Always returned in DD.MM.YYYY or DD.MM format. We want to return as Y-m-d format
                if (strpos($value, '.')) // this means a real value was sent back
                {
                    $parts = explode('.', $value);
                    $date = $parts[1] . "-" . $parts[0];
                    if (count($parts) == 3)
                        $date = $parts[2] . '-' . $date;
                    return $date;
                }
            }

        }

        return $value;
    }

}