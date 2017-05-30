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
 *
 * @package Smarty
 * @subpackage PluginsFilter
 */

/**
 * Smarty htmlspecialchars variablefilter plugin
 *
 * @param string                   $source input string
 * @param Smarty_Internal_Template $smarty Smarty object
 * @return string filtered output
 */
function smarty_variablefilter_htmlspecialchars($source, $smarty)
{
    return htmlspecialchars($source, ENT_QUOTES, Smarty::$_CHARSET);
}

?>