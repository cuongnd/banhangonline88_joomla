<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

function smarty_function_jdate($params, $template)
{
	if (count($params) == 0) return "";

	/*
	JDate
	
	1 param - format - display current date with that format
	2 param - format, timestamp - display date with format
	3 param - format, timestamp, offset - display offset date. From now use 'now' in timestamp
	*/
	//print_p($params);
	
	if (count($params) == 1)
	{
		$date = new JDate('now');
		return $date->format($params[0]);
	} else if (count($params) == 2)
	{
		$date = new JDate($params[1]);
		return $date->format($params[0]);
	} else if (count($params) == 3)
	{
		$date = new JDate($params[1] . " " . $params[2]);
		return $date->format($params[0]);
	}

	/*if (count($params) > 1)
	{
		return call_user_func_array(array("JText","sprintf"), $params); 
	} else {
		return JText::_($params[0]);	
	}*/
}

?>