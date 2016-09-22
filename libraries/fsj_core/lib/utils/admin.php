<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSJ_Admin_Helper
{
	static function getVersion($element, $type = "component")
	{
		
		if (substr($element, 0, 8) == "com_fsj_" && $type == "component")
		{
			$com = str_replace("com_", "", $element);
			$file = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$element.DS.$com.".xml";
			$xml = simplexml_load_file($file);
			
			if ($xml)
			{
				if ($xml->version)
				{
					return (string)$xml->version;	
				}	
			}
		}
		
		$db = JFactory::getDBO();
		$qry = "SELECT * FROM #__extensions WHERE element = '" . $db->escape($element) . "' AND type = '" . $db->escape($type) . "'";
		$db->setQuery($qry);
		
		$item = $db->loadObject();

		if ($item)
		{
			$mc = json_decode($item->manifest_cache);
			
			if ($mc)
			{
				return $mc->version;
			}
		}
		
		return "Unknown";		
	}
}