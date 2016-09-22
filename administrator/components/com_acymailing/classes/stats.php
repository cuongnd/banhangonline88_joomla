<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class statsClass extends acymailingClass{

	var $tables = array('urlclick','userstats','stats');
	var $pkey = 'mailid';

	var $countReturn = true;

	var $subid = 0;
	var $mailid = 0;


	function saveStats(){
		$subid = empty($this->subid) ? JRequest::getInt('subid') : $this->subid;
		$mailid = empty($this->mailid) ? JRequest::getInt('mailid') : $this->mailid;
		if(empty($subid) OR empty($mailid)) return false;

		$db = JFactory::getDBO();
		$db->setQuery('SELECT open FROM '.acymailing_table('userstats').' WHERE mailid = '.$mailid.' AND subid = '.$subid.' LIMIT 1');
		$actual = $db->loadObject();
		if(empty($actual)) return false;

		$this->database->setQuery('UPDATE #__acymailing_subscriber SET lastopen_date = '.time().' WHERE subid = '.$subid);
		$this->database->query();

		$open = 0;

		if(empty($actual->open)){
			$open = 1;
			$unique = ',openunique = openunique +1';
		}elseif($this->countReturn){
			$open = $actual->open +1;
			$unique = '';
		}

		if(empty($open)) return true;

		$ipClass = acymailing_get('helper.user');
		$ip = $ipClass->getIP();

		$db->setQuery('UPDATE '.acymailing_table('userstats').' SET open = '.$open.', opendate = '.time().', `ip`= '.$db->Quote($ip).' WHERE mailid = '.$mailid.' AND subid = '.$subid.' LIMIT 1');
		if(!$db->query()){
			acymailing_display($db->getErrorMsg(),'error');
			exit;
		}

		$db->setQuery('UPDATE '.acymailing_table('stats').' SET opentotal = opentotal +1 '.$unique.' WHERE mailid = '.$mailid.' LIMIT 1');
		$db->query();

		if(!empty($subid)){
			$filterClass = acymailing_get('class.filter');
			$filterClass->subid = $subid;
			$filterClass->trigger('opennews');
		}

		$classGeoloc = acymailing_get('class.geolocation');
		$classGeoloc->saveGeolocation('open', $subid);

		JPluginHelper::importPlugin('acymailing');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onAcyOpenMail',array($subid,$mailid));

		return true;
	}

}
