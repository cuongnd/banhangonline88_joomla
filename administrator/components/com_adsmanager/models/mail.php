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
class AdsmanagerModelMail extends TModel
{
	function getMail($id) {
    	$this->_db->setQuery( "SELECT * FROM #__adsmanager_pending_mails WHERE id = ".(int)$id);
		$mail = $this->_db->loadObject();
		return $mail;
    }
    
    function getNbMails()
	{
		$query =  " SELECT count(*) FROM #__adsmanager_pending_mails";
		$this->_db->setQuery($query);				 
		$nb = $this->_db->loadResult();
		return $nb;
	}
    
    function getMails() {
    	$this->_db->setQuery( "SELECT * FROM #__adsmanager_pending_mails ORDER BY created_on DESC");
		$mails = $this->_db->loadObjectList();
		return $mails;
    }
}
