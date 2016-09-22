<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class hikaserialHistoryClass extends hikaserialClass {
	protected $tables = array('history');
	protected $pkeys = array('history_id');

	public function save(&$element) {
		if(!is_array($element)) {
			return parent::save($element);
		}

		$struct = array();
		$first = reset($element);
		$query = 'INSERT IGNORE INTO '.hikaserial::table('history').' (';
		$sep = '';
		foreach (get_object_vars($first) as $k => $v) {
			if(is_array($v) || is_object($v) || $v === null || $k[0] == '_' ) {
				continue;
			}
			if(!HIKASHOP_J30){
				$query .= $sep.$this->db->nameQuote($k);
			} else {
				$query .= $sep.$this->db->quoteName($k);
			}
			$struct[] = $k;
			$sep = ',';
		}
		$query .= ') VALUES ';
		$sep = '';
		foreach($element as $el) {
			if($el === false)
				continue;
			$query .= $sep.'(';
			$sep2 = '';
			foreach($struct as $k) {
				$value = $el->$k;
				if(!HIKASHOP_J25) {
					$query .= $sep2 . ($this->db->isQuoted($k) ? $this->db->Quote($value) : (int)$value);
				} else {
					$query .= $sep2 . $this->db->Quote($value);
				}
				$sep2 = ',';
			}
			$query .= ')';
			$sep = ',';
		}
		$this->db->setQuery($query);
		$this->db->query();
		return true;
	}

	public function &generate($id, &$serial, $newSerial = false) {
		if(empty($id))
			$id = @$serial->serial_id;
		if(empty($id))
			return false;

		$history = new stdClass();
		$history->history_serial_id = $id;
		$history->history_created = time();
		$history->history_ip = hikaserial::getIP();
		$history->history_user_id = hikaserial::loadUser();
		if($newSerial) {
			$history->history_type = 'creation';
		} else {
			$history->history_type = 'modification';
		}
		if(empty($serial->serial_status) && !$newSerial) {
			$serialClass = hikaserial::get('class.serial');
			$old = $serialClass->get((int)$id);
			$serial->serial_status = $old->order_status;
			unset($old);
		}
		$history->history_new_status = $serial->serial_status;
		if(!empty($serial->history)) {
			foreach(get_object_vars($serial->history) as $k => $v) {
				$history->$k = $v;
			}
		}
		return $history;
	}

	public function &create($id, $status = '', $type = 'modification', $data = '') {
		$history = new stdClass();
		$history->history_serial_id = $id;
		$history->history_created = time();
		$history->history_ip = hikaserial::getIP();
		$history->history_user_id = hikaserial::loadUser();
		if(!empty($status)) {
			$history->history_new_status = $status;
		}
		if(!empty($data)) {
			$history->history_data = $data;
		}
		return $history;
	}

}
