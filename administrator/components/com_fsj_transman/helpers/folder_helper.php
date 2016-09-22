<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
jimport("joomla.filesystem.folder");
jimport("joomla.filesystem.file");
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'general_helper.php');
class FSJ_TM_Folder_Helper
{
	static $comp_folders;
	static function ScanForComponentLanguages()
	{
		if (empty(self::$comp_folders))
		{
			$admin = self::_ScanSet("administrator" . DS . "components", true,'c');
			$site = self::_ScanSet("components", false,'c');
			$modules = self::_ScanSet("modules", false, "m");
			$modules_admin = self::_ScanSet("administrator" . DS . "modules", true, "m");
			$templates = self::_ScanSet("templates", false, "t");
			$plugins = self::_ScanPlugins();
			self::$comp_folders = array_merge($admin, $site, $modules, $modules_admin, $templates, $plugins);
		}
		return self::$comp_folders;
		return array();
	}
	static function describePath($client_id, $prefix, $component)
	{
		if ($component == "general")
			return self::describePrefix($client_id, $prefix);
		return self::describePrefix($client_id, $prefix) . ": $component";		
	}
	static function describePrefix($client_id, $value)
	{
		switch ($value)
		{
			case '':
			case 'g':
				return self::describeClient($client_id);
			case 'c':
				return self::describeClient($client_id) . " " . JText::_("FSJ_TM_COMPONENT");
			case 't':
				return JText::_("FSJ_TM_TEMPLATE");
			case 'm':
				return self::describeClient($client_id) . " " . JText::_("FSJ_TM_MODULE");	
			case 'p':
				return JText::_("FSJ_TM_PLUGIN");	
		}
		if (substr($value, 0, 1) == "p")
		{
			$name = substr($value, 2);
			return JText::_("FSJ_TM_PLUGIN")." ($name)";	
		}
		return $value;
	}
	static function describeClient($client_id)
	{
		if ($client_id == 1)
			return JText::_('FSJ_TM_ADMIN');
		return JText::_('FSJ_TM_SITE');
	}
	static function _ScanPlugins()
	{
		$types = JFolder::folders(JPATH_ROOT.DS."plugins");
		$result = array();
		foreach ($types as $type)
		{
			$items = self::_ScanSet("plugins".DS.$type, false, "p.".$type);
			$result = array_merge($result, $items);
		}
		return $result;
	}
	static function _ScanSet($path, $is_admin, $prefix = null)
	{
		$results = array();
		$components = JFolder::folders(JPATH_ROOT.DS.$path);
		foreach ($components as $component)
		{
			$lang_folder = $path . DS . $component . DS . "language";
			if (file_exists(JPATH_ROOT.DS.$lang_folder))
			{
				$item = new stdClass();
				$item->path = $lang_folder;
				$item->component = $component;
				$item->admin = $is_admin;
				$item->prefix = $prefix;
				$results[] = $item;
			}
		}		
		return $results;
	}
}
