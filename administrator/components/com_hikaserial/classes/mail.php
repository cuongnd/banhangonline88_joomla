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
class hikaserialMailClass extends hikaserialClass {

	protected $tables = array();
	protected $pkeys = array();
	protected $toggle = array();

	public function load($name, &$data) {
		$shopMailClass = hikaserial::get('shop.class.mail');
		$shopMailClass->mailer = JFactory::getMailer();
		$shopMailClass->mail_folder = HIKASERIAL_MEDIA . 'mail' . DS;

		if(substr($name, 0, 7) == 'serial.')
			$name = substr($name, 7);

		$mail = new stdClass();
		$mail->mail_name = $name;
		$shopMailClass->loadInfos($mail, 'serial.'.$name);

		$mail->body = $shopMailClass->loadEmail($mail, $data);
		$mail->altbody = $shopMailClass->loadEmail($mail, $data, 'text');
		$mail->preload = $shopMailClass->loadEmail($mail, $data, 'preload');
		$mail->data =& $data;
		$mail->mailer =& $shopMailClass->mailer;
		if($data !== true)
			$mail->body = hikaserial::absoluteURL($mail->body);
		if(empty($mail->altbody) && $data !== true)
			$mail->altbody = $shopMailClass->textVersion($mail->body);

		return $mail;
	}

	public function sendMail(&$mail) {
		$shopMailClass = hikaserial::get('shop.class.mail');
		if(!empty($mail->mailer)) {
			unset($shopMailClass->mailer);
			$shopMailClass->mailer =& $mail->mailer;
		}
		return $shopMailClass->sendMail($mail);
	}

	public function beforeMailPrepare(&$mail, &$mailer, &$do) {
		if(!isset($mail->hikaserial) || empty($mail->hikaserial))
			return;

		$mail_name = 'serial.' . $mail->mail_name;
		if(empty($mail->attachments)) {
			$shopMailClass = hikamarket::get('shop.class.mail');
			$mail->attachments = $shopMailClass->loadAttachments($mail_name);
		}
	}

	public function beforeMailSend(&$mail, &$mailer) {

		$orderEmails = array(
			'order_creation_notification' => 1,
			'order_notification' => 1,
			'order_status_notification' => 1,
		);

		$mail_name = $mail->mail_name;
		if(isset($mail->hikamarket) && !empty($mail->hikamarket))
			$mail_name = 'market.' . $mail_name;

		if(!isset($orderEmails[$mail_name]))
			return;

		if(empty($mail->data->order_id) || (int)$mail->data->order_id == 0)
			return;

		$config = hikaserial::config();
		$display_serial_statuses = $config->get('display_serial_statuses','');
		if(empty($display_serial_statuses)) {
			$display_serial_statuses = array($config->get('used_serial_status','used'));
		} else {
			$display_serial_statuses = explode(',', $display_serial_statuses);
		}
		$statuses = array();
		foreach($display_serial_statuses as $s) {
			$statuses[] = $this->db->Quote($s);
		}

		$serials = array();
		if(!empty($mail->data->order_id)) {
			$query = 'SELECT s.*, p.*, op.product_id '.
				' FROM ' . hikaserial::table('serial') . ' AS s '.
				' INNER JOIN ' . hikaserial::table('pack') . ' AS p ON s.serial_pack_id = p.pack_id '.
				' LEFT JOIN ' . hikaserial::table('shop.order_product') . ' AS op ON op.order_product_id = s.serial_order_product_id AND op.order_id = s.serial_order_id '.
				' WHERE s.serial_status IN ('.implode(',',$statuses).') AND s.serial_order_id = '. (int)$mail->data->order_id . ' ' .
				' ORDER BY s.serial_id';
			$this->db->setQuery($query);
			$serials = $this->db->loadObjectList();
		}

		if(!empty($serials)) {
			$mail->hikaserial = new stdClass();
			$mail->hikaserial->serials =& $serials;

			JPluginHelper::importPlugin('hikaserial');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onDisplaySerials', array(&$serials, 'beforeMailSend'));
			$dispatcher->trigger('onBeforeSerialMailSend', array(&$mail, &$mailer, &$serials, $mail->data));
		}
	}
}
