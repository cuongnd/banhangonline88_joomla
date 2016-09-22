<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/**
 * Output formatting functions
 **/
if (!class_exists("FSJ_Format"))
{
	class FSJ_Format {
		static function Size($size)
		{
			if ($size < 0) $size = 0;
			$sizes = array('Bytes', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb');
			if ($size == 0) 
			return('n/a');
			return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $sizes[$i]);
		}			
		
		static function SizeK($size)
		{
			$sizes = array('Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb');
			if ($size == 0) return('n/a');
			return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $sizes[$i]);
		}	
		
		/*
		static function Date($date,$format = FSS_DATE_LONG, $format_custom = null)
		{
			//echo "In : $date<br>";
			//echo "Offset : " . FSS_Settings::Get('timezone_offset') . "<br>";
			
			if ((int)$date > 10000)
				$date = date("Y-m-d H:i:s", $date);

			if ((int)FSS_Settings::Get('timezone_offset') != 0)
			{
				$time = strtotime($date);
				$time += 3600 * (int)FSS_Settings::Get('timezone_offset');
				$date = date("Y-m-d H:i:s", $time);
			}
			
			switch($format)
			{
				case FSS_DATE_SHORT:	
					$ft = JText::_('DATE_FORMAT_LC4');
					break;
				case FSS_DATE_MID:	
					$ft = JText::_('DATE_FORMAT_LC3');
					break;
				case FSS_TIME_SHORT:	
					$ft = 'H:i';
					break;
				case FSS_TIME_LONG:	
					$ft = 'H:i:s';
					break;
				case FSS_DATETIME_SHORT:	
					$ft = JText::_('DATE_FORMAT_LC4') . ', H:i';
					break;
				case FSS_DATETIME_MID:	
					$ft = JText::_('DATE_FORMAT_LC3') . ', H:i';
					break;
				case FSS_DATETIME_MYSQL:	
					$ft = 'Y-m-d H:i:s';
					break;
				case FSS_DATE_CUSTOM:
					$ft = $format_custom;
					break;
				default:
					$ft = JText::_('DATE_FORMAT_LC');
			}
			
			if ($format == FSS_DATETIME_SHORT && FSS_Settings::Get('date_dt_short') != "")
				$ft = FSS_Settings::Get('date_dt_short');
			
			if ($format == FSS_DATETIME_MID && FSS_Settings::Get('date_dt_long') != "")
				$ft = FSS_Settings::Get('date_dt_long');
			
			if ($format == FSS_DATE_SHORT && FSS_Settings::Get('date_d_short') != "")
				$ft = FSS_Settings::Get('date_d_short');
			
			if ($format == FSS_DATE_MID && FSS_Settings::Get('date_d_long') != "")
				$ft = FSS_Settings::Get('date_d_long');
			
			$date = new JDate($date, new DateTimeZone("UTC"));
			$date->setTimezone(FSS_Helper::getTimezone());
			
			//echo "Out : " . $date->format($ft, true) . "<br>";
			return $date->format($ft, true);
			
		}
		*/
		
		static function Date($date, $format, $showtime = false)
		{
			// This needs the relevant settings adding to the globals page for setting up date formats and timezone offsets
			// Use the code above!
			$format = JText::_($format);
			if ($showtime)
				$format .= ', H:i';
			
			$date = new JDate($date, new DateTimeZone("UTC"));
			$date->setTimezone(self::getTimezone());
			return $date->format($format, true);
		}
		
		static function getTimeZone() {
			$userTz = JFactory::getUser()->getParam('timezone');
			
			if (FSJ_Helper::IsJ3())
			{
				$timeZone = JFactory::getConfig()->get('offset');
			} else {
				$timeZone = JFactory::getConfig()->getValue('offset');
			}
			
			if($userTz) {
				$timeZone = $userTz;
			}
			
			if ((string)$timeZone == "" || (string)$timeZone == "0") 
				$timeZone = "UTC";
			
			return new DateTimeZone($timeZone);
		}
		
		
		static function RelativeTime($timestamp){
			//echo "Start : $timestamp<br>";
			
			$difference = strtotime(date("Y-m-d H:i:s",time())) - $timestamp;
			//echo "Diff : $difference<br>";
			//exit;
			$periods = array("sec","min", "hour", "day", "week",
				"month", "years", "decade");
			$lengths = array("60","60","24","7","4.35","12","10");

			//$difference /= 60;
			//$difference /= 60;
			//$difference /= 24;

			if ($difference > 0) { // this was in the past
				$ending = "ago";
			} else { // this was in the future
				$difference = -$difference;
				$ending = "to go";
			}
			
			for($j = 0; $j < count($lengths) && $difference >= $lengths[$j] ; $j++)
			{
				//echo "J: $j, Diff : $difference, ";
				$difference /= $lengths[$j];
				//echo "New Diff : $difference<br>";
			}
			$difference = round($difference);
			if($difference != 1) $periods[$j].= "s";
			$text = "$difference $periods[$j] $ending";
			
			if ($j == 0)
			{
				if ($difference == 0)
					$text = "Today";
				else if ($difference == 1)
					$text = "Yesterday";
				else {
					$dow = date("l", $timestamp);
					$text .= " - " . $dow;	
				}
			}
			
			//echo $text . "<br><br>";
			
			//exit;
			return $text;
		}

		static function perms_format($perms)
		{
			if (($perms & 0xC000) == 0xC000) {
				// Socket
				$info = 's';
			} elseif (($perms & 0xA000) == 0xA000) {
				// Symbolic Link
				$info = 'l';
			} elseif (($perms & 0x8000) == 0x8000) {
				// Regular
				$info = '-';
			} elseif (($perms & 0x6000) == 0x6000) {
				// Block special
				$info = 'b';
			} elseif (($perms & 0x4000) == 0x4000) {
				// Directory
				$info = 'd';
			} elseif (($perms & 0x2000) == 0x2000) {
				// Character special
				$info = 'c';
			} elseif (($perms & 0x1000) == 0x1000) {
				// FIFO pipe
				$info = 'p';
			} else {
				// Unknown
				$info = 'u';
			}

			// Owner
			$info .= (($perms & 0x0100) ? 'r' : '-');
			$info .= (($perms & 0x0080) ? 'w' : '-');
			$info .= (($perms & 0x0040) ?
						(($perms & 0x0800) ? 's' : 'x' ) :
						(($perms & 0x0800) ? 'S' : '-'));

			// Group
			$info .= (($perms & 0x0020) ? 'r' : '-');
			$info .= (($perms & 0x0010) ? 'w' : '-');
			$info .= (($perms & 0x0008) ?
						(($perms & 0x0400) ? 's' : 'x' ) :
						(($perms & 0x0400) ? 'S' : '-'));

			// World
			$info .= (($perms & 0x0004) ? 'r' : '-');
			$info .= (($perms & 0x0002) ? 'w' : '-');
			$info .= (($perms & 0x0001) ?
						(($perms & 0x0200) ? 't' : 'x' ) :
						(($perms & 0x0200) ? 'T' : '-'));

			return $info;

		}

		static function Number($number, $decimals = 0)
		{
			$dec_sep = JText::_('DECIMALS_SEPARATOR');
			$tho_sep = JText::_('THOUSANDS_SEPARATOR');

			return number_format($number, $decimals, $dec_sep, $tho_sep);
		}
	}
}