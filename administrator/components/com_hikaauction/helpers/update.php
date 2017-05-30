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
class hikaauctionUpdateHelper {

	private $db;
	private $dbHelper;

	public function __construct() {
		$this->dbHelper = hikaauction::get('helper.database');
		$this->db = $this->dbHelper->get();

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$this->update = JRequest::getBool('update');
	}

	public function addDefaultModules() {
	}

	public function createUploadFolders() {
		$file = hikaauction::get('shop.class.file');
		$path = $file->getPath('file');
		if(!JFile::exists($path.'.htaccess')) {
			$text = 'deny from all';
			JFile::write($path.'.htaccess', $text);
		}
		$path = $file->getPath('image');
	}

	public function installExtensions() {
		$path = HIKAAUCTION_BACK.'extensions';
		if(!is_dir($path))
			return;
		$dirs = JFolder::folders($path);

		if(!HIKAAUCTION_J16) {
			$query = 'SELECT CONCAT(`folder`,`element`) FROM `#__plugins` WHERE `folder` IN '.
					"( 'hikaauction', 'hikashop' ) ".
					"OR `element` LIKE '%hikaauction%' ".
					"UNION SELECT `module` FROM `#__modules` WHERE `module` LIKE '%hikaauction%'";
		} else {
			$query = $this->dbHelper->getQuery(true);
			$query->select( $query->concatenate(array('folder','element')))
				->from('#__extensions')
				->where('folder IN (' . $query->quote('hikaauction') . ', ' . $query->quote('hikashop') . ') OR element LIKE \'%'.$query->escape('hikaauction').'%\'');
		}
		$this->db->setQuery($query);
		if(!HIKAAUCTION_J25) {
			$existingExtensions = $this->db->loadResultArray();
		} else {
			$existingExtensions = $this->db->loadColumn();
		}

		$success = array();
		$plugins = array();
		$modules = array();

		$exts = array(
			'plg_hikashop_auction' => array('HikaAuction - HikaShop Integration plugin', 0, 1),
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
				hikaauction::display('Could not handle : '.$dir, 'error');
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
				if(!HIKAAUCTION_J16) {
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

				if(!hikaauction::createDir(HIKAAUCTION_ROOT.'plugins'.DS.$newExt->folder, $report))
					continue;

				if(!HIKAAUCTION_J16) {
					$destinationFolder = HIKAAUCTION_ROOT.'plugins'.DS.$newExt->folder;
				} else {
					$destinationFolder = HIKAAUCTION_ROOT.'plugins'.DS.$newExt->folder.DS.$newExt->element;
					if(!hikaauction::createDir($destinationFolder))
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

				$destinationFolder = HIKAAUCTION_ROOT.'modules'.DS.$dir;

				if(!hikaauction::createDir($destinationFolder))
					continue;

				if(!$this->copyFolder($path.DS.$dir, $destinationFolder))
					continue;

				if(in_array($newExt->element, $existingExtensions))
					continue;

				$modules[] = $newExt;
			}
		}

		if(!empty($this->errors))
			hikaauction::display($this->errors, 'error');

		if( empty($plugins) && empty($modules) ) {
			return;
		}

		if(!HIKAAUCTION_J16) {
			$extensions =& $plugins;
		} else {
			$extensions = array_merge($plugins, $modules);
		}

		$success = array();
		if(!empty($extensions)) {
			$query = $this->dbHelper->getQuery(true);
			if(!HIKAAUCTION_J16) {
				$query->insert('#__plugins')->columns(array('name','element','folder','published','ordering'));
			} else {
				$query->insert('#__extensions')->columns(array('name','element','folder','enabled','ordering','type','access'));
			}

			foreach($extensions as $oneExt) {
				$values = array($oneExt->name, $oneExt->element, $oneExt->folder, $oneExt->enabled, $oneExt->ordering);
				if(!!HIKAAUCTION_J16) {
					$values[] = $oneExt->type;
					$values[] = 1;
				}
				$query->values($this->dbHelper->implode($values));
				if($oneExt->type != 'module') {
					$success[] = JText::sprintf('PLUG_INSTALLED', $oneExt->name);
				}
			}

			$this->db->setQuery($query);
			$this->db->query();
		}

		if(!empty($modules)) {
			foreach($modules as $oneModule) {
				$query = $this->dbHelper->getQuery(true);
				$query->insert('#__modules');
				if(!HIKAAUCTION_J16) {
					$query->columns(array('title','position','published','module'))
						->values($this->dbHelper->implode(array($oneModule->name,'left',0,$oneModule->element)));
				} else {
					$query->columns(array('title','position','published','module','access','language'))
						->values($this->dbHelper->implode(array($oneModule->name,'position-7',0,$oneModule->element,1,'*')));
				}
				$this->db->setQuery($query);
				$this->db->query();
				$moduleId = $this->db->insertid();

				$query = $this->dbHelper->getQuery(true);
				$query->insert('#__modules_menu')
					->columns(array('moduleid','menuid'))
					->values($moduleId.',0');
				$this->db->setQuery($query);
				$this->db->query();

				$success[] = JText::sprintf('MODULE_INSTALLED', $oneModule->name);
			}
		}

		if(!empty($success)) {
			hikaauction::display('<ul><li>'.implode('</li><li>', $success).'</li></ul>', 'success');
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
			if(!HIKAAUCTION_J16 && substr($oneFile,-4) == '.xml') {
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
				if(!hikaauction::createDir($to.DS.$oneFolder))
					continue;
				if(!$this->copyFolder($from.DS.$oneFolder,$to.DS.$oneFolder))
					$ret = false;
			}
		}
		return $ret;
	}

	public function addUpdateSite(){
		$config = hikaauction::config();
		$newconfig = new stdClass();
		$newconfig->website = HIKAAUCTION_LIVE;
		$config->save($newconfig);

		if(!HIKAAUCTION_J16)
			return false;
		$query = $this->dbHelper->getQuery(true);
		$query->select('update_site_id')->from('#__update_sites')->where('location LIKE \'%hikaauction%\' AND type = \'extension\'');
		$this->db->setQuery($query);
		$update_site_id = $this->db->loadResult();

		$object = new stdClass();
		$object->name = 'HikaAuction';
		$object->type = 'extension';
		$object->enabled = 1;
		$object->location = 'http://www.hikashop.com/component/updateme/updatexml/component-hikaauction/version-'.$config->get('version').'/level-'.$config->get('level').'/li-'.urlencode(base64_encode(HIKAAUCTION_LIVE)).'/file-extension.xml';

		if(empty($update_site_id)){
			$this->db->insertObject('#__update_sites', $object);
			$update_site_id = $this->db->insertid();
		} else {
			$object->update_site_id = $update_site_id;
			$this->db->updateObject('#__update_sites', $object, 'update_site_id');
		}

		$query = $this->dbHelper->getQuery(true);
		$query->select('extension_id')
			->from('#__extensions')
			->where('name = \'hikaauction\' AND type = \'component\'');
		$this->db->setQuery($query);
		$extension_id = $this->db->loadResult();
		if(empty($update_site_id) || empty($extension_id))
			return false;

		$query = $this->dbHelper->getQuery(true);
		$query->select('count(*)')->from('#__update_sites_extensions')->where('update_site_id = ' . $update_site_id . ' AND extension_id = ' . $extension_id);
		$this->db->setQuery($query);
		$nb = $this->db->loadResult();

		if(empty($nb)) {
			$query = $this->dbHelper->getQuery(true);
			$query->insert('#__update_sites_extensions')
				->columns(array('update_site_id', 'extension_id'))
				->values($this->dbHelper->implode(array($update_site_id,$extension_id)));
			$this->db->setQuery($query);
			$this->db->query();
		}
		return true;
	}

	public function installMenu($code = '') {
		if(empty($code)) {
			$lang = JFactory::getLanguage();
			$code = $lang->getTag();
		}
		$path = JLanguage::getLanguagePath(JPATH_ROOT).DS.$code.DS.$code.'.'.HIKAAUCTION_COMPONENT.'.ini';
		if(!file_exists($path))
			return;
		$content = file_get_contents($path);
		if(empty($content))
			return;

		$menuFileContent = strtoupper(HIKAAUCTION_COMPONENT).'="'.HIKAAUCTION_NAME.'"'."\r\n".strtoupper(HIKAAUCTION_NAME).'="'.HIKAAUCTION_NAME.'"'."\r\n";
		$menuStrings = array('HELP','AUCTIONS','UPDATE_ABOUT');
		foreach($menuStrings as $s) {
			preg_match('#(\n|\r)(HIKA_)?'.$s.'="(.*)"#i',$content,$matches);
			if(empty($matches[3]))
				continue;
			if(!HIKAAUCTION_J16) {
				$menuFileContent .= strtoupper(HIKAAUCTION_COMPONENT).'.'.$s.'="'.$matches[3].'"'."\r\n";
			} else {
				$menuFileContent .= $s.'="'.$matches[3].'"'."\r\n";
			}
		}

		if(!HIKAAUCTION_J16) {
			$menuPath = HIKAAUCTION_ROOT.'administrator'.DS.'language'.DS.$code.DS.$code.'.'.HIKAAUCTION_COMPONENT.'.menu.ini';
		} else {
			$menuPath = HIKAAUCTION_ROOT.'administrator'.DS.'language'.DS.$code.DS.$code.'.'.HIKAAUCTION_COMPONENT.'.sys.ini';
		}
		if(!JFile::write($menuPath, $menuFileContent)) {
			hikaauction::display(JText::sprintf('FAIL_SAVE',$menuPath),'error');
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
		$controller = new hikaauctionBridgeController(array(
			'base_path'=> HIKAAUCTION_ROOT.'administrator'.DS.'components'.DS.'com_installer',
			'name'=>'Installer',
			'default_task' => 'installform'
		));
		$model = $controller->getModel('Install');
		return $model->install();
	}

	public function getUrl() {
		$urls = parse_url(HIKAAUCTION_LIVE);
		$lurl = preg_replace('#^www2?\.#Ui', '', $urls['host'], 1);
		if(!empty($urls['path']))
			$lurl .= $urls['path'];
		return strtolower(rtrim($lurl, '/'));
	}

	public function addJoomfishElements() {
		$dstFolder = rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_joomfish'.DS.'contentelements'.DS;
		if(JFolder::exists($dstFolder)) {
			$srcFolder = HIKAAUCTION_BACK.'translations'.DS;
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

			$element = new stdClass();

			$element->menutype = 'hikashop_default';
			$element->link = 'index.php?option=com_hikaauction&view=productauction&layout=bid';
			$element->title = JText::_('COM_HIKAAUCTION_PRODUCT_BID');
			$element->alias = 'hikashop-menu-for-auction-bid';

			$element->type = 'component';
			$element->published = 1;

			if(version_compare(JVERSION,'1.6','<')){
				$element->name = $element->title;
				$element->parent = 0;
				$element->sublevel = 1;
				$element->access = 0;
				unset($element->title);
			}else{
				$element->path = $element->alias;
				$element->client_id = 0;
				$element->language = '*';
				$element->level = 1;
				$element->parent_id = 1;
				$element->access = 1;
			}

			$this->db->setQuery('SELECT menutype FROM '.hikashop_table('menu_types',false).' WHERE menutype=\'hikashop_default\'');
			$mainMenu = $this->db->loadResult();
			if(empty($mainMenu)){
				$this->db->setQuery('INSERT INTO '.hikashop_table('menu_types',false).' ( `menutype`,`title`,`description` ) VALUES ( \'hikashop_default\',\'HikaShop default menus\',\'This menu is used by HikaShop to store menus configurations\' )');
				$this->db->query();
			}

			if(version_compare(JVERSION,'1.5','>')){
				$this->db->setQuery('SELECT rgt FROM '.hikashop_table('menu',false).' WHERE id=1');
				$root = $this->db->loadResult();
				$element->lft = $root;
				$element->rgt = $root+1;
				$this->db->setQuery('UPDATE '.hikashop_table('menu',false).' SET rgt='.($root+2).' WHERE id=1');
				$this->db->query();
			}

			$this->db->setQuery('SELECT id FROM '.hikashop_table('menu',false).' WHERE link=\'index.php?option=com_hikaauction&view=productauction&layout=bid\'');
			$bid_itemid = $this->db->loadResult();
			if(empty($bid_itemid)){
				$menuClass = hikaauction::get('class.menu');
				$menuId = $menuClass->save($element);
			}

	}
}
