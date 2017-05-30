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
class JFBConnectProfileFacebook extends JFBConnectProfile
{
    protected function setProviderFields()
    {
        $this->providerFields = array(
                '0' => 'None',
                'name' => 'User - Full Name',
                'first_name' => 'User - First Name',
                'middle_name' => 'User - Middle Name',
                'last_name' => 'User - Last Name',
                'link' => 'User - Profile Link',
                'friends.summary.total_count' => 'User - Friend Count',
                'locale' => 'Basic Info - Locale',
                'hometown.name' => 'Basic Info - Hometown City, State', // user_hometown    /* New - hometown, now a page? */
                'location.name' => 'Basic Info - Current City, State', // user_location      /* New - location, now a page? */
                'timezone' => 'Basic Info - Timezone',
                'gender' => 'Basic Info - Sex (Male / Female)',
                'birthday' => 'Basic Info - Birthday', // user_birthday
                'political' => 'Basic Info - Political View', // user_religion_politics
                'religion' => 'Basic Info - Religious Views', // user_religion_politics
                'bio' => 'Basic Info - About Me', // user_about_me
//                'profile_blurb' => 'Basic Info - Profile Blurb', // user_about_me
                'quotes' => 'Basic Info - Favorite Quotes', // user_about_me
                'music' => 'Likes & Interests - Music', // user_likes                               /* EDGE */
                'books' => 'Likes & Interests - Books', // user_likes                               /* EDGE */
                'movies' => 'Likes & Interests - Movies', // user_likes                             /* EDGE */
                'television' => 'Likes & Interests - TV', // user_likes
                'games' => 'Likes & Interests - Games', //user_likes                                /* EDGE */
                'activities' => 'Likes & Interests - Activities', // user_activities                /* EDGE */
                'interests' => 'Likes & Interests - Interests', // user_interests                   /* EDGE */
                'relationship_status' => 'Relationship - Relationship Status', // user_relationships
                'work.0.employer.name' => 'Education and Work - Employer', // user_work_history
                'work.0.location.name' => 'Education and Work - Location', // user_work_history
                'work.0.position.name' => 'Education and Work - Position', // user_work_history
                'work.0.start_date' => 'Education and Work - Start Date', // user_work_history
                'work.0.end_date' => 'Education and Work - End Date', // user_work_history
                'education.College.school.name' => 'Education and Work - College Name',
                'education.College.concentration.0.name' => 'Education and Work - College Degree',
                'education.College.year.name' => 'Education and Work - College Year',
                'education.High School.school.name' => 'Education and Work - High School', // user_education_history
                'education.High School.year.name' => 'Education and Work - High School Year', // user_education_history
                'email' => 'Contact - Email', // email
                'website' => 'Contact - Website' // user_website
        );
    }

    public function getPermissionsForFields($fields)
    {
        $perms = array();
        if (!$fields)
            return $perms;

        foreach ($fields as $field)
        {
            if (strpos($field, "friends") !== false)
                $perms[] = "user_friends";
            if (strpos($field, "location") !== false)
                $perms[] = "user_location";
            else if (strpos($field, "hometown") !== false)
                $perms[] = "user_hometown";
            else if ($field == "activities" || $field == "birthday" || $field == "interests" || $field == "website")
                $perms[] = "user_" . $field;
            else if ($field == "bio" || $field == "quotes")
                $perms[] = "user_about_me";
            else if ($field == "religion" || $field == "political")
                $perms[] = "user_religion_politics";
            else if ($field == "relationship_status")
                $perms[] = "user_relationships";
            else if ($field == "music" || $field == "books" || $field == "movies" || $field == "tv" || $field == "games")
                $perms[] = "user_likes";
            else if (strpos($field, "work") !== false)
                $perms[] = "user_work_history";
            else if (strpos($field, "education") !== false)
                $perms[] = "user_education_history";
        }
        return $perms;
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

        self::$requiredScope = array();
        self::$requiredScope[] = "email";
        // Query to see if any actions are enabled to automatically post (reads, listen, etc). If so, request the proper permission at login.
        // Otherwise, should be requested at time of action.
        require_once(JPATH_SITE . '/components/com_jfbconnect/models/opengraphaction.php');
        $ogActionModel = new JFBConnectModelOpenGraphAction();
        $ogActions = $ogActionModel->getActions(true);
        if ($ogActions)
        {
            foreach ($ogActions as $action)
            {
                if ($action->params->get('og_auto_type', "none") == "page_load")
                {
                    self::$requiredScope[] = "publish_actions";
                    break;
                }
            }
        }

        JPluginHelper::importPlugin('socialprofiles');
        $app = JFactory::getApplication();
        $args = array('facebook');
        $perms = $app->triggerEvent('socialProfilesGetRequiredScope', $args);
        if ($perms)
        {
            foreach ($perms as $permArray)
                self::$requiredScope = array_merge(self::$requiredScope, $permArray);
        }

        $customPermsSetting = JFBCFactory::config()->getSetting('facebook_perm_custom');
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

    /*
     * Fetch a user's profile based on a profile plugin field-mapping
     * @return JRegistry with profile field values
     */
    public function fetchProfileFromFieldMap($fieldMap, $permissionGranted = true)
    {
        $fields = array();
        if (is_object($fieldMap))
        {
            foreach ($fieldMap as $field)
            {
                $fieldArray = explode('.', $field);
                if (!empty($fieldArray[0]))
                    $fields[] = $fieldArray[0]; // Get the root field to grab from FB
            }
        }
        $fbUserId = JFBCFactory::provider('facebook')->getProviderUserId();

        $fields = array_unique($fields);
        $profile = $this->fetchProfile($fbUserId, $fields);
        $profile->setFieldMap($fieldMap);
        return $profile;

    }

    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    public function fetchProfile($fbUserId, $fields)
    {
        if (in_array('full_name', $fields))
        {
            $fields[] = 'name';
            unset($fields[array_search('full_name', $fields)]);
        }

        $profile = new JFBConnectProfileDataFacebook();
        if (!empty($fields))
        {
            $colFields = implode(",", $fields);
            $data = JFBCFactory::provider('facebook')->api('/v2.1/' . $fbUserId . '?fields=' . $colFields);
            $profile->loadObject($data);
        }
        return $profile;
    }

    public function fetchStatus($providerUserId)
    {
        $response = JFBCFactory::provider('facebook')->api('/me/statuses');
        if (!isset($response['data'][0]))
            return;
        $socialStatus = $response['data'][0]['message'];
        return $socialStatus;
    }


    // Created for parity with JLinked/SourceCoast library
    // nullForDefault - If the avatar is the default image for the social network, return null instead
    // Prevents the default avatars from being imported
    function getAvatarUrl($providerUserId, $nullForDefault = false, $params = null)
    {
        if (!$params)
            $params = new JRegistry();

        $width = $params->get('width', 300);
        $height = $params->get('height', 300);

        $nullString = $nullForDefault ? 'null' : 'notnull';
        $avatarUrl = JFBCFactory::cache()->get('facebook.avatar.' . $nullString . '.' . $providerUserId . '.' . $width . 'x' . $height);
        if ($avatarUrl === false)
        {
            $avatarData = JFBCFactory::provider('facebook')->api('/' . $providerUserId . '/picture/?width=' . $width . "&height=" . $height . '&return_ssl_resources=1&redirect=false');
            if (is_array($avatarData) && array_key_exists('data', $avatarData))
            {
                if ($avatarData['data']['is_silhouette'] && $nullForDefault)
                    $avatarUrl = null;
                else
                    $avatarUrl = $avatarData['data']['url'];
            }
            JFBCFactory::cache()->store($avatarUrl, 'facebook.avatar.' . $nullString . '.' . $providerUserId . '.' . $width . 'x' . $height);
        }

        return $avatarUrl;
    }

    function getCoverPhoto($providerId)
    {
        $response = JFBCFactory::provider('facebook')->api('/' . $providerId . '/?fields=cover');
        if (array_key_exists('cover', $response) && array_key_exists('source', $response['cover']))
        {
            $cover = new JRegistry();
            $url = $response['cover']['source'];
            $cover->set('url', $url);
            $cover->set('offsetY', isset($response['cover']) && isset($response['cover']['offset_y']) ? $response['cover']['offset_y'] : 0);
            $cover->set('offsetX', isset($response['cover']) && isset($response['cover']['offset_x']) ? $response['cover']['offset_x'] : 0);
            return $cover;
        }

        return null;
    }


    function getProfileUrl($fbUserId)
    {
        $profileUrl = JFBCFactory::cache()->get('facebook.profile.' . '.' . $fbUserId);
        if ($profileUrl === false)
        {
            $profileData = JFBCFactory::provider('facebook')->api('/' . $fbUserId);
            if (is_array($profileData) && array_key_exists('link', $profileData))
            {
                $profileUrl = $profileData['link'];
            }
            JFBCFactory::cache()->store($profileUrl, 'facebook.profile.' . '.' . $fbUserId);
        }
        return $profileUrl;
    }

}

class JFBConnectProfileDataFacebook extends JFBConnectProfileData
{
    var $fieldMap;

    function get($path, $default = null)
    {
        $data = $default;
        $pageListTypes = array("music", "books", "movies", "television", "games", "activities", "interests");
        if (in_array($path, $pageListTypes))
            $data = $this->formatPageList(parent::get($path, $default));
        else if ($this->exists($path))
            $data = parent::get($path, $default);
        else if ($path == "full_name") // standardized provider value for full name
            $data = parent::get('name', $default);
        else
        {
            // Alternative fields that require extra parsing
            $parts = explode('.', $path);
            if ($parts[0] == 'education')
            {
                $edu = $this->data->education;
                foreach ($edu as $k => $node)
                {
                    if ($node->type == $parts[1])
                    {
                        unset($parts[0]);
                        unset($parts[1]);
                        $newPath = 'education.' . $k . '.' . implode('.', $parts);
                        $data = parent::get($newPath, $default);
                        break;
                    }
                }
            }
            else
                return $default;
        }

        if (!empty($data))
        {
            if (is_array($data))
            { // This is a field with multiple, comma separated values
                // Remove empty values to prevent blah, , blah as output
                unset($data['id']); // Remove id key which is useless to import
                $data = SCStringUtilities::r_implode(', ', $data);
            }
            // add custom field handlers here
            switch ($path)
            {
                case 'website':
                    $websites = explode("\n", $data);
                    if (count($websites) > 0)
                        $data = trim($websites[0]);
                    break;
            }
        }

        return $data;
    }

    private function formatPageList($pageList)
    {
        $vals = array();
        if (isset($pageList->data))
        {
            foreach ($pageList->data as $page)
            {
                $vals[] = $page->name;
            }
        }
        return implode(', ', $vals);
    }
}