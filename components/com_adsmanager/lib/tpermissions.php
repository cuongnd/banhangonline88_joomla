<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_ADMINISTRATOR.'/components/com_adsmanager/models/category.php');
include_once(JPATH_ROOT.'/components/com_adsmanager/helpers/usergroups.php');

class TPermissions {
	static private $listcategories;
    static private $showContact;
    
    /**
     * Return in a array all the authorised categories
     * 
     * @param string $mode
     * @return Array
     */
	static public function getAuthorisedCategories($mode){
        
        //If the static variable is already set, we directly return it
        if (self::$listcategories == null) {
            $user = JFactory::getUser();

            $userGroups = array();

            //If the user is a guest, we authorise the public group
            //else we return the authorised group of the user
            if($user->guest)
                $userGroups[] = 1;
            else
                $userGroups = JHTMLAdsmanagerUserGroups::getUserGroup($user->id);

            $authorisedCategories = array();

            $modelCategories = new AdsmanagerModelCategory();
            $categories = $modelCategories->getCategories(false);

            foreach($categories as $category){
                    $labelUsergroups = 'usergroups'.$mode;
                    $catUserGroups = $category->$labelUsergroups;
                    $catUserGroupsArray = explode(',', $catUserGroups); //We load the usergroups authorised by the category
                    //If there is no usergroups saved, we authorised the category
                    if($catUserGroups != ''){
                        //We compare the authorised usergroups of the two array
                        //And save in a array the matching value.
                        foreach($userGroups as $userGroup){
                            if(array_search($userGroup, $catUserGroupsArray) !== false){
                                if(array_search($category->id, $authorisedCategories) === false)
                                    $authorisedCategories[] = $category->id;
                            }
                        }
                    } else {
                        if(array_search($category->id, $authorisedCategories) === false)
                            $authorisedCategories[] = $category->id;
                    }
            }

            self::$listcategories = $authorisedCategories;
        }
        return self::$listcategories;
    }
    
    /**
     * Return in a array all the authorised categories
     * 
     * @param string $mode
     * @return Array
     */
	static public function getAuthorisedFields($fields, $mode = 'read'){
        
        $user = JFactory::getUser();

        $userGroups = array();
        
        $authorisedFields = array();

        if(version_compare(JVERSION, '1.6', 'ge')) {
            //If the user is a guest, we authorise the public group
            //else we return the authorised group of the user
            if($user->guest)
                $userGroups[] = 1;
            else
                $userGroups = JHTMLAdsmanagerUserGroups::getUserGroup($user->id);

            foreach($fields as $field){
                    $userGroupsMode = 'usergroups'.'_'.$mode;
                    if(isset($field->options->$userGroupsMode)){
                        $fieldUserGroups = $field->options->$userGroupsMode;
                    } else {
                        $fieldUserGroups = '';
                    }
                    $fieldUserGroupsArray = explode(',', $fieldUserGroups); //We load the usergroups authorised by the field
                    //If there is no usergroups saved, we authorised the field
                    if($fieldUserGroups != ''){
                        //We compare the authorised usergroups of the two array
                        //And save in a array the matching value.
                        foreach($userGroups as $userGroup){
                            if(array_search($userGroup, $fieldUserGroupsArray) !== false){
                                if(array_search($field->fieldid, $authorisedFields) === false)
                                    $authorisedFields[$field->name] = $field;
                            }
                        }
                    } else {
                        if(array_search($field->fieldid, $authorisedFields) === false)
                            $authorisedFields[$field->name] = $field;
                    }
            }

        }else{
            $authorisedFields = $fields;
        }
        
        return $authorisedFields;
    }
    
    /**
     * Return if a user can see the ads contact
     * 
     * @return boolean
     */
	static public function checkRightContact(){
        
        //If the static variable is already set, we directly return it
        if (self::$showContact == null) {
            $user = JFactory::getUser();
            
            $conf = TConf::getConfig();
            
            $userGroups = array();

            if(version_compare(JVERSION, '1.6', 'ge')) {
                //If the user is a guest, we authorise the public group
                //else we return the authorised group of the user
                if($user->guest)
                    $userGroups[] = 1;
                else
                    $userGroups = JHTMLAdsmanagerUserGroups::getUserGroup($user->id);

                $authorisedContact = false;
                $allowedContacts = explode(',', $conf->show_contact);

                foreach ($userGroups as $userGroup) {
                    if(array_search($userGroup, $allowedContacts) !== false){
                        $authorisedContact = true;
                    }
                }

                self::$showContact = $authorisedContact;
            } else {
                self::$showContact = true;
            }
        }
        return self::$showContact;
    }
}