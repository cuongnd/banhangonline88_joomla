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
class hikamarketUpdateHelper {
	private $db;

	public function __construct() {
		$this->db = JFactory::getDBO();
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$this->update = JRequest::getBool('update');
	}

	public function addDefaultModules() {
	}

	public function createUploadFolders() {
		$file = hikamarket::get('shop.class.file');
		$path = $file->getPath('file');
		if(!JFile::exists($path.'.htaccess')) {
			$text = 'deny from all';
			JFile::write($path.'.htaccess', $text);
		}
		$path = $file->getPath('image');
	}

	public function installExtensions() {
		$path = HIKAMARKET_BACK.'extensions';
		if(!is_dir($path))
			return;
		$dirs = JFolder::folders($path);

		if(!HIKASHOP_J16) {
			$query = 'SELECT CONCAT(`folder`,`element`) FROM `#__plugins` WHERE `folder` IN '.
					"( 'hikashop','hikamarket','hikashoppayment' ) ".
					"OR `element` LIKE '%hikamarket%' ".
					"UNION SELECT `module` FROM `#__modules` WHERE `module` LIKE '%hikamarket%' OR `module` LIKE '%_market_%' OR  `module` LIKE '%hikashop%' ";
		} else {
			$query = 'SELECT CONCAT(`folder`,`element`) FROM `#__extensions` WHERE `folder` IN '.
					"( 'hikashop','hikamarket','hikashoppayment' ) ".
					"OR `element` LIKE '%hikamarket%' OR `element` LIKE '%_market_%' OR `element` LIKE '%hikashop%' ";
		}
		$this->db->setQuery($query);
		if(!HIKASHOP_J16) {
			$existingExtensions = $this->db->loadResultArray();
		} else {
			$existingExtensions = $this->db->loadColumn();
		}

		$success = array();
		$plugins = array();
		$modules = array();

		$exts = array(
			'plg_hikashop_market' => array('HikaMarket - HikaShop Integration plugin', 0, 1),
			'plg_system_hikamarketoverrides' => array('HikaMarket - HikaShop overrides plugin', 0, 1),
			'plg_hikashoppayment_paypaladaptive' => array('Hikashop (market) Paypal Adaptive Payment Plugin', 0, 1),
			'mod_market_locationsearch' => array('HikaMarket Location Search module', 0, 0),
			'plg_hikamarket_duplicateproducts' => array('HikaMarket - Duplicate products', 0, 0),
			'plg_hikamarket_vendorlocationfilter' => array('HikaMarket Vendor User Location Filter', 0, 0),
			'plg_hikamarket_vendorusergroup' => array('HikaMarket vendor user group', 0, 0),
			'plg_hikashop_market_vendorselectfield' => array('HikaShop: Vendor Selection Custom Field', 0, 1),
			'plg_hikashop_marketmodule_vendorrelated' => array('HikaShop - Vendor same products (listing module)', 0, 0),
			'plg_hikashop_productfiltervendor' => array('HikaShop - Product Filter for vendors', 0, 0),
			'plg_hikashop_productforcevendorcategory' => array('HikaShop - product force vendor category', 0, 0),
			'plg_hikashop_vendorgroupafterpurchase' => array('HikaShop - Vendor group after purchase', 0, 1),
			'plg_hikashop_vendorlocationfilter' => array('HikaShop Vendor User Location Filter', 1, 0),
			'plg_hikashop_vendorpoints' => array('HikaShop - Vendor points', 0, 1),
			'plg_search_hikamarket_vendors' => array('Search - HikaMarket Vendors', 0, 1),
		);

		$listTables = $this->db->getTableList();
		$this->errors = array();
		foreach($dirs as $dir) {
			$arguments = explode('_', $dir, 3);
			$report = true;
			if(!empty($exts[$dir][3])) {
				$report = false;
			}
			$prefix = array_shift($arguments);

			if($prefix != 'plg' && $prefix != 'mod') {
				hikamarket::display('Could not handle : '.$dir, 'error');
				continue;
			}

			$newExt = new stdClass();
			$newExt->enabled = 1;
			$newExt->params = '{}';
			$newExt->name = isset($exts[$dir][0])?$exts[$dir][0]:$dir;
			$newExt->ordering = isset($exts[$dir][1])?$exts[$dir][1]:0;

			if(!isset($exts[$dir])) {
				if($prefix == 'plg')
					$xmlFile = $path.DS.$dir.DS.$arguments[1].'.xml';
				else
					$xmlFile = $path.DS.$dir.DS.$dir.'.xml';
				if(!HIKASHOP_J16) {
					$xml = JFactory::getXMLParser('simple');
					if($xml->loadFile($xmlFile) && $xml->document->name() == 'install') {
						$newExt->name = (string)$xml->document->getElementByPath('name')->data();
						$hikainstall = $xml->document->getElementByPath('hikainstall');
						if(!empty($hikainstall)) {
							$newExt->ordering = (int)$hikainstall->attributes('ordering');
							$newExt->enabled = (int)$hikainstall->attributes('enable');
							$report = (int)$hikainstall->attributes('report');
						}
					}
					unset($xml);
				} else {
					$xml = JFactory::getXML($xmlFile);
					if (!empty($xml) && ($xml->getName() == 'install' || $xml->getName() == 'extension')) {
						$newExt->name = (string)$xml->name;
						if(isset($xml->hikainstall)) {
							$attribs = $xml->hikainstall->attributes();
							$newExt->ordering = (int)$attribs->ordering;
							$newExt->enabled = (int)$attribs->enable;
							$report = (int)$attribs->report;
						}
					}
					unset($xml);
				}
			}

			if($prefix == 'plg') {

				$newExt->type = 'plugin';
				$newExt->folder = array_shift($arguments);
				$newExt->element = implode('_', $arguments);

				if(isset($exts[$dir][2]) && is_numeric($exts[$dir][2])) {
					$newExt->enabled = (int)$exts[$dir][2];
				}

				if(!hikamarket::createDir(HIKAMARKET_ROOT.'plugins'.DS.$newExt->folder, $report))
					continue;

				if(!HIKASHOP_J16) {
					$destinationFolder = HIKAMARKET_ROOT.'plugins'.DS.$newExt->folder;
				} else {
					$destinationFolder = HIKAMARKET_ROOT.'plugins'.DS.$newExt->folder.DS.$newExt->element;
					if(!hikamarket::createDir($destinationFolder))
						continue;
				}

				if(!$this->copyFolder($path.DS.$dir, $destinationFolder))
					continue;

				if(in_array($newExt->folder.$newExt->element, $existingExtensions))
					continue;

				$plugins[] = $newExt;

			} else {

				$newExt->type = 'module';
				$newExt->folder = '';
				$newExt->element = $dir;

				$destinationFolder = HIKAMARKET_ROOT.'modules'.DS.$dir;

				if(!hikamarket::createDir($destinationFolder))
					continue;

				if(!$this->copyFolder($path.DS.$dir, $destinationFolder))
					continue;

				if(in_array($newExt->element, $existingExtensions))
					continue;

				$modules[] = $newExt;
			}
		}

		if(!empty($this->errors))
			hikamarket::display($this->errors, 'error');

		if( empty($plugins) && empty($modules) ) {
			return;
		}

		if(!HIKASHOP_J16) {
			$extensions =& $plugins;
		} else {
			$extensions = array_merge($plugins, $modules);
		}

		$success = array();
		if(!empty($extensions)) {
			if(!HIKASHOP_J16) {
				$query = 'INSERT INTO `#__plugins` (`name`,`element`,`folder`,`published`,`ordering`) VALUES ';
			} else {
				$query = 'INSERT INTO `#__extensions` (`name`,`element`,`folder`,`enabled`,`ordering`,`type`,`access`) VALUES ';
			}

			$sep = '';
			foreach($extensions as $oneExt) {
				$query .= $sep.'('.$this->db->Quote($oneExt->name).','.$this->db->Quote($oneExt->element).','.$this->db->Quote($oneExt->folder).','.$oneExt->enabled.','.$oneExt->ordering;
				if(HIKASHOP_J16) {
					$query .= ','.$this->db->Quote($oneExt->type).',1';
				}
				$query .= ')';
				if($oneExt->type!='module') {
					$success[] = JText::sprintf('PLUG_INSTALLED', $oneExt->name);
				}
				$sep = ',';
			}

			$this->db->setQuery($query);
			$this->db->query();
		}

		if(!empty($modules)) {
			foreach($modules as $oneModule) {
				if(!HIKASHOP_J16) {
					$query = 'INSERT INTO `#__modules` (`title`,`position`,`published`,`module`) VALUES '.
						'('.$this->db->Quote($oneModule->name).",'left',0,".$this->db->Quote($oneModule->element).")";
				} else {
					$query = 'INSERT INTO `#__modules` (`title`,`position`,`published`,`module`,`access`,`language`) VALUES '.
						'('.$this->db->Quote($oneModule->name).",'position-7',0,".$this->db->Quote($oneModule->element).",1,'*')";
				}
				$this->db->setQuery($query);
				$this->db->query();
				$moduleId = $this->db->insertid();

				$this->db->setQuery('INSERT IGNORE INTO `#__modules_menu` (`moduleid`,`menuid`) VALUES ('.$moduleId.',0)');
				$this->db->query();

				$success[] = JText::sprintf('MODULE_INSTALLED', $oneModule->name);
			}
		}

		if(!empty($success)) {
			hikamarket::display($success, 'success');
		}
	}

	public function copyFolder($from, $to) {
		$ret = true;

		$allFiles = JFolder::files($from);
		foreach($allFiles as $oneFile) {
			if(file_exists($to.DS.'index.html') && $oneFile == 'index.html')
				continue;
			if(JFile::copy($from.DS.$oneFile,$to.DS.$oneFile) !== true) {
				$this->errors[] = 'Could not copy the file from '.$from.DS.$oneFile.' to '.$to.DS.$oneFile;
				$ret = false;
			}
			if(version_compare(JVERSION,'1.6.0','<') && substr($oneFile,-4) == '.xml') {
				$data = file_get_contents($to.DS.$oneFile);
				if(strpos($data, '<extension ') !== false) {
					$data = str_replace(array('<extension ','</extension>','version="2.5"'), array('<install ','</install>','version="1.5"'), $data);
					file_put_contents($to.DS.$oneFile, $data);
				}
			}
		}
		$allFolders = JFolder::folders($from);
		if(!empty($allFolders)) {
			foreach($allFolders as $oneFolder) {
				if(!hikamarket::createDir($to.DS.$oneFolder))
					continue;
				if(!$this->copyFolder($from.DS.$oneFolder,$to.DS.$oneFolder))
					$ret = false;
			}
		}
		return $ret;
	}

	public function installMenu($code = '') {
		if(empty($code)) {
			$lang = JFactory::getLanguage();
			$code = $lang->getTag();
		}
		$path = JLanguage::getLanguagePath(JPATH_ROOT).DS.$code.DS.$code.'.'.HIKAMARKET_COMPONENT.'.ini';
		if(!file_exists($path))
			return;
		$content = file_get_contents($path);
		if(empty($content))
			return;

		$menuFileContent = strtoupper(HIKAMARKET_COMPONENT).'="'.HIKAMARKET_NAME.'"'."\r\n".strtoupper(HIKAMARKET_NAME).'="'.HIKAMARKET_NAME.'"'."\r\n";
		$menuStrings = array('CONFIG','VENDORS','HELP','UPDATE_ABOUT');
		foreach($menuStrings as $s) {
			preg_match('#(\n|\r)(HIKA_)?'.$s.'="(.*)"#i',$content,$matches);
			if(empty($matches[3]))
				continue;
			if(!HIKASHOP_J16) {
				$menuFileContent .= strtoupper(HIKAMARKET_COMPONENT).'.'.$s.'="'.$matches[3].'"'."\r\n";
			} else {
				$menuFileContent .= $s.'="'.$matches[3].'"'."\r\n";
			}
		}

		preg_match_all('#(\n|\r)(COM_HIKAMARKET_.*)="(.*)"#iU', $content, $matches);
		if(!empty($matches))
			$menuFileContent .= implode('', $matches[0]);
		$menuFileContent .= "\r\n" . strtoupper(HIKAMARKET_COMPONENT) . '_CONFIGURATION="'.HIKAMARKET_NAME.'"';

		if(!HIKASHOP_J16) {
			$menuPath = HIKAMARKET_ROOT.'administrator'.DS.'language'.DS.$code.DS.$code.'.'.HIKAMARKET_COMPONENT.'.menu.ini';
			if(!JFile::write($menuPath, $menuFileContent)){
				hikamarket::display(JText::sprintf('FAIL_SAVE', $menuPath), 'error');
			}
			$menuPath = HIKAMARKET_ROOT.'administrator'.DS.'language'.DS.$code.DS.$code.'.'.HIKAMARKET_COMPONENT.'.ini';
		} else {
			$menuPath = HIKAMARKET_ROOT.'administrator'.DS.'language'.DS.$code.DS.$code.'.'.HIKAMARKET_COMPONENT.'.sys.ini';
		}
		if(!JFile::write($menuPath, $menuFileContent)) {
			hikamarket::display(JText::sprintf('FAIL_SAVE',$menuPath),'error');
		}
	}

	private function installOne($folder) {
		if(empty($folder))
			return false;
		unset($GLOBALS['_JREQUEST']['installtype']);
		unset($GLOBALS['_JREQUEST']['install_directory']);
		JRequest::setVar('installtype', 'folder');
		JRequest::setVar('install_directory', $folder);
		$_REQUEST['installtype'] = 'folder';
		$_REQUEST['install_directory'] = $folder;
		$controller = new hikashopBridgeController(array(
			'base_path'=> HIKAMARKET_ROOT.'administrator'.DS.'components'.DS.'com_installer',
			'name' => 'Installer',
			'default_task' => 'installform'
		));
		$model = $controller->getModel('Install');
		return $model->install();
	}

	public function getUrl() {
		$urls = parse_url(HIKAMARKET_LIVE);
		$lurl = preg_replace('#^www2?\.#Ui', '', $urls['host'], 1);
		if(!empty($urls['path']))
			$lurl .= $urls['path'];
		return strtolower(rtrim($lurl, '/'));
	}

	public function addJoomfishElements() {
		$dstFolder = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_joomfish'.DS.'contentelements'.DS;
		if(JFolder::exists($dstFolder)) {
			$srcFolder = HIKAMARKET_BACK.'translations'.DS;
			$files = JFolder::files($srcFolder);
			if(!empty($files)) {
				foreach($files as $file) {
					JFile::copy($srcFolder.$file,$dstFolder.$file);
				}
			}
		}
		return true;
	}

	public function addUpdateSite() {
		$config = hikamarket::config();
		$newconfig = new stdClass();
		$newconfig->website = HIKASHOP_LIVE;
		$config->save($newconfig);

		if(!HIKASHOP_J16)
			return false;

		$query = 'SELECT update_site_id FROM #__update_sites WHERE location LIKE \'%hikamarket%\' AND type = \'extension\'';
		$this->db->setQuery($query);
		$update_site_id = $this->db->loadResult();

		$object = new stdClass();
		$object->name = 'Hikamarket';
		$object->type = 'extension';
		$object->enabled = 1;
		$object->location = 'http://www.hikashop.com/component/updateme/updatexml/component-hikamarket/version-'.$config->get('version').'/level-'.$config->get('level').'/li-'.urlencode(base64_encode(HIKASHOP_LIVE)).'/file-extension.xml';

		if(empty($update_site_id)){
			$this->db->insertObject('#__update_sites', $object);
			$update_site_id = $this->db->insertid();
		} else {
			$object->update_site_id = $update_site_id;
			$this->db->updateObject('#__update_sites', $object, 'update_site_id');
		}

		$query = 'SELECT extension_id FROM #__extensions WHERE `name` = \'hikamarket\' AND type = \'component\'';
		$this->db->setQuery($query);
		$extension_id = $this->db->loadResult();
		if(empty($update_site_id) || empty($extension_id))
			return false;

		$query = 'INSERT IGNORE INTO #__update_sites_extensions (update_site_id, extension_id) values ('.$update_site_id.','.$extension_id.')';
		$this->db->setQuery($query);
		$this->db->query();

		return true;
	}

	public function addDefaultData() {
		if(!HIKASHOP_J16) {
			$query = 'DELETE FROM `#__components` WHERE `admin_menu_link` LIKE \'%option=com\_hikamarket%\' AND `parent`!=0';
			$this->db->setQuery($query);
			$this->db->query();
			$query = 'SELECT id FROM `#__components` WHERE `option`=\'com_hikamarket\' AND `parent`=0';
			$this->db->setQuery($query);
			$parent = (int)$this->db->loadResult();
			$query  = "INSERT IGNORE INTO `#__components` (`admin_menu_link`,`admin_menu_img`,`admin_menu_alt`,`name`,`ordering`,`parent`) VALUES ".
				"('option=com_hikamarket&amp;ctrl=vendor','../includes/js/ThemeOffice/user.png','Vendors','Vendors',1,".$parent."),".
				"('option=com_hikamarket&amp;ctrl=plugins','../includes/js/ThemeOffice/plugin.png','Plugins','Plugins',2,".$parent."),
				('option=com_hikamarket&amp;ctrl=config','../includes/js/ThemeOffice/config.png','Configuration','Configuration',3,".$parent."),
				('option=com_hikamarket&amp;ctrl=documentation','../includes/js/ThemeOffice/help.png','Help','Help',4,".$parent."),
				('option=com_hikamarket&amp;ctrl=update','../includes/js/ThemeOffice/install.png','Update / About','Update / About',5,".$parent.");";
			$this->db->setQuery($query);
			$this->db->query();
		} else {
			$query = 'SELECT * FROM `#__menu` WHERE `title` IN (\'com_hikamarket\',\'hikamarket\',\'HikaMarket\') AND `client_id`=1 AND `parent_id`=1 AND menutype IN (\'main\',\'mainmenu\',\'menu\')';
			$this->db->setQuery($query);
			$parentData = $this->db->loadObject();
			$parent = $parentData->id;
			$query = 'SELECT id FROM `#__menu` WHERE `parent_id`='.$parent;
			$this->db->setQuery($query);
			$submenu = $this->db->loadColumn();
			$old=count($submenu);
			$query = 'DELETE FROM `#__menu` WHERE `parent_id`='.$parent;
			$this->db->setQuery($query);
			$this->db->query();
			$query = 'UPDATE `#__menu` SET `rgt`=`rgt`-'.($old*2).' WHERE `rgt`>='.$parentData->rgt;
			$this->db->setQuery($query);
			$this->db->query();
			$query = 'UPDATE `#__menu` SET `rgt`=`rgt`+10 WHERE `rgt`>='.$parentData->rgt;
			$this->db->setQuery($query);
			$this->db->query();
			$query = 'UPDATE `#__menu` SET `lft`=`lft`+10 WHERE `lft`>'.$parentData->lft;
			$this->db->setQuery($query);
			$this->db->query();
			$left = $parentData->lft;
			$cid = $parentData->component_id;
			$query  = "INSERT IGNORE INTO `#__menu` (`type`,`link`,`menutype`,`img`,`alias`,`title`,`client_id`,`parent_id`,`level`,`language`,`lft`,`rgt`,`component_id`) VALUES ".
				"('component','index.php?option=com_hikamarket&ctrl=vendor','menu','./templates/bluestork/images/menu/icon-16-user.png','Vendors','Vendors',1,".$parent.",2,'*',".($left+1).",".($left+2).",".$cid."),".
				"('component','index.php?option=com_hikamarket&ctrl=plugins','menu','./templates/bluestork/images/menu/icon-16-plugin.png','Plugins','Plugins',1,".$parent.",2,'*',".($left+3).",".($left+4).",".$cid."),
				('component','index.php?option=com_hikamarket&ctrl=config','menu','./templates/bluestork/images/menu/icon-16-config.png','Configuration','Configuration',1,".$parent.",2,'*',".($left+5).",".($left+6).",".$cid."),
				('component','index.php?option=com_hikamarket&ctrl=documentation','menu','./templates/bluestork/images/menu/icon-16-help.png','Help','Help',1,".$parent.",2,'*',".($left+7).",".($left+8).",".$cid."),
				('component','index.php?option=com_hikamarket&ctrl=update','menu','./templates/bluestork/images/menu/icon-16-help-jrd.png','Update / About','Update / About',1,".$parent.",2,'*',".($left+9).",".($left+10).",".$cid.");";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	public function onAfterCheckDB(&$ret) {
		$query = 'INSERT IGNORE INTO `'.hikamarket::table('customer_vendor').'` (customer_id, vendor_id) '.
			' SELECT DISTINCT order_user_id, order_vendor_id FROM `'.hikamarket::table('shop.order').'` AS o '.
			' WHERE o.order_type = ' . $this->db->Quote('subsale') . ' AND o.order_vendor_id > 1';
		try {
			$this->db->setQuery($query);
			$result = $this->db->query();
			if($result){
				$ret[] = array(
					'success',
					'Customer vendor synchronized'
				);
			}
		} catch(Exception $e) {
			$ret[] = array(
				'error',
				$e->getMessage()
			);
		}
	}
}
