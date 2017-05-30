<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.6.3
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class hikashopPlg_email_historyClass extends hikashopClass {
	public $tables = array('email_log');
	public $pkeys = array('email_log_id');
	protected $db = null;

	private $dbStructure = array(
		'hikashop_email_log' => array(
			'fields' => array(
				'email_log_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'email_log_sender_email' => 'varchar(255) NOT NULL DEFAULT \'\'',
				'email_log_sender_name' => 'varchar(255) NOT NULL DEFAULT \'\'',
				'email_log_recipient_email' => 'varchar(255) NOT NULL DEFAULT \'\'',
				'email_log_recipient_name' => 'varchar(255) NOT NULL DEFAULT \'\'',
				'email_log_reply_email' => 'varchar(255) NOT NULL DEFAULT \'\'',
				'email_log_reply_name' => 'varchar(255) NOT NULL DEFAULT \'\'',
				'email_log_cc_email' => 'varchar(255) NOT NULL DEFAULT \'\'',
				'email_log_bcc_email' => 'varchar(255) NOT NULL DEFAULT \'\'',
				'email_log_subject' => 'text NOT NULL',
				'email_log_altbody' => 'text NOT NULL',
				'email_log_body' => 'text NOT NULL',
				'email_log_name' => 'varchar(255) NOT NULL DEFAULT \'\'',
				'email_log_ref_id' => 'varchar(255) NOT NULL DEFAULT \'\'',
				'email_log_params' => 'text NOT NULL',
				'email_log_date' => 'int(10) NOT NULL',
				'email_log_published' => 'tinyint(3) unsigned NOT NULL DEFAULT \'1\'',
			),
			'primary' => array('email_log_id')
		),
	);

	public function __construct( $config = array() ) {
		parent::__construct($config);
		$this->db = JFactory::getDBO();
	}

	public function initDB() {
		try {
			if(!HIKASHOP_J25) {
				$tmp = $this->db->getTableFields(hikashop_table('email_log'));
				$current = reset($tmp);
				unset($tmp);
			} else {
				$current = $this->db->getTableColumns(hikashop_table('email_log'));
			}
		} catch(Exception $e) {
			$current = null;
		}

		if(!empty($current))
			return true;

		$query = $this->getDBCreateQuery('hikashop_email_log');
		$this->db->setQuery($query);
		$this->db->query();
		return true;
	}

	public function beforeCheckDb(&$createTable, &$custom_fields, &$structure, &$helper) {
		$createTable['#__hikashop_email_log'] = $this->getDBCreateQuery('hikashop_email_log');
		if(!isset($structure['']))
			$structure['#__hikashop_email_log'] = $this->dbStructure['hikashop_email_log']['fields'];
	}

	private function getDBCreateQuery($name) {
		if(!isset($this->dbStructure[$name]))
			return false;

		$data = array();
		foreach($this->dbStructure[$name]['fields'] as $k => $v) {
			$data[] = '`'.$k.'` ' . $v;
		}
		if(isset($this->dbStructure[$name]['primary'])) {
			if(!is_array($this->dbStructure[$name]['primary']))
				$this->dbStructure[$name]['primary'] = array($this->dbStructure[$name]['primary']);
			$data[] = 'PRIMARY KEY (`'. implode('`, `', $this->dbStructure[$name]['primary']) . '`)';
		} else {
			$k = reset(array_keys($this->dbStructure[$name]['fields']));
			$data[] = 'PRIMARY KEY (`'. $k . '`)';
		}
		return 'CREATE TABLE IF NOT EXISTS `#__'.$name.'` (' . "\r\n" . implode(",\r\n", $data) . ') ENGINE=MyISAM;';
	}

	public function beforeMailSend(&$mail, &$mailer) {
		if(!$this->initDB())
			return false;

		$data = new stdClass();

		$data->email_log_sender_email = strip_tags($mail->from_email);

		if(!empty($mail->from_name))
			$data->email_log_sender_name = strip_tags($mail->from_name);

		if(is_array($mail->dst_email))
			$data->email_log_recipient_email = strip_tags(implode(',', $mail->dst_email));
		else
			$data->email_log_recipient_email = strip_tags($mail->dst_email);

		if(!empty($mail->dst_name)) {
			if(is_array($mail->dst_name))
				$data->email_log_recipient_name = strip_tags(implode(',', $mail->dst_name));
			else
				$data->email_log_recipient_name = strip_tags($mail->dst_name);
		}

		if(!empty($mail->reply_email))
			$data->email_log_reply_email = strip_tags($mail->reply_email);

		if(!empty($mail->reply_name))
			$data->email_log_reply_name = strip_tags($mail->reply_name);

		if(!empty($mail->cc_email)) {
			if(is_array($mail->cc_email))
				$data->email_log_cc_email = strip_tags(implode(',', $mail->cc_email));
			else
				$data->email_log_cc_email = strip_tags($mail->cc_email);
		}
		if(!empty($mail->bcc_email)) {
			if(is_array($mail->bcc_email))
				$data->email_log_bcc_email = strip_tags(implode(',', $mail->bcc_email));
			else
				$data->email_log_bcc_email = strip_tags($mail->bcc_email);
		}
		if(!empty($mail->subject))
			$data->email_log_subject = strip_tags($mail->subject);

		if(!empty($mail->altbody))
			$data->email_log_altbody = strip_tags($mail->altbody);

		if(!empty($mail->body))
			$data->email_log_body = $mail->body;

		if(!isset($mail->email_log_published)) {
			$config =& hikashop_config();
			$data->email_log_published = $config->get($mail->mail_name.'.email_log_published', 1);
		} else
			$data->email_log_published = $mail->email_log_published;

		$data->email_log_date = time();
		$data->email_log_name = $mail->mail_name;

		switch($mail->mail_name) {
			case 'user_account':
				$data->email_log_ref_id = $mail->data->user_data->user_id;
				break;
			case 'user_account_admin_notification':
				$data->email_log_ref_id = $mail->data->user_data->user_id;
				break;
			case 'order_notification':
				$data->email_log_ref_id = $mail->data->order_id;
				break;
			case 'order_admin_notification':
				$data->email_log_ref_id = $mail->data->order_id;
				break;
			case 'order_creation_notification':
				$data->email_log_ref_id = $mail->data->order_id;
				break;
			case 'order_status_notification':
				$data->email_log_ref_id = $mail->data->order_id;
				break;
			case 'payment_notification':
				$data->email_log_ref_id = $mail->data->order_id;
				break;
			case 'contact_request':
				$data->email_log_ref_id = $mail->data->product->product_id;
				break;
			case 'new_comment':
				$data->email_log_ref_id = $mail->data->type->product_id;
				break;
			case 'order_cancel':
				$data->email_log_ref_id = $mail->data->order_id;
				break;
			default:
				break;
		}
		$this->save($data);

		$pluginsClass = hikashop_get('class.plugins');
		$plugin = $pluginsClass->getByName('hikashop', 'email_history');
		if(!empty($plugin->params['number_of_days']))
			$this->clearEntries((int)$plugin->params['number_of_days']);
	}

	protected function clearEntries($days = 0) {
		if(empty($days) || (int)$days <= 0)
			return;

		$query = 'DELETE FROM '.hikashop_table('email_log').' '.
			   ' WHERE email_log_date < '.(time() - ((3600 * 24) * (int)$days));
		$this->db->setQuery($query);
		$this->db->query();
	}
}
