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
class hikaserialSerialClass extends hikaserialClass {

	protected $tables = array('serial');
	protected $pkeys = array('serial_id');

	public function get($element, $full = false) {
		$ret = parent::get($element);

		if(!empty($ret->serial_extradata)) {
			$ret->serial_extradata = hikaserial::unserialize($ret->serial_extradata);
		}

		if($full) {
			if(!empty($ret->serial_pack_id)) {
				$packClass = hikaserial::get('class.pack');
				$ret->pack = $packClass->get($ret->serial_pack_id);
			}
			if(!empty($ret->serial_user_id)) {
				$userClass = hikaserial::get('shop.class.user');
				$ret->user = $userClass->get($ret->serial_user_id);
			}
			if(!empty($ret->serial_order_id)) {
				$orderClass = hikaserial::get('shop.class.order');
				$ret->order = $orderClass->get($ret->serial_order_id);
			}
			if(!empty($ret->serial_order_product_id)){
				$orderProductClass = hikaserial::get('shop.class.order_product');
				$ret->orderproduct = $orderProductClass->get($ret->serial_order_product_id);
			}

			$config = hikaserial::config();
			if($config->get('save_history', 1)) {
				$query = 'SELECT * FROM ' . hikaserial::table('history') . ' WHERE history_serial_id = '. (int)$ret->serial_id . ' ORDER BY history_created ASC';
				$this->db->setQuery($query);
				$ret->history = $this->db->loadObjectList();
			}
		}

		return $ret;
	}

	public function saveForm() {
		$serial = new stdClass();
		$serial->serial_id = hikaserial::getCID('serial_id');

		$new = !empty($serial->serial_id);

		if(!$new) {
			$serial->old = $this->get( (int)$serial->serial_id );
		}

		$formData = JRequest::getVar('data', array(), '', 'array');
		foreach($formData['serial'] as $col => $val) {
			hikaserial::secureField($col);
			if(is_array($val) || is_object($val))
				continue;
			$serial->$col = strip_tags($val);
		}
		if(!empty($formData['serial']['serial_extradata'])) {
			$serial->serial_extradata = $formData['serial']['serial_extradata'];
		} else {
			$isExtraData = JRequest::getInt('hikaserial_extradata', 0);
			if($isExtraData) {
				$serial->serial_extradata = '';
			}
		}

		JPluginHelper::importPlugin('hikashop');
		JPluginHelper::importPlugin('hikaserial');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onBeforeSerialSave', array(&$serial));

		if(empty($serial->serial_id))
			unset($serial->serial_id);

		$status = $this->save($serial);

		if($status) {
			$p = (int)$serial->serial_pack_id;
			$packs = array($p => $p);

			if(isset($serial->old)) {
				$p = (int)$serial->old->serial_pack_id;
				$packs[$p] = $p;
			}

			$productClass = hikaserial::get('class.product');
			$productClass->refreshQuantity(null, $packs);
		}
		return $status;
	}

	public function save(&$element) {
		if(!empty($element->serial_extradata) && !is_string($element->serial_extradata)) {
			$element->serial_extradata = serialize($element->serial_extradata);
		}
		if(isset($element->history) && empty($element->history)) {
			unset($element->history);
		}
		$newSerial = false;
		if(empty($element->serial_id)) {
			$newSerial = true;
		}
		$status = parent::save($element);

		$config = hikaserial::config();
		if($config->get('save_history', 1)) {
			$historyClass = hikaserial::get('class.history');
			$history = $historyClass->generate($status, $element, $newSerial);
			$historyClass->save($history);
		}

		return $status;
	}

	public function generate(&$serials, $struct) {
		$config = hikaserial::config();
		$serial_order_product_id = null;

		$data = array(
			'serial_pack_id' => '0',
			'serial_data' => '',
			'serial_extradata' => $this->db->Quote(''),
			'serial_status' => $this->db->Quote(''),
			'serial_assign_date' => 'NULL',
			'serial_order_id' => 'NULL',
			'serial_user_id' => 'NULL',
			'serial_order_product_id' => 'NULL'
		);
		foreach($struct as $k => $v) {
			if($k == 'serial_extradata' && !is_string($v))
				$v = $this->db->Quote(serialize($v));
			if(is_string($v) && $v != 'NULL') {
				$v = $this->db->Quote(trim($v,'"\''));
			}
			$data[$k] = $v;
		}

		if($config->get('save_history', 1)) {
			$serial_order_product_id = $data['serial_order_product_id'];
			$data['serial_order_product_id'] = '0';
		}

		$query = 'INSERT IGNORE INTO ' . hikaserial::table('serial') . ' (' . implode(',', array_keys($data)) . ') VALUES ';
		$queryData = array();
		foreach($serials as $serial) {
			$data['serial_extradata'] = $this->db->Quote('');
			if(is_array($serial)) {
				$data['serial_data'] = $this->db->Quote($serial['data']);
				if(!empty($serial['extradata']))
					$data['serial_extradata'] = $this->db->Quote(serialize($serial['extradata']));
			} else if(is_object($serial)) {
				$data['serial_data'] = $this->db->Quote($serial->data);
				if(!empty($serial->extradata))
					$data['serial_extradata'] = $this->db->Quote(serialize($serial->extradata));
			} else {
				$data['serial_data'] = $this->db->Quote($serial);
			}
			$queryData[] = '(' . implode(',', $data) . ')';
		}
		$query .= implode(',', $queryData);
		unset($queryData);

		$this->db->setQuery($query);
		$this->db->query();
		unset($query);

		if($config->get('save_history', 1)) {
			$generateTime = time();
			$query = 'SELECT a.* FROM '.hikaserial::table('serial').' AS a WHERE a.serial_assign_date = '.(int)$generateTime.' AND serial_order_id = 0';
			$this->db->setQuery($query);
			$generateSerials = $this->db->loadObjectList();

			$histories = array();
			foreach($generateSerials as $serial) {
				$histories[] = $historyClass->generate(null, $serial, true);
			}
			if(!empty($histories))
				$historyClass->save($histories);
		}
	}

	public function find($serial_data, $filters = null, $orders = null) {
		$query = 'SELECT serial.* FROM ' . hikaserial::table('serial') . ' AS serial INNER JOIN ' . hikaserial::table('pack') . ' AS pack '.
			' ON serial.serial_pack_id = pack.pack_id '.
			' WHERE pack.pack_published = 1 AND serial.serial_data = ' . $this->db->Quote($serial_data);
		if(!empty($filters)) {
			if(is_array($filters))
				$query .= '(' . implode(') AND (', $filters) . ')';
		}
		if(!empty($orders)) {
			if(is_array($orders)) {
				$query .= ' ORDER BY ' . implode(',', $orders);
			} else {
				$query .= ' ORDER BY ' . $orders;
			}
		}
		$this->db->setQuery($query);
		$this->db->query();
		$ret = $this->db->loadObjectList();
		foreach($ret as &$r) {
			if(!empty($r->serial_extradata)) {
				$r->serial_extradata = hikaserial::unserialize($r->serial_extradata);
			}
		}
		return $ret;
	}

	public function delete($elements) {
		if(!is_array($elements)) {
			$elements = array($elements);
		}
		$config = hikaserial::config();
		JPluginHelper::importPlugin('hikashop');
		JPluginHelper::importPlugin('hikaserial');
		$dispatcher = JDispatcher::getInstance();
		$do = true;
		$dispatcher->trigger('onBeforeSerialDelete', array(&$elements, &$do));
		if(!$do) {
			return false;
		}

		$deleted_status = 'deleted';
		$useDeletedStatus = $config->get('use_deleted_serial_status', false);

		if(!$useDeletedStatus) {
			$result = parent::delete($elements);
			if($result) {
				$query = 'DELETE FROM '.hikaserial::table('history').' WHERE history_serial_id IN ('.implode(',',$elements).')';
				$this->db->setQuery($query);
				$this->db->query();

				$dispatcher->trigger('onAfterSerialDelete', array(&$elements));
			}
		} else {
			$query = 'DELETE FROM ' . hikaserial::table('serial') . ' WHERE serial_id IN ('.implode(',',$elements).') AND serial_status = '.$this->db->Quote($deleted_status);
			$this->db->setQuery($query);
			$this->db->query();
			$result = true;

			$query = 'SELECT serial_id FROM ' . hikaserial::table('serial') . ' WHERE serial_id IN ('.implode(',',$elements).')';
			$this->db->setQuery($query);
			if(!HIKASHOP_J25) {
				$updateIds = $this->db->loadResultArray();
			} else {
				$updateIds = $this->db->loadColumn();
			}

			if(!empty($updateIds)) {
				$query = 'UPDATE ' . hikaserial::table('serial') . ' SET serial_status = '.$this->db->Quote($deleted_status) . ' WHERE serial_id IN ('.implode(',',$updateIds).')';
				$this->db->setQuery($query);
				$this->db->query();

				$query = 'DELETE FROM '.hikaserial::table('history').' WHERE history_serial_id IN ('.implode(',',$elements).') AND history_serial_id NOT IN ('.implode(',',$updateIds).')';
				$this->db->setQuery($query);
				$this->db->query();

				if($config->get('save_history', 1)) {
					$historyClass = hikaserial::get('class.history');
					$histories = array();
					foreach($updateIds as $id) {
						$histories[] = $historyClass->create($id, $deleted_status, 'delete');
					}
					if(!empty($histories))
						$historyClass->save($histories);
				}
			} else {
				$query = 'DELETE FROM '.hikaserial::table('history').' WHERE history_serial_id IN ('.implode(',',$elements).')';
				$this->db->setQuery($query);
				$this->db->query();
			}

			$dispatcher->trigger('onAfterSerialDelete', array(&$elements));
		}

		$productClass = hikaserial::get('class.product');
		$productClass->refreshQuantities();

		return $result;
	}

	public function consume($serial_id, $extra_data = null, $checkUser = true) {
		$app = JFactory::getApplication();
		$config = hikaserial::config();
		$serial = $this->get($serial_id, true);
		$assigned_status = $config->get('assigned_serial_status', 'assigned');
		$used_status = $config->get('used_serial_status', 'used');
		$do = true;

		if(!empty($serial->pack->pack_params) && is_string($serial->pack->pack_params))
			$serial->pack->pack_params = hikaserial::unserialize($serial->pack->pack_params);

		if(empty($serial->pack->pack_params->consumer) || !$serial->pack->pack_params->consumer || $serial->serial_status != $assigned_status) {
			return false;
		}

		$user_id = 0;
		if(!$app->isAdmin() && $checkUser) {
			$user_id = hikaserial::loadUser();
			if($user_id == 0 || ($serial->serial_user_id > 0 && $serial->serial_user_id != $user_id)) {
				return false;
			}
		}

		JPluginHelper::importPlugin('hikashop');
		JPluginHelper::importPlugin('hikaserial');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onBeforeSerialConsume', array(&$serial, $user_id, &$do, &$extra_data));
		if(!$do)
			return false;

		if(!empty($serial->managed))
			return $serial->serial_id;

		$serialConsumed = new stdClass();
		$serialConsumed->serial_id = $serial->serial_id;

		$serialConsumed->serial_status = $used_status;
		if(!empty($serial->status))
			$serialConsumed->serial_status = $serial->status;

		if($user_id > 0)
			$serialConsumed->serial_user_id = $user_id;

		if(!empty($extra_data)) {
			$serialConsumed->serial_extradata = array();
			if(!empty($serial->serial_extradata)) {
				$serialConsumed->serial_extradata = $serial->serial_extradata;
			}
			foreach($extra_data as $key => $value) {
				$serialConsumed->serial_extradata[$key] = $value;
			}
		}

		$status = $this->save($serialConsumed);

		if($status) {
			$dispatcher->trigger('onAfterSerialConsume', array(&$serial));
		}
		return $status;
	}

	public function check($serial_data, $pack = null) {
		$do = true;
		$filters = array();
		JPluginHelper::importPlugin('hikashop');
		JPluginHelper::importPlugin('hikaserial');
		$dispatcher = JDispatcher::getInstance();

		if(!empty($pack)) {
			$f = null;
			if(is_array($pack))
				$f = reset($pack);

			if(is_int($pack))
				$filters[] = 'b.pack_id = '.(int)$pack;
			if(is_int($f)) {
				JArrayHelper::toInteger($pack);
				$filters[] = 'b.pack_id IN (' . implode(',', $pack) . ')';
			}

			if(is_string($pack))
				$filters[] = 'b.pack_name = ' . $this->db->Quote($pack);
			if(is_string($f)) {
				$elems = array();
				foreach($pack as $p)
					$elems[] = $this->db->Quote($p);
				$filters[] = 'b.pack_name IN ('.implode(',', $elems).')';
			}
		}

		$dispatcher->trigger('onBeforeSerialCheck', array(&$serial_data, &$do, &$filters));
		if(!$do)
			return false;

		$query = 'SELECT a.*, b.pack_params FROM '.hikaserial::table('serial').' AS a INNER JOIN '.hikaserial::table('pack').' AS b '.
				' ON a.serial_pack_id = b.pack_id '.
				' WHERE (b.pack_published = 1 AND a.serial_data = '.$this->db->Quote($serial_data).') ' . implode(' AND ', $filters);
		$this->db->setQuery($query);
		$ret = $this->db->loadObjectList();
		if(!empty($ret)) {
			foreach($ret as $k => &$r) {
				$params = new stdClass();
				if(!empty($r->pack_params))
					$params = hikaserial::unserialize($r->pack_params);
				if(!empty($params->webservice)) {
					unset($r->pack_params);
					if(!empty($r->serial_extradata)) {
						$r->serial_extradata = hikaserial::unserialize($r->serial_extradata);
					}
				} else {
					unset($ret[$k]);
				}
			}
		}

		$dispatcher->trigger('onAfterSerialCheck', array($serial_data, &$ret));

		return $ret;
	}

	public function unassign($serial_id, $type = 'all') {
		$ret = false;
		if(empty($serial_id))
			return $ret;

		$app = JFactory::getApplication();
		if(!$app->isAdmin()) {
			$user_id = hikaserial::loadUser();
			if($serial->serial_user_id != $user_id) {
				return false;
			}
		}

		$save = false;
		$serial = $this->get($serial_id);
		$newSerial = new stdClass();
		$newSerial->serial_id = $serial_id;
		if($type == 'all' || $type == 'order') {
			$newSerial->serial_assign_date = 0;
			$newSerial->serial_order_id = 0;
			$save = true;
		}
		if($type == 'all' || $type == 'user') {
			if(empty($serial->serial_order_id) || isset($newSerial->serial_order_id)) {
				$newSerial->serial_user_id = 0;
				$newSerial->serial_assign_date = 0;
				$save = true;
			}
		}
		if($type == 'all') {
			$newSerial->serial_extra_data = array();
		}

		JPluginHelper::importPlugin('hikashop');
		JPluginHelper::importPlugin('hikaserial');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onBeforeSerialUnassign', array($serial, $type, &$newSerial, &$save));

		if($save) {
			if(!empty($newSerial->serial_extradata) && !is_string($newSerial->serial_extradata)) {
				$newSerial->serial_extradata = serialize($newSerial->serial_extradata);
			}
			$ret = parent::save($newSerial);
		}
		return $ret;
	}
}
