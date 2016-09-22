<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class hikamarketPluginsClass extends hikamarketClass {

	public function  __construct($config = array()) {
		if(!HIKASHOP_J16) {
			$this->toggle = array('published' => 'id');
			$this->pkeys = array('id');
			$this->tables = array('joomla.plugins');
		} else {
			$this->toggle = array('enabled' => 'extension_id');
			$this->pkeys = array('extension_id');
			$this->tables = array('joomla.extensions');
		}
		return parent::__construct($config);
	}

	public function getTable() {
		if(!HIKASHOP_J16) {
			return hikamarket::table('plugins', false);
		}
		return hikamarket::table('extensions', false);
	}

	public function params(&$methods,$type){
		if(empty($methods))
			return;

		$params = $type.'_params';
		foreach($methods as $k => $el){
			if(!empty($el->$params))
				$methods[$k]->$params = @hikamarket::unserialize($el->$params);
		}
	}

	public function get($id, $default = null){
		$result = parent::get($id);
		$this->loadParams($result);
		return $result;
	}

	public function getByName($type, $name) {
		$table = $this->getTable();
		if(!HIKASHOP_J16) {
			$query = 'SELECT * FROM ' . $table . ' WHERE folder=' . $this->db->Quote($type) . ' AND element=' . $this->db->Quote($name);
		} else {
			$query = 'SELECT * FROM ' . $table . ' WHERE folder=' . $this->db->Quote($type) . ' AND element=' . $this->db->Quote($name) . ' AND type=\'plugin\'';
		}
		$this->db->setQuery($query);
		$result = $this->db->loadObject();
		$this->loadParams($result);
		return $result;
	}

	private function loadParams(&$result) {
		if(empty($result->params))
			return;

		if(!HIKASHOP_J16) {
			$lines = explode("\n", $result->params);
			$result->params = array();
			foreach($lines as $line) {
				$param = explode('=', $line,2);
				if(count($param) == 2)
					$result->params[$param[0]] = $param[1];
			}
		} else {
			$registry = new JRegistry();
			if(!HIKASHOP_J30)
				$registry->loadJSON($result->params);
			else
				$registry->loadString($result->params, 'JSON');
			$result->params = $registry->toArray();
		}
	}


	public function save(&$element) {
		if(!empty($element->params)) {
			if(!HIKASHOP_J16) {
				$params = '';
				foreach($element->params as $key => $val) {
					$params.=$key.'='.$val."\n";
				}
				$element->params = rtrim($params);
			} else {
				$handler = JRegistryFormat::getInstance('JSON');
				$element->params = $handler->objectToString($element->params);
			}
		}
		return parent::save($element);
	}

	public function getPlugin($name, $type = 'hikamarket') {
		$ret = $this->getPlugins($type, $name);
		if(!empty($ret)){
			if(count($ret) == 1)
				return reset($ret);
			return $ret;
		}
		return null;
	}

	public function getPlugins($type = 'hikamarket', $name = '') {
		$where = array();
		if(!empty($name)) {
			$where[] = $type.'_type='.$this->db->Quote($name);
		}


		if(!empty($where)) {
			$where = ' WHERE '.implode(' AND ',$where);
		} else {
			$where = '';
		}

		$query = 'SELECT * FROM '.hikamarket::table($type).' '.$where;
		$this->db->setQuery($query);
		$methods = $this->db->loadObjectList($type.'_id');
		$this->params($methods, $type);
		if(empty($methods)) {
			$methods = array();
		}
		return $methods;
	}

}
