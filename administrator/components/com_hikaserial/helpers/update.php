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
class hikaserialUpdateHelper {

	private $db;

	public function __construct() {
		$this->db = JFactory::getDBO();
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$this->update = JRequest::getBool('update');
	}

	public function addDefaultModules() {
	}

	public function installExtensions() {
		$path = HIKASERIAL_BACK.'extensions';
		$dirs = JFolder::folders($path);

		if(!HIKASHOP_J16) {
			$query = 'SELECT CONCAT(`folder`,`element`) FROM `#__plugins` WHERE `folder` IN '.
					"( 'hikashop','hikaserial' ) ".
					"OR `element` LIKE '%hikaserial%' ".
					"UNION SELECT `module` FROM `#__modules` WHERE `module` LIKE '%hikaserial%'";
		} else {
			$query = 'SELECT CONCAT(`folder`,`element`) FROM `#__extensions` WHERE `folder` IN '.
					"( 'hikashop','hikaserial' ) ".
					"OR `element` LIKE '%hikaserial%' ";
		}
		$this->db->setQuery($query);
		if(!HIKASHOP_J25) {
			$existingExtensions = $this->db->loadResultArray();
		} else {
			$existingExtensions = $this->db->loadColumn();
		}

		$success = array();
		$plugins = array();
		$modules = array();

		$exts = array(
			'plg_hikashop_serial' => array('HikaShop HikaSerial bridge plugin', 0, 1),
			'plg_hikaserial_randomgen' => array('Random - HikaSerial Generator plugin', 1, 1),
			'plg_hikaserial_coupongen' => array('Coupon - HikaSerial Generator plugin', 2, 1),
			'plg_hikaserial_seriesgen' => array('Series - HikaSerial Generator plugin', 3, 0),
			'plg_hikaserial_secureebook' => array('Secure eBook - HikaSerial Generator plugin', 4, 0),
			'plg_hikaserial_attachserial' => array('AttachSerial - HikaSerial plugin', 5, 0),
			'plg_hikaserial_groupconsumer' => array('GroupAssociation - HikaSerial Consumer plugin', 10, 0),
			'plg_hikaserial_productaddconsumer' => array('Product-add consumer', 10, 0),
			'plg_hikaserial_groupfilterconsumer' => array('Group filter consumer', 11, 0),
			'plg_hikashop_productaddcheck' => array('HikaShop serial product-add check', 10, 0),
			'mod_hikaserial_consume' => array('HikaSerial consume module', 10, 1),
			'plg_acymailing_hikaserial' => array('AcyMailing Tag : HikaSerial content', 20, 1)
		);

		$listTables = $this->db->getTableList();
		$this->errors = array();
		foreach($dirs as $dir) {
			$arguments = explode('_', $dir);
			$report = true;
			if(!empty($exts[$dir][3])) {
				$report = false;
			}
			$prefix = array_shift($arguments);

			if($prefix != 'plg' && $prefix != 'mod') {
				hikaserial::display('Could not handle : '.$dir, 'error');
				continue;
			}

			$newExt = new stdClass();
			$newExt->enabled = 1;
			$newExt->params = '{}';
			$newExt->name = isset($exts[$dir][0])?$exts[$dir][0]:$dir;
			$newExt->ordering = isset($exts[$dir][1])?$exts[$dir][1]:0;

			if(!isset($exts[$dir])) {
				$xmlFile = $path.DS.$dir.DS.$arguments[1].'.xml';
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
						if(!empty($xml->hikainstall)){
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

				if(!hikaserial::createDir(HIKASERIAL_ROOT.'plugins'.DS.$newExt->folder, $report))
					continue;

				if(!HIKASHOP_J16) {
					$destinationFolder = HIKASERIAL_ROOT.'plugins'.DS.$newExt->folder;
				} else {
					$destinationFolder = HIKASERIAL_ROOT.'plugins'.DS.$newExt->folder.DS.$newExt->element;
					if(!hikaserial::createDir($destinationFolder))
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

				$destinationFolder = HIKASERIAL_ROOT.'modules'.DS.$dir;

				if(!hikaserial::createDir($destinationFolder))
					continue;

				if(!$this->copyFolder($path.DS.$dir, $destinationFolder))
					continue;

				if(in_array($newExt->element, $existingExtensions))
					continue;

				$modules[] = $newExt;
			}
		}

		if(!empty($this->errors))
			hikaserial::display($this->errors, 'error');

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
				if(!!HIKASHOP_J16) {
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
			hikaserial::display('<ul><li>'.implode('</li><li>', $success).'</li></ul>', 'success');
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
		}
		$allFolders = JFolder::folders($from);
		if(!empty($allFolders)) {
			foreach($allFolders as $oneFolder) {
				if(!hikaserial::createDir($to.DS.$oneFolder))
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
		$path = JLanguage::getLanguagePath(JPATH_ROOT).DS.$code.DS.$code.'.'.HIKASERIAL_COMPONENT.'.ini';
		if(!file_exists($path))
			return;
		$content = file_get_contents($path);
		if(empty($content))
			return;

		$menuFileContent = strtoupper(HIKASERIAL_COMPONENT).'="'.HIKASERIAL_NAME.'"'."\r\n".strtoupper(HIKASERIAL_NAME).'="'.HIKASERIAL_NAME.'"'."\r\n";
		$menuStrings = array('SERIALS','PACKS','PLUGINS','HELP','UPDATE_ABOUT');
		foreach($menuStrings as $s) {
			preg_match('#(\n|\r)(HIKA_)?'.$s.'="(.*)"#i',$content,$matches);
			if(empty($matches[3]))
				continue;
			if(!HIKASHOP_J16) {
				$menuFileContent .= strtoupper(HIKASERIAL_COMPONENT).'.'.$s.'="'.$matches[3].'"'."\r\n";
			} else {
				$menuFileContent .= $s.'="'.$matches[3].'"'."\r\n";
			}
		}

		if(!HIKASHOP_J16) {
			$menuPath = HIKASERIAL_ROOT.'administrator'.DS.'language'.DS.$code.DS.$code.'.'.HIKASERIAL_COMPONENT.'.menu.ini';
		} else {
			$menuPath = HIKASERIAL_ROOT.'administrator'.DS.'language'.DS.$code.DS.$code.'.'.HIKASERIAL_COMPONENT.'.sys.ini';
		}
		if(!JFile::write($menuPath, $menuFileContent)) {
			hikaserial::display(JText::sprintf('FAIL_SAVE',$menuPath),'error');
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
		$controller = new hikaserialBridgeController(array(
			'base_path'=> HIKASERIAL_ROOT.'administrator'.DS.'components'.DS.'com_installer',
			'name'=>'Installer',
			'default_task' => 'installform'
		));
		$model = $controller->getModel('Install');
		return $model->install();
	}

	public function addUpdateSite() {
		$config = hikaserial::config();
		$newconfig = new stdClass();
		$newconfig->website = HIKASHOP_LIVE;
		$config->save($newconfig);

		if(!HIKASHOP_J16)
			return false;

		$query = 'SELECT update_site_id FROM #__update_sites WHERE location LIKE \'%hikaserial%\' AND type = \'extension\'';
		$this->db->setQuery($query);
		$update_site_id = $this->db->loadResult();

		$object = new stdClass();
		$object->name = 'HikaSerial';
		$object->type = 'extension';
		$object->enabled = 1;
		$object->location = 'http://www.hikashop.com/component/updateme/updatexml/component-hikaserial/version-'.$config->get('version').'/level-'.$config->get('level').'/li-'.urlencode(base64_encode(HIKASHOP_LIVE)).'/file-extension.xml';

		if(empty($update_site_id)){
			$this->db->insertObject('#__update_sites', $object);
			$update_site_id = $this->db->insertid();
		} else {
			$object->update_site_id = $update_site_id;
			$this->db->updateObject('#__update_sites', $object, 'update_site_id');
		}

		$query = 'SELECT extension_id FROM #__extensions WHERE `name` = \'hikaserial\' AND type LIKE \'component\'';
		$this->db->setQuery($query);
		$extension_id = $this->db->loadResult();
		if(empty($update_site_id) || empty($extension_id))
			return false;

		$query = 'INSERT IGNORE INTO #__update_sites_extensions (update_site_id, extension_id) values ('.$update_site_id.','.$extension_id.')';
		$this->db->setQuery($query);
		$this->db->query();

		return true;
	}

	public function getUrl() {
		$urls = parse_url(HIKASERIAL_LIVE);
		$lurl = preg_replace('#^www2?\.#Ui', '', $urls['host'], 1);
		if(!empty($urls['path']))
			$lurl .= $urls['path'];
		return strtolower(rtrim($lurl, '/'));
	}

	public function addJoomfishElements() {
		$dstFolder = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_joomfish'.DS.'contentelements'.DS;
		if(JFolder::exists($dstFolder)) {
			$srcFolder = HIKASERIAL_BACK.'translations'.DS;
			$files = JFolder::files($srcFolder);
			if(!empty($files)) {
				foreach($files as $file) {
					JFile::copy($srcFolder.$file,$dstFolder.$file);
				}
			}
		}
		return true;
	}

	public function addDefaultData() {
		if(!HIKASHOP_J16) {
			$query = 'DELETE FROM `#__components` WHERE `admin_menu_link` LIKE \'%option=com\_hikaserial%\' AND `parent`!=0';
			$this->db->setQuery($query);
			$this->db->query();
			$query = 'SELECT id FROM `#__components` WHERE `option`=\'com_hikaserial\' AND `parent`=0';
			$this->db->setQuery($query);
			$parent = (int)$this->db->loadResult();
			$query  = "INSERT IGNORE INTO `#__components` (`admin_menu_link`,`admin_menu_img`,`admin_menu_alt`,`name`,`ordering`,`parent`) VALUES
				('option=com_hikaserial&amp;ctrl=serial','../includes/js/ThemeOffice/document.png','Serials','Serials',1,".$parent."),
				('option=com_hikaserial&amp;ctrl=pack','../includes/js/ThemeOffice/sections.png','Packs','Packs',2,".$parent."),
				('option=com_hikaserial&amp;ctrl=plugins','../includes/js/ThemeOffice/plugin.png','Plugins','Plugins',3,".$parent."),
				('option=com_hikaserial&amp;ctrl=config','../includes/js/ThemeOffice/config.png','Configuration','Configuration',4,".$parent."),
				('option=com_hikaserial&amp;ctrl=documentation','../includes/js/ThemeOffice/help.png','Help','Help',5,".$parent."),
				('option=com_hikaserial&amp;ctrl=update','../includes/js/ThemeOffice/install.png','Update / About','Update / About',6,".$parent.");";
			$this->db->setQuery($query);
			$this->db->query();
		} else {
			$query = 'SELECT * FROM `#__menu` WHERE `title` IN (\'com_hikaserial\',\'hikaserial\',\'HikaSerial\') AND `client_id`=1 AND `parent_id`=1 AND menutype IN (\'main\',\'mainmenu\',\'menu\')';
			$this->db->setQuery($query);
			$parentData = $this->db->loadObject();
			$parent = $parentData->id;
			$query = 'SELECT id FROM `#__menu` WHERE `parent_id`='.$parent;
			$this->db->setQuery($query);
			if(!HIKASHOP_J25) {
				$submenu = $this->db->loadResultArray();
			} else {
				$submenu = $this->db->loadColumn();
			}
			$old=count($submenu);
			$query = 'DELETE FROM `#__menu` WHERE `parent_id`='.$parent;
			$this->db->setQuery($query);
			$this->db->query();
			$query = 'UPDATE `#__menu` SET `rgt`=`rgt`-'.($old*2).' WHERE `rgt`>='.$parentData->rgt;
			$this->db->setQuery($query);
			$this->db->query();
			$query = 'UPDATE `#__menu` SET `rgt`=`rgt`+12 WHERE `rgt`>='.$parentData->rgt;
			$this->db->setQuery($query);
			$this->db->query();
			$query = 'UPDATE `#__menu` SET `lft`=`lft`+12 WHERE `lft`>'.$parentData->lft;
			$this->db->setQuery($query);
			$this->db->query();
			$left = $parentData->lft;
			$cid = $parentData->component_id;
			$query  = "INSERT IGNORE INTO `#__menu` (`type`,`link`,`menutype`,`img`,`alias`,`title`,`client_id`,`parent_id`,`level`,`language`,`lft`,`rgt`,`component_id`) VALUES
				('component','index.php?option=com_hikaserial&ctrl=serial','menu','./templates/bluestork/images/menu/icon-16-article.png','Serials','Serials',1,".$parent.",2,'*',".($left+1).",".($left+2).",".$cid."),
				('component','index.php?option=com_hikaserial&ctrl=pack','menu','./templates/bluestork/images/menu/icon-16-category.png','Packs','Packs',1,".$parent.",2,'*',".($left+3).",".($left+4).",".$cid."),
				('component','index.php?option=com_hikaserial&ctrl=plugins','menu','./templates/bluestork/images/menu/icon-16-plugin.png','Plugins','Plugins',1,".$parent.",2,'*',".($left+5).",".($left+6).",".$cid."),
				('component','index.php?option=com_hikaserial&ctrl=config','menu','./templates/bluestork/images/menu/icon-16-config.png','Configuration','Configuration',1,".$parent.",2,'*',".($left+7).",".($left+8).",".$cid."),
				('component','index.php?option=com_hikaserial&ctrl=documentation','menu','./templates/bluestork/images/menu/icon-16-help.png','Help','Help',1,".$parent.",2,'*',".($left+9).",".($left+10).",".$cid."),
				('component','index.php?option=com_hikaserial&ctrl=update','menu','./templates/bluestork/images/menu/icon-16-help-jrd.png','Update / About','Update / About',1,".$parent.",2,'*',".($left+11).",".($left+12).",".$cid.");";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
