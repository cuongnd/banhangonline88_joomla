<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSJ_Data_Helper
{
	static function createSearchFilter($fields, $search, &$model, $fallback = false, $mode = 'any')
	{
		$db = JFactory::getDBO();
		
		// split the phrases out etc and add the query for the search to be done
		$type = FSJ_Settings::Get('search','type');

		//echo "Type : $type, FB : $fallback<br>";

		if ($type != "like" && !$fallback) // full text search on $fields
		{		
			//echo "Using FT<br>";
			// add score as a field so we can sort by it
			$model->fields[] = "MATCH (" . implode(", ", $fields). ") AGAINST ('" . $db->escape($search) . "') AS search_score";			
			
			$model->order = "search_score DESC";
			
			// return filter
			return new FSJ_Model_Filter($fields, $search, 'text');
		} else {	
			//echo "Using Like<br>";
				
			// put newest results first
			$model->order = "modified DESC";
			
			$words = explode(" ", $search);
			
			$parts = array();
			foreach ($words as $word)
			{
				$word = trim($word);
				if (!$word) continue;
					
				$field_bits = array();
					
				foreach ($fields as $field)
				{
					$field_bits[] = "(" . $field . " LIKE '%" . $db->escape($word) . "%' )";
				}
				
				$parts[] = 	"( " . implode(" OR ", $field_bits) . " )";
			}
			
			if (count($parts) < 1)
				return new FSJ_Model_Filter("text_search", " 0 ", "custom");	
			
			return new FSJ_Model_Filter("text_search", " ( " . implode(" AND ", $parts) . " ) ", "custom");	
		}
	}	
	
	static function makeFieldKey($name)
	{
		return preg_replace("/[^A-Za-z0-9]/", '', $name);
	}
	
	static function createWeightedSearchFilter($fields, $search, &$model, $fallback = false, $mode = 'any')
	{
		$db = JFactory::getDBO();
		
		// split the phrases out etc and add the query for the search to be done
		$type = FSJ_Settings::Get('search','type');

		//echo "Type : $type, FB : $fallback<br>";

		if ($type != "like" && !$fallback) // full text search on $fields
		{		
			//echo "Using FT<br>";
			// add score as a field so we can sort by it
			
			$flist = array();
			
			$relevance = array();
			
			foreach ($fields as $fieldname => $weight)
			{	
				$flist[] = $fieldname;
				$field_key = self::makeFieldKey($fieldname);
				$model->fields[] = "MATCH (" . $fieldname . ") AGAINST ('" . $db->escape($search) . "') AS search_score_$field_key";		
				
				$relevance[] = "(search_score_" . $field_key . " * " . $weight . ")";	
			}
			
			$model->order = implode(" + ", $relevance) . " DESC";
			
			// return filter
			return new FSJ_Model_Filter($flist, $search, 'text');
		} else {	
			//echo "Using Like<br>";
				
			// put newest results first
			$model->order = "modified DESC";
			
			$words = explode(" ", $search);
			
			$parts = array();
			foreach ($words as $word)
			{
				$word = trim($word);
				if (!$word) continue;
					
				$field_bits = array();
					
				foreach ($fields as $field => $weight)
				{
					$field_bits[] = "(" . $field . " LIKE '%" . $db->escape($word) . "%' )";
				}
				
				$parts[] = 	"( " . implode(" OR ", $field_bits) . " )";
			}
			
			if (count($parts) < 1)
				return new FSJ_Model_Filter("text_search", " 0 ", "custom");	
			
			return new FSJ_Model_Filter("text_search", " ( " . implode(" AND ", $parts) . " ) ", "custom");	
		}
	}		
}