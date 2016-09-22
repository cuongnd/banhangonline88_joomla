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
class hikamarketShippingClass extends hikamarketClass {

	protected $tables = array('shop.shipping');
	protected $pkeys = array('shipping_id');
	protected $toggle = array('shipping_published' => 'shipping_id');
	protected $toggleAcl = array('shipping' => 'shipping_published');
	protected $deleteToggle = array('shop.shipping' => array('shipping_id', 'shipping_type'));

	public function save(&$element) {
		$shopClass = hikamarket::get('shop.class.shipping');
		return $shopClass->save($element);
	}

	public function onShippingWarehouseFilter(&$shipping_groups, &$order, &$rates) {
		$config = hikamarket::config();
		if(!$config->get('shipping_per_vendor', 1))
			return;

		$orderClass = hikamarket::get('class.order');
		$assignedProducts = $orderClass->getProductVendorAttribution($order);

		$singlegroup = (count($shipping_groups) == 1);

		$new_groups = array();
		$vendors = array();
		$moveGroups = array();

		foreach($shipping_groups as $group_id => $shipping_group) {
			foreach($shipping_group->products as $k => $product) {
				$vendor_id = (int)$product->product_vendor_id;
				if(isset($assignedProducts[(int)$product->cart_product_id]) && !empty($assignedProducts[(int)$product->cart_product_id]['vendor']))
					$vendor_id = (int)$assignedProducts[(int)$product->cart_product_id]['vendor'];
				if($vendor_id > 1) {
					$key = $group_id . ';' . $vendor_id;
					if(!isset($new_groups[$key])) {
						$new_groups[$key] = new stdClass();
						$new_groups[$key]->products = array();
						$new_groups[$key]->shippings = array();
					}
					$new_groups[$key]->products[] = $product;
					$vendors[] = $vendor_id;
					unset($shipping_group->products[$k]);
				}
			}

			if(!empty($shipping_group->products))
				$moveGroups[] = $group_id;
		}

		foreach($moveGroups as $group_id) {
			$move_shipping_group =& $shipping_groups[$group_id];
			unset($shipping_groups[$group_id]);
			$shipping_groups[$group_id.'v1'] =& $move_shipping_group;
			unset($move_shipping_group);
		}

		if(!empty($new_groups)) {
			$query = 'SELECT vendor_id, vendor_name FROM '.hikamarket::table('vendor').' WHERE vendor_id in (' . implode(',', $vendors).')';
			$this->db->setQuery($query);
			$vendorNames = $this->db->loadObjectList('vendor_id');
			foreach($new_groups as $key => $new_group) {
				list($group_id, $vendor_id) = explode(';', $key, 2);
				$vendor_id = (int)$vendor_id;
				$new_group->name = JText::sprintf('SOLD_BY_VENDOR', $vendorNames[$vendor_id]->vendor_name);
				$shipping_groups[$group_id.'v'.$vendor_id] = $new_group;
			}

			if($singlegroup) {
				$vendorClass = hikamarket::get('class.vendor');
				$mainVendor = $vendorClass->get(1);
				if(isset($shipping_groups['0v1']))
					$shipping_groups['0v1']->name = JText::sprintf('SOLD_BY_VENDOR', $mainVendor->vendor_name);
				else {
					$id = array_keys($shipping_groups);
					$id = reset($id);
					$shipping_groups[$id]->name = JText::sprintf('SOLD_BY_VENDOR', $mainVendor->vendor_name);
				}
			}
		}
	}

	public function onPluginConfiguration(&$plugin, &$element, &$extra_config, &$extra_blocks) {
		$app = JFactory::getApplication();
		$current_vendor_id = 0;
		$vendor_id = '';
		if(!$app->isAdmin())
			$current_vendor_id = hikamarket::loadVendor(false);

		if(!empty($element->shipping_params->shipping_warehouse_filter)) {
			if(strpos($element->shipping_params->shipping_warehouse_filter, 'v') !== false) {
				list($data, $vendor_id) = explode('v', $element->shipping_params->shipping_warehouse_filter, 2);
				$vendor_id = (int)$vendor_id;
				if($vendor_id === 0)
					$vendor_id = '';
				$element->shipping_params->shipping_warehouse_filter = $data;
			}
		}

		if($current_vendor_id > 1)
			return;

		if(empty($vendor_id) && isset($element->shipping_vendor_id))
			$vendor_id = (int)$element->shipping_vendor_id;

		$nameboxType = hikamarket::get('type.namebox');

		$extra_blocks[] = '
<fieldset class="adminform">
	<legend>'.JText::_('VENDOR_OPTIONS').'</legend>
	<table class="admintable table" style="width:100%;">
		<tr>
			<td class="key">
				<label for="data[shipping][shipping_params][shipping_vendor_filter]">'.JText::_('HIKA_VENDOR').'</label>
			</td>
			<td>'.
				$nameboxType->display(
					'data[shipping][shipping_params][shipping_vendor_filter]',
					(int)$vendor_id,
					hikamarketNameboxType::NAMEBOX_SINGLE,
					'vendor',
					array(
						'delete' => true,
						'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>'
					)
				).
			'</td>
		</tr>
	</table>
</fieldset>';
	}

	public function onBeforePluginSave(&$element, &$do, $new = false) {
		$app = JFactory::getApplication();
		if($app->isAdmin() && isset($element->shipping_params->shipping_vendor_filter))
			$element->shipping_vendor_id = (int)$element->shipping_params->shipping_vendor_filter;

		if(!$app->isAdmin() && isset($element->shipping_params) && isset($element->shipping_vendor_id) && (!isset($element->shipping_params->shipping_vendor_filter) || (int)$element->shipping_vendor_id > 1))
			$element->shipping_params->shipping_vendor_filter = (int)$element->shipping_vendor_id;

		if(empty($element->shipping_params->shipping_vendor_filter))
			return;

		if(!empty($element->shipping_params->shipping_warehouse_filter))
			$element->shipping_params->shipping_warehouse_filter .= 'v' . $element->shipping_params->shipping_vendor_filter;
		else
			$element->shipping_params->shipping_warehouse_filter = '0v' . $element->shipping_params->shipping_vendor_filter;
	}

	public function loadConfigurationFields() {
		$main_form = array(
			'shipping_price' => array(
				'name' => 'PRICE',
				'type' => 'price',
				'format' => 'float',
				'link' => 'shipping_currency_id',
				'data' => 'shipping_currency_id',
				'linkformat' => 'int',
			),
			'params.shipping_percentage' => array(
				'name' => 'DISCOUNT_PERCENT_AMOUNT',
				'type' => 'input',
				'format' => 'float',
				'append' => '%'
			),
			'shipping_tax_id' => array(
				'name' => 'TAXATION_CATEGORY',
				'type' => 'tax',
				'format' => 'int'
			),
			'params.shipping_per_product' => array(
				'name' => 'USE_PRICE_PER_PRODUCT',
				'type' => 'boolean',
				'format' => 'boolean',
				'default' => '0'
			),
			'params.shipping_price_per_product' => array(
				'name' => 'PRICE_PER_PRODUCT',
				'type' => 'input',
				'format' => 'float',
				'display' => array(
					'params.shipping_per_product' => 1
				)
			),
			'params.shipping_override_address' => array(
				'name' => 'OVERRIDE_SHIPPING_ADDRESS',
				'type' => 'list',
				'format' => 'int',
				'data' => array(
					0 => 'HIKASHOP_NO',
					1 => 'STORE_ADDRESS',
					2 => 'HIKA_HIDE',
					3 => 'TEXT_VERSION',
					4 => 'HTML_VERSION'
				)
			),
			'params.shipping_override_address_text' => array(
				'name' => 'OVERRIDE_SHIPPING_ADDRESS_TEXT',
				'type' => 'textarea',
				'format' => 'string',
				'display' => array(
					'params.shipping_override_address' => array(3, 4)
				)
			),
			'params.override_tax_zone' => array(
				'name' => 'OVERRIDE_TAX_ZONE',
				'type' => 'zone',
				'format' => 'string'
			)
		);

		$restriction_form = array(
			'shipping_zone_namekey' => array(
				'name' => 'ZONE',
				'type' => 'zone',
				'format' => 'string',
				'category' => 'zone'
			),
			'shipping_currency' => array(
				'name' => 'CURRENCY',
				'type' => 'currencies',
				'format' => 'arrayInt',
				'category' => 'currency'
			),
			'params.shipping_warehouse_filter' => array(
				'name' => 'WAREHOUSE',
				'type' => 'warehouse',
				'format' => 'string',
				'category' => 'warehouse'
			),
			'params.shipping_min_price' => array(
				'name' => 'SHIPPING_MIN_PRICE',
				'type' => 'input',
				'format' => 'float',
				'category' => 'price'
			),
			'params.shipping_max_price' => array(
				'name' => 'SHIPPING_MAX_PRICE',
				'type' => 'input',
				'format' => 'float',
				'category' => 'price'
			),
			'params.shipping_virtual_included' => array(
				'name' => 'INCLUDE_VIRTUAL_PRODUCTS_PRICE',
				'type' => 'boolean',
				'format' => 'boolean',
				'default' => '0',
				'category' => 'price',
				'category_check' => false
			),
			'params.shipping_price_use_tax' => array(
				'name' => 'WITH_TAX',
				'type' => 'boolean',
				'format' => 'boolean',
				'default' => '1',
				'category' => 'price',
				'category_check' => false
			),
			'params.shipping_min_quantity' => array(
				'name' => 'SHIPPING_MIN_QUANTITY',
				'type' => 'input',
				'format' => 'int',
				'category' => 'quantity'
			),
			'params.shipping_max_quantity' => array(
				'name' => 'SHIPPING_MAX_QUANTITY',
				'type' => 'input',
				'format' => 'int',
				'category' => 'quantity'
			),
			'params.shipping_min_weight' => array(
				'name' => 'SHIPPING_MIN_WEIGHT',
				'type' => 'weight',
				'format' => 'float',
				'link' => 'shipping_weight_unit',
				'linkformat' => 'string',
				'category' => 'weight'
			),
			'params.shipping_max_weight' => array(
				'name' => 'SHIPPING_MAX_WEIGHT',
				'type' => 'weight',
				'format' => 'float',
				'link' => 'shipping_weight_unit',
				'linkformat' => 'string',
				'category' => 'weight'
			),
			'params.shipping_min_volume' => array(
				'name' => 'SHIPPING_MIN_VOLUME',
				'type' => 'volume',
				'format' => 'float',
				'link' => 'shipping_size_unit',
				'linkformat' => 'string',
				'category' => 'volume'
			),
			'params.shipping_max_volume' => array(
				'name' => 'SHIPPING_MAX_VOLUME',
				'type' => 'volume',
				'format' => 'float',
				'link' => 'shipping_size_unit',
				'linkformat' => 'string',
				'category' => 'volume'
			),
			'params.shipping_zip_prefix' => array(
				'name' => 'SHIPPING_PREFIX',
				'type' => 'input',
				'format' => 'string',
				'category' => 'postcode'
			),
			'params.shipping_min_zip' => array(
				'name' => 'SHIPPING_MIN_ZIP',
				'type' => 'input',
				'format' => 'string',
				'category' => 'postcode'
			),
			'params.shipping_max_zip' => array(
				'name' => 'SHIPPING_MAX_ZIP',
				'type' => 'input',
				'format' => 'string',
				'category' => 'postcode'
			),
			'params.shipping_zip_suffix' => array(
				'name' => 'SHIPPING_SUFFIX',
				'type' => 'input',
				'format' => 'string',
				'category' => 'postcode'
			),
		);
		if(hikashop_level(2)) {
			$restriction_form['shipping_access'] = array(
				'name' => 'ACCESS_LEVEL',
				'type' => 'acl',
				'format' => 'arrayInt',
				'category' => 'acl',
				'empty_value' => 'all'
			);
		}

		return array(
			'main' => $main_form,
			'restriction' => $restriction_form
		);
	}

	public function &getNameboxData($typeConfig, &$fullLoad, $mode, $value, $search, $options) {
		$ret = array(
			0 => array(),
			1 => array()
		);

		$query = 'SELECT * FROM ' . hikamarket::table('shop.shipping') . ' WHERE shipping_published = 1';
		$this->db->setQuery($query);
		$methods = $this->db->loadObjectList('shipping_id');
		foreach($methods as $method) {
			$plugin = null;
			if($method->shipping_type != 'manual')
				$plugin = hikamarket::import('hikashopshipping', $method->shipping_type);

			if(!empty($plugin) && method_exists($plugin, 'shippingMethods')) {
				if(is_string($method->shipping_params) && !empty($method->shipping_params))
					$method->shipping_params = hikamarket::unserialize($method->shipping_params);
				$instances = $plugin->shippingMethods($method);
				if(!empty($instances)) {
					foreach($instances as $id => $instance) {
						$shipping_namekey = $method->shipping_type . '_' . $id;
						$ret[0][$shipping_namekey] = $method->shipping_name . ' - ' . $instance;
					}
				}
			} else {
				$shipping_namekey = $method->shipping_type . '_' . $method->shipping_id;
				$ret[0][$shipping_namekey] = $method->shipping_name;
			}
		}

		if(!empty($value)) {
			if($mode == hikamarketNameboxType::NAMEBOX_SINGLE) {
				$ret[1] = $ret[0][$value];
			} else {
				if(!is_array($value))
					$value = array($value);
				foreach($value as $v) {
					if(isset($ret[0][$v]))
						$ret[1][$v] = $ret[0][$v];
				}
			}
		}

		return $ret;
	}

	public function toggleId($task, $value = null) {
		if($value !== null) {
			$app = JFactory::getApplication();
			if(!$app->isAdmin() && ((int)$value == 0 || empty($this->toggle[$task]) || !hikamarket::acl('shippingplugin/edit/'.str_replace('shipping_', '', $task)) || !hikamarket::isVendorPlugin((int)$value, 'shipping') ))
				return false;
		}
		if(!empty($this->toggle[$task]))
			return $this->toggle[$task];
		return false;
	}

	public function toggleDelete($value1 = '', $value2 = '') {
		$app = JFactory::getApplication();
		if(!$app->isAdmin() && ((int)$value1 == 0 || !hikamarket::acl('shippingplugin/delete') || !hikamarket::isVendorPlugin((int)$value1, 'shipping')))
			return false;
		if(!empty($this->deleteToggle))
			return $this->deleteToggle;
		return false;
	}
}
