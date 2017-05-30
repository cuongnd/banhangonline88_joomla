<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_adsmanager'.DS.'tables');

/**
 * @package		Joomla
 * @subpackage	Contact
 */
class AdsmanagerModelPosition extends TModel
{
    function getPositions($type = 'details') {
    	if ($type != null) {
    		$where = "type = ".$this->_db->Quote($type);
    	} else {
    		$where = 1;
    	}
    	$this->_db->setQuery( "SELECT * FROM #__adsmanager_positions WHERE $where " );

		$positions = $this->_db->loadObjectList();
		return $positions;
    }
    
    function cleanFieldsByPosition($type = 'details') {
    	if ($type == "details") {
    		$this->_db->setQuery("UPDATE #__adsmanager_fields SET pos = -1");
    		$this->_db->query();
    	} else {
    		//do nothing (done in setPosition)
    	}
    }
    
    function setPosition($id,$title,$listfields,$type="details") {
    	$obj = new stdClass();
    	$obj->id = $id;
    	$obj->title = $title;
    	$this->_db->updateObject('#__adsmanager_positions',$obj,'id');
    	
    	if ($type != "details") {
    		$this->_db->setQuery("DELETE FROM #__adsmanager_field2position WHERE positionid = ".(int)$id);
    		$this->_db->query();
    	}
    	
    	$list = explode(',',$listfields);
    	foreach($list as $key => $fieldid) {
    		if ($type == "details") {
    			$obj = new stdClass();
    			$obj->fieldid = $fieldid;
    			$obj->posorder = $key;
    			$obj->pos = $id;
    			$this->_db->updateObject('#__adsmanager_fields',$obj,'fieldid');
    		} else {
    			$obj = new stdClass();
    			$obj->fieldid = $fieldid;
    			$obj->ordering = $key;
    			$obj->positionid = $id;
    			$this->_db->insertObject('#__adsmanager_field2position',$obj);
    		}
    	}
    }
}