<?php
/**
 * @version		$Id: ug_CN.localise.php 00199 2013-01-21 06:56:00 Akber Tursun $
 * @package    Joomla.Language
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
 
defined('_JEXEC') or die;

/**
 * ug-CN localise class
 *
 * @package  Joomla.Language
 * @since	 1.6
 */
abstract class Ug_CNLocalise 
{
 	/**
 	 * Returns the potential suffixes for a specific number of items
 	 *
	 * @param   int $count  The number of items.
	 * @return  array  An array of potential suffixes.
	 * @since   1.6
 	 */
	public static function getPluralSuffixes($count)
	{
		if ($count == 0) 
		{
			$return = array('0');
		}
		elseif($count == 1)
		{
			$return = array('1');
		}
		else
		{
			$return = array('MORE');
		}
		return $return;
	}
 	/**
 	 * Returns the ignored search words
 	 *
	 * @return  array  An array of ignored search words.
	 * @since   1.6
 	 */
	public static function getIgnoredSearchWords()
	{
		$search_ignore = array();
		$search_ignore[] = "ۋە";
		$search_ignore[] = "بىلەن";
		$search_ignore[] = "بىللە";
		$search_ignore[] = "ئىچىدە";
		return $search_ignore;
	}
 	/**
 	 * Returns the lower length limit of search words
 	 *
	 * @return  integer  The lower length limit of search words.
	 * @since   1.6
 	 */
	public static function getLowerLimitSearchWord()
	{
		return 3;
	}
 	/**
 	 * Returns the upper length limit of search words
 	 *
	 * @return  integer  The upper length limit of search words.
	 * @since   1.6
 	 */
	public static function getUpperLimitSearchWord()
	{
		return 20;
	}
 	/**
 	 * Returns the number of chars to display when searching
 	 *
	 * @return  integer  The number of chars to display when searching.
	 * @since   1.6
 	 */
	public static function getSearchDisplayedCharactersNumber() 
	{
		return 200;
	}
}
