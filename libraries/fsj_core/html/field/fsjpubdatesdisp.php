<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

jimport('fsj_core.lib.utils.format');

class JFormFieldFSJPubDatesDisp extends JFormField
{
	function getInput()
	{
		return "";	
	}
	
	function AdminDisplay($value, $name, $item)
	{
		$from = max(0, strtotime($item->publish_up));
		$to = max(0, strtotime($item->publish_down));
		$now = time();
		if ($to && $to < $now)
		{
			$state = "<span style='color: red;'>" . JText::_("FSJ_PUBDATES_EXPIRED") . "</span>";
		} else if ($from && $from > $now) 
		{
			$state = "<span style='color: blue;'>" . JText::_("FSJ_PUBDATES_UPCOMING") . "</span>";
		} else {
			$state = "<span style='color: green;'>" . JText::_("FSJ_PUBDATES_ACTIVE") . "</span>";	
		}
		
		$output = array();
		
		if ($from)
			$output[] = "<b>" . JText::_("FSJ_PUBDATES_FROM") . "</b> " . FSJ_Format::Date($from, 'DATE_FORMAT_LC4', true);
		if ($to)
			$output[] = "<b>" . JText::_("FSJ_PUBDATES_TO") . "</b> " . FSJ_Format::Date($to, 'DATE_FORMAT_LC4', true);
		
		if (!$from && !$to)
			$output[] = JText::_("FSJ_PUBDATES_ALWAYS");		
		
		return "<div style='float:right'>" . $state . "</div>" . implode("<br />", $output);
	}
}
