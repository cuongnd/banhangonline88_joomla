<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSJ_Plugin_CustField_FSJCombo extends FSJ_Plugin_CustField
{
	function ToXML($field)
	{
		$xml = "<field name=\"" . htmlspecialchars($field->name) . "\" type=\"" . htmlspecialchars($field->fieldtype) . "\" label=\"" . htmlspecialchars($field->title) . "\">";	
		
		if (isset($field->params->display))
		{
			$items = explode("\n", $field->params->display);
			foreach ($items as $item)
			{
				$item = trim($item);
				if (!$item) continue;
				
				$xml .= "<option value=\"" . htmlspecialchars($item) . "\">" . htmlspecialchars($item) . "</option>";	
			}	
		}
		
		$xml .= "</field>";
		return $xml;
	}	
}