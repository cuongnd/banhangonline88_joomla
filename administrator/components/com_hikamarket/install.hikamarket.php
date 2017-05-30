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
$version = explode('.',PHP_VERSION);
if(!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);

if(version_compare(JVERSION, '2.5.0', '<')) {
	$hikashopFile = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php';
	if(!file_exists($hikashopFile)) {
		echo '<html><body><h1>This extension works with HikaShop.</h1>'.
			'<h2>Please install HikaShop (starter, essential or business) before installing HikaMarket.</h2>'.
			'installation abort...</body></html>';
		$db = JFactory::getDBO();
		$db->setQuery('REPLACE INTO `#__hikamarket_config` (`config_namekey`,`config_value`) VALUES (\'installcomplete\',\'0\')');
		$db->query();
		exit;
	}
	include_once($hikashopFile);
	$hikashopConfig = hikashop_config();
}

if($version[0] < 5) {
	echo '<html><body><h1>This extension works with PHP 5 or newer.</h1>'.
		'<h2>Please contact your web hosting provider to update your PHP version</h2>'.
		'installation abort...</body></html>';
	$db = JFactory::getDBO();
	$db->setQuery('REPLACE INTO `#__hikamarket_config` (`config_namekey`,`config_value`) VALUES (\'installcomplete\',\'0\')');
	$db->query();
	exit;
} else if(version_compare(JVERSION, '2.5.0', '<') && version_compare($hikashopConfig->get('version', '1.0'), '2.6.2', '<')) {
	echo '<html><body><h1>This extension works with HikaShop 2.6.2 or newer.</h1>'.
		'<h2>Please install the latest version of HikaShop.</h2>'.
		'<p>Current installed version: '.$hikashopConfig->get('version', 'indeterminate').'</p>'.
		'installation abort...</body></html>';
	$db = JFactory::getDBO();
	$db->setQuery('REPLACE INTO `#__hikamarket_config` (`config_namekey`,`config_value`) VALUES (\'installcomplete\',\'0\')');
	$db->query();
	exit;
} else {
	class hikamarketInstall {
		private $level = 'Multivendor';
		private $version = '1.7.0';
		private $freshinstall = true;
		private $update = false;
		private $fromLevel = '';
		private $fromVersion = '';
		private $db;

		public function __construct() {
			$this->db = JFactory::getDBO();

			$this->db->setQuery('SELECT COUNT(*) as `count` FROM `'.hikamarket::table('config').'` WHERE `config_namekey` IN ('.$this->db->Quote('version').','.$this->db->Quote('level').')');
			$results = $this->db->loadResult();
			if($results == 2)
				$this->freshinstall = false;
		}

		public function addPref() {
			$conf = JFactory::getConfig();
			$this->level = ucfirst($this->level);

			$allPref = array(
				'level' => $this->level
				,'version' => $this->version
				,'show_footer' => '1'
				,'installcomplete' => '0'
				,'Frontedition' => '0'
				,'Multivendor' => '1'
			);
			$sep = '';
			$query = 'INSERT IGNORE INTO `'.HIKAMARKET_DBPREFIX.'config` (`config_namekey`,`config_value`,`config_default`) VALUES ';
			foreach($allPref as $n => $v) {
				$query .= $sep.'('.$this->db->Quote($n).','.$this->db->Quote($v).','.$this->db->Quote($v).')';
				$sep = ',';
			}
			$this->db->setQuery($query);
			$this->db->query();

			$allPref = array(
				'market.product_creation.html' => 1,
				'market.product_creation.subject' => 'MARKET_PRODUCT_CREATION_SUBJECT',
				'market.product_creation.published' => 0,

				'market.product_modification.html' => 1,
				'market.product_modification.subject' => 'MARKET_PRODUCT_MODIFICATION_SUBJECT',
				'market.product_modification.published' => 0,

				'market.product_approval.html' => 1,
				'market.product_approval.subject' => 'MARKET_PRODUCT_APPROVAL_SUBJECT',
				'market.product_approval.published' => 1,
				'market.vendor_admin_registration.html' => 1,
				'market.vendor_admin_registration.subject' => 'MARKET_VENDOR_ADMIN_REGISTRATION_SUBJECT',
				'market.vendor_admin_registration.published' => 0,

				'market.vendor_registration.html' => 1,
				'market.vendor_registration.subject' => 'MARKET_VENDOR_REGISTRATION_SUBJECT',
				'market.vendor_registration.published' => 0,

				'market.vendor_payment_request.html' => 1,
				'market.vendor_payment_request.subject' => 'MARKET_VENDOR_PAYMENT_REQUEST_SUBJECT',
				'market.vendor_payment_request.published' => 0,
			);

			if(!empty($allPref)) {
				$sep = '';
				$query = 'INSERT IGNORE INTO `#__hikashop_config` (`config_namekey`,`config_value`,`config_default`) VALUES ';
				foreach($allPref as $n => $v) {
					$query .= $sep.'('.$this->db->Quote($n).','.$this->db->Quote($v).','.$this->db->Quote($v).')';
					$sep = ',';
				}
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		public function updatePref() {
			$this->db->setQuery('SELECT `config_namekey`, `config_value` FROM `'.HIKAMARKET_DBPREFIX.'config` WHERE `config_namekey` IN (\'version\',\'level\')', 0, 2);
			$res = $this->db->loadObjectList('config_namekey');
			if($res['version']->config_value == $this->version && $res['level']->config_value == $this->level)
				return true;

			$this->update = true;
			$this->fromLevel = $res['level']->config_value;
			$this->fromVersion = $res['version']->config_value;
			$query = 'REPLACE INTO `'.HIKAMARKET_DBPREFIX.'config` (`config_namekey`,`config_value`) VALUES (\'level\','.$this->db->Quote($this->level).'),(\'version\','.$this->db->Quote($this->version).'),(\'installcomplete\',\'0\')';
			$this->db->setQuery($query);
			$this->db->query();
		}

		public function updateSQL() {
			$structs = array(
				'user' => array(
					'user_vendor_id' => 'INT(10) NOT NULL DEFAULT 0',
					'user_vendor_access' => 'text NOT NULL DEFAULT \'\''
				),
				'product' => array(
					'product_status' => 'VARCHAR(255) NOT NULL DEFAULT \'\'',
					'product_vendor_params' => 'TEXT NOT NULL DEFAULT \'\''
				),
				'order' => array(
					'order_parent_id' => 'INT(10) NOT NULL DEFAULT 0',
					'order_vendor_id' => 'INT(10) NOT NULL DEFAULT 0',
					'order_vendor_price' => 'decimal(12,5) NOT NULL DEFAULT \'0.00000\'',
					'order_vendor_paid' => 'INT(10) NOT NULL DEFAULT 0',
					'order_vendor_params' => 'TEXT NOT NULL DEFAULT \'\''
				),
				'order_product' => array(
					'order_product_parent_id' => 'INT(10) NOT NULL DEFAULT 0',
					'order_product_vendor_price' => 'decimal(12,5) NOT NULL DEFAULT \'0.00000\'',
				),
				'discount' => array(
					'discount_target_vendor' => 'INT(10) NOT NULL DEFAULT 0'
				),
				'characteristic' => array(
					'characteristic_vendor_id' => 'INT(10) NOT NULL DEFAULT 0'
				),
				'shipping' => array(
					'shipping_vendor_id' => 'INT(10) NOT NULL DEFAULT 0'
				),
				'payment' => array(
					'payment_vendor_id' => 'INT(10) NOT NULL DEFAULT 0'
				),
				'plugin' => array(
					'plugin_vendor_id' => 'INT(10) NOT NULL DEFAULT 0'
				),
			);

			foreach($structs as $table => &$v) {
				$sql = array();
				$current = array();

				if(!HIKASHOP_J25) {
					$tmp = $this->db->getTableFields(hikamarket::table('shop.'.$table));
					$current = reset($tmp);
					unset($tmp);
				} else {
					$current = $this->db->getTableColumns(hikamarket::table('shop.'.$table));
				}

				foreach($v as $col => $colSql) {
					if(!isset($current[$col])) {
						$sql[] = 'ADD COLUMN `' . $col . '` ' . $colSql;
					}
				}
				if(!empty($sql)) {
					$query = 'ALTER TABLE `'.hikamarket::table('shop.'.$table).'` '.implode(',', $sql);
					$this->db->setQuery($query);
					try {
						$this->db->query();
					}catch(Exception $e) { }
					unset($query);
				}
				unset($sql);
			}

			$this->db->setQuery('SELECT COUNT(*) FROM `'.hikamarket::table('shop.field').'` WHERE field_table = ' . $this->db->Quote('plg.hikamarket.vendor'));
			$countMarketFields = $this->db->loadResult();

			if($countMarketFields == 0) {
				$query = 'INSERT IGNORE INTO `'.hikamarket::table('shop.field').'` ' . <<<EOD
(`field_table`, `field_realname`, `field_namekey`, `field_type`, `field_value`, `field_published`, `field_ordering`, `field_options`, `field_core`, `field_required`, `field_backend`, `field_frontcomp`, `field_default`, `field_backend_listing`) VALUES
('plg.hikamarket.vendor', 'Company', 'vendor_address_company', 'text', '', 1, 5, 'a:5:{s:12:"errormessage";s:0:"";s:4:"cols";s:0:"";s:4:"rows";s:0:"";s:4:"size";s:0:"";s:6:"format";s:0:"";}', 1, 0, 1, 1, '', 0),
('plg.hikamarket.vendor', 'Street', 'vendor_address_street', 'text', '', 1, 6, 'a:5:{s:12:"errormessage";s:0:"";s:4:"cols";s:0:"";s:4:"rows";s:0:"";s:4:"size";s:0:"";s:6:"format";s:0:"";}', 1, 1, 1, 1, '', 0),
('plg.hikamarket.vendor', 'Complement', 'vendor_address_street2', 'text', '', 0, 7, 'a:5:{s:12:"errormessage";s:0:"";s:4:"cols";s:0:"";s:4:"rows";s:0:"";s:4:"size";s:0:"";s:6:"format";s:0:"";}', 1, 0, 1, 1, '', 0),
('plg.hikamarket.vendor', 'Post code', 'vendor_address_post_code', 'text', '', 1, 8, 'a:5:{s:12:"errormessage";s:0:"";s:4:"cols";s:0:"";s:4:"rows";s:0:"";s:4:"size";s:0:"";s:6:"format";s:0:"";}', 1, 0, 1, 1, '', 0),
('plg.hikamarket.vendor', 'City', 'vendor_address_city', 'text', '', 1, 9, 'a:5:{s:12:"errormessage";s:0:"";s:4:"cols";s:0:"";s:4:"rows";s:0:"";s:4:"size";s:0:"";s:6:"format";s:0:"";}', 1, 1, 1, 1, '', 0),
('plg.hikamarket.vendor', 'Telephone', 'vendor_address_telephone', 'text', '', 1, 10, 'a:5:{s:12:"errormessage";s:0:"";s:4:"cols";s:0:"";s:4:"rows";s:0:"";s:4:"size";s:0:"";s:6:"format";s:0:"";}', 1, 1, 1, 1, '', 0),
('plg.hikamarket.vendor', 'Fax', 'vendor_address_fax', 'text', '', 0, 12, 'a:5:{s:12:"errormessage";s:0:"";s:4:"cols";s:0:"";s:4:"rows";s:0:"";s:4:"size";s:0:"";s:6:"format";s:0:"";}', 1, 0, 1, 1, '', 0),
('plg.hikamarket.vendor', 'State', 'vendor_address_state', 'zone', '', 1, 13, 'a:6:{s:12:"errormessage";s:0:"";s:4:"cols";s:0:"";s:4:"rows";s:0:"";s:9:"zone_type";s:5:"state";s:4:"size";s:0:"";s:6:"format";s:0:"";}', 1, 1, 1, 1, 'state_Rh__ne_1375', 0),
('plg.hikamarket.vendor', 'Country', 'vendor_address_country', 'zone', '', 1, 14, 'a:6:{s:12:"errormessage";s:0:"";s:4:"cols";s:0:"";s:4:"rows";s:0:"";s:9:"zone_type";s:7:"country";s:4:"size";s:0:"";s:6:"format";s:0:"";}', 1, 1, 1, 1, 'country_France_73', 0),
('plg.hikamarket.vendor', 'VAT number', 'vendor_address_vat', 'text', '', 1, 15, 'a:6:{s:12:"errormessage";s:0:"";s:4:"cols";s:0:"";s:4:"rows";s:0:"";s:9:"zone_type";s:7:"country";s:4:"size";s:0:"";s:6:"format";s:0:"";}', 1, 0, 1, 1, '', 0);
EOD;
				$this->db->setQuery($query);
				$this->db->query();
			}

			$this->db->setQuery('SELECT COUNT(*) FROM `'.hikamarket::table('shop.characteristic').'` WHERE characteristic_parent_id = 0 AND characteristic_alias = ' . $this->db->Quote('vendor'));
			$countVendorCharacteristics = $this->db->loadResult();

			if($countVendorCharacteristics == 0) {
				$query = 'INSERT IGNORE INTO `'.hikamarket::table('shop.characteristic').'` '.
					'(`characteristic_parent_id`, `characteristic_ordering`, `characteristic_value`, `characteristic_alias`) VALUES '.
					'(0, 0, ' . $this->db->Quote('Vendor') . ', ' . $this->db->Quote('vendor') . ')';
				$this->db->setQuery($query);
				$this->db->query();
			}

			if($this->freshinstall) {
				$e = $this->db->Quote('');
				$query = 'INSERT IGNORE INTO `'.hikamarket::table('vendor').'` '.
'(`vendor_id`,`vendor_admin_id`,`vendor_published`,`vendor_name`,`vendor_email`,`vendor_currency_id`,`vendor_description`,`vendor_access`,`vendor_shippings`,`vendor_params`) VALUES '.
'(1,0,1,'.$e.','.$e.',0,'.$e.','.$this->db->Quote('*').','.$e.','.$e.')';
				$this->db->setQuery($query);
				$this->db->query();
			}

			if($this->freshinstall) {
				$vendorCategory = new stdClass();
				$vendorCategory->category_type = 'vendor';
				$vendorCategory->category_name = 'Vendor category';
				$vendorCategory->category_namekey = 'vendor';
				$categoryClass = hikamarket::get('shop.class.category');
				$categoryClass->save($vendorCategory);
			}

			if(!$this->update)
				return true;


			if(version_compare($this->fromVersion, '1.1.0', '<')) {
				$this->addColumns('vendor', '`vendor_template_id` INT( 11 ) NOT NULL DEFAULT 0 AFTER `vendor_params`');
			}

			if(version_compare($this->fromVersion, '1.1.1', '<')) {
				$this->db->setQuery('SELECT `config_namekey`, `config_value` FROM `'.HIKAMARKET_DBPREFIX.'config` WHERE `config_namekey` IN (\'allow_registration\',\'vendor_auto_published\')', 0, 2);
				$res = $this->db->loadObjectList('config_namekey');
				if(isset($res['vendor_auto_published']) && isset($res['allow_registration']) && (int)$res['vendor_auto_published']->config_value == 1 && (int)$res['allow_registration']->config_value == 1) {
					$this->db->setQuery('UPDATE '.HIKAMARKET_DBPREFIX.'config` SET config_value = 2 WHERE config_namekey = \'allow_registration\'');
					$this->db->query();
				}
			}

			if(version_compare($this->fromVersion, '1.1.3', '<')) {
				$this->addColumns('vendor', array(
					'`vendor_site_id` VARCHAR(255) NOT NULL DEFAULT \'\'',
					'`vendor_average_score` decimal(16,5) NOT NULL DEFAULT \'0.00000\'',
					'`vendor_total_vote` INT NOT NULL DEFAULT 0'
				));
			}

			if(version_compare($this->fromVersion, '1.3.0', '<')) {
				$this->addColumns('vendor', '`vendor_image` VARCHAR(255) NOT NULL DEFAULT \'\'');
			}

			if(version_compare($this->fromVersion, '1.3.1', '<')) {
				$this->addColumns('vendor', array(
					'`vendor_zone_id` INT(10) UNSIGNED NOT NULL DEFAULT 0',
					'`vendor_terms` TEXT NOT NULL DEFAULT \'\''
				));
				$marketConfig = hikamarket::config();
				if($marketConfig->get('market_mode') == 'commission') {
					$query = 'UPDATE `'.hikamarket::table('shop.order').'` '.
						' SET order_vendor_price = (order_vendor_price - order_full_price) '.
						' WHERE order_type = '.$this->db->Quote('subsale') . ' AND order_vendor_paid = 0';
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			if(version_compare($this->fromVersion, '1.4.0', '<')) {
				$this->addColumns('fee', array(
					'`fee_min_price` decimal(16,5) NOT NULL DEFAULT \'0.00000\'',
					'`fee_fixed` decimal(16,5) NOT NULL DEFAULT \'0.00000\''
				));
			}

			if(version_compare($this->fromVersion, '1.4.1', '<')) {
				$this->addColumns('vendor', '`vendor_alias` VARCHAR(255) NOT NULL DEFAULT \'\' AFTER `vendor_name`');
				$this->addColumns('fee', '`fee_group` int(10) UNSIGNED NOT NULL DEFAULT \'0\'');
			}

			if(version_compare($this->fromVersion, '1.4.2', '<')) {
				$this->addColumns('vendor', '`vendor_canonical` VARCHAR(255) NOT NULL DEFAULT \'\' AFTER `vendor_alias`');
			}

			if(version_compare($this->fromVersion, '1.6.0', '<')) {
				$tables = array(
					array(
						'id' => 'shipping_id',
						'params' => 'shipping_params',
						'table' => 'shop.shipping',
						'param_key' => 'shipping_vendor_filter',
						'vendor_key' => 'shipping_vendor_id'
					),
					array(
						'id' => 'payment_id',
						'params' => 'payment_params',
						'table' => 'shop.payment',
						'param_key' => 'payment_vendor_id',
						'vendor_key' => 'payment_vendor_id'
					)
				);
				foreach($tables as $p) {
					$query = 'SELECT '.$p['id'].', '.$p['params'].' FROM ' . hikamarket::table($p['table']) . ' WHERE '.$p['params'].' LIKE \'%'.$p['param_key'].'%\'';
					$this->db->setQuery($query);
					$res = $this->db->loadObjectList($p['id']);
					$update = array();
					foreach($res as $k => $r) {
						$params = hikamarket::unserialize($r->{$p['params']});
						if(empty($params->{$p['param_key']}))
							continue;
						$update[(int)$k] = $params->{$p['param_key']};
					}

					if(!empty($update)) {
						$data = array();
						foreach($update as $k => $v) {
							$data[] = (int)$k . ',' . (int)$v;
						}
						$query = 'INSERT INTO ' . hikamarket::table($p['table']) . ' ('.$p['id'].', '.$p['vendor_key'].') VALUES (' . implode('),(', $data) . ') '.
								'ON DUPLICATE KEY UPDATE '.$p['vendor_key'].' = VALUES('.$p['vendor_key'].')';
						$this->db->setQuery($query);
						$this->db->query();
					}
				}

				$query = 'ALTER TABLE `#__hikamarket_vendor` CHANGE `vendor_template_id` `vendor_template_id` VARCHAR( 255 ) NOT NULL DEFAULT \'\';';
				$this->db->setQuery($query);
				try { $this->db->query(); }
				catch(Exception $e) {}

				$query = 'CREATE TABLE IF NOT EXISTS `#__hikamarket_customer_vendor` '.
					' (`customer_id` INT(10) NOT NULL, `vendor_id` INT(10) NOT NULL, PRIMARY KEY (`customer_id`,`vendor_id`)) '.
					' ENGINE=MyISAM DEFAULT CHARSET=utf8;';
				$this->db->setQuery($query);
				try {
					$this->db->query();
					$err = false;
				}catch(Exception $e) {
					$err = true;
				}
			}

			if(version_compare($this->fromVersion, '1.6.6', '<')) {
				if(version_compare($this->fromVersion, '1.6.4', '>=')) {
					$query = 'DELETE FROM `#__hikamarket_customer_vendor` WHERE 1';
					$this->db->setQuery($query);
					try {
						$this->db->query();
						$err = false;
					}catch(Exception $e) {
						$err = true;
					}
				}

				$query = 'INSERT IGNORE INTO `#__hikamarket_customer_vendor` (customer_id, vendor_id) '.
					' SELECT DISTINCT order_user_id, order_vendor_id FROM `#__hikashop_order` AS o '.
					' WHERE o.order_type = ' . $this->db->Quote('subsale') . ' AND o.order_vendor_id > 1 ';
				$this->db->setQuery($query);
				try {
					$this->db->query();
					$err = false;
				}catch(Exception $e) {
					$err = true;
				}
			}
		}

		protected function addColumns($table, $columns) {
			if(!is_array($columns))
				$columns = array($columns);
			$query = 'ALTER TABLE `'.hikamarket::table($table).'` ADD '.implode(', ADD', $columns).';';
			$this->db->setQuery($query);
			$err = false;
			try {
				$this->db->query();
			}catch(Exception $e) {
				$err = true;
			}
			if(!$err)
				return true;
			if($err && count($columns) > 1) {
				foreach($columns as $col) {
					$query = 'ALTER TABLE `'.hikamarket::table($table).'` ADD '.$col.';';
					$this->db->setQuery($query);
					$err = 0;
					try {
						$this->db->query();
					}catch(Exception $e) {
						$err++;
					}
				}
				if($err < count($columns))
					return true;
			}
			$app = JFactory::getApplication();
			$app->enqueueMessage('Error while adding column for the table &quot;'.htmlentities($table).'&quot;', 'error');
			return false;
		}

		public function displayInfo() {
			$url = 'index.php?option='.HIKAMARKET_COMPONENT.'&ctrl=update&task=install&fromversion='.$this->fromVersion.'&update='.(int)$this->update.'&freshinstall='.(int)$this->freshinstall;
			echo '<h1>Please wait...</h1>'.
				'<h2>'.HIKAMARKET_NAME.' will now automatically install the Plugins and the Modules</h2>'.
				'<a href="'.$url.'">Please click here if you are not automatically redirected within 3 seconds</a>'.
				'<script language="javascript" type="text/javascript">'."\r\n".'document.location.href="'.$url.'";'."\r\n".'</script>';
		}
	}

	class hikamarketUninstall {
		private $db;

		public function __construct(){
			$this->db = JFactory::getDBO();
			$this->db->setQuery('DELETE FROM `#__hikamarket_config` WHERE `config_namekey` = \'li\'');
			$this->db->query();
			if(version_compare(JVERSION,'1.6.0','>=')){
				$this->db->setQuery('DELETE FROM `#__menu` WHERE link LIKE \'%com_hikamarket%\'');
				$this->db->query();
			}
		}

		public function unpublishModules(){
			$this->db->setQuery('UPDATE `#__modules` SET `published` = 0 WHERE `module` LIKE \'%hikamarket%\'');
			$this->db->query();
		}

		public function unpublishPlugins(){
			if(!HIKASHOP_J16){
				$this->db->setQuery('UPDATE `#__plugins` SET `published` = 0 WHERE `element` LIKE \'%hikamarket%\' AND `folder` NOT LIKE \'%hikamarket%\'');
			} else {
				$this->db->setQuery('UPDATE `#__extensions` SET `enabled` = 0 WHERE `type` = \'plugin\' AND `element` LIKE \'%hikamarket%\' AND `folder` NOT LIKE \'%hikamarket%\'');
			}
			$this->db->query();
		}
	}

	if(version_compare(JVERSION, '2.5.0', '<')) {
		function com_install() {
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikamarket'.DS.'helpers'.DS.'helper.php');
			$lang = JFactory::getLanguage();
			$lang->load(HIKAMARKET_COMPONENT,JPATH_SITE);

			$installClass = new hikamarketInstall();
			$installClass->addPref();
			$installClass->updatePref();
			$installClass->updateSQL();
			$installClass->displayInfo();
		}

		function com_uninstall(){
			$uninstallClass = new hikamarketUninstall();
			$uninstallClass->unpublishModules();
			$uninstallClass->unpublishPlugins();
		}
	}

	class com_hikamarketInstallerScript {
		public function install($parent) {
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikamarket'.DS.'helpers'.DS.'helper.php');
			$lang = JFactory::getLanguage();
			$lang->load(HIKAMARKET_COMPONENT,JPATH_SITE);

			$installClass = new hikamarketInstall();
			$installClass->addPref();
			$installClass->updatePref();
			$installClass->updateSQL();
			$installClass->displayInfo();

			return true;
		}

		public function update($parent) {
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikamarket'.DS.'helpers'.DS.'helper.php');
			$lang = JFactory::getLanguage();
			$lang->load(HIKAMARKET_COMPONENT,JPATH_SITE);

			$installClass = new hikamarketInstall();
			$installClass->addPref();
			$installClass->updatePref();
			$installClass->updateSQL();
			$installClass->displayInfo();

			return true;
		}

		public function uninstall($parent)	{
			$uninstallClass = new hikamarketUninstall();
			$uninstallClass->unpublishModules();
			$uninstallClass->unpublishPlugins();
		}

		public function preflight($type, $parent) {
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			$hikashopFile = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php';
			if(!file_exists($hikashopFile)) {
				echo '<h1>This extension works with HikaShop.</h1>'.
					'<h2>Please install HikaShop (starter, essential or business) before installing HikaMarket.</h2>'.
					'installation abort.';
				JError::raiseWarning(null, 'Cannot install HikaMarket without HikaShop');
				return false;
			}

			include_once($hikashopFile);
			$hikashopConfig = hikashop_config();

			if(version_compare($hikashopConfig->get('version', '1.0'), '2.6.2', '<')) {
				echo '<h1>This extension works with HikaShop 2.6.2 or newer.</h1>'.
					'<h2>Please install the latest version of HikaShop before.</h2>'.
					'installation abort.';
				if($type == 'update')
					JError::raiseWarning(null, 'Cannot update HikaMarket 1.7.0 without HikaShop 2.6.2 or newer');
				else
					JError::raiseWarning(null, 'Cannot install HikaMarket 1.7.0 without HikaShop 2.6.2 or newer');

				$joomConf = JFactory::getConfig();
				$debug = $joomConf->get('debug');
				if($debug) {
					JError::raiseError(500, 'Cannot install HikaMarket 1.7.0 without HikaShop 2.6.2 or newer');
				}
				return false;
			}

			return true;
		}

		public function postflight($type, $parent) {
			return true;
		}
	}
}
