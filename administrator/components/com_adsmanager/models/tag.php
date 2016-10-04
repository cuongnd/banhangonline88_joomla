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
class AdsmanagerModelTag extends TModel
{
    function getTags($type="content",$filter=null) {
    	$this->_db->setQuery( "SELECT value FROM #__adsmanager_tags WHERE type= ".$this->_db->Quote($type).
    			              " AND value LIKE ".$this->_db->Quote("%$filter%") );

		$results = $this->_db->loadObjectList();
		
		$tags =array();
		foreach($results as $r) {
			$tags[] = array("text"=>$r->value,"value"=>$r->value);
		}
		return $tags;
    }
}