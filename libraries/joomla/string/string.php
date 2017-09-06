<?php
/**
 * @package     Joomla.Platform
 * @subpackage  String
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\String\StringHelper;

/**
 * String handling class for utf-8 data
 * Wraps the phputf8 library
 * All functions assume the validity of utf-8 strings.
 *
 * @since       11.1
 * @deprecated  4.0  Use {@link \Joomla\String\StringHelper} instead unless otherwise noted.
 */
abstract class JString extends StringHelper
{
    /**
     * Split a string in camel case format
     *
     * "FooBarABCDef"            becomes  array("Foo", "Bar", "ABC", "Def");
     * "JFooBar"                 becomes  array("J", "Foo", "Bar");
     * "J001FooBar002"           becomes  array("J001", "Foo", "Bar002");
     * "abcDef"                  becomes  array("abc", "Def");
     * "abc_defGhi_Jkl"          becomes  array("abc_def", "Ghi_Jkl");
     * "ThisIsA_NASAAstronaut"   becomes  array("This", "Is", "A_NASA", "Astronaut")),
     * "JohnFitzgerald_Kennedy"  becomes  array("John", "Fitzgerald_Kennedy")),
     *
     * @param   string $string The source string.
     *
     * @return  array   The splitted string.
     *
     * @deprecated  12.3 (Platform) & 4.0 (CMS) - Use JStringNormalise::fromCamelCase()
     * @since   11.3
     */
    public static function splitCamelCase($string)
    {
        JLog::add('JString::splitCamelCase has been deprecated. Use JStringNormalise::fromCamelCase.', JLog::WARNING, 'deprecated');

        return JStringNormalise::fromCamelCase($string, true);
    }

    static function sub_string($str, $len, $more = '...', $encode = 'utf-8')
    {
        if ($str == "" || $str == NULL || is_array($str) || strlen($str) <= $len) {
            return $str;
        }
        $str = mb_substr($str, 0, $len, $encode);
        if ($str != "") {
            if (!substr_count($str, " ")) {
                $str .= $more;
                return $str;
            }
            while (strlen($str) && ($str[strlen($str) - 1] != " ")) {
                $str = mb_substr($str, 0, -1, $encode);
            }
            $str = mb_substr($str, 0, -1, $encode);
            $str .= $more;
        }
        $str = preg_replace("/[[:blank:]]+/", " ", $str);
        return $str;
    }

    public static function clean($string)
    {
        $string = str_replace('', '-', $string); // Replaces all spaces with hyphens.
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    /**
     * Does a UTF-8 safe version of PHP parse_url function
     *
     * @param   string $url URL to parse
     *
     * @return  mixed  Associative array or false if badly formed URL.
     *
     * @see     http://us3.php.net/manual/en/function.parse-url.php
     * @since   11.1
     * @deprecated  4.0 (CMS) - Use {@link \Joomla\Uri\UriHelper::parse_url()} instead.
     */
    public static function parse_url($url)
    {
        JLog::add('JString::parse_url has been deprecated. Use \\Joomla\\Uri\\UriHelper::parse_url.', JLog::WARNING, 'deprecated');

        return \Joomla\Uri\UriHelper::parse_url($url);
    }


    public static function vn_str_filter($str)
    {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );

        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    }
}
