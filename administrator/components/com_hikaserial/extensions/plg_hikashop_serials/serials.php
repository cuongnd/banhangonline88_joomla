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
class plgHikashopSerials extends JPlugin {
	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	private function init() {
		static $init = null;
		if($init !== null)
			return $init;

		$init = defined('HIKASERIAL_COMPONENT');
		if(!$init) {
			$filename = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaserial'.DS.'helpers'.DS.'helper.php';
			if(file_exists($filename)) {
				include_once($filename);
				$init = defined('HIKASERIAL_COMPONENT');
			}
		}
		return $init;
	}

	public function onProductFormDisplay(&$product, &$html) {
		if(!$this->init())
			return;

		$params = new HikaParameter('');
		if(!empty($product->product_id)) {
			$params->set('product_id', $product->product_id);
		} else {
			$params->set('product_id', 0);
		}
		$js = '';
		$ret = hikaserial::getLayout('productserial', 'shop_form', $params, $js);
		if(!empty($ret)) {
			$html[] = $ret;
		}
	}

	public function onProductBlocksDisplay(&$product, &$html) {
		if(!$this->init())
			return;

		$params = new HikaParameter('');
		if(!empty($product->product_id)) {
			$params->set('product_id', $product->product_id);
		} else {
			$params->set('product_id', 0);
		}
		$js = '';
		$ret = hikaserial::getLayout('productserial', 'shop_block', $params, $js);
		if(!empty($ret)) {
			$html[] = $ret;
		}
	}

	public function onMarketProductBlocksDisplay(&$product, &$html) {
		if(!defined('HIKAMARKET_COMPONENT'))
			return;

		if(!$this->init())
			return;

		$marketConfig = hikamarket::config();
		if(!$marketConfig->get('frontend_edition',0)) return;
		if(!hikamarket::acl('product_edit_plugin_hikaserial')) return;

		$params = new HikaParameter('');
		if(!empty($product->product_id)) {
			$params->set('product_id', $product->product_id);
		} else {
			$params->set('product_id', 0);
		}
		$js = '';
		$ret = hikaserial::getLayout('productserial', 'market_block', $params, $js);
		if(!empty($ret)) {
			$html[] = $ret;
		}
	}

	public function onMarketAclPluginListing(&$categories) {
		$categories['product'][] = 'hikaserial';

		if(empty($categories['root']['plugins']))
			$categories['root']['plugins'] = array();
		$categories['root']['plugins']['hikaserial'] = array(
			'pack' => array(
				'listing',
				'add',
				'edit' => array(
					'name',
					'data',
					'generator',
					'published',
					'description',
					'vendor'
				)
			),
			'serial' => array(
				'listing',
				'export',
				'import'
			)
		);
	}

	public function onVendorPanelDisplay(&$buttons, &$statistics) {
		if(!$this->init())
			return;

		$marketConfig = hikamarket::config();
		$marketVersion = $marketConfig->get('version', '1.0.0');
		if(version_compare($marketVersion, '1.6.6', '>=')) {
			$buttons['hikaserial'] = array(
				'url' => hikamarket::completeLink('serials'),
				'level' => 1,
				'icon' => 'iconM-48-serial',
				'name' => JText::_('HIKA_SERIALS'),
				'description' => '',
				'display' => hikamarket::acl('plugins/hikaserial')
			);
		}

		if(hikamarket::acl('vendor/statistics')) {
			$vendor_id = hikamarket::loadVendor(false);
			$db = JFactory::getDBO();
			$doc = JFactory::getDocument();
			$doc->addStyleSheet(HIKASERIAL_CSS.'frontend_default.css');

			$statistics['serial_count'] = array(
				'slot' => 1,
				'order' => 3,
				'published' => 0,
				'type' => 'plugin',
				'plugin' => 'hikaserial',
				'label' => JText::_('HIKA_SERIALS'),
				'query' => array(
					'get' => 'list',
					'select' => 'pack.pack_name, pack.pack_id, serial.serial_status, COUNT(serial.serial_id) as count',
					'tables' => array(
						'serial' => hikaserial::table('serial').' AS serial ',
						'pack' => 'INNER JOIN '.hikaserial::table('pack').' AS pack ON serial.serial_pack_id = pack.pack_id ',
						'hk_order' => 'INNER JOIN '.hikaserial::table('shop.order').' AS hk_order ON serial.serial_order_id = hk_order.order_parent_id',
					),
					'filters' => array(
						'order_vendor' => ('hk_order.order_vendor_id = ' . (int)$vendor_id),
						'order_type' => 'hk_order.order_type = ' . $db->Quote('subsale'),
					),
					'group' => 'pack.pack_name, serial.serial_status',
					'order' => 'pack.pack_id, serial.serial_status'
				)
			);

			if($vendor_id <= 1) {
				$statistics['serial_count']['query']['tables']['hk_order'] = 'INNER JOIN '.hikaserial::table('shop.order').' AS hk_order ON serial.serial_order_id = hk_order.order_id';
				$statistics['serial_count']['query']['filters']['order_type'] = 'hk_order.order_type = ' . $db->Quote('sale');
				unset($statistics['serial_count']['query']['filters']['order_vendor']);
			}
		}
	}

	public function onHikamarketPluginController($ctrl) {
		if($ctrl != 'serials')
			return;

		$app = JFactory::getApplication();
		if($app->isAdmin() || !$this->init())
			return;

		return array(
			'type' => '',
			'component' => 'com_hikaserial',
			'name' => 'serials',
			'file' => 'market'
		);
	}

	public function onHikamarketStatisticPluginDisplay($data) {
		if(!isset($data['type']) || $data['type'] != 'plugin')
			return;
		if(!isset($data['plugin']) || $data['plugin'] != 'hikaserial')
			return;

		if(empty($data['value']))
			return JText::_('HIKAM_EMPTY_CHART');

		$ret = '<table style="width:100%" class="hikamarket_stat_table"><thead><tr><th>'.JText::_('PACK_NAME').'</th><th>'.JText::_('SERIAL_STATUS').'</th><th>'.JText::_('TOTAL_SERIALS').'</th></tr></thead><tbody>';
		foreach($data['value'] as $v) {
			$ret .= '<tr><td>'.htmlentities($v->pack_name).'</td><td>'.htmlentities($v->serial_status).'</td><td>'. (int)$v->count.'</td></tr>';
		}
		$ret .= '</tbody></table>';
		return $ret;
	}

	public function onUserAccountDisplay(&$buttons) {
		if(!$this->init())
			return;

		$serialConfig = hikaserial::config();
		if((int)$serialConfig->get('front_serial_listing', 1) == 0)
			return;

		$doc = JFactory::getDocument();
		$doc->addStyleSheet(HIKASERIAL_CSS.'frontend_default.css');

		$cancel_id = '';
		global $Itemid;
		if(!empty($Itemid))
			$cancel_id = '&cancel_id='.(int)$Itemid;

		$buttons['serial_listing'] = array(
			'link' => hikaserial::completeLink('serial&task=listing'.$cancel_id),
			'image' => 'serial',
			'level' => 0,
			'text' => JText::_('HIKA_SERIALS'),
			'description' => JText::_('HIKA_SERIAL_LISTING_DESCRIPTION'),
		);
		return true;
	}

	public function onAfterProductCreate(&$product) {
		if(!$this->init())
			return;

		$app = JFactory::getApplication();
		if(!$app->isAdmin() & !hikaserial::initMarket())
			return;

		$class = hikaserial::get('class.product');
		$class->saveForm($product);
	}

	public function onAfterProductUpdate(&$product) {
		if(!$this->init())
			return;

		$app = JFactory::getApplication();
		if(!$app->isAdmin() & !hikaserial::initMarket())
			return;

		$class = hikaserial::get('class.product');
		$class->saveForm($product);
	}

	public function onBeforeOrderCreate(&$order, &$do) {
		if(!$this->init())
			return;

		$class = hikaserial::get('class.order');
		$class->preUpdate($order);
	}

	public function onAfterOrderCreate(&$order, &$send_email) {
		if(!$this->init())
			return;

		$class = hikaserial::get('class.order');
		$class->postUpdate($order);
	}

	public function onBeforeOrderUpdate(&$order, &$do) {
		if(!$this->init())
			return;

		$class = hikaserial::get('class.order');
		$class->preUpdate($order);
	}

	public function onAfterOrderUpdate(&$order, &$send_email) {
		if(!$this->init())
			return;

		$class = hikaserial::get('class.order');
		$class->postUpdate($order);
	}

	public function onAfterOrderProductsListingDisplay(&$order, $type) {
		if(!$this->init())
			return;

		$types = array(
			'order_back_show', 'order_back_invoice', 'order_front_show', 'email_notification_html',
			'order_frontvendor_show', 'order_frontvendor_invoice', 'order_frontmarket_show'
		);

		if(in_array($type, $types)) {
			$params = new HikaParameter('');
			if(isset($order->order_id)) {
				$params->set('order_id', (int)$order->order_id);
			} else {
				$params->set('order_id', (int)$order->products[0]->order_id);
			}
			$params->set('order_obj', $order);
			$js = '';
			echo hikaserial::getLayout('orderserial', 'show_'.$type, $params, $js);
		}
	}

	public function onMarketOrderEditionLoading(&$order) {
		if(!$this->init())
			return;

		$block = JRequest::getString('block', null);
		if(!empty($block) && !in_array($block, array('product', 'edit_product')))
			return;

		$marketHelper = hikaserial::get('helper.market');
		$marketHelper->processOrderEditionLoading($order, $block);
	}

	public function onBeforeMailSend(&$mail, &$mailer) {
		if(!$this->init())
			return;

		$mailClass = hikaserial::get('class.mail');
		$mailClass->beforeMailSend($mail, $mailer);
	}

	public function onBeforeDownloadFile(&$filename, &$do, &$file) {
		if(!$this->init())
			return;
		$downloadClass = hikaserial::get('class.download');
		if(!empty($downloadClass))
			$downloadClass->beforeDownloadFile($filename, $do, $file);
	}

	public function onBeforeOrderExport(&$rows, &$view) {
		if(!$this->init())
			return;
		$orderClass = hikaserial::get('class.order');
		$orderClass->beforeOrderExport($rows, $view);
	}

	public function onViewsListingFilter(&$views, $client) {
		if(!$this->init())
			return;

		switch($client){
			case 0:
				$views[] = array(
					'client_id' => 0,
					'name' => HIKASERIAL_NAME,
					'component' => HIKASERIAL_COMPONENT,
					'view' => HIKASERIAL_FRONT.'views'.DS
				);
				break;
			case 1:
				$views[] = array(
					'client_id' => 1,
					'name' => HIKASERIAL_NAME,
					'component' => HIKASERIAL_COMPONENT,
					'view' => HIKASERIAL_BACK.'views'.DS
				);
				break;
			default:
				$views[] = array(
					'client_id' => 0,
					'name' => HIKASERIAL_NAME,
					'component' => HIKASERIAL_COMPONENT,
					'view' => HIKASERIAL_FRONT.'views'.DS
				);
				$views[] = array(
					'client_id' => 1,
					'name' => HIKASERIAL_NAME,
					'component' => HIKASERIAL_COMPONENT,
					'view' => HIKASERIAL_BACK.'views'.DS
				);
				break;
		}
	}

	public function onHikashopBeforeDisplayView(&$viewObj) {
		$app = JFactory::getApplication();

		$viewName = $viewObj->getName();

		if($viewName == 'menu') {
			if(!$app->isAdmin() || !$this->init())
				return;
			$class = hikaserial::get('class.menu');
			$class->processView($viewObj);
			return;
		}
	}

	public function onMailListing(&$files) {
		if(!$this->init())
			return;

		jimport('joomla.filesystem.folder');
		$emailFiles = JFolder::files(HIKASERIAL_MEDIA.'mail'.DS, '^([-_A-Za-z]*)(\.html)?\.php$');
		if(empty($emailFiles))
			return;
		foreach($emailFiles as $emailFile) {
			$file = str_replace(array('.html.php', '.php'), '', $emailFile);
			if(substr($file, -9) == '.modified')
				continue;
			$key = strtoupper($file);
			$files[] = array(
				'folder' => HIKASERIAL_MEDIA.'mail'.DS,
				'name' => JText::_('SERIAL_' . $key),
				'filename' => $file,
				'file' => 'serial.'.$file
			);
		}
	}

	public function onCheckoutStepList(&$list) {
		$list['plg.serial.coupon'] = 'HikaSerial ' . JText::_('HIKASHOP_COUPON');
	}

	public function onCheckoutStepDisplay($layoutName, &$html, &$view) {
		if($layoutName == 'plg.serial.coupon') {
			if(!$this->init())
				return;
			$params = new stdClass();
			$params->view = $view;
			$js = null;
			$html .= hikaserial::getLayout('checkoutserial', 'coupon', $params, $js);
		}
	}

	public function onBeforeCheckoutStep($controllerName, &$go_back, $original_go_back, &$controller) {
	}

	public function onAfterCheckoutStep($controllerName, &$go_back, $original_go_back, &$controller) {
		if($controllerName == 'plg.serial.coupon') {
			if(!$this->init())
				return;

			$checkoutClass = hikaserial::get('class.checkout');
			$checkoutClass->afterCheckoutStep($controllerName, $go_back, $original_go_back, $controller);
		}
	}

	public function onNameboxTypesLoad(&$types) {
		if(!$this->init())
			return;
		$types['plg.hikaserial.pack'] = array(
			'class' => array(
				'file' => HIKASERIAL_CLASS . 'pack.php',
				'name' => 'hikaserialPackClass'
			),
			'name' => 'pack_id',
			'mode' => 'list',
			'displayFormat' => '{pack_name}',
			'params' => array(),
			'url' => 'index.php?option='.HIKASERIAL_COMPONENT.'&ctrl=pack&task=findList',
			'url_params' => array(),
			'options' => array(
				'olist' => array(
					'table' => array(
						'pack_name' => 'HIKA_NAME',
						'pack_data' => 'PACK_DATA',
						'pack_generator' => 'PACK_GENERATOR',
						'pack_id' => 'ID'
					),
					'displayFormat' => '{pack_name}',
				)
			)
		);
	}

	public function onHikashopBeforeCheckDB(&$createTable, &$custom_fields, &$structure, &$helper) {
		if(!$this->init())
			return;
		$helper->parseTableFile(HIKASERIAL_BACK.'_database'.DS.'install.sql', $createTable, $structure);
	}
}
