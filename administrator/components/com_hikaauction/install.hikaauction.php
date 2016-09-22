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
$version = explode('.',PHP_VERSION);
if(!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR);

if(version_compare(JVERSION, '2.5.0', '<')) {
	$hikashopFile = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php';
	if(!file_exists($hikashopFile)) {
		echo '<html><body><h1>This extension works with HikaShop.</h1>'.
			'<h2>Please install HikaShop (starter, essential or business) before installing HikaAuction.</h2>'.
			'installation abort...</body></html>';
		$db = JFactory::getDBO();
		$db->setQuery('REPLACE INTO `#__hikaauction_config` (`config_namekey`,`config_value`) VALUES (\'installcomplete\',\'0\')');
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
	$db->setQuery('REPLACE INTO `#__hikaauction_config` (`config_namekey`,`config_value`) VALUES (\'installcomplete\',\'0\')');
	$db->query();
	exit;
} else if(version_compare(JVERSION, '2.5.0', '<') && version_compare($hikashopConfig->get('version', '1.0'), '2.5.0', '<')) {
	echo '<html><body><h1>This extension works with HikaShop 2.5.0 or newer.</h1>'.
		'<h2>Please install the latest version of HikaShop.</h2>'.
		'installation abort...</body></html>';
	$db = JFactory::getDBO();
	$db->setQuery('REPLACE INTO `#__hikaauction_config` (`config_namekey`,`config_value`) VALUES (\'installcomplete\',\'0\')');
	$db->query();
	exit;
} else {
	class hikaauctionInstall {
		private $level = 'standard';
		private $version = '1.2.0';
		private $update = false;
		private $fromLevel = '';
		private $fromVersion = '';
		private $db;

		public function __construct() {
			$this->dbHelper = hikaauction::get('helper.database');
			$this->db = $this->dbHelper->get();
		}

		public function addPref() {
			$conf = JFactory::getConfig();
			$this->version = ucfirst($this->version);
			$allPref = array(
				'version' => $this->version,
				'level' => $this->level,
				'starter' => 0,
				'essential' => 1,
				'installcomplete' => 1,
			);

			$query = $this->dbHelper->getQuery(true);
			$query->delete(HIKAAUCTION_DBPREFIX.'config')
				->where('config_namekey IN ('.$this->dbHelper->implode(array_keys($allPref)).')');
			$this->db->setQuery($query);
			$this->db->query();

			$query = $this->dbHelper->getQuery(true);
			$query->insert(HIKAAUCTION_DBPREFIX.'config')
				->columns( $query->quoteName(array('config_namekey', 'config_value', 'config_default')) );
			foreach($allPref as $k => $v) {
				$query->values($this->dbHelper->implode(array($k, $v, $v)));
			}
			$this->db->setQuery($query);
			$this->db->query();

			$allPref = array(
				'auction.auction_bidders_outbid_notification.html' => 1,
				'auction.auction_bidders_outbid_notification.subject' => 'AUCTION_EMAIL_OUTBID_TITLE',
				'auction.auction_bidders_outbid_notification.published' => 1,

				'auction.auction_cancelled.html' => 1,
				'auction.auction_cancelled.subject' => 'AUCTION_NEW_BID_EMAIL_TITLE',
				'auction.auction_cancelled.published' => 0,

				'auction.auction_finished_bidders_notification.html' => 1,
				'auction.auction_finished_bidders_notification.subject' => 'AUCTION_FINISHED_BIDDERS_EMAIL_TITLE',
				'auction.auction_finished_bidders_notification.published' => 1,

				'auction.auction_finished_winner_notification.html' => 1,
				'auction.auction_finished_winner_notification.subject' => 'AUCTION_FINISHED_WINNER_EMAIL_TITLE',
				'auction.auction_finished_winner_notification.published' => 1,

				'auction.auction_price_changed_notification.html' => 1,
				'auction.auction_price_changed_notification.subject' => 'AUCTION_NEW_BID_EMAIL_TITLE',
				'auction.auction_price_changed_notification.published' => 0,

				'auction.auction_update.html' => 1,
				'auction.auction_update.subject' => 'AUCTION_EMAIL_UPDATE_TITLE',
				'auction.auction_update.published' => 0,
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
			$query = $this->dbHelper->getQuery(true);

			$elems = $this->dbHelper->quote(array('version','level'));
			$query->select('config_namekey, config_value')
				->from(HIKAAUCTION_DBPREFIX.'config')
				->where('config_namekey IN ('.implode(',', $elems).')');
			$this->db->setQuery($query, 0 , 2);
			$res = $this->db->loadObjectList('config_namekey');
			if($res['version']->config_value == $this->version)
				return true;

			$this->update = true;
			$this->fromVersion = $res['version']->config_value;

			$values = array(
				'version' => array('version', $this->version),
				'level' => array('level', $this->level),
				'installcomplete' => array('installcomplete', 0)
			);
			$this->dbHelper->replace(HIKAAUCTION_DBPREFIX.'config', 'config_namekey', array('config_namekey', 'config_value'), $values);
		}

		public function updateSQL() {

			$structs = array(
				'product' => array(
					'product_auction' => 'INT(10) NOT NULL DEFAULT \'0\'',
					'product_bid_increment' => 'INT(10) NOT NULL DEFAULT \'-1\''
				)
			);

			foreach($structs as $table => &$v) {
				$sql = array();
				$current = array();

				if(!HIKASHOP_J25) {
					$tmp = $this->db->getTableFields(hikaauction::table('shop.'.$table));
					$current = reset($tmp);
					unset($tmp);
				} else {
					$current = $this->db->getTableColumns(hikaauction::table('shop.'.$table));
				}

				foreach($v as $col => $colSql) {
					if(!isset($current[$col])) {
						$sql[] = 'ADD COLUMN `' . $col . '` ' . $colSql;
					}
				}
				if(!empty($sql)) {
					$query = 'ALTER IGNORE TABLE `'.hikaauction::table('shop.'.$table).'` '.implode(',', $sql);
					$this->db->setQuery($query);
					$this->db->query();
					unset($query);
				}
				unset($sql);
			}

			if(!$this->update)
				return true;

		}

		public function displayInfo() {
			$url = 'index.php?option='.HIKAAUCTION_COMPONENT.'&ctrl=update&task=install&update='.(int)$this->update;
			echo '<h1>Please wait...</h1>'.
				'<h2>'.HIKAAUCTION_NAME.' will now automatically install the Plugins and the Modules</h2>'.
				'<a href="'.$url.'">Please click here if you are not automatically redirected within 3 seconds</a>'.
				'<script language="javascript" type="text/javascript">'."\r\n".'document.location.href="'.$url.'";'."\r\n".'</script>';
		}
	}

	class hikaauctionUninstall{
		private $db;

		function __construct(){
			$this->dbHelper = hikaauction::get('helper.database');
			$this->db = $this->dbHelper->get();

			$query = $this->dbHelper->getQuery(true);
			$query->delete('#__hikaauction_config')->where('config_namekey = ' . $query->quote('li'));
			$this->db->setQuery($query);
			$this->db->query();

			if(version_compare(JVERSION,'1.6.0', '>=')) {
				$query = $this->dbHelper->getQuery(true);
				$query->delete('#__menu')->where('link LIKE \'%' . $query->escape('com_hikaauction') . '%\'');
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		function unpublishModules(){
			$query = $this->dbHelper->getQuery(true);
			$query->update('#__modules')
				->set('published = 0')
				->where('module LIKE \'%'.$query->escape('hikaauction').'%\'');
			$this->db->setQuery($query);
			$this->db->query();
		}

		function unpublishPlugins() {
			$query = $this->dbHelper->getQuery(true);
			if(!HIKASHOP_J16){
				$query->update('#__plugins')
					->set('published = 0')
					->where(array(
						'element LIKE \'%'.$query->escape('hikaauction').'%\'',
						'folder NOT LIKE \'%'.$query->escape('hikaauction').'%\''
					));
			} else {
				$query->update('#__extensions')
					->set('enabled = 0')
					->where(array(
						'type = \'plugin\'',
						'element LIKE \'%'.$query->escape('hikaauction').'%\'',
						'folder NOT LIKE \'%'.$query->escape('hikaauction').'%\''
					));
			}
			$this->db->setQuery($query);
			$this->db->query();
			$this->db->query();
		}
	}

	if(version_compare(JVERSION, '2.5.0', '<')) {
		function com_install() {
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaauction'.DS.'helpers'.DS.'helper.php');
			$lang = JFactory::getLanguage();
			$lang->load(HIKAAUCTION_COMPONENT,JPATH_SITE);

			$installClass = new hikaauctionInstall();
			$installClass->addPref();
			$installClass->updatePref();
			$installClass->updateSQL();
			$installClass->displayInfo();
		}

		function com_uninstall(){
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaauction'.DS.'helpers'.DS.'helper.php');

			$uninstallClass = new hikaauctionUninstall();
			$uninstallClass->unpublishModules();
			$uninstallClass->unpublishPlugins();
		}
	}

	class com_hikaauctionInstallerScript {
		function install($parent) {
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaauction'.DS.'helpers'.DS.'helper.php');
			$lang = JFactory::getLanguage();
			$lang->load(HIKAAUCTION_COMPONENT,JPATH_SITE);

			$installClass = new hikaauctionInstall();
			$installClass->addPref();
			$installClass->updatePref();
			$installClass->updateSQL();
			$installClass->displayInfo();
		}

		function update($parent) {
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaauction'.DS.'helpers'.DS.'helper.php');
			$lang = JFactory::getLanguage();
			$lang->load(HIKAAUCTION_COMPONENT,JPATH_SITE);

			$installClass = new hikaauctionInstall();
			$installClass->addPref();
			$installClass->updatePref();
			$installClass->updateSQL();
			$installClass->displayInfo();
		}

		function uninstall($parent)	{
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikaauction'.DS.'helpers'.DS.'helper.php');

			$uninstallClass = new hikaauctionUninstall();
			$uninstallClass->unpublishModules();
			$uninstallClass->unpublishPlugins();
		}

		function preflight($type, $parent) {
			if(!defined('DS'))
				define('DS', DIRECTORY_SEPARATOR);
			$hikashopFile = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikashop'.DS.'helpers'.DS.'helper.php';
			if(!file_exists($hikashopFile)) {
				echo '<h1>This extension works with HikaShop.</h1>'.
					'<h2>Please install HikaShop (starter, essential or business) before installing HikaAuction.</h2>'.
					'installation abort.';
				JError::raiseWarning(null, 'Cannot install HikaAuction without HikaShop');
				return false;
			}

			include_once($hikashopFile);
			$hikashopConfig = hikashop_config();

			if(version_compare($hikashopConfig->get('version', '1.0'), '2.5.0', '<')) {
				echo '<h1>This extension works with HikaShop 2.5.0 or newer.</h1>'.
					'<h2>Please install the latest version of HikaShop before.</h2>'.
					'installation abort.';
				if($type == 'update')
					JError::raiseWarning(null, 'Cannot update HikaAuction 1.2.0 without HikaShop 2.5.0 or newer');
				else
					JError::raiseWarning(null, 'Cannot install HikaAuction 1.2.0 without HikaShop 2.5.0 or newer');

				$joomConf = JFactory::getConfig();
				$debug = $joomConf->get('debug');
				if($debug) {
					JError::raiseError(500, 'Cannot install HikaAuction 1.2.0 without HikaShop 2.5.0 or newer');
				}
				return false;
			}
			return true;
		}

		function postflight($type, $parent) {
			return true;
		}
	}
}
