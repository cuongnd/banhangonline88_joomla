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
class JFBConnectProfileLinkedin extends JFBConnectProfile
{
    protected function setProviderFields()
    {
        $this->providerFields = array(
                '0' => 'None',
                'first-name' => 'Basic Info - First Name',
                'last-name' => 'Basic Info - Last Name',
                'maiden-name' => 'Basic Info - Maiden Name',
                'formatted-name' => 'Basic Info - Formatted Name',
                'headline' => 'Basic Info - Headline',
                'location.name' => 'Basic Info - Current Location',
                'location.country.code' => 'Basic Info - Current Country Code',
                'industry' => 'Basic Info - Industry',
                'current-share.comment' => 'Basic Info - Current Status/Last Share',
                'num-connections' => 'Basic Info - Number of Connections',
            //        'num-connections-capped' => 'Basic Info - Number of Connections (Capped)',
                'summary' => 'Basic Info - Summary',
                'specialties' => 'Basic Info - Specialties',
                'picture-url' => 'Basic Info - Profile Picture URL',
                'site-standard-profile-request' => 'Basic Info - Authenticated Profile URL',
                'public-profile-url' => 'Basic Info - Public Profile URL',
            //Email Fields - r_emailaddress permission
                'email-address' => 'Email Fields - Primary Email Address',
            //Full Profile Fields - f_fullprofile permission
                'proposal-comments' => 'Full Info - Proposal Approach',
                'associations' => 'Full Info - Associations',
                'interests' => 'Full Info - Interests',
                'skills' => 'Full Info - Skills',
                'num-recommenders' => 'Full Info - Number of Recommendations',
                'date-of-birth' => 'Full Info - Date of Birth',
            /* Collections */
                'honors-awards.0' => 'Full Info - Honors - 0 - Summary',
                'honors-awards.1' => 'Full Info - Honors - 1 - Summary',
                'honors-awards.2' => 'Full Info - Honors - 2 - Summary',
                'three-current-positions.0' => 'Current Position - 0 - Summary',
                'three-current-positions.1' => 'Current Position - 1 - Summary',
                'three-current-positions.2' => 'Current Position - 2 - Summary',
                'three-past-positions.0' => 'Past Position - 0 - Summary',
                'three-past-positions.1' => 'Past Position - 1 - Summary',
                'three-past-positions.2' => 'Past Position - 2 - Summary',
                'publications.0' => 'Publication - 0 - Summary',
                'publications.1' => 'Publication - 1 - Summary',
                'publications.2' => 'Publication - 2 - Summary',
                'patents.0' => 'Patents - 0 - Summary',
                'patents.1' => 'Patents - 1 - Summary',
                'patents.2' => 'Patents - 2 - Summary',
                'languages.0' => 'Language - 0 - Summary',
                'languages.1' => 'Language - 1 - Summary',
                'languages.2' => 'Language - 2 - Summary',
                'certifications.0' => 'Certifications - 0 - Summary',
                'certifications.1' => 'Certifications - 1 - Summary',
                'certifications.2' => 'Certifications - 2 - Summary',
                'educations.0' => 'Educations - 0 - Summary',
                'educations.1' => 'Educations - 1 - Summary',
                'educations.2' => 'Educations - 2 - Summary',
                'courses.0' => 'Courses - 0 - Summary',
                'courses.1' => 'Courses - 1 - Summary',
                'courses.2' => 'Courses - 2 - Summary',
                'volunteer.0' => 'Volunteer Experience - 0 - Summary',
                'volunteer.1' => 'Volunteer Experience - 1 - Summary',
                'volunteer.2' => 'Volunteer Experience - 2 - Summary',
                'recommendations-received.0' => 'Recommendations - 0 - Summary',
                'recommendations-received.1' => 'Recommendations - 1 - Summary',
                'recommendations-received.2' => 'Recommendations - 2 - Summary',
            //Contact Info - r_contactinfo permission
                'main-address' => 'Contact Info - Main Address',
                'phone-numbers' => 'Phone Number - 0 - Summary',
            //'phone-numbers.1' => 'Phone Number - 1 - Summary',
            //'phone-numbers.2' => 'Phone Number - 2 - Summary',
                'im-accounts.0' => 'IM Accounts - 0 - Summary',
                'im-accounts.1' => 'IM Accounts - 1 - Summary',
                'im-accounts.2' => 'IM Accounts - 2 - Summary',
                'twitter-accounts.0' => 'Twitter Accounts - 0 - Summary',
                'twitter-accounts.1' => 'Twitter Accounts - 1 - Summary',
                'twitter-accounts.2' => 'Twitter Accounts - 2 - Summary',
                'primary-twitter-account' => 'Twitter Accounts - Primary'
        );
    }


    public function getPermissionsForFields($fields)
    {
        $scope = array();
        if (!$fields)
            return $scope;

        foreach ($fields as $field)
        {
            if ($field == 'proposal-comments' ||
                    $field == 'associations' ||
                    $field == 'interests' ||
                    $field == 'skills' ||
                    $field == 'num-recommenders' ||
                    $field == 'date-of-birth' ||
                    strpos($field, 'three-current-positions') !== false ||
                    strpos($field, 'three-past-positions') !== false ||
                    strpos($field, 'publications') !== false ||
                    strpos($field, 'patents') !== false ||
                    strpos($field, 'languages') !== false ||
                    strpos($field, 'certifications') !== false ||
                    strpos($field, 'educations') !== false ||
                    strpos($field, 'courses') !== false ||
                    strpos($field, 'volunteer') !== false ||
                    strpos($field, 'honors-awards') !== false ||
                    strpos($field, 'recommendations-received') !== false
            )
            {
                $scope[] = 'r_fullprofile';
            }
            else if ($field == 'main-address' ||
                    $field == 'primary-twitter-account' ||
                    strpos($field, 'phone-numbers') !== false ||
                    strpos($field, 'im-accounts') !== false ||
                    strpos($field, 'twitter-accounts') !== false
            )
            {
                $scope[] = 'r_contactinfo';
            }
        }
        return $scope;
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
        self::$requiredScope[] = "r_emailaddress";

        JPluginHelper::importPlugin('socialprofiles');
        $app = JFactory::getApplication();
        $args = array('linkedin');
        $perms = $app->triggerEvent('socialProfilesGetRequiredScope', $args);
        if ($perms)
        {
            foreach ($perms as $permArray)
                self::$requiredScope = array_merge(self::$requiredScope, $permArray);
        }

        $customPermsSetting = JFBCFactory::config()->getSetting('linkedin_perm_custom');
        if ($customPermsSetting != '')
        {
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
    public function fetchProfile($userId, $fields)
    {
        if (!is_array($fields))
            $fields = array($fields);

        if (in_array('first_name', $fields))
        {
            $fields[] = 'first-name';
            unset($fields[array_search('first_name', $fields)]);
        }
        if (in_array('last_name', $fields))
        {
            $fields[] = 'last-name';
            unset($fields[array_search('last_name', $fields)]);
        }
        if (in_array('full_name', $fields))
        {
            $fields[] = 'first-name';
            $fields[] = 'last-name';
            unset($fields[array_search('full_name', $fields)]);
        }
        if (in_array('email', $fields))
        {
            $fields[] = 'email-address';
            unset($fields[array_search('email', $fields)]);
        }
        if (in_array('middle_name', $fields))
            unset($fields[array_search('middle_name', $fields)]);

        if (in_array('publications', $fields))
        {
            $fields[] = 'publications:(id,title,publisher,authors,date,url,summary)';
            unset($fields[array_search('publications', $fields)]);
        }

        if (in_array('patents', $fields))
        {
            $fields[] = 'patents:(id,title,summary,number,status,office,inventors,date,url)';
            unset($fields[array_search('patents', $fields)]);
        }

        if (in_array('languages', $fields))
        {
            $fields[] = 'languages:(id,language,proficiency)';
            unset($fields[array_search('languages', $fields)]);
        }

        if (in_array('certifications', $fields))
        {
            $fields[] = 'certifications:(id,name,authority,number,start-date,end-date)';
            unset($fields[array_search('certifications', $fields)]);
        }

        if (in_array('honors-awards', $fields))
        {
            $fields[] = 'honors-awards:(id,name,issuer,date,description)';
            unset($fields[array_search('honors-awards', $fields)]);
        }

        $fields = array_unique($fields);

        $profile = new JFBConnectProfileDataLinkedin();
        if (!empty($fields))
        {
            $url = 'https://api.linkedin.com/v1/people/' . $userId . ':(' . implode(',', $fields) . ')';
            try
            {
                $data = $this->provider->client->query($url);
                $data = json_decode($data->body, true);
                $profile->loadObject($data);
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
    public function fetchStatus($providerId)
    {
        $status = $this->fetchProfile($providerId, 'current-share');
        return $status->get('current-share.comment');
    }


    // nullForDefault - If the avatar is the default image for the social network, return null instead
    // Prevents the default avatars from being imported
    function getAvatarUrl($providerId, $nullForDefault = true, $params = null)
    {
        $avatarUrl = JFBCFactory::cache()->get('linkedin.avatar.' . $providerId);
        if ($avatarUrl === false)
        {
            $data = $this->fetchProfile($providerId, 'picture-urls::(original)');
            $avatarUrl = $data->get('picture-urls.values.0');
            if ($avatarUrl == "")
                $avatarUrl = null;
            JFBCFactory::cache()->store($avatarUrl, 'linkedin.avatar.' . $providerId);
        }
        return $avatarUrl;
    }

    function getProfileUrl($memberId)
    {
        return 'http://www.linkedin.com/x/profile/' . $this->provider->appId . '/' . $memberId;
    }

}

class JFBConnectProfileDataLinkedin extends JFBConnectProfileData
{
    /*    private function checkForTimestampError()
        {
            if ($this->rawData['linkedin']['oauth_problem'] == 'timestamp_refused')
            {
                $liTime = str_replace("+-300", "", $this->rawData['linkedin']['oauth_acceptable_timestamps']);
                $offset = time() - $liTime;
                $configModel = JFBCFactory::config();
                $configModel->update('linkedin_oauth_timestamp_offset', $offset);
    //            $jlinkedLibrary->setTimestampOffset($offset);
                if ($configModel->getSetting('linkedin_display_errors', false))
                {
                    $app = JFactory::getApplication();
                    $app->enqueueMessage('JFBConnect Notice: Server time not in sync with LinkedIn. Setting correction offset to: ' . $offset . 's', 'error');
                }
                $this->errorRecoverable = true;
                return true;
            }
            else
                return false;
        }*/

    public function get($path, $default = "")
    {
        $value = $default;

        if ($path == 'full_name')
            return parent::get('firstName') . ' ' . parent::get('lastName');
        else if ($path == 'email')
            return parent::get('emailAddress');
        else if ($path == "middle_name")
            return "";

        if ($path != 'id')
        {
            if ($path == "first_name" || $path == "last_name")
                $path = str_replace('_', '-', $path);
            // Make the path into a JSON key value
            $parts = explode('-', $path);
            $parts = array_map('ucfirst', $parts);
            $path = implode('', $parts);
            $path = lcfirst($path);
        }

        if ($this->exists($path))
            $value = parent::get($path, $default);

        $valueParts = explode('.', $path);
        $element = parent::get($valueParts[0]);
        if (array_key_exists(1, $valueParts))
            $index = intval($valueParts[1]);

        switch ($valueParts[0])
        {
            case 'positions':
                return $this->getPosition($element, $index);
            case 'threeCurrentPositions':
            case 'threePastPositions':
                return $this->getPosition($element, $index);
            case 'publications':
                return $this->getPublication($element, $index);
            case 'patents':
                return $this->getPatent($element, $index);
            case 'languages':
                return $this->getLanguage($element, $index);
            case 'skills':
                return $this->getSkills($element, $index);
            case 'certifications':
                return $this->getCertification($element, $index);
            case 'educations':
                return $this->getEducation($element, $index);
            case 'courses':
                return $this->getCourse($element, $index);
            case 'volunteer':
                return $this->getVolunteer($element, $index);
            case 'recommendationsReceived':
                return $this->getRecommendation($element, $index);
            case 'phoneNumbers':
                return $this->getPhoneNumber($element, $index);
            case 'imAccounts':
                return $this->getIMAccount($element, $index);
            case 'twitterAccounts':
                return parent::get($valueParts[0] . '.values.' . $index . '.providerAccountName');
            case 'primaryTwitterAccount':
                return parent::get($valueParts[0] . '.providerAccountName');
            case 'dateOfBirth':
                return $this->getDOB($value);
            case 'siteStandardProfileRequest':
                return parent::get('siteStandardProfileRequest.url', '');
            case 'honorsAwards':
                return $this->getHonors($element, $index);
        }

        return $value;
    }

    private function getDOB($element)
    {
        $newValue = '';
        $year = (string)($element->year);
        $month = (string)($element->month);
        $day = (string)($element->day);

        if ($month != '' && $day != '' && $year != '')
            $newValue = $month . '/' . $day . '/' . $year;

        return $newValue;
    }

    private function getSkills($element, $index)
    {
        $skills = array();
        if (isset($element->values))
        {
            foreach ($element->values as $skill)
            {
                $skills[] = $skill->skill->name;
            }
        }
        $newValue = implode(', ', $skills);
        return $newValue;

    }

    private function getPosition($element, $index)
    {
        if (!property_exists($element->values, $index))
            return null;

        $position = $element->values->$index;
        $dateString = $this->getDateRange($position);

        $newValue = $position->title . ' at ' . $position->company->name;
        $newValue .= ': ' . $dateString;
        if ($position->summary)
            $newValue .= '. ' . $position->summary;

        return $newValue;
    }

    private function getPublication($element, $index)
    {
        $newValue = '';
        if (isset($element->values->$index))
        {
            $publication = $element->values->$index;
            $dateString = $this->getDateString($publication->date, ', ', '.');

            $authors = $this->getPeopleList($publication->authors);
            if ($authors)
                $newValue .= $authors . '. ';
            $newValue .= '"' . $publication->title . '."';
            if ($publication->publisher->name)
                $newValue .= ' ' . $publication->publisher->name;
            if ($dateString)
                $newValue .= $dateString;
            if ($publication->url)
                $newValue .= ' ' . $publication->url . '.';
            if ($publication->summary)
                $newValue .= ' ' . $publication->summary;
        }
        return $newValue;
    }

    private function getPatent($element, $index)
    {
        $newValue = '';
        if (isset($element->values->$index))
        {
            $patent = $element->values->$index;
            $dateString = $this->getDateString($patent->date, '. ', '.');

            $inventors = $this->getPeopleList($patent->inventors);
            if ($inventors)
                $newValue .= $inventors . '. ';
            $newValue .= $patent->title . '. ' . $patent->office->name . '. Patent ' . $patent->number;
            if ($dateString)
                $newValue .= $dateString;
            if ($patent->url)
                $newValue .= ' ' . $patent->url;
            if ($patent->summary)
                $newValue .= '. ' . $patent->summary . '.';
        }
        return $newValue;
    }

    private function getLanguage($element, $index)
    {
        $newValue = '';
        if (isset($element->values->$index))
        {
            $language = $element->values->$index->language;
            $proficiency = $element->values->$index->proficiency;
            $newValue = $language->name;
            if (isset($proficiency->name))
                $newValue .= '-' . $proficiency->name;
        }
        return $newValue;
    }

    private function getCertification($element, $index)
    {
        $newValue = '';
        if (isset($element->values->$index))
        {
            $certification = $element->values->$index;
            $dateString = $this->getDateRange($certification);

            $newValue = $certification->name;
            if ($certification->authority)
                $newValue .= '. ' . $certification->authority->name;
            if ($certification->number)
                $newValue .= ', ' . $certification->number;
            $newValue .= '. ' . $dateString . '.';
        }
        return $newValue;
    }

    private function getHonors($element, $index)
    {
        $newValue = '';
        if (isset($element->values->$index))
        {
            $honor = $element->values->$index;
            $dateString = $this->getDateString($honor->date, ' ', '.');

            $newValue = $honor->name;
            if ($honor->issuer)
                $newValue .= ' at ' . $honor->issuer . ':';
            if ($dateString)
                $newValue .= $dateString;
            if ($honor->description)
                $newValue .= ' ' . $honor->description;
        }
        return $newValue;
    }

    private function getEducation($element, $index)
    {
        $newValue = '';
        if (isset($element->values->$index))
        {
            $education = $element->values->$index;
            $dateString = $this->getDateRange($education);

            $newValue = $education->schoolName . ':';
            if (isset($education->degree) && $education->degree != '')
                $newValue .= ' ' . $education->degree;
            if (isset($education->fieldOfStudy) && $education->fieldOfStudy != '')
                $newValue .= ' ' . $education->fieldOfStudy;
            if ($dateString)
                $newValue .= ' ' . $dateString . '.';
            if (isset($education->activities) && $education->activities != '')
                $newValue .= ' ' . $education->activities . '.';
            if (isset($education->notes) && $education->notes != '')
                $newValue .= ' ' . $education->notes;
        }
        return $newValue;
    }

    private function getCourse($element, $index)
    {
        $newValue = '';
        if (isset($element->values->$index))
        {
            $course = $element->values->$index;
            $newValue = $course->name;
            if ($course->number)
                $newValue .= ' (' . $course->number . ")";
        }
        return $newValue;
    }

    private function getVolunteer($element, $index)
    {
        $newValue = '';
        if (isset($element->volunteerExperiences->values->$index))
        {
            $volunteer = $element->volunteerExperiences->values->$index;
            $newValue = $volunteer->role . " at " .
                    $volunteer->organization->name;
            //TODO: Add cause
        }
        return $newValue;
    }

    private function getRecommendation($element, $index)
    {
        $newValue = '';
        if (isset($element->values->$index))
        {
            $recommendation = $element->values->$index;
            $newValue = $recommendation->recommender->firstName . ' ' . $recommendation->recommender->lastName . ' (' . $recommendation->recommendationType->code . ') - ' . $recommendation->recommendationText;
        }
        return $newValue;
    }

    private function getPhoneNumber($element, $index)
    {
        $newValue = '';
        $phoneNumberString = 'phoneNumber';
        $phoneTypeString = 'phoneType';

        if (isset($element->values->$index->$phoneNumberString))
        {
            $newValue = $element->values->$index->$phoneNumberString . ' (' .
                    $element->values->$index->$phoneTypeString . ')';
        }
        return $newValue;
    }

    private function getIMAccount($element, $index)
    {
        $newValue = '';
        $imAccountType = 'imAccountType';
        $imAccountName = 'imAccountName';

        if (isset($element->values->$index->$imAccountName))
        {
            $newValue = $element->values->$index->$imAccountName . ' (' .
                    $element->values->$index->$imAccountType . ')';
        }

        return $newValue;
    }

    /** Helper functions **/

    private function getDateRange($element)
    {
        $dateRange = "";
        $startDateString = 'startDate';
        $endDateString = 'endDate';
        $start = '';
        $end = '';
        if (property_exists($element, $startDateString))
            $start = $this->getDateString($element->$startDateString, null, null);
        if (property_exists($element, $endDateString))
            $end = $this->getDateString($element->$endDateString, null, null);

        if ($start)
            $dateRange = $start . ' to ';

        if ($end)
            $dateRange .= $end;
        else
            $dateRange .= 'Present';
        return $dateRange;
    }

    private function getDateString($date, $prefix, $suffix)
    {
        $dateValue = '';
        if (is_object($date))
        {
            if (property_exists($date, 'month'))
                $dateValue = $date->month . '-' . $date->year;
            else if (property_exists($date, 'year'))
                $dateValue = $date->year;

            if ($dateValue != '')
            {
                if ($prefix)
                    $dateValue = $prefix . $dateValue;
                if ($suffix)
                    $dateValue = $dateValue . $suffix;
            }
        }
        return $dateValue;
    }

    private function getPeopleList($authors)
    {
        $people = array();
        foreach ($authors->values as $author)
        {
            if ($author->name)
                $people[] = $author->name;
            else if (!isset($people[0]))
                $people[] = $author->person->lastName . ', ' . $author->person->firstName;
            else
                $people[] = $author->person->firstName . ' ' . $author->person->lastName;
        }

        return implode(" and ", $people);
    }
}