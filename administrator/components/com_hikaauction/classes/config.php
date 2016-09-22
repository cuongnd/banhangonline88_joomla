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
class hikaauctionConfigClass extends hikaauctionClass {
	protected $toggle = array('config_value' => 'config_namekey');

	public function load() {
		$query = 'SELECT * FROM '.hikaauction::table('config');
		$this->db->setQuery($query);
		$this->values = $this->db->loadObjectList('config_namekey');
		if(!empty($this->values['default_params']->config_value)) {
			$this->values['default_params']->config_value = unserialize(base64_decode($this->values['default_params']->config_value));
		}
	}

	public function set($namekey, $value = null) {
		if(empty($this->values))
			$this->values = array();
		$this->values[$namekey] = new stdClass();
		$this->values[$namekey]->config_value = $value;
		$this->values[$namekey]->config_namekey = $namekey;
		return true;
	}

	public function get($namekey, $default = null) {
		if(isset($this->values[$namekey])){
			if(preg_match('#^(menu_|params_)[0-9]+$#',$namekey) && !empty($this->values[$namekey]->config_value) && is_string($this->values[$namekey]->config_value)) {
				$this->values[$namekey]->config_value = unserialize(base64_decode($this->values[$namekey]->config_value));
			}
			return $this->values[$namekey]->config_value;
		}
		return $default;
	}

	public function save(&$configObject, $default = false) {
		if(empty($this->values)) {
			$this->load();
		}
		$query = 'REPLACE INTO '.hikaauction::table('config').' (config_namekey,config_value'.($default?',config_default':'').') VALUES ';
		$params = array();
		if(is_object($configObject)) {
			$configObject = get_object_vars($configObject);
		}
		jimport('joomla.filter.filterinput');
		$safeHtmlFilter = JFilterInput::getInstance(null, null, 1, 1);
		foreach($configObject as $namekey => $value) {
			if( $namekey == 'default_params' || preg_match('#^(menu_|params_)[0-9]+$#',$namekey) ) {
				$value = base64_encode(serialize($value));
			}
			if(is_array($value))
				continue;
			if(empty($this->values[$namekey]))
				$this->values[$namekey] = new stdClass();
			$this->values[$namekey]->config_value = $value;
			if( !isset($this->values[$namekey]->config_default) ) {
				$this->values[$namekey]->config_default = $this->values[$namekey]->config_value;
			}
			$params[] = '('.$this->db->Quote(strip_tags($namekey)).','.$this->db->Quote($safeHtmlFilter->clean($value, 'string')).($default?','.$this->db->Quote($this->values[$namekey]->config_default):'').')';
		}
		$query .= implode(',',$params);
		$this->db->setQuery($query);
		return $this->db->query();
	}

	public function reset() {
		$query = 'UPDATE '.hikaauction::table('config').' SET config_value = config_default';
		$this->db->setQuery($query);
		$this->values = $this->db->query();
	}
}
