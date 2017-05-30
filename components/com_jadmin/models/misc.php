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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class JAdminModelMisc
{
	function __construct()
	{
		$this->JAdminModelMisc();
	}

	function JAdminModelMisc()
	{
		jimport('joomla.application.component.model');
	}

	function getURLContents($url, $useragent = 'cURL', $headers = false, $follow_redirects = false, $debug = false)
	{
		if(function_exists('curl_init'))
		{
			# initialise the CURL library
			$ch = curl_init();

			# specify the URL to be retrieved
			curl_setopt($ch, CURLOPT_URL, $url);

			# we want to get the contents of the URL and store it in a variable
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			# specify the useragent: this is a required courtesy to site owners
			curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

			# ignore SSL errors
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

			# return headers as requested
			if($headers) curl_setopt($ch, CURLOPT_HEADER, 1);

			# only return headers
			if($headers == 'headers only') curl_setopt($ch, CURLOPT_NOBODY, 1);

			# follow redirects - note this is disabled by default in most PHP installs from 4.4.4 up
			if($follow_redirects) curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

			$settings = & CMSModel::getInstance('Setting', 'JAdminModel');

			if($settings->getSetting('use_proxy') == 1)
			{
				// Use proxy server
				curl_setopt($ch, CURLOPT_PROXY, $settings->getSetting('proxy_uri'));

				$proxyAuth = $settings->getSetting('proxy_auth');

				if($settings->getSetting('proxy_port') > 0) curl_setopt($ch, CURLOPT_PROXYPORT, $settings->getSetting('proxy_port'));
				if(!empty($proxyAuth)) curl_setopt($ch, CURLOPT_PROXYUSERPWD, $settings->getSetting('proxy_auth'));
				if($settings->getSetting('use_socks') > 0) curl_setopt($ch, CURLOPT_PROXYTYPE, 5);
			}

			# if debugging, return an array with CURL's debug info and the URL contents
			if($debug)
			{
				$result['contents'] = curl_exec($ch);
				$result['info'] = curl_getinfo($ch);
			}
			else
			{
				# otherwise just return the contents as a variable
				$result = curl_exec($ch);
			}

			# free resources
			curl_close($ch);
		}
		elseif(ini_get('allow_url_fopen') == 1 || ini_get('allow_url_fopen') == 'On')
		{
			$result = file_get_contents($url);
		}
		else
		{
			$result = false;
		}

		# send back the data
		return $result;
	}
}