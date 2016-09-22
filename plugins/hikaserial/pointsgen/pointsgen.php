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
class plgHikaserialPointsgen extends hikaserialPlugin {

	protected $type = 'generator';
	protected $multiple = true;
	protected $populate = true;
	protected $name = 'pointsgen';
	protected $doc_form = 'pointsgen-';
	public $test = false;

	protected $serial_default_format = '[a-zA-Z0-9]{size}';
	protected $pluginConfig = array(
		'value' => array('POINTS_VALUE', 'input'),
		'currency_rate' => array('POINTS_CURRENCY_RATE', 'input', '', ' ¤ = 1 point'),
		'size' => array('SERIAL_SIZE', 'input'),
		'format' => array('SERIAL_FORMAT', 'input', '[a-zA-Z0-9]{size}'),
		'test' => array('SERIAL_TEST', 'serial_test')
	);

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	public function onPluginConfiguration(&$elements) {
		$ret = parent::onPluginConfiguration($elements);

		$shopConfig = hikaserial::config(false);
		$main_currency = $shopConfig->get('main_currency', 1);
		$currencyClass = hikaserial::get('shop.class.currency');
		$currency = $currencyClass->get($main_currency);

		$this->pluginConfig['currency_rate'][3] = JText::sprintf('POINTS_CONVERSION', $currency->currency_code . ' ' . $currency->currency_symbol);

		return $ret;
	}

	public function generate(&$pack, &$order, $quantity, &$serials) {
		if(!isset($pack->pointsgen))
			return;

		parent::pluginParams($pack->pointsgen);

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

			if(!$this->test) {
				if(!isset($serialObj->extradata))
					$serialObj->extradata = array();

				$serialObj->extradata['points_value'] = (int)$this->plugin_params->value;

				if(!empty($this->plugin_params->currency_rate)) {
					$v = hikaserial::toFloat(trim($this->plugin_params->currency_rate));
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
						$shopConfig = hikaserial::config(false);
						$main_currency = $shopConfig->get('main_currency', 1);
						$currencyClass = hikaserial::get('shop.class.currency');
						$currency = $currencyClass->get($main_currency);

						if(isset($order->order_currency_id))
							$order_currency_id = $order->order_currency_id;
						else
							$order_currency_id = hikashop_getCurrency();

						if($main_currency != $order_currency_id)
							$product_price = $currencyClass->convertUniquePrice($product_price, $main_currency, $order_currency_id);

						$serialObj->extradata['points_value'] += (int)($product_price / $v);
					}
				}

				if(!empty($order->hikaserial->formData['points_value'])) {
					$serialObj->extradata['points_value'] += (int)$order->hikaserial->formData['points_value'];
				}
			}

			if(!empty($serialObj) && !empty($serialObj->extradata)) {
				$serialObj->data = $serial;
				$serials[] = $serialObj;
			} else {
				$serials[] = $serial;
			}
		}
	}


	public function configurationHead() {
		return array();
	}

	public function configurationLine($id = 0, $conf = null) {
		return null;
	}

	public function populateForm(&$pack) {
		$ret = '';

		if(isset($pack->pointsgen))
			parent::pluginParams($pack->pointsgen);

		if(isset($this->plugin_params)) {
			$ret .= '<tr>
				<td class="key"><label>'.JText::_('POINTS_VALUE').'</label></td>
				<td><strong>'.$this->plugin_params->value.'</strong></td>
			</tr>';

			if(!empty($this->plugin_params->currency_rate)) {
				$shopConfig = hikaserial::config(false);
				$main_currency = $shopConfig->get('main_currency', 1);
				$currencyClass = hikaserial::get('shop.class.currency');
				$currency = $currencyClass->get($main_currency);

				$ret .= '<tr>
					<td class="key"><label>'.JText::_('POINTS_CURRENCY_RATE').'</label></td>
					<td><strong>'.$this->plugin_params->currency_rate.' '.JText::sprintf('POINTS_CONVERSION', $currency->currency_code . ' ' . $currency->currency_symbol).'</strong></td>
				</tr>';
			}

			$ret .= '<tr>
				<td class="key"><label>'.JText::_('ADD_POINTS').'</label></td>
				<td><input type="text" name="data[points_value]" value="0"/></td>
			</tr>';
		} else {
			$ret .= '<tr>
				<td class="key"><label>'.JText::_('POINTS_VALUE').'</label></td>
				<td><input type="text" name="data[points_value]" value=""/></td>
			</tr>';
		}

		return $ret;
	}

	private function getAUP($warning = false) {
		static $aup = null;
		if(!isset($aup)) {
			$aup = false;
			$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
			if(file_exists($api_AUP)) {
				require_once ($api_AUP);
				if(class_exists('AlphaUserPointsHelper'))
					$aup = true;
			}
			if(!$aup && $warning) {
				$app = JFactory::getApplication();
				if($app->isAdmin())
					$app->enqueueMessage('The HikaShop UserPoints plugin requires the component AlphaUserPoints to be installed. If you want to use it, please install the component or use another mode.');
			}
		}
		return $aup;
	}
}
