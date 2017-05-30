<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSJ_Plugin_CustField_FSJYesNo extends FSJ_Plugin_CustField
{
	function CF_Display($value, $settings)
	{
		if ($settings->display == "tf")
			return ($value) ? JText::_("Jtrue") : JText::_("Jfalse");
		
		if ($settings->display == "yn")
			return ($value) ? JText::_("JYES") : JText::_("JNO");
		
		if ($settings->display == "custom")
			return ($value) ? JText::_($settings->custom_yes) : JText::_($settings->custom_no);

		return ($value) ? 1 : 0;
	}	
	
	function CF_DisplayEmpty($settings)
	{
		return "";
	}
}