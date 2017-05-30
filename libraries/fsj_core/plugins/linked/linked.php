<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('fsj_core.lib.utils.plugin_handler');

class FSJ_Linked extends FSJ_Plugin_Type
{
	var $edit = false;
	
	function __construct($source, $id)
	{
		$this->source = $source;
		$this->id = $id;
	}

	function LoadSingle(&$item)
	{
		$items = array();
		$items[] =$item;
		$this->LoadMultiple($items);	
	}
	
	function LoadMultiple(&$items)
	{
		$source = $this->source;
		$ids = array();

		if (!empty($items))
		{
			foreach ($items as &$i)
			{
				if (is_array($i))
				{
					if ($i['id'] > 0)
						$ids[] = $i['id'];
				} else if (is_object($i)) {
					if ($i->id > 0)
						$ids[] = $i->id;
				}
			}
		}
		
		if (count($ids) == 0)
			return;
		
		$db = JFactory::getDBO();
		
		$qry = "SELECT source_id, dest, dest_id, params FROM #__fsj_main_{$this->id} WHERE source = '{$db->escape($this->source)}'";
		if (count($ids) == 1)
		{
			$qry .= " AND source_id = {$ids[0]}";
		} else {
			$qry .= " AND source_id IN (".implode(", ",$ids) . ")";
		}
		
		$qry .= " ORDER BY ordering";
		
		$db->setQuery($qry);
		$data = $db->loadObjectList();
		
		//print_p($data);
		//exit;
		
		$this->plugins = FSJ_Plugin_Handler::GetPlugins($this->id);
		
		$datapp = array();
		foreach($data as &$dataitem)
		{
			$plugin = $dataitem->dest;
			if (!array_key_exists($plugin,$this->plugins))
				continue;
			if (!array_key_exists($plugin,$datapp))
				$datapp[$plugin] = array();
			$datapp[$plugin][] = $dataitem;
		}
		
		foreach ($datapp as $plugin_id => &$lookedupitems)
		{
			//echo "Looking up related $plugin<br>";	
			$plugin = $this->plugins[$plugin_id];
			
			
			if (property_exists($plugin, "params") && property_exists($plugin->params, "display"))
			{
				$type = $plugin->params->display->type;
				
				if ($type == "table")
				{
					$ids = array();
					foreach ($lookedupitems as &$item)
					{
						$ids[] = $item->dest_id;
					}

					$related = $this->DoLookup($plugin, $ids);			
					
					foreach ($lookedupitems as &$item)
					{
						$item->pluginid = $plugin_id;
						$item->ok = 1;
						if (array_key_exists($item->dest_id,$related))
						{
							$item->title = $related[$item->dest_id]->title;
						} else {
							$item->ok = 0;	
						}
					}
					
				} else if ($type == "params") {
					foreach ($lookedupitems as &$item)
					{
						$this->decodeParams($plugin, $item);
						$item->pluginid = $plugin_id;
						$item->ok = 1;
						
					}		
				} else {
					foreach ($lookedupitems as &$item)
					{
						$item->pluginid = $plugin_id;
						$item->ok = 1;
					}
				}
			}
		}
		
		$this->data = array();
		foreach ($data as &$item)
		{
			$this->data[$item->source_id][] = $item;	
		}
		
		foreach ($items as &$i)
		{
			$var = $this->id;
			if (is_object($i))
			{
				if (array_key_exists($i->id, $this->data))
				{
					$i->$var = $this->data[$i->id];
				} else {
					$i->$var = array();	
				}
			}	
		}
	}

	function DoLookup(&$plugin, &$ids)
	{
		$db = JFactory::getDBO();
		
		$qry = "SELECT {$plugin->params->display->data->id} as id, {$plugin->params->display->data->field} as title FROM #__{$plugin->params->display->data->table}";
		
		$where = array();
		
		if (!$this->edit && array_key_exists('use_published', $plugin->params) && $plugin->params['use_published'])
			$where[] = "published = 1";
		if (!$this->edit && array_key_exists('use_state', $plugin->params) && $plugin->params['use_state'])
			$where[] = "state = 1";
		if (!$this->edit && array_key_exists('use_access', $plugin->params) && $plugin->params['use_access'])
		{
			/*$user = FSJUser::getInstance();
			$where[] = $user->AccessFilter();*/
		}
		
		$idsc = count($ids);
		if ($idsc == 0)
			return;
		else if ($idsc == 1)
			$where[] = "{$plugin->params->display->data->id} = {$ids[0]}";
		else
			$where[] = "{$plugin->params->display->data->id} IN (" . implode(", ",$ids) . ")";
		
		$qry .= " WHERE " . implode(" AND ",$where);
		$db->setQuery($qry);

		return $db->loadObjectList('id');
	}
	
	function HasLinked($id)
	{
		if (!array_key_exists($id,$this->data))
			return false;
		
		return true;
	}	
	
	function decodeParams(&$plugin, &$item)
	{	
		if (!property_exists($item, "params"))
			$item->params = array();
		
		if (is_string($item->params))
			$item->params = FSJ_Helper::SplitINIParams($item->params);
		
		// need to parse the params to find the correct shit for the item
		if (property_exists($plugin->params,"output") 
			&& property_exists($plugin->params->output,"type")
			&& $plugin->params->display->type == "params" 
			&& property_exists($plugin->params->output, "data"))
		{
			foreach ($plugin->params->output->data as $field => $value)
			{
				$item->replace[$field] = $value;
				
				$value = trim($value);
				
				if (preg_match_all("/(\{([a-zA-Z]+)\})/", $value, $matched))
				{
					foreach ($matched[0] as $offset => $match)
					{
						$key = $matched[2][$offset];
						
						if (array_key_exists($key, $item->params))
						{
							$value = str_replace($match, $item->params[$key], $value);	
						}
					}	
				}
				
				$item->$field = $value;
			}
		}
	}
	
	function Display($id)
	{
		if (!array_key_exists($id,$this->data))
		{
			return "";
		}
		
		$output = "<h3>" . JText::_("FSJ_LINKED_HEADER_".$this->id) . "</h3>";
		
		$data = $this->data[$id];
		
		//$output .= "<pre>".print_r($data, true)."</pre>";
		
		return $output;
	}
}