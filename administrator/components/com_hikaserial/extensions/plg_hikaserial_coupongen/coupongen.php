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
class plgHikaserialCoupongen extends hikaserialPlugin {

	protected $type = 'generator';
	protected $multiple = true;
	protected $populate = true;
	protected $name = 'coupongen';
	protected $doc_form = 'coupongen-';
	public $test = false;

	protected $serial_default_format = '[a-zA-Z0-9]{size}';
	protected $pluginConfig = array(
		'discount_id' => array('DISCOUNT_COUPON_ID', 'discount'),
		'discount_percent' => array('DISCOUNT_PRODUCT_PERCENT', 'input', '', ' %'),
		'discount_percent_tax' => array('DISCOUNT_PRODUCT_PERCENT_WITH_TAX', 'boolean'),
		'validity_value' => array('DISCOUNT_VALIDITY_PERIOD', 'period-value'),
		'validity_period' => array('', 'period-type'),
		'size' => array('SERIAL_SIZE', 'input'),
		'format' => array('SERIAL_FORMAT', 'input', '[a-zA-Z0-9]{size}'),
		'test' => array('SERIAL_TEST', 'serial_test')
	);


	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	public function generate(&$pack, &$order, $quantity, &$serials) {
		if(!isset($pack->coupongen))
			return;

		parent::pluginParams($pack->coupongen);

		if(empty($this->plugin_params->format) || !preg_match_all('#\\\[|\\\]|\[[^]]+\]\{.*\}|\[.*\]|.#iU', $this->plugin_params->format, $matches)) {
			$matches = array(array('[a-zA-Z0-9]{size}'));
		}

		$config = hikaserial::config();
		$fastRandom = (int)$config->get('use_fast_random', 0);

		for($q = 0; $q < $quantity; $q++) {
			$serial = '';
			$serialObj = new stdClass();

			if(!HIKASHOP_J16 || $fastRandom) {
				$stat = @stat(__FILE__);
				if(empty($stat) || !is_array($stat)) $stat = array(php_uname());
				mt_srand(crc32(microtime() . implode('|', $stat)));
			} else {
				if(empty($this->plugin_params->size) || $this->plugin_params->size == 0) {
					$this->plugin_params->size = 15;
				}
				$rndCpt = 1;
				$random = JCrypt::genRandomBytes($this->plugin_params->size + 1);
				$shift = ord($random[0]);
			}

			foreach($matches[0] as $m) {
				if(strlen($m) == 1) {
					$serial .= $m;
				} else {
					$repeat = 1;
					$format = $m;

					if(strpos($m, '{') !== false) {
						list($format, $repeat) = explode('{',$m);
						$repeat = trim(trim($repeat, '}'));
						if( empty($repeat) || (int)$repeat == 0 ) {
							$repeat = $this->plugin_params->size;
						} else {
							$repeat = (int)$repeat;
						}
					}
					$format = substr($format, 1, -1);

					$list = '';
					$l = strlen($format);
					for($i = 0; $i < $l; $i++) {
						if($i+2 < $l) {
							if($format[$i+1] == '-') {
								$s = $format[$i];
								$e = $format[$i+2];
								$s1 = ($s >= 'a' && $s <= 'z'); $s2 = ($s >= 'A' && $s <= 'Z'); $s3 = ($s >= '0' && $s <= '9');
								$e1 = ($e >= 'a' && $e <= 'z'); $e2 = ($e >= 'A' && $e <= 'Z'); $e3 = ($e >= '0' && $e <= '9');

								if(!$s1 && !$s2 && !$s3) {
									$list.=$s.'-';
									$i++; // Skip '-'
									continue;
								}

								if( ($s1 && $e1) || ( $s2 && $e2 ) || ( $s3 && $e3 ) ) {
									if($s > $e) { $c = $s; $s = $e; $e = $c; }
									for($c = $s; $c < $e; $c++) {
										$list .= $c;
									}
									$i+=2;
								} else if($s1 && $e2) {
									for($c = $s; $c < 'z'; $c++) {
										$list .= $c;
									}
									for($c = 'A'; $c < $e; $c++) {
										$list .= $c;
									}
									$i+=2;
								} else {
									$list.=$s.'-';
									$i++; // Skip '-'
								}
							} else {
								$list .= $format[$i];
							}
						} else {
							$list .= $format[$i];
						}
					}

					$base = strlen($list);
					if(!HIKASHOP_J16 || $fastRandom) {
						for($i = 1; $i <= $repeat; $i++) {
							$serial .= $list[mt_rand(0, $base-1)];
						}
					} else {
						for($i = 1; $i <= $repeat; $i++) {
							$serial .= $list[($shift + ord($random[$rndCpt])) % $base];
							$shift += ord($random[$rndCpt++]);
							if($rndCpt == $this->plugin_params->size) {
								$rndCpt = 1;
								$random = JCrypt::genRandomBytes($this->plugin_params->size + 1);
								$shift = ord($random[0]);
							}
						}
					}
				}
			}

			$discount_id = (int)$this->plugin_params->discount_id;
			$result = true;
			if(!$this->test && !empty($discount_id)){
				$discountClass = hikaserial::get('shop.class.discount');
				$data = $discountClass->get($discount_id);
				if($data) {
					unset($data->discount_id);
					$data->discount_code = $serial;
					$data->discount_published = 1;
					$data->discount_used_times = 0;

					if(!empty($this->plugin_params->validity_period) && !empty($this->plugin_params->validity_value) && (int)$this->plugin_params->validity_value > 0) {
						$date_d = date("d");
						$date_m = date("m");
						$date_y = date("Y");
						$v = (int)$this->plugin_params->validity_value;
						switch($this->plugin_params->validity_period) {
							case 'year':
								$data->discount_end = mktime(0,0,0, $date_m, $date_d, $date_y + $v);
								break;
							case 'month':
								$data->discount_end = mktime(0,0,0, $date_m + $v, $date_d, $date_y);
								break;
							case 'day':
								$data->discount_end = mktime(0,0,0, $date_m, $date_d + $v, $date_y);
								break;
						}
						if(!isset($serialObj->extradata))
							$serialObj->extradata = array();
						$serialObj->extradata['discount_end'] = $data->discount_end;
					}

					if(!empty($this->plugin_params->discount_percent)) {
						$v = hikaserial::toFloat(trim($this->plugin_params->discount_percent));
						$product_price = 0;
						if(!empty($order->cart->products)) {
							foreach($order->cart->products as $p) {
								if($p->product_id == $pack->product_id || (isset($pack->order_product_id) && $p->order_product_id == $pack->order_product_id)) {
									$product_price = hikaserial::toFloat($p->order_product_price);
									if(!empty($this->plugin_params->discount_percent_tax))
										$product_price += hikaserial::toFloat($p->order_product_tax);
									break;
								}
							}
						}
						if(!empty($product_price)) {
							if(!empty($order->order_currency_id))
								$data->discount_currency_id = (int)$order->order_currency_id;
							else
								$data->discount_currency_id = (int)$order->old->order_currency_id;
							$data->discount_flat_amount = $product_price * $v / 100;
							$data->discount_percent_amount = 0.0;
						}
					}

					if(!$discountClass->save($data)){
						$result = false;
					}
				}
			}
			if(!$result) {
				$app = JFactory::getApplication();
				$app->enqueueMessage(JText::_('ERR_CREATING_DISCOUNT_COUPON'));
			}

			if(!empty($serialObj) && !empty($serialObj->extradata)) {
				$serialObj->data = $serial;
				$serials[] = $serialObj;
			} else {
				$serials[] = $serial;
			}
		}
	}

	public function onAfterSerialUnassigned(&$serials) {
		if(empty($serials))
			return;

		$db = JFactory::getDBO();
		$unpublished_coupon = array();
		foreach($serials as $serial) {
			if(strpos($serial->pack_generator, '-') === false)
				continue;
			list($generator, $generator_id) = explode('-', $serial->pack_generator, 2);
			if($generator != 'plg.coupongen')
				continue;

			$unpublished_coupon[] = $db->Quote($serial->serial_data);
		}

		if(empty($unpublished_coupon))
			return;

		$query = 'UPDATE ' . hikaserial::table('shop.discount') .
				' SET discount_published = 0 '.
				' WHERE discount_type = ' . $db->Quote('coupon') . ' AND discount_code IN (' . implode(',', $unpublished_coupon) . ')';
		$db->setQuery($query);
		$db->query();
	}

	public function configurationHead() {
		return array(
			1 => 'HIKASHOP_COUPON',
			2 => 'DISCOUNT_PRODUCT_PERCENT',
			3 => 'SERIAL_FORMAT',
			4 => 'SERIAL_SIZE'
		);
	}

	public function configurationLine($id = 0, $conf = null) {
		if(empty($this->discountClass))
			$this->discountClass = hikaserial::get('shop.class.discount');
		switch($id) {
			case 1:
				$d = null;
				if(!empty($conf->generator_params->discount_id))
					$d = $this->discountClass->get($conf->generator_params->discount_id);
				if(empty($d))
					return '<img src="'.HIKASERIAL_IMAGES.'icon-16/unpublish.png" alt="'.JText::_('ERROR').'"/>';
				return '<a href="'.hikaserial::completeLink('shop.discount&task=edit&cid='.$conf->generator_params->discount_id).'">'.$d->discount_code.'</a>';
			case 2:
				if(!empty($conf->generator_params->discount_percent))
					return $conf->generator_params->discount_percent.'%';
				return '';
			case 3:
				if(empty($conf->generator_params->format))
					return $this->serial_default_format;
				return $conf->generator_params->format;
			case 4:
				if(empty($conf->generator_params->size))
					return 12;
				return $conf->generator_params->size;
		}
		return null;
	}
}
