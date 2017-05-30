<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/**
 * Settings class
 **/
if (!class_exists("FSJ_Settings"))
{
	class FSJ_Settings 
	{
		static $component = '';
		/**
		 * Loading:
		 * 
		 * Store j_asset and name to id mappings when loading
		 * load from ground upwards
		 * for each item loaded, store its base settings
		 * store calculated settings from the item above
		 * 
		 **/
		
		static $loaded = array();
		static $asset_map = array();
		static $id_map = array();
		static $name_map = array();
		static $base_item = 0;
		
		static $menu_settings = null;
		
		static $debug = 0;
		
		
		static $powered_com = 'Freestyle Joomla';
		static $powered_link = 'http://www.freestyle-joomla.com';
		
		/**
		 * Special get function for dealing with settings pages on joomla backend. 
		 * Will need to provide specific set of values
		 **/
		static function GetLayer($set, $layers = -1, $top = '')
		{
			// loads the settings from $set	up by $layers layers upto but excluding $top
			
			// will return all loaded settings
			
			// will merge all loaded settings with what is loaded
		}

		/**
		 * process to load all settings
		 * 
		 * On component init call LoadBaseSettings with component
		 * 
		 * At start of display() get the set asset id we are displaying and call AddViewSettings with the set_asset_id if there is one and the function
		 * will retrieve the view settings and merge them in
		 *
		 * Once the items asset_id is available, call AddItemSettings
		 * 
		 * 
		 **/
		static function LoadBaseSettings($component = '')
		{
			// if component is blank, use the current option var
			if ($component == '') $component = JRequest::getVar('option');

			if ($component == FSJ_Settings::$component)
				return;

			FSJ_Settings::$component = $component;
			
			//echo "Loading base settings for $component<br>";
			
			// loads settings from name = $component upto the root entry
			
			$qry = "SELECT * FROM #__fsj_main_settings WHERE name = '$component'";
			$db = JFactory::getDBO();
			$db->setQuery($qry);
			$compitem = $db->loadObject();

			if (!$compitem)
			{
				$qry = "SELECT * FROM #__fsj_main_settings WHERE name = 'com_fsj_main'";						
				$db->setQuery($qry);
				$compitem = $db->loadObject();
			}

			$qry = "SELECT * FROM #__fsj_main_settings WHERE lft <= ".(int)$compitem->lft." AND rgt >= ".(int)$compitem->rgt." ORDER BY level ASC";
			$db->setQuery($qry);
			$items = $db->loadObjectList();
			
			//print_p(FSJ_Settings::$loaded);
			foreach ($items as &$item)
			{
				FSJ_Settings::_AddLayer($item);
			}
			//print_p(FSJ_Settings::$loaded);
			
			FSJ_Settings::_AddLayer($compitem);
			FSJ_Settings::$base_item = $compitem->j_asset;
			
			return $compitem->j_asset;
		}
		
		function AddViewSettings($set_asset_id = 0)
		{
			//echo "Adding View Settings<br>";
			
			if ($set_asset_id > 0)
			{
				$qry = "SELECT * FROM #__fsj_main_settings WHERE j_asset = $set_asset_id";
				$db = JFactory::getDBO();
				$db->setQuery($qry);
				$item = $db->loadObject();
				
				FSJ_Settings::_AddLayer($item);
			}
		}
		
		static function AddItemSettings($item_asset_id, $set_as_base = true)
		{
			//echo "AddItemSettings : $item_asset_id<br>";
			$db = JFactory::getDBO();
			// loads settings from $item_asset_id upto but excluding the component settings item
			if (is_array($item_asset_id))
			{ 
				$ids = array();
				foreach ($item_asset_id as $id)
					$ids[] = (int)$id;
				
				$qry = "SELECT MAX(lft) as lft, MIN(rgt) as rgt FROM #__fsj_main_settings WHERE j_asset IN (" . implode(", " ,$ids) . ")";
			} else {
				$item_asset_id = (int)$item_asset_id;
				
				if ($item_asset_id < 1)
				{
					jimport('fsj_core.lib.utils.dbug');
					echo dumpStack();	
					exit;
				}
				
				$qry = "SELECT lft, rgt FROM #__fsj_main_settings WHERE j_asset = " . (int)$item_asset_id;
			}
			
			$db->setQuery($qry);
			$lr = $db->loadObject();
			
			if (!$lr)
			{
				return;
			}
			
			// ADDED level > 4 to try and optimize this, but it may fuck things up!
			$qry = "SELECT * FROM #__fsj_main_settings WHERE lft <= ".(int)$lr->lft." AND rgt >= ".(int)$lr->rgt." AND level > 4 ORDER BY level ASC";
			$db->setQuery($qry);
			$items = $db->loadObjectList();
			
			if (count($items) == 0)
			{
				// cant find item, so load by asset
				// should only ever happen for orphaned items, which will only happen when things go wrong!
				$qry = "SELECT * FROM #__fsj_main_settings WHERE j_asset = " . (int)$item_asset_id;
				$db->setQuery($qry);
				$items = $db->loadObjectList();
			}
			
			foreach ($items as &$item)
			{
				FSJ_Settings::_AddLayer($item);
			}
			
			if (!is_array($item_asset_id) && $set_as_base)
				FSJ_Settings::$base_item = $item_asset_id;
		}	
		
		/**
		 * Get setting will return the setting for the current view if no asset_id is passed, 
		 * otherwise the setting for the asset
		 */	
		static function GetSet($j_asset_id = 0)
		{
			if ($j_asset_id == 0)
				$j_asset_id = FSJ_Settings::$base_item;
			
			
			if (is_array($j_asset_id))
			{
				print_p($j_asset_id);
				echo dumpStack();		
				exit;
				return array();
			}
			
			//echo "Asset : $j_asset_id<br>";
			if (!array_key_exists($j_asset_id, FSJ_Settings::$asset_map))
			{
				// we dont have the current asset id loaded, load the settings for it	
				FSJ_Settings::AddItemSettings($j_asset_id);
			}
			
			if ($j_asset_id == 0)
			{
				//echo dumpStack();		
				//exit;
				return array();
			}

			$settings_id = FSJ_Settings::$asset_map[$j_asset_id];
			
			
			if (!empty(FSJ_Settings::$loaded[$settings_id]->settings))
			{
				$settings = FSJ_Settings::$loaded[$settings_id]->settings;
			} else {
				/*echo "Using Settings id $settings_id for Joomla Asset ID : $j_asset_id<br>";
				print_p(FSJ_Settings::$loaded);	
				exit;*/
				
				return array();
			}
			
			return $settings;
		}
		
		static function GetBaseItem()
		{
			if (FSJ_Settings::$base_item == "")
			{
				$option = JRequest::getVar('option');
				if (substr($option, 0, 8) == "com_fsj_")
					FSJ_Settings::LoadBaseSettings($option);
				else
					FSJ_Settings::LoadBaseSettings("com_fsj_main");
			}
			
			return FSJ_Settings::$base_item;
		}
		
		static function sortAssetID($asset_id)
		{
			if (!is_numeric($asset_id))
			{
				// asset id is a string, so look up the component name and find its asset id	
				
				//echo "String Asset : $asset_id - remapping<br>";
				
				//print_p(FSJ_Settings::$name_map);
				
				if (array_key_exists($asset_id, FSJ_Settings::$name_map))
				{
					$id = FSJ_Settings::$name_map[$asset_id];
					//echo "Got ID : $id<br>";
					
					//print_p(FSJ_Settings::$id_map);
					
					if (array_key_exists($id, FSJ_Settings::$id_map))
						$asset_id = FSJ_Settings::$id_map[$id];
				}
				
				//echo $asset_id . "<br>";
			}
			
			if ($asset_id == 0)
				$asset_id = FSJ_Settings::GetBaseItem();
			
			return $asset_id;
		}
		
		static function Get($setting_group, $setting, $j_asset_id = 0, $use_menu = true)
		{
			if (self::$debug)
				echo "Getting Setting : $setting_group, $setting, $j_asset_id ( = ";
			
			$j_asset_id = FSJ_Settings::sortAssetID($j_asset_id);
			
			if (self::$debug)
				echo $j_asset_id . ")<br>";
			
			// need to check for a menu item override first!
			if (FSJ_Settings::$menu_settings && $use_menu)
			{
				$menu_key = $setting_group."_".$setting;
				if (self::$debug)
				{
					echo "Menu Key : $menu_key<br>";
					print_p(FSJ_Settings::$menu_settings);
				}
				$menu_val = FSJ_Settings::$menu_settings->get($menu_key,"--XX--");
				if (self::$debug)
					echo "menu value - $menu_val<br>";
				if ($menu_val !== "--XX--")
				{
					if (self::$debug)
						echo "Returning menu value - $menu_val<br>";
					return $menu_val;
				}
			}
			
			$settings = FSJ_Settings::GetSet($j_asset_id);
			if (self::$debug)
				print_p($settings);
			
			if (isset($settings[$setting_group][$setting]))
				return $settings[$setting_group][$setting];
			
			return null;
		}	
		
		static private function _AddLayer(&$layer)
		{
			if (array_key_exists($layer->id, FSJ_Settings::$loaded))
				return;
			
			//echo "Adding layer {$layer->name} ({$layer->title})<br>";
			
			$layer->value = json_decode($layer->value, true);
			if (!is_array($layer->value)) $layer->value = array();
			
			FSJ_Settings::$loaded[$layer->id] = $layer;				
			FSJ_Settings::$name_map[$layer->name] = $layer->id;
			if ($layer->j_asset > 0)
			{
				FSJ_Settings::$asset_map[$layer->j_asset] = $layer->id;
				FSJ_Settings::$id_map[$layer->id] = $layer->j_asset;
			}
			
			// calculate generated settings
			if ($layer->parent_id > 0)
			{
				if (array_key_exists($layer->parent_id, FSJ_Settings::$loaded))
				{
					$parlayer = FSJ_Settings::$loaded[$layer->parent_id];
					if ($parlayer && isset($parlayer->settings))
						$layer->settings = $parlayer->settings;
					foreach ($layer->value as $set_id => &$set)
					{
						foreach ($set as $key => &$value)
						{
							$layer->settings[$set_id][$key] = $value;
						}		
					}
				}
			} else {
				$layer->settings = $layer->value;	
			}
		}	
		
		// get view settings
		static function AddMenuItemSettings($new_settings = null)
		{
			if ($new_settings)
				self::$menu_settings = $new_settings;
	
			// add settings for the current menu item, and the relevant data below the item (used at start of view to get the basic settings to be used)
			if (!self::$menu_settings)
			{
				self::$menu_settings = JFactory::getApplication('site')->getParams();
			}
		}
		
		static function Delete($asset_id)
		{
			if ($asset_id < 1)
				return;
			
			$db = JFactory::getDBO();
			
			$qry = "DELETE FROM #__fsj_main_settings WHERE j_asset = " . $db->escape($asset_id);
			
			$db->setQuery($qry);
			$db->Query();
		}
		
		static function DumpCurrent()
		{
			$settings = FSJ_Settings::GetSet(FSJ_Settings::$base_item);
			echo "Asset : " . FSJ_Settings::$base_item . "<br>";
			print_p($settings);	
			print_p(FSJ_Settings::$menu_settings);
		}
	}
}