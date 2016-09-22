<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport("fsj_core.lib.utils.plugin_handler");
jimport("fsj_core.plugins.linked.linked");

class FSJ_Linked_Edit extends FSJ_Linked
{
	// need to fill in the following for derived classes
	var $id;
	var $addtext;
	var $addbtntext;
	// end derived options
	
	var $data = array();
	var $edit = false;
	
	function __construct($source, $edit = false)
	{
		parent::__construct($source, $this->id);
		
		$this->edit = $edit;
				
		$document = JFactory::getDocument();

		FSJ_Page::Style("libraries/fsj_core/assets/css/plugin.{$this->id}.less");

		if ($this->edit)
		{
			// need to make sure that the main js file is included here!
			$jsfile = JPATH_SITE.DS.'libraries'.DS.'fsj_core'.DS.'assets'.DS.'js'.DS.'plugin'.DS.'plugin.linked.js';
			echo "<script>";
			include $jsfile;
			echo "</script>";
		}
	}

	function Display($id,$template = "")
	{
		if ($template == "")
			$template = "{$this->id}|{$this->id}";
			
		if (!array_key_exists($id,$this->data))
		{
			return "";
		}

		$this->item_id = $id;
		
		$tmplfile = FSJ_Template::GetFile($template);

		require $tmplfile;
	}
	
	function decodeParams(&$plugin, &$item)
	{	
		if (!property_exists($item, "params"))
			$item->params = array();
		
		if (is_string($item->params))
			$item->params = FSJ_Helper::SplitINIParams($item->params);
		
		// need to parse the params to find the correct shit for the item
		if (property_exists($plugin->params,"display") 
			&& property_exists($plugin->params->display,"type")
			&& $plugin->params->display->type == "params" 
			&& property_exists($plugin->params->display, "data"))
		{
			foreach ($plugin->params->display->data as $field => $value)
			{
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
		
	function ItemDisplay($id)
	{
		foreach ($this->data[$id] as &$item)
		{
			if (!$item->ok) continue;
			$plugin = $this->plugins[$item->pluginid];
			
			// sort link out
			// this may have problems with kb stuff if no prod is selected, but who knows!
			if (array_key_exists('link',$plugin->params))
			{
				$link = trim($plugin->params['link']);
				$link = str_replace("{id}",$item->dest_id,$link);
				if (strpos($link,"{") > 0)
				{
					foreach ($_GET as $var => $value)
					{
						$link = str_replace("{".$var."}",$value,$link);	
					}	
				}
				if (strpos($link,"{") > 0)
				{
					// need to remove any remaining {xxx} stuff from the link!
				}
				$link = FSJ_Route::_($link);
			}
			
			// sort image src
			$image = $plugin->params['image'];
			
			$item->params = FSJ_Helper::SplitINIParams($item->params);
			
			$template = "{$this->id}_items|{$this->id}.{$this->id}";
			if (array_key_exists('template',$plugin->params))
				$template = "{$this->id}_items|{$this->id}.{$plugin->params['template']}";
			$tplfile = FSJ_Template::GetFile($template);
			
			include $tplfile;
		}	
	}
	
	function ShowForm($id)
	{
		/*$document = JFactory::getDocument();
		$document->addScript( JURI::root() .'libraries/fsj_core/assets/js/jquery.sortable.js');
		$document->addScript( JURI::root() .'libraries/fsj_core/assets/js/' . $this->id . '.js');*/
		
		echo "<div class='fsj fsj_linked fsj_{$this->id}'>";
		
		$html = array();
		//$html[] = '<div id="fsj_related_add" class="fsj fsj_related">';
		$html[] = '<a href="#" class="btn" id="fsj_'.$this->id.'_add">';
		$html[] = '<img src="' . JURI::root() .'libraries/fsj_core/assets/images/general/add-16.png">';
		$html[] = '<span>' . JText::_('FSJ_RELATED_ADD_RELATED_ITEM') . '</span>';
		$html[] = '</a>';//</a></div>';

		echo implode($html);
		
		$values = array();
		if (array_key_exists($id,$this->data))
		{
			foreach ($this->data[$id] as &$item)
			{
				$values[] = "{$item->dest}={$item->dest_id}";	
			}
		} 
		
		$value = implode("&",$values);
		
		echo "<input type='hidden' id='fsj_{$this->id}_values' name='{$this->id}' value='$value' size=80>";
?>
<script>
var fsj_<?php echo $this->id; ?>_url = '<?php echo JRoute::_("index.php?option=com_fsj_main&tmpl=component&task={$this->id}.add&source=" . $this->source . "&source_id=" . $id, false); ?>';
var fsj_<?php echo $this->id; ?>_lookup_url = '<?php echo JRoute::_("index.php?option=com_fsj_main&tmpl=component&task={$this->id}.lookup", false); ?>';
var fsj_<?php echo $this->id; ?>_param_url = '<?php echo JRoute::_("index.php?option=com_fsj_main&tmpl=component&task={$this->id}.param", false); ?>';
</script>
	<div class="fsj_clear"></div>
	<div id="fsj_<?php echo $this->id; ?>_items" class="dragsort">
<?php
		if (array_key_exists($id, $this->data))
		{
			foreach ($this->data[$id] as &$item)
			{
				if (!$item->ok) continue;
				$plugin = $this->plugins[$item->pluginid];
				
				if (isset($plugin->params->image))
				{
					$image = JURI::root() . $plugin->params->image;
				} else {
					$image = "";	
				}
			
				$this->decodeParams($plugin, $item);		
				$this->inludePluginEdit($plugin, $item);	
			}
		}
?>
	</div></div><div style="clear: both;"></div>
<?php
	}
	
	function inludePluginEdit($plugin, $item)
	{
		$plugintype = FSJ_Plugin_Handler::GetPluginType($plugin->type);
		$file1 = JPATH_ROOT.DS.$plugin->path.DS."tmpl_edit".DS.$plugin->name.".edit.php";
		$file2 = JPATH_ROOT.DS.$plugintype->path.DS."tmpl_edit".DS.$plugin->type.".edit.php";
		if (file_exists($file1))
		{
			include $file1;
		} else if (file_exists($file2))
		{
			include $file2;
		} else {
			echo "File 1 : $file1<br>";
			echo "File 2 : $file2<br>";
			print_p($item);
			print_p($plugin);
			print_p($plugintype);
		}
		
	}
	
	function add()
	{
		$this->plugins = FSJ_Plugin_Handler::GetPlugins($this->id);

		$mainframe = JFactory::getApplication();
		$pluginid = $mainframe->getUserStateFromRequest($this->id.'.type', 'pluginid', '', 'string');
		if ($pluginid == "")
		{
			foreach ($this->plugins as $id => &$plugin)		
			{
				$pluginid = $id;
				break;
			}
		}
	
		$lang = JFactory::getLanguage();
		$lang->load('com_fsj_main');		
			
		$this->pluginid = $pluginid;
			
		// build select box for plugin type
		$pluginlist = array();
		foreach ($this->plugins as $id => &$plugin)		
		{
			if (isset($plugin->params->pick->type) && $plugin->params->pick->type == "none") continue;
			$pluginlist[] = JHTML::_('select.option', $id, JText::_($plugin->title), 'id', 'title');
		}
		$this->pluginselect = JHTML::_('select.genericlist',  $pluginlist, 'pluginid', 'class="inputbox" size="1" id="pluginid"', 'id', 'title', $pluginid);
			
		$this->plugin = $this->plugins[$pluginid];
			
		$document = JFactory::getDocument();
		$this->pluginselect_tabs = (count($this->plugins) < 8);
				
		jimport("fsj_core.plugins.picktype.picktype");
			
		$this->pick = FSJ_Plugin_Type_PickType::GetInstance($this->plugin->params->pick->type);
		$this->pick->id = $this->id;
		$this->pick->source = $this->source;
		$this->pick->addbtntext = $this->addbtntext;
			
		$this->pick->InitFromLinked($this->plugin);
	
		include JPATH_LIBRARIES.DS.'fsj_core'.DS.'tmpl'.DS.'linked'.DS.'choose_popup.php';
			
		return true;
	}
	
	function lookup()
	{
		$this->plugins = FSJ_Plugin_Handler::GetPlugins($this->id);

		$ids = JRequest::getVar('ids');
		$pluginid = JRequest::getVar('pluginid');
		$plugin = $this->plugins[$pluginid];
		$ids = explode(",",$ids);
		$data = $this->DoLookup($plugin, $ids);
		foreach ($data as &$item)
		{
			$item->pluginid = $pluginid;
			$item->dest_id = $item->id;
			$image = JURI::root() . $plugin->params->image;
								
			ob_clean();
			//print_p($item);
				
			$this->decodeParams($plugin, $item);
			$this->inludePluginEdit($plugin, $item);

			$item->html = ob_get_contents();
		}
			
		ob_clean();
			
		echo json_encode($data);
		exit;	
	}
	
	function param()
	{
		$this->plugins = FSJ_Plugin_Handler::GetPlugins($this->id);

		$pluginid = JRequest::getVar('pluginid');
		$plugin = $this->plugins[$pluginid];

		$item = new stdClass();
		mt_srand(time());
		$item->dest_id = mt_rand(0,10000000);
		$image = JURI::root() . $plugin->params->image;
		$params = json_decode(JRequest::getVar('params'));
		$item->params = array();
		$parstr = "";
		foreach($params as $var => $val)
		{
			$item->params[$var] = $val;
		}
		$item->pluginid = $pluginid;
			
		// Call OnAdd for the plugin if it exists
		$plugin_class = "FSJ_Plugin_{$this->id}_{$pluginid}";
		$plugin_file = JPATH_ROOT.DS.$plugin->path.DS."{$this->id}.{$pluginid}.php";
			
		if (file_exists($plugin_file))
			require_once($plugin_file);
			

		if (class_exists($plugin_class))
		{
			$plugin_obj = new $plugin_class();
				
			if (method_exists($plugin_obj, "OnAdd"))
				$plugin_obj->OnAdd($item);
		}
			
		$this->decodeParams($plugin, $item);

		ob_clean();
		$this->inludePluginEdit($plugin, $item);
		$item->html = ob_get_contents();
		ob_clean();
			
		echo json_encode($item);
		exit;	
	}
	
	/*function Process()
	{		
		// add item popup
		if (JRequest::getVar('task') == $this->id."_add")
		{
			return $this->add();			
			
		// lookup the added item and return the html for the edit form
		} else if (JRequest::getVar('task') == $this->id."_lookup")
		{
			return $this->lookup();	
			
		// WHAT DOES THIS DO?
		} else if (JRequest::getVar('task') == $this->id."_param")
		{
			return $this->param();			
		}
		
		return false;
	}*/

	function Save($source,$source_id)
	{
		// need to load form data, and save with id and source	
	
		$db = JFactory::getDBO();
		
		$qry = "DELETE FROM #__fsj_main_{$this->id} WHERE source = '{$db->escape($source)}' AND source_id = {$db->escape($source_id)}";
		$db->setQuery($qry);
		$db->Query();
		
		$main = JRequest::getVar($this->id);
		$parts = explode("&",$main);
		$qry = "REPLACE INTO #__fsj_main_{$this->id} (source, source_id, dest, dest_id, params, ordering) VALUES \n";
		
		$results = array();
		
		$order = 1;

		foreach ($parts as $item)
		{
			if (trim($item) == "") continue;
			$bits = explode("=",$item,2);
			if (count($bits) != 2)
				continue;
			$dest = $bits[0];
			$dest_id = $bits[1];
				
			$params = JRequest::getVar("{$this->id}_params_{$dest}_{$dest_id}","");			
			$title = JRequest::getVar("{$this->id}_title_{$dest}_{$dest_id}");
			if ($title)
				$params .= "\ntitle=$title";
			
			$results[] = "('{$db->escape($source)}',{$db->escape($source_id)},'{$db->escape($dest)}',{$db->escape($dest_id)},'{$db->escape($params)}', $order)";
			
			$order++;
		}
		
		if (count($results) > 0)
		{
			$qry .= implode(",\n ",$results);
			$db->setQuery($qry);
			$db->Query();
		}
	}
	
	function Delete($source_id)
	{
		$db = JFactory::getDBO();
		$qry = "DELETE FROM #__fsj_main_{$this->id} WHERE source = '{$db->escape($this->source)}' AND source_id = {$db->escape($source_id)}";
		//echo $qry."<br>";
		$db->setQuery($qry);
		$db->Query();
	}
}

class FSJ_Linked_Plugin extends FSJ_Plugin
{
	
}