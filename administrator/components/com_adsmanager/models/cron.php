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
class AdsmanagerModelCron extends TModel
{
	var $_conf;
	
	function getLastCronTime() {
		$this->_db->setQuery( "CREATE TABLE IF NOT EXISTS #__adsmanager_cron (last_time datetime default NULL)");
		$this->_db->query();
		
		$this->_db->setQuery( "SELECT last_time FROM #__adsmanager_cron");
		$result = $this->_db->loadResult();
		if ($result == false) {
			$result = time();
			$this->_db->setQuery( "INSERT INTO #__adsmanager_cron (last_time) VALUES (".$this->_db->Quote($result).")");
			$this->_db->query();
		} else {
			$result = strtotime($result);
		}
		return $result;
	}

	function saveCronTime($time) {
		$data = new stdClass();
		$date = date('Y-m-d H:i:s',$time);
		$this->_db->setQuery( "UPDATE #__adsmanager_cron SET last_time = ".$this->_db->Quote($date));
		$this->_db->query();
	}
}
