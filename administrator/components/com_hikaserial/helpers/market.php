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
class hikaserialMarketHelper {
	public function processOrderEditionLoading(&$order, $block = null) {
		if(!defined('HIKAMARKET_COMPONENT'))
			return;

		if(isset($order->hikaserial->serials) || isset($order->hikaserial->serials_managed))
			return;

		$db = JFactory::getDBO();
		$config = hikaserial::config();
		$marketConfig = hikamarket::config();
		$shopConfig = hikaserial::config(false);
		$orderSerialClass = hikaserial::get('class.order');

		$data = null;
		$order_id = (int)$order->order_id;
		if(empty($order_id))
			return;

		if(!isset($order->hikaserial))
			$order->hikaserial = new stdClass();
		$order->hikaserial->serials_managed = true;

		if($order->order_type == 'subsale' && !empty($order->order_parent_id))
			$order_id = (int)$order->order_parent_id;

		$order->hikaserial->serials = $orderSerialClass->loadSerialData($order_id, 'order_frontmarket_trigger');

		if(empty($order->hikaserial->serials))
			return;

		foreach($order->hikaserial->serials as &$serial) {
			if(empty($serial->order_product_id))
				continue;

			$serial->order_product_id = (int)$serial->order_product_id;

			foreach($order->products as &$product) {
				if($order->order_type == 'sale' && (int)$product->order_product_id != $serial->order_product_id)
					continue;
				if($order->order_type == 'subsale' && (int)$product->order_product_parent_id != $serial->order_product_id)
					continue;

				if(!isset($product->serials))
					$product->serials = array();

				$product->serials[] = $serial;
			}
			unset($product);
		}
		unset($serial);

		foreach($order->products as &$product) {
			if(empty($product->serials))
				continue;

			$serials = array();
			foreach($product->serials as $s) {
				$serials[] = $s->serial_data;
			}

			if(empty($product->extraData))
				$product->extraData = array();
			$product->extraData['serial'] = '
<a href="#serials" data-toggle-display="hikamarket_order_product_serials_'.(int)$product->order_product_id.'" onclick="return window.orderMgr.toggleDisplay(this);">'.JText::_('HIKAM_SHOW_SERIALS').'</a>
<ul id="hikamarket_order_product_serials_'.(int)$product->order_product_id.'" style="display:none;">
	<li class="hikamarket_product_serial">'.implode('</li><li class="hikamarket_product_serial">', $serials). '</li>
</ul>
';
		}
		unset($product);
	}
}
