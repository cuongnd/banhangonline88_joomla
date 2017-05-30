<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

jimport('sourcecoast.plugins.socialprofile');
jimport('sourcecoast.utilities');

class plgSocialProfilesJoomla extends SocialProfilePlugin
{

    function __construct(&$subject, $params)
    {
        parent::__construct($subject, $params);

        $this->defaultSettings->set('import_always', '0');
        $this->defaultSettings->set('registration_show_fields', '0'); //0=None, 1=Required, 2=All
        $this->defaultSettings->set('imported_show_fields', '0'); //0=No, 1=Yes

        //$this->defaultSettings->set('group_choices', array());

        // Set this for allowing registration through this component
        $this->registration_url = 'index.php?option=com_users&view=registration';
    }

    public function prefillRegistration()
    {
        $input = JFactory::getApplication()->input;
        if (($input->getCmd('option') == 'com_users' && $input->getCmd('view') == 'registration') ||
                ($input->getCmd('option') == 'com_jfbconnect' && $input->getCmd('view') == 'loginregister')
        )
        {
            $profileData = $this->profileLibrary->fetchProfile($this->socialId, array('full_name', 'first_name', 'last_name', 'email'));
            $this->setRegistrationField('name', $profileData->get('full_name'));
            $this->setRegistrationField('username', $this->getAutoUsername($profileData));
            $this->setRegistrationField('email1', $profileData->get('email'));
            $this->setRegistrationField('email2', $profileData->get('email'));

            $profileData = $this->fetchProfileFromFieldMap();
            $this->prefillProfileFields($profileData);
            return true;
//                    return $this->finalizeRegistration();
        }
        return false;
    }

    // Used by prefillRegistration and the Normal JFBConnect registration mode
    private function prefillProfileFields($profileData)
    {
        foreach ($profileData->fieldMap as $f => $k)
        {
            if ($k)
            {
                $data = $this->formatField($f, $profileData->get($k));
                $this->setRegistrationField('profile.' . $f, $data);
            }
        }
    }

    private function setRegistrationField($name, $value)
    {
        $app = JFactory::getApplication();
        $cs = $app->getUserState('com_users.registration.data');
        $state = new JRegistry();
        $state->loadArray($cs);
        if (!$state->exists($name))
            JFactory::getApplication()->setUserState('com_users.registration.data.' . $name, $value);
    }

    protected function getRegistrationForm($profileData)
    {
        $this->prefillProfileFields($profileData);
        return '';

        /*  Implement this when we add the user-groups options

        $groupIds = $this->settings->get('group_choices');
        $html = "";
        if (!empty($groupIds))
        {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id, title')->from('#__usergroups')->where('id IN (' . implode(',', $groupIds) . ')');

            $db->setQuery($query);
            $groupOptions = $db->loadAssocList('id');
            $html = "<fieldset><dl>";
            $html .= "<dt>Select your group</dt>";
            $html .= "<dd>";
            $html .= JHTML::_('select.genericlist', $groupOptions, 'joomla_group', null, 'id', 'title', '2', 'joomla_group_id');
            $html .= "</dd>";
            $html .= "</dl></fieldset>";
        }
        return $html;*/
    }

    protected function saveProfileField($fieldId, $value)
    {
        $value = $this->formatField($fieldId, $value);
        // get original ordering or get last ordering
        $query = $this->db->getQuery(true);
        $query->select($this->db->qn('ordering'))
                ->from($this->db->qn('#__user_profiles'))
                ->where($this->db->qn('profile_key') . '=' . $this->db->q('profile.' . $fieldId))
                ->where($this->db->qn('user_id') . '=' . $this->db->q($this->joomlaId));
        $this->db->setQuery($query);
        $order = $this->db->loadResult();
        if (!$order)
        {
            $query = $this->db->getQuery(true);
            $query->select($this->db->qn('ordering'))
                    ->from($this->db->qn('#__user_profiles'))
                    ->where($this->db->qn('user_id') . '=' . $this->db->q($this->joomlaId))
                    ->order($this->db->qn('ordering') . " DESC");
            $this->db->setQuery($query);
            $order = $this->db->loadResult();
            $order++;
        }

        // Delete the row for this field. Joomla doesn't do updates, so we won't either
        $query = $this->db->getQuery(true);
        $query->delete($this->db->qn('#__user_profiles'))
                ->where($this->db->qn('user_id') . '=' . $this->db->q($this->joomlaId))
                ->where($this->db->qn('profile_key') . '=' . $this->db->q('profile.' . $fieldId));
        $this->db->setQuery($query);
        $this->db->execute();

        // Insert the new row
        $value = $this->formatField($fieldId, $value);
        $query = $this->db->getQuery(true);
        $query->insert($this->db->qn('#__user_profiles'))
                ->set($this->db->qn('user_id') . '=' . $this->db->q($this->joomlaId))
                ->set($this->db->qn('profile_key') . '=' . $this->db->q('profile.' . $fieldId))
                ->set($this->db->qn('profile_value') . '=' . $this->db->q(json_encode($value)))
                ->set($this->db->qn('ordering') . '=' . $this->db->q($order));
        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function createUser($profileData)
    {
        //Sanitize the date
        $tuples = array();
        $order = 1;

        foreach ($profileData->fieldMap as $k => $map)
        {
            if ($map)
            {
                $v = $profileData->get($map);
                $v = $this->formatField($k, $v);
                $tuples[] = '(' . $this->joomlaId . ', ' . $this->db->quote('profile.' . $k) . ', ' . $this->db->quote(json_encode($v)) . ', ' . $order++ . ')';
            }
        }
        if (count($tuples) > 0)
        {
            $this->db->setQuery('INSERT INTO #__user_profiles VALUES ' . implode(', ', $tuples));
            try
            {
                $this->db->query();
            }
            catch (Exception $e)
            {
                // The above fails in JFBConnect reg mode because the user profile has already been created by Joomla
                // Need to add more visibility as to the registration mode in this function in a future release to detect/skip this
            }
        }

    }

    private function formatField($fieldId, $value)
    {
        if ($fieldId == 'dob')
        {
            $value = new JDate($value);
            $value = $value->format('Y-m-d');
        }
        return $value;
    }

    protected function getProfileFields()
    {
        $fields = array();
        if (JPluginHelper::isEnabled('user', 'profile'))
        {
            SCStringUtilities::loadLanguage('plg_user_profile', JPATH_ADMINISTRATOR);
            $form = JForm::getInstance('profile', JPATH_SITE . '/plugins/user/profile/profiles/profile.xml');
            $allFields = $form->getFieldset();
            foreach ($allFields as $f)
            {
                if ($f->name != 'profile[spacer]' && $f->fieldname != 'tos')
                    $fields[] = (object)array('id' => $f->fieldname, "name" => $f->title);
            }
        }
        else
        {
            $fields[] = (object)array('id' => 'xyz', 'name' => 'User - Profile plugin not enabled');
        }

        return $fields;
    }

    /* Implement this when we add the user-groups options
    protected function onRegister()
    {
        if (empty($this->joomlaId) || empty($this->socialId))
            return; // Something's wrong, we should have both of these

        $groupId = JFactory::getApplication()->input->getInt('joomla_group');
        if (!empty($groupId))
        {
            // Security check: Group must be in options provided
            if (in_array($groupId, $this->settings->get('group_choices')))
            {
                JUserHelper::setUserGroups($this->joomlaId, array($groupId));
            }
        }
    }*/

    /* TODO: Move to this in v6.x when we load socialId and joomlaId always
    public function onContentPrepareData($formName, $data)
    {
        if ($formName == 'com_users.registration')
        {
            $profileData = $this->profileLibrary->fetchProfile($this->socialId, array('full_name', 'first_name', 'last_name', 'email'));
            $this->setRegistrationData($data, 'name', $profileData->get('full_name'));
            $this->setRegistrationData($data, 'username', $this->getAutoUsername($profileData));
            $this->setRegistrationData($data, 'email1', $profileData->get('email'));
            $this->setRegistrationData($data, 'email2', $profileData->get('email'));
        }
    }

    private function setRegistrationData(&$data, $name, $value)
    {
        if (!isset($data->$name))
            $data->$name = $value;
    }*/

}