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
class plgHikaserialSecureebook extends hikaserialPlugin {

	protected $type = 'generator';
	protected $multiple = false;
	protected $populate = false;
	protected $name = 'secureebook';
	protected $doc_form = 'secureebook-';
	protected $fullname = 'Secure E-Book';

	protected $pluginConfig = array(
		'vendor_code' => array('VENDOR_CODE', 'input'),
		'sdk_key' => array('SDK_KEY', 'input'),
		'test_mode' => array('TEST_MODE', 'boolean'),
		'remote_product_code' => array('USE_SECURE_EBOOK_PRODUCT_CODE', 'boolean')
	);

	private $sdk_url = 'https://www.secure-ebook.com/sdk.jsp';

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	public function generate(&$pack, &$order, $quantity, &$serials) {
		parent::pluginParams(0, 'secureebook');

		$product_code = '';
		if(!empty($order->cart->products)) {
			foreach($order->cart->products as $p) {
				if($p->product_id == $pack->product_id) {
					$product_code = $p->order_product_code;
					break;
				}
			}
		}
		if(empty($product_code))
			return;
		$data = '<'.'?xml version="1.0"?'.'>'."\r\n".
			'<request vendorcode="'.@$this->plugin_params->vendor_code.'" secureebookproductcode="'.$this->tobool(@$this->plugin_params->remote_product_code).'" userdata="'.$order->order_id.'" testmode="'.$this->tobool(@$this->plugin_params->test_mode).'">'."\r\n".
			'<product code="'.$product_code.'" qty="'.$quantity.'" />'."\r\n".
			'<orderinfo name="'.@$order->customer->name.'" email="'.@$order->customer->user_email.'" total="'.@$order->order_full_price.'" />'."\r\n".
			'</request>';

		$result = $this->send('xml='.urlencode($data));
		$token = $this->extractElems('token', $result, true);
		if(isset($token[0]['token'])) {
			$token = $token[0]['token'];
		} else {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::sprintf('SECURE_EBOOK_ERROR', 'no token. Invalid request for product ['.$product_code.']'));
			return false;
		}

		$data = '<'.'?xml version="1.0"?'.'>'."\r\n".
			'<confirm code="'.strtolower(md5($token . $this->plugin_params->sdk_key)).'"/>';
		$result = $this->send('xml='.urlencode($data));

		if(strpos($result, '<success') !== false) {
			$keys = $this->extractElems('key', $result, true);
			$links = $this->extractElems('link', $result, true);

			$books = array();
			foreach($keys as $key) {
				$code = $key['productcode'];
				if(!isset($books[$code])) {
					$books[$code] = array();
				}
				$books[$code][] = array('key' => $key['key']);
			}
			foreach($links as $link) {
				$code = $link['productcode'];
				if(isset($books[$code])) {
					foreach($books[$code] as $i => $v) {
						$books[$code][$i]['link'] = $link['url'];
					}
				}
			}
			foreach($books as $book) {
				foreach($book as $b) {
					$serials[] = serialize($b);
				}
			}
		} else {
			$error = $this->extractContent('error', $result);
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::sprintf('SECURE_EBOOK_ERROR', $error[0]));
			return false;
		}
	}

	protected function toxml($data) {
		return str_replace (
			array('&', '"', "'", '<', '>'),
			array('&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;'),
			$data
		);
	}

	protected function tobool($data) {
		if($data == 1)
			return 'true';
		return 'false';
	}

	protected function send($data) {
		$session = curl_init();

		curl_setopt($session, CURLOPT_URL, $this->sdk_url);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($session, CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt($session, CURLOPT_POST, 1);

		curl_setopt($session, CURLOPT_POSTFIELDS, $data);

		$ret = curl_exec($session);
		$error = curl_errno($session);
		$err_msg = curl_error($session);
		$info = curl_getinfo($session);

		curl_close($session);

		return $ret;
	}

	public function extractElems($name, $data, $params = false) {
		$ret = array();
		if(preg_match_all('#<'.$name.' (.*)/>#iU', $data, $matches)) {
			foreach($matches[1] as $match) {
				if($params) {
					$ret[] = $this->extractParams($match);
				} else {
					$ret[] = $match;
				}
			}
		}
		return $ret;
	}

	protected function extractParams($data) {
		$params = explode(' ', $data);
		$ret = array();
		foreach($params as $param) {
			if(empty($param)) continue;
			if(strpos($param, '=') !== false) {
				list($n, $d) = explode('=', $param, 2);
				$ret[$n] = trim($d, '"');
			} else {
				$ret[$param] = false;
			}
		}
		return $ret;
	}

	protected function extractContent($name, $data) {
		$ret = array();
		if(preg_match_all('#<'.$name.'.*>(.*)</'.$name.'>#msiU', $data, $matches)) {
			foreach($matches[1] as $match) {
				$ret[] = trim($match);
			}
		}
		return $ret;
	}

	public function configurationHead() {
		return array();
	}

	public function configurationLine($id = 0, $conf = null) {
		return null;
	}

	public function onDisplaySerials(&$data, $viewName) {
		if($viewName == 'back-serial-form')
			return;

		$crlf = '<br/>';
		foreach($data as &$serial) {
			$d = $serial->serial_data;
			if($serial->pack_generator != 'plg.secureebook')
				continue;
			if(!empty($d)) {
				$d = hikaserial::unserialize($d);
				$serial->serial_data = JText::_('SECURE_EBOOK_KEY') . ' ' . $d['key'];
				if(!empty($d['link'])) {
					$serial->serial_data .= $crlf . JText::_('SECURE_EBOOK_DOWNLOAD') . ' ' . $d['link'];
				}
			}
			unset($d);
		}
		unset($serial);
		return;
	}
}
