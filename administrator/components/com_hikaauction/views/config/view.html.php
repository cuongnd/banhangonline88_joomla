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
class configViewConfig extends hikaauctionView {

	const ctrl = 'config';
	const name = 'CONFIG';
	const icon = 'config';

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKAAUCTION_COMPONENT.'.'.$this->getName();
		$function = $this->getLayout();
		if(method_exists($this,$function))
			$this->$function();
		parent::display($tpl);
	}

	public function config($tpl = null) {
		hikaauction::setTitle(JText::_(self::name), self::icon, self::ctrl);

		$config = hikaauction::config();
		$this->assignRef('config', $config);

		$this->loadRef(array(
			'popup' => 'shop.helper.popup',
			'dealmodeType' => 'type.dealmode',
			'pricemodeType' => 'type.pricemode',
			'orderstatusType' => 'shop.type.order_status',
		));

		$manage = hikaauction::isAllowed($config->get('acl_config_manage', 'all'));
		$this->assignRef('manage', $manage);

		$toggleHelper = hikaauction::get('helper.toggle');

		$languages = array();
		$lg = JFactory::getLanguage();
		$language = $lg->getTag();
		$styleRemind = 'float:right;margin-right:30px;position:relative;';
		$loadLink = $this->popup->display(
			JText::_('LOAD_LATEST_LANGUAGE'),
			'EDIT_LANGUAGE_FILE',
			hikaauction::completeLink('config&task=latest&code=' . $language, true),
			'loadlatest_language_'.$language,
			array('width' => 800, 'height' => 500, 'attr' => 'onclick="window.document.getElementById(\'hikaauction_messages_warning\').style.display = \'none\';"', 'type' => 'link')
		);
		if(!file_exists(HIKAAUCTION_ROOT . 'language' . DS . $language . DS . $language . '.' . HIKAAUCTION_COMPONENT . '.ini')) {
			if($config->get('errorlanguagemissing', 1)) {
				$noteremind = '<small style="' . $styleRemind . '">' . $toggleHelper->delete('hikaauction_messages_warning', 'errorlanguagemissing-0', 'config', false, JText::_('DONT_REMIND')) . '</small>';
				hikaauction::display(JText::_('MISSING_LANGUAGE') . ' ' . $loadLink . ' ' . $noteremind, 'warning');
			}
		}
		$edit_image = HIKAAUCTION_IMAGES.'icon-16/edit.png';
		$new_image = HIKAAUCTION_IMAGES.'icon-16/plus.png';

		jimport('joomla.filesystem.folder');
		$path = JLanguage::getLanguagePath(JPATH_ROOT);
		$dirs = JFolder::folders($path);
		foreach($dirs as $dir) {
			$xmlFiles = JFolder::files($path . DS . $dir, '^([-_A-Za-z]*)\.xml$');
			$xmlFile = array_pop($xmlFiles);
			if($xmlFile == 'install.xml')
				$xmlFile = array_pop($xmlFiles);
			if(empty($xmlFile))
				continue;
			$data = JApplicationHelper::parseXMLLangMetaFile($path . DS . $dir . DS . $xmlFile);
			$oneLanguage = new stdClass();
			$oneLanguage->language 	= $dir;
			$oneLanguage->name = $data['name'];
			$languageFiles = JFolder::files($path . DS . $dir, '^(.*)\.' . HIKAAUCTION_COMPONENT . '\.ini$' );
			$languageFile = reset($languageFiles);

			$linkEdit = hikaauction::completeLink('config&task=language&code='.$oneLanguage->language, true, false, false);
			if(!empty($languageFile)){
				$oneLanguage->edit = $this->popup->display(
					'<img id="image' . $oneLanguage->language . '" src="' . $edit_image . '" alt="' . JText::_('EDIT_LANGUAGE_FILE', true) . '"/>',
					'EDIT_LANGUAGE_FILE',
					$linkEdit,
					'edit_language_'.$oneLanguage->language,
					array('width' => 800, 'height' => 500, 'type' => 'link')
				);
			} else {
				$oneLanguage->edit = $this->popup->display(
					'<img id="image' . $oneLanguage->language . '" src="' . $new_image . '" alt="' . JText::_('ADD_LANGUAGE_FILE', true) . '"/>',
					'ADD_LANGUAGE_FILE',
					$linkEdit,
					'edit_language_'.$oneLanguage->language,
					array('width' => 800, 'height' => 500, 'type' => 'link')
				);
			}

			$languages[] = $oneLanguage;
		}
		$this->assignRef('languages', $languages);

		$this->toolbar = array(
			'|',
			'save',
			'apply',
			'hikacancel',
			'|',
			array('name' => 'pophelp', 'target' => 'config'),
			'dashboard'
		);
	}

	public function sql($tpl = null) {
		hikaauction::setTitle(JText::_('HIKA_CONFIGURATION_SQL'), self::icon, self::ctrl);

		$config = hikaauction::config();
		$this->assignRef('config', $config);

		$toolbar = JToolBar::getInstance('toolbar');

		$sql_data = JRequest::getVar('sql_data', '', '', 'string', JREQUEST_ALLOWRAW);
		$this->assignRef('sql_data', $sql_data);

		$user = JFactory::getUser();
		$iAmSuperAdmin = false;
		if(!HIKAAUCTION_J16) {
			$iAmSuperAdmin = ($user->get('gid') == 25);
		} else {
			$iAmSuperAdmin = $user->authorise('core.admin');
		}

		$query_result = '';
		if(!empty($sql_data) && $iAmSuperAdmin) {
			$p = strpos($sql_data, ' ');
			if($p) {
				$db = JFactory::getDBO();
				$word = strtolower(substr($sql_data, 0, $p));
				if(in_array($word, array('insert', 'update', 'delete'))) {
					$db->setQuery($sql_data);
					$db->query();
					$query_result = JText::_('HIKA_X_ROWS_AFFECTED', $db->getAffectedRows());
				} else if($word == 'select') {
					$db->setQuery($sql_data);
					$query_result = $db->loadObjectList();
				} else if(in_array($word, array('create', 'drop', 'alter'))) {
					$db->setQuery($sql_data);
					if( $db->query() ) {
						$query_result = JText::_('HIKA_QUERY_SUCCESS');
					} else {
						$query_result = JText::_('HIKA_QUERY_FAILURE');
					}
				}
			}
		}
		$this->assignRef('query_result', $query_result);

		$this->toolbar = array(
			'|',
			array('name' => 'custom', 'icon' => 'apply', 'alt' => JText::_('APPLY'), 'task' => 'sql', 'check' => false),
			'hikacancel',
			'|',
			array('name' => 'pophelp', 'target' => 'config'),
			'dashboard'
		);
	}

	public function language() {
		$code = JRequest::getString('code');
		if(empty($code)) {
			hikaauction::display('Code not specified','error');
			return;
		}

		jimport('joomla.filesystem.file');
		$path = JLanguage::getLanguagePath(JPATH_ROOT) . DS . $code . DS . $code . '.' . HIKAAUCTION_COMPONENT . '.ini';
		$file = new stdClass();
		$file->name = $code;
		$file->path = $path;
		if(JFile::exists($path)) {
			$file->content = JFile::read($path);
			if(empty($file->content)) {
				hikaauction::display('File not found : '.$path,'error');
			}
		} else {
			hikaauction::display(JText::_('LOAD_ENGLISH_1') . '<br/>' . JText::_('LOAD_ENGLISH_2') . '<br/>' . JText::_('LOAD_ENGLISH_3'), 'info');
			$file->content = JFile::read(JLanguage::getLanguagePath(JPATH_ROOT) . DS . 'en-GB' . DS . 'en-GB.' . HIKAAUCTION_COMPONENT . '.ini');
		}
		$override_content = '';
		$override_path = JLanguage::getLanguagePath(JPATH_ROOT) . DS . 'overrides' . DS . $code . '.override.ini';
		if(JFile::exists($override_path)) {
			$override_content = JFile::read($override_path);
		}
		$this->assignRef('override_content', $override_content);
		$this->assignRef('showLatest', $showLatest);
		$this->assignRef('file', $file);
	}

	public function share(){
		$file = new stdClass();
		$file->name = JRequest::getString('code');
		$this->assignRef('file',$file);
	}

	public function debug() {
		hikaauction::setTitle(JText::_('DEBUG'), self::icon, self::ctrl);
		$this->toolbar = array('dashboard');

		$config = hikaauction::config();
		$this->assignRef('config', $config);

		$dbHelper = hikaauction::get('helper.database');
		$this->assignRef('dbHelper', $dbHelper);

		$db = $dbHelper->get();
		$this->assignRef('db', $db);
	}
}
