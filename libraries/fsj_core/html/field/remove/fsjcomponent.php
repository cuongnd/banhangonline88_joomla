<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('fsj_core.html.field.fsjchecklist');

class JFormFieldFSJComponent extends JFormFieldFSJChecklist
{
	function getData()
	{
		$db = JFactory::getDBO();
		
		$qry = $db->getQuery(true);
		$qry->select("name as display, name as id");
		$qry->from("#__extensions");
		$qry->where("type = 'component'");
		
		$db->setQuery($qry);
		
		return $db->loadObjectList();
	}
}
