<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSJ_Plugin_Comments_JComments
{
	function getCommentCounts($item_set, $ids)
	{
		$comments = JPATH_SITE . '/components/com_jcomments/jcomments.php';
		if (file_exists($comments)) {	
			$id_list = array();
	
			foreach ($ids as $id)
				$id_list[] = (int)$id;
			
			if (count($id_list) < 1)
				return array();
			
			$db = JFactory::getDBO();
			
			$qry = "SELECT count(*) as cnt, object_id FROM #__jcomments as c WHERE ";
			
			$where[] = "c.published = 1";
			$where[] = "c.object_group = '" . $db->escape($item_set) . "'";
			
			if (FSJ_Lang_Helper::isEnabled()) {
				$where[] = "c.lang = '" . FSJ_Lang_Helper::getLanguage() . "'";
			}

			$where[] = "c.object_id IN (" . implode(", ", $id_list) . ")";

			$qry .= implode(" AND ", $where);
			$qry .= " GROUP BY c.object_id";
			
			$db->setQuery($qry);
			$data = $db->loadObjectList();
			
			$result = array();
			foreach ($data as $item)
				$result[$item->object_id] = $item->cnt;
			
			return $result;
		}	
		
		return array();
	}
	
	function displayComments($id, $set, $title)
	{
		$comments = JPATH_SITE . '/components/com_jcomments/jcomments.php';
		if (file_exists($comments)) {
			require_once($comments);
			return JComments::showComments($id, $set, $title);
		}
	}
}