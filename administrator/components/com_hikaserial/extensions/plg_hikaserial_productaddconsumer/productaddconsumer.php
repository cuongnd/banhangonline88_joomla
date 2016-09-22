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
class plgHikaserialProductaddconsumer extends hikaserialPlugin {

	protected $type = 'consumer';
	protected $multiple = true;

	protected $pluginConfig = array(
		'pack_id' => array('PACK', 'pack'),
		'product_id' => array('PRODUCT_ID', 'product'),
		'block_product' => array('BLOCK_PRODUCT', 'boolean'),
	);

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	public function onBeforeSerialConsume(&$serial, $user_id, &$do, &$extra_data) {
		if(empty($user_id)) {
			$user_id = hikaserial::loadUser();
		}

		$app = JFactory::getApplication();
		$products_added = array();
		$ids = array();
		$product_qty = array();

		$current_products = $app->getUserState('com_hikaserial.plg_productaddconsumer.products', array());
		if(!empty($current_products)) {
			$current_products[$serial->serial_id] = array();
			foreach($current_products as $serial_id => $products) {
				foreach($products as $product_id) {
					if(empty($product_qty[$product_id]))
						$product_qty[$product_id] = 0;
					$product_qty[$product_id]++;
				}
			}
		}

		parent::listPlugins('productaddconsumer', $ids, false);
		foreach($ids as $id) {
			parent::pluginParams($id);
			if($this->plugin_params->pack_id == $serial->serial_pack_id) {

				if(empty($user_id)) {
					$app->enqueueMessage(JText::_('PLEASE_LOGIN'));
					$do = false;
					return;
				} elseif($serial->serial_user_id != 0 && $serial->serial_user_id != $user_id) {
					$app->enqueueMessage(JText::_('NOT_YOUR_SERIAL'));
					$do = false;
					return;
				}

				if(empty($product_qty[$this->plugin_params->product_id]))
					$product_qty[$this->plugin_params->product_id] = 1;
				else
					$product_qty[$this->plugin_params->product_id]++;

				$this->addProductToCart($this->plugin_params->product_id, $product_qty[$this->plugin_params->product_id]);
				if(empty($products_added[$serial->serial_id]))
					$products_added[$serial->serial_id] = array();
				$products_added[$serial->serial_id][] = $this->plugin_params->product_id;
			}
		}

		if(!empty($products_added)) {
			if(!empty($current_products)) {
				foreach($current_products as $serial_id => $products) {
					if(empty($products_added[$serial_id]))
						$products_added[$serial_id] = $products;
					else
						$products_added[$serial_id] = array_merge($products_added[$serial_id], $products);
				}
			}

			$app->enqueueMessage(JText::_('PRODUCT_ADDED_TO_YOUR_CART'));
			$app->setUserState('com_hikaserial.plg_productaddconsumer.products', $products_added);

			$serial->managed = true;

			if(empty($serial->serial_user_id)) {
				$updateSerial = new stdClass();
				$updateSerial->serial_id = $serial->serial_id;
				$updateSerial->serial_user_id = $user_id;

				$serialClass = hikaserial::get('class.serial');
				$serialClass->save($updateSerial);
			}
		}
	}

	private function addProductToCart($product_id, $qty = 1) {
		$cartClass = hikaserial::get('shop.class.cart');
		$cartContent = $cartClass->get(0, true, 'cart');
		if(empty($cartContent)) {
			$cartClass->initCart();
		}
		foreach($cartContent as $key => $cartEntry) {
			if($cartEntry->product_id == $product_id) {
				$cart_product_id = $key;
				$cartClass->updateEntry($qty, $cartContent, $cart_product_id, 0, false, 'cart');
				$qty = 0;
			}
		}
		if($qty > 0)
			return $cartClass->updateEntry($qty, $cartContent, $product_id, 0, false);
		return true;
	}
}
