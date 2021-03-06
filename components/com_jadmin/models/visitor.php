<?php
/**
 * @package JAdmin!
 * @version 1.5.4.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU Lesser General Public License as published by
  the Free Software Foundation; either version 3 of the License, or (at your
  option) any later version.

  This program is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
  or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
  License for more details.

  You should have received a copy of the GNU Lesser General Public License
  along with this program.  If not, see http://www.gnu.org/licenses/.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class JAdminModelVisitor extends CMSModel
{
	var $_visitorId = null;
	var $_visitorParams = null;
	var $_visitorOperatingSystem = null;
	var $_visitor = null;
	var $_settings = null;
	var $_osList = array(
		// Match user agent string with operating systems
		'Windows 3.11' => 'Win16',
		'Windows 95' => '(Windows 95|Win95|Windows_95)',
		'Windows 98' => '(Windows 98|Win98)',
		'Windows 2000' => '(Windows NT 5\.0|Windows 2000)',
		'Windows XP' => '(Windows NT 5\.1|Windows XP)',
		'Windows Server 2003' => '(Windows NT 5\.2)',
		'Windows Vista' => '(Windows NT 6\.0)',
		'Windows 7' => '(Windows NT 7\.0)',
		'Windows NT 4.0' => '(Windows NT 4\.0|WinNT4\.0|WinNT|Windows NT)',
		'Windows ME' => 'Windows ME',
		'Open BSD' => 'OpenBSD',
		'Sun OS' => 'SunOS',
		'Ubuntu' => 'Ubuntu',
		'Debian' => 'Debian',
		'Fedora' => 'Fedora',
		'CentOS' => 'CentOS',
		'Linux' => '(Linux|X11)',
		'Mac OS' => '(Mac_PowerPC|Macintosh)',
		'QNX' => 'QNX',
		'BeOS' => 'BeOS',
		'OS/2' => 'OS/2',
		'Search Bot' => '(nuhk|Googlebot|Yammybot|Openbot|Slurp|MSNBot|Ask Jeeves/Teoma|ia_archiver)'
	);
	var $_spiderList = array('bot', 'ArchitextSpider', 'Googlebot', 'TeomaAgent',
		'Zyborg', 'Gulliver', 'Architext spider', 'FAST-WebCrawler',
		'Slurp', 'Ask Jeeves', 'ia_archiver', 'Scooter', 'Mercator',
		'crawler@fast', 'Crawler', 'InfoSeek Sidewinder',
		'almaden.ibm.com', 'appie 1.1', 'augurfind', 'baiduspider',
		'bannana_bot', 'bdcindexer', 'docomo', 'frooglebot', 'geobot',
		'henrythemiragorobot', 'sidewinder', 'lachesis', 'moget/1.0',
		'nationaldirectory-webspider', 'naverrobot', 'ncsa beta',
		'netresearchserver', 'ng/1.0', 'osis-project', 'polybot',
		'pompos', 'seventwentyfour', 'steeler/1.3', 'szukacz',
		'teoma', 'turnitinbot', 'vagabondo', 'zao/0', 'zyborg/1.0',
		'Lycos_Spider_(T-Rex)', 'Lycos_Spider_Beta2(T-Rex)',
		'Fluffy the Spider', 'Ultraseek', 'MantraAgent', 'Moget',
		'T-H-U-N-D-E-R-S-T-O-N-E', 'MuscatFerret', 'VoilaBot',
		'Sleek Spider', 'KIT_Fireball', 'WISEnut', 'WebCrawler',
		'asterias2.0', 'suchtop-bot', 'YahooSeeker', 'ai_archiver',
		'Jetbot', 'msnbot', 'Yahoo', 'Google', 'libwww-perl');

	function __construct()
	{
		$this->JAdminModelVisitor();
	}

	function JAdminModelVisitor()
	{
		parent::__construct();

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jadmdate.php';

		$this->_settings = & CMSModel::getInstance('Setting', 'JAdminModel');

		$user = & JFactory::getUser();

		$this->setVisitorId(md5(JRequest::getVar('HTTP_USER_AGENT', '', 'server').JRequest::getVar('REMOTE_ADDR', '0.0.0.0', 'server').$user->get('id')));

		$this->_loadVisitor();
	}

	function setVisitorId($visitorId)
	{
		$this->_visitorId = $visitorId;
	}

	function _loadVisitor()
	{
		$sql = "SELECT
		    visitor_id,
		    visitor_params
		FROM #__jadm_visitor
		WHERE visitor_id = ".$this->_db->Quote($this->_visitorId)."
		LIMIT 1;";
		$this->_db->setQuery($sql);

		$this->_visitor = $this->_db->loadAssoc();

		if(isset($this->_visitor['visitor_params']))
		{
			$this->_visitorParams = json_decode($this->_visitor['visitor_params']);
		}
		else
		{
			$this->_visitorParams = new stdClass();
		}

		if(!isset($this->_visitorParams->uri_history))
		{
			$this->_visitorParams->uri_history = array();
		}
	}

	function expireOldVisitorRecords()
	{
		$dateObj = new JAdminModelJADMDate();

		$lastCheckTime = $this->_settings->getSetting('last_monitor_expiration');

		// Expire record check every minute
		if($dateObj->toUnix() < ($lastCheckTime + 60)) return false;

		$this->_settings->setSetting('last_monitor_expiration', $dateObj->toUnix());
		$this->_settings->saveSettings();

		$expireTime = $dateObj->toUnix() - ($this->_settings->getSetting('activity_monitor_expiration') * 60);

		$sql = "DELETE FROM #__jadm_visitor
		WHERE visitor_mdate <= ".$expireTime.";";
		$this->_db->setQuery($sql);

		return $this->_db->query();
	}

	function track($force = false, $userId = null, $fullName = null, $userName = null, $email = null, $referrer = null, $lastUri = null)
	{
		$app = & JFactory::getApplication();

		if($app->getName() != 'site') return false;

		$this->expireOldVisitorRecords();

		$user = & JFactory::getUser();
		$uri = & JFactory::getURI();
		$date = new JAdminModelJADMDate();

		$nowUnixTime = $date->toUnix();

		$currentUri = ($lastUri) ? $lastUri : $uri->toString();

		// Don't track some activity
		if((strpos($currentUri, 'do_not_log') || preg_match('@(\.(?:gif|jpe?g|png|bmp|swf|js|css)$)@i', $currentUri)) && !$force)
		{
			$doNotLog = true;
		}
		else
		{
			$doNotLog = false;
		}

		if(!$doNotLog && !$force)
		{
			$this->_visitorParams->uri_history[] = $currentUri;

			if(count($this->_visitorParams->uri_history) > 15)
			{
				array_shift($this->_visitorParams->uri_history);
			}
		}

		$data = new stdClass();

		$data->visitor_id = $this->_visitorId;
		$data->user_id = ($userId) ? $userId : $user->get('id');
		$data->visitor_params = json_encode($this->_visitorParams);

		if(!$doNotLog)
		{
			$data->visitor_mdate = $nowUnixTime;
			if(!$force && !preg_match('@(^https://.*://)@i', $currentUri)) $data->visitor_last_uri = $currentUri;
		}

		if($data->user_id > 0)
		{
			// This user is logged in
			$data->visitor_name = ($fullName) ? $fullName : $user->get('name');
			$data->visitor_username = ($userName) ? $userName : $user->get('username');
			$data->visitor_email = ($email) ? $email : $user->get('email');
		}

		if(!isset($this->_visitor['visitor_id']))
		{
			// Visitor record doesn't exist yet, create it
			$visitorIp = JRequest::getVar('REMOTE_ADDR', '0.0.0.0', 'server');

			$location = $this->locateIp($visitorIp);

			$finalReferrer = ($referrer) ? $referrer : JRequest::getVar('HTTP_REFERER', null, 'server');

			$data->visitor_ip_address = $visitorIp;
			$data->visitor_browser = JRequest::getVar('HTTP_USER_AGENT', '', 'server');
			$data->visitor_city = $location['city'];
			$data->visitor_country = $location['country'];
			$data->visitor_country_code = $location['country_code'];
			if($finalReferrer) $data->visitor_referrer = $finalReferrer;
			$data->visitor_cdate = $nowUnixTime;
			$data->visitor_operating_system = $this->getOperatingSystem();
			$data->is_spider = (int)$this->isSpider(JRequest::getVar('HTTP_USER_AGENT', '', 'server'));

			$this->_db->insertObject('#__jadm_visitor', $data, 'visitor_id');
		}
		else
		{
			// Visitor record exists
			$this->_db->updateObject('#__jadm_visitor', $data, 'visitor_id');
		}

		return true;
	}

	function isSpider($userAgent)
	{
		$userAgent = trim($userAgent);

		if(empty($userAgent)) return true;

		foreach($this->_spiderList as $spiderSignature)
		{
			if(strpos($userAgent, $spiderSignature) !== FALSE) return true;
		}

		return false;
	}

	function getOperatingSystem()
	{
		if($this->_visitorOperatingSystem) return $this->_visitorOperatingSystem;

		$userAgent = JRequest::getVar('HTTP_USER_AGENT', '', 'server');

		foreach($this->_osList as $os => $match)
		{
			// Find a match
			if(preg_match('@'.$match.'@i', $userAgent))
			{
				// We found the correct match
				$this->_visitorOperatingSystem = $os;

				break;
			}
		}

		if(!$this->_visitorOperatingSystem) $this->_visitorOperatingSystem = '';

		return $this->_visitorOperatingSystem;
	}

	function setVisitorParam($name, $val)
	{
		$this->_visitorParams->$name = $val;
	}

	function getVisitorParam($name)
	{
		if(isset($this->_visitorParams->$name)) return $this->_visitorParams->$name;

		return false;
	}

	function locateIp($ip)
	{
		$ipDetail = array();

		$ipDetail['ip'] = $ip;
		$ipDetail['city'] = JText::_('UNKNOWN');
		$ipDetail['country'] = JText::_('UNKNOWN');
		$ipDetail['country_code'] = 'RD';
		$ipDetail['region_name'] = JText::_('UNKNOWN');
		$ipDetail['region_code'] = JText::_('UNKNOWN');
		$ipDetail['postal_code'] = JText::_('UNKNOWN');
		$ipDetail['latitude'] = JText::_('UNKNOWN');
		$ipDetail['longitude'] = JText::_('UNKNOWN');

		$url = 'http://api.cmsfruit.com/iplookup.php?do_not_log=true&k=UGbdnsdfkl64&ip_address='.$ipDetail['ip'];

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'misc.php';

		$misc = new JAdminModelMisc();

		$results = $misc->getURLContents($url);

		if(!empty($results))
		{
			// JSON Decode the results
			$results = json_decode($results);

			if(is_object($results))
			{
				if(!empty($results->city)) $ipDetail['city'] = $results->city;
				if(!empty($results->country_name)) $ipDetail['country'] = $results->country_name;
				if(!empty($results->country_code)) $ipDetail['country_code'] = $results->country_code;
				if(!empty($results->zipcode)) $ipDetail['postal_code'] = $results->zipcode;
				if(!empty($results->region_name)) $ipDetail['region_name'] = $results->region_name;
				if(!empty($results->region_code)) $ipDetail['region_code'] = $results->region_code;
				if(!empty($results->latitude)) $ipDetail['latitude'] = $results->latitude;
				if(!empty($results->longitude)) $ipDetail['longitude'] = $results->longitude;
			}
		}

		return $ipDetail;
	}

	function getActivity()
	{
		$mainframe = & JFactory::getApplication();

		$sql = "SELECT
		    v.visitor_id,
		    v.visitor_ip_address,
		    v.visitor_browser,
		    v.visitor_city,
		    v.visitor_country,
		    v.visitor_country_code,
		    v.visitor_referrer,
		    v.visitor_cdate,
		    v.visitor_mdate,
		    v.visitor_params,
		    v.user_id,
		    v.visitor_operating_system,
		    v.visitor_last_uri,
		    v.is_spider,
		    v.visitor_name,
		    v.visitor_username,
		    v.visitor_email 
		FROM #__jadm_visitor v;";
		$this->_db->setQuery($sql);

		return $this->_db->loadObjectList();
	}

	function getVisitor($visitorId)
	{
		$sql = "SELECT
		    v.visitor_id,
		    v.visitor_name,
		    v.visitor_username,
		    v.visitor_email,
		    v.visitor_ip_address,
		    v.visitor_browser,
		    v.visitor_city,
		    v.visitor_country,
		    v.visitor_country_code,
		    v.visitor_referrer,
		    v.visitor_cdate,
		    v.visitor_mdate,
		    v.visitor_params,
		    v.user_id,
		    v.visitor_operating_system,
		    v.visitor_last_uri,
		    v.is_spider 
	    FROM #__jadm_visitor v
	    WHERE v.visitor_id = ".$this->_db->Quote($visitorId)."
	    LIMIT 1;";
		$this->_db->setQuery($sql);

		$result = $this->_db->loadAssoc();

		if(isset($result['visitor_params']))
		{
			if(!empty($result['visitor_params']))
			{
				$result['visitor_params'] = json_decode($result['visitor_params']);
			}
		}

		return $result;
	}

	function getVisitorId()
	{
		return $this->_visitorId;
	}
}