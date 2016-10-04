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

class JAdminModelSettingAdmin extends CMSModel
{
    var $_appId = null;
    var $_appName = 'JAdmin!';
    var $_appVersion = '1.5.4.3';
    
    var $_settings = null;

    function __construct()
    {
	$this->JAdminModelSettingAdmin();
    }

    function JAdminModelSettingAdmin()
    {
	parent::__construct();

	require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jadmdateadmin.php';

	$this->_loadSettings();
    }

    function refreshSettings()
    {
	$this->_loadSettings();
    }

    function _loadSettings()
    {
	$sql = "SELECT
		    app_id,
		    app_data
		FROM #__cms_app
		WHERE app_name = ".$this->_db->Quote($this->_appName)."
		LIMIT 1;";
	$this->_db->setQuery($sql);

	$result = $this->_db->loadAssoc();

	if(isset($result['app_id']))
	{
	    $this->_appId = $result['app_id'];

	    if(isset($result['app_data']))
	    {
		$this->_settings = json_decode($result['app_data']);
	    }

	    if(!is_object($this->_settings))
	    {
		// Settings should be an object
		$this->_settings = new stdClass();
	    }
	}
	else
	{
	    // App record doesn't exist yet, create it
	    if(!is_object($this->_settings))
	    {
		// Settings should be an object
		$this->_settings = new stdClass();
	    }

	    $date = new JAdminModelJADMDateAdmin();

	    $nowUnixTime = $date->toUnix();

	    $data = new stdClass();

	    $data->app_name =  $this->_appName;
	    $data->app_data =  json_encode($this->_settings);
	    $data->app_cdate =  $nowUnixTime;
	    $data->app_mdate =  $nowUnixTime;

	    $this->_db->insertObject('#__cms_app', $data, 'app_id');

	    $this->_appId = $this->_db->insertid();

	    return $this->_loadSettings();
	}

	return true;
    }

    function getAppName()
    {
	return $this->_appName;
    }

    function getAppId()
    {
	return $this->_appId;
    }
    
    function getAppVersion()
    {
	return $this->_appVersion;
    }

    function getPluginXMLObject()
    {
	$manifestFilePath = JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'jadmin'.DIRECTORY_SEPARATOR.'jadmin.xml';

	if(file_exists($manifestFilePath))
	{
	    // Module is installed
	    return simplexml_load_file($manifestFilePath);
	}
	else
	{
	    $data = new stdClass();

	    $data->version = null;

	    return $data;
	}
    }

    function getPluginVersion()
    {
	$obj = $this->getPluginXMLObject();

	return $obj->version;
    }

    function getSetting($name)
    {
	if(isset($this->_settings->$name)) return $this->_settings->$name;

	// Default Values
	if($name == 'activity_monitor') return 1;
	if($name == 'activity_monitor_expiration') return 180;
	if($name == 'use_proxy') return 0;
	if($name == 'use_socks') return 0;
	if($name == 'use_gzip') return 1;
	
	return false;
    }

    function getSiteName()
    {
	$siteName = $this->getSetting('site_name');

	if(!$siteName)
	{
	    $uri =& JFactory::getURI();

	    $siteName = str_replace('www.', '', $uri->toString(array('host')));
	}

	return $siteName;
    }

    function setSetting($name, $value)
    {
	$this->_settings->$name = $value;
    }

    function deleteSetting($name)
    {
	if(isset($this->_settings->$name))
	{
	    unset($this->_settings->$name);
	}
    }

    function saveSettings()
    {
	$date = new JAdminModelJADMDateAdmin();
	
	$data = new stdClass();

	$data->app_id = $this->_appId;
	$data->app_data = json_encode($this->_settings);
	$data->app_mdate = $date->toUnix();

	$this->_db->updateObject('#__cms_app', $data, 'app_id');
		
	return true;
    }

    function doPOST($url, $postData, $useragent='cURL', $headers=false,  $follow_redirects=false, $debug=false)
    {
	$fields_string = '';

	foreach($postData as $key => $value)
	{
	    $fields_string .= $key.'='.urlencode($value).'&';
	}

	rtrim($fields_string,'&');
	
	# initialise the CURL library
	$ch = curl_init();

	# specify the URL to be retrieved
	curl_setopt($ch, CURLOPT_URL, $url);

	curl_setopt($ch, CURLOPT_POST, count($postData));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

	# we want to get the contents of the URL and store it in a variable
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

	# specify the useragent: this is a required courtesy to site owners
	curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

	# ignore SSL errors
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

	# return headers as requested
	if($headers==true)
	{
	    curl_setopt($ch, CURLOPT_HEADER,1);
	}

	# only return headers
	if($headers=='headers only')
	{
	    curl_setopt($ch, CURLOPT_NOBODY ,1);
	}

	# follow redirects - note this is disabled by default in most PHP installs from 4.4.4 up
	if($follow_redirects==true)
	{
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	}

	if($this->getSetting('use_proxy') == 1)
	{
	    // Use proxy server
	    curl_setopt($ch, CURLOPT_PROXY, $this->getSetting('proxy_uri'));

	    if($this->getSetting('proxy_port') > 0) curl_setopt($ch, CURLOPT_PROXYPORT, $this->getSetting('proxy_port'));
	    if(strlen($this->getSetting('proxy_auth')) > 0) curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->getSetting('proxy_auth'));
	    if($this->getSetting('use_socks') > 0) curl_setopt($ch, CURLOPT_PROXYTYPE, 5);
	}

	# if debugging, return an array with CURL's debug info and the URL contents
	if($debug)
	{
	    $result['contents']=curl_exec($ch);
	    $result['info']=curl_getinfo($ch);
	}
	else
	{
	    # otherwise just return the contents as a variable
	    $result=curl_exec($ch);
	}

	# free resources
	curl_close($ch);

	# send back the data
	return $result;
    }

    function getRemoteURL($uri)
    {
	if(function_exists('curl_init'))
	{
	    $content = $this->url_get_contents($uri, 'JLive! Chat Component');
	}
	elseif(ini_get('allow_url_fopen') == 1 || ini_get('allow_url_fopen') == 'On')
	{
	    $content = file_get_contents($uri);
	}
	else
	{
	    $content = false;
	}

	return $content;
    }

    function url_get_contents($url, $useragent='cURL', $headers=false,  $follow_redirects=false, $debug=false)
    {
	# initialise the CURL library
	$ch = curl_init();

	# specify the URL to be retrieved
	curl_setopt($ch, CURLOPT_URL,$url);

	# we want to get the contents of the URL and store it in a variable
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

	# specify the useragent: this is a required courtesy to site owners
	curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

	# ignore SSL errors
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

	# return headers as requested
	if ($headers==true)
	{
	    curl_setopt($ch, CURLOPT_HEADER,1);
	}

	# only return headers
	if ($headers=='headers only')
	{
	    curl_setopt($ch, CURLOPT_NOBODY ,1);
	}

	# follow redirects - note this is disabled by default in most PHP installs from 4.4.4 up
	if ($follow_redirects==true)
	{
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	}

	if($this->getSetting('use_proxy') == 1)
	{
	    // Use proxy server
	    curl_setopt($ch, CURLOPT_PROXY, $this->getSetting('proxy_uri'));
	    
	    if($this->getSetting('proxy_port') > 0) curl_setopt($ch, CURLOPT_PROXYPORT, $this->getSetting('proxy_port'));
	    if(strlen($this->getSetting('proxy_auth')) > 0) curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->getSetting('proxy_auth'));
	    if($this->getSetting('use_socks') > 0) curl_setopt($ch, CURLOPT_PROXYTYPE, 5);
	}
	
	# if debugging, return an array with CURL's debug info and the URL contents
	if ($debug==true)
	{
	    $result['contents']=curl_exec($ch);
	    $result['info']=curl_getinfo($ch);
	}

	# otherwise just return the contents as a variable
	else $result=curl_exec($ch);

	# free resources
	curl_close($ch);

	# send back the data
	return $result;
    }

    function getSettingsChecksum()
    {
	$sql = "SELECT SUM(app_mdate) FROM #__cms_app
		WHERE app_id = ".(int)$this->_appId.";";
	$this->_db->setQuery($sql);

	return $this->_db->loadResult();
    }

    function touchSettings()
    {
	$sql = "UPDATE #__cms_app SET 
		    app_mdate = app_mdate+1 
		WHERE app_id = ".(int)$this->_appId.";";
	$this->_db->setQuery($sql);

	return $this->_db->query();
    }
}
