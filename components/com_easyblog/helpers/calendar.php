<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

if(!function_exists('cal_days_in_month'))
{
    function cal_days_in_month($calendar, $month, $year)
    {
        return date('t', mktime(0, 0, 0, $month, 1, $year));
    }
}

class EasyBlogCalendarHelper
{
	public static function prepareData($date=array())
	{
		if(empty($date))
		{
			$date = EasyBlogCalendarHelper::processDate('');
		}

		$calendar = new stdClass();

		//Here we generate the first day of the month
		$calendar->first_day = mktime(0,0,0,$date['month'], 1, $date['year']) ;

		//This gets us the month name
		$calendar->title = date('F', $calendar->first_day) ;

		//Here we find out what day of the week the first day of the month falls on
		$calendar->day_of_week = date('D', $calendar->first_day) ;

		//previous month
		$calendar->previous	= strtotime('-1 month', $calendar->first_day);

		//next month
		$calendar->next		= strtotime('+1 month', $calendar->first_day);

		//Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero
		switch($calendar->day_of_week)
		{
			case "Sun":
				$calendar->blank = 0;
				break;
			case "Mon":
				$calendar->blank = 1;
				break;
			case "Tue":
				$calendar->blank = 2;
				break;
			case "Wed":
				$calendar->blank = 3;
				break;
			case "Thu":
				$calendar->blank = 4;
				break;
			case "Fri":
				$calendar->blank = 5;
				break;
			case "Sat":
				$calendar->blank = 6;
				break;
		}

		//We then determine how many days are in the current month
		$calendar->days_in_month = cal_days_in_month(0, $date['month'], $date['year']);

		return $calendar;
	}

	public static function processDate($timestamp='')
	{
		//This gets today's date
		if(empty($timestamp))
		{
			$jdate	= EasyBlogHelper::getDate();
			$timestamp	= $jdate->toUnix();
		}

		//This puts the day, month, and year in seperate variables
		$date['day']		= date('d', $timestamp);
		$date['month']		= date('m', $timestamp);
		$date['year']		= date('Y', $timestamp);
		$date['unix']		= $timestamp;

		return $date;
	}
}
