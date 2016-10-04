<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * @package		Joomla
 * @subpackage	Contact
 */
class AdsmanagerModelConfiguration extends TModel
{
	var $_conf;
	
	function getConfiguration() {
    	if ($this->_conf)
    		return $this->_conf;
    	else {
    		$this->_db->setQuery( "SELECT * FROM #__adsmanager_config");
			$this->_conf = $this->_db->loadObject();
			$params = json_decode($this->_conf->params);
			if ($params != null) {
				foreach($params as $name => $value) {
					$this->_conf->$name = $value;
					if($name == 'max_width_m' && $value == '/')
                        $this->_conf->$name = 300;
                    if($name == 'max_height_m' && $value == '/')
                        $this->_conf->$name = 200;
				}
			}
			if (!isset($this->_conf->display_nb_categories_per_row)) {
				$this->_conf->display_nb_categories_per_row = 3;
			}
			if (!isset($this->_conf->globalfilter_user)) {
				$this->_conf->globalfilter_user = 1;
			}
			if (!isset($this->_conf->globalfilter_fieldname)) {
				$this->_conf->globalfilter_fieldname = "";
			}	
			if (!isset($this->_conf->crontype)) {
				$this->_conf->crontype = "cron";
			}	
			if (!isset($this->_conf->wizard_form)) {
				$this->_conf->wizard_form = 0;
			}
			if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
                if (JRequest::getVar('c','') != "configuration") {
                    $user = JFactory::getUser();
                    $groups = JHTMLAdsmanagerUserGroups::getUserGroup($user->id);
                    if (isset($this->_conf->nb_images_groups)) {
                        foreach($this->_conf->nb_images_groups as $key => $group) {
                            if (in_array($group,$groups)) {
                                $this->_conf->nb_images = $this->_conf->nb_images_value[$key];
                            }
                        }
                    }

                    if (isset($this->_conf->nb_ads_by_user_groups)) {
                        foreach($this->_conf->nb_ads_by_user_groups as $key => $group) {
                            if (in_array($group,$groups)) {
                                $this->_conf->nb_ads_by_user = $this->_conf->nb_ads_by_user_value[$key];
                            }
                        }
                    }
                }
            }
			
			return $this->_conf;
    	}
    }
    
    function updateUpdateSiteXML($dlid) {
    	$db = JFactory::getDbo();
    	$db->setQuery("SELECT e.extension_id
							FROM #__extensions AS e
							WHERE e.type = 'component' AND e.element = 'com_adsmanager'");
    	$extension_id = $db->loadResult();
    		
    	$db->setQuery("DELETE FROM #__update_sites WHERE update_site_id IN
					       (SELECT ue.update_site_id FROM #__update_sites_extensions AS ue
							WHERE ue.extension_id = ".(int)$extension_id.")");
    	$db->query();
    		
    	$db->setQuery("DELETE FROM #__update_sites_extensions WHERE extension_id = ".(int)$extension_id);
    	$db->query();
    		
    	$data = new stdClass();
    		
    	$data->location = "http://www.joomprod.com/updatestream?id=2&dummy=/extension.xml";  // Free URL
    	
    	$data->name ="AdsManager Update XML";
    	$data->type ="extension";
    	$data->enabled = 1;
    	$data->last_check_timestamp = 0;
    	$db->insertObject("#__update_sites",$data);
    		
    	$update_site_id = $db->insertid();
    		
    	$data = new stdClass();
    	$data->update_site_id = $update_site_id;
    	$data->extension_id = $extension_id;
    	$db->insertObject("#__update_sites_extensions",$data);
    }
}
