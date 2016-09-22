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
class hikamarketCartClass extends hikamarketClass {

	protected $tables = array();
	protected $pkeys = array();
	protected $toggle = array();

	protected $config = null;
	static protected $zone_id = null;
	static protected $vendor_id = null;

	public function  __construct($config = array()) {
		parent::__construct($config);
		$this->config = hikamarket::config();
	}

	public function onAfterCartProductsLoad(&$cart) {
		if(!empty($cart->products))
			$this->manageZoneVendor($cart->products);
	}

	public function manageZoneVendor(&$products, $address = null) {
		if(empty($products) || !$this->config->get('allow_zone_vendor', 0))
			return;

		$zone_id = hikamarket::getZone('shipping');
		if(self::$zone_id != $zone_id) {
			self::$zone_id = $zone_id;
			self::$vendor_id = null;

			$zoneClass = hikamarket::get('shop.class.zone');
			$zones = $zoneClass->getZoneParents($zone_id);

			$zonesQuoted = array();
			foreach($zones as $z) {
				$zonesQuoted[] = $this->db->Quote($z);
			}

			$query = 'SELECT vendor.vendor_id, vendor.vendor_zone_id, zone.zone_namekey, zone.zone_type '.
				' FROM ' . hikamarket::table('vendor') . ' AS vendor '.
				' INNER JOIN ' . hikamarket::table('shop.zone') . ' AS zone ON vendor.vendor_zone_id = zone.zone_id '.
				' WHERE zone.zone_namekey IN ('.implode(',', $zonesQuoted).') ORDER BY vendor.vendor_id ASC';
			$this->db->setQuery($query);
			$vendors = $this->db->loadObjectList('zone_namekey');

			if(!empty($vendors)) {
				foreach($zones as $z) {
					if(isset($vendors[$z])) {
						self::$vendor_id = (int)$vendors[$z]->vendor_id;
						break;
					}
				}
			}
		}

		if(!empty(self::$vendor_id)) {
			foreach($products as &$product) {
				if($product->product_vendor_id == 0) // || $product->product_vendor_id == 1)
					$product->product_vendor_id = self::$vendor_id;
			}
			unset($product);
		}
	}

	public function onAfterProductQuantityCheck(&$product, &$wantedQuantity, &$quantity, &$cartContent, &$cart_product_id_for_product, &$displayErrors) {
		if(!empty($cartContent))
			$this->manageZoneVendor($cartContent);

		$limit_vendors_in_cart = $this->config->get('vendors_in_cart', 0);
		if($limit_vendors_in_cart == 0)
			return;

		$vendor_id = (int)$product->product_vendor_id;
		if($vendor_id == 1)
			$vendor_id = 0;

		if($limit_vendors_in_cart == 2 && $vendor_id == 0)
			return;

		$refuse = false;
		$vendor_ids = array();
		if(!empty($cartContent)) {
			foreach($cartContent as $p) {
				if((int)$p->cart_product_quantity == 0)
					continue;

				$v = (int)$p->product_vendor_id;

				if($v == 0 && $p->product_type == 'variant' && isset($cartContent[ (int)$p->cart_product_parent_id ])) {
					$v = (int)$cartContent[ (int)$p->cart_product_parent_id ]->product_vendor_id;
				}

				if($v == 1)
					$v = 0;

				if($v > 1)
					$vendor_ids[$v] = $v;
				else
					$vendor_ids[1] = 1;

				if($limit_vendors_in_cart == 2 && $v == 0)
					continue;

				if($v != $vendor_id) {
					$refuse = true;
					break;
				}
			}
		}

		if($refuse) {
			$quantity = 0;
			$displayErrors = false;

			$app = JFactory::getApplication();

			$this->db->setQuery('SELECT vendor_id, vendor_name FROM ' . hikamarket::table('vendor') . ' as v WHERE v.vendor_id IN (' . $vendor_id . ',' . implode(',', $vendor_ids) . ')');
			$vendors = $this->db->loadObjectList('vendor_id');

			$wantedVendor = $vendors[$vendor_id]->vendor_name;
			$otherVendor = null;
			foreach($vendors as $v) {
				if($v->vendor_id > 1 && $v->vendor_id != $vendor_id)
					$otherVendor = $v->vendor_name;
			}
			if($limit_vendors_in_cart == 2 && $otherVendor !== null && isset($vendors[1])) {
				$app->enqueueMessage(JText::sprintf('VENDOR_CART_PRODUCT_REFUSED_2', $product->product_name, $wantedVendor, $otherVendor, $vendors[1]->vendor_name));
			} else {
				if($otherVendor === null)
					$otherVendor = $vendors[1]->vendor_name;
				$app->enqueueMessage(JText::sprintf('VENDOR_CART_PRODUCT_REFUSED', $product->product_name, $wantedVendor, $otherVendor));
			}
		}
	}
}
