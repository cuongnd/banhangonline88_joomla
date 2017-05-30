<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('fsj_core.lib.utils.xml');

/**
 * Pluging handler classes 
 **/

// plugin handler class
if (!class_exists("FSJ_Plugin_Handler"))
{
	class FSJ_Plugin_Handler
	{
		static $plugin_types = array();
		static $plugins = array();
		static function GetPlugins($type)
		{
			if (array_key_exists($type, self::$plugin_types))
				return self::$plugin_types[$type];
			
			$db	= JFactory::getDBO();
			$qry = "SELECT * FROM #__fsj_plg_plugin WHERE type = '{$db->escape($type)}'";
			$db->setQuery($qry);
			$plugins = $db->loadObjectList('name');
			foreach($plugins as &$plugin)
			{
				$plugin->params = json_decode($plugin->params, false);	
				self::$plugins[$plugin->type][$plugin->name] = $plugin;
			}
			self::$plugin_types[$type] = $plugins;
			return $plugins;
		}
		
		static function GetPlugin($type, $name)
		{
			if (array_key_exists($type, self::$plugins) &&
				array_key_exists($name, self::$plugins[$type]))
				return self::$plugins[$type][$name];
			
			$db	= JFactory::getDBO();
			$qry = "SELECT * FROM #__fsj_plg_plugin WHERE type = '{$db->escape($type)}' AND name = '{$db->escape($name)}'";
			$db->setQuery($qry);
			$plugin = $db->loadObject();
			
			self::$plugins[$type][$name] = null;
			
			if (!$plugin)
				return null;
			
			$plugin->params = json_decode($plugin->params, false);	
			$plugin->settings = json_decode($plugin->settings, false);	
			
			self::$plugins[$type][$name] = $plugin;
			
			return $plugin;
		}
		
		static function GetPluginInstance($type, $name)
		{
			$plugin = self::GetPlugin($type, $name);
			/*echo "PLuginb<br>";
			print_p($plugin);*/
			return self::GetInstance($plugin);	
		}
		
		static $plugin_type_obj = array();
		static function GetPluginType($type)
		{
			if (array_key_exists($type, self::$plugin_type_obj))
				return self::$plugin_type_obj[$type];
			
			
			$db	= JFactory::getDBO();
			$qry = "SELECT * FROM #__fsj_plg_type WHERE name = '{$db->escape($type)}'";
			$db->setQuery($qry);
			$plugin = $db->loadObject();
			if ($plugin)
			{
				$plugin->params = json_decode($plugin->params, false);	
				$plugin->settings = json_decode($plugin->settings, false);	
			}
			
			self::$plugin_type_obj[$type] = $plugin;
			
			return $plugin;		
		}
		
		static function GetInstance(&$plugin)
		{		
			$classname = "FSJ_Plugin_{$plugin->type}_{$plugin->name}";
			
			if (!class_exists($classname))
			{
				$phpfile = JPATH_ROOT.DS.$plugin->path.DS."{$plugin->type}.{$plugin->name}.php";
				if (!file_exists($phpfile))
				{
					$classname = "FSJ_Plugin_{$plugin->type}";
				} else {
					require_once($phpfile);
				}
			}
			
			$object = new $classname();
			$object->plugin = $plugin;
			$object->blank = true;
			
			return $object;
		}
	}

	// base plugin class
	class FSJ_Plugin 
	{
		function Register($name, $opts)
		{
			
		}
		
		function getParam(&$params, $name, $default = null)
		{
			if (isset($params->values) && array_key_exists($name, $params->values))
				return $params->values[$name];
			
			return $default;
		}

	}

	class FSJ_Plugin_Type
	{
		
	}
	
	class FSJ_Plugin_CustField extends FSJ_Plugin
	{
		
	}
}