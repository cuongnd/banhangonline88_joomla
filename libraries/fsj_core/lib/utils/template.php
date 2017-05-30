<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'fsj_core.lib.utils.general');

if (!defined("DS")) define('DS', DIRECTORY_SEPARATOR);
require_once(JPATH_LIBRARIES.DS.'fsj_core'.DS.'third'.DS.'smarty'.DS.'Smarty.class.php');

/** 
 * Template handling code
 **/

class FSJ_Template extends Smarty
{
	private $sections;
	private $fsj_templates;
	private $fsj_unparsed;
	private $fsj_tpl_types;
	private $mtime;
	private $css;
	private $replace_all;
	private $replace_sets;
	private $tpl;
	
	function __construct()
	{
		parent::__construct();	
		
		$this->registerResource('fsjtpl', new Smarty_Resource_FSJTPL($this));
		
		$this->fsj_templates = array();
		$this->fsj_unparsed = array();
		$this->fsj_tpl_types = array();
		
		$this->setCompileDir(JPATH_CACHE . DS . 'fsj' . DS . 'smarty');
	}
	
	function get_template($component, $type, $name)
	{
		return $this->fsj_templates[$component][$type][$name];	
	}
	
	function load_template($component, $type, $name)
	{
		// if we already have this component loaded, return
		//echo "load_template($component, $type, $name)<br>";
		$this->setCompileDir(JPATH_CACHE . DS . 'fsj' . DS . 'smarty' .DS . $component . DS . $type . DS . $name);
	
		$db = JFactory::getDBO();	
		// load in all the templates from array template set from the templates table	
		$qry = "SELECT * FROM #__fsj_tpl_template WHERE component = '" . $db->escape($component) . "' AND type = '" . $db->escape($type) . "' AND ";
		$qry .=  "name = '" . $db->escape($name) . "'";
		
		$db->setQuery($qry);
		
		$tpl = $db->loadObject();
		
		if (!$tpl)
		{
			// no template found, load the default one	
			$qry = "SELECT * FROM #__fsj_tpl_template WHERE component = '" . $db->escape($component) . "' AND type = '" . $db->escape($type) . "' AND ";
			$qry .=  "name = 'default'";
		
			$db->setQuery($qry);
		
			$tpl = $db->loadObject();
		}
		
		$tpl->parsed = "";
		$tpl->params = json_decode($tpl->params, true);
		
		if ($tpl->parsed == "")
		{
			//echo "Parsing Temaplte<br>";
			// process params as we need them to create the parsed template
			
			
			// load in all the unparsed template stuff
			foreach ($tpl->params['tmpl'] as $section => $text)
			{
				$this->fsj_unparsed[$component][$type][$section] = $text;
			}

			// template we are loading requires parsing, so do this now
			//echo "Template requires parsing<br>";
			$this->parse_all_templates($tpl);
			
			$this->mtime = time();
		} else {
			//echo "Tempalte is parsed<br>";
			
			//echo "Template already parsed<br>";
			$tpl->parsed = json_decode($tpl->parsed, true);
			
			// load in all the unparsed template stuff
			foreach ($tpl->parsed as $section => $text)
			{
				$this->fsj_templates[$component][$type][$section] = $text;
			}
		
			$this->mtime = $tpl->updated;
		}
		
		if (isset($tpl->params['css']))
			$this->css[$component][$type] = $tpl->params['css'];

		// load in some base variables
		$this->assign("base_url", JURI::root());
		$this->assign("blank_image", JURI::root() . "images/libraries/fsj_core/assets/images/misc/blank_16.png");

	}
	
	function load_template_type($tpl)
	{
		$db = JFactory::getDBO();
		$qry = "SELECT * FROM #__fsj_tpl_type WHERE component = '" . $db->escape($tpl->component) . "' AND type = '" . $db->escape($tpl->type) . "'";
		$db->setQuery($qry);
		$tpl_type = $db->loadObject();
		
		$this->tpl = $tpl;
		
		if (!$tpl_type)
		{
			echo "Missing template type";
			print_p($tpl);
			exit;	
		}
		
		$file = @simplexml_load_file(JPATH_ROOT.DS.$tpl_type->xmlfile);
		
		if (!$file)
		{
			echo "Error loading " . JPATH_ROOT.DS.$tpl_type->xmlfile . "<br>";
			exit;
		}
		
		$this->fsj_tpl_types[$tpl->component][$tpl->type] = array();
		
		$this->replace_all = array();
		if ($file->replace_all)
		{
			foreach ($file->replace_all->replace as $replace)
			{
				$this->replace_all[(string)$replace->attributes()->name] = (string)$replace;
			}
		}
		
		$this->replace_sets = array();
		if (property_exists($file, "replace_set"))
		{
			foreach ($file->replace_set as $set)
			{
				$id = (string)$set->attributes()->id;
				
				$set_data = array();
				
				foreach ($set->replace as $replace)
				{
					$set_data[(string)$replace->attributes()->name] = (string)$replace;
				}
				
				$this->replace_sets[$id] = $set_data;
			}
		}
		
		if ($file->section)
		{
			foreach ($file->section as $section_xml)
			{
				$this->process_section($section_xml);
			}
		}
		
		if ($file->section_group)
		{
			foreach ($file->section_group as $section_group)
			{
				$this->process_section_group($section_group);
			}
		}
	}
	
	function process_section_group($section_group, $replace_set_parent = array())
	{
		$replace_set = array_merge(array(), $replace_set_parent);
		
		if($section_group->replace_set)
		{
			foreach ($section_group->replace_set as $set)
				$replace_set[] = (string)$set->attributes()->id;
		}
		
		foreach ($section_group->section as $section_xml)
		{
			$this->process_section($section_xml, $replace_set);
		}
		
		if ($section_group->section_group)
		{
			foreach ($section_group->section_group as $section_group)
			{
				$this->process_section_group($section_group, $replace_set);
			}
		}
	}		
	
	function process_section($section_xml, $replace_set = array())
	{
		$section = new stdClass();
		//$section->xml = $section_xml;
		$section->name = (string)$section_xml->attributes()->name;
		$section->title = (string)$section_xml->attributes()->title;
		$section->default = (string)$section_xml->default;
		//$section->default = "<!-- begin.{$section->name} -->\n" . $section->default . "\n<!-- end.{$section->name} -->\n";
		$section->processed = false;
		
		$section->prepend = "";
		$section->append = "";
		if ($section_xml->prepend) $section->prepend = (string)$section_xml->prepend;
		if ($section_xml->append) $section->append = (string)$section_xml->append;			
		
		$section->replaces = array();

		if ($this->replace_all)
		{
			foreach ($this->replace_all as $key => $value)
			{
				$section->replaces[$key] = $value;		
			}
		}
		
		if (property_exists($section_xml, "replace_set"))
		{
			foreach ($section_xml->replace_set as $set)
			{
				$id = (string)$set->attributes()->id;
				
				if (!array_key_exists($id, $this->replace_sets))
				{
					echo "Missing replace set - $id<br>";
					continue;	
				}
				
				foreach ($this->replace_sets[$id] as $key => $value)
					$section->replaces[$key] = $value;
			}
		}
		
		foreach ($replace_set as $set)
		{
			if (!array_key_exists($set, $this->replace_sets))
			{
				echo "Missing replace set - $set<br>";
				continue;	
			}
			
			foreach ($this->replace_sets[$set] as $key => $value)
				$section->replaces[$key] = $value;
		}
		
		if (property_exists($section_xml, "replaces"))
		{
			// check to see if we can load in the replaces from a set
			if (property_exists($section_xml->replaces, "replace"))
			{
				foreach ($section_xml->replaces->replace as $replace_xml)
				{
					/*$replace = new stdClass();
					$replace->name = (string)$replace_xml->attributes()->name;
					$replace->replace = (string)$replace_xml;*/
					
					$name = (string)$replace_xml->attributes()->name; 
					//$section->replaces[$name] = "<!-- replace.begin.$name -->\n" . (string)$replace_xml . "\n<!-- replace.end.$name -->\n";
					$section->replaces[$name] = (string)$replace_xml;
				}
			}
		}
		$this->fsj_tpl_types[$this->tpl->component][$this->tpl->type][$section->name] = $section;

	}
	
	function parse_all_templates($tpl)
	{
		$this->load_template_type($tpl);
		// need to get the parsed data for all of the sections
		
		$tpl->parsed = array();
		
		foreach ($tpl->params['tmpl'] as $name => $text)
		{
			$this->fsj_templates[$tpl->component][$tpl->type][$name] = $this->parse_template($tpl->component, $tpl->type, $name);	
		}
		
		$tpl->parsed = $this->fsj_templates[$tpl->component][$tpl->type];
		
		$parsed = json_encode($tpl->parsed);
		
		$db = JFactory::getDBO();
		$qry = "UPDATE #__fsj_tpl_template SET parsed = '" . $db->escape($parsed) . "', updated = " . $db->escape(time()) . " WHERE component = '" . $db->escape($tpl->component) . "' AND type = '" . $db->escape($tpl->type) . "' AND ";
		$qry .=  "name = '" . $db->escape($tpl->name) . "'";
		
		$db->setQuery($qry);
		$db->Query();
		// need to update the database
	}
	
	function parse_template($component, $type, $name)
	{			
		// check if we have a parsed version of this already available or not
		//echo "Parsing section [{$component}][{$type}][{$name}]<br>";
		if (array_key_exists($component,	$this->fsj_templates) &&
			array_key_exists($type,			$this->fsj_templates[$component]) &&
			array_key_exists($name,			$this->fsj_templates[$component][$type]))
		{
			//echo "Already parsed!";
			return $this->fsj_templates[$component][$type][$name];
		}
		
		$section = $this->fsj_tpl_types[$component][$type][$name];
		
		$text = $this->fsj_unparsed[$component][$type][$name];
		
		if ($section->append)
			$text = $text . $section->append;
		if ($section->prepend)
			$text = $section->prepend . $text;
				
		if (count($section->replaces) > 0)
		{
			foreach ($section->replaces as $name => $replace)
			{	
				if (preg_match_all("/%([a-z0-9A-Z\_\.]+)%/", $replace, $matches))
				{
					foreach ($matches[1] as $match)
					{
						$match_parts = explode(".", $match);
						
						$match_component = $component;
						$match_type = $type;
						
						if (count($match_parts) == 3)
						{
							$match_component = $match_parts[0];
							$match_type = $match_parts[1];
							$match_name = $match_parts[2];
						} else if (count($match_parts) == 2)
						{
							$match_type = $match_parts[0];
							$match_name = $match_parts[1];
						} else {
							$match_name = $match_parts[0];
						}
						
						if ($match_component != $component ||
							$match_type != $type)
						{
							// need to load in the componet/type object
							$this->load_template($match_component, $match_type, "default");
						}

						// get the resulting parsed text of the match we have found!
						$subtmpl = $this->parse_template($match_component, $match_type, $match_name);

						$replace = str_replace("%".$match."%", $subtmpl, $replace);
						
					}
				}
				
				$text = str_replace("{".$name."}", $replace, $text);
			}
		}
		
		$this->fsj_templates[$component][$type][$name] = $text;
		
		return $text;
	}
	
	function GetModTime()
	{
		return $this->mtime;
	}
	
	function OutputCSS($component, $type, $name)
	{
		$css = $this->css[$component][$type];
		
		if (trim($css) != "")
		{
			$css = ".fsjtpl_{$component}_{$type}_{$name} {\n" . $css . "\n}\n";
			
			FSJ_Page::StyleParsed("{$component}_{$type}_{$name}", $css, $this->mtime);
		}
	}
}

class Smarty_Resource_FSJTPL extends Smarty_Resource_Custom {
	
	public function __construct($template) {
		$this->template = $template;
	}
	
	protected function fetch($name, &$source, &$mtime)
	{
		$parts = explode(".", $name);
		
		if (count($parts) == 4)
		{
			$component = $parts[0];
			$type = $parts[1];
			$section = $parts[3];
		} else {
			$component = $parts[0];
			$type = $parts[1];
			$section = $parts[2];
		}
	
		$source = $this->template->get_template($component, $type, $section);
		$mtime = $this->template->GetModTime();
		
		//echo "Mod Time : $mtime<br>";
		//exit;
	}
}
