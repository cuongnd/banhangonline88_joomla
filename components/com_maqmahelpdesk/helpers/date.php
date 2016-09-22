<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class HelpdeskDate
{
	public static function DateOffset($format = "%Y-%m-%d %H:%M:%S", $timestamp = '')
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		!$timestamp ? $timestamp = time() : '';
		return strftime($format, ($timestamp + ($supportConfig->offset * 3600)));
	}

	public static function TimestampOffset($timestamp = '')
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		if (!$timestamp) {
			$timestamp = time() + ($supportConfig->offset * 3600);
		} else {
			$timestamp = $timestamp + ($supportConfig->offset * 3600);
		}
		return $timestamp;
	}

	// DUE DATE	- year, month, day, hour, minute
	// TICKET DATE - year, month, day, hour, minute
	public static function ElapsedTime($year, $month, $day, $hour, $minute, $year2, $month2, $day2, $hour2, $minute2)
	{
		$dif_month = 0;
		$dif_days = 0;
		$dif_hours = 0;
		$dif_mins = 0;

		$now = mktime($hour2, $minute2, 0, $month2, $day2, $year2); // 2005-03-06 19:35
		$due = mktime($hour, $minute, 0, $month, $day, $year); // 2005-03-30 15:12

		$dif = $due - $now;

		if ($dif < 0) {
			$dif = $now - $due;
			$prefix = '';
		}

		$dif_month = date("m", $dif) - 1;
		$dif_month = ($dif_month > 0 ? "$dif_month<small>" . JText::_('times_months') . "</small>" : '');

		$dif_days = date("d", $dif) - 1;
		$dif_days = ($dif_days > 0 ? "$dif_days<small>" . JText::_('times_days') . "</small>" : '');

		$dif_hours = date("H", $dif);
		$dif_hours = ($dif_hours > 0 ? "$dif_hours<small>" . JText::_('times_hours') . "</small>" : '');

		$dif_mins = date("i", $dif);
		$dif_mins = ($dif_mins > 0 ? "$dif_mins<small>" . JText::_('times_mins') . "</small>" : '');

		return $dif_month . ($dif_days > 0 ? ($dif_month > 0 ? ' ' : '') . $dif_days : '') . ($dif_hours > 0 ? ($dif_days > 0 ? ' ' : '') . $dif_hours : '') . ($dif_mins != 0 ? ($dif_hours > 0 ? ' ' : '') . $dif_mins : '');
	}

	public static function GetMonthDays($year, $month)
	{
		switch ($month) {
			case "01":
			case 1:
				return 31;
				break;

			case "02":
			case 2:
				return HelpdeskDate::DaysFebruary($year);
				break;

			case "03":
			case 3:
				return 31;
				break;

			case "04":
			case 4:
				return 30;
				break;

			case "05":
			case 5:
				return 31;
				break;

			case "06":
			case 6:
				return 30;
				break;

			case "07":
			case 7:
				return 31;
				break;

			case "08":
			case 8:
				return 31;
				break;

			case "09":
			case 9:
				return 30;
				break;

			case "10":
				return 31;
				break;

			case "11":
				return 30;
				break;

			case "12":
				return 31;
				break;
		}
	}

	public static function DaysFebruary($year)
	{
		if ($year < 0) $year++;
		$year += 4800;
		if (($year % 4) == 0) {
			if (($year % 100) == 0) {
				if (($year % 400) == 0) {
					return (29);
				} else {
					return (28);
				}
			} else {
				return (29);
			}
		} else {
			return (28);
		}
	}

	public static function GetDateShortFormat()
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		if ($supportConfig->date_short) {
			$format = $supportConfig->date_short;
		} else {
			$format = '%d/%m/%Y %H:%M';
		}
		return $format;
	}

	public static function GetDateLongFormat()
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		if ($supportConfig->date_long) {
			$format = $supportConfig->date_long;
		} else {
			$format = '%e %B %Y, %H:%M';
		}
		return $format;
	}

	public static function FormatDuration($time_in_sec)
	{
		if ($time_in_sec < 60 * 60) {
			$time_min = round(($time_in_sec / 60), 0);
			$formated_time = intval($time_min) . " " . JText::_('times_mins');
		} elseif ($time_in_sec < 60 * 60 * 24) {
			$time_hour = floor($time_in_sec / (60 * 60));
			$time_min = fmod($time_in_sec, 60 * 60) / 60;
			$formated_time = intval($time_hour) . " " . JText::_('times_hours') . " " . intval($time_min) . " " . JText::_('times_mins');
		} else {
			$time_day = floor($time_in_sec / (60 * 60 * 24));
			$time_hour = floor(($time_in_sec - $time_day * 60 * 60 * 24) / (60 * 60));
			$time_min = round(($time_in_sec - ($time_hour * 60 * 60) - ($time_day * 60 * 60 * 24)) / 60, 0);
			$formated_time = intval($time_day) . " " . JText::_('times_days') . " " . intval($time_hour) . " " . JText::_('times_hours') . " " . intval($time_min) . " " . JText::_('times_mins');
		}
		return $formated_time;
	}

	public static function GetOrdinalNumber($i)
	{
		switch (floor($i / 10) % 10) {
			default:
				switch ($i % 10) {
					case 1:
						return 'st';
					case 2:
						return 'nd';
					case 3:
						return 'rd';
				}
			case 1:
		}
		return 'th';
	}

	public static function SecondsToHours($sec, $padHours = false, $days = false, $secs = true)
	{
		$hms = "";
		$hours = intval(intval($sec) / 3600);
		$hms .= ($padHours)
			? str_pad($hours, 2, "0", STR_PAD_LEFT) . ':'
			: $hours . ':';
		$minutes = intval(($sec / 60) % 60);
		$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT);
		if ($secs) {
			$seconds = intval($sec % 60);
			$hms .= ':' . str_pad($seconds, 2, "0", STR_PAD_LEFT);
		}
		if ($days) {
			if ($hours > 24) {
				$days = intval($hours / 24);
				$hours = $hours - ($days * 24);
				$hms = "";
				$hms .= $days . ' ' . JText::_('times_days') . ' ';
				$hms .= ($padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ' ' . JText::_('times_hours') . ' ' : $hours . ':';
				$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT) . ' ' . JText::_('times_mins') . ' ';
				$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT) . ' ' . JText::_('seconds') . '';
			} else {
				$hms = "";
				$hms .= ($padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ' ' . JText::_('times_hours') . ' ' : $hours . ':';
				$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT) . ' ' . JText::_('times_mins') . ' ';
				$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT) . ' ' . JText::_('seconds') . '';
			}
		}
		return $hms;
	}

	public static function AddHoursMinutes($time1, $time2, $format)
	{
		$time1 = str_replace('.', ':', $time1);
		$time2 = str_replace('.', ':', $time2);

		$totaltime = HelpdeskDate::ConvertHoursMinutesToDecimal($time1) + HelpdeskDate::ConvertHoursMinutesToDecimal($time2);
		$totaltime_hhmm = HelpdeskDate::ConvertDecimalsToHoursMinutes($totaltime);

		switch ($format) {
			case 'hhmm' :
				return $totaltime_hhmm;
				break;
			case 'decim' :
				return $totaltime;
				break;
		}
	}

	public static function ConvertDecimalsToHoursMinutes($dectime, $separator = ':')
	{
		if ($dectime > 0) {
			$hours = floor($dectime);
			$mins = $dectime - $hours;
		} elseif ($dectime == 0) {
			$hours = '0';
			$mins = '00';
		} else {
			$hours = ceil($dectime);
			$hours == '0' ? $hours = '-' . $hours : '';
			$mins = $dectime * (-1) - $hours * (-1);
		}

		$mins = round($mins * 60);
		$mins < 10 ? $mins = '0' . $mins : '';

		return $hours . $separator . $mins;
	}

	public static function ConvertHoursMinutesToDecimal($hhmm)
	{
		$delimiter_pos = strpos($hhmm, ':');
		if ($delimiter_pos === false) {
			$delimeter_pos = str_replace('.', ':', $hhmm);
			$delimiter_pos = strpos($hhmm, '.');
		}
		$hours = substr($hhmm, 0, $delimiter_pos);
		$mins = substr($hhmm, $delimiter_pos + 1, strlen($hhmm));
		$mins = round(($mins / 60), 2) * 100;
		$mins < 10 ? $mins = '0' . $mins : '';

		return $hours . '.' . $mins;
	}

	public static function ParseDate($date, $format = "%Y-%m-%d %H:%M:%S")
	{
		// Builds up date pattern from the given $format, keeping delimiters in place.
		$datePattern = '';
		if (!preg_match_all("/%([YmdHMSp])([^%])*/", $format, $formatTokens, PREG_SET_ORDER)) {
			return false;
		}
		foreach ($formatTokens as $formatToken) {
			$delimiter = '';
			isset ($formatToken[2]) ? $delimiter = preg_quote($formatToken[2], "/") : '';
			$datePattern .= "(.*)" . $delimiter;
		}
		// Splits up the given $date
		if (!preg_match("/" . $datePattern . "/", $date, $dateTokens)) {
			return false;
		}

		$dateSegments = array();
		for ($i = 0; $i < count($formatTokens); $i++) {
			$dateSegments[$formatTokens[$i][1]] = $dateTokens[$i + 1];
		}

		// Reformats the given $date into US English date format, suitable for strtotime()
		if (isset($dateSegments["Y"]) && isset($dateSegments["m"]) && isset($dateSegments["d"])) {
			$dateReformated = $dateSegments["Y"] . "-" . $dateSegments["m"] . "-" . $dateSegments["d"];
		}
		else {
			return false;
		}
		if (isset($dateSegments["H"]) && isset($dateSegments["M"])) {
			$dateReformated .= " " . $dateSegments["H"] . ":" . $dateSegments["M"] . ":" . $dateSegments["S"];
		}

		return strtotime($dateReformated);
	}

	public static function GetMonthName($selmonth)
	{
		return JText::_("MONTH" . $selmonth);
	}

	public static function ShortDate($date)
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		$date = strftime($supportConfig->date_short, strtotime($date));
		return $date;
	}

	public static function LongDate($date)
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		$date = strftime($supportConfig->date_long, strtotime($date));
		return $date;
	}

	public static function DaysDifference($endDate, $beginDate)
	{
		//explode the date by "-" and storing to array
		$date_parts1 = explode("-", $beginDate);
		$date_parts2 = explode("-", $endDate);
		//gregoriantojd() Converts a Gregorian date to Julian Day Count
		$start_date = gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
		$end_date = gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
		return $end_date - $start_date;
	}

	/**
	 * Get a string.
	 *
	 * Returns the passed time from a given date from the current moment in text.
	 *
	 * @param   datetime  $date  The date to compare with current moment.
	 *
	 * @return string
	 */
	public static function TimeAgo($date)
	{
		$periods = array(JText::_('seconds'), JText::_('minutes'), JText::_('hours'), JText::_('days'),
			JText::_('weeks'), JText::_('months'), JText::_('years'), "decade");
		$periods_single = array(JText::_('TIME_SINGLE_SECOND'), JText::_('TIME_SINGLE_MINUTE'),
			JText::_('TIME_SINGLE_HOUR'), JText::_('TIME_SINGLE_DAY'), JText::_('TIME_SINGLE_WEEK'),
			JText::_('TIME_SINGLE_MONTH'), JText::_('TIME_SINGLE_YEAR'), "decade");
		$lengths = array("60","60","24","7","4.35","12","10");

		$now = self::TimestampOffset();
		$unix_date = strtotime($date);

		// Is it future date or past date
		if ($now > $unix_date)
		{
			$difference = $now - $unix_date;
			$tense = JText::_("ago");
		}
		else
		{
			$difference = $unix_date - $now;
			$tense = JText::_("from_now");
		}

		for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++)
		{
			$difference /= $lengths[$j];
		}

		$difference = round($difference);

		if (function_exists("mb_convert_case"))
		{
			return $difference . '&nbsp;<small>' . ($difference == 1 ?
				mb_convert_case($periods_single[$j], MB_CASE_LOWER, "UTF-8") :
				mb_convert_case($periods[$j], MB_CASE_LOWER, "UTF-8")) . " {$tense}</small>";
		}
		else
		{
			return $difference . '&nbsp;<small>' . ($difference == 1 ?
				JString::strtolower($periods_single[$j]) :
				JString::strtolower($periods[$j])) . " {$tense}</small>";
		}
	}

	/**
	 * Get a number.
	 *
	 * Returns the passed hours converted to seconds.
	 *
	 * @param   string  $hours  The hours to convert in the format HH:MM
	 *
	 * @return string
	 */
	public static function HoursToSeconds($hours)
	{
		$seconds = explode(':', $hours);
		$seconds = ( $seconds[0] * 3600 ) + ( $seconds[1] * 60 );
		return $seconds;
	}

	public static function DueDateOffset($format, $timestamp=0)
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		$offset = $supportConfig->offset;
		$offset = (($offset) * 60 * 60);
		$timestamp = (!$timestamp ? mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")) : $timestamp);
		return strftime($format, $timestamp+($offset));
	}

	public static function CheckIfHoliday($date)
	{
		$database = JFactory::getDBO();

		$sql = "SELECT COUNT(*)
				FROM `#__support_holidays`
				WHERE `holiday_date`='" . $date . "' OR `holiday_date`='0000-" . substr($date,5) . "'";
		$database->setQuery($sql);
		return (int) $database->loadResult();
	}

	public static function CheckIfVacance($date)
	{

	}

	public static function MinYear()
	{
		$database = JFactory::getDBO();

		$sql = "SELECT MIN(YEAR(`date`))
				FROM `#__support_ticket`";
		$database->setQuery($sql);
		return (int) $database->loadResult();
	}
}
