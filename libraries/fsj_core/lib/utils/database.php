<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/**
 * Database simplification functions
 **/
if (!class_exists("FSJ_Database"))
{
	class FSJ_Database
	{
		static function Replace($table, $data)
		{		
			$db	= JFactory::getDBO();		
			
			$flist = array();
			$vlist = array();
			
			foreach ($data as $field => $value)
			{
				$flist[] = "`".$field."`";
				$vlist[] = "'".$db->escape($value)."'";
			}
			
			$qry = "REPLACE INTO $table (" . implode(" ,", $flist) . ") VALUES (" . implode(" ,", $vlist) . ")";
			
			$db->setQuery($qry);
			$db->Query();
		}	
		
		static function Insert($table, $data)
		{		
			$db	= JFactory::getDBO();		
			
			$flist = array();
			$vlist = array();
			
			foreach ($data as $field => $value)
			{
				$flist[] = "`".$field."`";
				$vlist[] = "'".$db->escape($value)."'";
			}
			
			$qry = "INSERT INTO $table (" . implode(" ,", $flist) . ") VALUES (" . implode(" ,", $vlist) . ")";
			
			$db->setQuery($qry);
			$db->Query();
		}	
		
		static function Update($table, $keys, $data)
		{
			$db	= JFactory::getDBO();		

			$where = array();
			$set = array();
			
			foreach ($keys as $key)
			{
				$where[] = "$key = '" . $db->escape($data[$key]) . "'";
				unset($data[$key]);
			}
			
			foreach ($data as $key => $value)
			{
				$set[] = "$key = '" . $db->escape($value) . "'";
			}
			
			$qry = "UPDATE $table SET " . implode(", ", $set) . " WHERE " . implode(" AND ", $where);
			
			$db->setQuery($qry);
			$db->Query();
		}
	}
}