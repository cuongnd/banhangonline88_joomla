<?php

/**
 * @package JLive! Chat
 * @version 4.3.3
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

class JLiveChatModelJLCDate
{
	var $_gmtUnixTime = null;

	function __construct($unixTime = null)
	{
		$this->JLiveChatModelJLCDate($unixTime);
	}

	function JLiveChatModelJLCDate($unixTime = null)
	{
		if(!empty($unixTime))
		{
			// A unix timestamp was supplied
			$this->_gmtUnixTime = $unixTime;
		}
		else
		{
			$this->_gmtUnixTime = $this->currentUTCUnixTimestamp();
		}
	}

	function getTimezoneOffset()
	{
		$settings = & CMSModel::getInstance('Setting', 'JLiveChatModel');

		if($settings->getSetting('timezone_offset') == 'sys')
		{
			return date('Z');
		}
		else
		{
			return $settings->getSetting('timezone_offset') * 3600;
		}
	}

	function currentUTCUnixTimestamp()
	{
		$db = & JFactory::getDBO();

		$sql = "SELECT UNIX_TIMESTAMP(UTC_TIMESTAMP()) AS 'current_utc_timestamp'";
		$db->setQuery($sql);

		return $db->loadResult();
	}

	function convertUTCToLocaleTimestamp($utcTimestamp)
	{
		return $utcTimestamp + $this->getTimezoneOffset();
	}

	function toUnix($local = false)
	{
		if($local)
		{
			// Apply offset
			return $this->convertUTCToLocaleTimestamp($this->_gmtUnixTime);
		}
		else
		{
			// Get GMT Time
			return $this->_gmtUnixTime;
		}
	}

	function toFormat($format = '%c')
	{
		return strftime($format, $this->toUnix(true));
	}
}