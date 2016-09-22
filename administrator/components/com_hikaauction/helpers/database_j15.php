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

class hikaauctionDatabaseQueryElement {
	protected $name = null;
	protected $elements = null;
	protected $glue = null;

	public function __construct($name, $elements, $glue = ',') {
		$this->elements = array();
		$this->name = $name;
		$this->glue = $glue;

		$this->append($elements);
	}

	public function __toString() {
		if (substr($this->name, -2) == '()')
			return PHP_EOL . substr($this->name, 0, -2) . '(' . implode($this->glue, $this->elements) . ')';
		return PHP_EOL . $this->name . ' ' . implode($this->glue, $this->elements);
	}

	public function append($elements) {
		if (is_array($elements))
			$this->elements = array_merge($this->elements, $elements);
		else
			$this->elements = array_merge($this->elements, array($elements));
	}

	public function getElements() {
		return $this->elements;
	}

	public function __clone() {
		foreach ($this as $k => $v)	{
			if (is_object($v) || is_array($v))
				$this->{$k} = unserialize(serialize($v));
		}
	}
}

class hikaauctionDatabaseQuery {
	protected $db = null;
	protected $sql = null;
	protected $type = '';
	protected $element = null;
	protected $select = null;
	protected $delete = null;
	protected $update = null;
	protected $insert = null;
	protected $from = null;
	protected $join = null;
	protected $set = null;
	protected $where = null;
	protected $group = null;
	protected $having = null;
	protected $columns = null;
	protected $values = null;
	protected $order = null;
	protected $autoIncrementField = null;
	protected $call = null;
	protected $exec = null;
	protected $union = null;
	protected $unionAll = null;
	protected $offset;
	protected $limit;

	public function __call($method, $args) {
		if (empty($args))
			return;

		switch ($method) {
			case 'q':
				return $this->quote($args[0], isset($args[1]) ? $args[1] : true);
			case 'qn':
				return $this->quoteName($args[0], isset($args[1]) ? $args[1] : null);
			case 'e':
				return $this->escape($args[0], isset($args[1]) ? $args[1] : false);
		}
	}

	public function __construct($db = null) {
		$this->db = $db;
	}

	public function __toString() {
		$query = '';

		if ($this->sql)
			return $this->sql;

		switch ($this->type) {
			case 'element':
				$query .= (string) $this->element;
				break;

			case 'select':
				$query .= (string) $this->select;
				$query .= (string) $this->from;

				if ($this->join) {
					foreach ($this->join as $join) {
						$query .= (string) $join;
					}
				}

				if ($this->where)
					$query .= (string) $this->where;

				if ($this->group)
					$query .= (string) $this->group;

				if ($this->having)
					$query .= (string) $this->having;

				if ($this->order)
					$query .= (string) $this->order;
				break;

			case 'union':
				$query .= (string) $this->union;
				break;

			case 'unionAll':
				$query .= (string) $this->unionAll;
				break;

			case 'delete':
				$query .= (string) $this->delete;
				$query .= (string) $this->from;

				if ($this->join) {
					foreach ($this->join as $join) {
						$query .= (string) $join;
					}
				}

				if ($this->where)
					$query .= (string) $this->where;
				break;

			case 'update':
				$query .= (string) $this->update;

				if ($this->join) {
					foreach ($this->join as $join) {
						$query .= (string) $join;
					}
				}

				$query .= (string) $this->set;

				if ($this->where)
					$query .= (string) $this->where;
				break;

			case 'insert':
				$query .= (string) $this->insert;

				if ($this->set) {
					$query .= (string) $this->set;
				} elseif ($this->values) {
					if ($this->columns)
						$query .= (string) $this->columns;

					$elements = $this->values->getElements();
					if (!($elements[0] instanceof $this))
						$query .= ' VALUES ';
					$query .= (string) $this->values;
				}
				break;

			case 'call':
				$query .= (string) $this->call;
				break;

			case 'exec':
				$query .= (string) $this->exec;
				break;
		}

		{
			$query = $this->processLimit($query, $this->limit, $this->offset);
		}

		return $query;
	}

	public function __get($name) {
		return isset($this->$name) ? $this->$name : null;
	}

	public function call($columns) {
		$this->type = 'call';

		if (is_null($this->call)) {
			$this->call = new hikaauctionDatabaseQueryElement('CALL', $columns);
		} else {
			$this->call->append($columns);
		}
		return $this;
	}

	public function castAsChar($value) {
		return $value;
	}

	public function charLength($field, $operator = null, $condition = null) {
		return 'CHAR_LENGTH(' . $field . ')' . (isset($operator) && isset($condition) ? ' ' . $operator . ' ' . $condition : '');
	}

	public function clear($clause = null) {
		$this->sql = null;

		switch ($clause) {
			case 'select':
				$this->select = null;
				$this->type = null;
				break;
			case 'delete':
				$this->delete = null;
				$this->type = null;
				break;
			case 'update':
				$this->update = null;
				$this->type = null;
				break;
			case 'insert':
				$this->insert = null;
				$this->type = null;
				$this->autoIncrementField = null;
				break;
			case 'from':
				$this->from = null;
				break;
			case 'join':
				$this->join = null;
				break;
			case 'set':
				$this->set = null;
				break;
			case 'where':
				$this->where = null;
				break;
			case 'group':
				$this->group = null;
				break;
			case 'having':
				$this->having = null;
				break;
			case 'order':
				$this->order = null;
				break;
			case 'columns':
				$this->columns = null;
				break;
			case 'values':
				$this->values = null;
				break;
			case 'exec':
				$this->exec = null;
				$this->type = null;
				break;
			case 'call':
				$this->call = null;
				$this->type = null;
				break;
			case 'limit':
				$this->offset = 0;
				$this->limit = 0;
				break;
			case 'union':
				$this->union = null;
				break;
			case 'unionAll':
				$this->unionAll = null;
				break;
			default:
				$this->type = null;
				$this->select = null;
				$this->delete = null;
				$this->update = null;
				$this->insert = null;
				$this->from = null;
				$this->join = null;
				$this->set = null;
				$this->where = null;
				$this->group = null;
				$this->having = null;
				$this->order = null;
				$this->columns = null;
				$this->values = null;
				$this->autoIncrementField = null;
				$this->exec = null;
				$this->call = null;
				$this->union = null;
				$this->unionAll = null;
				$this->offset = 0;
				$this->limit = 0;
				break;
		}
		return $this;
	}

	public function columns($columns) {
		if (is_null($this->columns))
			$this->columns = new hikaauctionDatabaseQueryElement('()', $columns);
		else
			$this->columns->append($columns);
		return $this;
	}

	public function currentTimestamp() {
		return 'CURRENT_TIMESTAMP()';
	}

	public function dateFormat() {
		return $this->db->getDateFormat();
	}

	public function dump() {
		return '<pre class="jdatabasequery">' . str_replace('#__', $this->db->getPrefix(), $this) . '</pre>';
	}

	public function delete($table = null) {
		$this->type = 'delete';
		$this->delete = new hikaauctionDatabaseQueryElement('DELETE', null);

		if (!empty($table))
			$this->from($table);
		return $this;
	}

	public function escape($text, $extra = false) {
		if(HIKAAUCTION_J30)
			return $this->db->escape($text, $extra);
		return $this->db->getEscaped($text, $extra);
	}

	public function exec($columns) {
		$this->type = 'exec';
		if (is_null($this->exec))
			$this->exec = new hikaauctionDatabaseQueryElement('EXEC', $columns);
		else
			$this->exec->append($columns);
		return $this;
	}

	public function from($tables, $subQueryAlias = null) {
		if (is_null($this->from)) {
			if ($tables instanceof $this) {
				if (is_null($subQueryAlias)) {
					throw new RuntimeException('JLIB_DATABASE_ERROR_NULL_SUBQUERY_ALIAS');
				}
				$tables = '( ' . (string) $tables . ' ) AS ' . $this->quoteName($subQueryAlias);
			}
			$this->from = new hikaauctionDatabaseQueryElement('FROM', $tables);
		} else {
			$this->from->append($tables);
		}
		return $this;
	}

	public function year($date) {
		return 'YEAR(' . $date . ')';
	}

	public function month($date) {
		return 'MONTH(' . $date . ')';
	}

	public function day($date) {
		return 'DAY(' . $date . ')';
	}

	public function hour($date) {
		return 'HOUR(' . $date . ')';
	}

	public function minute($date) {
		return 'MINUTE(' . $date . ')';
	}

	public function second($date) {
		return 'SECOND(' . $date . ')';
	}

	public function group($columns) {
		if (is_null($this->group))
			$this->group = new hikaauctionDatabaseQueryElement('GROUP BY', $columns);
		else
			$this->group->append($columns);
		return $this;
	}

	public function having($conditions, $glue = 'AND') {
		if (is_null($this->having)) {
			$glue = strtoupper($glue);
			$this->having = new hikaauctionDatabaseQueryElement('HAVING', $conditions, ' '.$glue.' ');
		}
		else
			$this->having->append($conditions);
		return $this;
	}

	public function innerJoin($condition) {
		$this->join('INNER', $condition);
		return $this;
	}

	public function insert($table, $incrementField=false) {
		$this->type = 'insert';
		$this->insert = new hikaauctionDatabaseQueryElement('INSERT INTO', $table);
		$this->autoIncrementField = $incrementField;
		return $this;
	}

	public function join($type, $conditions) {
		if (is_null($this->join))
			$this->join = array();
		$this->join[] = new hikaauctionDatabaseQueryElement(strtoupper($type) . ' JOIN', $conditions);
		return $this;
	}

	public function leftJoin($condition) {
		$this->join('LEFT', $condition);
		return $this;
	}

	public function length($value) {
		return 'LENGTH(' . $value . ')';
	}

	public function nullDate($quoted = true) {
		$result = $this->db->getNullDate($quoted);
		if ($quoted)
			return $this->db->quote($result);
		return $result;
	}

	public function order($columns) {
		if (is_null($this->order))
			$this->order = new hikaauctionDatabaseQueryElement('ORDER BY', $columns);
		else
			$this->order->append($columns);
		return $this;
	}

	public function outerJoin($condition) {
		$this->join('OUTER', $condition);
		return $this;
	}

	public function quote($text, $escape = true) {
		return $this->db->quote($text, $escape);
	}

	public function quoteName($name, $as = null) {
		if(HIKAAUCTION_J30)
			return $this->db->quoteName($name, $as);
		return $this->db->nameQuote($name, $as);
	}

	public function rightJoin($condition) {
		$this->join('RIGHT', $condition);
		return $this;
	}

	public function select($columns) {
		$this->type = 'select';
		if (is_null($this->select))
			$this->select = new hikaauctionDatabaseQueryElement('SELECT', $columns);
		else
			$this->select->append($columns);
		return $this;
	}

	public function set($conditions, $glue = ',') {
		if (is_null($this->set)) {
			$glue = strtoupper($glue);
			$this->set = new hikaauctionDatabaseQueryElement('SET', $conditions, "\n\$".$glue.' ');
		}
		else
			$this->set->append($conditions);
		return $this;
	}

	public function setQuery($query) {
		$this->sql = $query;
		return $this;
	}

	public function update($table) {
		$this->type = 'update';
		$this->update = new hikaauctionDatabaseQueryElement('UPDATE', $table);
		return $this;
	}


	public function values($values) {
		if (is_null($this->values))
			$this->values = new hikaauctionDatabaseQueryElement('()', $values, '),(');
		else
			$this->values->append($values);
		return $this;
	}

	public function where($conditions, $glue = 'AND') {
		if (is_null($this->where)) {
			$glue = strtoupper($glue);
			$this->where = new hikaauctionDatabaseQueryElement('WHERE', $conditions, ' ' . $glue . ' ');
		} else
			$this->where->append($conditions);
		return $this;
	}

	public function __clone() {
		foreach ($this as $k => $v) {
			if ($k === 'db')
				continue;
			if (is_object($v) || is_array($v))
				$this->$k = unserialize(serialize($v));
		}
	}

	public function union($query, $distinct = false, $glue = '') {
		if (!is_null($this->order))
			$this->clear('order');

		if ($distinct) {
			$name = 'UNION DISTINCT ()';
			$glue = ')' . PHP_EOL . 'UNION DISTINCT (';
		} else {
			$glue = ')' . PHP_EOL . 'UNION (';
			$name = 'UNION ()';
		}

		if (is_null($this->union)) {
			$this->union = new hikaauctionDatabaseQueryElement($name, $query, ''.$glue);
		} else {
			$glue = '';
			$this->union->append($query);
		}

		return $this;
	}

	public function unionDistinct($query, $glue = '') {
		$distinct = true;
		return $this->union($query, $distinct, $glue);
	}

	public function format($format) {
		$query = $this;
		$args = array_slice(func_get_args(), 1);
		array_unshift($args, null);

		$i = 1;
		$func = function ($match) use ($query, $args, &$i) {
			if (isset($match[6]) && $match[6] == '%')
				return '%';

			switch ($match[5]) {
				case 't':
					return $query->currentTimestamp();
				case 'z':
					return $query->nullDate(false);
				case 'Z':
					return $query->nullDate(true);
			}

			$index = is_numeric($match[4]) ? (int) $match[4] : $i++;

			if (!$index || !isset($args[$index]))
				$replacement = '';
			else
				$replacement = $args[$index];

			switch ($match[5]) {
				case 'a':
					return 0 + $replacement;
				case 'e':
					return $query->escape($replacement);
				case 'E':
					return $query->escape($replacement, true);
				case 'n':
					return $query->quoteName($replacement);
				case 'q':
					return $query->quote($replacement);
				case 'Q':
					return $query->quote($replacement, false);
				case 'r':
					return $replacement;
				case 'y':
					return $query->year($query->quote($replacement));
				case 'Y':
					return $query->year($query->quoteName($replacement));
				case 'm':
					return $query->month($query->quote($replacement));
				case 'M':
					return $query->month($query->quoteName($replacement));
				case 'd':
					return $query->day($query->quote($replacement));
				case 'D':
					return $query->day($query->quoteName($replacement));
				case 'h':
					return $query->hour($query->quote($replacement));
				case 'H':
					return $query->hour($query->quoteName($replacement));
				case 'i':
					return $query->minute($query->quote($replacement));
				case 'I':
					return $query->minute($query->quoteName($replacement));
				case 's':
					return $query->second($query->quote($replacement));
				case 'S':
					return $query->second($query->quoteName($replacement));
			}

			return '';
		};

		return preg_replace_callback('#%(((([\d]+)\$)?([aeEnqQryYmMdDhHiIsStzZ]))|(%))#', $func, $format);
	}

	public function dateAdd($date, $interval, $datePart) {
		return trim("DATE_ADD('" . $date . "', INTERVAL " . $interval . ' ' . $datePart . ')');
	}

	public function unionAll($query, $distinct = false, $glue = '') {
		$glue = ')' . PHP_EOL . 'UNION ALL (';
		$name = 'UNION ALL ()';

		if (is_null($this->unionAll)) {
			$this->unionAll = new hikaauctionDatabaseQueryElement($name, $query, "$glue");
		} else {
			$glue = '';
			$this->unionAll->append($query);
		}

		return $this;
	}

	public function processLimit($query, $limit, $offset = 0) {
		if ($limit > 0 || $offset > 0) {
			$query .= ' LIMIT ' . $offset . ', ' . $limit;
		}
		return $query;
	}

	public function concatenate($values, $separator = null) {
		if ($separator) {
			$concat_string = 'CONCAT_WS(' . $this->quote($separator);

			foreach ($values as $value) {
				$concat_string .= ', ' . $value;
			}

			return $concat_string . ')';
		}
		return 'CONCAT(' . implode(',', $values) . ')';
	}

	public function setLimit($limit = 0, $offset = 0) {
		$this->limit  = (int) $limit;
		$this->offset = (int) $offset;
		return $this;
	}
}
