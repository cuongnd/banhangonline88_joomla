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
class plgHikaserialTimelimitedgen extends hikaserialPlugin {

	protected $type = 'generator';
	protected $multiple = true;
	protected $populate = true;
	protected $name = 'timelimitedgen';
	protected $doc_form = 'timelimitedgen-';

	protected $serial_default_format = '[a-zA-Z0-9]{size}';

	protected $pluginConfig = array(
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
		if(!isset($pack->timelimitedgen))
			return;

		parent::pluginParams($pack->timelimitedgen);

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

			if(!empty($this->plugin_params->validity_period) && !empty($this->plugin_params->validity_value) && (int)$this->plugin_params->validity_value > 0) {
				$date_d = date("d");
				$date_m = date("m");
				$date_y = date("Y");
				$v = (int)$this->plugin_params->validity_value;
				$discount_end = -1;
				switch($this->plugin_params->validity_period) {
					case 'year':
						$discount_end = mktime(0,0,0, $date_m, $date_d, $date_y + $v);
						break;
					case 'month':
						$discount_end = mktime(0,0,0, $date_m + $v, $date_d, $date_y);
						break;
					case 'day':
						$discount_end = mktime(0,0,0, $date_m, $date_d + $v, $date_y);
						break;
				}
				if($discount_end > 0) {
					if(!isset($serialObj->extradata))
						$serialObj->extradata = array();
					$serialObj->extradata['validity_end'] = $discount_end;
				}
			}

			if(empty($this->test) && !empty($serialObj) && !empty($serialObj->extradata)) {
				$serialObj->data = $serial;
				$serials[] = $serialObj;
			} else {
				$serials[] = $serial;
			}
		}
		return true;
	}

	public function onBeforeSerialConsume(&$serial, $user_id, &$do, &$extra_data) {
		if(substr($serial->pack->pack_generator, 0, 18) != 'plg.timelimitedgen-')
			return;

		if(empty($serial->serial_extradata['validity_end']))
			return;

		$date = (int)$serial->serial_extradata['validity_end'];
		if($date < time())
			$do = false;
	}

	public function configurationHead() {
		return array(
			1 => 'format',
			2 => 'size',
			2 => 'validity'
		);
	}

	public function configurationLine($id = 0, $conf = null) {
		switch($id) {
			case 1:
				if(empty($conf->generator_params->format))
					return $this->serial_default_format;
				return $conf->generator_params->format;
			case 2:
				if(empty($conf->generator_params->size))
					return 12;
				return $conf->generator_params->size;
			case 3:
				$v = (int)$conf->generator_params->validity_value;
				if(empty($v))
					return JText::_('UNLIMITED');
				if($v == 1)
					return JText::sprintf('HIKASERIAL_PERIOD_' . strtoupper($this->generator_params->validity_period), $v);
				return JText::sprintf('HIKASERIAL_PERIOD_' . strtoupper($this->generator_params->validity_period) . 'S', $v);
		}
		return null;
	}
}
