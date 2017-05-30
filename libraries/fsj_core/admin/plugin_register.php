<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport("fsj_core.lib.utils.xml");
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('fsj_core.lib.utils.database');
jimport("fsj_core.lib.utils.plugin_handler");

// plugin handler class
class FSJ_Plugin_Register
{
	function RegisterPlugins($component, $path = '')	
	{
		$log = "";
		
		//$log = "Register for $component\n";
		
		if ($path)
		{
			$path .= DS.'plugins'.DS;
		} else {
			$path = 'components'.DS.$component.DS.'plugins'.DS;
		}
		
		//$log .= "Path : $path\n";
		//$log .= "Full Path " . JPATH_ROOT.DS.$path . "\n";
		if (JFolder::exists(JPATH_ROOT.DS.$path))
		{
			$types = JFolder::folders(JPATH_ROOT.DS.$path);
			
			foreach ($types as $type)
			{
				$log .= $this->RegisterPluginType($path, $type);
				
				$plugins = JFolder::folders(JPATH_ROOT.DS.$path.$type);
				
				foreach ($plugins as $plugin)
				{
					if (substr($plugin, 0, strlen($type) + 1) != $type . ".")
						continue;
					
					list($junk, $plugin) = explode(".", $plugin, 2);
					
					$log .= $this->RegisterPlugin($path, $type, $plugin);	
				}
			}
		}
		
		return $log;
	}
	
	function RegisterPlugin($path, $type, $plugin)
	{
		$xmlfile = JPATH_ROOT.DS.$path.$type.DS."{$type}.{$plugin}".DS."{$type}.{$plugin}.xml";
		
		if (!JFile::exists($xmlfile))
			return;
		
		$log = "Register Plugin : $type.$plugin\n";
		
		$fields['type'] = $type;
		$fields['name'] = $plugin;
		$fields['path'] = $path.$type.DS."{$type}.{$plugin}";
		$fields['state'] = 1;
		
		$xml = @simplexml_load_file($xmlfile);
		
		$plugin_type = FSJ_Plugin_Handler::GetPluginType($type);
		
		if ($xml)
		{
			if ($xml->name)
				$fields['title'] = (string)$xml->name;	
			if ($xml->description)
				$fields['description'] = (string)$xml->description;	
			if ($xml->author)
				$fields['author'] = (string)$xml->author;	
		
			$params = FSJ_XML::XMLToClass($xml->params);
			$fields['params'] = json_encode($params);
			
			$settings = new stdClass();
			if (isset($plugin_type->params->pluginsettings))
				$settings = $plugin_type->params->pluginsettings;

			if (isset($params->settings))
			{
				foreach ($params->settings as $set => $data)
				{
					if (!isset($settings->$set)) $settings->$set = new stdClass();
					
					foreach ($data as $key => $value)
						$settings->$set->$key = $value;
				}
			}
			
			$fields['settings'] = json_encode($settings);
		}

		$db = JFactory::getDBO();		
		$qry = "SELECT * FROM #__fsj_plg_plugin WHERE name = '" . $db->escape($fields['name']) . "' AND type = '" . $db->escape($fields['type']) . "'";
		$db->setQuery($qry);
		$item = $db->loadObjectList();
		
		if (count($item) == 0)
		{
			FSJ_Database::Insert('#__fsj_plg_plugin', $fields);
		} else {
			unset($fields['settings']);
			unset($fields['state']);
			FSJ_Database::Update('#__fsj_plg_plugin', array('name', 'type'), $fields);
		}

		return $log;		
	}
	
	function RegisterPluginType($path, $type)
	{
		$xmlfile = JPATH_ROOT.DS.$path.$type.DS."{$type}.xml";
		
		if (!JFile::exists($xmlfile))
			return;
		
		$log = "Register Plugin Type : $type\n";
		
		$fields['name'] = $type;
		$fields['path'] = $path.$type;
		
		$xml = @simplexml_load_file($xmlfile);
		
		if ($xml)
		{
			if ($xml->name)
				$fields['title'] = (string)$xml->name;	
			if ($xml->description)
				$fields['description'] = (string)$xml->description;	
			if ($xml->author)
				$fields['author'] = (string)$xml->author;	
			if ($xml->component)
				$fields['component'] = (string)$xml->component;	
			
			$params = FSJ_XML::XMLToClass($xml->params);
			$fields['params'] = json_encode($params);
	
			if (isset($params->settings))
				$fields['settings'] = json_encode($params->settings);
		}
		
		$db = JFactory::getDBO();		
		$qry = "SELECT * FROM #__fsj_plg_type WHERE name = '" . $db->escape($fields['name']) . "'";
		$db->setQuery($qry);
		$item = $db->loadObjectList();
		
		if (count($item) == 0)
		{
			FSJ_Database::Insert('#__fsj_plg_type', $fields);
		} else {
			unset($fields['settings']);
			FSJ_Database::Update('#__fsj_plg_type', array('name'), $fields);
		}
		
		if ($xml && $xml->template)
		{
			$log .= FSJ_Plugin_Register::RegisterTemplates("main", $xml) . "\n";
		}
		
		return $log;
	}
	
	static function RegisterTemplates($component, $xml)
	{
		$fsjcom = str_replace("com_fsj_","",$component);
		$log = array();
		
		foreach ($xml->template as $template)
		{
			$replace = array();
			$replace['component'] = $fsjcom;
			$replace['type'] = (string)$template->attributes()->name;
			$replace['xmlfile'] = (string)$template->path;
			$replace['instpath'] = (string)$template->templates;
			
			$xmlfile_tmpl = JPATH_ROOT.DS.$replace['xmlfile'];
			
			if (!file_exists($xmlfile_tmpl))
			{
				$log[] = "Cant load template {$replace['type']}, unable to load its xml definition {$xmlfile_tmpl}";
				continue;
			}
			
			$xml2 = @simplexml_load_file($xmlfile_tmpl);
			
			if (!$xml2)
			{
				$log[] = "Cant load template {$replace['type']}, unable to parse its xml definition {$xmlfile_tmpl}";
				continue;
			}
			
			$replace['title'] = (string)$xml2->overview->attributes()->title;
			$replace['description'] = trim((string)$xml2->overview->description);
			
			FSJ_Database::Replace("#__fsj_tpl_type", $replace);	
			
			$log[] = "Register template type {$replace['type']}";
			
			$log[] = FSJ_Plugin_Register::AddTemplates($replace);
			// need to look in the instpath folder for any xml files that we should register as instances of this template		
		}
		
		return implode("\n", $log);
	}
	
	static function AddTemplates(&$templatetype)
	{
		$log = array();
		$path = $templatetype['instpath'];
		
		$files = JFolder::files(JPATH_ROOT.DS.$path, ".xml");
		
		foreach ($files as $file)
		{
			$xml = @simplexml_load_file(JPATH_ROOT.DS.$path.DS.$file);
			if (!$xml)
				continue;
			
			if (!$xml->install)
				continue;
			
			if ((string)$xml->install->attributes()->target != $templatetype['type'])
				continue;
			
			if ((string)$xml->install->attributes()->com != $templatetype['component'])
				continue;
			
			$name = (string)$xml->install->attributes()->name;
			
			$db = JFactory::getDBO();
			
			$qry = "SELECT * FROM #__fsj_tpl_template WHERE component = '" . $db->escape($templatetype['component']) . "' AND type = '" . $db->escape($templatetype['type']);
			$qry .=  "' AND name = '" . $db->escape($name) . "'";
			
			//echo "Qry : $qry<br>";
			$db->setQuery($qry);
			$result = $db->loadObjectList();
			
			/*if (count($result) > 0)
				continue;*/
			
			$log[] = "Add template $file<br>";
			
			$data['component'] = $templatetype['component'];
			$data['type'] = $templatetype['type'];
			$data['name'] = $name;
			$data['title'] = (string)$xml->install->title;
			$data['description'] = (string)$xml->install->description;
			$data['noedit'] = 1;
			
			$sections = array();

			foreach ($xml->section as $section)
			{
				$name = (string)$section->attributes()->name;
				$content = (string)$section;
				
				$sections[$name] = $content;
			}
		
			$params = new stdClass();
			$params->tmpl = $sections;
			$params->css = "";
			
			if ($xml->css)
				$params->css = (string)$xml->css;			
		
			$data['params'] = json_encode($params);
			
			FSJ_Database::Replace("#__fsj_tpl_template", $data);	
		}
		
		return implode("\n", $log);
	}
}
