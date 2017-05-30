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
class hikamarketMailClass extends hikamarketClass {

	protected $tables = array();
	protected $pkeys = array();
	protected $toggle = array();

	private $orderEmails = array(
		'order_admin_notification' => 1,
		'order_cancel' => 0,
		'order_creation_notification' => 0,
		'order_notification' => 0,
		'order_status_notification' => 0
	);

	public function  __construct($config = array()){
		$marketConfig = hikamarket::config();
		return parent::__construct($config);
	}

	public function load($name, &$data) {
		$shopMailClass = hikamarket::get('shop.class.mail');
		$shopMailClass->mailer = JFactory::getMailer();
		$shopMailClass->mail_folder = HIKAMARKET_MEDIA . 'mail' . DS;

		if(substr($name, 0, 7) == 'market.')
			$name = substr($name, 7);

		$mail = new stdClass();
		$mail->mail_name = $name;
		$shopMailClass->loadInfos($mail, 'market.'.$name);

		$mail->body = $shopMailClass->loadEmail($mail, $data);
		$mail->altbody = $shopMailClass->loadEmail($mail, $data, 'text');
		$mail->preload = $shopMailClass->loadEmail($mail, $data, 'preload');
		$mail->data =& $data;
		$mail->mailer =& $shopMailClass->mailer;
		if($data !== true)
			$mail->body = hikamarket::absoluteURL($mail->body);
		if(empty($mail->altbody) && $data !== true)
			$mail->altbody = $shopMailClass->textVersion($mail->body);

		return $mail;
	}

	public function sendMail(&$mail) {
		$shopMailClass = hikamarket::get('shop.class.mail');
		return $shopMailClass->sendMail($mail);
	}

	public function cleanEmail($text) {
		return trim(preg_replace('/(%0A|%0D|\n+|\r+)/i', '', (string)$text));
	}

	public function beforeMailPrepare(&$mail, &$mailer, &$do) {
		$mail_name = $mail->mail_name;
		if(isset($mail->hikamarket) && !empty($mail->hikamarket)) {
			$mail_name = 'market.' . $mail_name;

			if(empty($mail->attachments)) {
				$shopMailClass = hikamarket::get('shop.class.mail');
				$mail->attachments = $shopMailClass->loadAttachments($mail_name);
			}
		}

		if(isset($this->orderEmails[$mail_name]))
			return $this->processOrderEmail($mail, $mailer, $do);

		if($mail_name == 'contact_request')
			return $this->processContactMail($mail, $mailer, $do);

		if($mail_name == 'new_comment')
			return $this->processCommentMail($mail, $mailer, $do);
	}

	public function processMailTemplate(&$mail, &$data, &$content, &$vars, &$texts, &$templates) {
		$mail_name = $mail->mail_name;
		if(isset($mail->hikamarket) && !empty($mail->hikamarket))
			$mail_name = 'market.' . $mail_name;

		if(isset($this->orderEmails[$mail_name]))
			return $this->processOrdernotificationTemplate($mail, $data, $content, $vars, $texts, $templates);
	}

	public function sendVendorOrderEmail(&$order) {
		if(empty($order->order_vendor_id))
			return false;
		if(!empty($order->hikamarket->vendor)) {
			$vendor =& $order->hikamarket->vendor;
		} else {
			$vendorClass = hikamarket::get('class.vendor');
			$vendor = $vendorClass->get($order->order_vendor_id);
		}

		if(empty($vendor) || empty($vendor->vendor_email) || filter_var($vendor->vendor_email, FILTER_VALIDATE_EMAIL) === false)
			return false;

		$order->vendor =& $vendor;
		if(empty($order->customer)) {
			$userClass = hikamarket::get('shop.class.user');
			$order->customer = $userClass->get($order->order_user_id);
		}

		if(empty($order->mail_status))
			$order->mail_status = @$order->order_status;

		$user_cms_id = (int)$order->customer->user_cms_id;

		$shopMailClass = hikamarket::get('shop.class.mail');
		$shopMailClass->mail_folder = HIKAMARKET_MEDIA . 'mail' . DS;
		$mail = $shopMailClass->get('order_status_notification', $order);
		$mail->hikamarket = true;

		$mail_order_status = hikamarket::orderStatus($order->mail_status);
		$mail_subject = JText::sprintf($mail->subject, $order->order_number, $mail_order_status, HIKASHOP_LIVE);


		if(empty($mail) || !$mail->published)
			return false;

		$mail->dst_email = $vendor->vendor_email;
		$mail->dst_name = $vendor->vendor_name;

		$this->setVendorNotifyEmails($mail, $vendor);
		if(empty($mail->dst_email))
			return;

		$mail->subject = $mail_subject;
		$ret = $shopMailClass->sendMail($mail);

		return $ret;
		return false;
	}

	public function setVendorNotifyEmails(&$mail, $vendor) {
		$vendor_access = $vendor->vendor_access;
		if(empty($vendor_access)) {
			$config = hikamarket::config();
			$vendor_access = $config->get('store_default_access', 'all');
		}
		if($vendor_access == 'all')
			$vendor_access = '*';
		$vendor_access = explode(',', trim(strtolower($vendor_access), ','));
		sort($vendor_access, SORT_STRING);

		if(!hikamarket::aclTest('order_notify', $vendor_access))
			return;

		$query = 'SELECT a.*,b.* FROM '.hikamarket::table('user','shop').' AS a LEFT JOIN '.hikamarket::table('users',false).' AS b ON a.user_cms_id = b.id '.
				'WHERE a.user_vendor_id = ' . (int)$vendor->vendor_id . ' ORDER BY a.user_id';
		$this->db->setQuery($query);
		$users = $this->db->loadObjectList();
		if(empty($users))
			return;

		foreach($users as $user) {
			if((is_string($mail->dst_email) && $user->user_email == $mail->dst_email) || (is_array($mail->dst_email) && in_array($user->user_email, $mail->dst_email)))
				continue;
			if(empty($user->user_vendor_access))
				continue;

			if($user->user_vendor_access == 'all')
				$user->user_vendor_access = '*';
			$user_access = explode(',', trim(strtolower($user->user_vendor_access), ','));
			sort($user_access, SORT_STRING);

			$ret = hikamarket::aclTest('order_notify', $user_access);
			if($ret) {
				if(!is_array($mail->dst_email)) $mail->dst_email = ( !empty($mail->dst_email) ? array($mail->dst_email) : array() );
				if(!is_array($mail->dst_name)) $mail->dst_name = ( !empty($mail->dst_name) ? array($mail->dst_name) : array() );

				$mail->dst_email[] = $user->user_email;
				$mail->dst_name[] = $user->username;
			}
		}
	}

	protected function loadLocale($user_cms_id) {
		return true;

		$locale = '';
		if(!empty($user_cms_id)) {
			$user = JFactory::getUser($user_cms_id);
			$locale = $user->getParam('language');
			if(empty($locale))
				$locale = $user->getParam('admin_language');
		} else if($user_cms_id === false && isset($this->oldLocale)) {
			if($this->oldLocale === false)
				return;
			$local = $this->oldLocale;
		}
		if(empty($locale)) {
			$params = JComponentHelper::getParams('com_languages');
			$locale = $params->get('site', 'en-GB');
		}

		$this->oldLocale = false;
		$lang = JFactory::getLanguage();
		if($lang->getTag() == $locale)
			return;

		$this->oldLocale = $lang->getTag();

		if(HIKASHOP_J16) {
			$joomlaConfig = JFactory::getConfig();
			$joomlaConfig->set('language', $locale);
			$debug = $joomlaConfig->get('debug');
			if(HIKASHOP_J25)
				JFactory::$language = new hikaLanguage($locale, $debug);
		} else {

		}
		$override_path = JLanguage::getLanguagePath(JPATH_ROOT) . DS . 'overrides' . DS . $locale . '.override.ini';
		$lang->load(HIKASHOP_COMPONENT, JPATH_SITE, $locale, true);
		if(file_exists($override_path)) {
			if(!HIKASHOP_J16)
				$lang->_load($override_path, 'override');
			else if(HIKASHOP_J25)
				$lang->publicLoadLanguage($override_path, 'override');
		}
		return $locale;
	}


	private function processOrderEmail(&$mail, &$mailer, &$do) {
		$supportEmail = $this->orderEmails[$mail->mail_name];
		$config = hikamarket::config();
		$vendorOrderType = 'subsale';

		$subsaleEmail = false;
		if((!empty($mail->data->order_type) && $mail->data->order_type == $vendorOrderType)
		|| (!empty($mail->data->old->order_type) && $mail->data->old->order_type == $vendorOrderType)) {
			$subsaleEmail = true;
		}

		if($subsaleEmail) {
			$do = false;
			return;
		}

		if(!$subsaleEmail && $supportEmail) {
			$vendorClass = hikamarket::get('class.vendor');
			$vendor = $vendorClass->get(1);
			$this->setVendorNotifyEmails($mail, $vendor);
		}
	}

	private function processContactMail(&$mail, &$mailer, &$do) {
		$config = hikamarket::config();

		if($config->get('contact_mail_to_vendor', 1) == 0)
			return;

		if(!empty($mail->data->product) && $mail->data->product->product_vendor_id == 0 && $mail->data->product->product_type == 'variant') {
			$productClass = hikashop_get('class.product');
			$parentProduct = $productClass->get((int)$mail->data->product->product_parent_id);
			if(!empty($parentProduct))
				$mail->data->product->product_vendor_id = $parentProduct->product_vendor_id;
		}

		if(!empty($mail->data->product) && $mail->data->product->product_vendor_id > 1) {
			$vendorClass = hikamarket::get('class.vendor');
			$vendor = $vendorClass->get($mail->data->product->product_vendor_id);
			$mail->dst_email = $vendor->vendor_email;
			$mail->dst_name = $vendor->vendor_name;
		}

		if(!empty($mail->data->element->target) && $mail->data->element->target == 'vendor') {
			$vendorClass = hikamarket::get('class.vendor');
			$vendor = $vendorClass->get($mail->data->element->vendor_id);
			$mail->dst_email = $vendor->vendor_email;
			$mail->dst_name = $vendor->vendor_name;
		}
	}

	private function processCommentMail(&$mail, &$mailer, &$do) {
		if($mail->data->result->vote_type == 'vendor') {


			$do = false;
			return;
		}

		if($mail->data->result->vote_type == 'product') {


		}
	}

	private function processOrdernotificationTemplate(&$mail, &$data, &$content, &$vars, &$texts, &$templates) {
		$config = hikamarket::config();

		if(empty($templates['PRODUCT_LINE']) || !$config->get('mail_display_vendor', 0))
			return;

		$vendor_ids = array();
		foreach($templates['PRODUCT_LINE'] as $p) {
			if(!empty($p['product']->product_vendor_id))
				$vendor_ids[ (int)$p['product']->product_vendor_id ] = (int)$p['product']->product_vendor_id;
		}
		if(empty($vendor_ids))
			return;

		$query = 'SELECT * FROM ' . hikamarket::table('vendor') . ' WHERE vendor_id IN (' . implode(',', $vendor_ids) . ') AND vendor_published = 1';
		$this->db->setQuery($query);
		$vendors = $this->db->loadObjectList('vendor_id');

		foreach($templates['PRODUCT_LINE'] as &$p) {
			if(empty($p['product']->product_vendor_id))
				continue;

			$v = (int)$p['product']->product_vendor_id;
			if(!isset($vendors[$v]))
				continue;

			$p['vendor'] = $vendors[$v];
			if(empty($p['PRODUCT_DETAILS'])) $p['PRODUCT_DETAILS'] = '';
			$p['PRODUCT_DETAILS'] .= '<br />' . JText::sprintf('SOLD_BY_VENDOR', $vendors[$v]->vendor_name);
		}
		unset($p);
	}
}
