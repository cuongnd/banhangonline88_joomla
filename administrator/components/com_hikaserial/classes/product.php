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
class hikaserialProductClass extends hikaserialClass {

	public function saveForm(&$product) {
		if(!isset($product->product_id))
			return;
		$product_id = (int)$product->product_id;

		$app = JFactory::getApplication();
		if(!$app->isAdmin()) {
			if(!hikaserial::initMarket())
				return;

			if(!hikamarket::acl('product_edit_plugin_hikaserial'))
				return;
		}

		$formData = JRequest::getVar('data', array(), '', 'array');
		if(empty($formData) || empty($formData['hikaserial']['form']))
			return;

		$serialData = $formData['hikaserial'];

		if(isset($serialData[$product_id]))
			$serialData = $serialData[$product_id];
		else
			$serialData = array();

		$packs = array();
		if(isset($serialData['pack_qty']) && isset($serialData['pack_id'])) {
			$packs = array_combine($serialData['pack_id'], $serialData['pack_qty']);
		}

		$query = 'DELETE FROM ' . hikaserial::table('product_pack') . ' WHERE product_id = ' . $product_id;
		$this->db->setQuery($query);
		$this->db->query();

		if(!empty($packs)) {
			$query = 'INSERT IGNORE INTO ' . hikaserial::table('product_pack') . ' (`product_id`, `pack_id`, `quantity`) VALUES ';
			$data = array();
			foreach($packs as $id => $qty) {
				if((int)$qty > 0) {
					$data[] = '(' . $product_id . ', ' . (int)$id . ', ' .(int)$qty . ')';
				}
				if(count($data) >= 50) {
					$this->db->setQuery($query . implode(',', $data));
					$this->db->query();
					$data = array();
				}
			}
			if(count($data) > 0) {
				$this->db->setQuery($query . implode(',', $data));
				$this->db->query();
				$data = array();
			}
		}
		$this->refreshQuantity( (int)$product->product_id );
	}

	public function refreshQuantities() {
		return $this->refreshQuantity();
	}

	public function refreshQuantity($product = null, $packs = null) {
		$config = hikaserial::config();
		if($config->get('link_product_quantity', false) == false)
			return;

		$filters = array(
			'product_pack.qty' => 'pp.quantity > 0',
			'serial_status' => 's.serial_status = \'free\'',
		);

		if($product !== null) {
			if(is_object($product))
				$product_id = (int)$product->product_id;
			else
				$product_id = (int)$product;

			if(!empty($product_id))
				$filters['product_id'] = 'pp.product_id = ' . (int)$product_id;
		}

		if($packs !== null) {
			$pack_filter = '';
			if(is_array($packs)) {
				JArrayHelper::toInteger($packs);
				$pack_filter = 'pp.pack_id IN (' . implode(',', $packs) . ')';
			}
			if(is_numeric($packs)) {
				$pack_filter = 'pp.pack_id = ' . (int)$packs;
			}

			if(!empty($pack_filter)) {
				$query = 'SELECT DISTINCT pp.product_id FROM ' . hikaserial::table('product_pack') . ' AS pp WHERE ' . $pack_filter;
				$this->db->setQuery($query);
				if(!HIKASHOP_J25)
					$products = $this->db->loadResultArray();
				else
					$products = $this->db->loadColumn();

				JArrayHelper::toInteger($products);
				if($product !== null) {
					if(is_object($product))
						$products[] = (int)$product->product_id;
					else
						$products[] = (int)$product;
				}

				if(empty($products))
					return;

				$filters['product_id'] = 'pp.product_id IN (' . implode(',', $products) . ')';

				unset($products);
				unset($pack_filter);
			}
		}


		$query = 'SELECT pp.product_id, COUNT(s.serial_id) as serials, pa.pack_id, pp.quantity as pack_qty, pa.pack_generator, pa.pack_params ' .
			'FROM ' . hikaserial::table('product_pack') . ' AS pp ' .
			'INNER JOIN ' . hikaserial::table('pack') . ' AS pa ON pp.pack_id = pa.pack_id ' .
			'LEFT JOIN ' . hikaserial::table('serial') . ' AS s ON s.serial_pack_id = pa.pack_id ' .
			'WHERE (' . implode(') AND (', $filters) . ') '.
			'GROUP BY pp.product_id, pa.pack_id ' .
			'ORDER BY pp.product_id ASC, pack_qty ASC';

		$this->db->setQuery($query);
		$ret = $this->db->loadObjectList();

		$products = array();
		foreach($ret as $p) {
			if(isset($products[$p->product_id]) && isset($products[$p->product_id]->pack_qty) && $products[$p->product_id]->pack_qty >= 0)
				continue;

			if(!empty($p->pack_generator)) {
				$p->qty = -1;
			} else {
				$p->qty = (int)floor((int)$p->serials / (int)$p->pack_qty);
			}

			if(!empty($p->pack_params)) {
				$p->pack_params = hikaserial::unserialize($p->pack_params);
				if(!empty($p->pack_params->unlimited_quantity))
					$p->qty = -1;
			}

			if(!isset($products[(int)$p->product_id])) {
				$products[(int)$p->product_id] = $p->qty;
			} else {
				$q = (int)$products[(int)$p->product_id];
				if($p->qty >= 0 && $p->qty < $q)
					$products[(int)$p->product_id] = $p->qty;
			}
		}
		unset($ret);

		if(empty($products))
			return;

		$query = 'SELECT p.product_id, p.product_quantity ' .
			' FROM ' . hikaserial::table('shop.product') . ' AS p ' .
			' WHERE p.product_id IN (' . implode(',', array_keys($products)) . ')';
		$this->db->setQuery($query);
		$products_qty = $this->db->loadObjectList('product_id');

		foreach($products as $product_id => $qty) {
			if(!isset($products_qty[ (int)$product_id ]))
				continue;
			if((int)$products_qty[ (int)$product_id]->product_quantity == (int)$qty)
				continue;

			$this->db->setQuery('UPDATE ' . hikaserial::table('shop.product') . ' SET product_quantity = ' . $qty . ' WHERE product_id = ' . $product_id);
			$this->db->query();
		}
		unset($products);

		return;
	}
}
