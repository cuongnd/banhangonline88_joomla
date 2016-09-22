<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class vendorMarketController extends hikamarketController {

	protected $type = 'vendor';
	protected $toggle = array('vendor_published' => 'vendor_id');
	protected $rights = array(
		'display' => array('display', 'show', 'cancel', 'listing', 'admin', 'products', 'invoices', 'pay', 'paymanual', 'geninvoice', 'dogeninvoice', 'selection', 'useselection', 'getprice', 'searchfields', 'getvalues', 'reports'),
		'add' => array('add'),
		'edit' => array('edit','toggle','publish','unpublish'),
		'modify' => array('save','apply','dopay'),
		'delete' => array('remove')
	);

	public function __construct($config = array())	{
		parent::__construct($config);
		$this->registerDefaultTask('listing');
	}

	public function store() {
		return parent::adminStore();
	}

	public function remove() {
		$confirm = JRequest::getVar('confirm', '');

		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		if(!empty($confirm)) {
			sort($cid);
			$check = md5(implode(';', $cid));
			if($confirm != $check || in_array(1, $cid)) {
				$confirm = null;
				$app = JFactory::getApplication();
				$app->enqueueMessage(JText::_('INCORRECT_DATA'));
			}
		}

		if(empty($confirm)) {
			JRequest::setVar('layout', 'delete');
			return parent::display();
		}
		return parent::adminRemove();
	}

	public function cancel(){
		$this->setRedirect( hikamarket::completeLink('vendor',false,true) );
	}

	public function admin() {
		JRequest::setVar('layout', 'admin');
		return parent::display();
	}

	public function products() {
		JRequest::setVar('layout', 'products');
		return parent::display();
	}

	public function pay() {
		$config = hikamarket::config();
		$vendor_id = hikamarket::getCID('vendor_id');
		$vendor_ids = JRequest::getVar('cid', array(), '', 'array');

		if(!empty($vendor_ids) && count($vendor_ids) > 1) {
			$vendor_id = $vendor_ids;
			JArrayHelper::toInteger($vendor_id);
		}

		if(!empty($vendor_id))
			JRequest::setVar('layout', 'pay');

		if(is_array($vendor_id) && JRequest::getInt('report', 0) != 0) {
			JRequest::setVar('layout', 'payreport');
			return parent::display();
		}

		$orders = JRequest::getVar('orders', array(), '', 'array');
		if(!empty($orders) && !is_array($vendor_id)) {
			JRequest::checkToken() || die('Invalid Token');

			$vendorClass = hikamarket::get('class.vendor');
			$status = $vendorClass->pay($vendor_id, $orders);

			if($status) {
				$app = JFactory::getApplication();
				$app->redirect(hikamarket::completeLink('shop.order&task=edit&cid[]='.$status, false, true));
			}
		}
		return parent::display();
		return false;
	}

	public function dopay() {
		JRequest::checkToken() || die('Invalid Token');

		$app = JFactory::getApplication();
		$vendor_id = hikamarket::getCID();
		$vendor_ids = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($vendor_ids);

		JRequest::setVar('layout', 'listing');
		if(empty($vendor_id))
			return parent::display();

		if(count($vendor_ids) > 1) {
			$filter_start = JRequest::getVar('filter_start', null);
			$filter_end = JRequest::getVar('filter_end', null);
			$session_filter_start = $app->getUserState(HIKAMARKET_COMPONENT.'.vendormarket.pay.filter_start', null);
			$session_filter_end = $app->getUserState(HIKAMARKET_COMPONENT.'.vendormarket.pay.filter_end', null);

			if($filter_start != $session_filter_start || $filter_end != $session_filter_end) {
				$app->enqueueMessage(JText::_('HIKAM_THE_FILTER_HAS_CHANGED'));
				JRequest::setVar('layout', 'pay');
				return parent::display();
			}

			$filters = array(
				'start' => $filter_start,
				'end' => $filter_end
			);

			$vendorClass = hikamarket::get('class.vendor');
			$status = $vendorClass->pay($vendor_ids, null, $filters);

			if(!empty($status)) {
				$app = JFactory::getApplication();
				if(!is_array($status)) {
					$app->redirect(hikamarket::completeLink('shop.order&task=edit&cid[]=' . (int)$status, false, true));
				}

				$vendor_errors = array();
				foreach($status as $k => $v) {
					if($v !== false)
						continue;
					unset($status[$k]);
					$vendor_errors[] = (int)$k;
				}
				if(!empty($vendor_errors)) {
					$query = 'SELECT vendor_name FROM ' . hikamarket::table('vendor') . ' WHERE vendor_id IN ('.implode(',', $vendor_errors).')';
					$db = JFactory::getDBO();
					if(!HIKASHOP_J25) $vendors = $db->loadResultArray();
					else $vendors = $db->loadColumn();
					$app->enqueueMessage(JText::sprint('CANNOT_PAY_VENDORS', implode(', ', $vendors)), 'error');
				}

				$app->redirect(hikamarket::completeLink('vendor&task=pay&report=1&cid[]=' . implode('&cid[]=', $status), false, true));
			}

			JRequest::setVar('layout', 'pay');
			$app->enqueueMessage(JText::_('INVALID_DATA'), 'error');
		} else {
			$orders = JRequest::getVar('orders', array(), '', 'array');
			if(!empty($orders)) {
				JRequest::checkToken() || die('Invalid Token');

				$vendorClass = hikamarket::get('class.vendor');
				$status = $vendorClass->pay($vendor_id, $orders);

				if($status) {
					$app = JFactory::getApplication();
					$app->redirect(hikamarket::completeLink('shop.order&task=edit&cid[]='.$status, false, true));
				}
			}
		}
		return parent::display();
		return false;
	}

	public function geninvoice() {
		return self::pay();
	}

	public function dogeninvoice() {
		return self::dopay();
	}

	public function paymanual() {
		$vendor_id = hikamarket::getCID('vendor_id');
		$order_id = JRequest::getInt('order_id', 0);
		$payment_method = JRequest::getString('payment_method', 'manual');

		if(empty($order_id) || empty($vendor_id)) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('INVALID_DATA'), 'error');
			return false;
		}

		$vendorClass = hikamarket::get('class.vendor');
		$vendor = $vendorClass->get($vendor_id);
		if(empty($vendor)) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('INVALID_DATA'), 'error');
			return false;
		}

		if($payment_method == 'paypal' && empty($vendor->vendor_params->paypal_email)) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('HIKAM_ERR_PAYPAL_EMAIL_EMPTY'), 'error');
			return false;
		}

		$formData = JRequest::getVar('data', array(), '', 'array');
		if(!empty($formData) && $payment_method == 'manual' && !empty($formData['validation'])) {
			$shopConfig = hikamarket::config(false);
			$confirmed_status = $shopConfig->get('order_confirmed_status', 'confirmed');

			$update_order = new stdClass();
			$update_order->order_id = (int)$order_id;
			$update_order->order_status = $confirmed_status;
			$update_order->history = new stdClass();
			$update_order->history->history_reason = JText::_('MANUAL_VALIDATION');
			$update_order->history->history_notified = false;

			if(!empty($formData['notify']))
				$update_order->history->history_notified = true;

			$orderClass = hikamarket::get('shop.class.order');
			$status = $orderClass->save($update_order);

			$data = array(
				'result' => ($status ? $confirmed_status : 'error')
			);

			echo '<html><body>'.
				'<script type="text/javascript">'."\r\n".
				'window.parent.hikamarket.submitBox('.json_encode($data).');'."\r\n".
				'</script>'."\r\n".
				'</body></html>';
			exit;
		}

		JRequest::setVar('layout', 'paymanual');
		return parent::display();
		return false;
	}

	public function selection() {
		JRequest::setVar('layout', 'selection');
		return parent::display();
	}

	public function useselection() {
		JRequest::setVar('layout', 'useselection');
		return parent::display();
	}

	public function searchfields() {
		JRequest::setVar('layout', 'searchfields');
		return parent::display();
	}

	public function reports() {
		$tmpl = JRequest::getCmd('tmpl', '');
		if($tmpl == 'ajax') {
			return $this->reportsAjax();
		}

		JRequest::setVar('layout', 'reports');
		return parent::display();
	}

	protected function reportsAjax() {
		$vendor_id = hikamarket::getCID('vendor_id', 0);
		$statName = JRequest::getCmd('chart', '');
		$statValue = JRequest::getString('value', '');
		if(empty($vendor_id) || empty($statName) || empty($statValue)) {
			echo '{}';
			exit;
		}

		$statisticsClass = hikamarket::get('class.statistics');
		$ret = $statisticsClass->getAjaxData($vendor_id, $statName, $statValue);

		if($ret === false) {
			echo '{}';
			exit;
		}
		echo $ret;
		exit;
	}

	public function getUploadSetting($upload_key, $caller = '') {
		$vendor_id = JRequest::getInt('vendor_id');
		$vendorClass = hikamarket::get('class.vendor');
		$vendor = $vendorClass->get($vendor_id);
		if(empty($upload_key) || (empty($vendor) && !empty($vendor_id)))
			return false;

		$upload_value = null;
		$upload_keys = array(
			'vendor_image' => array(
				'type' => 'image',
				'field' => 'data[vendor][vendor_image]'
			)
		);

		if(empty($upload_keys[$upload_key]))
			return false;
		$upload_value = $upload_keys[$upload_key];

		return array(
			'limit' => 1,
			'type' => $upload_value['type'],
			'options' => array(),
			'extra' => array(
				'vendor_id' => $vendor_id,
				'field_name' => $upload_value['field']
			)
		);
	}

	public function manageUpload($upload_key, &$ret, $uploadConfig, $caller = '') {
		if(empty($ret) || empty($ret->name) || empty($uploadConfig['extra']['vendor_id']))
			return;

		$vendorClass = hikamarket::get('class.vendor');
		$vendor = new stdClass();
		$vendor->vendor_id = (int)$uploadConfig['extra']['vendor_id'];
		$vendor->vendor_image = $ret->name;
		$vendorClass->save($vendor);
	}

	public function getPrice() {
		$currency_id = JRequest::getInt('currency_id', 0);
		$price_id = JRequest::getFloat('value', 0);
		$currencyClass = hikamarket::get('shop.class.currency');
		echo $currencyClass->format($price_id, $currency_id);
		exit;
	}

	public function getValues() {
		$displayFormat = JRequest::getVar('displayFormat', '');
		$search = JRequest::getVar('search', null);
		$start = JRequest::getInt('start', 0);

		$nameboxType = hikamarket::get('type.namebox');
		$options = array(
			'displayFormat' => $displayFormat
		);
		if($start > 0)
			$options['page'] = $start;
		$ret = $nameboxType->getValues($search, 'vendor', $options);
		if(!empty($ret)) {
			echo json_encode($ret);
			exit;
		}
		echo '[]';
		exit;
	}
}
