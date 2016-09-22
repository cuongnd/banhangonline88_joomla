<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport("fsj_core.plugins.linked.linked_edit");

class FSJ_Plugin_Type_Related extends FSJ_Linked_Edit
{
	var $id = "related";
	var $addtext = "FSJ_RELATED_ADD_RELATED_ITEM";
	var $addbtntext = 'FSJ_RELATED_ADD_RELATED';
}