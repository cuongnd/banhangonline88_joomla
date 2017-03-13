<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.utilities.date');
jimport('sourcecoast.plugins.socialprofile');
jimport('sourcecoast.utilities');

class plgSocialProfilesKunena extends SocialProfilePlugin
{
    function __construct(&$subject, $params)
    {
        $this->_componentFolder = JPATH_SITE . '/components/com_kunena';

        parent::__construct($subject, $params);
        $this->defaultSettings->set('import_avatar', '1');
        $this->defaultSettings->set('import_always', '0');
    }

    protected function createUser($profileData)
    {
        $query = "INSERT IGNORE INTO #__kunena_users (userid) VALUES (" . $this->db->quote($this->joomlaId) . ")";
        $this->db->setQuery($query);
        $this->db->execute();

        foreach ($this->getProfileFields() as $field)
            $this->saveProfileField($field->id, $profileData->getFieldWithUserState($field->id));
    }

    protected function saveProfileField($fieldId, $value)
    {
        // Load the language file for gender used below
        SCStringUtilities::loadLanguage('com_jfbconnect');

        switch ($fieldId)
        {
            case "FACEBOOK":
                $value = str_replace('http://www.facebook.com/', '', $value);
                $value = str_replace('https://www.facebook.com/', '', $value);
                break;
            case "birthdate":
                $value = new JDate($value);
                $value = $value->toSql();
                break;
            case "gender":
                switch ($value)
                {
                    case 'male':
                        $value = 1;
                        break;
                    case 'female':
                        $value = 2;
                        break;
                    default:
                        $value = 0;
                        break;
                }
                break;
        }
        $this->db->setQuery("UPDATE #__kunena_users SET `" . $fieldId . "` = " . $this->db->quote($value) . " WHERE userid=" . $this->joomlaId);
        $this->db->execute();
    }

    protected function setDefaultAvatar()
    {
        $query = "UPDATE #__kunena_users SET `avatar`=NULL WHERE userid = " . $this->joomlaId;
        $this->db->setQuery($query);
        $this->db->execute();
        return true;
    }

    protected function setAvatar($socialAvatar)
    {
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');

        $errorDetected = false;
        // Get a hash for the file name.
        $socialAvatarFile = $this->getAvatarPath() . '/' . $socialAvatar;

        //@todo: configurable path for avatar storage?
        $socialExtension = substr($socialAvatar, strpos($socialAvatar, '.'));
        switch ($socialExtension)
        {
            case ".png" :
            case ".gif" :
            case ".jpg" :
                break;
            default:
                $app = JFactory::getApplication();
                $app->enqueueMessage("File type not supported for user " . $this->joomlaId . ", Avatar '" . $socialAvatar . "', type '" . $socialExtension . "'", 'error');
                $errorDetected = true;
        }
        if ($errorDetected)
            return false;

        $storage = JPATH_ROOT . '/media/kunena/avatars/users';
        $avatarImageName = 'avatar' . $this->joomlaId . $socialExtension;

        $storageImage = $storage . '/' . $avatarImageName;

        // Delete any old resized avatars so they'll be regenerated
        $query = "SELECT `avatar` FROM #__kunena_users WHERE userid = " . $this->joomlaId;
        $this->db->setQuery($query);
        $oldAvatar = $this->db->loadResult();
        $deletelist = JFolder::folders(JPATH_ROOT . '/media/kunena/avatars/resized', '.', false, true);
        foreach ($deletelist as $delete)
        {
            if (is_file($delete . '/' . $oldAvatar))
                JFile::delete($delete . '/' . $oldAvatar);
        }

        if (JFile::exists($socialAvatarFile))
            JFile::copy($socialAvatarFile, $storageImage);
        else
            return false;

        $query = "UPDATE #__kunena_users SET `avatar` = " . $this->db->quote('users/' . $avatarImageName) . " WHERE userid = " . $this->joomlaId;
        $this->db->setQuery($query);
        $this->db->execute();

        return true;
    }

    protected function getProfileFields()
    {
        $kunenaFields = array();
        $kunenaFields[] = (object)array('id' => "signature", "name" => "Signature");
        $kunenaFields[] = (object)array('id' => "personalText", "name" => "Personal Text");
        $kunenaFields[] = (object)array('id' => "gender", "name" => "Gender");
        $kunenaFields[] = (object)array('id' => "birthdate", "name" => "Birthdate");
        $kunenaFields[] = (object)array('id' => "location", "name" => "Location");
        $kunenaFields[] = (object)array('id' => "FACEBOOK", "name" => "Facebook URL");
        $kunenaFields[] = (object)array('id' => "LINKEDIN", "name" => "LinkedIn URL");
        $kunenaFields[] = (object)array('id' => "ICQ", "name" => "ICQ");
        $kunenaFields[] = (object)array('id' => "AIM", "name" => "AIM");
        $kunenaFields[] = (object)array('id' => "YIM", "name" => "YIM");
        $kunenaFields[] = (object)array('id' => "MSN", "name" => "MSN");
        $kunenaFields[] = (object)array('id' => "SKYPE", "name" => "Skype");
        $kunenaFields[] = (object)array('id' => "GTALK", "name" => "GTalk");
        $kunenaFields[] = (object)array('id' => "websitename", "name" => "Website Name");
        $kunenaFields[] = (object)array('id' => "websiteurl", "name" => "Website URL");

        return $kunenaFields;
    }
}