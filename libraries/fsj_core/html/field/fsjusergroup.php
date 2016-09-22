<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('fsj_core.html.field.fsjchecklist');

class JFormFieldFSJUsergroup extends JFormFieldFSJChecklist
{
	function getData()
	{
		$db = JFactory::getDBO();
		
		$qry = $db->getQuery(true);
		$qry->select("title as display, id, parent_id");
		$qry->from("#__usergroups");
		$qry->order("lft");
		
		$db->setQuery($qry);
		
		$groups = $db->loadObjectList();
		
		foreach ($groups as &$group)
		{
			if ($group->parent_id == 0)
			{
				$group->level = 0;	
			} else {
				// find parent group
				foreach ($groups as $pargroup)
				{
					if ($pargroup->id == $group->parent_id)
						$group->level = $pargroup->level + 1;	
				}	
				$group->display = str_repeat("<span class='gi'>|&mdash;&thinsp;</span>", $group->level) . $group->display;
			}
		}
		
		return $groups;
		
	}
}
