<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

if(!class_exists('hikaauctionDatabaseQuery')) {
	if(!HIKAAUCTION_J25) {
		include_once('database_j15.php');
	} else {
		class hikaauctionDatabaseQuery extends jDatabaseQuery {}
	}
}

class hikaauctionDatabaseHelper {

	public $db = null;

	public function __construct() {
		$this->db = JFactory::getDBO();
	}

	public function &get() {
		return $this->db;
	}

	public function getQuery($new = false) {
		if($new && !HIKAAUCTION_J25)
			return new hikaauctionDatabaseQuery($this->db);
		return $this->db->getQuery($new);
	}

	public function quote($elem) {
		if(is_array($elem)) {
			$ret = array();
			foreach($elem as $e) {
				$ret[] = $this->db->quote($e);
			}
			return $ret;
		}
		return $this->db->quote($elem);
	}

	public function implode($elements, $glue = ',') {
		if(is_array($elements)) {
			$ret = array();
			foreach($elements as $e) {
				if(is_int($e))
					$ret[] = $e;
				else
					$ret[] = $this->db->quote($e);
			}
			return implode($glue, $ret);
		}
		return $this->db->quote($elements);
	}

	public function insert($table, $data) {
		if(substr($table, 0, 1) != '#')
			$table = hikaauction::table($table);

		$query = $this->getQuery(true);
		$query->insert($table)
			->columns($query->quoteName(array_keys($data)))
			->values($this->implode($data));
		$this->db->setQuery($query);
		$ret = $this->db->query();

		return $ret;
	}

	public function replace($table, $key, $columns, $values) {
		$query = $this->getQuery(true);
		$query->delete($table)->where($key.' IN ('.$this->implode(array_keys($values)).')');
		$this->db->setQuery($query);
		$this->db->query();

		$query = $this->getQuery(true);
		$query->insert($table)->columns( $query->quoteName($columns) );
		foreach($values as $v) {
			$query->values($this->implode($v));
		}
		$this->db->setQuery($query);
		$this->db->query();
	}
}
