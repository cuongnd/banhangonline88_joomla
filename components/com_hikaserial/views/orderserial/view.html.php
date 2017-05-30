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
class orderserialViewOrderserial extends hikaserialView {

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKASERIAL_COMPONENT.'.'.$this->getName();
		$fct = $this->getLayout();
		if(method_exists($this, $fct))
			$this->$fct($params);
		parent::display($tpl);
	}

	public function show($params = null, $viewName = 'email-notification') {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$config = hikaserial::config();
		$this->assignRef('config', $config);

		$data = null;
		$order_id = 0;

		if(!empty($params)) {
			$order_id = (int)$params->get('order_id');
			$order = $params->get('order_obj', null);
		} else {
			$order_id = hikaserial::getCID('order_id');
		}

		if(empty($order)) {
			$orderClass = hikaserial::get('shop.class.order');
			$order = $orderClass->loadFullOrder($order_id);
		}
		if($order->order_type == 'subsale' && !empty($order->order_parent_id)) {
			$order_id = (int)$order->order_parent_id;
		}

		if(isset($order->hikaserial->serials_managed))
			return;

		if(empty($order->hikaserial->serials)) {
			$serialOrderClass = hikaserial::get('class.order');
			if(empty($order->hikaserial))
				$order->hikaserial = new stdClass();
			$order->hikaserial->serials = $serialOrderClass->loadSerialData($order_id, $viewName);
		}

		$this->assignRef('data', $order->hikaserial->serials);
		$this->assignRef('order_id', $order_id);
	}

	public function show_order_front_show($params = null) {
		$this->show($params, 'front-order-show');
	}

	public function show_order_frontvendor_show($params = null) {
		$this->show($params, 'front-order-show');
	}

	public function show_order_frontmarket_show($params = null) {
		$this->show($params, 'front-order-show');
	}

	public function show_order_frontvendor_invoice($params = null) {
		$this->show($params, 'front-order-invoice');
	}

	public function show_email_notification_html($params = null) {
		$this->show($params, 'email-notification');
	}
}
