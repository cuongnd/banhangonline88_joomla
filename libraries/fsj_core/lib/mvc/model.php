<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/**
 * Not sure what this is for, seems only to be used for faqs at the moment
 **/

class FSJModel extends JModelLegacy
{
	var $table = "";

	var $opt_state = 1;
	var $opt_language = 1;
	
	var $opt_join_cat = null;
	var $opt_join_set = null;
	var $opt_join_author = false;
	var $opt_filter_pubdates = false;
	
	var $order = 'ordering';
	
	var $msg_not_found = "NOT FOUND MESSAGE GOES HERE";
	
	var $fields = array('*');
	
	var $limit = 0;
	var $offset = 0;
	
	protected function BuildKey($params)
	{
		return crc32(json_encode($params));	
	}
	
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// this needs imporving
		if ($this->opt_state)
		{
			$user = JFactory::getUser();
			if ((!$user->authorise('core.edit.state', 'com_fsj_faqs')) && (!$user->authorise('core.edit', 'com_fsj_faqs')))
			{
				$this->setState('filter.published', 1);
				$this->setState('filter.archived', 2);
			}
		}

		if ($this->opt_language)
			$this->setState('filter.language', JLanguageMultilang::isEnabled());
	}
	
	
	public function getItems($filter = array(), $single = false)
	{
		$key = $this->BuildKey($filter);
		
		if (isset($this->_items[$key]))
		{
			return $this->_items[$key];
		}
		
		try
		{
			$db = $this->getDbo();
			
			$query = $db->getQuery(true);
			
			$query->select(implode(", ", $this->fields));
			
			$query->from($this->table . " as a");
			
			if ($this->opt_join_cat)
			{
				$query->select('c.title AS category_title, c.alias AS category_alias, c.access AS category_access')
					->join('LEFT', $this->opt_join_cat . ' AS c on c.id = a.cat_id');	
			}
			
			if ($this->opt_join_set)
			{
				$query->select('c.set_id, s.title AS set_title, s.alias AS set_alias, s.access AS set_access')
					->join('LEFT', $this->opt_join_set . ' AS s on s.id = c.set_id');
			}	
			
			if ($this->opt_join_author)
			{
				$query->select('u.name AS author')
					->join('LEFT', '#__users AS u on u.id = a.created_by');
			}
		
			foreach ($filter as $filter_field)
			{
				$filter_field->AddToQuery($query);	
			}

			// filter state
			if ($this->opt_state && is_numeric($this->getState('filter.published')))
			{
				$query->where('(a.state = ' . (int) $this->getState('filter.published') . ' OR a.state =' . (int) $this->getState('filter.archived') . ')');
			} else {
				$query->where('a.state != -2');
			}
			
			// filter language
			if ($this->opt_language && $this->getState('filter.language'))
			{
				$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
				
				if ($this->opt_join_cat)
					$query->where('c.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
				
				if ($this->opt_join_set)
					$query->where('s.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
			}
			
			// access levels
			$query->where('a.access IN (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')');
			
			if ($this->opt_join_cat)
				$query->where('c.access IN (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')');
			
			if ($this->opt_join_set)
				$query->where('s.access IN (' . implode(',', JFactory::getUser()->getAuthorisedViewLevels()) . ')');
			
			/*if ($this->opt_filter_pubdates)
			{
				$nullDate = $db->getNullDate();
				$nowDate = JFactory::getDate()->toSql();
				$query->where('(a.publish_up = "' . $nullDate . '" OR a.publish_up <= "' . $nowDate . '")')
					->where('(a.publish_down = "' . $nullDate . '" OR a.publish_down >= "' . $nowDate . '")');	
			}*/
			
			$this->getItemsSQL($query);
			
			// setup ordering						
			//if (!$this->order) $this->order = $this->nested_input ? 'lft' : 'ordering';
			$query->order($this->order);

			//echo "Qry : " . str_replace("#__", JFactory::getApplication()->getCfg('dbprefix'), $query) . "<br>";

			$db->setQuery($query, $this->offset, $this->limit);
			
			//echo "Query : $query<br>";
			
			$data = $db->loadObjectList();

			if (!empty($data))
			{
				$this->parseMetaData($data);
				$this->postLoadItems($data);
			}
			
			$this->_items[$key] = $data;
		}
		catch (Exception $e)
		{
			if ($e->getCode() == 404)
			{
				// Need to go thru the error handler to allow Redirect to work.
				JError::raiseError(404, $e->getMessage());
			}
			else
			{
				$this->setError($e);
				$this->_items[$key] = false;
			}
		}	
		
		if (!is_array($this->_items[$key]) || count($this->_items[$key]) == 0)
			return null;
		
		if ($single)
			return $this->_items[$key][0];

		return $this->_items[$key];
	}
	
	function getItemsSQL($query)
	{
		// override me!
	}

	function parseMetaData(&$data)
	{
		foreach ($data as $item)
		{
			if (isset($item->metadata) && $item->metadata)
				$item->metadata = json_decode($item->metadata);	
		}
	}

	function postLoadItems(&$data)
	{
		// override me in model class
	}

	public function hit($pk)
	{
		$input = JFactory::getApplication()->input;
		$hitcount = $input->getInt('hitcount', 1);

		if ($hitcount)
		{
			$db = $this->getDbo();

			$db->setQuery(

				'UPDATE ' . $this->table . 
				' SET hits = hits + 1' .
				' WHERE id = ' . (int) $pk
				);

			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				return false;
			}
		}
		return true;
	}
	
	public function lookupAlias($alias)
	{
		$db = $this->getDbo();

		$db->setQuery("SELECT id FROM " . $this->table . " WHERE alias = '" . $db->escape($alias) . "'");
		$item = $db->loadObject();
		
		if ($item)
			return $item->id;
		
		return null;
	}	
	
	public function findDefault()
	{
		$db = $this->getDbo();

		$db->setQuery("SELECT id FROM " . $this->table . " WHERE home = 1");
		$item = $db->loadObject();
		
		if ($item)
			return $item->id;
		
		return null;
	}
}

class FSJ_Model_Filter
{
	var $field;
	var $value;
	var $operator;
	
	function __construct($field, $value, $operator = "=")
	{
		$this->field = $field;
		$this->value = $value;
		$this->operator = $operator;
	}	
	
	function NotNull()
	{
		if ($this->value == "")
			return false;
		
		return true;	
	}	
	
	function getValue()
	{
		return $this->value;	
	}
	
	function AddToQuery($query, $table_alias = 'a')
	{
		$field_name = $this->field;

		if (is_string($field_name) && strpos($field_name, ".") === FALSE)
			$field_name = $table_alias . "." . $this->field;
		
		if ($this->operator == "=" || 
			$this->operator == "<" || $this->operator == ">" || 
			$this->operator == "<=" || $this->operator == ">=")
		{
			if (!is_numeric($this->value))
			{
				$value = "'" . $query->escape($this->value) . "'";
			} else {
				$value = $query->escape($this->value);	
			}
			
			return $query->where($field_name . " " . $this->operator . " " . $value);
		} elseif ($this->operator == "in")
		{
			$values = array();
			if (is_array($this->value))
			{
				foreach ($this->value as $value)
				{
					if (!is_numeric($value))
					{
						$values[] = "'" . $query->escape($value) . "'";
					} else {
						$values[] = $query->escape($value);	
					}
				}
			} else if (is_numeric($this->value))
			{
				$values[] = $query->escape($this->value);	
			}
			
			if (count($values) == 0)
				$values[] = "0";			
			
			return $query->where($field_name . " IN (" . implode(", ", $values) . ")");
		} elseif ($this->operator == "text")
		{
			return $query->where("MATCH (" . implode(", ", $field_name). ") AGAINST ('" . $query->escape($this->value) . "')");	
		} elseif ($this->operator == "custom")
		{
			return $query->where($this->value);
		}
	}
}