<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

defined('_JEXEC') or die();

class AdsmanagerTableMail extends JTable
{
	var $id = null;
	var $from = null;
    var $fromname = null;
    var $recipient = null;
	var $created_on = null;
	var $subject = null;
	var $body = null;
	var $statut = null;
			
    function __construct(&$db)
    {
    	parent::__construct( '#__adsmanager_pending_mails', 'id', $db );
    }
	
	function save($mail) {
		$this->_db->insertObject('#__adsmanager_pending_mails', $mail);
		return $this->_db->insertId();
    }
    
    function deleteContent($id) {
    	$app = JFactory::getApplication();

		$this->_db->setQuery("DELETE FROM #__adsmanager_pending_mails \nWHERE id = ".(int)$id);
		$this->_db->query();

        return true;
    }
}
	