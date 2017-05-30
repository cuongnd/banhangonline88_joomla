<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class JFormFieldFSJCount extends JFormField
{
	protected $type = 'FSJCount';

	static $counts = array();
	
	protected function getInput()
	{
		return "";
	}
	
	function AdminDisplay($value, $name, $item)
	{
		$table = $this->fsjcount->table;
		$field = "set_id";
		if ($this->fsjcount->field)
			$field = $this->fsjcount->field;
		
		if (substr($table,0,3) != "#__")
			$table = "#__fsj_".$table;
		
		if (!array_key_exists($table, self::$counts))
		{
			// load set counts for table
			$qry = "SELECT $field, count(*) as cnt FROM $table";

			$where = array();

			if (isset($this->fsjcount->where))
			{
				$where[] = " {$this->fsjcount->where} ";
			}

			if (isset($this->fsjcount->state))
			{
				$where[] = " state = 1 ";
			}

			if (count($where))
				$qry .= " WHERE " . implode(" AND ", $where);

			$qry .= " GROUP BY $field";
			self::$counts[$table] = array();
			$db = JFactory::getDBO();
			$db->setQuery($qry);
			
			//echo $qry . "<br>";
			$rows = $db->loadObjectList();
			
			foreach ($rows as &$row)
			{
				self::$counts[$table][$row->$field] = $row->cnt;
			}
			//print_p(self::$counts[$table]);
			
		}
		
		if (property_exists($this->fsjcount, "key"))
		{
			$key = $this->fsjcount->key;	
		} else {
			$key = "id";
		}
			
		//echo "Key : {$item->$key}<br>";
		if (array_key_exists($item->$key, self::$counts[$table]))
		{
			$count = self::$counts[$table][$item->$key];
		} else {
			$count = 0;
		}
		
		$display = $count;
		
		$item->count = $count;

		if (isset($this->fsjcount->display))
		{
			$display = $this->fsjcount->display;
			$display = FSJ_Helper::ParseDataFields($display, $item);
		}
		
		if (isset($this->fsjcount->icon))
		{
			$display = "<i class='icon-" . $this->fsjcount->icon . "'></i> " . $display;
		}

		if (isset($this->fsjcount->target))
		{
			$link = $this->fsjcount->target;
			$link = FSJ_Helper::ParseDataFields($link, $item);
			$link = JRoute::_($link);
			
			if (isset($this->fsjcount->class))
			{
				return "<a href='$link' class='{$this->fsjcount->class}'>$display</a>";	
			} else {
				return "<a href='$link'>$display</a>";	
			}
		} else {
			return $display;	
		}
	}
}
