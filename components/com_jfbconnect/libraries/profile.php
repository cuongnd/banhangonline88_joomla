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


jimport('sourcecoast.utilities');

class JFBConnectProfile
{
    var $provider;
    var $providerFields;

    public function __construct($provider)
    {
        $this->provider = $provider;
        $this->setProviderFields();
    }

    protected function setProviderFields()
    {
        $this->providerFields = null;
    }

    public function getPermissionsForFields($fields)
    {
        $perms = array();

        return $perms;
    }

    public function getProviderFields()
    {
        return $this->providerFields;
    }

    /**
     *  Get all permissions that are required by Facebook for email, status, and/or profile, regardless
     *    of whether they're set to required in JFBConnect
     * @return string Comma separated list of FB permissions that are required
     */
    static private $requiredScope;

    public function getRequiredScope()
    {
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
        $providerUserId = $this->provider->getProviderUserId();

        $fields = array_unique($fields);
        $profile = $this->fetchProfile($providerUserId, $fields);
        $profile->setFieldMap($fieldMap);
        return $profile;
    }

    /* Fetch a user's profile based on the passed in array of fields
    * @return JRegistry with profile field values
    */
    public function fetchProfile($providerUserId, $fields)
    {
        $profile = new JFBConnectProfileData();
        return $profile;
    }

    public function fetchStatus($providerUserId)
    {
        return "";
    }


    // Created for parity with JLinked/SourceCoast library
    // nullForDefault - If the avatar is the default image for the social network, return null instead
    // Prevents the default avatars from being imported
    function getAvatarUrl($fbUserId, $nullForDefault = true, $params = null)
    {
        return null;
    }

    function getProfileUrl($providerId)
    {
        return null;
    }

    function getCoverPhoto($providerId)
    {
        return null;
    }
}

// Declare a special JRegistry class with bindData specific to the J! version to prevent a strict standards
$jVersion = new JVersion();
if (version_compare($jVersion->getShortVersion(), '3.3.0beta', '>='))
{
    class JFBConnectProfileDataProxy extends JRegistry
    {
        /* bindData
         * Overridden function due to Joomla's checking of each variable to see if it's an associative array or not
         * We don't care, we want all arrays to be translated to a class
         */
        protected function bindData($parent, $data, $recursive = true)
        {
            // Ensure the input data is an array.
            if (is_object($data))
                $data = get_object_vars($data);
            else
                $data = (array)$data;

            foreach ($data as $k => $v)
            {
                if (is_array($v) || is_object($v))
                {
                    $parent->$k = new stdClass;
                    $this->bindData($parent->$k, $v);
                }
                else
                    $parent->$k = $v;
            }
        }
    }
}
else if (version_compare($jVersion->getShortVersion(), '3.0.0', '>='))
{
    class JFBConnectProfileDataProxy extends JRegistry
    {
        /* bindData
         * Overridden function due to Joomla's checking of each variable to see if it's an associative array or not
         * We don't care, we want all arrays to be translated to a class
         */
        protected function bindData($parent, $data)
        {
            // Ensure the input data is an array.
            if (is_object($data))
                $data = get_object_vars($data);
            else
                $data = (array)$data;

            foreach ($data as $k => $v)
            {
                if (is_array($v) || is_object($v))
                {
                    $parent->$k = new stdClass;
                    $this->bindData($parent->$k, $v);
                }
                else
                    $parent->$k = $v;
            }
        }
    }
}
else
{
    class JFBConnectProfileDataProxy extends JRegistry
    {
        protected function bindData(&$parent, $data)
        {
            // Ensure the input data is an array.
            if (is_object($data))
                $data = get_object_vars($data);
            else
                $data = (array)$data;

            foreach ($data as $k => $v)
            {
                if (is_array($v) || is_object($v))
                {
                    $parent->$k = new stdClass;
                    $this->bindData($parent->$k, $v);
                }
                else
                    $parent->$k = $v;
            }
        }
    }
}


class JFBConnectProfileData extends JFBConnectProfileDataProxy
{
    var $fieldMap;

    // All providers must support the retrieval of the following fields:
    // email, full_name, first_name, middle_name, last_name
    function get($path, $default = null)
    {
        if ($this->exists($path))
            return parent::get($path, $default);
        else
            return $default;
    }

    function setFieldMap($fieldMap)
    {
        $this->fieldMap = $fieldMap;
    }

    function getFieldWithUserState($field)
    {
        $val = JRequest::getVar($field, '', 'POST');
        // Check if there's a session variable from a previous POST, and use that
        if (empty($val))
        {
            $app = JFactory::getApplication();
            $prevPost = $app->getUserState('com_jfbconnect.registration.data', array());
            if (array_key_exists($field, $prevPost))
                $val = $prevPost[$field];
        }

        if (empty($val))
            $val = $this->getFieldFromMapping($field);

        return $val;
    }

    function getFieldFromMapping($field)
    {
        if (!property_exists($this->fieldMap, $field))
            return "";

        $fieldName = $this->fieldMap->$field;
        return $this->get($fieldName, "");
    }

}

// Deprecated as of v5.1. Use JFBCFactory going forward
class JFBConnectProfileLibrary extends JFBConnectProfileFacebook
{

}