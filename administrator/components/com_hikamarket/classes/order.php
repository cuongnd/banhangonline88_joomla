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
class hikamarketOrderClass extends hikamarketClass {

	protected $tables = array('shop.order_product', 'shop.order');
	protected $pkeys = array('order_id', 'order_id');

	private static $creatingSubSales = false;
	private static $events = array();

	public function frontSaveForm() {
		self::$events = array();

		if(!hikamarket::loginVendor())
			return false;

		$config = hikamarket::config();

		$vendor_id = hikamarket::loadVendor(false);
		if($vendor_id == 1)
			$vendor_id = 0;

		$order_id = hikamarket::getCID('order_id');

		if(empty($order_id) || !hikamarket::isVendorOrder($order_id))
			return false;

		$block = JRequest::getCmd('block', '');

		$orderClass = hikamarket::get('shop.class.order');
		$fieldsClass = hikamarket::get('shop.class.field');
		jimport('joomla.filter.filterinput');
		$safeHtmlFilter = JFilterInput::getInstance(null, null, 1, 1);

		$do = false;
		$no_modification = false;
		$fields = array();
		$order = new stdClass();
		$order->hikamarket = new stdClass();

		$order->old = $this->getRaw($order_id);

		$editable_order = true;
		if($vendor_id > 1 || $order->old->order_type == 'subsale') {
			$editable_order = false;
			$vendors_in_cart = (int)$config->get('vendors_in_cart', 0);
			if($vendors_in_cart == 1 && ((int)$config->get('vendor_edit_order', 0) == 1)) {
				$order_vendor_id = ($vendor_id > 1) ? $vendor_id : (int)$order->old->order_vendor_id;
				$editable_order = hikamarket::isEditableOrder($order_id, $order_vendor_id);
			}
		}

		if(!empty($order->old->order_tax_info) && is_string($order->old->order_tax_info))
			$order->old->order_tax_info = hikamarket::unserialize($order->old->order_tax_info);
		if(!empty($order->old->order_payment_params) && is_string($order->old->order_payment_params))
			$order->old->order_payment_params = hikamarket::unserialize($order->old->order_payment_params);
		if(!empty($order->old->order_shipping_params) && is_string($order->old->order_shipping_params))
			$order->old->order_shipping_params = hikamarket::unserialize($order->old->order_shipping_params);

		$order->order_id = $order_id;
		$order->history = new stdClass();
		$order->history->history_type = 'modification';
		$order->history->history_notified = false;
		$order->history->history_reason = array();
		$order->history->history_data = array();

		$order->old->order_discount_tax_namekey = '';
		$order->old->order_payment_tax_namekey = '';
		$order->old->order_shipping_tax_namekey = array();
		if(!empty($order->old->order_tax_info)) {
			foreach($order->old->order_tax_info as $k => $v) {
				if(isset($v->tax_amount_for_coupon) && empty($order->old->order_discount_tax_namekey))
					$order->old->order_discount_tax_namekey = $k;
				if(isset($v->tax_amount_for_payment) && empty($order->old->order_payment_tax_namekey))
					$order->old->order_payment_tax_namekey = $k;
				if(isset($v->tax_amount_for_shipping))
					$order->old->order_shipping_tax_namekey[$k] = $v->tax_amount_for_shipping;
			}
		}
		if(count($order->old->order_shipping_tax_namekey) == 1) {
			$keys = array_keys($order->old->order_shipping_tax_namekey);
			$order->old->order_shipping_tax_namekey = reset($keys);
			unset($keys);
		}

		if(empty($order_id) || empty($order->order_id))
			$orderClass->sendEmailAfterOrderCreation = false;

		$data = JRequest::getVar('order', array(), '', 'array');

		if($block == 'delete_product' && $editable_order) {
			$data = array();

			$pid = JRequest::getInt('pid', 0);
			$product_hash = JRequest::getString('product_hash', '');
			if(empty($product_hash) || empty($pid) || empty($order->old) || !hikamarket::acl('order/edit/products'))
				return false;

			$query = 'SELECT * FROM ' . hikamarket::table('shop.order_product') . ' WHERE order_id = ' . (int)$order_id . ' AND order_product_id = ' . (int)$pid;
			$this->db->setQuery($query);
			$order_product = $this->db->loadObject();
			$local_hash = md5((int)$order_product->order_product_id . '#' . (int)$order_product->order_id . '#' . (int)$order->old->order_modified);

			if($local_hash != $product_hash)
				return false;

			$do = true;

			$order->history->history_reason[] = 'Order product deleted';
			$order->history->history_data['product_delete'] = array(
				'product' => array(
					'id' => (int)$order_product->product_id,
					'name' => $order_product->order_product_name,
					'code' => $order_product->order_product_code,
					'qty' => (int)$order_product->order_product_quantity,
					'price' => (float)hikamarket::toFloat($order_product->order_product_price)
				)
			);
			$this->addEvent('orderMgr.details', array(
				'src' => $block
			));

			$order_product->order_product_quantity = 0;
			$order->product = array(
				$order_product
			);
		}

		if(hikamarket::acl('order/edit/general') && !empty($data['general'])) {
			$order_status = $data['general']['order_status'];

			if($order->old->order_type == 'subsale' && $order->old->order_vendor_paid > 0 && $config->get('filter_orderstatus_paid_order', 1)) {
				$valid_order_statuses = explode(',', $config->get('valid_order_statuses', 'confirmed,shipped'));
				if(!in_array($order->old->order_status, $valid_order_statuses) || !in_array($order_status, $valid_order_statuses)) {
					$order_status = null;
				}
			}

			if($this->isValidOrderStatus($order_status)) {
				$order->order_status = $order_status;
				$do = true;

				$order->history->history_reason[] = 'Order status modified';
				$order->history->history_data['order_status'] = array(
					'old' => $order->old->order_status,
					'new' => $order->order_status
				);
				$this->addEvent('orderMgr.order_status', array(
					'src' => $block,
					'old' => (int)$order->old->order_status,
					'new' => $order->order_status,
				));
			}
		}

		if(hikamarket::acl('order/edit/customer') && !empty($data['customer']['user_id']) && $editable_order) {
			$user_id = (int)$data['customer']['user_id'];
			if($user_id > 0 && (int)$order->old->order_user_id != $user_id && hikamarket::isVendorCustomer($user_id)) {
				$order->order_user_id = $user_id;
				$do = true;

				$order->history->history_reason[] = 'Customer modified';
				$order->history->history_data['customer'] = array(
					'old' => (int)$order->old->order_user_id,
					'new' => $user_id
				);
				$this->addEvent('orderMgr.customer', array(
					'src' => $block,
					'old' => (int)$order->old->order_user_id,
					'id' => $user_id,
				));

				if(!empty($data['customer']['addrlink'])) {
					$addressClass = hikamarket::get('shop.class.address');
					$addresses = $addressClass->getByUser( (int)$user_id );
					if(!empty($addresses)) {
						$addr = reset($addresses);

						$order->order_billing_address_id = (int)$addr->address_id;
						$order->history->history_data['billing_address'] = array(
							'old' => (int)$order->old->order_billing_address_id,
							'new' => (int)$addr->address_id
						);
						$this->addEvent('orderMgr.shippingaddress', array(
							'src' => $block,
							'old' => (int)$order->old->order_billing_address_id,
							'id' => (int)$addr->address_id
						));

						$order->order_shipping_address_id = (int)$addr->address_id;
						$order->history->history_data['shipping_address'] = array(
							'old' => (int)$order->old->order_shipping_address_id,
							'new' => (int)$addr->address_id
						);
						$this->addEvent('orderMgr.billingaddress', array(
							'src' => $block,
							'old' => (int)$order->old->order_shipping_address_id,
							'id' => (int)$addr->address_id
						));
					}
				}
			}
		}

		if(hikamarket::acl('order/edit/billingaddress') && !empty($data['billingaddress'])) {
			if($this->processAddressFormData($order, 'billing', $data['billingaddress'], $block))
				$do = true;
		}

		if(hikamarket::acl('order/edit/shippingaddress') && !empty($data['shippingaddress'])) {
			if($this->processAddressFormData($order, 'shipping', $data['shippingaddress'], $block))
				$do = true;
		}

		if(hikamarket::acl('order/edit/coupon') && !empty($data['coupon']) && $editable_order) {
			$coupon = $this->processFormData($order, $data['coupon'], array(
				'order_discount_code' => array(
					'default' => '',
					'field' => 'code',
					'type' => 'text'
				),
				'order_discount_price' => array(
					'default' => 0.0,
					'field' => 'value',
					'type' => 'price'
				),
				'order_discount_tax' => array(
					'default' => 0.0,
					'field' => 'tax',
					'type' => 'price'
				),
				'order_discount_tax_namekey' => array(
					'default' => '',
					'field' => 'tax_namekey',
					'type' => 'tax_namekey'
				),
			));

			if($coupon !== false && !empty($coupon)) {
				$do = true;

				$order->history->history_reason[] = 'Coupon modified';
				$order->history->history_data['coupon'] = array(
					'old' => $coupon['old'],
					'new' => $coupon['new']
				);
				$this->addEvent('orderMgr.details', array(
					'src' => $block
				));
			}
		}

		if(hikamarket::acl('order/edit/shipping') && !empty($data['shipping']) && $editable_order) {
			$shipping = $this->processFormData($order, $data['shipping'], array(
				'order_shipping_id' => array(
					'default' => 0,
					'field' => 'id',
				),
				'order_shipping_method' => array(
					'default' => '',
					'field' => 'namekey',
					'type' => 'shipping_method',
					'link' => 'order_shipping_id'
				),
				'order_shipping_price' => array(
					'default' => 0.0,
					'field' => 'value',
					'type' => 'price'
				),
				'order_shipping_tax' => array(
					'default' => 0.0,
					'field' => 'tax',
					'type' => 'price'
				),
				'order_shipping_tax_namekey' => array(
					'default' => '',
					'field' => 'tax_namekey',
					'type' => 'tax_namekey'
				),
			));

 			if($shipping !== false && !empty($shipping)) {
 				$do = true;

				$order->history->history_reason[] = 'Shipping modified';
				$order->history->history_data['shipping'] = array(
					'old' => $shipping['old'],
					'new' => $shipping['new']
				);
				$this->addEvent('orderMgr.details', array(
					'src' => $block
				));
			}

		} elseif(hikamarket::acl('order/edit/shipping') && !empty($data['shippings']) && $editable_order) {
			$shippings = $this->processShippingsFormData($order, $data['shippings']);
 			if($shippings !== false && !empty($shippings)) {
 				$do = true;

				$order->history->history_reason[] = 'Shipping modified';
				$order->history->history_data['shipping'] = array(
					'old' => $shipping['old'],
					'new' => $shipping['new']
				);
				$this->addEvent('orderMgr.details', array(
					'src' => $block
				));
			}
		}

		if(hikamarket::acl('order/edit/payment') && !empty($data['payment']) && $editable_order) {
			$payment = $this->processFormData($order, $data['payment'], array(
				'order_payment_id' => array(
					'default' => 0,
					'field' => 'id',
				),
				'order_payment_method' => array(
					'default' => '',
					'field' => 'namekey',
					'type' => 'payment_method',
					'link' => 'order_payment_id'
				),
				'order_payment_price' => array(
					'default' => 0.0,
					'field' => 'value',
					'type' => 'price'
				),
				'order_payment_tax' => array(
					'default' => 0.0,
					'field' => 'tax',
					'type' => 'price'
				),
				'order_payment_tax_namekey' => array(
					'default' => '',
					'field' => 'tax_namekey',
					'type' => 'tax_namekey'
				),
			));

 			if($payment !== false && !empty($payment)) {
				$do = true;

				$order->history->history_reason[] = 'Payment modified';
				$order->history->history_data['payment'] = array(
					'old' => $payment['old'],
					'new' => $payment['new']
				);
				$this->addEvent('orderMgr.details', array(
					'src' => $block
				));
			}
		}

		if(hikamarket::acl('order/edit/customfields') && !empty($data['field'])) {
			$null = null;

			$orderFields = $fieldsClass->getInput(array('field','order'), $null, true, 'order', false, 'display:vendor_order_edit=1');

			if(!empty($orderFields)) {
				$old_fields = array();
				$new_fields = array();

				foreach($orderFields as $key => $value) {
					$order->$key = $value;
					if(!isset($order->old->$key) || $value != $order->old->$key) {
						$new_fields[$key] = $value;
						$old_fields[$key] = @$order->old->$key;
					}
				}

				if(!empty($new_fields)) {
					$do = true;

					$order->history->history_reason[] = 'Custom fields modified';
					$order->history->history_data['fields'] = array(
						'old' => $old_fields,
						'new' => $new_fields
					);
					$this->addEvent('orderMgr.fields', array(
						'src' => $block
					));
				}

				if(!$do)
					$no_modification = true;
			}
		}

		if(hikamarket::acl('order/edit/products') && !empty($data['products']) && $editable_order) {
			$query = 'SELECT * FROM ' . hikamarket::table('shop.order_product') . ' WHERE order_id = ' . (int)$order_id;
			$this->db->setQuery($query);
			$order->old->order_products = $this->db->loadObjectList('order_product_id');

			$null = null;
			$itemFieldsCat = $fieldsClass->getCategories('item', $null);
			$fields['item'] = $fieldsClass->getData('display:vendor_order_edit=1', 'item', $itemFieldsCat);

			$order->product = array();
			$order->hikamarket->products = array();
			foreach($data['products'] as $order_product_id => $order_product) {
				if((int)$order_product_id > 0 && !isset($order->old->order_products[ (int)$order_product_id ]))
					continue;

				$product = new stdClass();

				$oldData = new stdClass();
				if((int)$order_product_id > 0)
					$oldData = $order->old->order_products[ (int)$order_product_id ];
				$fields_checked = $fieldsClass->_checkOneInput($fields['item'], $order_product['field'], $product, 'item', $oldData);

				$product->order_id = (int)$order_id;
				if(!empty($order_product_id) && (int)$order_product_id > 0)
					$product->order_product_id = (int)$order_product_id;

				$product->product_id = (int)$order_product['id'];
				$product->order_product_name = $safeHtmlFilter->clean(trim($order_product['name']), 'string');
				$product->order_product_code = $safeHtmlFilter->clean(strip_tags(trim($order_product['code'])), 'string');
				$product->order_product_quantity = (int)$order_product['qty'];

				$product->order_product_price = (float)hikamarket::toFloat(trim($order_product['value']));
				$product->order_product_tax = (float)hikamarket::toFloat(trim($order_product['tax']));
				if(!empty($product->order_product_tax))
					$product->order_product_price -= $product->order_product_tax;

				$product->tax_namekey = $safeHtmlFilter->clean(strip_tags(trim($order_product['tax_namekey'])), 'string');

				if($vendor_id <= 1 && hikamarket::acl('order/edit/vendor') && isset($order_product['vendor_id'])) {
					$ref = uniqid();

					$order->hikamarket->products[(int)$order_product_id] = array(
						'vendor_id' =>  (int)$order_product['vendor_id'],
						'vendor_price' => (float)hikamarket::toFloat(trim($order_product['vendor_price'])),
						'product_id' => (int)$order_product['id'],
						'order_product_id' => (int)$order_product_id,
						'order_product_quantity' => (int)$order_product['qty'],
						'ref' => $ref
					);

					$product->hikamarket = new stdClass();
					$product->hikamarket->ref = $ref;
				}

				if((int)$order_product_id > 0) {
					$diff = false;
					foreach($product as $k => $v) {
						if($v == $oldData->$k) {
							$diff = true;
							break;
						}
					}
				} else
					$diff = true;

				if($diff)
					$order->product[] =& $product;

				unset($product);
			}

			if(!empty($order->product)) {
				$do = true;
				if(count($order->product) == 1 && empty($order->product[0]->order_product_id)) {
					$order->history->history_reason[] = 'Order product added';
					$order->history->history_data['product'] = array(
					);
				} else {
					$order->history->history_reason[] = 'Order product modified';
					$order->history->history_data['product'] = array(
					);
				}
				$this->addEvent('orderMgr.details', array(
					'src' => $block
				));
			}

		}

		if(!$do && $no_modification)
			return true;

		if(!$do)
			return false;

		$order->history->history_reason = (count($order->history->history_reason) == 1) ? reset($order->history->history_reason) : 'Order modified';
		$order->history->history_data = json_encode($order->history->history_data);

		if(isset($order->order_shipping_price) || isset($order->order_payment_price) || isset($order->order_discount_price)) {
			if(!isset($order->order_payment_tax_namekey) && isset($order->old->order_payment_tax_namekey)) {
				$order->order_payment_tax = $order->old->order_payment_tax;
				$order->order_payment_tax_namekey = $order->old->order_payment_tax_namekey;
			}
			if(!isset($order->order_discount_tax_namekey) && isset($order->old->order_discount_tax_namekey)) {
				$order->order_discount_tax = $order->old->order_discount_tax;
				$order->order_discount_tax_namekey = $order->old->order_discount_tax_namekey;
			}
			if(!isset($order->order_shipping_tax_namekey) && isset($order->old->order_shipping_tax_namekey)) {
				$order->order_shipping_tax = $order->old->order_shipping_tax;
				$order->order_shipping_tax_namekey = $order->old->order_shipping_tax_namekey;
			}
		}
		unset($order->old->order_discount_tax_namekey);
		unset($order->old->order_shipping_tax_namekey);
		unset($order->old->order_payment_tax_namekey);

		$status = $orderClass->save($order);
		if(!$status)
			return false;

		$order->order_id = (int)$status;

		$this->addEvent('orderMgr.history', null);

		return $order;
	}

	protected function processFormData(&$order, $data, $conf) {
		$safeHtmlFilter = JFilterInput::getInstance(null, null, 1, 1);

		foreach($conf as $k => $v) {
			if(!isset($order->$k) && isset($v['default']))
				$order->$k = $v['default'];

			$f = $v['field'];
			$t = isset($v['type']) ? $v['type'] : null;
			if($f !== null && $t !== null && isset($data[$f])) {
				switch($t) {
					case 'price':
						$order->$k = (float)hikamarket::toFloat(trim($data[$f]));
						break;

					case 'tax_namekey':
						$order->$k = $safeHtmlFilter->clean(strip_tags(trim($data[$f])), 'string');
						break;

					case 'shipping_method':
					case 'payment_method':
						$blocks = explode('_', trim($data[$f]));
						$link = $v['link'];
						$order->$link = array_pop($blocks);
						$order->$k = implode('_', $blocks);
						break;

					case 'text':
					default:
						$order->$k = $safeHtmlFilter->clean(strip_tags(trim($data[$f])), 'string');
						break;
				}
			} elseif(isset($order->old->$k))
				$order->$k = $order->old->$k;
		}

		$new = array();
		$old = array();
		foreach($conf as $k => $v) {
			if(!isset($order->old->$k) || $order->$k != $order->old->$k) {
				$f = $v['field'];

				$new[$f] = $order->$k;
				$old[$f] = @$order->old->$k;
			}
		}

		if(empty($new))
			return false;

		foreach($conf as $k => $v) {
			if(!isset($v['link']))
				continue;
			$f = $v['field'];
			$l = $conf[ $v['link'] ]['field'];
			if(isset($new[$f]) || isset($new[$l])) {
				$new[$f] = $order->$k;
				$old[$f] = @$order->old->$k;

				$new[$l] = $order->$v['link'];
				$old[$l] = @$order->old->$v['link'];
			}
		}

		return array('old' => $old, 'new' => $new);
	}

	protected function processShippingsFormData(&$order, $data) {
		$safeHtmlFilter = JFilterInput::getInstance(null, null, 1, 1);

		$order->order_shipping_id = array();
		$order->order_shipping_method = '';
		$order->order_shipping_price = 0.0;
		$order->order_shipping_tax = 0.0;
		$order->order_shipping_tax_namekey = array();
		$order->order_shipping_params = new stdClass();

		if(!empty($order->old->order_shipping_params))
			$order->order_shipping_params = clone($order->old->order_shipping_params);
		if(empty($order->order_shipping_params))
			$order->order_shipping_params = new stdClass();
		$order->order_shipping_params->prices = array();

		foreach($data as $key => $shipping_data) {
			$blocks = explode('_', trim($shipping_data['namekey']));
			$shipping_id = array_pop($blocks);

			$order->order_shipping_id[] = $shipping_id . '@' . $key;

			$shipping_price = (float)hikamarket::toFloat(trim($shipping_data['value']));
			$shipping_tax = (float)hikamarket::toFloat(trim($shipping_data['tax']));
			$shipping_tax_namekey = $safeHtmlFilter->clean(strip_tags(trim($shipping_data['tax_namekey'])), 'string');

			$order->order_shipping_price += $shipping_price;
			$order->order_shipping_tax += $shipping_tax;
			if(!isset($order->order_shipping_tax_namekey[ $shipping_tax_namekey ]))
				$order->order_shipping_tax_namekey[ $shipping_tax_namekey ] = 0.0;
			$order->order_shipping_tax_namekey[ $shipping_tax_namekey ] += $shipping_tax;

			$prices = new stdClass();
			$prices->price_with_tax = $shipping_price;
			$prices->tax = $shipping_tax;
			$prices->taxes = array( $shipping_tax_namekey => $shipping_tax );
			$order->order_shipping_params->prices[ $shipping_id . '@' . $key ] = $prices;
		}

		if(count($order->order_shipping_tax_namekey) == 1) {
			$keys = array_keys($order->order_shipping_tax_namekey);
			$order->order_shipping_tax_namekey = reset($keys);
			unset($keys);
		}

		$order->order_shipping_id = implode(';', $order->order_shipping_id);

		$old_hash = '';
		if(!empty($order->old->order_shipping_params->prices))
			$old_hash = serialize($order->old->order_shipping_params->prices);
		$new_hash = serialize($order->order_shipping_params->prices);
		if($old_hash == $new_hash)
			return false;

		$new = array();
		$old = array();

		return array('old' => $old, 'new' => $new);
	}

	protected function processAddressFormData(&$order, $type, $data, $block) {
		$address_id = (int)$data['address_id'];
		$addressClass = hikamarket::get('shop.class.address');

		$order_user_id = !empty($order->order_user_id) ? (int)$order->order_user_id : (int)$order->old->order_user_id;

		$address_id_field = 'order_'.$type.'_address_id';
		$other_type = (($type == 'billing') ? 'shipping' : 'billing');
		$other_address_id_field = 'order_'.$other_type.'_address_id';

		if(!empty($data['addrselect'])) {
			if($address_id == (int)$order->old->$address_id_field && (empty($data['addrselect']) || $address_id == (int)$order->old->$other_address_id_field))
				return false;

			$address = $addressClass->get($address_id);

			if(empty($address) || empty($address->address_published) || (int)$address->address_user_id != $order_user_id)
				return false;

			if(!empty($address->address_type) && $address->address_type != $type)
				return false;

		} else {
			if($address_id != (int)$order->old->$address_id_field)
				return false;

			$address = $addressClass->get($address_id);

			$fieldsClass = hikamarket::get('shop.class.field');
			$new_address = $fieldsClass->getInput(array($type.'address', 'address'), $address, true, 'order', false, 'backend');
			if(empty($new_address))
				return false;

			$new_address->address_type = $address->address_type;
			unset($new_address->address_default);

			$status = $addressClass->save($new_address, $order->order_id, $type);
			if(!$status)
				return false;

			$address_id = $status;
		}

		$order->$address_id_field = $address_id;
		$order->history->history_reason[] = ucfirst($type) . ' Address modified';
		$order->history->history_data[$type.'_address'] = array(
			'old' => (int)$order->old->$address_id_field,
			'new' => $address_id
		);
		$this->addEvent('orderMgr.'.$type.'address', array(
			'src' => $block,
			'old' => (int)$order->old->$address_id_field,
			'id' => (int)$address_id
		), true);

		if(empty($data['addrlink']) || !empty($address->address_type) || !empty($order->$other_address_id_field))
			return true;

		if(empty($data['addrselect']) && $order->old->$address_id_field != $order->old->$other_address_id_field)
			return true;

		$order->$other_address_id_field = $address_id;
		$order->history->history_data[$other_type.'_address'] = array(
			'old' => (int)$order->old->$other_address_id_field,
			'new' => $address_id
		);
		$this->addEvent('orderMgr.'.$other_type.'address', array(
			'src' => $block,
			'old' => (int)$order->old->$other_address_id_field,
			'id' => (int)$address_id
		), true);

		return true;
	}

	public function addEvent($name, $params = null, $force = false) {
		if(isset(self::$events[$name]) && !$force)
			return false;
		self::$events[$name] = $params;
		return true;
	}

	public function getEvents() {
		return self::$events;
	}

	public function frontSaveFormLegacy($task = '', $acl = true) {
		$shopOrderBackendHelper = hikamarket::get('helper.shop-order_backend');
		if($shopOrderBackendHelper)
			return $shopOrderBackendHelper->frontSaveFormLegacy($this, $task, $acl);
		return false;
	}

	public function save(&$order) {
		return false;
	}

	private function saveRaw(&$order) {
		return parent::save($order);
	}

	public function delete(&$elements) {
		return false;
	}

	public function beforeCreate(&$order, &$do) {
		$this->beforeUpdate($order, $do, true);
	}

	public function beforeUpdate(&$order, &$do, $creation = false) {
		$order_type = (!empty($order->order_type)) ? $order->order_type : @$order->old->order_type;

		if(empty($order->hikamarket))
			$order->hikamarket = new stdClass();

		$app = JFactory::getApplication();
		$ctrl = JRequest::getCmd('ctrl', '');
		$task = JRequest::getCmd('task', '');
		if($app->isAdmin() && $ctrl == 'order' && $task == 'save') {
			$shopOrderBackendHelper = hikamarket::get('helper.shop-order_backend');
			if($shopOrderBackendHelper)
				$shopOrderBackendHelper->processAdminForm($order, $do, 'order');

			if($order_type == 'subsale' && !empty($order->hikamarket->products) && !empty($order->hikamarket->products[0])) {
				$do = false;
				return;
			}
		}

		switch($order_type) {
			case 'sale':
				return $this->beforeUpdateSale($order, $do, $creation);

			case 'subsale':
				return $this->beforeUpdateSubsale($order, $do, $creation);

			case 'vendorrefund':
				if(empty($order->hikamarket->internal_process) || !$order->hikamarket->internal_process)
					$do = false;
				return;
		}
	}

	private function beforeUpdateSale(&$order, &$do, $creation = false) {
		if(empty($order->hikamarket->children) && !empty($order->order_id)) {
			$query = 'SELECT o.* FROM ' . hikamarket::table('shop.order') . ' AS o '.
					' WHERE o.order_type = '. $this->db->Quote('subsale') .' AND o.order_parent_id = ' . (int)$order->order_id;
			$this->db->setQuery($query);
			$order->hikamarket->children = $this->db->loadObjectList('order_id');

			foreach($order->hikamarket->children as &$suborder) {
				if(!empty($suborder->order_tax_info))
					$suborder->order_tax_info = hikamarket::unserialize($suborder->order_tax_info);
			}
			unset($suborder);
		}

		if(empty($order->hikamarket->refunds)  && !empty($order->order_id)) {
			$this->loadOrderRefunds($order);
		}
	}

	private function beforeUpdateSubsale(&$order, &$do, $creation = false) {
		$vendor_id = 0;
		if(!empty($order->order_vendor_id)) {
			$vendor_id = (int)$order->order_vendor_id;
		} else {
			$vendor_id = (int)$order->old->order_vendor_id;
		}

		if(!empty($order->old->order_vendor_paid) && $order->old->order_vendor_paid > 0 && (!isset($order->hikamarket->internal_process) || $order->hikamarket->internal_process === false) ) {

			if(isset($order->order_vendor_price)) {
				$do = false;
				return;
			}
		}

		if($vendor_id > 0 && !empty($order->order_status) && empty($order->order_invoice_id) && empty($order->old->order_invoice_id)) {
			$shopConfig = hikamarket::config(false);
			$invoice_statuses = explode(',', $shopConfig->get('invoice_order_statuses','confirmed,shipped'));
			if(empty($invoice_statuses))
				$invoice_statuses = array('confirmed','shipped');
			$excludeFreeOrders = $shopConfig->get('invoice_exclude_free_orders', 0);
			if(isset($order->order_full_price))
				$total = $order->order_full_price;
			else
				$total = $order->old->order_full_price;

			if(in_array($order->order_status, $invoice_statuses) && ($total > 0 || !$excludeFreeOrders)) {
				$this->generateOrderInvoiceNumber($order, $vendor_id);
			}
		}

		if(empty($order->hikamarket->parent)) {
			$parent_id = 0;
			if(!empty($order->old->order_parent_id))
				$parent_id = (int)$order->old->order_parent_id;
			if(!empty($order->order_parent_id))
				$parent_id = (int)$order->order_parent_id;
			$query = 'SELECT * FROM ' . hikamarket::table('shop.order') . ' AS a WHERE order_id = ' . $parent_id;
			$this->db->setQuery($query);
			$order->hikamarket->parent = $this->db->loadObject();
		}
	}

	public function afterCreate(&$order, &$send_email) {
		if(empty($order) || empty($order->order_type))
			return;
		if($order->order_type == 'subsale')
			$send_email = false;

		if(isset($order->hikamarket->do_not_process))
			return;

		if($order->order_type == 'sale') {
			$this->afterCreateSale($order, $send_email);
		}

		if($order->order_type == 'subsale') {
			$this->afterCreateSubsale($order, $send_email);
		}

		if(!empty($order->hikamarket) && isset($order->hikamarket->send_email) && $order->hikamarket->send_email === true) {

			$config = hikamarket::config();
			$statuses = $config->get('vendor_email_order_status_notif_statuses', '');
			if(!empty($statuses))
				$statuses = explode(',', $statuses);

			if(!isset($order->hikamarket->vendor)) {
				$vendorClass = hikamarket::get('class.vendor');
				$vendor_id = (int)@$order->old->order_vendor_id;
				if(isset($order->order_vendor_id))
					$vendor_id = (int)$order->order_vendor_id;
				$order->hikamarket->vendor = $vendorClass->get( $vendor_id );
			}

			if(!empty($order->hikamarket->vendor)) {
				if(!empty($order->hikamarket->vendor->vendor_params->notif_order_statuses))
					$statuses = explode(',', $order->hikamarket->vendor->vendor_params->notif_order_statuses);

				if(empty($statuses) || in_array($order->order_status, $statuses)) {
					$mailClass = hikamarket::get('class.mail');
					$mailClass->sendVendorOrderEmail($order);
				}
			}
		}
	}

	private function afterCreateSale(&$order, &$send_email) {
		if(empty($order->cart->products))
			return;

		$products = array();
		foreach($order->cart->products as $product) {
			$products[(int)$product->cart_product_id] = array(
				'_id' => (int)$product->cart_product_id,
				'id' => (int)$product->product_id,
				'vendor' => null,
				'fee' => array(),
				'qty' => (int)$product->order_product_quantity,
				'price' => (float)hikamarket::toFloat($product->order_product_price),
				'price_tax' => (float)hikamarket::toFloat($product->order_product_tax),
			);
		}

		$vendors = $this->getVendorsByProducts($products, $order);
		$vendor_ids = array_keys($vendors);

		if(empty($vendors) || count($vendors) == 1)
			return;

		$feeClass = hikamarket::get('class.fee');
		$allFees = $feeClass->getProducts($products, $vendor_ids);

		if(!empty($order->order_discount_code) && empty($order->cart->coupon)) {
		}
		if(!empty($order->order_discount_code) && !empty($order->cart->coupon) && (int)$order->cart->coupon->discount_target_vendor >= 1) {
			$order->cart->coupon->products_full_price = 0.0;

			if(!empty($order->cart->coupon->products)) {
				foreach($order->cart->coupon->products as $p) {
					$order->cart->coupon->products_full_price += (int)$p->cart_product_quantity * (float)$p->prices[0]->price_value;
				}
			} else {
				foreach($order->cart->products as $p) {
					$order->cart->coupon->products_full_price += (int)$p->order_product_quantity * (float)$p->order_product_price;
				}
			}
		}

		self::$creatingSubSales = true;

		foreach($vendors as $vendor_id => $vendor) {
			if(empty($vendor))
				continue;

			$subsale = $this->createSubOrder($order, $vendor_id, $products);
			if(isset($statuses[$subsale->order_status]))
				$statuses[$subsale->order_status]++;
			else
				$statuses[$subsale->order_status] = 1;
		}

		self::$creatingSubSales = false;

		if(count($statuses) == 1 && empty($statuses[$order->order_status])) {
			$update_order = new stdClass();
			$update_order->order_id = $order->order_id;
			$update_order->order_status = reset(array_keys($statuses));
			$update_order->old = $order;
			$update_order->hikamarket = new stdClass();
			$update_order->hikamarket->internal_process = true;

			$orderClass = hikamarket::get('shop.class.order');
			$orderClass->save($update_order);
		}
	}

	private function afterCreateSubsale(&$order, &$send_email) {
		if((int)$order->order_vendor_id > 1 && (int)$order->order_user_id > 0) {
			$query = 'INSERT IGNORE INTO `'.hikamarket::table('customer_vendor').'` (customer_id, vendor_id) VALUES ('.(int)$order->order_user_id.','.(int)$order->order_vendor_id.')';
			$this->db->setQuery($query);
			$this->db->query();
		}

		if(!empty($order->cart->products)) {
			foreach($order->cart->products as $product) {
				$query = 'UPDATE ' . hikamarket::table('shop.order_product') .
						' SET order_product_parent_id = ' . (int)$product->order_product_parent_id.', order_product_vendor_price = ' . (float)$product->order_product_vendor_price;

				if(!empty($product->order_product_id))
					$query .= ' WHERE order_product_id = ' . $product->order_product_id;
				else
					$query .= ' WHERE order_id = ' . (int)$product->order_id . ' AND product_id = ' . (int)$product->product_id . ' AND order_product_price = ' . (float)hikamarket::toFloat($product->order_product_price);
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	public function beforeProductsUpdate(&$order, &$do) {
		$order_type = (!empty($order->order_type)) ? $order->order_type : @$order->old->order_type;

		$app = JFactory::getApplication();
		$ctrl = JRequest::getCmd('ctrl', '');
		$task = JRequest::getCmd('task', '');
		if($app->isAdmin() && $ctrl == 'order' && $task == 'save') {
			$shopOrderBackendHelper = hikamarket::get('helper.shop-order_backend');
			if($shopOrderBackendHelper)
				$shopOrderBackendHelper->processAdminForm($order, $do, 'products');
		}

		if($order_type == 'vendorrefund')
			$do = false;

		if(($order_type == 'subsale') && !empty($order->product) && (empty($order->hikamarket->internal_process) || !$order->hikamarket->internal_process))
			$do = false;

		if($order_type == 'sale' && !empty($order->product) && !empty($order->hikamarket->reprocess)) {
			$query = 'SELECT op.*, o.order_vendor_params FROM ' . hikamarket::table('shop.order_product') . ' AS op '.
					' INNER JOIN ' . hikamarket::table('shop.order') . ' AS o ON op.order_id = o.order_id '.
					' WHERE o.order_type = ' . $this->db->Quote('subsale') . ' AND o.order_parent_id = ' . $order->order_id;
			$this->db->setQuery($query);
			$suborder_products = $this->db->loadObjectList();

			if(is_array($order->product))
				$order_products = $order->product;
			else
				$order_products = array($order->product);

			$updates = array();
			foreach($order_products as $order_product) {
				foreach($suborder_products as $suborder_product) {
					if((int)$suborder_product->order_product_parent_id != (int)$order_product->order_product_id)
						continue;
					if($suborder_product->order_product_price == $order_product->order_product_price)
						break;
					$suborder_product->order_product_price = $order_product->order_product_price;

					$op = array(
						'order_product_price' => $suborder_product->order_product_price,
						'order_product_vendor_price' => $this->recalculateProductPrice($suborder_product, $order_vendor_params)
					);
					if($op['order_product_vendor_price'] === false)
						unset($op['order_product_vendor_price']);

					$updates[$suborder_product->order_product_id] = $op;
					break;
				}
			}
			unset($order_product);

			if(!empty($updates)) {
				foreach($updates as $i => $update) {
					$query = 'UPDATE ' . hikamarket::table('shop.order_product') . ' SET ';
					foreach($update as $k => $v) {
						$query .= $k . ' = ' . $this->db->Quote($v) . ' ';
					}
					$query .= ' WHERE order_product_id = ' . $i;
					$this->db->setQuery($query);
					$this->db->query();
				}
				unset($updates);

				$query = 'SELECT * FROM ' . hikamarket::table('shop.order') . ' AS o WHERE o.order_type = ' . $this->db->Quote('subsale') . ' AND o.order_parent_id = ' . $order->order_id;
				$this->db->setQuery($query);
				$suborders = $this->db->loadObjectList();
				foreach($suborders as $suborder) {
					$ret = $this->recalculateVendorPrice($suborder);
					$query = 'UPDATE ' . hikamarket::table('shop.order') . ' SET order_vendor_price = ' . $this->db->Quote($ret) . ' WHERE order_id = ' . (int)$suborder->order_id . ' AND o.order_type = ' . $this->db->Quote('subsale') . ' AND o.order_parent_id = ' . $order->order_id;
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}

	public function afterUpdate(&$order, &$send_email) {
		$config = hikamarket::config();

		$order_type = '';
		if(!empty($order->order_type)) {
			$order_type = $order->order_type;
		} else {
			$order_type = @$order->old->order_type;
		}

		if($order_type == 'sale') {
			$this->afterUpdateSale($order);
		}

		if($order_type == 'subsale') {
			$this->afterUpdateSubsale($order);
		}
	}

	private function afterUpdateSale(&$order) {
		$config = hikamarket::config();

		$updatableOrderStatuses = explode(',', $config->get('updatable_order_statuses', 'created'));
		$confirmedOrderStatuses = explode(',', $config->get('valid_order_statuses', 'confirmed,shipped'));

		$exclude_orderproduct_fields = array(
			'order_product_id', // key to remove
			'order_id', // overrided
			'order_product_quantity', // already handle in the code
			'order_product_option_parent_id', // can't work
			'order_product_wishlist_id', // unwanted
			'order_product_parent_id', // already used
			'order_product_vendor_price' // internal value
		);

		$updateOrders = array();
		$somePaid = array();

		if(!empty($order->hikamarket->products)) {
			if(isset($order->hikamarket->products['vendor_id']))
				$order->hikamarket->products = array($order->hikamarket->products);

			$this->loadOrderProducts($order);

			foreach($order->hikamarket->products as $order_product) {
				$order_product_id = 0;
				if(!empty($order_product['order_product_id']))
					$order_product_id = (int)$order_product['order_product_id'];

				if($order_product_id > 0 && !isset($order->order_products[ $order_product_id ]))
					continue;

				$current_vendor_id = $this->getVendorIdFromOrderProducts($order_product_id, $order);

				$new_vendor_id = (isset($order_product['vendor_id'])) ? (int)$order_product['vendor_id'] : $current_vendor_id;
				if($new_vendor_id == 1)
					$new_vendor_id = 0;

				$suborder_current_id = -1;
				$suborder_new_id = -1;
				foreach($order->hikamarket->children as $k => $v) {
					if($suborder_current_id < 0 && (int)$v->order_vendor_id == $current_vendor_id) {
						$suborder_current_id = (int)$v->order_id;
					}
					if($suborder_new_id < 0 && (int)$v->order_vendor_id == $new_vendor_id) {
						$suborder_new_id = (int)$v->order_id;
					}
				}

				if($suborder_new_id < 0) {

					$order->hikamarket->children[] = $this->createNewSubOrder($order, $new_vendor_id, $order_product);

				} elseif($order_product_id > 0) {
					$fields = array(
						'order_product_vendor_price' => 'order_product_vendor_price = ' . $this->db->Quote($order_product['vendor_price'])
					);
					if(isset($order_product['order_product_quantity'])) {
						$fields['order_product_quantity'] = 'order_product_quantity = ' . (int)$order_product['order_product_quantity'];
					}

					if($new_vendor_id != $current_vendor_id) {
						$fields['order_id'] = 'order_id = ' . (int)$suborder_new_id;
					}

					foreach(get_object_vars($order->order_products[ $order_product_id ]) as $k => $v) {
						if(isset($fields[$k]) || in_array($k, $exclude_orderproduct_fields))
							continue;
						$fields[$k] = $k . ' = ' . $this->db->Quote($v);
					}

					$query = 'UPDATE ' . hikamarket::table('shop.order_product') .
						' SET ' . implode(', ', $fields) .
						' WHERE order_product_parent_id = ' . (int)$order_product_id . ' AND product_id = ' . (int)$order_product['product_id'];
					$this->db->setQuery($query);
					$this->db->query();

				} else {
					$order_product_obj = new stdClass();
					foreach($order->product as $product) {
						if(isset($order_product['ref']) && (!isset($product->hikamarket->ref) || $product->hikamarket->ref != $order_product['ref']))
							continue;
						$order_product_obj = $product;
					}
					list($fields, $values) = $this->getQuotedObject($order_product_obj);

					if(isset($values['order_id'])) {
						$values['order_id'] = $suborder_new_id;
						$fields['order_product_id'] = 'order_product_parent_id';
						$fields['order_product_vendor_price'] = 'order_product_vendor_price';
						$values['order_product_vendor_price'] = $this->db->Quote($order_product['vendor_price']);

						unset($fields['order_product_parent_id']);
						unset($values['order_product_parent_id']);

						$query = 'INSERT IGNORE INTO ' . hikamarket::table('shop.order_product') . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')';
						$this->db->setQuery($query);
						$this->db->query();
					}
					unset($values);
					unset($fields);
				}
			}
		} elseif(!empty($order->product) && (int)$order->product[0]->order_product_quantity === 0) {
			$query = 'DELETE FROM ' . hikamarket::table('shop.order_product') .
				' WHERE order_product_parent_id = ' . (int)$order->product[0]->order_product_id;
			$this->db->setQuery($query);
			$this->db->query();

		} elseif(!empty($order->product)) {
			$product = hikamarket::cloning($order->product[0]);

			$pcid = $product->order_product_id;

			unset($product->order_product_id);
			unset($product->order_product_parent_id);
			unset($product->order_product_vendor_price);

			list($fields, $values) = $this->getQuotedObject($product);
			unset($product);

			$query = 'UPDATE ' . hikamarket::table('shop.order_product') . ' SET ';
			$sep = '';
			foreach($fields as $k => $v) {
				$query .= $sep.' '. $v . '=' . $values[$k];
				$sep = ',';
			}
			$query .= ' WHERE order_product_parent_id = '.(int)$pcid;
			$this->db->setQuery($query);
			$this->db->query();
			unset($values);
			unset($fields);
		}

		if(!empty($order->order_status) && $order->order_status != $order->old->order_status && empty($order->hikamarket->internal_process)) {
			foreach($order->hikamarket->children as $suborder) {
				if((int)$suborder->order_vendor_paid > 0 && (int)$suborder->order_vendor_id > 0) {
					$somePaid[(int)$suborder->order_vendor_id] = (int)$suborder->order_vendor_id;
					continue;
				}
				if($order->order_status == $suborder->order_status)
					continue;

				$r = $this->generateUpdateSuborder($suborder->order_id, $order);

				$r->order_status = $order->order_status;
				if(!isset($r->history)) {
					$r->history = new stdClass();
					$r->history->history_notified = $send_email;
				}

				$updateOrders[(int)$r->order_id] = $r;
			}
		}

		$reprocess_suborders = false;

		$serializes = array(
			'order_tax_info',
			'order_shipping_params',
			'order_payment_params'
		);
		$compares = array(
			'order_payment_price',
			'order_payment_tax',
			'order_shipping_id',
			'order_shipping_price',
			'order_shipping_tax',
			'order_discount_price',
			'order_discount_tax',
			'order_full_price'
		);

		if(!empty($order->product) || !empty($order->hikamarket->products))
			$reprocess_suborders = true;

		foreach($serializes as $k) {
			if($reprocess_suborders)
				break;

			$n = is_string($order->$k) ? $order->$k : serialize($order->$k);
			$o = is_string($order->old->$k) ? $order->old->$k : serialize($order->old->$k);

			if($n != $o)
				$reprocess_suborders = true;

			unset($n);
			unset($o);
		}

		foreach($compares as $k) {
			if($reprocess_suborders)
				break;

			$v = $order->$k;
			if(in_array($k, array('order_full_price')))
				$v = (float)hikamarket::toFloat($v);

			if($v != $order->old->$k)
				$reprocess_suborders = true;
		}

		$confirmingOrder = (in_array($order->order_status, $confirmedOrderStatuses) && !in_array($order->old->order_status, $confirmedOrderStatuses));
		$refundingOrder  = (!in_array($order->order_status, $confirmedOrderStatuses) && in_array($order->old->order_status, $confirmedOrderStatuses));

		if(count($somePaid) > 0 && ($confirmingOrder || $refundingOrder))
			$reprocess_suborders = true;

		$includeFields = array(
			'order_billing_address_id',
			'order_shipping_address_id',
			'order_user_id',
			'order_discount_code',
			'order_ip',
			'order_currency_id',
			'order_payment_id',
			'order_payment_method',
		);

		$query = 'SELECT field_namekey FROM '.hikamarket::table('shop.field').' WHERE field_table = ' . $this->db->Quote('order');
		$this->db->setQuery($query);
		if(!HIKASHOP_J25)
			$customFields = $this->db->loadResultArray();
		else
			$customFields = $this->db->loadColumn();
		$includeFields = array_merge($includeFields, $customFields);

		foreach($includeFields as $field) {
			if(!isset($order->$field))
				continue;

			$value = $order->$field;
			if($field == 'order_payment_method')
				$value = 'market-' . $order->$field;

			foreach($order->hikamarket->children as $suborder) {
				if(!isset($updateOrders[(int)$suborder->order_id])) {
					$updateOrders[(int)$suborder->order_id] = $this->generateUpdateSubOrder($suborder->order_id, $order);
				}
				$updateOrders[(int)$suborder->order_id]->$field = $value;
			}
		}

		if($reprocess_suborders) {
			$shopOrderClass = hikamarket::get('shop.class.order');

			$this->loadOrderProducts($order->hikamarket->children, true);

			foreach($order->hikamarket->children as $subOrder) {

				$shopOrderClass->recalculateFullPrice($subOrder, $subOrder->order_products);
				if($subOrder->order_full_price != $subOrder->old->order_full_price) {
					if(!isset($updateOrders[(int)$subOrder->order_id])) {
						$updateOrders[(int)$subOrder->order_id] = $this->generateUpdateSuborder($subOrder->order_id, $order);
					}
					$updateOrders[(int)$subOrder->order_id]->order_full_price = $subOrder->order_full_price;
					$updateOrders[(int)$subOrder->order_id]->order_tax_info = $subOrder->order_tax_info;
				}

				$subOrder->order_vendor_price = (float)hikamarket::toFloat($subOrder->order_vendor_price);
				$vendor_new_total = $this->recalculateVendorPrice($subOrder);

				if(is_string($subOrder->order_payment_params) && !empty($subOrder->order_payment_params))
					$subOrder->order_payment_params = hikamarket::unserialize($subOrder->order_payment_params);
				$feeMode = true;
				if(isset($subOrder->order_payment_params->market_mode))
					$feeMode = $subOrder->order_payment_params->market_mode;

				if(!$feeMode) {
					if($config->get('shipping_per_vendor', 1) && !empty($order->order_shipping_price))
						$vendor_new_total -= $subOrder->order_full_price;
					else
						$vendor_new_total -= $subOrder->order_full_price + (float)$subOrder->order_shipping_price;
				}

				if($vendor_new_total != $subOrder->order_vendor_price || $subOrder->order_vendor_paid > 0) {
					$this->updateVendorRefund($subOrder, $vendor_new_total, $order);

					if(!isset($updateOrders[(int)$subOrder->order_id])) {
						$updateOrders[(int)$subOrder->order_id] = $this->generateUpdateSuborder($subOrder->order_id, $order);
					}
					$updateOrders[(int)$subOrder->order_id]->order_vendor_price = $subOrder->order_vendor_price;
				}
			}
		} elseif(count($somePaid) > 0) {
			$query = 'UPDATE ' . hikamarket::table('shop.order') .
				' SET order_status = ' . $this->db->Quote($order->order_status) .
				' WHERE order_parent_id = ' . (int)$order->order_id . ' AND order_vendor_paid = 0 AND order_type = ' . $this->db->Quote('vendorrefund');
			$this->db->setQuery($query);
			$this->db->query();
		}

		if(!empty($updateOrders)) {
			$shopOrderClass = hikamarket::get('shop.class.order');
			foreach($updateOrders as &$suborder) {
				$shopOrderClass->save($suborder);
			}
			unset($suborder);
		}
	}

	private function generateUpdateSuborder($id, &$order) {
		$ret = new stdClass();
		$ret->order_id = (int)$id;

		$ret->hikamarket = new stdClass();
		$ret->hikamarket->internal_process = true;
		$ret->hikamarket->parent = $order;

		if(isset($order->history))
			$ret->history = $order->history;

		return $ret;
	}


	private function afterUpdateSubsale(&$order) {
		$config = hikamarket::config();

		if((!isset($order->hikamarket->internal_process) || !$order->hikamarket->internal_process) && !self::$creatingSubSales) {

			if(!empty($order->order_status) && $order->order_status != $order->old->order_status) {
				if($order->hikamarket->parent->order_status != $order->order_status) {

					$query = 'SELECT a.order_status, count(a.order_id) as count FROM ' . hikamarket::table('shop.order') . ' AS a'.
							' WHERE order_type = ' . $this->db->Quote('subsale') . ' AND order_parent_id = ' . (int)$order->hikamarket->parent->order_id .
							' GROUP BY a.order_status';
					$this->db->setQuery($query);
					$statuses = $this->db->loadObjectList();

					if(count($statuses) == 1) {
						$shopOrderClass = hikamarket::get('shop.class.order');

						$parentOrder = new stdClass();
						$parentOrder->order_id = $order->hikamarket->parent->order_id;
						$parentOrder->order_status = $order->order_status;

						$parentOrder->hikamarket = new stdClass();
						$parentOrder->hikamarket->internal_process = true;

						$parentOrder->history = new stdClass();
						$parentOrder->history->history_reason = JText::sprintf('AUTOMATIC_UPDATE_WITH_VENDORS');
						$parentOrder->history->history_type = 'modification';
						$parentOrder->history->history_notified = (bool)$config->get('send_mail_subsale_update_main', 0);

						$shopOrderClass->save($parentOrder);
					}
				}
			}

		}

		if(!empty($order->order_status) && (empty($order->old) || $order->order_status != $order->old->order_status) && (!isset($order->hikamarket->send_email) || !$order->hikamarket->send_email)) {
			$statuses = $config->get('vendor_email_order_status_notif_statuses', '');
			if(!empty($statuses))
				$statuses = explode(',', $statuses);

			if(!isset($order->hikamarket->vendor)) {
				$vendorClass = hikamarket::get('class.vendor');
				$vendor_id = (int)@$order->old->order_vendor_id;
				if(isset($order->order_vendor_id))
					$vendor_id = (int)$order->order_vendor_id;
				$order->hikamarket->vendor = $vendorClass->get( $vendor_id );
			}

			if(!empty($order->hikamarket->vendor)) {
				if(!empty($order->hikamarket->vendor->vendor_params->notif_order_statuses))
					$statuses = explode(',', $order->hikamarket->vendor->vendor_params->notif_order_statuses);

				if(empty($statuses) || in_array($order->order_status, $statuses)) {
					$mailClass = hikamarket::get('class.mail');
					$mailClass->sendVendorOrderEmail($order);
				}
			}
		}
	}

	public function beforeDelete(&$elements, &$do) {
		$string = array();
		foreach($elements as $key => $val) {
			$string[$val] = $this->db->Quote($val);
		}

		$query = 'SELECT order_id, order_type, order_status FROM ' . hikamarket::table('shop.order') . ' WHERE order_id IN (' . implode(',', $string) . ')';
		$this->db->setQuery($query);
		$orders = $this->db->loadObjectList();

		$removedList = array();
		foreach($orders as $order) {
			if($order->order_type == 'subsale') {
				foreach($elements as $k => $e) {
					if($e == $order->order_id)
						unset($elements[$k]);
				}
				$removedList[] = $order->order_id;
			}
		}
		if(!empty($removedList)) {
			$app = JFactory::getApplication();
			if(count($removedList) == 1) {
				$app->enqueueMessage(JText::sprintf('CANNOT_DELETE_SUBORDER', $removedList[0]));
			} else {
				$app->enqueueMessage(JText::sprintf('CANNOT_DELETE_SUBORDERS', implode(', ',$removedList)));
			}
		}

		if(empty($elements))
			$do = false;
	}

	public function afterDelete(&$elements) {
		$string = array();
		foreach($elements as $key => $val) {
			$string[$val] = $val;
		}

		$query = 'SELECT order_id, order_billing_address_id, order_shipping_address_id FROM '.hikamarket::table('shop.order').' WHERE order_type = '.$this->db->Quote('subsale').' AND order_parent_id IN ('.implode(',',$string).')';
		$this->db->setQuery($query);
		$orders = $this->db->loadObjectList();

		if(!empty($orders)) {

			$addr = array();
			$string = array();
			foreach($orders as $o) {
				$addr[$o->order_billing_address_id] = $o->order_billing_address_id;
				$addr[$o->order_shipping_address_id] = $o->order_shipping_address_id;
				$string[] = $this->db->Quote($o->order_id);
			}

			$query = 'DELETE FROM ' . hikamarket::table('shop.order') . ' WHERE order_id IN (' . implode(',', $string) . ')';
			$this->db->setQuery($query);
			$this->db->query();

			$query = 'DELETE FROM ' . hikamarket::table('shop.order_product') . ' WHERE order_id IN (' . implode(',', $string) . ')';
			$this->db->setQuery($query);
			$this->db->query();

			$addressClass = hikamarket::get('shop.class.address');
			foreach($addr as $address) {
				$addressClass->delete($address, true);
			}
		}
	}

	public function isValidOrderStatus($order_status) {
		if(empty($order_status))
			return false;

		static $order_statuses = null;
		if($order_statuses === null) {
			$categoryClass = hikamarket::get('shop.class.category');
			$filters = array();
			$rows = $categoryClass->loadAllWithTrans('status', false, $filters);
			foreach($rows as $row) {
				$order_statuses[$row->category_name] = $row->category_name;
			}
			unset($rows);
		}
		return isset($order_statuses[$order_status]);
	}


	private function generateOrderInvoiceNumber(&$order, $vendor_id, $force = false) {
		$shopConfig = hikamarket::config(false);
		$format = $shopConfig->get('invoice_number_format','{automatic_code}');

		$vendorClass = hikamarket::get('class.vendor');
		$vendor = $vendorClass->get($vendor_id);
		if(!empty($vendor->vendor_params->invoice_number_format))
			$format = $vendor->vendor_params->invoice_number_format;

		if(!$force && (!empty($order->order_invoice_created) || !empty($order->old->order_invoice_created)))
			return false;

		if($format == '{parent_code}') {
			$order_parent_id = !empty($order->order_parent_id) ? $order->order_parent_id : $order->old->order_parent_id;
			$query = 'SELECT o.order_invoice_id, o.order_invoice_number FROM '.hikamarket::table('shop.order').' AS o WHERE o.order_type = '.$this->db->Quote('sale').' AND o.order_id = '.(int)$order_parent_id;
			$this->db->setQuery($query);
			$order_number_data = $this->db->loadObject();
			if(!empty($order_number_data) && !empty($order_number_data->order_invoice_id)) {
				$order->order_invoice_id = (int)$order_number_data->order_invoice_id;
				$order->order_invoice_number = $order_number_data->order_invoice_number;
				$order->order_invoice_created = time();
			}
			return true;
		}

		$filters = array(
			'order_type' => 'o.order_type = '.$this->db->Quote('subsale'),
			'vendor_id' => 'o.order_vendor_id = '. (int)$vendor_id,
		);

		$resetFrequency = $shopConfig->get('invoice_reset_frequency', '');
		if(!empty($resetFrequency)) {
			$y = (int)date('Y');
			$m = 1;
			$d = 1;
			if($resetFrequency == 'month')
				$m = (int)date('m');

			if(strpos($resetFrequency, '/') !== false) {
				list($d,$m) = explode('/', $resetFrequency, 2);
				$d = ($d == '*') ? (int)date('d') : (int)$d;
				$m = ($m == '*') ? (int)date('m') : (int)$m;
				if($d <= 0) $d = 1;
				if($m <= 0) $m = 1;
			}

			$filters['date'] = 'o.order_invoice_created >= '.mktime(0, 0, 0, $m, $d, $y);
		}

		$query = 'SELECT MAX(o.order_invoice_id) + 1 FROM '.hikamarket::table('shop.order').' AS o WHERE ('.implode(') AND (', $filters) . ')';
		$this->db->setQuery($query);

		$order->order_invoice_id = (int)$this->db->loadResult();
		if(empty($order->order_invoice_id))
			$order->order_invoice_id = 1;
		$order->order_invoice_number = hikamarket::encodeNumber($order, 'invoice', $format);
		$order->order_invoice_created = time();

		return true;
	}

	private function getVendorIdFromOrderProducts($order_product_id, &$order) {
		if(empty($order_product_id) || (int)$order_product_id <= 0)
			return 0;

		if(empty($order->hikamarket))
			$order->hikamarket = new stdClass();

		if(empty($order->hikamarket->order_product_vendors)) {
			if(empty($order->order_products))
				$this->loadOrderProducts($order);

			$order_product_ids = array_keys($order->order_products);
			JArrayHelper::toInteger($order_product_ids);
			$query = 'SELECT op.order_product_id, op.order_id, o.order_vendor_id, o.order_type, op.order_product_parent_id, op.order_product_vendor_price ' .
				' FROM ' . hikamarket::table('shop.order_product') . ' AS op ' .
				' INNER JOIN ' . hikamarket::table('shop.order') . ' AS o ON op.order_id = o.order_id ' .
				' WHERE op.order_product_parent_id IN (' . implode(',', $order_product_ids) . ')';
			$this->db->setQuery($query);
			$order->hikamarket->order_product_vendors = $this->db->loadObjectList('order_product_id');
		}

		foreach($order->hikamarket->order_product_vendors as $order_product) {
			if((int)$order_product->order_product_parent_id == (int)$order_product_id)
				return (int)$order_product->order_vendor_id;
		}
		return 0;
	}

	private function createSubOrder(&$order, $vendor_id, &$products) {
		$shopOrderClass = hikamarket::get('shop.class.order');
		$config = hikamarket::config();

		$v_order = unserialize(serialize($order));
		unset($v_order->cart->products);

		$v_order->order_type = 'subsale';
		$v_order->order_parent_id = $v_order->order_id;
		$v_order->order_vendor_id = $vendor_id;
		$v_order->order_payment_method = 'market-' . $v_order->order_payment_method;

		$v_order->order_partner_id = 0;
		$v_order->order_partner_price = 0.0;

		$v_order->order_payment_price = 0.0;
		$v_order->order_payment_tax = 0.0;
		$v_order->order_shipping_price = 0.0;
		$v_order->order_shipping_tax = 0.0;
		$v_order->order_discount_price = 0.0;
		$v_order->order_discount_tax = 0.0;
		$v_order->order_discount_code = '';

		$total_products = 0.0;
		$total_products_vendor = 0.0;

		$v_order->cart->products = array();
		foreach($order->cart->products as $product) {
			$pid = (int)$product->product_id;
			$pcid = (isset($product->cart_product_id)) ? (int)$product->cart_product_id : (int)$product->order_product_id;

			$total_products += $product->order_product_price;

			if(!isset($products[$pcid]))
				continue;

			if($products[$pcid]['vendor'] != $vendor_id)
				continue;

			$newProduct = hikamarket::cloning($product);
			$newProduct->order_product_parent_id = (int)$newProduct->order_product_id;
			$newProduct->cart_product_parent_id = (int)$newProduct->cart_product_id;
			unset($newProduct->order_product_id);
			unset($newProduct->order_id);


			$discount_apply_vendor = !isset($newProduct->discount->discount_target_vendor) ? 0 : (int)$newProduct->discount->discount_target_vendor;
			if(!empty($newProduct->discount) && $newProduct->discount->discount_type == 'discount' && $discount_apply_vendor <= 0) {
				if(isset($newProduct->discount->price_value_without_discount)) {
					$newProduct->order_product_price = $newProduct->discount->price_value_without_discount;
					$newProduct->order_product_tax = $newProduct->discount->price_value_without_discount_with_tax - $newProduct->discount->price_value_without_discount;
					$newProduct->order_product_tax_info = $newProduct->discount->taxes_without_discount;
				} else {
					if(!empty($newProduct->discount->discount_percent_amount)) {
						$percent = (float)hikamarket::toFloat($newProduct->discount->discount_percent_amount);
						$newProduct->order_product_price /= (1 - $percent/100);
					}
					if(!empty($newProduct->discount->discount_flat_amount)) {
						$value = (float)hikamarket::toFloat($newProduct->discount->discount_flat_amount);
						$newProduct->order_product_price += $value;
					}
				}
			}

			$newProduct->order_product_vendor_price = null;

			if(!empty($newProduct->cart_product_option_parent_id)) {
				$f = false;
				foreach($order->cart->products as $p) {
					$pcid = (isset($p->cart_product_id)) ? (int)$p->cart_product_id : (int)$p->order_product_id;
					if($p->cart_product_id == $newProduct->cart_product_option_parent_id && isset($products[$pcid])) {
						$f = true;
						break;
					}
				}
				if(!$f)
					unset($newProduct->cart_product_option_parent_id);
			}

			$newProduct->no_update_qty = true;
			$v_order->cart->products[] = $newProduct;

			$total_products_vendor += $newProduct->order_product_price;
		}

		if($config->get('split_order_payment_fees', 0) && !empty($total_products)) {
			$v_order->order_payment_price = $order->order_payment_price * $total_products_vendor / $total_products;
			$v_order->order_payment_tax = ((int)@$order->order_payment_tax) * $total_products_vendor / $total_products;
		}

		$this->processShippingParams($order, $vendor_id, $v_order, $total_products, $total_products_vendor, $products);

		if(!empty($order->cart->coupon) && !empty($order->cart->coupon->products_full_price)) {
			$vendor_coupon_total = 0.0;
			if(!empty($order->cart->coupon->products)) {
				foreach($order->cart->coupon->products as $product) {
					if($vendor_id > 1 && (int)$product->product_vendor_id != $vendor_id)
						continue;
					if($vendor_id <= 1 && (int)$product->product_vendor_id > 1)
						continue;
					foreach($products as $p) {
						if($p['id'] != (int)$product->product_id)
							continue;
						$vendor_coupon_total += (int)$product->cart_product_quantity * (float)$product->prices[0]->price_value;
					}
				}
			} else {
				foreach($v_order->cart->products as $product) {
					$vendor_coupon_total += (int)$product->order_product_quantity * (float)$product->order_product_price;
				}
			}
			if(empty($order->cart->coupon->vendors))
				$order->cart->coupon->vendors = array();
			$order->cart->coupon->vendors[$vendor_id] = $vendor_coupon_total;

			if($vendor_coupon_total > 0.0 && ($order->cart->coupon->discount_target_vendor == 1 || $order->cart->coupon->discount_target_vendor == $vendor_id)) {
				$v_order->order_discount_code = $order->order_discount_code;

				$coupon_percentage = (float)($vendor_coupon_total / $order->cart->coupon->products_full_price);

				$v_order->order_discount_price = $order->order_discount_price * $coupon_percentage;
				if($order->order_discount_tax > 0)
					$v_order->order_discount_tax = $order->order_discount_tax * $coupon_percentage;
				else
					$v_order->order_discount_tax = 0.0;
			}
		}

		if(empty($order->cart->coupon))
			$order->cart->coupon = null;

		$shopOrderClass->recalculateFullPrice($v_order, $v_order->cart->products);
		$v_order->order_vendor_price = $this->calculateVendorPrice($vendor_id, $v_order, $products, $order->cart->coupon);

		$feeMode = ($config->get('market_mode', 'fee') == 'fee');
		$payment_params = null;
		if(!empty($order->order_payment_params))
			$payment_params = is_string($order->order_payment_params) ? hikamarket::unserialize($order->order_payment_params) : $order->order_payment_params;
		if(!empty($payment_params->market_mode)) {
			$feeMode = (($payment_params->market_mode === true) || ($payment_params->market_mode === 'fee'));
		} else {
			$payment_id = 0;
			if(!empty($order->order_payment_id))
				$payment_id = (int)$order->order_payment_id;
			if($payment_id > 0) {
				$paymentClass = hikamarket::get('shop.class.payment');
				$payment = $paymentClass->get($payment_id);

				if(!empty($payment->market_mode)) {
					$feeMode = true;
				} else if(!empty($payment->payment_params->payment_market_mode)) {
					$feeMode = ($payment->payment_params->payment_market_mode == 'fee');
				}
			}
		}
		if(!$feeMode) {
			if($config->get('shipping_per_vendor', 1))
				$v_order->order_vendor_price -= $order->order_full_price;
			else
				$v_order->order_vendor_price -= $order->order_full_price + (float)$v_order->order_shipping_price;
		}

		if(empty($v_order->order_payment_params))
			$v_order->order_payment_params = new stdClass();
		$v_order->order_payment_params->market_mode = $feeMode;

		unset($v_order->order_id);
		if(!$config->get('use_same_order_number', 0))
			unset($v_order->order_number);
		unset($v_order->order_invoice_id);
		unset($v_order->order_invoice_number);

		$v_order->hikamarket = new stdClass();
		$v_order->hikamarket->internal_process = true;
		$v_order->hikamarket->send_email = true;
		$v_order->hikamarket->parent = $order;

		if(!empty($v_order->order_vendor_params) && is_object($v_order->order_vendor_params))
			$v_order->order_vendor_params = serialize($v_order->order_vendor_params);
		$shopOrderClass->save($v_order);

		return $v_order;
	}

	private function createNewSubOrder(&$order, $vendor_id, &$order_product) {
		$shopOrderClass = hikamarket::get('shop.class.order');
		$config = hikamarket::config();

		$v_order = unserialize(serialize($order));

		$raw_order = $this->getRaw((int)$order->order_id);
		foreach(get_object_vars($raw_order) as $k => $v) {
			if(isset($v_order->$k))
				continue;
			$v_order->$k = $v;
		}

		$v_order->order_type = 'subsale';
		$v_order->order_parent_id = $v_order->order_id;
		$v_order->order_vendor_id = $vendor_id;
		$v_order->order_payment_method = 'market-' . $v_order->order_payment_method;

		$v_order->order_partner_id = 0;
		$v_order->order_partner_price = 0.0;

		$v_order->order_payment_price = 0.0;
		$v_order->order_payment_tax = 0.0;
		$v_order->order_shipping_id = '';
		$v_order->order_shipping_price = 0.0;
		$v_order->order_shipping_tax = 0.0;
		$v_order->order_discount_price = 0.0;
		$v_order->order_discount_tax = 0.0;
		$v_order->order_discount_code = '';

		$v_order->cart = new stdClass();
		$v_order->cart->products = array();
		foreach($order->product as $product) {

			if(isset($order_product['ref']) && (!isset($product->hikamarket->ref) || $product->hikamarket->ref != $order_product['ref']))
				continue;

			$newProduct = hikamarket::cloning($product);

			$newProduct->order_product_parent_id = (int)$newProduct->order_product_id;
			$newProduct->no_update_qty = true;
			$newProduct->order_product_vendor_price = (float)hikamarket::toFloat($order_product['vendor_price']);

			unset($newProduct->order_product_id);
			unset($newProduct->order_id);
			unset($newProduct->hikamarket->ref);

			$v_order->cart->products[] = $newProduct;
		}

		$shopOrderClass->recalculateFullPrice($v_order, $v_order->cart->products);

		if(!empty($order_product['order_product_id'])) {
			unset($v_order->cart);
		}

		$v_order->order_vendor_price = (float)hikamarket::toFloat($order_product['vendor_price']);

		$feeMode = ($config->get('market_mode', 'fee') == 'fee');
		$payment_params = null;
		if(!empty($raw_order->order_payment_params))
			$payment_params = is_string($raw_order->order_payment_params) ? hikamarket::unserialize($raw_order->order_payment_params) : $raw_order->order_payment_params;
		if(!empty($payment_params->market_mode)) {
			$feeMode = (($payment_params->market_mode === true) || ($payment_params->market_mode === 'fee'));
		} else {
			$payment_id = 0;
			if(!empty($raw_order->order_payment_id))
				$payment_id = (int)$raw_order->order_payment_id;
			if($payment_id > 0) {
				$paymentClass = hikamarket::get('shop.class.payment');
				$payment = $paymentClass->get($payment_id);

				if(!empty($payment->market_mode)) {
					$feeMode = true;
				} else if(!empty($payment->payment_params->payment_market_mode)) {
					$feeMode = ($payment->payment_params->payment_market_mode == 'fee');
				}
			}
		}
		if(!$feeMode) {
			$v_order->order_vendor_price -= $raw_order->order_full_price;
		}

		if(empty($v_order->order_payment_params))
			$v_order->order_payment_params = new stdClass();
		$v_order->order_payment_params->market_mode = $feeMode;

		unset($v_order->product);
		unset($v_order->hikamarket);
		unset($v_order->old);
		unset($v_order->serial_data);
		unset($v_order->order_serial_params);
		unset($v_order->order_products);

		unset($v_order->order_id);
		unset($v_order->order_invoice_id);
		unset($v_order->order_invoice_number);

		if(!$config->get('use_same_order_number', 0))
			unset($v_order->order_number);

		$v_order->hikamarket = new stdClass();
		$v_order->hikamarket->internal_process = true;
		$v_order->hikamarket->send_email = false;

		if(!empty($v_order->order_vendor_params) && is_object($v_order->order_vendor_params))
			$v_order->order_vendor_params = serialize($v_order->order_vendor_params);

		$order_id = $shopOrderClass->save($v_order);

		if(empty($order_id))
			return false;

		$filters = array();
		$fields = array(
			'order_product_vendor_price' => 'order_product_vendor_price = ' . $this->db->Quote($order->hikamarket->products['vendor_price'])
		);

		if(isset($order_product['order_product_quantity']))
			$fields['order_product_quantity'] = 'order_product_quantity = ' . (int)$order_product['order_product_quantity'];

		if(empty($order_product['order_product_id'])) {
			$fields['order_product_parent_id'] = 'order_product_parent_id = ' . (int)$v_order->cart->products[0]->order_product_parent_id;
			$filters['order_product_id'] = 'order_product_id = ' . (int)$v_order->cart->products[0]->order_product_id;
		} else {
			$fields['order_id'] = 'order_id = ' . (int)$order_id;
			$filters['order_product_id'] = 'order_product_parent_id = ' . (int)$order_product['order_product_id'];
		}

		$query = 'UPDATE ' . hikamarket::table('shop.order_product') . ' SET ' . implode(', ', $fields) . ' WHERE (' . implode(') AND (', $filters) . ')';
		$this->db->setQuery($query);
		$this->db->query();

		return $v_order;
	}

	public function getProductVendorAttribution(&$order) {
		$products = array();
		$cart_products = isset($order->cart->products) ? $order->cart->products : $order->products;
		foreach($cart_products as $product) {
			$products[(int)$product->cart_product_id] = array(
				'_id' => (int)$product->cart_product_id,
				'id' => (int)$product->product_id,
				'vendor' => null
			);
		}
		$this->getVendorsByProducts($products, $order);
		return $products;
	}

	protected function getVendorsByProducts(&$products, $order = null) {
		$vendors = array(0 => array());
		if(empty($products))
			return $vendors;

		$product_ids = array();
		foreach($products as $product) { $product_ids[] = $product['id']; }

		$query = 'SELECT a.product_id, a.product_vendor_id, a.product_parent_id, b.product_vendor_id as `parent_vendor_id`'.
				' FROM ' . hikamarket::table('shop.product') . ' AS a'.
				' LEFT JOIN ' . hikamarket::table('shop.product') . ' AS b ON a.product_parent_id = b.product_id'.
				' WHERE a.product_id IN (' . implode(',', $product_ids) . ')';
		$this->db->setQuery($query);
		$productObjects = $this->db->loadObjectList('product_id');
		if(!empty($productObjects)) {
			foreach($productObjects as $productObject) {
				$vid = $productObject->product_vendor_id;
				if(empty($vid) && !empty($productObject->parent_vendor_id)) {
					$vid = $productObject->parent_vendor_id;
				}
				if($vid == 1)
					$vid = 0;
				$pid = (int)$productObject->product_id;
				foreach($products as $key => &$product) {
					if($product['id'] == $pid)
						$product['vendor'] = $vid;
				}
				unset($product);
			}
		}

		JPluginHelper::importPlugin('hikamarket');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onBeforeProductsVendorAttribution', array(&$products, &$productObjects, &$order));

		foreach($products as $key => &$product) {
			$vid = $product['vendor'];
			if($vid == 1) { $vid = 0; $product['vendor'] = 0; }
			if(empty($vendors[$vid]))
				$vendors[$vid] = array();
			$vendors[$vid][$key] = $key;
		}
		unset($product);

		$config = hikamarket::config();

		$vendorselection_custom_field = $config->get('vendor_select_custom_field', '');
		if(!empty($vendorselection_custom_field) && !empty($vendors[0]) && !empty($order)) {
			$query = 'SELECT field.field_namekey, field.field_table '.
				' FROM ' . hikamarket::table('shop.field') . ' AS field '.
				' WHERE field.field_namekey = '.$this->db->Quote($vendorselection_custom_field).' AND (field.field_table = \'order\' OR field.field_table = \'item\') '.
				' AND field.field_type = \'plg.market_vendorselectfield\' AND field_published = 1 AND field_frontcomp = 1';
			$this->db->setQuery($query);
			$result = $this->db->loadObject();
			if(!empty($result)) {
				if($result->field_table == 'order' && isset($order->$vendorselection_custom_field) && !empty($order->$vendorselection_custom_field)) {
					$query = 'SELECT vendor.vendor_id FROM '.hikamarket::table('vendor').' AS vendor WHERE vendor.vendor_id = '.(int)$order->$vendorselection_custom_field.' AND vendor.vendor_published = 1';
					$this->db->setQuery($query);
					$selected_vendor_id = $this->db->loadResult();
					if(!empty($selected_vendor_id)) {
						$selected_vendor_id = (int)$selected_vendor_id;
						if(empty($vendors[$selected_vendor_id]))
							$vendors[$selected_vendor_id] = array();
						foreach($vendors[0] as $product_cart_id) {
							$vendors[$selected_vendor_id][$product_cart_id] = $product_cart_id;
							$products[$product_cart_id]['vendor'] = $selected_vendor_id;
						}
						$vendors[0] = array();
					}
				}
				$cart_products = isset($order->cart->products) ? $order->cart->products : $order->products;
				if($result->field_table == 'item' && !empty($cart_products)) {
					$affectedProducts = array();

					foreach($cart_products as $order_product) {
						$pcid = 0;
						if(isset($order_product->cart_product_id))
							$pcid = (int)$order_product->cart_product_id;
						else if(isset($order_product->order_product_id))
							$pcid = (int)$order_product->order_product_id;
						$pid = (int)$order_product->product_id;
						if(isset($vendors[0][$pcid]) && isset($order_product->$vendorselection_custom_field)) {
							$vid = (int)$order_product->$vendorselection_custom_field;
							if($vid > 1)
								$affectedProducts[$pcid] = $vid;
						}
					}

					if(!empty($affectedProducts)) {

						$vendor_ids = array();
						foreach($affectedProducts as $pcid => $vendor_id) {
							$vendor_ids[(int)$vendor_id] = (int)$vendor_ids;
						}
						$query = 'SELECT vendor_id, vendor_published, vendor_params FROM ' . hikamarket::table('vendor').' WHERE vendor_id IN ('.implode(',',$vendor_ids).') AND vendor_published = 1';
						$this->db->setQuery($query);
						$valid_vendors = $this->db->loadObjectList('vendor_id');

						foreach($affectedProducts as $pcid => $vendor_id) {
							if(!isset($valid_vendors[$vendor_id]) || empty($valid_vendors[$vendor_id]->vendor_published))
								continue;

							if(is_string($valid_vendors[$vendor_id]->vendor_params))
								$valid_vendors[$vendor_id]->vendor_params = hikamarket::unserialize($valid_vendors[$vendor_id]->vendor_params);

							if(!isset($valid_vendors[$vendor_id]->vendor_params['vendor_selector']) || !empty($valid_vendors[$vendor_id]->vendor_params['vendor_selector'])) {
								if(isset($products[$pcid]) && $products[$pcid]['vendor'] == 0) {
									$products[$pcid]['vendor'] = $vendor_id;
									unset($vendors[0][$pcid]);
									$vendors[$vendor_id][$pcid] = $pcid;
								}
							}
						}
					}
				}
			}
		}

		if($config->get('allow_zone_vendor', 0) && !empty($vendors[0]) && !empty($order)) {
			$zoneClass = hikamarket::get('shop.class.zone');
			$zones = $zoneClass->getOrderZones($order);
			if(count($zones) == 1) {
				$zones = $zoneClass->getZoneParents($zones);
			}
			$zonesQuoted = array();
			foreach($zones as $z) {
				$zonesQuoted[] = $this->db->Quote($z);
			}

			$query = 'SELECT vendor.vendor_id, vendor.vendor_zone_id, zone.zone_namekey, zone.zone_type '.
				' FROM ' . hikamarket::table('vendor') . ' AS vendor '.
				' INNER JOIN ' . hikamarket::table('shop.zone') . ' AS zone ON vendor.vendor_zone_id = zone.zone_id '.
				' WHERE vendor.vendor_zone_id > 0 AND zone.zone_namekey IN ('.implode(',', $zonesQuoted).') ORDER BY vendor.vendor_id ASC';
			$this->db->setQuery($query);
			$zoneVendors = $this->db->loadObjectList('zone_namekey');
			$zone_vendor_id = null;

			if(!empty($zoneVendors)) {
				foreach($zones as $z) {
					if(isset($zoneVendors[$z])) {
						$zone_vendor_id = (int)$zoneVendors[$z]->vendor_id;
						break;
					}
				}
			}

			if(!empty($zone_vendor_id)) {
				if(empty($vendors[$zone_vendor_id]))
					$vendors[$zone_vendor_id] = array();
				foreach($vendors[0] as $k => $p) {
					$vendors[$zone_vendor_id][$k] = $p;
					$products[$k]['vendor'] = $zone_vendor_id;
				}
				$vendors[0] = array();
			}
		}

		$dispatcher->trigger('onAfterProductsVendorAttribution', array(&$vendors, &$products, &$productObjects, &$order));

		return $vendors;
	}

	private function loadOrderProducts(&$order, $force = false) {
		if(empty($order) || (is_object($order) && empty($order->order_id)))
			return false;

		if(is_object($order)) {
			if(isset($order->order_products) && is_array($order->order_products) && !$force)
				return true;

			$query = 'SELECT * FROM ' . hikamarket::table('shop.order_product') .
				' WHERE order_id = ' . (int)$order->order_id . ' AND order_product_quantity > 0';
			$this->db->setQuery($query);
			$order->order_products = $this->db->loadObjectList('order_product_id');

			return true;
		}

		if(is_array($order)) {
			$ids = array();
			foreach($order as &$o) {
				if(empty($o->order_id))
					continue;
				if(!$force && isset($o->order_products) && is_array($o->order_products))
					continue;
				$ids[] = (int)$o->order_id;
				unset($o->order_products);
				$o->order_products = array();
			}
			unset($o);

			$query = 'SELECT * FROM ' . hikamarket::table('shop.order_product') .
				' WHERE order_id IN (' . implode(',', $ids) . ') AND order_product_quantity > 0';
			$this->db->setQuery($query);
			$order_products = $this->db->loadObjectList('order_product_id');

			foreach($order_products as &$op) {
				foreach($order as &$o) {
					if((int)$op->order_id != (int)$o->order_id)
						continue;
					$o->order_products[ (int)$op->order_product_id ] =& $op;
					break;
				}
				unset($o);
			}
			unset($op);

			return true;
		}

		return false;
	}

	private function loadOrderRefunds(&$order, $force = false) {
		if(empty($order) || empty($order->order_id))
			return false;

		if(!isset($order->hikamarket))
			$order->hikamarket = new stdClass();

		if(isset($order->hikamarket->refunds) && is_array($order->hikamarket->refunds) && !$force)
			return true;

		$query = 'SELECT * FROM ' . hikamarket::table('shop.order') .
			' WHERE order_type = '. $this->db->Quote('vendorrefund') .' AND order_parent_id = ' . (int)$order->order_id;
		$this->db->setQuery($query);
		$order->hikamarket->refunds = $this->db->loadObjectList('order_id');

		return true;
	}

	public function calculateVendorPrice($vendor_id, &$v_order, &$products, $coupon) {
		$ret = 0.0;
		$total_qty = 0;
		$config = hikamarket::config();

		if($vendor_id <= 1)
			return 0.0;

		$order_products =& $v_order->cart->products;

		$do = true;
		JPluginHelper::importPlugin('hikamarket');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onBeforeMarketCalculateVendorPrice', array($vendor_id, &$ret, &$order_products, &$products, $coupon, $v_order, &$do));

		if(!$do)
			return $ret;

		$global_fixed_fees = array();

		if(empty($v_order->order_vendor_params))
			$v_order->order_vendor_params = new stdClass();
		if(empty($v_order->order_vendor_params->fees))
			$v_order->order_vendor_params->fees = new stdClass();
		if(empty($v_order->order_vendor_params->fees->rules))
			$v_order->order_vendor_params->fees->rules = array();
		if(empty($v_order->order_vendor_params->fees->fixed))
			$v_order->order_vendor_params->fees->fixed = array();
		if(empty($v_order->order_vendor_params->fees->shipping))
			$v_order->order_vendor_params->fees->shipping = 0.0;

		$total_quantity = 0;
		$total_price = 0.0;
		$total_price_with_tax = 0.0;
		foreach($products as $product) {
			if($product['vendor'] == $vendor_id) {
				$total_quantity += $product['qty'];
				$total_price += $product['price'];
				$total_price_with_tax += $product['price_tax'];
			}
		}
		if($config->get('fee_on_shipping', 0) && !empty($v_order->order_shipping_price)) {
			$total_price += (float)$v_order->order_shipping_price - (float)$v_order->order_shipping_tax;
			$total_price_with_tax += (float)$v_order->order_shipping_price;
		}

		foreach($order_products as &$product) {
			if($product->order_product_quantity == 0)
				continue;

			if($config->get('calculate_vendor_price_with_tax', false))
				$full_price = (float)($product->order_product_price + $product->order_product_tax) * (int)$product->order_product_quantity;
			else
				$full_price = (float)$product->order_product_price * (int)$product->order_product_quantity;

			if(!empty($coupon) && !empty($coupon->products) && empty($coupon->all_products)) {
				foreach($coupon->products as $couponProduct) {
					if($couponProduct->product_id != $product->product_id)
						continue;

					if(isset($couponProduct->processed_discount_value)) {
						$full_price -= floatval($couponProduct->processed_discount_value);
					} elseif(bccomp($coupon->discount_flat_amount, 0, 5) !== 0) {
						$percent = 1.0;
						if(!empty($coupon->products_full_price))
							$percent = floatval($full_price / $coupon->products_full_price);
						$full_price -= floatval($coupon->discount_flat_amount) * $percent;
					} elseif(bccomp($coupon->discount_percent_amount, 0, 5) !== 0) {
						$full_price *= floatval((100 - floatval($coupon->discount_percent_amount)) / 100);
					}
				}
			}

			$pcid = isset($product->cart_product_parent_id) ? $product->cart_product_parent_id : $product->order_product_parent_id;
			if($config->get('calculate_vendor_price_with_tax', false))
				$product->order_product_vendor_price = ($product->order_product_price + $product->order_product_tax);
			else
				$product->order_product_vendor_price = $product->order_product_price;

			$product_fee = false;
			if(isset($products[$pcid]))
				$product_fee = $this->getProductFee($product, $products[$pcid]['fee'], $full_price, $total_price, $total_quantity);

			if(!empty($product_fee)) {
				$products[$pcid]['vendor_fee'] = $product_fee;
				$products[$pcid]['vendor_price'] = $product_fee['vendor'];
				$product->order_product_vendor_price = $product_fee['vendor'];
				$ret += $product_fee['price'];

				foreach($products[$pcid]['fee'] as $fee) {
					if($fee->fee_id == 	$product_fee['id']) {
						$v_order->order_vendor_params->fees->rules[] = $fee;
						break;
					}
				}

				if(!empty($product_fee['fixed'])) {
					if(empty($v_order->order_vendor_params->fees->fixed[ $product_fee['id'] ]))
						$v_order->order_vendor_params->fees->fixed[ $product_fee['id'] ] = $product_fee['fixed'];
				}

				if(substr($product_fee['mode'], -7) == '_global') {
					if(!isset($global_fixed_fees[ $product_fee['id'] ])) {
						$ret -= $product_fee['fixed'];
						$global_fixed_fees[ $product_fee['id'] ] = true;
					}
				} else {
					$ret -= $product_fee['fixed'];
				}
			} else {
				$ret += $full_price; // $product->order_product_vendor_price;
			}
		}
		unset($product);

		if(!empty($coupon) && !empty($coupon->discount_target_vendor) && (int)$coupon->discount_target_vendor > 0 && (empty($coupon->products) || !empty($coupon->all_products))) {
			if(empty($v_order->order_vendor_params->coupon))
				$v_order->order_vendor_params->coupon = new stdClass();

			if(bccomp($coupon->discount_flat_amount, 0, 5) !== 0 && (!isset($coupon->discount_percent_amount_orig) || bccomp($coupon->discount_percent_amount_orig, 0, 5) === 0)) {
				$coupon_percentage = (float)($coupon->vendors[$vendor_id] / $coupon->products_full_price);

				$v_order->order_discount_price = floatval($coupon->discount_flat_amount) * $coupon_percentage;

				$v_order->order_vendor_params->coupon->mode = 'flat';
				$v_order->order_vendor_params->coupon->value = floatval($coupon->discount_flat_amount);
				$v_order->order_vendor_params->coupon->ratio = $coupon_percentage;

				$ret -= floatval($coupon->discount_flat_amount) * $coupon_percentage;

			} elseif(bccomp($coupon->discount_percent_amount, 0, 5) !== 0 || (isset($coupon->discount_percent_amount_orig) && bccomp($coupon->discount_percent_amount_orig, 0, 5) !== 0)) {
				$percent_amount = (float)hikamarket::toFloat($coupon->discount_percent_amount);
				if(empty($percent_amount) && isset($coupon->discount_percent_amount_orig))
					$percent_amount = (float)hikamarket::toFloat($coupon->discount_percent_amount_orig);

				$v_order->order_vendor_params->coupon->mode = 'percent';
				$v_order->order_vendor_params->coupon->value = $percent_amount;
				$v_order->order_vendor_params->coupon->target_vendor = $coupon->discount_target_vendor;

				if($coupon->discount_target_vendor > 1) {
					if($config->get('calculate_vendor_price_with_tax', false))
						$ret -= $v_order->order_discount_price;
					else
						$ret -= $v_order->order_discount_price + $v_order->order_discount_tax;
				} else {
					$ret *= floatval((100 - $percent_amount) / 100);
				}
			}
		}

		if(!empty($v_order->order_payment_price)) {
			$ret += $v_order->order_payment_price;
			if(!$config->get('calculate_vendor_price_with_tax', false))
				$ret -= (float)hikamarket::toFloat($v_order->order_payment_tax);
		}

		if(!empty($v_order->order_shipping_price)) {
			$ret += $v_order->order_shipping_price;
			if(!$config->get('calculate_vendor_price_with_tax', false))
				$ret -= (float)$v_order->order_shipping_tax;

			if($config->get('fee_on_shipping', 0)) {
				$f = false;
				foreach($v_order->order_vendor_params->fees->rules as $rule) {
					if(substr($rule->fee_type, -7) == '_global') {
						$v_order->order_vendor_params->fees->shipping = (float)((100 - (float)$rule->fee_percent) * $v_order->order_shipping_price / 100) - $rule->fee_value;
						$f = true;
						break;
					}
				}

				if(!$f) {
					$feeClass = hikamarket::get('class.fee');
					$vendorFees = $feeClass->getVendor($vendor_id, true);
					foreach($vendorFees as $fee) {
						if((int)$fee->fee_min_quantity > 1)
							continue;
						if((int)$fee->fee_currency_id != $v_order->order_currency_id)
							continue;
						if((float)hikamarket::toFloat($fee->fee_min_price) > $v_order->order_shipping_price)
							continue;

						$v_order->order_vendor_params->fees->shipping = (float)((100 - (float)hikamarket::toFloat($fee->fee_percent)) * $v_order->order_shipping_price / 100) - (float)hikamarket::toFloat($fee->fee_value);
						break;
					}
				}

				if(!empty($v_order->order_vendor_params->fees->shipping)) {
					if($v_order->order_vendor_params->fees->shipping > $v_order->order_shipping_price)
						$v_order->order_vendor_params->fees->shipping = $v_order->order_shipping_price;
					$ret -= $v_order->order_vendor_params->fees->shipping;
				}
			}
		}

		$dispatcher->trigger('onAfterMarketCalculateVendorPrice', array($vendor_id, &$ret, &$order_products, &$products, $coupon, $v_order));

		return $ret;
	}

	public function getProductFee(&$product, &$fees, $full_price, $total_price = 0, $total_quantity = 0) {
		$current_product_qty = (int)$product->order_product_quantity;

		$product_fee = array();
		$modes = array('product','vendor','config');
		$global_modes = array('vendor','config');
		$config = hikamarket::config();
		$price_with_tax = ($config->get('calculate_vendor_price_with_tax', false));

		for($i = 0; $i < count($modes) && empty($product_fee); $i++) {
			$mode = $modes[$i];
			$fee_processing = array(
				'qty' => array(-1,$full_price,-1,0,null),
				'price' => array(-1,$full_price,-1,0,null)
			);
			$mode_gbl = null;
			if(in_array($mode, $global_modes))
				$mode_gbl = $mode.'_global';

			foreach($fees as $fee) {
				if($fee->fee_type != $mode && $fee->fee_type != $mode_gbl)
					continue;

				$global_mode = ($fee->fee_type == $mode_gbl);
				if(empty($fee->fee_min_quantity) && empty($fee->fee_min_price))
					$fee->fee_min_quantity = 1;

				if(
				 (!$global_mode && (($current_product_qty >= $fee->fee_min_quantity || $fee->fee_min_quantity <= 1) && ($product->order_product_price >= $fee->fee_min_price || $fee->fee_min_price == 0)))
				 ||
				 ($global_mode && (($total_quantity >= $fee->fee_min_quantity || $fee->fee_min_quantity <= 1) || ($total_price >= $fee->fee_min_price || $fee->fee_min_price == 0)))
				) {
					$product_full_price = (float)((100 - (float)$fee->fee_percent) * $full_price / 100) - (float)($fee->fee_value * $current_product_qty);
					$product_vendor_unit_price = (float)((100 - (float)$fee->fee_percent) * $product->order_product_vendor_price / 100) - $fee->fee_value;

					if($fee->fee_min_quantity > 0 && $fee->fee_min_quantity > $fee_processing['qty'][0])
						$fee_processing['qty'] = array($fee->fee_min_quantity, $product_full_price, $product_vendor_unit_price, $fee->fee_fixed, $fee->fee_id);
					if($fee->fee_min_price > 0 && $fee->fee_min_price > $fee_processing['price'][0])
						$fee_processing['price'] = array($fee->fee_min_price, $product_full_price, $product_vendor_unit_price, $fee->fee_fixed, $fee->fee_id);
				}
			}

			if($fee_processing['qty'][0] >= 0 || $fee_processing['price'][0] >= 0) {
				if($fee_processing['qty'][0] >= 0 && ($fee_processing['price'][0] < 0 || $fee_processing['qty'][1] > $fee_processing['price'][1])) {
					$product_fee = array(
						'price' => $fee_processing['qty'][1],
						'vendor' => $fee_processing['qty'][2],
						'fixed' => $fee_processing['qty'][3],
						'id' => $fee_processing['qty'][4],
						'type' => 'qty',
						'mode' => $fee->fee_type
					);
				} else {
					$product_fee = array(
						'price' => $fee_processing['price'][1],
						'vendor' => $fee_processing['price'][2],
						'fixed' => $fee_processing['qty'][3],
						'id' => $fee_processing['price'][4],
						'type' => 'price',
						'mode' => $fee->fee_type
					);
				}
			}
		}
		return $product_fee;
	}

	public function recalculateVendorPrice(&$order) {
		if((int)$order->order_vendor_id <= 1)
			return 0.0;

		$config = hikamarket::config();

		$this->loadOrderProducts($order);

		$ret = 0.0;
		foreach($order->order_products as $order_product) {
			$ret += (int)$order_product->order_product_quantity * (float)hikamarket::toFloat($order_product->order_product_vendor_price);
		}

		if(!empty($order->order_payment_price))
			$ret += (float)hikamarket::toFloat($order->order_payment_price);
		if(!empty($order->order_shipping_price))
			$ret += (float)hikamarket::toFloat($order->order_shipping_price);

		if(!empty($order->order_discount_price))
			$ret -= (float)hikamarket::toFloat($order->order_discount_price);

		if($config->get('calculate_vendor_price_with_tax', false)) {
			if(!empty($order->order_discount_tax))
				$ret -= (float)hikamarket::toFloat($order->order_discount_tax);
		} else {
			if(!empty($order->order_shipping_tax))
				$ret -= (float)hikamarket::toFloat($order->order_shipping_tax);
			if(!empty($order->order_payment_tax))
				$ret -= (float)hikamarket::toFloat($order->order_payment_tax);
		}

		$order_vendor_params = is_string($order->order_vendor_params) ? hikamarket::unserialize($order->order_vendor_params) : $order->order_vendor_params;
		if(!empty($order_vendor_params->fees->fixed)) {
			foreach($order_vendor_params->fees->fixed as $fixedFee) {
				$ret -= (float)hikamarket::toFloat($fixedFee);
			}
		}
		if(!empty($order_vendor_params->fees->shipping)) {
			$ret -= (float)hikamarket::toFloat($order_vendor_params->fees->shipping);
		}
		return $ret;
	}

	private function recalculateProductPrice($suborder_product, $order_vendor_params) {
		if(empty($order_vendor_params))
			return false;
		$order_vendor_params = is_string($order_vendor_params) ? hikamarket::unserialize($order_vendor_params) : $order_vendor_params;

		if(empty($order_vendor_params->fees->rules))
			return false;

		foreach($order_vendor_params->fees->rules as $rule) {
			if((int)$rule->product_id == (int)$suborder_product->product_id || (int)$rule->product_parent_id == (int)$suborder_product->product_id) {
				$ret = (float)hikamarket::toFloat($suborder_product->order_product_price);
				$ret = (float)((100 - (float)$rule->fee_percent) * $ret / 100) - $rule->fee_value;

				return $ret;
			}
		}
		return false;
	}

	private function getQuotedObject($obj) {
		$fields = array();
		$values = array();
		foreach(get_object_vars($obj) as $k => $v) {
			if(is_array($v) || is_object($v) || $v === null || $k[0] == '_' )
				continue;
			if(!HIKASHOP_J25) {
				$fields[$k] = $this->db->nameQuote($k);
				$values[$k] = $this->db->isQuoted($k) ? $this->db->Quote($v) : (int)$v ;
			} else {
				$fields[$k] = $this->db->quoteName($k);
				$values[$k] = $this->db->Quote($v);
			}
		}
		return array($fields, $values);
	}

	protected function updateVendorRefund(&$subOrder, $vendor_new_total, &$order) {
		$config = hikamarket::config();
		$confirmedOrderStatuses = explode(',', $config->get('valid_order_statuses', 'confirmed,shipped'));

		if($subOrder->order_vendor_paid == 0) {
			$subOrder->order_vendor_price = $vendor_new_total;
			return true;
		}

		$refundUnpaid = 0;
		$refunds_total = 0.0;
		if(!isset($order->hikamarket->refunds))
			$this->loadOrderRefunds($order);

		if(!empty($order->hikamarket->refunds)) {
			foreach($order->hikamarket->refunds as $refund_id => $refund) {
				if($refund->order_vendor_id != $subOrder->order_vendor_id)
					continue;

				if((int)$refund->order_vendor_paid == 0) {
					$refundUnpaid = $refund_id;
				} else {
					$refunds_total += (float)hikamarket::toFloat($subOrder->order_vendor_price);
				}
			}
		}

		$order_status = isset($order->order_status) ? $order->order_status : $order->old->order_status;
		$currency_id = (int)(isset($order->order_currency_id) ? $order->order_currency_id : $order->old->order_currency_id);
		if(empty($currency_id)) {
			$currency_id = (int)(isset($subOrder->order_currency_id) ? $subOrder->order_currency_id : $subOrder->old->order_currency_id);
		}

		if(in_array($order_status, $confirmedOrderStatuses)) {
			$refunds_total = $vendor_new_total - $refunds_total - $subOrder->order_vendor_price;
		} else {
			$refunds_total = - $subOrder->order_vendor_price - $refunds_total;
		}

		if($refundUnpaid == 0 && $refunds_total == 0) {
			return true;
		}

		if($refundUnpaid > 0) {
			$query = 'UPDATE ' . hikamarket::table('shop.order') .
				' SET order_vendor_price = ' . (float)$refunds_total . ', order_status = ' . $this->db->Quote($order->order_status) .
				' WHERE order_id = ' . (int)$refundUnpaid . ' AND order_type = ' . $this->db->Quote('vendorrefund');
		} else {
			$query = 'INSERT INTO ' . hikamarket::table('shop.order') .
				' (order_type, order_vendor_id, order_parent_id, order_status, order_vendor_price, order_currency_id) '.
				' VALUES ('.$this->db->Quote('vendorrefund').','.(int)$subOrder->order_vendor_id.','.(int)$order->order_id.','.$this->db->Quote($order_status).','.(float)$refunds_total.','.(int)$currency_id.')';
		}

		$this->db->setQuery($query);
		$ret = $this->db->query();

		if(empty($ret))
			return false;

		if($refundUnpaid > 0)
			return true;

		$refund = new stdClass();
		$refund->order_id = (int)$ret;
		$refund->order_type = 'vendorrefund';
		$refund->order_vendor_id = (int)$subOrder->order_vendor_id;
		$refund->order_parent_id = (int)$order->order_id;
		$refund->order_status = $order_status;
		$refund->order_vendor_price = (float)$refunds_total;
		$refund->order_currency_id = (int)$currency_id;
		$order->hikamarket->refunds[ $ret ] = $refund;

		return true;
	}

	private function processShippingParams(&$order, $vendor_id, &$vendor_order, $total_products, $total_products_vendor, $products = null) {
		$config = hikamarket::config();
		$shipping_per_vendor = false;
		$shipping_found = false;

		JPluginHelper::importPlugin('hikamarket');
		$dispatcher = JDispatcher::getInstance();
		$continue = true;
		$dispatcher->trigger('onBeforeMarketProcessShippingParams', array(&$vendor_order, $vendor_id, $total_products, $total_products_vendor, $order, $products, $continue));
		if(!$continue)
			return;

		if($vendor_id == 0)
			$vendor_id = 1;

		if(!empty($order->cart->shipping)) {
			$vendor_order->cart->shipping = array();
			foreach($order->cart->shipping as $shipping) {
				$warehouse = null; $shipping_vendor = null;
				if(!empty($shipping->shipping_warehouse_id) && is_string($shipping->shipping_warehouse_id)) {
					if(strpos($shipping->shipping_warehouse_id, 'v') !== false)
						list($warehouse, $shipping_vendor) = explode('v', $shipping->shipping_warehouse_id, 2);
					else
						$warehouse = $shipping->shipping_warehouse_id;
				}
				if(!empty($shipping->shipping_warehouse_id) && is_array($shipping->shipping_warehouse_id)) {
					$warehouse = $shipping->shipping_warehouse_id[''];
					$shipping_vendor = $shipping->shipping_warehouse_id['v'];
				}

				if($shipping_vendor !== null) {
					if((int)$shipping_vendor == $vendor_id) {
						$vendor_order->order_shipping_price += $shipping->shipping_price_with_tax;
						$vendor_order->order_shipping_tax += $shipping->shipping_price_with_tax - $shipping->shipping_price;
						$shipping_found = true;
					}
					$vendor_order->cart->shipping[] = $shipping;
				} else if($vendor_id == 1 && $shipping_vendor === null) {
					$vendor_order->order_shipping_price += $shipping->shipping_price_with_tax;
					$vendor_order->order_shipping_tax += $shipping->shipping_price_with_tax - $shipping->shipping_price;
					$shipping_found = true;

					$vendor_order->cart->shipping[] = $shipping;
				}

				if(!$shipping_per_vendor && $shipping_vendor !== null)
					$shipping_per_vendor = true;
			}
		} else if(!empty($order->order_shipping_params)) {
			foreach($order->order_shipping_params->prices as $key => $prices) {
				if(strpos($key, 'v') !== false) {
					list($null, $shipping_vendor) = explode('v', $key, 2);
					if((int)$shipping_vendor == $vendor_id) {
						$vendor_order->order_shipping_price += $prices->price_with_tax;
						$vendor_order->order_shipping_tax += $prices->tax;
						$shipping_found = true;
					}
				} else if($vendor_id == 1 && strpos($key, 'v') === false) {
					$vendor_order->order_shipping_price += $prices->price_with_tax;
					$vendor_order->order_shipping_tax += $prices->tax;
					$shipping_found = true;
				}
				if(!$shipping_per_vendor && (
						(is_string($shipping->shipping_warehouse_id) && strpos($shipping->shipping_warehouse_id, 'v') !== false) ||
						(is_array($shipping->shipping_warehouse_id) && isset($shipping->shipping_warehouse_id['v']))
					)
				) {
					$shipping_per_vendor = true;
				}
			}
		}

		if(!empty($vendor_order->order_shipping_price) || $shipping_found) {
			$vendor_order->order_shipping_id = '';
			$order_shipping_id = explode(';', $order->order_shipping_id);
			$order_shipping_vendor = 'v' . $vendor_id;

			foreach($order_shipping_id as $order_shipping) {
				if(($vendor_id == 1 && strpos($order_shipping, 'v') === false) || substr($order_shipping, -strlen($order_shipping_vendor)) == $order_shipping_vendor) {
					if(!empty($vendor_order->order_shipping_id))
						$vendor_order->order_shipping_id .= ';';
					$vendor_order->order_shipping_id .= $order_shipping;
				}
				if(!$shipping_per_vendor && strpos($order_shipping, 'v') !== false)
					$shipping_per_vendor = true;
			}
			if(empty($vendor_order->order_shipping_params))
				$vendor_order->order_shipping_params = new stdClass();
			$vendor_order->order_shipping_params->prices = array();
			foreach($order->order_shipping_params->prices as $k => $v) {
				if(($vendor_id == 1 && strpos($k, 'v') === false) || substr($k, -strlen($order_shipping_vendor)) == $order_shipping_vendor) {
					$vendor_order->order_shipping_params->prices[$k] = $v;
					if(empty($vendor_order->order_shipping_id))
						$vendor_order->order_shipping_id = substr($k, 0, strpos('@', $k));
				}
				if(!$shipping_per_vendor && strpos($k, 'v') !== false)
					$shipping_per_vendor = true;
			}

			if(empty($vendor_order->order_shipping_id) && !empty($vendor_order->cart->shipping)) {
				foreach($vendor_order->cart->shipping as $s) {
					if(!empty($vendor_order->order_shipping_id))
						$vendor_order->order_shipping_id .= ';';
					$vendor_order->order_shipping_id .= $s->shipping_id;
				}
			}
		}

		if(empty($vendor_order->order_shipping_price) && !$shipping_per_vendor && $config->get('split_order_shipping_fees', 0) && !empty($total_products)) {
			$vendor_order->order_shipping_price = $order->order_shipping_price * $total_products_vendor / $total_products;
			$vendor_order->order_shipping_tax = $order->order_shipping_tax * $total_products_vendor / $total_products;
		}

		if(!empty($vendor_order->order_tax_info)) {
			foreach($vendor_order->order_tax_info as $tax_namekey => &$tax) {
				$tax->tax_amount_for_shipping = 0;
			}
			unset($tax);
		}
		if(!empty($vendor_order->order_shipping_params) && !empty($vendor_order->order_shipping_params->prices)) {
			foreach($vendor_order->order_shipping_params->prices as $shipping_price) {
				if(empty($shipping_price->taxes))
					continue;
				foreach($shipping_price->taxes as $tax_namekey => $tax_value) {
					$vendor_order->order_tax_info[$tax_namekey]->tax_amount_for_shipping += $tax_value;
				}
			}
		}

		if(empty($vendor_order->order_shipping_price) && $shipping_per_vendor && !$shipping_found) {
			$vendor_order->order_shipping_id = '';
		}
	}



	public function processView(&$view) {
		$shopOrderBackendHelper = hikamarket::get('helper.shop-order_backend');
		if($shopOrderBackendHelper)
			$shopOrderBackendHelper->processView($view);
	}
}
