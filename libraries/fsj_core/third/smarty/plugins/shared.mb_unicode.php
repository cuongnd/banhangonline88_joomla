<?php
/**
 * @package Freestyle Joomla
 * @copyright 2008 New Digital Group, Inc.
 * @author Monte Ohrt <monte at ohrt dot com>
 * @author Uwe Tews
 * @author Rodney Rehm
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/**
 * Smarty shared plugin
 *
 * @package Smarty
 * @subpackage PluginsShared
 */

/**
 * convert characters to their decimal unicode equivalents
 *
 * @link http://www.ibm.com/developerworks/library/os-php-unicode/index.html#listing3 for inspiration
 * @param string $string   characters to calculate unicode of
 * @param string $encoding encoding of $string, if null mb_internal_encoding() is used
 * @return array sequence of unicodes
 * @author Rodney Rehm
 */
function smarty_mb_to_unicode($string, $encoding=null) {
    if ($encoding) {
        $expanded = mb_convert_encoding($string, "UTF-32BE", $encoding);
    } else {
        $expanded = mb_convert_encoding($string, "UTF-32BE");
    }
    return unpack("N*", $expanded);
}

/**
 * convert unicodes to the character of given encoding
 *
 * @link http://www.ibm.com/developerworks/library/os-php-unicode/index.html#listing3 for inspiration
 * @param integer|array $unicode  single unicode or list of unicodes to convert
 * @param string        $encoding encoding of returned string, if null mb_internal_encoding() is used
 * @return string unicode as character sequence in given $encoding
 * @author Rodney Rehm
 */
function smarty_mb_from_unicode($unicode, $encoding=null) {
    $t = '';
    if (!$encoding) {
        $encoding = mb_internal_encoding();
    }
    foreach((array) $unicode as $utf32be) {
        $character = pack("N*", $utf32be);
        $t .= mb_convert_encoding($character, $encoding, "UTF-32BE");
    }
    return $t;
}

?>