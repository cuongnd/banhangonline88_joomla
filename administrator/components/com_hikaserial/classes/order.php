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
class hikaserialOrderClass extends hikaserialClass {

	protected $order_id = 0;
	protected $order_packs = array();
	protected $order_products = array();
	protected $products = array();

	protected function loadPacks(&$order) {

		if($order->order_id == $this->order_id)
			return;

		$this->order_id = $order->order_id;
		$this->sqlPacks = array();
		$this->order_packs = array();
		$this->order_products = array();
		$this->products = array();

		$p = null;
		if(isset($order->cart)) {
			$p = reset($order->cart->products);
		}
		if(!isset($p->order_product_id)) {
			$orderClass = hikaserial::get('shop.class.order');
			$order->cart = $orderClass->loadFullOrder($order->order_id, false, false);
		}

		if(!empty($order->order_serial_params) && empty($order->order_serials)) {

			$order_serial_params = json_decode($order->order_serial_params, true);

			$serials = array();
			$packs = array();

			if(!empty($order_serial_params['order'])) {
				$qty = 0;
				foreach($order->cart->products as $p) {
					$qty += (int)$p->order_product_quantity;
				}
				foreach($order_serial_params['order'] as $k => $v) {
					$a = 0; $b = 0;
					if(is_array($v)) list($a, $b) = $v;
					else $a = (int)$v;

					if(!isset($packs[(int)$k]))
						$packs[(int)$k] = 0;
					$packs[(int)$k] += ((int)$a * (int)$qty) + (int)$b;
				}
			}

			if(!empty($order_serial_params['product']) && !empty($order->cart->products)) {
				foreach($order_serial_params['product'] as $pid => $data) {
					$qty = 0;
					foreach($order->cart->products as $p) {
						if((int)$p->product_id == (int)$k)
							$qty += (int)$p->order_product_quantity;
					}
					if(empty($qty))
						continue;

					foreach($data as $k => $v) {
						$a = 0; $b = 0;
						if(is_array($v)) list($a, $b) = $v;
						else $a = (int)$v;

						if(!isset($packs[(int)$k]))
							$packs[(int)$k] = 0;
						$packs[(int)$k] += ((int)$a * (int)$qty) + (int)$b;
					}
				}
			}

			if(!empty($order_serial_params['order_product']) && !empty($order->cart->products)) {
				foreach($order_serial_params['order_product'] as $pid => $data) {
					$qty = 0;
					foreach($order->cart->products as $p) {
						if((int)$p->order_product_id == (int)$k) {
							$qty = (int)$p->order_product_quantity;
							break;
						}
					}
					if(empty($qty))
						continue;
					foreach($data as $k => $v) {
						$a = 0; $b = 0;
						if(is_array($v)) list($a, $b) = $v;
						else $a = (int)$v;

						if(!isset($packs[(int)$k]))
							$packs[(int)$k] = 0;
						$packs[(int)$k] += ((int)$a * (int)$qty) + (int)$b;
					}
				}
			}

			if(!empty($order_serial_params['serial'])) {
				foreach($order_serial_params['serial'] as $v) {
					$serials[(int)$v] = (int)$v;
				}
			}

			$order->order_serials = new stdClass();
			$order->order_serials->packs = $packs;
			$order->order_serials->serials = $serials;
		}

		if(!empty($order->cart->products)) {
			foreach($order->cart->products as $p) {
				if(!isset($p->order_product_id) || empty($p->product_id))
					continue;

				$this->order_products[(int)$p->order_product_id] = array(
					'id' => (int)$p->product_id,
					'quantity' => (int)$p->order_product_quantity
				);
				if(empty($this->products[(int)$p->product_id]))
					$this->products[(int)$p->product_id] = array();
				$this->products[(int)$p->product_id][] = (int)$p->order_product_id;
			}
		}
		if(empty($this->products))
			return;

		$query = 'SELECT product.product_id as `main_product_id`, product.product_parent_id, pp.*, pack.* '.
			' FROM ' . hikaserial::table('shop.product') . ' as product '.
			' INNER JOIN ' . hikaserial::table('product_pack') . ' as pp ON ( pp.product_id = product.product_id OR (pp.product_id = product.product_parent_id AND pp.product_id > 0)) '.
			' INNER JOIN ' . hikaserial::table('pack') . ' as pack ON pp.pack_id = pack.pack_id '.
			' WHERE product.product_id IN (' . implode(',', array_keys($this->products)) . ') AND pack.pack_published = 1 '.
			' ORDER BY pp.pack_id ASC';

		$this->db->setQuery($query);
		$this->order_packs = $this->db->loadObjectList();
	}

	public function loadSerialData($order_id, $viewName = null) {
		if(empty($order_id))
			return false;

		$config = hikaserial::config();

		$display_serial_statuses = $config->get('display_serial_statuses','');
		if(empty($display_serial_statuses)) {
			$display_serial_statuses = array($config->get('used_serial_status','used'));
		} else {
			$display_serial_statuses = explode(',', $display_serial_statuses);
		}
		foreach($display_serial_statuses as &$s) {
			$s = $this->db->Quote($s);
		}
		unset($s);

		$query = 'SELECT s.*, p.*, op.* FROM '.
			hikaserial::table('serial') . ' as s '.
			'INNER JOIN '. hikaserial::table('pack') . ' as p ON s.serial_pack_id = p.pack_id '.
			'LEFT JOIN ' . hikaserial::table('shop.order_product') . ' as op ON s.serial_order_product_id = op.order_product_id '.
			'WHERE s.serial_status IN ('.implode(',',$display_serial_statuses).') AND s.serial_order_id = ' . $order_id;
		$this->db->setQuery($query);
		$data = $this->db->loadObjectList();

		if(!empty($viewName)) {
			JPluginHelper::importPlugin('hikaserial');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onDisplaySerials', array(&$data, $viewName));
		}

		if(!empty($data)) {
			foreach($data as &$serial) {
				if(!isset($serial->serial_text_data)) {
					$serial->serial_text_data = $serial->serial_data;
					$serial->serial_data = str_replace(array("\r\n","\r","\n"), '<br/>', $serial->serial_data);
				}
				if(!empty($serial->serial_extradata) && is_string($serial->serial_extradata))
					$serial->serial_extradata = hikaserial::unserialize($serial->serial_extradata);
				unset($serial);
			}
		}

		return $data;
	}

	public function preUpdate(&$order) {
		if(isset($order->order_type) && $order->order_type != 'sale')
			return;
		if(isset($order->old->order_type) && $order->old->order_type != 'sale')
			return;

		$order->serial_data = new stdClass();
		$order->serial_data->old_order_status = '';
		if(empty($order->old) || empty($order->old->order_status)) {
			if(isset($order->order_id) && (int)$order->order_id > 0) {
				$this->db->setQuery('SELECT order_status FROM ' . hikaserial::table('shop.order') . ' WHERE order_id = ' . $order->order_id);
				$ret = $this->db->loadObject();
				$order->serial_data->old_order_status = $ret->order_status;
			}
		} else {
			$order->serial_data->old_order_status = $order->old->order_status;
		}

		$order_serial_params = array(
			'order' => array(),
			'product' => array(),
			'order_product' => array(),
			'serial' => array()
		);
		if(empty($order->order_serial_params) && !empty($order->old->order_serial_params))
			$order->order_serial_params = $order->old->order_serial_params;
		if(!empty($order->order_serial_params)) {
			if(is_string($order->order_serial_params))
				$order_serial_params = array_merge($order_serial_params, json_decode($order->order_serial_params, true));
			elseif(is_array($order->order_serial_params))
				$order_serial_params = array_merge($order_serial_params, $order->order_serial_params);
		}

		$new = empty($order->order_id);

		JPluginHelper::importPlugin('hikaserial');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onSerialOrderPreUpdate', array($new, &$order, &$order_serial_params));

		$order->order_serial_params = json_encode($order_serial_params);
	}

	public function postUpdate(&$order) {
		if(!isset($order->order_status))
			return;
		if(!isset($order->serial_data->old_order_status))
			return;

		if(isset($order->order_type) && $order->order_type != 'sale')
			return;
		if(isset($order->old->order_type) && $order->old->order_type != 'sale')
			return;

		$config = hikaserial::config();
		$statuses = explode(',', $config->get('assignable_order_statuses', 'confirmed,shipped'));
		if(empty($statuses) || (count($statuses) == 1 && empty($statuses[0]))) {
			$shopConfig = hikaserial::config(false);
			$statuses = array($shopConfig->get('order_confirmed_status'));
		}
		if(in_array($order->order_status, $statuses)) {
			if(!in_array($order->serial_data->old_order_status, $statuses)) {
				$this->assignSerials($order);
			}
		} else if(in_array($order->serial_data->old_order_status, $statuses)) {
			$this->unassignSerials($order);
		}
	}

	public function assignSerials(&$order) {
		if(isset($order->order_type) && $order->order_type != 'sale')
			return;
		if(isset($order->old->order_type) && $order->old->order_type != 'sale')
			return;

		$shopConfig = hikaserial::config(false);
		$config = hikaserial::config();
		$packClass = hikaserial::get('class.pack');
		$historyClass = hikaserial::get('class.history');

		$useable_serial_statuses = explode(',', str_replace('\'', '\\\'', $config->get('useable_serial_statuses', 'free')));
		$this->useable_serial_statuses = $useable_serial_statuses;
		$assigned_serial_status = $config->get('assigned_serial_status', 'assigned');
		$used_serial_status = $config->get('used_serial_status', 'used');
		$unassignedStatus = $config->get('unassigned_serial_status', '');
		$statuses = explode(',', $config->get('assignable_order_statuses', 'confirmed,shipped'));
		if(empty($statuses) || (count($statuses) == 1 && empty($statuses[0]))) {
			$statuses = array($shopConfig->get('order_confirmed_status'));
		}
		if(!in_array($order->order_status, $statuses))
			return;

		$serial_statuses = array(
			$this->db->Quote($used_serial_status),
			$this->db->Quote($assigned_serial_status)
		);
		$query = 'SELECT serial.serial_pack_id, serial.serial_id, serial_order_product_id FROM ' . hikaserial::table('serial') . ' as serial '.
			' WHERE serial_status IN ('.implode(',',$serial_statuses).') AND serial_order_id = '.(int)$order->order_id.
			' ORDER BY serial_id';
		$this->db->setQuery($query);
		$current_serials = $this->db->loadObjectList('serial_id');
		$current = array();
		$used = array();
		if(!empty($current_serials)) {
			foreach($current_serials as &$serial) {
				$serial->serial_pack_id = (int)$serial->serial_pack_id;
				$serial->serial_id = (int)$serial->serial_id;
				$serial->serial_order_product_id = (int)$serial->serial_order_product_id;

				if(empty($current[$serial->serial_pack_id]))
					$current[$serial->serial_pack_id] = array();
				$current[$serial->serial_pack_id][] = $serial->serial_id;
			}
			unset($serial);
		}

		$this->loadPacks($order);
		if(empty($this->order_packs) && empty($order->order_serials->serials) && empty($order->order_serials->packs)) {
			if(!empty($current)) {
				$this->unassignSerials($order);
			}
			return;
		}

		$packs = array();
		$unassigned = array();
		foreach($this->order_packs as &$pack) {
			$pack->quantity = (int)$pack->quantity;
			$pack->main_product_id = (int)$pack->main_product_id;
			$pack->product_parent_id = (int)$pack->product_parent_id;
			$pack->product_id = (int)$pack->product_id;
			$pack->pack_id = (int)$pack->pack_id;

			if( $pack->quantity <= 0 )
				continue;

			$pid = (int)$pack->product_id;
			if($pack->main_product_id != $pack->product_id) {
				$found = false;
				foreach($this->order_packs as $search) {
					if($search->product_id == $pack->main_product_id) {
						$found = true;
						break;
					}
				}
				if($found)
					continue;

				$pid = (int)$pack->main_product_id;
			}

			foreach($this->products[$pid] as $order_product_id) {
				$order_product = $this->order_products[$order_product_id];

				$pack_quantity = $pack->quantity * $order_product['quantity'];
				$final_quantity = $pack_quantity;

				$tmp_serials = array();
				foreach($current_serials as $k => &$v) {
					if($v->serial_pack_id != $pack->pack_id || (isset($v->used) && $v->used == true))
						continue;

					if($v->serial_order_product_id != $order_product_id) {
						if(!isset($v->used))
							$v->used = false;
						continue;
					}

					$pack_quantity--;

					$tmp_serials[] = $k;

					$v->used = true;
				}
				unset($v);

				if($pack_quantity == 0 )
					continue;

				if($pack_quantity < 0 ) {
					$unassigned = array_merge($unassigned, array_slice($tmp_serials, $final_quantity));
					continue;
				}

				if(is_string($pack->pack_params))
					$pack->pack_params = hikaserial::unserialize($pack->pack_params);
				$packs[$pack->pack_id] =& $pack;

				$this->assignPack($pack, $order, $order_product_id, $pack_quantity);
			}
		}
		unset($pack);

		if(!empty($order->order_serials->packs)) {
			foreach($order->order_serials->packs as $pack_id => $quantity) {
				foreach($current_serials as $k => &$v) {
					if($v->serial_pack_id != $pack_id || !empty($v->used))
						continue;

					if(!isset($v->used) || $v->used == false) {
						$v->used = true;
						$quantity--;
					}
					if($quantity == 0)
						break;
				}
				unset($v);

				if($quantity > 0) {
					$pack = $packClass->get($pack_id);
					$pack->quantity = $quantity;
					$this->assignPack($pack, $order, 0);
					$packs[] =& $pack;
				}
			}
		}

		if(!empty($order->order_serials->serials)) {
			$update_time = time();
			$query = 'UPDATE ' . hikaserial::table('serial') .
				' SET serial_order_id = ' . (int)$order->order_id .
				', serial_assign_date = ' . (int)$update_time .
				', serial_status = ' . $this->db->quote($used_serial_status) .
				', serial_user_id = ' . (int)@$order->cart->order_user_id .
				', serial_order_product_id = 0' .
				' WHERE serial_id IN (' . implode(',',$order->order_serials->serials) . ')'.
					' AND serial_status IN (\'\', \''.implode('\',\'', $this->useable_serial_statuses).'\')'.
					' AND serial_user_id IN (NULL, 0, '.(int)@$order->cart->order_user_id.')';
			$this->db->setQuery($query);
			$this->db->query();

			if($config->get('save_history', 1)) {
				$histories = array();
				foreach($order->order_serials->serials as $id) {
					$histories[] = $historyClass->create($id, $used_serial_status, 'assignation');
				}
				if(!empty($histories))
					$historyClass->save($histories);
			}
		}

		foreach($current_serials as $k => $v) {
			if(!isset($v->used) || $v->used == false)
				$unassigned[] = $k;
		}

		if(!empty($unassigned)){
			$querySet = array();
			if(!empty($unassignedStatus)) {
				$querySet[] = 'serial_status = ' . $this->db->Quote($unassignedStatus);
			}
			if((int)$config->get('unassigned_remove_data', 1) == 1) {
				$querySet[] = 'serial_assign_date = NULL';
				$querySet[] = 'serial_order_id = NULL';
				$querySet[] = 'serial_user_id = NULL';
				$querySet[] = 'serial_order_product_id = NULL';
			}
			if(!empty($querySet)) {
				$query = 'SELECT a.*, b.* FROM ' . hikaserial::table('serial') . ' AS a INNER JOIN ' . hikaserial::table('pack') . ' AS b ON a.serial_pack_id = b.pack_id WHERE serial_id IN (' . implode(',',$unassigned).')';
				$this->db->setQuery($query);
				$serials = $this->db->loadObjectList();

				$query = 'UPDATE ' . hikaserial::table('serial') . ' SET ' . implode(', ', $querySet) . ' WHERE serial_id IN (' . implode(',',$unassigned).')';
				$this->db->setQuery($query);
				$this->db->query();

				foreach($serials as &$serial) {
					$serial->unassignedStatus = $unassignedStatus;
					if(!empty($serial->pack_params)) {
						$serial->pack_params = hikaserial::unserialize($serial->pack_params);
						if(!empty($serial->pack_params->status_for_refund) && $serial->pack_params->status_for_refund != $unassignedStatus) {
							$serial->unassignedStatus = $serial->pack_params->status_for_refund;

							$query = 'UPDATE ' . hikaserial::table('serial') . ' SET serial_status = '.$this->db->Quote($serial->pack_params->status_for_refund).' WHERE serial_id = ' . (int)$serial->serial_id;
							$this->db->setQuery($query);
							$this->db->query();
							$updated = true;
						}
					}
				}

				if($config->get('save_history', 1)) {
					$histories = array();
					foreach($serials as &$serial) {
						$histories[] = $historyClass->create($serial->serial_id, $serial->unassignedStatus, 'unassignation');
					}
					if(!empty($histories))
						$historyClass->save($histories);
				}
			}
		}

		if(!empty($packs)) {
			foreach($packs as &$pack) {
				$packClass->checkQuantity($pack);
			}
		}

		if(!empty($unassigned) || !empty($packs)){
			$productClass = hikaserial::get('class.product');
			$productClass->refreshQuantities();
		}
	}

	protected function assignPack($pack, $order, $order_product_id, $request_quantity = 0) {
		$config = hikaserial::config();
		if($config->get('save_history', 1))
			$historyClass = hikaserial::get('class.history');

		if(empty($pack->pack_params->consumer) || !$pack->pack_params->consumer)
			$pack_serial_status = $config->get('used_serial_status', 'used');
		else
			$pack_serial_status = $config->get('assigned_serial_status', 'assigned');

		if(empty($request_quantity) || (int)$request_quantity == 0)
			$request_quantity = (int)$pack->quantity;

		$ids = null;
		if($pack->pack_data == 'sql') {
			$select = array('serial_id');
			if(empty($pack->pack_params->random_pickup))
				$select[] = 'RAND() as serial_random';

			$query = 'SELECT '.implode(',', $select).' FROM ' . hikaserial::table('serial') . ' WHERE serial_pack_id = '.$pack->pack_id.' AND '.
					'(serial_status = \'\' OR serial_status IN (\''.implode('\',\'', $this->useable_serial_statuses).'\')) AND '.
					'(serial_user_id = '.(int)@$order->cart->order_user_id.' OR serial_user_id = 0 OR serial_user_id IS NULL) AND '.
					'(serial_order_id = '.(int)$order->order_id.' OR serial_order_id = 0 OR serial_order_id IS NULL) ';
			if(empty($pack->pack_params->random_pickup))
				$query .= ' ORDER BY serial_user_id DESC, serial_random ASC, serial_id ASC';
			else
				$query .= ' ORDER BY serial_user_id DESC, serial_id ASC';

			$this->db->setQuery($query, 0, (int)$request_quantity);
			if(!HIKASHOP_J25)
				$ids = $this->db->loadResultArray();
			else
				$ids = $this->db->loadColumn();
		}

		if(!empty($ids)) {
			$query = 'UPDATE ' . hikaserial::table('serial') .
				' SET serial_order_id = ' . (int)$order->order_id .
				', serial_assign_date = ' . (int)time() .
				', serial_status = ' . $this->db->quote($pack_serial_status) .
				', serial_user_id = ' . (int)@$order->cart->order_user_id .
				', serial_order_product_id = ' . (int)$order_product_id .
				' WHERE serial_id IN (' . implode(',',$ids) . ') AND (serial_status = \'\' OR serial_status IN (\''.implode('\',\'', $this->useable_serial_statuses).'\'))';
			$this->db->setQuery($query);
			$this->db->query();

			if($config->get('save_history', 1)) {
				$histories = array();
				foreach($ids as $id) {
					$histories[] = $historyClass->create($id, $pack_serial_status, 'assignation');
				}
				if(!empty($histories))
					$historyClass->save($histories);
			}
		}

		if(empty($ids) || count($ids) < $request_quantity ) {
			$app = JFactory::getApplication();

			$quantity = $request_quantity;
			if(!empty($ids))
				$quantity -= count($ids);

			$generateTime = time();
			$data = array(
				'serial_pack_id' => (int)$pack->pack_id,
				'serial_data' => '',
				'serial_extradata' =>  $this->db->Quote(''),
				'serial_status' => $this->db->quote($pack_serial_status),
				'serial_assign_date' => (int)$generateTime,
				'serial_order_id' => (int)$order->order_id,
				'serial_user_id' => (int)@$order->cart->order_user_id,
				'serial_order_product_id' => (int)$order_product_id
			);

			if(!empty($pack->pack_params->no_user_assign))
				$data['serial_user_id'] = 0;

			$query = 'INSERT IGNORE INTO ' . hikaserial::table('serial') . ' (' . implode(',', array_keys($data)) . ') VALUES ';

			if(substr($pack->pack_generator, 0, 4) == 'plg.') {

				$pluginId = 0;
				$pluginName = substr($pack->pack_generator, 4);
				if(strpos($pluginName,'-') !== false){
					list($pluginName,$pluginId) = explode('-', $pluginName, 2);
					$pack->$pluginName = $pluginId;
				}
				$serials = array();

				$plugin = hikaserial::import('hikaserial', $pluginName);
				if(method_exists($plugin, 'generate')) {
					$pack->order_product_id = (int)$order_product_id;
					ob_start();
					$ret = $plugin->generate($pack, $order, $quantity, $serials);
					ob_get_clean();
				}

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
				if(!empty($queryData)) {
					$query .= implode(',', $queryData);

					$this->db->setQuery($query);
					$this->db->query();

					if($config->get('save_history', 1)) {
						$query = 'SELECT a.* FROM '.hikaserial::table('serial').' AS a WHERE a.serial_assign_date = '.(int)$generateTime.' AND serial_order_id = '.(int)$order->order_id;
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

				if(empty($serials) || count($serials) < $quantity) {
					if(isset($ret) && $ret === false || (int)$ret == count($serials)) {
						if($app->isAdmin()) {
							$app->enqueueMessage(JText::sprintf('COULD_NOT_GENERATE_SERIALS_FOR_PACK', $pack->pack_name), 'error');
						} else {
						}
					}
				}
			} else {
				if($app->isAdmin()) {
					$app->enqueueMessage(JText::sprintf('PACK_IS_EMPTY_AND_NO_GENERATOR', $pack->pack_name), 'error');
				} else {
				}
			}
		}
	}

	public function unassignSerials(&$order) {
		$config = hikaserial::config();

		$updated = false;
		$querySet = array();
		$unassignedStatus = $config->get('unassigned_serial_status', '');

		$query = 'SELECT a.*, b.* FROM ' . hikaserial::table('serial') . ' AS a '.
				' INNER JOIN ' . hikaserial::table('pack') . ' AS b ON a.serial_pack_id = b.pack_id '.
				' WHERE serial_order_id = ' . (int)$order->order_id;
		$this->db->setQuery($query);
		$serials = $this->db->loadObjectList();

		if(!empty($unassignedStatus)) {
			$querySet['serial_status'] = 'serial_status = ' . $this->db->Quote($unassignedStatus);
		}
		if($config->get('unassigned_remove_data', '1') == '1') {
			$querySet['serial_assign_date'] = 'serial_assign_date = NULL';
			$querySet['serial_order_id'] = 'serial_order_id = NULL';
			$querySet['serial_user_id'] = 'serial_user_id = NULL';
			$querySet['serial_order_product_id'] = 'serial_order_product_id = NULL';
		}
		if(!empty($querySet)) {
			$query = 'UPDATE ' . hikaserial::table('serial') . ' SET ' . implode(', ', $querySet) . ' WHERE serial_order_id = ' . (int)$order->order_id;
			$this->db->setQuery($query);
			$this->db->query();
			$updated = true;
		}

		foreach($serials as &$serial) {
			$serial->unassignedStatus = $unassignedStatus;
			if(!empty($serial->pack_params)) {
				$serial->pack_params = hikaserial::unserialize($serial->pack_params);
				if(!empty($serial->pack_params->status_for_refund) && $serial->pack_params->status_for_refund != $unassignedStatus) {
					$serial->unassignedStatus = $serial->pack_params->status_for_refund;

					$query = 'UPDATE ' . hikaserial::table('serial') . ' SET serial_status = '.$this->db->Quote($serial->pack_params->status_for_refund).' WHERE serial_id = ' . (int)$serial->serial_id;
					$this->db->setQuery($query);
					$this->db->query();
					$updated = true;
				}
			}
		}

		JPluginHelper::importPlugin('hikaserial');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onAfterSerialUnassigned', array(&$serials));

		if($config->get('save_history', 1)) {
			$historyClass = hikaserial::get('class.history');
			$histories = array();
			foreach($serials as &$serial) {
				$histories[] = $historyClass->create($serial->serial_id, $serial->unassignedStatus, 'unassignation');
			}
			if(!empty($histories))
				$historyClass->save($histories);
		}

		if($updated) {
			$productClass = hikaserial::get('class.product');
			$productClass->refreshQuantities();
		}
	}

	public function refresh($order_id) {
		$shopConfig = hikaserial::config(false);
		$config = hikaserial::config();
		$orderClass = hikaserial::get('shop.class.order');

		if(empty($order_id))
			return;
		$order = $orderClass->loadFullOrder($order_id);
		if(empty($order))
			return;

		if($order->order_type != 'sale') {
			$this->unassignSerials($order);
			return;
		}

		$statuses = explode(',', $config->get('assignable_order_statuses'));
		if(empty($statuses)) {
			$shopConfig = hikaserial::config(false);
			$statuses = array($shopConfig->get('order_confirmed_status'));
		}
		if(in_array($order->order_status, $statuses)) {
			$this->assignSerials($order);
		} else {
			$this->unassignSerials($order);
		}
	}

	public function beforeOrderExport(&$rows, &$view) {
		$orders = array();
		foreach($rows as $order) {
			$orders[] = (int)$order->order_id;
		}

		if(!empty($orders)) {
			$query = 'SELECT serial.serial_order_id, serial.serial_data FROM ' . hikaserial::table('serial') . ' AS serial WHERE serial.serial_order_id IN (' . implode(',', $orders) . ') ORDER BY serial.serial_order_id, serial.serial_id';
			$this->db->setQuery($query);
			$serials = $this->db->loadObjectList();
		}
		unset($orders);

		foreach($rows as $k => $order) {
			$order_serials = array();
			foreach($serials as $serial) {
				if($serial->serial_order_id == $order->order_id)
					$order_serials[] = $serial->serial_data;
			}
			$rows[$k]->serials = implode(';', $order_serials);
		}
		unset($serials);
	}

	public function modifyPack($order_id, $pack_id, $qty, $addition = true) {

		$orderClass = hikaserial::get('shop.class.order');
		$order = $orderClass->get($order_id);
		if(empty($order))
			return false;

		if($order->order_type != 'sale')
			return false;

		$order_serial_params = array();
		if(!empty($order->order_serial_params))
			$order_serial_params = json_decode($order->order_serial_params, true);

		if(empty($order_serial_params['order']))
			$order_serial_params['order'] = array();
		if(empty($order_serial_params['order'][$pack_id]))
			$order_serial_params['order'][$pack_id] = array(0,0);

		if($addition)
			$order_serial_params['order'][$pack_id][1] += $qty;
		else
			$order_serial_params['order'][$pack_id][1] = $qty;

		if($order_serial_params['order'][$pack_id][0] == 0 && $order_serial_params['order'][$pack_id][1] == 0)
			unset($order_serial_params['order'][$pack_id]);

		$updateOrder = new stdClass();
		$updateOrder->order_id = (int)$order_id;
		$updateOrder->order_serial_params = json_encode($order_serial_params);
		$orderClass->save($updateOrder);

		return true;
	}

	public function modifySerial($order_id, $serial_id, $action = true) {

		$orderClass = hikaserial::get('shop.class.order');
		$order = $orderClass->get($order_id);
		if(empty($order))
			return false;

		if($order->order_type != 'sale')
			return false;

		$serial_id = (int)$serial_id;
		$serialClass = hikamarket::get('class.serial');
		$serial = $serialClass->get($serial_id);

		if(empty($serial) || (!empty($serial->serial_order_id) && (int)$serial->serial_order_id != $order_id))
			return false;

		$order_serial_params = array();
		if(!empty($order->order_serial_params))
			$order_serial_params = json_decode($order->order_serial_params, true);

		if(empty($order_serial_params['serial']))
			$order_serial_params['serial'] = array();

		JArrayHelper::toInteger($order_serial_params['serial']);
		if($action === 1 && in_array($serial_id, $order_serial_params['serial']))
			return false;
		if($action === 0 && !in_array($serial_id, $order_serial_params['serial']))
			return false;

		if($action === true)
			$action = !in_array($serial_id, $order_serial_params['serial']);

		if($action) {
			$order_serial_params['serial'][] = $serial_id;
		} else {
			$order_serial_params['serial'] = array_diff($order_serial_params['serial'], array($serial_id));
		}

		$updateOrder = new stdClass();
		$updateOrder->order_id = (int)$order_id;
		$updateOrder->order_serial_params = json_encode($order_serial_params);
		$orderClass->save($updateOrder);

		return true;
	}
}
