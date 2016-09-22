<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
jimport( 'fsj_core.lib.utils.format');

function smarty_function_formatsize($params, $template)
{
	if (count($params) == 0)
		return "";

	$size = reset($params);

	return FSJ_Format::Size($size);
}

?>