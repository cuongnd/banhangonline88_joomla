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
class plgHikaserialEangen extends hikaserialPlugin {

	protected $type = 'generator';
	protected $multiple = true;
	protected $populate = true;
	protected $name = 'eangen';
	protected $doc_form = 'eangen-';

	protected $serial_default_eantype = 'ean13';

	protected $pluginConfig = array(
		'eantype' => array('EAN_TYPE', 'list', array(
			'ean13' => 'ean13',
			'ean8' => 'ean8'
		)),
		'test' => array('SERIAL_TEST', 'serial_test')
	);


	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	public function generate(&$pack, &$order, $quantity, &$serials) {
		if(!isset($pack->eangen))
			return;

		parent::pluginParams($pack->eangen);

		$config = hikaserial::config();
		$fastRandom = (int)$config->get('use_fast_random', 0);

		for($q = 0; $q < $quantity; $q++) {
			$serial = '';

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

			$repeat = 12;
			if($this->plugin_params->eantype == 'ean8')
				$repeat = 7;
			$list = '0123456789';

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

			$sequence_ean8  = array(3, 1);
			$sequence_ean13 = array(1, 3);

			$sums = 0;
			foreach(str_split($serial) as $n => $digit) {
				if($repeat == 7) {
					$sums += (int)$digit * $sequence_ean8[$n % 2];
				} else {
					$sums += (int)$digit * $sequence_ean13[$n % 2];
				}
			}

			$checksum = 10 - $sums % 10;
			if($checksum == 10) {
				$checksum = 0;
			}

			$serials[] = $serial . $checksum;
		}
		return true;
	}


	public function configurationHead() {
		return array(
			1 => 'type'
		);
	}

	public function configurationLine($id = 0, $conf = null) {
		switch($id) {
			case 1:
				if(empty($conf->generator_params->eantype))
					return $this->serial_default_eantype;
				return $conf->generator_params->eantype;
		}
		return null;
	}
}
