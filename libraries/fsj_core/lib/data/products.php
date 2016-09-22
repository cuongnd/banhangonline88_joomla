<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/**
 * PRODUCT HANDLER - THIS IS NOT USED YET!
 **/

class FSJ_Prods
{
	private $component = '';
	private $for_user = true;
	private $sub_cats = false;
	private $cats_with_prods = true;
	private $published = array(0, 1);
	private $cat_id = 0;
	private $set_id = 0;
	
	function setComponent($component) { $this->component = $component; }
	function setForUser($for_user) { $this->for_user = $for_user; }
	function setCategory($cat_id) { $this->cat_id = $cat_id; }
	function setSet($set_id) { $this->set_id = $set_id; }
	function setIncludeSubCats($sub_cats) { $this->sub_cats = $sub_cats; }
	function setCatsWithProds($cats_with_prods) { $this->cats_with_prods = $cats_with_prods; }
	function setPublished($published) 
	{ 
		if (is_array($published))
		{
			$this->published = $published;	
		} else {
			if ($published == "")
			{
				$this->published = "";	
			} else {
				$this->published = array($published);
			}
		}
	}

	function getCategories()
	{
		// returns a list of product categories within the current cat_id
		
		// filters out categories that have no products within them (optional)	
		// filter out cats the user cannot see (optional)
	}

	function getProducts()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select("*");
		$query->from("#__fsj_main_prod");
		
		if (is_array($this->published))
			$query->where("state IN (" . implode(", ",$this->published) . ")");
		
		if ($this->cat_id) // filter based on cat id
		{
			if ($this->sub_cats)
			{
				// get a list of categories within the current one	
			} else {
				// filter by specified cat only
			}
		}
		
		if ($this->set_id > 0)
			$query->where("set_id = " . $db->escape($this->set_id));
		
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		if ($this->component) // filter based on component
		{
			// go through all items, and replace values with component overrides is needed
			
			foreach ($rows as $offset => &$row)
			{
				$row->produse = json_decode($row->produse,true);
				$row->compparams = json_decode($row->compparams, true);
				
				if (is_array($row->produse) && array_key_exists($this->component, $row->produse) && $row->produse[$this->component] == 0)
				{
					unset($rows[$offset]);
					continue;	
				}
				
				if (!is_array($row->compparams) || !array_key_exists($this->component, $row->compparams))
					continue;
				
				$data = $row->compparams[$this->component];
				
				if ($data['custom_title']) $row->title = $data['title'];
				if ($data['custom_image']) $row->image = $data['image'];
				if ($data['custom_desc']) $row->description = $data['description'];
				if ($data['access'] != -1) $row->access = $data['access'];	
			}
		}
				
		if ($this->for_user) // can only be done once got from db
		{
			// filter based on access level	
		}

		return $rows;
	}
	
	/*function getParamsSection($section, &$data)
	{
		$parts = explode("[",$data);
		foreach($parts as $part)	
		{
			$bits = explode("]",$part);
			if (count($bits) == 0)
				continue;
			if ($bits[0] == $section)
				return $bits[1];
		}
		return "";
	}

	function ConvertParams(&$item, $component)
	{
		$params = FSJ_Prods::getParamsSection($component, $item->params);
		$item->params = FSJ_Prods::SplitINIParams($params);
	}
	
	function GetField(&$item, $field, $component)
	{
		if (!is_array($item->params))
			FSJ_Prods::ConvertParams($item, $component);
			
		if (array_key_exists('custom_' . $field,$item->params) && $item->params['custom_' . $field])
			return $item->params[$field];
		return $item->$field;
	}*/
}