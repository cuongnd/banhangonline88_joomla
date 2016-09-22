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

	const ctrl = 'orderserial';
	const name = 'HIKASERIAL_ORDERSERIAL';
	const icon = 'generic';

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKASERIAL_COMPONENT.'.'.$this->getName();
		$fct = $this->getLayout();
		if(method_exists($this, $fct))
			$this->$fct($params);
		parent::display($tpl);
	}

	public function show($params = null, $viewName = 'email-notification', $loadLocale = false, $edition = false) {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$config = hikaserial::config();
		$this->assignRef('config', $config);

		$data = null;
		$order_id = 0;
		$this->assignRef('data', $data);
		$this->assignRef('order_id', $order_id);

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

		$nameboxType = hikaserial::get('shop.type.namebox');
		$this->assignRef('nameboxType', $nameboxType);

		$ajax = (JRequest::getVar('tmpl', '') == 'component');
		$this->assignRef('ajax', $ajax);

		$show_refresh = $edition;
		$manage_order_packs = $edition;
		if($order->order_type == 'subsale') {
			$order_id = $order->order_parent_id;
			$show_refresh = false;
			$manage_order_packs = false;
		}
		$this->assignRef('show_refresh', $show_refresh);
		$this->assignRef('manage_order_packs', $manage_order_packs);

		if($order->order_type == 'vendorpayment') {
			$show_refresh = false;
			$manage_order_packs = false;
			return false;
		}

		if(!empty($order->order_serial_params) && is_string($order->order_serial_params))
			$order->order_serial_params = json_decode($order->order_serial_params, true);

		if($manage_order_packs && !empty($order->order_serial_params))
			$this->assignRef('order_serial_params', $order->order_serial_params);

		if($order_id > 0) {
			$display_serial_statuses = $config->get('display_serial_statuses','');
			if(empty($display_serial_statuses)) {
				$display_serial_statuses = array();
			} else {
				$display_serial_statuses = explode(',', $display_serial_statuses);
			}

			$status = $config->get('used_serial_status','used');
			if(!in_array($status, $display_serial_statuses))
				$display_serial_statuses[] = $status;

			$status = $config->get('assigned_serial_status','assigned');
			if(!in_array($status, $display_serial_statuses))
				$display_serial_statuses[] = $status;

			foreach($display_serial_statuses as &$s) {
				$s = $db->Quote($s);
			}
			unset($s);

			$query = 'SELECT serial.*, pack.*, order_product.*, u.user_cms_id FROM '.
				hikaserial::table('serial') . ' as serial '.
				'INNER JOIN '. hikaserial::table('pack') . ' as pack ON serial.serial_pack_id = pack.pack_id '.
				'LEFT JOIN ' . hikaserial::table('shop.order_product') . ' as order_product ON serial.serial_order_product_id = order_product.order_product_id '.
				'LEFT JOIN ' . hikaserial::table('shop.user') . ' AS u ON serial.serial_user_id = u.user_id '.
				'WHERE serial.serial_status IN ('.implode(',', $display_serial_statuses).') AND serial.serial_order_id = ' . $order_id;
			$db->setQuery($query);
			$data = $db->loadObjectList();
		}

		if($order->order_type == 'subsale' && !empty($order->products)) {
			foreach($data as $k => $v) {
				$order_product_id = (int)$v->order_product_id;
				$f = false;
				foreach($order->products as $order_product) {
					if((int)$order_product->order_product_parent_id == $order_product_id) {
						$f = true;
						break;
					}
				}
				if(!$f)
					unset($data[$k]);
			}
		}

		JPluginHelper::importPlugin('hikaserial');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onDisplaySerials', array(&$data, $viewName));

		$packs = array();

		$order_packs = array();
		if($manage_order_packs && !empty($order->order_serial_params['order'])) {
			$order_quantity = 0;

			foreach($order->order_serial_params['order'] as $pack => $o) {
				$order_packs[$pack] = $o;
				$packs[ (int)$pack ] = (int)$pack;
				if(!empty($order_packs[$pack][0])) {
					if(empty($order_quantity)) {
						foreach($order->products as $p) {
							$order_quantity += $p->order_product_quantity;
						}
					}
					$order_packs[$pack][0] *= $order_quantity;
				}
			}
		}

		if(!empty($packs)) {
			$query = 'SELECT * FROM ' . hikaserial::table('pack') . ' WHERE pack_id IN (' . implode(',', $packs) . ')';
			$db->setQuery($query);
			$packs = $db->loadObjectList('pack_id');
		}
		$this->assignRef('packs', $packs);

		$user_cms_id = 0;
		foreach($data as &$serial) {
			if(!isset($serial->serial_text_data)) {
				$serial->serial_text_data = $serial->serial_data;
				$serial->serial_data = str_replace(array("\r\n","\r","\n"), '<br/>', $serial->serial_data);
			}
			if(!empty($serial->user_cms_id))
				$user_cms_id = $serial->user_cms_id;

			if(!$manage_order_packs || $serial->order_product_name !== null)
				continue;

			$serial->assignation = null;

			if(!empty($order->order_serial_params['serial']) && in_array($serial->serial_id, $order->order_serial_params['serial']))
				$serial->assignation = 'serial';

			if($serial->assignation === null && !empty($order_packs)) {
				foreach($order_packs as $pack => $o) {
					if($pack != $serial->pack_id || ($o[0] == 0 && $o[1] == 0))
						continue;

					if($o[0] > 0) $o[0]--;
					else $o[1]--;
					$serial->assignation = 'order';
				}
			}
		}
		unset($serial);

		if($loadLocale) {
			$locale = '';
			if(!empty($user_cms_id)) {
				$user = JFactory::getUser($user_cms_id);
				$locale = $user->getParam('language');
				if(empty($locale)) {
					$locale = $user->getParam('admin_language');
				}
			}
			if(empty($locale)) {
				$params = JComponentHelper::getParams('com_languages');
				$locale = $params->get('site', 'en-GB');
			}
			if(!empty($locale)) {
				$lang = JFactory::getLanguage();
				$lang->load(HIKASERIAL_COMPONENT, JPATH_SITE, $locale, true);
			}
		}
	}

	public function show_order_back_show($params = null) {
		$this->show($params, 'back-order-show', false, true);
	}

	public function show_order_back_invoice($params = null) {
		$this->show($params, 'back-invoice-show', false, false);
	}

	public function show_email_notification($params = null) {
		$this->show($params, 'email-notification', true, false);
	}

	public function show_email_notification_html($params = null) {
		$this->show($params, 'email-notification', true, false);
	}
}
