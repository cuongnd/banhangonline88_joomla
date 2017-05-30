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

class plgSocialProfilesHikashop extends SocialProfilePlugin
{
    function __construct(&$subject, $params)
    {
        $this->_componentFolder = JPATH_SITE . '/components/com_hikashop';
        $this->_componentFile = '';

        parent::__construct($subject, $params);
        $this->defaultSettings->set('import_always', '0');
        $this->defaultSettings->set('registration_show_fields', '0'); //0=None, 1=Required, 2=All
        $this->defaultSettings->set('imported_show_fields', '0'); //0=No, 1=Yes
    }

    protected function getRegistrationForm($profileData)
    {
        $showRegistrationFields = $this->settings->get('registration_show_fields');
        $showImportedFields = $this->settings->get('imported_show_fields');

        $html = "";

        $profileFields = $this->getProfileFields();
        $fieldMap = $this->getFieldMap($this->network);

        if (!include_once(rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_hikashop' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php'))
            return true;
        $fieldsClass = hikashop_get('class.field');

        $null = array();
        $fieldsClass->addJS($null, $null, $null);
        $fieldsClass->jsToggle($this->userFields, null, 0);
        $fieldsClass->jsToggle($this->addressFields, null, 0);

        $title = false;
        $html .= count($profileFields) ? '<fieldset>' : '';

        $fieldsShown = false;
        foreach ($profileFields as $profileField)
        {
            $mapName = $profileField->field_table . '|' . $profileField->field_namekey;
            $fieldName = property_exists($fieldMap, $mapName) ? $fieldMap->$mapName : 0;

            // Show All/Required Fields. Hide mapped fields if not showing imported fields
            $showField = ($showRegistrationFields == '2' || ($profileField->field_required && $showRegistrationFields == '1')) &&
                    ($showImportedFields == "1" || ($showImportedFields == "0" && $fieldName == '0'));

            if (!$showField)
                continue;

            $fieldsShown = true;
            if ($profileField->field_table == 'address' && !$title)
            {
                $html .= '<legend>' . JText::_('ADDRESS_INFORMATION') . '</legend>';
                $title = true;
            }

            // Recreating the getFieldWithUserState function here, since it's saved in an array. If more extensions do this, we'll need to add this
            // to the base socialprofile class.
            $fieldValue = null;
            $app = JFactory::getApplication();
            $prevPost = $app->getUserState('com_jfbconnect.registration.data', array());
            if (isset($fieldMap->$mapName))
            {
                $checkField = $fieldMap->$mapName;
                if (isset($prevPost['data']) && isset($prevPost['data'][$profileField->field_table]) && isset($prevPost['data'][$profileField->field_table][$checkField]))
                    $fieldValue = $prevPost['data'][$profileField->field_table][$checkField];
            }

            if (empty($fieldValue))
                $fieldValue = $profileData->getFieldFromMapping($mapName);
            // End custom getFieldWithUserState

            $text = $fieldsClass->trans($profileField->field_realname);
            $html .= '<label title="" class="required" aria-invalid="false" for="' . $fieldName . '" id="' . $fieldName . '-lbl">';
            $html .= $text . ':';
            if ($profileField->field_required)
            {
                $html .= '<span class="star">&nbsp;*</span>';
            }
            $html .= '</label>';

            $onWhat = 'onchange';
            if ($profileField->field_type == 'radio')
                $onWhat = 'onclick';

            $requiredOption = $profileField->field_required ? ' aria-required="true" required="required" class="required" ' : '';

            $element = $fieldsClass->display(
                    $profileField,
                    $fieldValue,
                    'data[' . $profileField->field_table . '][' . $fieldName . ']',
                    false,
                    $requiredOption . $onWhat . '="hikashopToggleFields(this.value,\'' . $fieldName . '\',\'' . $profileField->field_table . '\',0);"',
                    false);

            //hikashop doesnt add class="required" to the normal input box so we add it here
            $fieldType = strtolower($profileField->field_type);
            if ($fieldType == 'text' && $profileField->field_required)
            {
                $element = str_replace('class="', 'class="required ', $element);
            }

            $html .= $element;

        }

        $html = $fieldsShown ? $html . "</fieldset>" : ""; // No fields, no HTML

        if (count($profileFields))
        {
            JFactory::getDocument()->addStyleDeclaration("
                #jfbc_loginregister_newuser span.hikashop_field_required {display:none;}
            "
            );
        }

        return $html;
    }

    protected function createUser($profileData)
    {
        if (!include_once(rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_hikashop' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php'))
            return true;

        $data = JRequest::getVar('data', '', 'POST');

        $addressData = new stdClass();
        $userData = new stdClass();

        foreach ($this->getProfileFields() as $hikaFieldName => $profileField)
        {
            $mapName = $profileField->field_table . '|' . $profileField->field_namekey;
            $profileFieldName = property_exists($profileData->fieldMap, $mapName) ? $profileData->fieldMap->$mapName : 0;

            if ($profileField->field_table == 'address')
            {
                if (isset($data['address']) && isset($data['address'][$profileFieldName]))
                    $addressData->$hikaFieldName = $data['address'][$profileFieldName];
                else
                    $addressData->$hikaFieldName = $profileData->getFieldFromMapping('address|' . $hikaFieldName);
            }
            elseif ($profileField->field_table == 'user')
            {
                if (isset($data['user']) && isset($data['user'][$profileFieldName]))
                    $userData->$hikaFieldName = $data['user'][$profileFieldName];
                else
                    $userData->$hikaFieldName = $profileData->getFieldFromMapping('user|' . $hikaFieldName);
            }
        }

        // save hikashop user
        $userData->user_cms_id = $this->joomlaId;

        //$userData->user_id = $this->joomlaId;
        $userClass = hikashop_get('class.user');
        $userData->user_id = $userClass->getID($userData->user_cms_id, 'cms');

        $hikashop_user_id = $userClass->save($userData);

        //save hikashop user address       
        $addressData->address_user_id = $hikashop_user_id;
        $addressClass = hikashop_get('class.address');
        $addressClass->save($addressData);
    }

    /**
     * Not supporting profile import on login for now.
     * The below code doesn't currently work yet.. also, not sure we want to give the option to update Address info on every login
     * Think this will cause more problems than it's worth.
     */
/*    protected function saveProfileField($fieldId, $value)
    {
        $addressData = new stdClass();
        $userData = new stdClass();

        $userClass = hikashop_get('class.user');
        $userData->user_cms_id = $this->joomlaId;
        $userData->user_id = $userClass->getID($userData->user_cms_id, 'cms');

        list($table, $fieldName) = explode("|", $fieldId);
        if ($table == "address")
        {
            $addressData->$fieldName = $value;
            $addressData->address_user_id = $userData->user_id;
            $addressClass = hikashop_get('class.address');
            $addressClass->save($addressData);
        }
        else if ($table == "user")
        {
            $userData->$fieldName = $value;
            $userClass->save($userData);
        }

        return true;
    }*/

    protected function getProfileFields()
    {
        if (!include_once(rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_hikashop' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php'))
            return true;

        // $filter = ' WHERE a.field_table=\'address\' OR a.field_table=\'user\'';
        // $this->db->setQuery('SELECT a.field_id AS id, a.field_realname AS name, a.* FROM '.hikashop_table('field').' AS a'.$filter.' ORDER BY a.`field_table` ASC, a.`field_ordering` ASC');
        // $fields = $this->db->loadObjectList();

        $fieldsClass = hikashop_get('class.field');

        $null = null;
        $this->userFields = $fieldsClass->getFields('frontcomp', $null, 'user');
        $this->addressFields = $fieldsClass->getFields('frontcomp', $null, 'address');

        $fields = array_merge($this->userFields, $this->addressFields);

        $returnFields = array();

        foreach ($fields as $field)
        {
            if ($field->field_type == "text" ||
                    $field->field_type == "text_area" ||
                    $field->field_type == "date" ||
                    $field->field_type == "link" ||
                    $field->field_type == "wysiwyg" ||
                    $field->field_type == "customtext"
            )
            {
                $field->id = $field->field_table . '|' . $field->field_namekey;
                $field->name = $field->field_realname;
                $returnFields[$field->field_namekey] = $field;
            }
        }

        return $returnFields;
    }
}