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
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty indent modifier plugin
 *
 * Type:     modifier<br>
 * Name:     indent<br>
 * Purpose:  indent lines of text
 *
 * @link http://www.smarty.net/manual/en/language.modifier.indent.php indent (Smarty online manual)
 * @author Uwe Tews
 * @param array $params parameters
 * @return string with compiled code
 */

function smarty_modifiercompiler_indent($params, $compiler)
{
    if (!isset($params[1])) {
        $params[1] = 4;
    }
    if (!isset($params[2])) {
        $params[2] = "' '";
    }
    return 'preg_replace(\'!^!m\',str_repeat(' . $params[2] . ',' . $params[1] . '),' . $params[0] . ')';
}

?>