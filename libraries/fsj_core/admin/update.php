<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport( 'joomla.version' );
jimport( 'joomla.installer.installer' );

jimport('fsj_core.admin.adminhelper');
jimport('fsj_core.admin.dbupdater');
jimport('fsj_core.lib.utils.plugin_handler');
jimport('fsj_core.admin.plugin_register');
jimport('fsj_core.lib.utils.database');

global $fsjjversion;

class FSJ_Updater
{
	var $done = array();
	
	function Process($path = "")
	{
		$log = array();
	
		// get a list of components to do updates for
		$this->comps = $this->GetComponentsList();
	
		$log[] = $this->UpdateSingle("com_fsj_main", $path);
		$log[] = $this->UpdateCore();
		$log[] = $this->UpdateSingle("com_fsj_includes", $path);

		// do rest of components
		foreach ($this->comps as $comp)
		{
			if (in_array($comp->element, $this->done))
				continue;
			
			$log[] = $this->UpdateComponent($comp, $path);	
		}

		$misc_log = array();
		$misc_log['name'] = "Freestyle Joomla General";
		$misc_log['log'] = array();
		$misc_log['log'][] = array('name' => 'Validate admin menus', 'log' => $this->MoveAdminMenu());
		$misc_log['log'][] = array('name' => 'Sort admin sub menus menus', 'log' => $this->SortAdminMenu());
		$misc_log['log'][] = array('name' => 'Validate settings db tables', 'log' => $this->RemoveBrokenSettings());
		$misc_log['log'][] = array('name' => 'Rename admin menus', 'log' => $this->RenameAdminMenus());
		

		$log[] = $misc_log;

		return $log;	
	}
	
	function RemoveBrokenSettings()
	{
		$qry = "SELECT * FROM #__fsj_main_settings WHERE j_asset = 0";
		$db = JFactory::getDBO();
		
		$log = array();
		
		$db->setQuery($qry);
		$items = $db->loadObjectList();
		foreach ($items as $item)
		{
			if (substr($item->name, 0, 8) == "default.") continue;
			if ($item->name == "com_fsj") continue;
			
			$qry = "DELETE FROM #__fsj_main_settings WHERE id = " . $item->id;
			$db->setQuery($qry);
			$db->Query();
			
			$log[] = "Removing broken settings entry for {$item->name}";
		}
		
		// clear all lft and rgt
		$qry = "UPDATE #__fsj_main_settings SET lft = 0, rgt = 0";
		$db->setQuery($qry);
		$db->Query();
		
		// rebuild all entries
		$setting = JTable::getInstance('FSJSettings', 'JTable');
		$setting->rebuild(0);
		
		// we may be left with some entries that have no lft or rgt now, so we need to reparent them if possible!
		$qry = "DELETE FROM #__fsj_main_settings WHERE lft = 0 AND rgt = 0";
		$db->setQuery($qry);
		$db->Query();
		
		return implode("\n", $log);
	}
	
	function UpdateSingle($com, $path)
	{
		foreach ($this->comps as $comp)
		{
			if ($comp->element != $com)
				continue;
			return $this->UpdateComponent($comp, $path);
		}	
		
		return "";
	}
	
	function UpdateCore()
	{
		$log = array();
		$log['name'] = "Freestyle Joomla Core Library";
		$log['log'] = array();
		
		$pluginregister = new FSJ_Plugin_Register();

		$path = 'libraries'.DS."fsj_core";

		$log['log'][] = array('name' => 'Register Plugins', 'log' => $pluginregister->RegisterPlugins("fsj_core", $path));	
		
		return $log;
	}
	
	function UpdateComponent($comp, $path)
	{
		$com = $comp->element;

		$this->done[] = $com;
		
		$lang = JFactory::getLanguage();
		$dbupdate = new FSJ_DBUpdater();
		$pluginregister = new FSJ_Plugin_Register();
		
		// load language file for component
		$lang->load($comp->element);	
			
		$mc = json_decode($comp->manifest_cache);
		
		$log = array();
		$log['name'] = JText::_($mc->description);
		$log['log'] = array();
			
			
		// update database
		$log['log'][] = array('name' => 'Setup Component Asset', 'log' => $this->MoveComAsset($com));
		$log['log'][] = array('name' => 'Database Tables', 'log' => $dbupdate->UpdateDatabase($com, $path));	
		$log['log'][] = array('name' => 'Base Settings', 'log' => $dbupdate->BaseSettings($com));	
		$log['log'][] = array('name' => 'Database Entries', 'log' => $dbupdate->DatabaseEntries($com, $path));	
		$log['log'][] = array('name' => 'Register Plugins', 'log' => $pluginregister->RegisterPlugins($com));	
		$log['log'][] = array('name' => 'Register Templates', 'log' => $this->RegisterTemplates($com));	
		
		//$log['log'][] = array('name' => 'Register Templates', 'log' => $pluginhandler->RegisterTemplates($com, $path));	
		
		// component specific updates if available
		$file = JPATH_SITE.DS.'administrator'.DS.'components'.DS.$com.DS.'update.php';
		if (file_exists($file))
		{
			include_once($file);
			$class = $com . "_Update";
			if (class_exists($class))
			{
				$upd = new $class();
				$upd->Run($log['log']);	
			}
		}			
		
		return $log;
	}
	
	function RegisterTemplates($component)
	{
		$fsjcom = str_replace("com_fsj_","",$component);
		$updatefile = JPATH_SITE.DS.'administrator'.DS.'components'.DS.$component.DS."$fsjcom.xml";
		
		$xml = @simplexml_load_file($updatefile);
		
		if (!$xml)
			return "ERROR: Unable to load component xml";

		return FSJ_Plugin_Register::RegisterTemplates($component, $xml);
	}
	
	function SortCompList($a, $b)
	{
		return strcmp($a->element, $b->element);
	}
	
	function MoveAdminMenu()
	{
		//return "Skip";
		
		$log = array();
		
		$menu = JTable::getInstance('Menu', 'JTable');
		if (!$menu->load(array('menutype' => 'main', 'link' => 'index.php?option=com_fsj_main')))
			return "ERROR: Cannot find FSJ Main";
		
		$menu->alias = $menu->title;
		$menu->store();	
		
		$comps = $this->GetComponentsList();
			
		foreach ($comps as &$comp)
		{
			$comp->title = JText::_($comp->name . "_MENU");
		}
			
		usort($comps, array($this, "SortCompList"));
			
		foreach ($comps as &$comp)
		{
			if ($comp->element == "com_fsj_main") continue;
			//if ($comp->element == "com_fsj_includes") continue;
				
			$commenu = JTable::getInstance('Menu', 'JTable');
				
			if ($commenu->load(array('menutype' => 'main', 'link' => 'index.php?option=' . $comp->element)))
			{
				// move to sub menu pos
				if ($commenu->parent_id != $menu->id)
				{
					$commenu->setLocation($menu->id, 'last-child');
					$commenu->alias =  JText::_($comp->name . "_MENU");
					$commenu->store();	
					$log[] = "Moved {$comp->name} menu to correct location";
				}
			} else {
				// item is missing, create a new one!	
				$commenu->menutype = "main";
				$commenu->title = $comp->title;
				$commenu->alias = JText::_($comp->name . "_MENU");
				$commenu->link = 'index.php?option=' . $comp->element;
				$commenu->type = 'component';
				$commenu->component_id = $comp->extension_id;
				$commenu->access = 1;
				$commenu->client_id = 1;
				$commenu->img = "components/{$comp->element}/assets/images/{$comp->element}-16.png";
				$commenu->setLocation($menu->id, 'last-child');
				$commenu->store();	
				$log[] = "Added missing {$comp->name} menu";
			}
		}

		return implode("\n", $log);
	}
	
	function SortAdminMenu()
	{
		$menu = JTable::getInstance('Menu', 'JTable');
		if (!$menu->load(array('menutype' => 'main', 'link' => 'index.php?option=com_fsj_main'))) return "ERROR: Cannot find FSJ Main";
			
		$parid = $menu->id;
		
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__menu WHERE parent_id = " . $parid . " ORDER BY title";
		$db->setQuery($sql);
		$items = $db->loadObjectList();
		
		foreach ($items as $item)
		{
			$commenu = JTable::getInstance('Menu', 'JTable');
				
			if ($commenu->load(array('menutype' => 'main', 'id' => $item->id)))
			{
				$commenu->setLocation($menu->id, 'last-child');
				$commenu->store();	
			}
		}
		
		return "Freestyle Joomla 'Components' menu sorted";
	}

	function RenameAdminMenus()
	{
		$sql = "SELECT * FROM #__menu WHERE menutype = 'main' AND link LIKE '%option=com_fsj_%'";
		$db = JFactory::getDBO();
		$db->setQuery($sql);
		$items = $db->loadObjectList();

		foreach ($items as $item)
		{
			$title = str_replace("index.php?option=", "", $item->link) . "_" . $item->alias;
			$title = str_replace("-", "_", $title);

			if ($item->title != $title)
			{
				$db->setQuery("UPDATE #__menu SET title = '" . $title . "' WHERE id = " . $item->id);
				$db->Query();
			}
		}
	}
	
	function MoveComAsset($com)
	{
		$parent_asset_name = "com_fsj_main";
		
		$log = array();
		
		if ($com == "com_fsj_main")
		{
			$parent_asset_name = "root.1";
		}
		
		$assets = JTable::getInstance("asset", 'JTable');
		
		if (!$assets->load(array('name' => $parent_asset_name)))
		{
			return "ERROR: Cannot find asset $parent_asset_name";
		}
			
		$main_id = $assets->id;
		
		if (!$assets->load(array('name' => $com)))
		{
			//echo "CANT Load asset for com $com<br>";
			// need to register a new asset for the component
			$comasset = JTable::getInstance("asset");
			$comasset->name = $com;
			$comasset->title = $com;
			$comasset->setLocation($main_id, 'last-child');
			if (!$comasset->store())
			{
				echo "Cannot save asset 2nd time<br>";
				$error = $assets->getError();
				echo "Error : $error<br>";
				exit;	
			}
			//return "ERROR: Cant find $com asset";
		} else {
			if ($assets->parent_id != $main_id)
			{
				$assets->setLocation($main_id, 'last-child');
				if (!$assets->store())
				{
					$log[] = "Cannot update asset location for {$assets->name}";
				} else {
					$log[] = "Moved {$assets->name} to correct asset parent";
				}
			}
			
		}
		
		//$assets->rebuild();
		
		return implode("\n", $log);
	}
	
	function GetComponentsList()
	{
		$db	= JFactory::getDBO();
		$qry = "SELECT * FROM #__extensions WHERE element LIKE '%com_fsj_%' AND type = 'component' ORDER BY name";
		$db->setQuery($qry);
		$items = $db->loadObjectList();
		
		foreach ($items as &$item)
		{
			$xmlfile = JPATH_ADMINISTRATOR.DS.'components'.DS.$item->element.DS.str_replace("com_fsj_", "", $item->element) . ".xml";
			if (!file_exists($xmlfile)) continue;
			$item->xml = simplexml_load_file($xmlfile); 				
		}
		return $items;		
	}
}

