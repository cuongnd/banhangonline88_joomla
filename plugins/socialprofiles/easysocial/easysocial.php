<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.utilities.date');
jimport('sourcecoast.plugins.socialprofile');
jimport('sourcecoast.utilities');

class plgSocialProfilesEasysocial extends SocialProfilePlugin
{
    var $_profileType = null;

    function __construct(&$subject, $params)
    {
        $this->displayName = "EasySocial";
        // Setup the file paths that detect if this component is actually installed.
        // Needed before calling the parent constructor
        $this->_componentFolder = JPATH_SITE . '/components/com_easysocial';

        parent::__construct($subject, $params);

        $this->defaultSettings->set('import_avatar', '1');
        $this->defaultSettings->set('import_always', '0');
        $this->defaultSettings->set('import_cover_photo', '1');
        $this->defaultSettings->set('import_status', '1');
//        $this->defaultSettings->set('push_status', '0');
        $this->defaultSettings->set('registration_show_fields', '0'); //0=None, 1=Required, 2=All
        $this->defaultSettings->set('imported_show_fields', '0'); //0=No, 1=Yes
        $this->defaultSettings->set('profiletype_visible', '0');
        $this->defaultSettings->set('profiletype_default', '0');

        // Set this for allowing registration through this component
        $this->registration_url = 'index.php?option=com_easysocial&view=registration';

        $this->_importEnabled = true; // This plugin has a method to transfer existing facebook connections over to JFBConnect
    }

    public function socialProfilesGetRequiredScope($network)
    {
        $scope = parent::socialProfilesGetRequiredScope($network);
        if ($network == 'facebook' && $this->settings->get('push_status'))
            $scope[] = 'publish_actions';

        return $scope;
    }

    function socialProfilesSendsNewUserEmails()
    {
        return true;
    }

    // Query must return at least id, name
    protected function getProfileFields($profileId = null)
    {
        SCStringUtilities::loadLanguage('com_easysocial');
        require_once(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php');
        if (!$profileId)
        {
            $model = Foundry::model('profiles');
            $profileTypes = $model->getProfiles();
        }
        else
        {
            $profileTypes = array();
            $p = Foundry::table('Profile');
            $p->load($profileId);
            $profileTypes[] = $p;
        }
        $supportedFields = array( /*'address', */
                'birthday', /*'country',*/
                'datetime', 'email', 'gender', 'textarea', 'textbox', 'url');
        $fields = array();

        $fieldsModel = Foundry::model('Fields');
        foreach ($profileTypes as $p)
        {
            $pFields = $fieldsModel->getCustomFields(array('profile_id' => $p->id));

            foreach ($pFields as $field)
            {
                if (in_array($field->element, $supportedFields))
                {
                    $new = new stdClass();
                    $new->id = $field->id;
                    $new->name = JText::_($field->title) . ' (' . $p->title . ')';
                    $new->type = $field->element;
                    $fields[] = $new;
                }
            }
        }
        return $fields;
    }

    public function prefillRegistration()
    {
        $input = JFactory::getApplication()->input;
        if ($input->getCmd('option') == 'com_easysocial')
        {
            if ($input->getCmd('view') == 'registration')
            {
                require_once(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php');

                // Load up necessary model and tables.
                $registration = Foundry::table('Registration');
                $session = JFactory::getSession();

                // Purge expired session data for registrations.
                $model = Foundry::model('Registration');
                $model->purgeExpired();

//                if (!$registration->load($session->getId()))
//                {
                // Method of loading a registration object was changed to (pretty much always) return true in ES1.2, so we need to query the db directly now
                $query = $this->db->getQuery(true);
                $query->select('COUNT(*)')
                        ->from('#__social_registrations')
                        ->where('session_id=' . $this->db->q(JFactory::getSession()->getId()));
                $cnt = $this->db->setQuery($query)->loadResult();
                if ($cnt == 0)
                {
                    $this->loadRegistrationObject($registration);
                }
                else
                {
                    if ($input->getCmd('task') == 'selectType')
                    {
                        $this->loadRegistrationObject($registration);
                    }
                }

                $registration->load(JFactory::getSession()->getId());

                $step = $input->getCmd('step', null);
                if ($step)
                {
                    $hideFields = json_decode(JFactory::getApplication()->getUserState('com_jfbconnect.registration.easysocial.hidefields', '{}'));

                    $registrationModel = Foundry::model('registration');

                    // Load the steps for this profile and then check if the fields are hidden (pre-popped) and should be skipped
                    $stepsModel = Foundry::model('Steps');
                    $profileSteps = $stepsModel->getSteps($registration->profile_id, 'profiles');
                    foreach ($profileSteps as $profileStep)
                    {
                        if ($profileStep->sequence == $step)
                            $sequence = $profileStep->id;
                    }

                    $fields = $registrationModel->getCustomFieldsForProfiles($registration->profile_id);
                    $skipStep = true;
                    foreach ($fields as $field)
                    {
                        if ($field->step_id == $sequence)
                        {
                            if ($field->element != "header" && !in_array($field->id, $hideFields))
                            {
                                $skipStep = false;
                                break;
                            }
                        }
                    }

                    if ($skipStep)
                    {
                        JFactory::getApplication()->redirect('index.php?option=com_easysocial&task=saveStep&currentStep=3&controller=registration&' . JSession::getFormToken() . '=1');
                    }
                }

                $this->prefillRegistrationHideFields();
            }
        }
    }

    private function prefillRegistrationHideFields()
    {
        $hideFields = json_decode(JFactory::getApplication()->getUserState('com_jfbconnect.registration.easysocial.hidefields', '{}'));
        if (count($hideFields) > 0)
        {
            $doc = JFactory::getDocument();
            foreach ($hideFields as $field)
                $doc->addStyleDeclaration('[data-fieldname="es-fields-' . $field . '"]{ display:none;}');
        }
    }

    public function onNewUserSave()
    {
        $this->importSocialAvatar();
        $this->importCoverPhoto();
    }

    private function processField($type, $value, $mode = null)
    {
        $field = new stdClass();
        $field->value = $value;
        $field->raw = $value;
        if (empty($value))
            return $field;

        switch ($type)
        {
            case "birthday" :
            case "datetime" :
                if ($mode == 'form')
                    $field->value = strftime("%d/%m/%Y", strtotime($value));
                else
                {
                    $field->value = strftime("%Y-%m-%d 00:00:00", strtotime($value));
                    $field->raw = strftime("%Y %B %d", strtotime($value));
                }
                break;
            case "gender" :
                $field->value = strtolower($value) == 'male' ? 1 : 2;
                $field->raw = $field->value;
                break;
            case "joomla_fullname" :
                if ($value instanceof JRegistry)
                {
                    $name = "";
                    foreach (array('first', 'middle', 'last') as $v)
                    {
                        if ($part = $value->get($v))
                            $name .= $part . " ";
                    }
                    $name = trim($name);
                    $value->set('name', $name);
                    $field->value = $value->toString();
                    $field->raw = $name;
                }
                break;
        }
        return $field;
    }

    private function loadRegistrationObject($registration)
    {
        $profileId = JRequest::getInt('profile_id', $this->settings->get('profiletype_default'));
        $session = JFactory::getSession();
        $esFields = $this->getProfileFields($profileId);

        $registration->set('session_id', $session->getId());
        $registration->set('created', Foundry::get('Date')->toMySQL());
        $registration->set('profile_id', $profileId);

        if (!$registration->store())
        {
            $this->setError($registration->getError());
            return false;
        }

        $registry = Foundry::get('Registry');
        $registry->load($registration->values);

        // Load json library.
        $json = Foundry::json();

        $hideFields = array();

        $fields = array('first_name', 'last_name', 'middle_name', 'email');
        $profileData = $this->profileLibrary->fetchProfile($this->socialId, $fields);

        $registry->set('first_name', $profileData->get('first_name', ""));
        $registry->set('last_name', $profileData->get('last_name', ""));
        $registry->set('middle_name', $profileData->get('middle_name', ""));

        /*if ($this->settings->get('generate_password'))
        {
            $registry->set('es-fields-4-input', '123456');
            $registry->set('es-fields-4-reconfirm', '123456');
        }*/

        $genUsername = $this->settings->get('generate_username');
        if ($genUsername > 0)
        {
            $registry->set('username', $this->getAutoUsername($profileData));
            if ($genUsername == 1)
                $hideFields[] = $this->getEsFieldName('JOOMLA_USERNAME', $profileId);
        }

        if ($profileData->get('email') != '')
        {
            $registry->set('email', $profileData->get('email'));
            if ($this->settings->get('show_email') == 0)
                $hideFields[] = $this->getEsFieldName('JOOMLA_EMAIL', $profileId);
        }

        if ($this->settings->get('import_cover_photo'))
            $hideFields[] = $this->getEsFieldName('COVER', $profileId);
        if ($this->settings->get('import_avatar'))
            $hideFields[] = $this->getEsFieldName('AVATAR', $profileId);

        // From profile mappings
        $profileData = $this->fetchProfileFromFieldMap();

        foreach ($profileData->fieldMap as $key => $value)
        {
            $data = '';
            foreach ($esFields as $esField)
            {
                if ($esField->id == $key)
                {
                    $type = $esField->type;
                    $data = $this->processField($type, $profileData->get($value), 'form')->value;
                    break;
                }
            }
            if ($data != '')
            {
                $registry->set('es-fields-' . $key, $data);
                if (!$this->settings->get(('imported_show_fields')))
                    $hideFields[] = $key;
            }
        }
        JFactory::getApplication()->setUserState('com_jfbconnect.registration.easysocial.hidefields', json_encode($hideFields));

        // Convert the values into an array.
        $data = $registry->toArray();

        $args = array(&$data, &$registration);
        $registration->values = $json->encode($data);
        return $registration->store();
    }

    // This is the models/registration -> createUser function from EasySocial, with modifications for JFBConnect options
    protected function createUser($profileData)
    {
        require_once(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php');

        $profile = Foundry::table('Profile');
        $profileId = $this->settings->get('profiletype_default');
        $profile->load($profileId);
        $profile->addUser($this->joomlaId);

        // Save 'name' field here

        $name = new JRegistry();
        $fields = array('first_name', 'last_name', 'middle_name');
        $profileData = $this->profileLibrary->fetchProfile($this->socialId, $fields);

        $name->set('first', $profileData->get('first_name', ""));
        $name->set('last', $profileData->get('last_name', ""));
        $name->set('middle', $profileData->get('middle_name', ""));

        $this->saveProfileField($this->getEsFieldName('JOOMLA_FULLNAME', $profileId), $name);

        $esUser = Foundry::user($this->joomlaId);

        // Award points and badges for the new registration
        SCStringUtilities::loadLanguage('com_easysocial');
        $points = Foundry::points();
        $points->assign('user.registration', 'com_easysocial', $this->joomlaId);
        $badge = Foundry::badges();
        $badge->log('com_easysocial', 'registration.create', $this->joomlaId, JText::_('COM_EASYSOCIAL_REGISTRATION_BADGE_REGISTERED'));

        // Check if account should be activated or not
        $skipActivation = JFBCFactory::config()->get('joomla_skip_newuser_activation');

        if (!$skipActivation)
        {
            $jUser = JUser::getInstance($this->joomlaId);
            // Need to keep these both in sync... not ideal
            $type = $profile->getRegistrationType();
            if ($type == 'verify')
            {
                $code = Foundry::getHash(JUserHelper::genRandomPassword());
                $esUser->activation = $code;
                $jUser->activation = $code;
                JFactory::getApplication()->enqueueMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'));
            }

            // If the registration type requires approval or requires verification, the user account need to be blocked first.
            if ($type == 'approvals' || $type == 'verify')
            {
                $jUser->block = 1;
                $esUser->block = 1;
                JFactory::getApplication()->enqueueMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY'));
            }
            $esUser->set('state', constant('SOCIAL_REGISTER_' . strtoupper($type)));
            $esUser->save();
            $jUser->save();
        }
        else
        {
            // And in my least favorite bit of code, set the table params with the hope that something else hasn't already loaded them
            $origParams = new JRegistry();
            $origParams->loadString($profile->params);
            $origParams->set('registration', 'auto');
            $profile->params = $origParams->toString();
        }

        // Load profile type.
        // Perform the avatar import here so it can go in the email.
        if ($this->settings->get('import_avatar'))
        {
            $this->importSocialAvatar();

            // This is required because resetting the SocialUser instance doesn't clear the usermeta properly. Need to talk to Mark fixing this.
            $avatarTable = Foundry::table('avatar');
            $avatarTable->load(array('uid' => $this->joomlaId));
            $esUser->avatar_id = $avatarTable->id;
            $params = new stdClass();
            $params->small = $avatarTable->small;
            $params->medium = $avatarTable->medium;
            $params->square = $avatarTable->square;
            $params->large = $avatarTable->large;
            $esUser->initParams($params);
        }

        $this->settings->set('import_avatar', 0);

        // Finally, send the notification emails
        // Send notification to admin if necessary.
        $registrationModel = Foundry::model('registration');

        $mailerData = array(
                'username' => $esUser->get('username'),
                'firstName' => $name->get('first'),
                'middleName' => $name->get('middle'),
                'lastName' => $name->get('last'),
                'name' => $esUser->getName(),
                'id' => $esUser->id,
                'avatar' => $esUser->getAvatar(SOCIAL_AVATAR_LARGE),
                'profileLink' => $esUser->getPermalink(),
                'email' => $esUser->email,
                'activation' => FRoute::registration(array('external' => true, 'task' => 'activate', 'controller' => 'registration', 'token' => $esUser->activation)),
                'token' => $esUser->activation,
                'profileType' => $profile->get('title')
        );

        // If everything goes through fine, we need to send notification emails out now.
        $esUser->password_clear = $this->registrationUser->password_clear;
        $registrationModel->notify($mailerData, $esUser, $profile);

        $mailerData['username'] = $mailerData['username'] . ' (via ' . ucwords($this->network) . ')';
        $registrationModel->notifyAdmins($mailerData, $esUser, $profile);

        $this->importSocialProfile();
        return true;
    }

    /*    protected function getRegistrationForm($profileData)
        {
            $html = '';

            CFactory::load('models', 'profile');
            CFactory::load('libraries', 'profile');
            $language = JFactory::getLanguage();
            $language->load('com_community');

            $defaultType = $this->settings->get('profiletype_default', 0);
            $profileType = $defaultType;
            if ($this->settings->get('profiletype_visible', 0))
            {
                $profileType = JRequest::getInt('profile_id', $profileType);
                $availableTypes = $this->getProfileTypes(!$defaultType); // Only show default if it's selected
                $profileJs = "var jfbcProfilesCommunityType = $('profile_id').value;" .
                        "window.location.href = jfbc.base + 'index.php?option=com_jfbconnect&view=loginregister&provider=" . $this->network . "&profile_id='+jfbcProfilesCommunityType;";
                $html .= "<br/><b>" . JText::_('COM_COMMUNITY_MULTIPROFILE_SELECT_TYPE') . ":</b><br/>";
                $html .= JHTML::_('select.genericlist', $availableTypes, 'profile_id', ' onchange="' . $profileJs . '"', 'id', 'name', $profileType);
                $html .= "<br/>";
            }
            $this->_profileType = $profileType;

            //Get register field forms
            $showRegistrationFields = $this->settings->get('registration_show_fields');
            $showImportedFields = $this->settings->get('imported_show_fields');

            $profileModel = CFactory::getModel('profile');

            // Get the groups and their fields for the selected profile type that are published and registered
            $filter = array('published' => '1', 'registration' => '1'); //, 'required' => '1');
            $fieldGroups = & $profileModel->getAllFields($filter, $this->_profileType);

            CFactory::load('libraries', 'template');
            CTemplate::addStylesheet('style');
            CTemplate::addStylesheet('minitip');

            foreach ($fieldGroups as $group)
            {
                $groupHtml = "";
                $hasVisibleFields = false;
                foreach ($group->fields as $field)
                {
                    $fieldName = $this->settings->get('field_map.' . $field->id, 0);
                    // Show All/Required Fields. Hide mapped fields if not showing imported fields
                    $showField = ($showRegistrationFields == '2' || ($field->required && $showRegistrationFields == '1')) &&
                            ($showImportedFields == "1" || ($showImportedFields == "0" && $fieldName == '0'));

                    if (!$showField)
                    {
                        if ($fieldName != '0')
                            $this->set('performsSilentImport', 1);
                        continue;
                    }
                    $fieldValue = $profileData->getFieldWithUserState('field' . $field->id);
                    if (empty($fieldValue)) // Just fetch it from the social network (no real post data is checked here)
                    $fieldValue = $profileData->getFieldWithUserState($field->id);

                    if (($field->type == "date" || $field->type == "birthdate"))
                        $fieldValue = $this->getJomSocialDate($fieldValue);
                    if ($field->type == "url" && is_array($fieldValue))
                        $fieldValue = implode("", $fieldValue);

                    $field->value = $fieldValue;

                    if ($showField)
                    {
                        $hasVisibleFields = true;
                        $groupHtml .= '<dt>' . $field->name . "</dt><dd>" . CProfileLibrary::getFieldHTML($field) . "</dd>";
                    }
                }
                if ($hasVisibleFields)
                    $html .= "<fieldset><legend>" . $group->name . "</legend>";

                if ($groupHtml != "")
                    $html .= '<dl>' . $groupHtml . '</dl>';

                if ($hasVisibleFields)
                    $html .= "</fieldset>";
            }

            if (!$this->settings->get('skip_tos'))
            {
                CFactory::load('configuration', 'model');
                $cConfig = CConfig::getInstance();
                $reg_tos_enabled = $cConfig->get('enableterms');

                //Load javascript for popup
                $doc = JFactory::getDocument();
                $doc->addScript(JURI::root() . 'components/com_community/assets/joms.jquery.js');
                $doc->addScript(JURI::root() . 'components/com_community/assets/script-1.2.js');
                $doc->addScript(JURI::root() . 'components/com_community/assets/window-1.0.js');
                $doc->addScript(JURI::root() . 'components/com_community/assets/joms.ajax.js');
                $doc->addStyleSheet(JURI::root() . 'components/com_community/assets/window.css');

                if ($reg_tos_enabled)
                {
                    $html .= '<div>' . JText::_('COM_COMMUNITY_REGISTER_TITLE_TNC') . '</div>
                     <input type="checkbox" name="tnc" id="tnc" value="Y" class="inputbox required"/>' .
                            JText::_('COM_COMMUNITY_I_HAVE_READ') .
                            ' <a href="javascript:void(0);" onclick="joms.registrations.showTermsWindow();">' .
                            JText::_('COM_COMMUNITY_TERMS_AND_CONDITION') .
                            '</a>';
                }
                $html .= "<br/><br/>";
            }

            return $html;
        }*/

    protected function getEsFieldName($fieldName, $profileId)
    {
        $fieldsModel = Foundry::model('Fields');
        $pFields = $fieldsModel->getCustomFields(array('profile_id' => $profileId));

        foreach ($pFields as $field)
        {
            if ($field->unique_key == $fieldName)
            {
                $inputName = $field->id;
                break;
            }
        }
        return $inputName;
    }

    protected function saveProfileField($fieldId, $value)
    {
        $dataTable = Foundry::table('fielddata');
        $args = array('field_id' => $fieldId, 'uid' => $this->joomlaId);

        // Grab the field row for the user, or create it if it doesn't exist.
        if (!$dataTable->load($args))
        {
            $dataTable->field_id = $fieldId;
            $dataTable->uid = $this->joomlaId;
            $dataTable->type = SOCIAL_TYPE_USER;
        }

        $fieldTable = Foundry::table('field');
        $fieldTable->load($fieldId);
        $appsTable = Foundry::table('app');
        $appsTable->load($fieldTable->app_id);

        $data = $this->processField($appsTable->element, $value);
        $dataTable->data = $data->value;
        $dataTable->raw = $data->raw;
        $dataTable->store();
    }

    protected function setCoverPhoto($cover)
    {
        require_once(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php');

        $user = Foundry::user($this->joomlaId);
        $pid = $user->profile_id;
        $p = Foundry::table('Profile');
        $p->load($pid);

        $inputName = 'es-field-' . $this->getEsFieldName('AVATAR', $pid);

        $fileInfo = array();
        $fileInfo['name'] = "Social network cover";
        $fileInfo['tmp_name'] = $cover->get('path');
        $fileInfo['type'] = $cover->get('type');
        $fileInfo['error'] = 0;
        $fileInfo['size'] = 10000; // Not doing size checking since the provider has already resized down

        $data = $this->createCover($fileInfo, $inputName);
        if (!$data)
            return false;

        $coverObj = Foundry::makeObject($data);

        // Create the default album for this cover.
        $model = Foundry::model('Albums');
        $album = $model->getDefaultAlbum($user->id, SOCIAL_TYPE_USER, SOCIAL_ALBUM_PROFILE_COVERS);
        $album->user_id = $this->joomlaId;
        $album->store();

        // Check that cover being imported isn't the same as the one being used.
        $currentCover = Foundry::table('Photo');
        $currentCover->load($album->cover_id);
        // Would like to check for the filesize of the current cover, but the meta returned from here is missing an folder in the path for some reason
        // Need to investigate later what's going on.
        /*$photoMeta = Foundry::table('PhotoMeta');
        $photoMeta->load(array('photo_id' => $album->cover_id, 'property' => 'original'));
        $currentCoverSize = filesize($photoMeta->value);
        $newCoverSize = filesize($coverObj->original->path);
        */
        if ($currentCover->caption == "Imported from " . ucwords($this->network))
            return;

        // Once the album is created, create the photo object.
        $photo = SocialFieldsUserCoverHelper::createPhotoObject($user->id, SOCIAL_TYPE_USER, $album->id, $coverObj->stock->title, false);
        $photo->caption = "Imported from " . ucwords($this->network);
        $photo->assigned_date = Foundry::get('Date')->toMySQL();
        $photo->store();

        // Set the new album with the photo as the cover.
        $album->cover_id = $photo->id;
        $album->store();

        // Rename temporary folder to the destination.
        jimport('joomla.filesystem.folder');

        // Get destination folder path.
        $config = Foundry::config();
        $storage = JPATH_ROOT . '/' . Foundry::cleanPath($config->get('photos.storage.container'));

        // Test if the storage folder exists
        Foundry::makeFolder($storage);

        // Set the storage path to the album
        $storage = $storage . '/' . $album->id;

        // If album folder doesn't exist, create it.
        Foundry::makeFolder($storage);

        foreach ($coverObj as $key => $value)
        {
            SocialFieldsUserCoverHelper::createPhotoMeta($photo, $key, $storage . '/' . $value->file);
        }

        // Build the temporary path.
        $tmp = SocialFieldsUserCoverHelper::getPath($inputName);

        $state = JFolder::move($tmp, $storage . '/' . $photo->id);

        if (!$state)
        {
            $this->setError(JText::_('PLG_FIELDS_COVER_ERROR_UNABLE_TO_MOVE_FILE'));
            return false;
        }

        // Set the cover now.
        $cover = Foundry::table('Cover');
        $state = $cover->load(array('uid' => $user->id, 'type' => SOCIAL_TYPE_USER));

        // User does not have a cover.
        if (!$state)
        {
            $cover->uid = $user->id;
            $cover->type = SOCIAL_TYPE_USER;
        }

        // Set the cover to pull from photo
        $cover->setPhotoAsCover($photo->id);

        // Save the cover.
        return $cover->store();
    }

    // This is copied directly from /media/com_easysocial/apps/fields/user/cover/ajax.php
    // classname is the same as cover/cover.php though, so can't load that directly
    private function createCover($file, $inputName)
    {
        Foundry::import('apps:/fields/user/cover/helper');

        // Load our own image library
        $image = Foundry::image();

        // Generates a unique name for this image.
        $name = $file['name'] . $inputName . Foundry::date()->toMySQL();
        $name = md5($name);

        // Load up the file.
        $image->load($file['tmp_name'], $name);

        // Ensure that the image is valid.
        if (!$image->isValid())
        {
            return false;
        }

        // Get the storage path
        $storage = SocialFieldsUserCoverHelper::getStoragePath($inputName);

        // Create a new avatar object.
        $photos = Foundry::get('Photos', $image);

        // Create avatars
        $sizes = $photos->create($storage);

        // We want to format the output to get the full absolute url.
        $base = basename($storage);

        $result = array();

        foreach ($sizes as $size => $value)
        {
            $row = new stdClass();

            $row->title = $file['name'];
            $row->file = $value;
            $row->path = JPATH_ROOT . '/media/com_easysocial/tmp/' . $base . '/' . $value;
            $row->uri = rtrim(JURI::root(), '/') . '/media/com_easysocial/tmp/' . $base . '/' . $value;

            $result[$size] = $row;
        }

        return $result;
    }

    protected function setAvatar($socialAvatar)
    {
        require_once(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php');

        $model = Foundry::model('Albums');
        $album = $model->getDefaultAlbum($this->joomlaId, SOCIAL_TYPE_USER, SOCIAL_ALBUM_PROFILE_PHOTOS);
        $album->user_id = $this->joomlaId;
        $album->store();

        $socialAvatarFile = $this->getAvatarPath() . '/' . $socialAvatar;

        $user = Foundry::user($this->joomlaId);
        $currentPhoto = $user->getAvatarPhoto();
        // Lots more hacky than I'd like..
        if ($currentPhoto && $currentPhoto->title == "Profile Picture from " . ucwords($this->network))
            return true;

        $pid = $user->profile_id;
        $p = Foundry::table('Profile');
        $p->load($pid);

        $inputName = 'es-field-' . $this->getEsFieldName("AVATAR", $p->id);

        Foundry::import('apps:/fields/user/avatar/helper');
        $tmpPath = SocialFieldsUserAvatarHelper::getStoragePath($inputName);
        $tmpName = md5($socialAvatar . $inputName . Foundry::date()->toMySQL());

        $path = $tmpPath . '/' . $tmpName;
        JFile::copy($socialAvatarFile, $path);

        $value = new stdClass();
        $value->path = $path;
        $value->data = "";
        $value->name = "Profile Picture from " . ucwords($this->network);
        $value->type = "upload";
        Foundry::import('apps:/fields/user/avatar/avatar');

        $init = array();
        $init['group'] = SOCIAL_TYPE_USER;
        $avatarField = new SocialFieldsUserAvatar($init);
        return $avatarField->createAvatar($value, $this->joomlaId);
    }

//***************** Points *****************************//

    protected function awardPoints($userId, $name, $data)
    {
        require_once( JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php' );
        Foundry::points()->assign( $name, 'com_jfbconnect' , $userId );
    }

//***************** Import status and rendering ******************************//
    protected function setStatus($socialStatus)
    {
        $streamModel = Foundry::model('stream');
        $args = array();
        $args['userid'][0] = $this->joomlaId;
        $args['context'] = SOCIAL_TYPE_STORY;
        $args['type'] = SOCIAL_TYPE_USER;

        $stream = $streamModel->getStreamData($args);
        // Don't post a duplicate story.
        foreach ($stream as $story)
        {
            if ($story->content == $socialStatus)
                return true;
        }

        $story = Foundry::story(SOCIAL_TYPE_USER);
        if (Foundry::getLocalVersion() >= 1.2)
        {
            $params = array();
            $params['content'] = $socialStatus;
            $mood = Foundry::table('Mood');
            $params['mood'] = $mood; // Empty, but necessary in 1.2.0-1.2.3 and lower
            $params['contextType'] = SOCIAL_TYPE_STORY;
            $params['actorId'] = $this->joomlaId;

            $story->create($params);
        }
        else // ES 1.1 and lower way
        {
            // TODO: Add a language string or something for 'via Facebook', etc
            $story->create($socialStatus, 0, SOCIAL_TYPE_STORY, $this->joomlaId, '', null, array());
        }

        // @badge: story.create
        // Add badge for the author when a report is created.
        // Skipping badges and points for now.
        /*
        $badge = Foundry::badges();
        $badge->log('com_easysocial', 'story.create', $my->id, JText::_('COM_EASYSOCIAL_STORY_BADGE_CREATED_STORY'));

        // @points: story.create
        // Add points for the author when a report is created.
        $points = Foundry::points();
        $points->assign('story.create', 'com_easysocial', $my->id);
        */
    }

    public function jfbcImportConnections()
    {
        // Get original EasySocial connections
        $query = 'SELECT * FROM #__social_oauth WHERE `client`="facebook" AND `type`="user"';
        $this->db->setQuery($query);
        $esConnections = $this->db->loadObjectList();
        $userMapModel = JFBCFactory::usermap();

        foreach ($esConnections as $es)
            $userMapModel->map($es->uid, $es->oauth_id, 'facebook', $es->token);
    }

}
