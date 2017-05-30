<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSJ_Plugin_CustField_FSJDate extends FSJ_Plugin_CustField
{
	function ToXML($field)
	{
		$xml = "<field name=\"" . htmlspecialchars($field->name) . "\" type=\"calendar\" label=\"" . htmlspecialchars($field->title) . "\" ";
		$xml .= ">";	
		$xml .= "</field>";
		return $xml;
	}	
}