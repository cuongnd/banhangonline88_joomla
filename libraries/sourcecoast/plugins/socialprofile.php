<?php
/**
 * @package         SourceCoast Extensions
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');
jimport('sourcecoast.utilities');

class SocialProfilePlugin extends JPlugin
{
    var $defaultSettings;
    var $settings;
    var $_componentFile = '';
    var $_componentFolder = '';
    var $network;
    var $profileLibrary;
    var $joomlaId;
    var $socialId;
    var $registrationUser; // Original JUser object used for registration. Mainly necessary so the password_clear can be read by the plugins

    var $db;
    var $_importEnabled = false; // Can this plugin import previous FB connections
    private $componentLoaded = null;

    protected $profileName;

    // Set this to a non-null value to allow 3rd party registration flows
    protected $registration_url = null;

    var $_settingShowRegistrationFields = null;
    var $_settingShowImportedFields = null;

    function __construct(&$subject, $params)
    {
        $this->profileName = $params['name'];
        $this->db = JFactory::getDBO();
        $this->defaultSettings = new JRegistry();

        // Don't even fully construct this plugin if the component isn't found
        if ($this->componentLoaded())
            parent::__construct($subject, $params);
    }

    function __get($name)
    {
        switch ($name)
        {
            case 'registration_url' :
                return $this->registration_url;
            case 'name' :
                return $this->profileName;
            case 'displayName' :
            {
                if (isset($this->displayName))
                    return $this->displayName;
                else
                    return ucwords($this->profileName);
            }
        }
    }

    public function socialProfilesGetPlugins()
    {
        return $this;
    }

    /**
     * Called after registration occurs
     * Good for importing the profile on first registration
     */
    public function socialProfilesOnRegister($network, $jUser, $socialId)
    {
        $this->registrationUser = $jUser;
        $this->loadSettings($network, $jUser->get('id'), $socialId);
        $this->onRegister();

        return true;
    }

    /**
     * OnRegister
     * Will call (optional) functions in the profile plugin to create the user and log them in for the first time
     *
     * @param $joomlaId
     * @param $fbUserId
     */
    protected function onRegister()
    {
        if (empty($this->joomlaId) || empty($this->socialId))
            return; // Something's wrong, we should have both of these

        $profileData = $this->fetchProfileFromFieldMap(true);

        // Create the new user in the 3rd party extension with profile information imported or input on the registration page
        $this->createUser($profileData);

        if ($this->settings->get('import_avatar'))
            $this->importSocialAvatar();

        if ($this->settings->get('import_status'))
            $this->importSocialStatus();

        if ($this->settings->get('import_cover_photo', 0))
            $this->importCoverPhoto();
    }

    /**
     * Called after registration occurs
     * Good for importing the profile on first registration
     */
    function socialProfilesOnLogin($network, $joomlaId, $socialId)
    {
        $this->loadSettings($network, $joomlaId, $socialId);
        $this->onLogin();

        return true;
    }

    protected function onLogin()
    {
        if (empty($this->joomlaId) || empty($this->socialId))
            return; // Something's wrong, we should have both of these

        if ($this->settings->get('import_always'))
        {
            $this->importSocialProfile(); // This will import fields from the user's profile

            if ($this->settings->get('import_avatar'))
                $this->importSocialAvatar();

            if ($this->settings->get('import_status'))
                $this->importSocialStatus();

            if ($this->settings->get('import_cover_photo', 0))
                $this->importCoverPhoto();
        }
    }

    /*
     * Called on user request or other times like mapping
     */
    public function socialProfilesOnImportProfile($network, $joomlaId, $socialId)
    {
        $this->loadSettings($network, $joomlaId, $socialId);
        if (empty($this->joomlaId) || empty($this->socialId))
            return; // Something's wrong, we should have both of these

        if ($this->getProfileImportPermission())
        {
            $this->importSocialProfile(); // This will import fields from the user's profile

            if ($this->settings->get('import_avatar'))
                $this->importSocialAvatar();

            if ($this->settings->get('import_status'))
                $this->importSocialStatus();
        }
    }

    public function socialProfilesGetRequiredScope($network)
    {
        $this->loadSettings($network);
        $scope = array();
        $fieldMap = $this->getFieldMap($network);

        $scope = array_merge($scope, $this->profileLibrary->getPermissionsForFields($fieldMap));
        if ($this->network == 'facebook')
        {
            // If the "import_status" setting is used, and enabled, also request the user_status perm from FB
            if ($this->settings->get('import_status', 0))
                $scope[] = "user_status";

        }
        return $scope;
    }

    public function socialProfilesOnNewUserSave($network, $joomlaId, $socialId)
    {
        $regComponent = JFBCFactory::config()->get('registration_component');
        if ($regComponent == $this->name)
        {
            $this->loadSettings($network, $joomlaId, $socialId);
            $this->onNewUserSave();
        }
    }

    protected function onNewUserSave()
    {
        return true;
    }

    protected function createUser($profileData)
    {

    }

    /**
     * Profile plugin (or integration component) will send the new user emails.
     * In this case, the JFBConnect/JLinked/etc will not send the admin/user emails
     * @return bool
     */
    public function socialProfilesSendsNewUserEmails()
    {
        return false;
    }

    /**
     * Profile will add its form validation script. If no custom validation is required,
     * default validation will be performed
     * @return bool
     */
    public function socialProfilesAddFormValidation()
    {
        return false;
    }


    protected function importSocialStatus()
    {
        $socialStatus = $this->profileLibrary->fetchStatus($this->socialId);
        if (!empty($socialStatus))
            $this->setStatus($socialStatus);
    }

    // Deprecated as of JFBConnect v5.2. Remove after JLinked is integrated
    public function getConfigurationTemplate($network)
    {
        $this->loadSettings($network);

        $file = JPATH_SITE . '/plugins/socialprofiles/' . $this->profileName . '/' . $this->profileName . '/tmpl/configuration.php';

        if (!JFile::exists($file))
            return "No configuration is required for this profile plugin";

        $this->profileFields = $this->getProfileFields();

        // Fetch in the template file included below
        if ($network == 'facebook')
            $socialNetworkProfileFields = JFBCFactory::provider($network)->profile->getProviderFields();
        else if ($network == 'linkedin')
            $socialNetworkProfileFields = JLinkedProfileLibrary::$profileFields;
        ob_start();
        include_once($file);
        $config = ob_get_clean();
        return $config;
    }

    // Deprecated - use $this->name instead
    function getName()
    {
        return $this->profileName;
    }

    public function socialProfilesPrefillRegistration()
    {
        $regComponent = JFBCFactory::config()->get('registration_component');
        if ($regComponent != $this->name)
            return;

        $app = JFactory::getApplication();
        $provider = $app->getUserState('com_jfbconnect.registration.provider.name');
        $providerUserId = $app->getUserState('com_jfbconnect.registration.provider.user_id');

        if ($provider && $providerUserId)
        {
            $this->loadSettings($provider, null, $providerUserId);
            if ($this->prefillRegistration())
            {
                if (JFBCFactory::config()->get('joomla_skip_newuser_activation') == 1)
                {
                    $params = JComponentHelper::getParams('com_users');
                    $params->set('useractivation', 0);
                }
            }
        }
    }

    protected function finalizeRegistration()
    {
        $app = JFactory::getApplication();

        // Try to auto-login the user if that will work
        $params = JComponentHelper::getParams('com_users');
        if (JFBCFactory::config()->get('joomla_skip_newuser_activation') == 1 || !$params->get('useractivation'))
        {
            $provider = $app->getUserState('com_jfbconnect.registration.provider.name');
            $redirect = 'index.php?option=com_jfbconnect&task=login.login&provider=' . $provider;
            $app->redirect($redirect);
        }
        else
            $app->setUserState('com_jfbconnect.registration.alternateflow', false);

        return true;
    }

    protected function prefillRegistration()
    {
        return;
    }

    // Method to set a registration field during registration. Will check if field is already set (likely by the user) and not overwrite that value.
    protected function prefillRegistrationField($fieldName, $value, $method = "POST")
    {
        if (!JRequest::getVar($fieldName, null, $method))
            JRequest::setVar($fieldName, $value, $method);
    }

    /**
     * Get field names and inputs to request additional information from users on registration
     * @return string HTML of form fields to display to user on registration
     */
    public function socialProfilesOnShowRegisterForm($network)
    {
        $this->loadSettings($network);
        $profileData = $this->fetchProfileFromFieldMap(false);
        $html = $this->getRegistrationForm($profileData);
        return $html;
    }

    protected function getRegistrationForm($profileData)
    {
        $showRegistrationFields = $this->settings->get('registration_show_fields');
        $showImportedFields = $this->settings->get('imported_show_fields');

        $html = "";

        $profileFields = $this->getProfileFields();

        $fieldMap = $this->getFieldMap($this->network);
        foreach ($profileFields as $profileField)
        {
            if (property_exists($fieldMap, $profileField->id))
            {
                $id = $profileField->id;
                $fieldName = $fieldMap->$id;
            }
            else
                $fieldName = 0;
            $showField = $showRegistrationFields == "1" &&
                    ($showImportedFields == "1" || ($showImportedFields == "0" && !$fieldName));

            if (!$showField)
            {
                if ($fieldName != '0')
                    $this->set('performsSilentImport', 1);
                continue;
            }

            $fieldValue = $profileData->getFieldWithUserState($profileField->id);

            $html .= '<label for="' . $profileField->id . '">' . $profileField->name . '</label>';
            $html .= '<input type="text" name="' . $profileField->id . '" id="' . $profileField->id . '" value="' . $fieldValue . '" /><br/>';
        }

        return $html;
    }

    /*
     * Fetches the user profile from the social network based on the field mapping settings
     * Then, merges the data with any POSTed fields from a possible registration submission
     *
     * Social network profile is saved to the session on the first time to avoid fetching it on every submission or after successful registration
     * @return JRegistry with profile data
     */
    protected function fetchProfileFromFieldMap($permissionNeeded = false)
    {
        $fieldMap = $this->getFieldMap($this->network);
        $app = JFactory::getApplication();

        $sessionKey = 'plg_socialprofiles.' . $this->network . '.' . $this->name;
        if ($permissionNeeded)
            $permissionGranted = $this->getProfileImportPermission();
        else
            $permissionGranted = true;
        $socialProfile = $this->profileLibrary->fetchProfileFromFieldMap($fieldMap, $permissionGranted);
        $app->setUserState($sessionKey, $socialProfile->toString());

        return $socialProfile;
    }

    /**
     * Used for plugins to check any credentials or information as necessary
     * Return true if login should proceed, false if not
     */
    public function socialProfilesOnAuthenticate($network, $joomlaId, $socialId)
    {
        $this->loadSettings($network, $joomlaId, $socialId);

        return $this->onAuthenticate();
    }

    protected function onAuthenticate()
    {
        $response = new profileResponse();
        $response->status = true;
        return $response;
    }

    /**
     * Determine if the Login Register view needs to give user the option to approve profile import
     * Required for LinkedIn, which requires explicit permission to import the users profile
     * @return bool if permission is needed, false if not
     */
    public function socialProfilesNeedsImportPermission($network)
    {
        return false;
    }

    public function socialProfilesAwardPoints($name, $data)
    {
        $userId = $data->get('userId', null);
        if (!$userId)
            $userId = JFactory::getUser()->get('id');
        if (!$userId)
            $userId = $this->joomlaId;

        if ($userId)
            $this->awardPoints($userId, $name, $data);
    }

    protected function awardPoints($userId, $name, $data)
    {
    }

    /*     * *
     *
     * ************ END Triggered functions ************
     *
     * ** */

    /*     * *
     * ************ Direct call functions **************
     */


    /*     * ***
     * ************* END Direct call functions ********8
     */

    // These functions should be overridden by the plugins
    protected function getProfileFields()
    {
        return array();
    }

    protected function getProfileImportPermission()
    {
        return true;
    }

    protected function getFieldMap($network)
    {
        $data = $this->settings->get('field_map.' . $network);

        if (!is_object($data))
            $data = new stdClass();
        return $data;
    }

    protected function importSocialProfile()
    {
        $fieldMap = $this->getFieldMap($this->network);
        $socialProfile = $this->fetchProfileFromFieldMap(true);
        if ($socialProfile)
        {
            foreach ($fieldMap as $fieldId => $socialField)
            {
                // Fetch the value from the POST data (if present) or the imported data from the Social Network
                $value = $socialProfile->getFieldWithUserState($fieldId);

                if ($value != null && $value != "")
                {
                    if (is_array($value))
                    { // This is a field with multiple, comma separated values
                        // Remove empty values to prevent blah, , blah as output
                        unset($value['id']); // Remove id key which is useless to import
                        $value = SCStringUtilities::r_implode(', ', $value);
                    }
                    $this->saveProfileField($fieldId, $value);
                }
            }
        }
    }

    protected function saveProfileField($fieldId, $value)
    {
        return true;
    }

    protected function importSocialAvatar()
    {
        jimport('joomla.filesystem.file');
        jimport('joomla.utilities.utility');

        $avatarURL = $this->profileLibrary->getAvatarURL($this->socialId, true);
        if ($avatarURL == null)
        {
            if ($this->registrationUser) // Only revert to the default avatar if new user registration
                $this->setDefaultAvatar($this->joomlaId);
            return false;
        }

        $data = SCSocialUtilities::getRemoteContent($avatarURL);
        if ($data)
        {
            $baseImgPath = $this->getAvatarPath();
            $tmpImgName = 'scprofile_' . $this->joomlaId . '_pic_tmp.jpg';
            JFile::write($baseImgPath . '/' . $tmpImgName, $data);
            if ($this->setAvatar($tmpImgName))
                return true;
        }

        # there was a problem adding the avatar, use the default
        $this->setDefaultAvatar();
        return false;

    }

    protected function importCoverPhoto()
    {
        $cover = $this->profileLibrary->getCoverPhoto($this->socialId);
        if (!$cover)
            return false;

        $data = SCSocialUtilities::getRemoteContent($cover->get('url'));
        $tmpImgName = 'sccover_' . $this->network . $this->socialId . '_pic_tmp.jpg';
        JFile::write($this->getAvatarPath() . '/' . $tmpImgName, $data);
        $cover->set('path', $this->getAvatarPath() . '/' . $tmpImgName);
        list($width, $height, $type) = getimagesize($cover->get('path'));

        // Resize to the offsets provided if non-0
        if ($type != 2 || $cover->get('offsetX') != 0 || $cover->get('offsetY') != 0)
        {
            $image_p = imagecreatetruecolor($width - $cover->get('offsetX'), $height - $cover->get('offsetY'));
            $origImage = imagecreatefromstring($data);
            imagecopyresampled($image_p, $origImage, 0, 0, $cover->get('offsetX'), $cover->get('offsetY'), $width, $height, $width, $height);
            if (!imagejpeg($image_p, $this->getAvatarPath() . '/' . $tmpImgName, 100))
                return false;
        }
        $cover->set('type', "image/jpeg");
        return $this->setCoverPhoto($cover);
    }

    protected function setCoverPhoto($cover)
    {
        return true;
    }

    protected function getAvatarPath()
    {
        $app = JFactory::getApplication();
        $tmpPath = $app->getCfg('tmp_path');
        return $tmpPath;
    }

    protected function setDefaultAvatar()
    {
        return true;
    }

    public function canImportConnections()
    {
        return $this->_importEnabled;
    }

    protected function componentLoaded()
    {
        if ($this->componentLoaded === null)
        {
            $this->componentLoaded = true;

            if ($this->_componentFile != '')
            {
                jimport('joomla.filesystem.file');
                $this->componentLoaded = JFile::exists($this->_componentFolder . '/' . $this->_componentFile);
            }
            else if ($this->_componentFolder != '')
            {
                jimport('joomla.filesystem.folder');
                $this->componentLoaded = JFolder::exists($this->_componentFolder);
            }
        }

        return $this->componentLoaded;
    }

    protected function getAutoUsername($providerProfile)
    {
        $usernamePrefixFormat = JFBCFactory::config()->getSetting('auto_username_format');
        $provider = $this->network;
        $providerPrefix = "__";
        if ($provider == 'facebook')
            $providerPrefix = "fb_";
        else if ($provider == "google")
            $providerPrefix = "g_";
        else if ($provider == "twitter")
            $providerPrefix = "t_";

        return SCUserUtilities::getAutoUsername($providerProfile->get('first_name'), $providerProfile->get('last_name'), $providerProfile->get('email'), $providerPrefix, $this->socialId, $usernamePrefixFormat);
    }

    protected function loadSettings($network, $joomlaId = null, $socialId = null)
    {
        $this->joomlaId = $joomlaId;
        $this->socialId = $socialId;

        if ($network == $this->network)
            return $this->settings;

        $this->network = $network;
        $query = $this->db->getQuery(true);
        $query->select("value");
        $query->where('setting="profile_' . $this->name . '"');
        $query->from("#__jfbconnect_config");

        $this->db->setQuery($query);
        $values = $this->db->loadResult();
        if (!empty($values))
        {
            $this->settings = new JRegistry();
            $this->settings->loadString($values);
        }
        else
            $this->settings = $this->defaultSettings;

        $this->profileLibrary = JFBCFactory::provider($network)->profile;

        return $this->settings;
    }

    public function getFieldMappingHTML()
    {
        $allProviders = JFBCFactory::getAllProviders();
        // Remove providers who aren't configured for authentication
        $providers = $allProviders;
        for ($i = 0; $i < count($allProviders); $i++)
        {
            $p = $allProviders[$i];
            if (!$p->appId)
                unset($providers[$i]);
        }

        $profileFields = $this->getProfileFields();
        $html = '<div class="row-fluid"><div class="span12">
            <div class="well">
    <legend>Social Network Profile Import Configuration</legend>
    <table>
        <tr>
            <th>' . ucwords($this->profileName) . " Field</th>";
        foreach ($providers as $provider)
            $html .= "<th>" . $provider->name . "</th>";

        $html .= "</tr>";
        foreach ($profileFields as $profileField)
        {
            $html .= '<tr><td>';
            $html .= JText::_($profileField->name);
            foreach ($providers as $provider)
            {
                $this->loadSettings(strtolower($provider->name));
                $fieldMap = $this->getFieldMap(strtolower($provider->name));
                if (property_exists($fieldMap, $profileField->id))
                {
                    $fieldId = $profileField->id;
                    $selectedValue = $fieldMap->$fieldId;
                }
                else
                    $selectedValue = "0";
                $html .= '</td><td>';
                $profile = $provider->profile;
                $html .= '<select name="profiles_' . $this->profileName . '_' . strtolower($provider->name) . '_field_map' . $profileField->id . '">';
                foreach ($profile->getProviderFields() as $name => $providerField)
                {
                    if ($name == $selectedValue)
                        $selected = 'selected';
                    else
                        $selected = '';
                    $html .= '<option value="' . $name . '" ' . $selected . '>' . $providerField . '</option>';
                }
                $html .= '</select>';
            }
            $html .= '</td></tr>';
        }
        $html .= "</table>";
        $html .= "</div></div></div>";
        return $html;
    }
}

if (!class_exists('profileResponse'))
{
    class profileResponse
    {

        var $status;
        var $message;

    }
}