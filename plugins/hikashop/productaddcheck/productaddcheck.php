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
class plgHikashopProductaddcheck extends JPlugin {

	protected $usedSerials = array();

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	private function init() {
		static $init = null;
		if($init !== null)
			return $init;

		$init = defined('HIKASERIAL_COMPONENT');
		if(!$init) {
			$filename = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaserial'.DS.'helpers'.DS.'helper.php';
			if(file_exists($filename)) {
				include_once($filename);
				$init = defined('HIKASERIAL_COMPONENT');
			}
		}
		return $init;
	}

	protected function loadConsumerParams() {
		if(!$this->init())
			return;

		$db = JFactory::getDBO();
		$query = 'SELECT * FROM '.hikaserial::table('consumer').' WHERE consumer_published = 1 AND consumer_type = ' . $db->Quote('productaddconsumer');
		$db->setQuery($query);
		$plugins = $db->loadObjectList();
		if(!empty($plugins)) {
			foreach($plugins as &$plugin) {
				$plugin->consumer_params = hikaserial::unserialize($plugin->consumer_params);
			}
			unset($plugin);
		}
		return $plugins;
	}

	public function onBeforeOrderCreate(&$order, &$do) {
		if(!$this->init())
			return;

		$app = JFactory::getApplication();
		$plugins = $this->loadConsumerParams();
		$products_added = $app->getUserState('com_hikaserial.plg_productaddconsumer.products', array());
		if($app->isAdmin())
			return;

		$cart_products = array();
		if(!empty($order->cart)) {
			foreach($order->cart->products as $product) {
				if(!isset($cart_products[$product->product_id]))
					$cart_products[$product->product_id] = 0;
				$cart_products[$product->product_id] += $product->order_product_quantity;
			}
		}

		if(!empty($products_added)) {
			$db = JFactory::getDBO();
			$serials = array_keys($products_added);
			$serialConfig = hikaserial::config();
			$assigned_status = $serialConfig->get('assigned_serial_status', 'assigned');

			$query = 'SELECT serial_id FROM '.hikaserial::table('serial').' WHERE '.
				' serial_status = ' . $db->Quote($assigned_status) . ' AND (serial_user_id IS NULL OR serial_user_id = 0 OR serial_user_id = '.(int)$order->order_user_id.') AND serial_id IN ('.implode(',', $serials).')';

			$db->setQuery($query);
			if(!HIKASHOP_J25)
				$serials = $db->loadResultArray();
			else
				$serials = $db->loadColumn();

			foreach($products_added as $key => $value) {
				if(!in_array($key, $serials)) {
					$products_added[$key] = array();
					unset($products_added[$key]);
				}
			}
		}

		$err = false;
		$this->usedSerials = array();
		foreach($plugins as $plugin) {
			if(!empty($plugin->consumer_params->block_product) && isset($cart_products[$plugin->consumer_params->product_id])) {
				$qty = 0;
				foreach($products_added as $serial => $products) {
					if(in_array($plugin->consumer_params->product_id, $products)) {
						$qty++;
						$this->usedSerials[] = $serial;
						break;
					}
				}
				if($qty == 0 || ($qty > $cart_products[$plugin->consumer_params->product_id])) {
					$ret = $this->setProductFromCart((int)$plugin->consumer_params->product_id, $qty);
					$err = true;
					$do = false;
				}
			}
		}

		if($err) {
			$app->enqueueMessage(JText::_('SOME_PRODUCTS_REQUIRED_SERIAL_IN_SESSION'));
			$do = false;
		}
	}

	public function onAfterOrderCreate(&$order, &$send_email) {
		if(!empty($this->usedSerials)) {
			if(!$this->init())
				return;

			$serialConfig = hikaserial::config();
			$used_status = $serialConfig->get('used_serial_status', 'used');

			$db = JFactory::getDBO();
			$query = 'UPDATE '.hikaserial::table('serial').' SET serial_status = '.$db->Quote($used_status).' WHERE serial_id IN ('.implode(',', $this->usedSerials).')';
			$db->setQuery($query);
			$db->query();
		}

		$empty = array();
		$app = JFactory::getApplication();
		$app->setUserState('com_hikaserial.plg_productaddconsumer.products', $empty);
	}

	private function setProductFromCart($product_id, $qty) {
		$cartClass = hikashop_get('class.cart');
		$cartContent = $cartClass->get();
		$cart_product_id = 0;
		foreach($cartContent as $key => $cartEntry) {
			if($cartEntry->product_id == $product_id) {
				$cart_product_id = $key;
				$cartClass->updateEntry($qty, $cartContent, $cart_product_id, 0, false, 'cart');
				if($qty > 0)
					$qty -= $cartEntry->product_quantity;
				if($qty < 0)
					$qty = 0;
			}
		}
		return true;
	}
}
