<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('fsj_core.html.field.fsjchecklist');

class JFormFieldFSJMenuItem extends JFormFieldFSJChecklist
{
	function getData()
	{
		$db = JFactory::getDBO();
		
		$qry = $db->getQuery(true);
		$qry->select("title as display, id, parent_id, level");
		$qry->from("#__menu");
		$qry->order("menutype, lft");
		$qry->where("level > 0");
		$qry->where("menutype != 'menu'");
		$qry->where("published = 1");

		$db->setQuery($qry);
		
		$items = $db->loadObjectList();
		
		
		foreach ($items as &$group)
		{
			$group->display = str_repeat("<span class='gi'>|&mdash;&thinsp;</span>", $group->level - 1) . $group->display;
		}
		
		return $items;
		
	}
}
