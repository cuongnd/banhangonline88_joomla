<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSJ_Plugin_Type_PickType
{
	static function GetInstance($picktype)
	{
		$plugin = FSJ_Plugin_Handler::GetPlugin("picktype", $picktype);
		
		if (!$plugin)
		{
			//echo "PickType : $picktype<br>";
			//return $this;
		}
		
		require_once(JPATH_ROOT.DS.$plugin->path.DS."picktype.{$picktype}.php");
		
		$class = "FSJ_Plugin_PickType_" . $picktype;
		$obj = new $class();
		$obj->type = $picktype;
		$obj->self_plugin = $plugin;
		
		return $obj;
	}	
}

class FSJ_Plugin_PickType
{
	function Display()
	{
		require (JPATH_ROOT.DS.$this->self_plugin->path.DS.'tmpl'.DS."{$this->type}.php");
	}
	
	function InitFromLinked(&$plugin)
	{
		$this->plugin = $plugin;	
		$this->pluginid = $plugin->name;
		//$this->pluginxml = $pluginxml;	
	}
}