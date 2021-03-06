<?php
/**
 * @package      Joomla.Language

 * @Copyright (C) 2005 - 2016 Open Source Matters and Hassan Abdalla (kwa lugha ya kiswahili). Haki zote zimehifadhiwa.
 * @@Leseni GNU General Public License toleo 2 au linalofuata; angalia LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * sw-KE localise class.
 *
 * @since        1.6
 */
abstract class sw_KELocalise
{
        /**
         * Returns the potential suffixes for a specific number of items
         *
         * @param    int $count  The number of items.
         *
         * @return   array  An array of potential suffixes.
         *
         * @since    1.6
         */
        public static function getPluralSuffixes($count)
        {
                if ($count == 0)
                {
                        $return =  array('0');
                }
                elseif($count == 1)
                {
                        $return =  array('1');
                }
                else
                {
                        $return = array('ZAIDI');
                }
                return $return;
        }

        /**
         * Returns the ignored search words
         *
         * @return   array  An array of ignored search words.
         *
         * @since    1.6
         */
        public static function getIgnoredSearchWords()
        {
                $search_ignore = array();
                $search_ignore[] = "na";
                $search_ignore[] = "ama";
                $search_ignore[] = "au";
                return $search_ignore;
        }

        /**
         * Returns the lower length limit of search words
         *
         * @return   integer  The lower length limit of search words.
         *
         * @since    1.6
         */
        public static function getLowerLimitSearchWord() 
		{
                return 3;
        }

        /**
         * Returns the upper length limit of search words
         *
         * @return   integer  The upper length limit of search words.
         *
         * @since    1.6
         */
        public static function getUpperLimitSearchWord()
        {
                return 20;
        }

        /**
         * Returns the number of chars to display when searching
         *
         * @return   integer  The number of chars to display when searching.
         *
         * @since    1.6
         */
        public static function getSearchDisplayedCharactersNumber()
        {
                return 200;
        }
}
