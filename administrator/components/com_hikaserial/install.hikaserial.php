<?php
$version = explode('.',PHP_VERSION);
if(!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);
$hikashopFile = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php';
if(!file_exists($hikashopFile)) {
	echo '<html><body><h1>This extension works with HikaShop.</h1>'.
			'<h2>Please install HikaShop (starter, essential or business) before installing HikaSerial.</h2>'.
			'installation abort...</body></html>';
	exit;
}
include_once($hikashopFile);
$hikashopConfig = hikashop_config();
if($version[0] < 5) {
	echo '<html><body><h1>This extension works with PHP 5 or newer.</h1>'.
		'<h2>Please contact your web hosting provider to update your PHP version</h2>'.
		'installation abort...</body></html>';
	exit;
} else if(version_compare(JVERSION, '2.5.0', '<') && version_compare($hikashopConfig->get('version', '1.0'), '2.6.0', '<')) {
	echo '<html><body><h1>This extension works with HikaShop 2.6.0 or newer.</h1>'.
		'<h2>Please install the latest version of HikaShop.</h2>'.
		'installation abort...</body></html>';
	$db = JFactory::getDBO();
	$db->setQuery('REPLACE INTO `#__hikaserial_config` (`config_namekey`,`config_value`) VALUES (\'installcomplete\',\'0\')');
	$db->query();
	exit;
} else {
	class hikaserialInstall {
		private $level = 'Standard';
		private $version = '1.10.4';
		private $update = false;
		private $fromLevel = '';
		private $fromVersion = '';
		private $db;

		public function __construct() {
			$this->db = JFactory::getDBO();
		}

		public function addPref() {
			$conf = JFactory::getConfig();
			$this->level = ucfirst($this->level);
			$allPref = array(
				'level' => $this->level,
				'version' => $this->version,

				'show_footer' => '1',

				'installcomplete' => '0',
				'Standard' => '0'
			);
			$sep = '';
			$query = 'INSERT IGNORE INTO `'.HIKASERIAL_DBPREFIX.'config` (`config_namekey`,`config_value`,`config_default`) VALUES ';
			foreach($allPref as $n => $v) {
				$query .= $sep.'('.$this->db->Quote($n).','.$this->db->Quote($v).','.$this->db->Quote($v).')';
				$sep = ',';
			}
			$this->db->setQuery($query);
			$this->db->query();
		}

		public function updatePref() {
			$this->db->setQuery('SELECT `config_namekey`, `config_value` FROM `'.HIKASERIAL_DBPREFIX.'config` WHERE `config_namekey` IN (\'version\',\'level\')', 0 , 2);
			$res = $this->db->loadObjectList('config_namekey');
			if($res['version']->config_value == $this->version && $res['level']->config_value == $this->level)
				return true;

			$this->update = true;
			$this->fromLevel = $res['level']->config_value;
			$this->fromVersion = $res['version']->config_value;
			$query = 'REPLACE INTO `'.HIKASERIAL_DBPREFIX.'config` (`config_namekey`,`config_value`) VALUES (\'level\','.$this->db->Quote($this->level).'),(\'version\','.$this->db->Quote($this->version).'),(\'installcomplete\',\'0\')';
			$this->db->setQuery($query);
			$this->db->query();
		}

		public function updateSQL() {
			$structs = array(
				'order' => array(
					'order_serial_params' => 'TEXT NOT NULL DEFAULT \'\'',
				),
			);

			foreach($structs as $table => &$v) {
				$sql = array();
				$current = array();

				if(!HIKASHOP_J25) {
					$tmp = $this->db->getTableFields(hikaserial::table('shop.'.$table));
					$current = reset($tmp);
					unset($tmp);
				} else {
					$current = $this->db->getTableColumns(hikaserial::table('shop.'.$table));
				}

				foreach($v as $col => $colSql) {
					if(!isset($current[$col])) {
						$sql[] = 'ADD COLUMN `' . $col . '` ' . $colSql;
					}
				}
				if(!empty($sql)) {
					$query = 'ALTER TABLE `'.hikaserial::table('shop.'.$table).'` '.implode(',', $sql);
					$this->db->setQuery($query);
					try {
						$this->db->query();
					}catch(Exception $e) { }
					unset($query);
				}
				unset($sql);
			}

			if(!$this->update)
				return true;


			if(version_compare($this->fromVersion, '1.2.0', '<')) {
				$query = "CREATE TABLE IF NOT EXISTS `#__hikaserial_consumer` (".
					"	`consumer_id` INT(10) NOT NULL AUTO_INCREMENT,".
					"	`consumer_type` VARCHAR(255) NOT NULL,".
					"	`consumer_published` INT(4) NOT NULL DEFAULT 0,".
					"	`consumer_name` VARCHAR(255) NOT NULL,".
					"	`consumer_ordering` INT(10) NOT NULL DEFAULT 0,".
					"	`consumer_description` TEXT NOT NULL DEFAULT '',".
					"	`consumer_params` TEXT NOT NULL DEFAULT '',".
					"	`consumer_access` VARCHAR(255) NOT NULL DEFAULT 'all',".
					"	PRIMARY KEY (`consumer_id`)".
					") ENGINE=MyISAM;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if(version_compare($this->fromVersion, '1.3.0', '<')) {
				$this->addColumns('serial', array("`serial_extradata` TEXT NULL DEFAULT NULL"));
			}

			if(version_compare($this->fromVersion, '1.4.0', '<')) {
				$query = "CREATE TABLE IF NOT EXISTS `#__hikaserial_history` (".
					"	`history_id` INT(10) NOT NULL AUTO_INCREMENT,".
					"	`history_serial_id` INT(10) unsigned NOT NULL DEFAULT '0',".
					"	`history_created` INT(10) unsigned NOT NULL DEFAULT '0',".
					"	`history_ip` VARCHAR(255) NOT NULL DEFAULT '',".
					"	`history_new_status` VARCHAR(255) NOT NULL DEFAULT '',".
					"	`history_type` VARCHAR(255) NOT NULL DEFAULT '',".
					"	`history_data` TEXT NOT NULL DEFAULT '',".
					"	`history_user_id` INT(10) unsigned DEFAULT '0',".
					"	PRIMARY KEY (`history_id`)".
					") ENGINE=MyISAM;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if(version_compare($this->fromVersion, '1.5.0', '<')) {
				$file = HIKASERIAL_BACK.'admin.hikaserial.php';
				if(file_exists($file)) JFile::delete($file);
			}

			if(version_compare($this->fromVersion, '1.5.1', '<')) {
				$query = "CREATE TABLE IF NOT EXISTS `#__hikaserial_plugin` (".
					"	`plugin_id` INT(10) NOT NULL AUTO_INCREMENT,".
					"	`plugin_type` VARCHAR(255) NOT NULL,".
					"	`plugin_published` INT(4) NOT NULL DEFAULT 0,".
					"	`plugin_name` VARCHAR(255) NOT NULL,".
					"	`plugin_ordering` INT(10) NOT NULL DEFAULT 0,".
					"	`plugin_description` TEXT NOT NULL DEFAULT '',".
					"	`plugin_params` TEXT NOT NULL DEFAULT '',".
					"	`plugin_access` VARCHAR(255) NOT NULL DEFAULT 'all',".
					"	PRIMARY KEY (`plugin_id`)".
					") ENGINE=MyISAM;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if(version_compare($this->fromVersion, '1.10.0', '<')) {
				$this->addColumns('pack', array(
					"`pack_vendor_id` INT(10) NOT NULL DEFAULT 0",
					"`pack_manage_access` VARCHAR(255) NOT NULL DEFAULT 'all'",
				));
			}

			if(version_compare($this->fromVersion, '1.10.4', '<')) {
				$this->addColumns('zone', "INDEX (`serial_pack_id`)");
			}
		}

		protected function addColumns($table, $columns) {
			if(!is_array($columns))
				$columns = array($columns);
			$query = 'ALTER TABLE `'.hikaserial::table($table).'` ADD '.implode(', ADD', $columns).';';
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
					$query = 'ALTER TABLE `'.hikaserial::table($table).'` ADD '.$col.';';
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
			return false;
		}

		public function displayInfo() {
			$url = 'index.php?option='.HIKASERIAL_COMPONENT.'&ctrl=update&task=install&update='.(int)$this->update;
			echo '<h1>Please wait...</h1>'.
				'<h2>'.HIKASERIAL_NAME.' will now automatically install the Plugins and the Modules</h2>'.
				'<a href="'.$url.'">Please click here if you are not automatically redirected within 3 seconds</a>'.
				'<script language="javascript" type="text/javascript">'."\r\n".'document.location.href="'.$url.'";'."\r\n".'</script>';
		}
	}

	class hikaserialUninstall {
		private $db;

		public function __construct() {
			$this->db = JFactory::getDBO();
			$this->db->setQuery('DELETE FROM `#__hikaserial_config` WHERE `config_namekey` = \'li\'');
			$this->db->query();
			if(version_compare(JVERSION,'1.6.0','>=')){
				$this->db->setQuery('DELETE FROM `#__menu` WHERE link LIKE \'%com_hikaserial%\'');
				$this->db->query();
			}
		}

		public function unpublishModules() {
			$this->db->setQuery('UPDATE `#__modules` SET `published` = 0 WHERE `module` LIKE \'%hikaserial%\'');
			$this->db->query();
		}

		public function unpublishPlugins() {
			if(!HIKASHOP_J16){
				$this->db->setQuery('UPDATE `#__plugins` SET `published` = 0 WHERE `element` LIKE \'%hikaserial%\' AND `folder` NOT LIKE \'%hikaserial%\'');
			} else {
				$this->db->setQuery('UPDATE `#__extensions` SET `enabled` = 0 WHERE `type` = \'plugin\' AND `element` LIKE \'%hikaserial%\' AND `folder` NOT LIKE \'%hikaserial%\'');
			}
			$this->db->query();
		}
	}

	function com_install() {
		if(!defined('DS'))
			define('DS', DIRECTORY_SEPARATOR);
		include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaserial'.DS.'helpers'.DS.'helper.php');
		$lang = JFactory::getLanguage();
		$lang->load(HIKASERIAL_COMPONENT,JPATH_SITE);

		$installClass = new hikaserialInstall();
		$installClass->addPref();
		$installClass->updatePref();
		$installClass->updateSQL();
		$installClass->displayInfo();
	}

	function com_uninstall(){
		$uninstallClass = new hikaserialUninstall();
		$uninstallClass->unpublishModules();
		$uninstallClass->unpublishPlugins();
	}

	class com_hikaserialInstallerScript {
		public function install($parent) {
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaserial'.DS.'helpers'.DS.'helper.php');
			$lang = JFactory::getLanguage();
			$lang->load(HIKASERIAL_COMPONENT,JPATH_SITE);

			$installClass = new hikaserialInstall();
			$installClass->addPref();
			$installClass->updatePref();
			$installClass->updateSQL();
			$installClass->displayInfo();
		}

		public function update($parent) {
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaserial'.DS.'helpers'.DS.'helper.php');
			$lang = JFactory::getLanguage();
			$lang->load(HIKASERIAL_COMPONENT,JPATH_SITE);

			$installClass = new hikaserialInstall();
			$installClass->addPref();
			$installClass->updatePref();
			$installClass->updateSQL();
			$installClass->displayInfo();
		}

		public function uninstall($parent)	{
			$uninstallClass = new hikaserialUninstall();
			$uninstallClass->unpublishModules();
			$uninstallClass->unpublishPlugins();
		}

		public function preflight($type, $parent) {
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			$hikashopFile = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php';
			if(!file_exists($hikashopFile)) {
				echo '<h1>This extension works with HikaShop.</h1>'.
					'<h2>Please install HikaShop (starter, essential or business) before installing HikaSerial.</h2>'.
					'installation abort.';
				JError::raiseWarning(null, 'Cannot install HikaSerial without HikaShop');
				return false;
			}

			include_once($hikashopFile);
			$hikashopConfig = hikashop_config();

			if(version_compare($hikashopConfig->get('version', '1.0'), '2.6.0', '<')) {
				echo '<h1>This extension works with HikaShop 2.6.0 or newer.</h1>'.
					'<h2>Please install the latest version of HikaShop before.</h2>'.
					'installation abort.';
				if($type == 'update')
					JError::raiseWarning(null, 'Cannot update HikaSerial 1.10.4 without HikaShop 2.6.0 or newer');
				else
					JError::raiseWarning(null, 'Cannot install HikaSerial 1.10.4 without HikaShop 2.6.0 or newer');

				$joomConf = JFactory::getConfig();
				$debug = $joomConf->get('debug');
				if($debug) {
					JError::raiseError(500, 'Cannot install HikaSerial 1.10.4 without HikaShop 2.6.0 or newer');
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
