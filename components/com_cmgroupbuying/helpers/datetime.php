<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelperDateTime
{
	public static function getCurrentDateTime()
	{
		$tz = new DateTimeZone(JFactory::getApplication()->getCfg('offset'));
		$now = new JDate('now', $tz);
		return $now->__toString();
	}

	public static function changeDateTimeFormat($dateTime, $format)
	{
		$newDateTime = new JDate($dateTime);
		return $newDateTime->format(JText::_($format), false);
	}
}