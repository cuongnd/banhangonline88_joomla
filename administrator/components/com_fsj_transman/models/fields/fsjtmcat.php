<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

class JFormFieldFSJTMCat extends JFormField
{
	static $js_loaded;
	
	protected function getInput()
	{
		return "";	
	}
	
	function AdminDisplay($value, $name, $item)
	{
		$key = str_replace(".ini", "", $item->filename);
		$key = str_replace(".", "_", $item->filename);
		
		echo "<div id='cat_cont_".$key."'>";
		
		echo "<div class='pull-left fsjTip' title='".JText::_('FSJ_TM_CHANGE_CATEGORY')."'><a href='#' item_id='".$key."' raw_id='".$item->id."' class='btn btn-micro  cat_btn' onclick='change_category_btn(this);return false;'><i class='icon-edit'></i></a></div>&nbsp;";

		echo "<span class='hide' id='base_cat_".$key."'>" . $item->base_cat . "</span>";

		if ($item->is_base_cat)
		{
			echo "<span class='item_cat' id='cat_".$key."'><span class='badge badge-info pull-right'>i</span> <span class='muted'>" . $value . "</span></span>";
		} else {
			echo "<span class='item_cat' id='cat_".$key."'>" . $value . "</span>";
		}
		
		echo "</div>";
		
		echo "<div id='cat_wait_".$key."' style='display:none'>". JText::_('FSJ_TM_SAVING'); "</div>";
	}
}
