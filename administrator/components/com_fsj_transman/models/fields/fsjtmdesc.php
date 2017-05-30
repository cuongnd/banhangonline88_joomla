<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');
jimport("joomla.filesystem.folder");

class JFormFieldFSJTMDesc extends JFormField
{
	protected function getInput()
	{
		return "";	
	}
	
	static $extensions;
	function loadExts()
	{
		if (!empty(self::$extensions))
			return;
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__extensions");
		self::$extensions = $db->loadObjectList();	
	}
	
	function buildDescription($value, $name, $item)
	{
		$this->item = $item;
		
		if ($item->filename == "ini")
		return JText::_("FSJ_TM_JOOMLA_CORE");
		
		$element = str_ireplace(".ini", "", $item->filename);
		
		$append = array();
		$type = substr($element, 0, strpos($element, "_"));
		
		if (strpos($element, ".") > 0)
		{
			$element = substr($element, 0, strpos($element, "."));	
		}

		$this->loadLang($element);

		if ($type == "com")
		{	
			$ext = $this->findExt("component", $element);
			
			if ($ext)
			$element = $ext->name;	
		} elseif ($type == "lib")
		{	
			$ext = $this->findExt("library", str_replace("lib_","",$element));
			
			if ($ext)
			$element = $ext->name;	
		} elseif ($type == "tpl")
		{	
			$ext = $this->findExt("template", str_replace("tpl_","",$element));
			
			if ($ext)
			$element = $ext->name;	
		} elseif ($type == "plg")
		{	
			list($type, $folder, $elementnamem) = explode("_", $element, 3);
			
			$ext = $this->findExt("plugin", $elementnamem, $folder);
			
			if ($ext)
			$element = $ext->name;	
		} elseif ($type == "mod")
		{	
			$ext = $this->findExt("module", $element);
			
			if ($ext)
			$element = $ext->name;	
		}
		
		return JText::_($element);
	}
	
	function AdminDisplay($value, $name, $item)
	{
		echo $this->buildDescription($value, $name, $item);
	}
	
	function loadLang($element)
	{
		$language = JFactory::getLanguage();
		if ($language->load($element.".sys"))
			return true;
		
		if ($language->load($element.".sys", JPATH_ADMINISTRATOR.DS."components".DS.$element))
			return true;
		
		list($client_id, $tag, $component, $filetemp) = explode("|", $this->item->id);
		$path = FSJ_TM_Helper::getPath($client_id, $tag, $component);
		
		$path = str_replace("language", "", $path);
		$path = str_ireplace($tag, "", $path);
		
		if ($language->load($element.".sys", $path))
			return true;	
	}
	
	function findExt($type, $element, $folder = null)
	{
		$this->loadExts();
		
		foreach (self::$extensions as $ext)
		{
			if ($folder && $ext->folder != $folder)
				continue;
			
			if ($ext->type == $type && $ext->element == $element)
				return $ext;
		}	
		
		return null;
	}
}
