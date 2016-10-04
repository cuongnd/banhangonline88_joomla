<?php
/**
 * @package    Joomla.Language
 *
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * mk-MK localise class
 *
 * @package  Joomla.Language
 * @since    1.6
 */
abstract class Mk_MKLocalise
{
	/**
	 * Returns the potential suffixes for a specific number of items
	 *
	 * @param	integer $count  The number of items.
	 * @return	array  An array of potential suffixes.
	 * @since	1.6
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
	 * @return	array  An array of ignored search words.
	 * @since	1.6
	 */
	public static function getIgnoredSearchWords()
	{
		return array('и', 'во', 'на');
	}
	/**
	 * Returns the lower length limit of search words
	 *
	 * @return	integer  The lower length limit of search words.
	 * @since	1.6
	 */
	public static function getLowerLimitSearchWord()
	{
		return 3;
	}
	/**
	 * Returns the upper length limit of search words
	 *
	 * @return	integer  The upper length limit of search words.
	 * @since	1.6
	 */
	public static function getUpperLimitSearchWord()
	{
		return 20;
	}
	/**
	 * Returns the number of chars to display when searching
	 *
	 * @return	integer  The number of chars to display when searching.
	 * @since	1.6
	 */
	public static function getSearchDisplayedCharactersNumber()
	{
		return 200;
	}
/**
         * This method processes a string and replaces all accented UTF-8 characters by unaccented
         * ASCII-7 "equivalents"
         *
         * @param       string  $string The string to transliterate
         * @return      string  The transliteration of the string
         * @since       1.6
         */
        public static function transliterate($string) {
                $str = JString::strtolower($string);
 
                //Specific language transliteration.
                //This one is for latin 1, latin supplement , extended A, Cyrillic, Greek
 
                $glyph_array = array(
                'a'            =>   'а',
                'b'            =>   'б',
                'v'            =>   'в',
                'g'            =>   'г',
                'd'            =>   'д',
                'gj'           =>   'ѓ',
                'e'            =>   'è,е',
				'zh'           =>   'ж',
                'z'            =>   'з',
                'dz'           =>   'ѕ',
                'i'            =>   'и,ѝ',
                'j'            =>   'ј',
                'k'            =>   'к',
                'l'            =>   'л',
                'lj'           =>   'љ',
                'm'            =>   'м',
                'n'            =>   'н',
                'nj'           =>   'њ',
                'o'            =>   'о',
                'p'            =>   'п',
                'r'            =>   'р',
                's'            =>   'с',
                't'            =>   'т',
                'kj'           =>   'ќ',
                'u'            =>   'у',
                'f'            =>   'ф',
                'h'            =>   'х',
                'c'            =>   'ц',
                'ch'           =>   'ч',
                'dj'           =>   'џ',
                'sh'           =>   'ш',
                );
 
                foreach( $glyph_array as $letter => $glyphs ) {
                        $glyphs = explode( ',', $glyphs );
                        $str = str_replace( $glyphs, $letter, $str );
                }
 
                return $str;
        }
}