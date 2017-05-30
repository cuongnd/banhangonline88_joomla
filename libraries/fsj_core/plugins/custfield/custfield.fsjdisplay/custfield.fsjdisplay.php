<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSJ_Plugin_CustField_FSJDisplay extends FSJ_Plugin_CustField
{
	function ToXML($field)
	{
		$xml = "<field name=\"" . htmlspecialchars($field->name) . "\" type=\"" . htmlspecialchars($field->fieldtype) . "\" label=\"" . htmlspecialchars($field->title) . "\" ";
		if ($field->params->hide_label)
			$xml .= " hide_label='1' ";
		$xml .= ">";	
		$xml .= "<inputtmpl><![CDATA[" . $field->params->inputtmpl . "]]></inputtmpl>";
		$xml .= "</field>";
		return $xml;
	}	
}