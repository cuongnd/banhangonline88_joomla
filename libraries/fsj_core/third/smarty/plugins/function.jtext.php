<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

function smarty_function_jtext($params, $template)
{
	static $counters = array();

	if (count($params) == 0)
		return "";

	if (count($params) > 1)
	{
		return call_user_func_array(array("JText","sprintf"), $params); 
	} else {
		return JText::_($params[0]);	
	}
}

?>