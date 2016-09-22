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
class plgHikaserialMailtocustomfield extends hikaserialPlugin {

	protected $type = 'plugin';
	protected $multiple = true;
	protected $doc_form = 'mailtocustomfield-';

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	public function configurationHead() {
		return array(
			'packs' => array(
				'title' => JText::_('SERIAL_PACKS'),
				'cell' => 'width="30%"'
			)
		);
	}

	public function configurationLine($id = 0, $conf = null) {
		if(empty($this->toggleHelper))
			$this->toggleHelper = hikaserial::get('helper.toggle');

		switch($id) {
			case 'packs':
				if(empty($this->packs)) {
					$db = JFactory::getDBO();
					$db->setQuery('SELECT * FROM '.hikaserial::table('pack'));
					$this->packs = $db->loadObjectList('pack_id');
				}
				$ret = array();
				if(!empty($conf->plugin_params->packs)) {
					if(!is_array($conf->plugin_params->packs))
						$conf->plugin_params->packs = array($conf->plugin_params->packs);
					foreach($conf->plugin_params->packs as $p) {
						if(isset($this->packs[(int)$p]))
							$ret[] = '<a href="'.hikaserial::completeLink('pack&task=edit&cid='.(int)$p).'">'.$this->packs[(int)$p]->pack_name.'</a>';
					}
				}
				return implode(', ', $ret);
		}
		return null;
	}

	public function onPluginConfigurationSave(&$element) {
	}

	public function onBeforeSerialMailSend(&$mail, &$mailer, &$serials, $order) {
		$ids = array();
		parent::listPlugins('mailtocustomfield', $ids, false);

		if(empty($ids))
			return;

		$fullOrder = null;

		foreach($ids as $id) {
			parent::pluginParams($id);
			$customfield_type = null;

			foreach($serials as $k => $serial) {
				if(is_string($this->plugin_params->packs) && strpos(','.$this->plugin_params->packs.',', ','.$serial->serial_pack_id.',') === false)
					continue;
				if(is_array($this->plugin_params->packs) && !in_array($serial->serial_pack_id, $this->plugin_params->packs))
					continue;

				$custom_field = $this->plugin_params->custom_field;
				if(empty($custom_field))
					continue;

				list($customfield_type, $custom_field) = explode('.', $custom_field, 2);

				if($fullOrder === null) {
					$orderClass = hikaserial::get('shop.class.order');
					$fullOrder = $orderClass->loadFullOrder($order->order_id, true, false);
				}

				if($customfield_type == 'order' && !isset($fullOrder->$custom_field))
					continue;

				if($customfield_type == 'item') {
					$f = false;
					foreach($fullOrder->products as $k => $product) {
						if(isset($product->$custom_field)) {
							$f = true;
							break;
						}
					}
					if(!$f)
						continue;
				}

				if($customfield_type == 'item') {
					$this->sendCustomEmail($serial, $fullOrder);
				} else {
					$this->sendCustomEmail($serials, $fullOrder);
					break;
				}
			}
		}
	}

	private function sendCustomEmail($serials, &$order) {
		$send_serials = array();
		$custom_field_value = '';
		list($type, $custom_field) = explode('.', $this->plugin_params->custom_field, 2);

		if(!in_array($type, array('order', 'item')))
			return false;

		if($type == 'order') {
			foreach($serials as $k => $serial) {
				if(is_string($this->plugin_params->packs) && strpos(','.$this->plugin_params->packs.',', ','.$serial->serial_pack_id.',') === false)
					continue;
				if(is_array($this->plugin_params->packs) && !in_array($serial->serial_pack_id, $this->plugin_params->packs))
					continue;
				$send_serials[] = $serial;
			}
			if(!empty($order->$custom_field))
				$custom_field_value = $order->$custom_field;
		}

		if($type == 'item') {
			$send_serials[] = $serials;
			foreach($order->products as $k => $product) {
				if((int)$product->order_product_id == (int)$serials->serial_order_product_id) {
					if(!empty($product->$custom_field))
						$custom_field_value = $product->$custom_field;
					break;
				}
			}
		}

		$custom_field_value = trim($custom_field_value);
		if(empty($custom_field_value))
			return false;
		if(!preg_match('/^([a-z0-9_\'&\.\-\+=])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,10})+$/i', $custom_field_value))
			return false;

		$data = new stdClass();
		$data->serials = $send_serials;
		$data->dest_email = $custom_field_value;
		$data->order =& $order;
		$mailClass = hikaserial::get('class.mail');
		$mail = $mailClass->load('mailtocustomfield', $data);

		if(empty($mail))
			return false;

		if(!empty($this->plugin_params->call_attachserial)) {
			$attachserialPlugin = hikaserial::import('hikaserial', 'attachserial');
			$attachserialPlugin->onBeforeSerialMailSend($mail, $mail->mailer, $send_serials, $order);
		}

		$mail->subject = JText::sprintf($mail->subject, HIKASERIAL_LIVE);
		$shopConfig =& hikaserial::config(false);

		if(empty($mail->from_email)) {
			$mail->from_email = $shopConfig->get('from_email');
			$mail->from_name = $shopConfig->get('from_name');
		}
		if(empty($mail->reply_email)) {
			$mail->reply_email = $shopConfig->get('from_email');
			$mail->reply_name = $shopConfig->get('from_name');
		}

		if(empty($mail->dst_email))
			$mail->dst_email = $custom_field_value;
		if(empty($mail->dst_name) && strpos($custom_field_value, '@') !== false)
			$mail->dst_name = substr($custom_field_value, 0, strpos($custom_field_value, '@'));

		$ret = $mailClass->sendMail($mail);

		return true;
	}
}
