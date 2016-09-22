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
class configMarketController extends hikamarketController {

	protected $rights = array(
		'display' => array('display','config','cancel','listing','sql','language','css','acl'),
		'add' => array(),
		'edit' => array('toggle','delete'),
		'modify' => array('save','apply','share','send','savelanguage','savecss','saveacl','applyacl'),
		'delete' => array()
	);

	public function __construct($config = array())	{
		parent::__construct($config);
		$this->registerDefaultTask('config');
	}

	public function display($tpl = null, $params = null) {
		JRequest::setVar('layout', 'config');
		return parent::display();
	}

	public function cancel(){
		$this->setRedirect( hikamarket::completeLink('dashboard',false,true) );
	}

	public function save() {
		$this->store();
		return $this->cancel();
	}

	public function apply() {
		$this->store();
		return $this->display();
	}

	public function store() {
		JRequest::checkToken() || die('Invalid Token');
		$app = JFactory::getApplication();
		$config = hikamarket::config();

		$formData = JRequest::getVar('config', array(), 'POST', 'array');

		if(isset($formData['store_default_access'])) {
			if($formData['store_default_access'] == 'none') {
				$formData['store_default_access'] = '';
			} else if($formData['store_default_access'] == 'all' || $formData['store_default_access'] == '*') {
				$formData['store_default_access'] = '*';
			} else {
				$marketaclType = hikamarket::get('type.market_acl');
				if(strpos($formData['store_default_access'], '/') === false)
					$formData['store_default_access'] = str_replace('_', '/', $formData['store_default_access']);
				$formData['store_default_access'] = $marketaclType->compile(explode(',', $formData['store_default_access']));
			}
		}

		$status = $config->save($formData);

		JRequest::setVar('vendor_id', 1);
		$vendorClass = hikamarket::get('class.vendor');
		$vendorStatus = $vendorClass->saveForm();

		if(hikamarket::level(1)) {
			$vendor_fees = array();
			if(!empty($formData['vendor_fee']))
				$vendor_fees = $formData['vendor_fee'];
			$feeClass = hikamarket::get('class.fee');
			$feeStatus = $feeClass->saveConfig($vendor_fees);
		}

		if($status) {
			$app->enqueueMessage(JText::_('HIKASHOP_SUCC_SAVED'), 'message');
		} else {
			$app->enqueueMessage(JText::_('ERROR_SAVING'), 'error');
		}

		if(!$vendorStatus) {
			$app->enqueueMessage(JText::_('ERROR_SAVING_VENDOR'), 'error');
		}

		$config->load();
	}

	public function acl() {
		JRequest::setVar('layout', 'acl');
		return parent::display();
	}

	public function saveacl() {
		$status = $this->acl_save();
		if($status)
			JRequest::setVar('acl_type', null);
		return $this->acl();
	}

	public function applyacl() {
		$this->acl_save();
		return $this->acl();
	}

	private function acl_save() {
		JRequest::checkToken() || die('Invalid Token');
		$app = JFactory::getApplication();

		$aclClass = hikamarket::get('class.acl');
		$status = $aclClass->saveForm();

		if($status) {
			$app->enqueueMessage(JText::_('HIKASHOP_SUCC_SAVED'), 'message');
		} else {
			$app->enqueueMessage(JText::_('ERROR_SAVING'), 'error');
		}
		return $status;
	}

	public function sql() {
		$user = JFactory::getUser();
		$iAmSuperAdmin = false;
		if(!HIKASHOP_J16) {
			$iAmSuperAdmin = ($user->get('gid') == 25);
		} else {
			$iAmSuperAdmin = $user->authorise('core.admin');
		}
		JRequest::setVar('layout', 'sql');
		if(!$iAmSuperAdmin) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::sprintf('HIKA_SUPER_ADMIN_REQUIRE_FOR_TASK', 'sql'), 'error'); // _('HIKA_SUPER_ADMIN_REQUIRE_FOR_TASK')
			JRequest::setVar('layout', 'config');
		}
		return parent::display();
	}


	public function language() {
		JRequest::setVar('layout', 'language');
		return parent::display();
	}

	public function savelanguage() {
		JRequest::checkToken() || die('Invalid Token');
		$this->savelng();
		return $this->language();
	}

	public function share() {
		JRequest::checkToken() || die('Invalid Token');
		if($this->savelng()) {
			JRequest::setVar('layout', 'share');
			return parent::display();
		}
		return $this->language();
	}

	public function send() {
		JRequest::checkToken() || die('Invalid Token');

		$code = JRequest::getString('code');
		JRequest::setVar('code', $code);
		if(empty($code))
			return;

		$bodyEmail = JRequest::getString('mailbody');
		$true = true;

		$mailClass = hikamarket::get('shop.class.mail');
		$shopConfig = hikamarket::config(false);
		$config = hikamarket::config();
		$user = hikamarket::loadUser(true);

		$addedName = $shopConfig->get('add_names',true) ? $mailClass->cleanText(@$user->name) : '';
		$mail = $mailClass->get('language', $true);
		$mailClass->mailer->AddAddress($user->user_email, $addedName);
		$mailClass->mailer->AddAddress('translate-hikamarket@hikashop.com', 'HikaMarket Translation Team');

		$mail->subject = '[HIKAMARKET LANGUAGE FILE] ' . $code;
		$mail->altbody = 'The website '.HIKASHOP_LIVE.' using HikaMarket '.$config->get('level') . $config->get('version') . ' sent a language file : '.$code;
		$mail->altbody .= "\n\n\n" . $bodyEmail;
		$mail->html = 0;

		jimport('joomla.filesystem.file');
		$path = JPath::clean(JLanguage::getLanguagePath(JPATH_ROOT) . DS . $code . DS . $code . '.' . HIKAMARKET_COMPONENT . '.ini');
		$mailClass->mailer->AddAttachment($path);
		$result = $mailClass->sendMail($mail);

		if($result) {
			hikamarket::display(JText::_('THANK_YOU_SHARING'), 'success');
		}
	}

	private function savelng() {
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$code = JRequest::getString('code');
		JRequest::setVar('code', $code);
		$content = JRequest::getVar('content', '', '', 'string', JREQUEST_ALLOWRAW);
		if(empty($code))
			return;
		$content_override = JRequest::getVar('content_override', '', '', 'string', JREQUEST_ALLOWRAW);
		$folder = JLanguage::getLanguagePath(JPATH_ROOT) . DS . 'overrides';
		if(!JFolder::exists($folder)) {
			JFolder::create($folder);
		}
		if(JFolder::exists($folder)) {
			$path = $folder . DS . $code . '.override.ini';
			$result = JFile::write($path, $content_override);
			if(!$result) {
				hikamarket::display(JText::sprintf('FAIL_SAVE', $path), 'error');
			}
		}

		if(empty($content))
			return;
		$path = JLanguage::getLanguagePath(JPATH_ROOT) . DS . $code . DS . $code . '.' . HIKAMARKET_COMPONENT . '.ini';
		$result = JFile::write($path, $content);
		if($result) {
			hikamarket::display(JText::_('HIKASHOP_SUCC_SAVED'), 'success');
			$updateHelper = hikamarket::get('helper.update');
			$updateHelper->installMenu($code);
			$js = 'window.top.document.getElementById("image'.$code.'").src = "'.HIKASHOP_IMAGES.'icons/icon-16-edit.png"';
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		} else {
			hikamarket::display(JText::sprintf('FAIL_SAVE', $path), 'error');
		}
		return $result;
	}

	public function css() {
		JRequest::setVar('layout', 'css');
		return parent::display();
	}

	public function savecss() {
		JRequest::checkToken() || die('Invalid Token');

		$type = JRequest::getCmd('var', '');
		if(!in_array($type, array('frontend', 'backend', 'style'))) {
			hikamarket::display('Invalid content');
			exit;
		}

		$new = JRequest::getInt('new', 0);
		if(!$new) {
			$file = JRequest::getCmd('file');
			if(!preg_match('#^([-_a-z0-9]*)_([-_a-z0-9]*)$#i', $file, $result)) {
				hikamarket::display('Could not load the file ' . $file . ' properly', 'error');
				return false;
			}
			if($result[1] != $type) {
				hikamarket::display('Invalid content', 'error');
				return false;
			}
			$filename = $result[2];
		} else {
			$filename = JRequest::getCmd('filename');

			if(!preg_match('#^([-_a-z0-9]*)_([-_a-z0-9]*)$#i', $type . '_' . $filename, $result)) {
				hikamarket::display('Could not load the file ' . $type . '_' . $filename . ' properly', 'error');
				return false;
			}

			$path = HIKAMARKET_MEDIA . 'css' . DS . $type . '_' . $filename.'.css';
			if(file_exists($path)) {
				hikamarket::display('Invalid content: file &quot;'.$type . '_' . $filename.'&quot; already exists', 'error');
				return $this->css();
			}
		}

		jimport('joomla.filesystem.file');
		$path = HIKAMARKET_MEDIA . 'css' . DS . $type . '_' . $filename.'.css';
		$csscontent = JRequest::getString('csscontent');
		$alreadyExists = file_exists($path);
		if(JFile::write($path, $csscontent)) {
			$configName = 'css_' . $type;
			$config = hikamarket::config();
			$newConfig = new stdClass();
			$newConfig->$configName = $filename;
 			$config->save($newConfig);

			hikamarket::display(JText::_('HIKASHOP_SUCC_SAVED'));

			if(!$alreadyExists) {
				$js = '
var optn = document.createElement("OPTION");
optn.text = "'.$filename.'";
optn.value = "'.$filename.'";
mydrop = window.parent.document.getElementById("css_'.$type.'_choice");
if(mydrop) {
	mydrop.options.add(optn);
	lastid = 0;
	while(mydrop.options[lastid+1]){ lastid++; }
	mydrop.selectedIndex = lastid;
	mydrop.onchange();
}
';
				$doc = JFactory::getDocument();
				$doc->addScriptDeclaration($js);
			}
			if($new) {
				JRequest::setVar('new', 0);
				JRequest::setVar('file', $type.'_'.$filename);
			}
		} else {
			hikamarket::display(JText::sprintf('FAIL_SAVE', $path), 'error');
		}
		return $this->css();
	}

	public function getUploadSetting($upload_key, $caller = '') {
		if(empty($upload_key))
			return false;

		$upload_value = null;
		$upload_keys = array(
			'default_vendor_image' => array(
				'type' => 'image',
				'field' => 'config[default_vendor_image]'
			)
		);

		if(empty($upload_keys[$upload_key]))
			return false;
		$upload_value = $upload_keys[$upload_key];

		return array(
			'limit' => 1,
			'type' => $upload_value['type'],
			'options' => array(),
			'extra' => array(
				'field_name' => $upload_value['field']
			)
		);
	}

	public function manageUpload($upload_key, &$ret, $uploadConfig, $caller = '') {
		if(empty($ret) || empty($ret->name))
			return;

		$upload_keys = array(
			'default_vendor_image' => true
		);
		if(empty($upload_keys[$upload_key]))
			return;

		$data = array(
			$upload_key => $ret->name
		);
		$config = hikamarket::config();
		$config->save($data);
	}
}
