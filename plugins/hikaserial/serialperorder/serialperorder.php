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
class plgHikaserialSerialperorder extends hikaserialPlugin {

	protected $type = 'plugin';
	protected $multiple = true;
	protected $doc_form = 'serialperorder-';

	protected $pluginConfig = array(
		'pack_id' => array('SERIAL_PACK', 'pack'),
		'quantity' => array('PRODUCT_QUANTITY', 'int', 1),
		'min_order_quantity' => array('SHIPPING_MIN_QUANTITY', 'int', 0),
		'max_order_quantity' => array('SHIPPING_MAX_QUANTITY', 'int', 0),
		'order_currency' => array('CURRENCY', 'currency'),
		'min_order_price' => array('SHIPPING_MIN_PRICE', 'float', 0),
		'max_order_price' => array('SHIPPING_MAX_PRICE', 'float', 0),
	);

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	public function configurationHead() {
		return array(
			'pack' => array(
				'title' => JText::_('SERIAL_PACK'),
				'cell' => 'width="20%"'
			),
			'restriction' => array(
				'title' => JText::_('RESTRICTIONS'),
				'cell' => 'width="25%"'
			)
		);
	}

	public function configurationLine($id = 0, $conf = null) {

		switch($id) {
			case 'pack':
				if(empty($this->packs)) {
					$db = JFactory::getDBO();
					$db->setQuery('SELECT * FROM '.hikaserial::table('pack'));
					$this->packs = $db->loadObjectList('pack_id');
				}
				$ret = array();
				if(!empty($conf->plugin_params->pack_id) && isset($this->packs[(int)$conf->plugin_params->pack_id]))
					return '<a href="'.hikaserial::completeLink('pack&task=edit&cid='.(int)$conf->plugin_params->pack_id).'">'.$this->packs[(int)$conf->plugin_params->pack_id]->pack_name.'</a>';

			case 'restriction':
				$ret = array();
				if(!empty($conf->plugin_params->min_order_quantity) && (int)$conf->plugin_params->min_order_quantity > 0)
					$ret['min_qty'] = JText::_('SHIPPING_MIN_QUANTITY') . ': ' . (int)$conf->plugin_params->min_order_quantity;
				if(!empty($conf->plugin_params->max_order_quantity) && (int)$conf->plugin_params->max_order_quantity > 0)
					$ret['max_qty'] = JText::_('SHIPPING_MAX_QUANTITY') . ': ' . (int)$conf->plugin_params->max_order_quantity;

				if((!empty($conf->plugin_params->min_order_price) || !empty($conf->plugin_params->max_order_price)) && empty($this->currencyClass))
					$this->currencyClass = hikaserial::get('shop.class.currency');

				if(!empty($conf->plugin_params->min_order_price) && (float)hikaserial::toFloat($conf->plugin_params->min_order_price) > 0.0)
					$ret['min_price'] = JText::_('SHIPPING_MIN_PRICE') . ': ' . $this->currencyClass->format($conf->plugin_params->min_order_price, (int)$conf->plugin_params->order_currency);
				if(!empty($conf->plugin_params->max_order_price) && (float)hikaserial::toFloat($conf->plugin_params->max_order_price) > 0.0)
					$ret['max_price'] = JText::_('SHIPPING_MAX_PRICE') . ': ' . $this->currencyClass->format($conf->plugin_params->max_order_price, (int)$conf->plugin_params->order_currency);

				return implode('<br/>', $ret);
		}
		return null;
	}

	public function onPluginConfiguration(&$elements) {
		parent::onPluginConfiguration($elements);

		$element = $elements;
		if(is_array($elements))
			$element = reset($elements);

		if(empty($element))
			$element = new stdClass();
		if(empty($element->plugin_params))
			$element->plugin_params = new stdClass();
		if(empty($element->plugin_params->quantity) || (int)$element->plugin_params->quantity == 0)
			$element->plugin_params->quantity = 1;
	}

	public function onPluginConfigurationSave(&$element) {
		if(empty($element->plugin_params->quantity) || (int)$element->plugin_params->quantity == 0)
			$element->plugin_params->quantity = 1;

		$element->plugin_params->min_order_quantity = (int)@$element->plugin_params->min_order_quantity;
		$element->plugin_params->max_order_quantity = (int)@$element->plugin_params->max_order_quantity;

		$element->plugin_params->min_order_price = (float)hikaserial::toFloat(@$element->plugin_params->min_order_price);
		$element->plugin_params->max_order_price = (float)hikaserial::toFloat(@$element->plugin_params->max_order_price);

		if($element->plugin_params->max_order_quantity > 0 && $element->plugin_params->max_order_quantity < $element->plugin_params->min_order_quantity) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('SHIPPING_MAX_QUANTITY') . ' < ' . JText::_('SHIPPING_MIN_QUANTITY'), 'error');
		}
		if($element->plugin_params->max_order_price > 0.0 && $element->plugin_params->max_order_price < $element->plugin_params->min_order_price) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('SHIPPING_MAX_PRICE') . ' < ' . JText::_('SHIPPING_MIN_PRICE'), 'error');
		}
	}

	public function onSerialOrderPreUpdate($new, &$order, &$order_serial_params) {
		if(!$new)
			return;

		$order_details = null;

		$ids = array();
		parent::listPlugins('serialperorder', $ids, false);
		foreach($ids as $id) {
			parent::pluginParams($id);

			if((int)$this->plugin_params->quantity == 0 || (int)$this->plugin_params->pack_id == 0)
				continue;

			if(!empty($this->plugin_params->min_order_quantity) || !empty($this->plugin_params->max_order_quantity) || !empty($this->plugin_params->min_order_price) || !empty($this->plugin_params->max_order_price)) {

				if(empty($order_details))
					$order_details = $this->getOrderDetails($order);

				if(!empty($this->plugin_params->min_order_price))
					$this->plugin_params->min_order_price = (float)hikaserial::toFloat($this->plugin_params->min_order_price);
				else
					$this->plugin_params->min_order_price = 0;

				if(!empty($this->plugin_params->max_order_price))
					$this->plugin_params->max_order_price = (float)hikaserial::toFloat($this->plugin_params->max_order_price);
				else
					$this->plugin_params->max_order_price = 0;

				if(!empty($this->plugin_params->min_order_quantity) && (int)$this->plugin_params->min_order_quantity > 0 && (int)$this->plugin_params->min_order_quantity > $order_details['product_quantity'])
					continue;
				if(!empty($this->plugin_params->max_order_quantity) && (int)$this->plugin_params->max_order_quantity > (int)$this->plugin_params->min_order_quantity && (int)$this->plugin_params->max_order_quantity < $order_details['product_quantity'])
					continue;

				if($this->plugin_params->min_order_price > 0 || $this->plugin_params->max_order_price > 0) {
					if($order_details['currency_id'] != (int)@$this->plugin_params->order_currency)
						continue;

					if($this->plugin_params->min_order_price > 0 && $order_details['full_price'] < $this->plugin_params->min_order_price)
						continue;

					if($this->plugin_params->max_order_price > 0 && $order_details['full_price'] > $this->plugin_params->max_order_price)
						continue;
				}
			}

			if(empty($order_serial_params['order'][ (int)$this->plugin_params->pack_id ]))
				$order_serial_params['order'][ (int)$this->plugin_params->pack_id ] = array(0, 0);
			if(!is_array($order_serial_params['order'][ (int)$this->plugin_params->pack_id ]))
				$order_serial_params['order'][ (int)$this->plugin_params->pack_id ] = array($order_serial_params['order'][ (int)$this->plugin_params->pack_id ], 0);
			$order_serial_params['order'][ (int)$this->plugin_params->pack_id ][1] += (int)$this->plugin_params->quantity;
		}
	}

	protected function getOrderDetails($order) {
		$ret = array(
			'product_quantity' => 0,
			'currency_id' => isset($order->order_currency_id) ? (int)$order->order_currency_id : (int)@$order->old->order_currency_id,
			'full_price' => isset($order->order_full_price) ? (float)hikaserial::toFloat($order->order_full_price) : (float)hikaserial::toFloat(@$order->old->order_full_price),
		);

		if(empty($order->cart->products))
			return $ret;

		foreach($order->cart->products as $p) {
			if(empty($p->product_id))
				continue;
			$ret['product_quantity'] += (int)@$p->order_product_quantity;
		}

		return $ret;
	}
}
