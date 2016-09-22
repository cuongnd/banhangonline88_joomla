<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/**
 * nested view class for handling sets -> cats -> items
 **/

jimport('fsj_core.lib.mvc.view');

class FSJViewNested extends FSJView
{
	var $using_set = true;
	var $show_children = true;
	
	var $cat_model_name;
	var $set_model;
	var $cat_model;
	
	var $cat;
	var $cats = array();
	var $items = array();
	
	function init()
	{
		parent::init();
		
		if ($this->cat_model_name && !$this->cat_model)
		{
			$this->cat_model = $this->controller->getModel($this->cat_model_name);
			$this->setModel($this->cat_model, false);
		}
	}
	
	function LoadData()
	{
		$cats_filter = array();
		
		if ($this->using_set)
			$cats_filter['set_id'] = new FSJ_Model_Filter("set_id", $this->set_id);
		
		if ($this->cat_id)
		{
			$this->cat = $this->cat_model->getItems( array('cat_id' => new FSJ_Model_Filter("id", $this->cat_id)),	true);
			
			if (!$this->cat)
				return $this->Error_404("FAQ Category " . $this->cat_id ." Not Found");
			
			if ($this->using_set && !$this->set_id)
			{
				$this->set_id = $this->cat->set_id;
				$cats_filter['set_id'] = new FSJ_Model_Filter("set_id", $this->set_id);
			}
				
			if ($this->show_children)
			{
				// doing nested, so filter by lft and rgt
				$cats_filter[] = new FSJ_Model_Filter('lft', $this->cat->lft, ">");
				$cats_filter[] = new FSJ_Model_Filter('rgt', $this->cat->rgt, "<");
			} else {
				// if we arent doing nested cats, then filter by parent_id
				$cats_filter[] = new FSJ_Model_Filter('parent_id', $this->cat->id);
			}
		} else {
			if (!$this->show_children)
			{
				// not nested, limit to first level of categories	
				$cats_filter[] = new FSJ_Model_Filter('level', 1);
			}	
		}
	
		if ($this->set_id)
		{
			if (!$this->set_model)
				$this->set_model = $this->controller->getModel("set");
			$this->set = $this->set_model->getItems( array('set_id' => new FSJ_Model_Filter("id", $this->set_id)),	true);
			
			if (!$this->set)
				return $this->Error_404("FAQ Set " . $this->set_id . " Not Found");
		}

		$this->cats = $this->cat_model->getItems($cats_filter);
		
		// build a list of all cat ids we have loaded
		$cat_ids = array();
		if ($this->cat)
			$cat_ids[] = $this->cat->id;
		
		// if we are showing nested cats add faqs for those too
		if ($this->show_children && $this->cats)
			foreach ($this->cats as $cat)
				$cat_ids[] = $cat->id;
		
		$items_filter = array();
		$items_filter[] = new FSJ_Model_Filter('cat_id', $cat_ids, "in");
		
		if (isset($this->run_search))
			$this->addSearch($items_filter, $this->search);
		
		$this->items = $this->item_model->getItems($items_filter);	
	}
	
	function DoNesting()
	{
		// move all of the sub cats into their parent cats, and all non root items into their respective cats
		$cur_level = 1;
		$cur_cat = 0;
		if ($this->cat)
		{
			$cur_level = $this->cat->level + 1;
			$cur_cat = $this->cat->id;
		}		
		
		$cat_index = array();
		
		if ($this->cats)
		{
			foreach ($this->cats as $offset => $cat)
			{
				$cat_index[$cat->id] = $offset;
				
				if ($cat->level == $cur_level) continue;
				
				foreach ($this->cats as $parcat)
				{
					if ($parcat->id != $cat->parent_id)
						continue;
					
					if (!isset($parcat->sub_cats)) $parcat->sub_cats = array();	
					
					$parcat->sub_cats[] = $cat;
				}
			}
		}
		
		if (count($this->items) > 0)
		{
			foreach ($this->items as $offset => $item)
			{
				if ($item->cat_id == $cur_cat) continue;
				
				$cat_offset = $cat_index[$item->cat_id];
				
				if (!isset($this->cats[$cat_offset]->items))
					$this->cats[$cat_offset]->items = array();
				
				$this->cats[$cat_offset]->items[] = $item;
				
				unset($this->items[$offset]);
			}
		}
		
		if ($this->cats)
		{
			foreach ($this->cats as $offset => $cat)
			{
				if ($cat->level == $cur_level) continue;
				
				unset($this->cats[$offset]);
			}
		}
	}
}