<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class hikaauctionMailClass extends hikaauctionClass {

	protected $tables = array();
	protected $pkeys = array();
	protected $toggle = array();
	protected $shopMailClass = null;

	public function  __construct($config = array()) {
		parent::__construct($config);
		$this->shopMailClass = hikaauction::get('shop.class.mail');
		$this->shopMailClass->mailer = JFactory::getMailer();
		$this->shopMailClass->mail_folder = HIKAAUCTION_MEDIA . 'mail' . DS;
	}

	public function queueProcess($item) {
		if(!isset($item->queue_type) || !in_array($item->queue_type, array('mail')))
			return false;

		$item_queue_data = is_string($item->queue_data) ? unserialize($item->queue_data) : $item->queue_data;

		$productClass = hikaauction::get('shop.class.product');
		$userClass = hikaauction::get('shop.class.user');

		$user = $userClass->get((int)$item->queue_user_id);
		$product = $productClass->get((int)$item->queue_product_id);

		$data = new stdClass();
		$data->user = $user;
		$data->product = $product;

		if(count($item_queue_data) > 1 )
			$data->price = $item_queue_data[1];

		$mail = $this->load($item_queue_data[0], $data);

		if(empty($mail) || !$mail->published)
			return false;

		$mail->dst_name =& $user->name;
		$mail->dst_email =& $user->user_email;

		$shopConfig = hikaauction::config(false);

		$mail->from_email = $shopConfig->get('from_email');
		$mail->from_name = $shopConfig->get('from_name');

		$this->sendMail($mail);

		return true;
	}

	public function batchProcess($items) {
		$item = reset($items);
		$data = $item->queue_data;
		$mail = $this->load('auction_update_batch', $data);

		if(empty($mail) || $mail->published)
			return false;

		$config = hikaauction::config();
		$bcc_limitation = $config->get('bcc_limitation', 20);
		if($bcc_limitation < 1)
			$bcc_limitation = 20;

		$order_ids = array();
		foreach($items as $item) {
			$order_ids[] = $item->queue_order_id;
		}

		$query = $this->dbHelper->getQuery(true);
		$query->select('o.order_id, u.user_email')
			->from(hikaauction::table('shop.user').' AS u')
			->innerjoin(hikaauction::table('shop.order').' AS o ON o.order_user_id = u.user_id')
			->where('o.order_id IN (' . $this->dbHelper->implode($order_ids).')');
		$this->db->setQuery($query);
		$emails = $this->db->loadObjectList();

		$mail->bcc_email = array();
		foreach($emails as $email) {
			$mail->bcc_email[] = $email->user_email;
			if(count($mail->bcc_email) == $bcc_limitation) {
				$this->sendMail($mail);
				$this->shopMailClass->mailer = JFactory::getMailer();
				$mail->bcc_email = array();
			}
		}
		if(!empty($mail->bcc_email))
			$this->sendMail($mail);

		$query = $this->dbHelper->getQuery(true);
		$query->delete(hikaauction::table('queue'))
			->where('queue_id IN (0)');
		$this->db->setQuery($query);
		$this->db->query();
	}

	public function loadInfos($name) {
		if(substr($name, 0, 7) == 'auction.')
			$name = substr($name, 7);

		$mail = new stdClass();
		$mail->mail_name = $name;
		$this->shopMailClass->loadInfos($mail, 'auction.'.$name);

		if(!empty($mail))
			$mail->hikaauction = true;

		return $mail;
	}

	public function load($name, &$data) {
		$this->shopMailClass->mailer = JFactory::getMailer();

		if(substr($name, 0, 7) == 'auction.')
			$name = substr($name, 7);

		$mail = new stdClass();
		$mail->mail_name = $name;
		$this->shopMailClass->loadInfos($mail, 'auction.'.$name);

		$mail->body = $this->shopMailClass->loadEmail($mail, $data);
		$mail->altbody = $this->shopMailClass->loadEmail($mail, $data, 'text');
		$mail->preload = $this->shopMailClass->loadEmail($mail, $data, 'preload');
		$mail->data =& $data;
		$mail->mailer =& $this->shopMailClass->mailer;
		if($data !== true)
			$mail->body = hikaauction::absoluteURL($mail->body);
		if(empty($mail->altbody) && $data !== true)
			$mail->altbody = $this->shopMailClass->textVersion($mail->body);

		return $mail;
	}

	public function sendMail(&$mail) {
		return $this->shopMailClass->sendMail($mail);
	}

	public function beforeMailPrepare(&$mail, &$mailer, &$do) {
		$mail_name = $mail->mail_name;
		if(isset($mail->hikaauction) && !empty($mail->hikaauction)) {
			$mail_name = 'auction.' . $mail_name;

			if(empty($mail->attachments)) {
				$mail->attachments = $this->shopMailClass->loadAttachments($mail_name);
			}
		}
	}
}
