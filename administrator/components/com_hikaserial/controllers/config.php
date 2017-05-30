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
class configController extends hikaserialController {

	protected $rights = array(
		'display' => array('display','config','cancel','listing','sql','language'),
		'add' => array(),
		'edit' => array('toggle','delete'),
		'modify' => array('save','apply','share','send','savelanguage'),
		'delete' => array()
	);

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->registerDefaultTask('config');
	}

	public function display($tpl = null, $params = null) {
		JRequest::setVar('layout', 'config');
		return parent::display();
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

	public function cancel(){
		$this->setRedirect(hikaserial::completeLink('dashboard', false, true));
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
		$config = hikaserial::config();

		$formData = JRequest::getVar('config', array(), 'POST', 'array');
		$formData['display_serial_statuses'] = implode(',', $formData['display_serial_statuses']);
		if(is_array($formData['assignable_order_statuses']))
			$formData['assignable_order_statuses'] = implode(',', $formData['assignable_order_statuses']);
		if(is_array($formData['useable_serial_statuses']))
			$formData['useable_serial_statuses'] = implode(',', $formData['useable_serial_statuses']);
		$status = $config->save($formData);
		if($status) {
			$app->enqueueMessage(JText::_( 'HIKASHOP_SUCC_SAVED' ), 'message');
		} else {
			$app->enqueueMessage(JText::_( 'ERROR_SAVING' ), 'error');
		}

		$config->load();
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

	public function share(){
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

		$mailClass = hikaserial::get('shop.class.mail');
		$shopConfig = hikaserial::config(false);
		$config = hikaserial::config();
		$user = hikaserial::loadUser(true);

		$addedName = $shopConfig->get('add_names',true) ? $mailClass->cleanText(@$user->name) : '';
		$mail = $mailClass->get('language', $true);
		$mailClass->mailer->AddAddress($user->user_email, $addedName);
		$mailClass->mailer->AddAddress('translate-hikaserial@hikashop.com', 'Hikaserial Translation Team');

		$mail->subject = '[HIKASERIAL LANGUAGE FILE] ' . $code;
		$mail->altbody = 'The website '.HIKASHOP_LIVE.' using HikaSerial '.$config->get('level') . $config->get('version') . ' sent a language file : '.$code;
		$mail->altbody .= "\n\n\n" . $bodyEmail;
		$mail->html = 0;

		jimport('joomla.filesystem.file');
		$path = JPath::clean(JLanguage::getLanguagePath(JPATH_ROOT) . DS . $code . DS . $code . '.' . HIKASERIAL_COMPONENT . '.ini');
		$mailClass->mailer->AddAttachment($path);
		$result = $mailClass->sendMail($mail);

		if($result) {
			hikaserial::display(JText::_('THANK_YOU_SHARING'), 'success');
		}
	}

	private function savelng(){
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
				hikaserial::display(JText::sprintf('FAIL_SAVE', $path), 'error');
			}
		}

		if(empty($content))
			return;
		$path = JLanguage::getLanguagePath(JPATH_ROOT) . DS . $code . DS . $code . '.' . HIKASERIAL_COMPONENT . '.ini';
		$result = JFile::write($path, $content);
		if($result) {
			hikaserial::display(JText::_('HIKASHOP_SUCC_SAVED'), 'success');
			$updateHelper = hikaserial::get('helper.update');
			$updateHelper->installMenu($code);
			$js = 'window.top.document.getElementById("image'.$code.'").src = "'.HIKASHOP_IMAGES.'icons/icon-16-edit.png"';
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		} else {
			hikaserial::display(JText::sprintf('FAIL_SAVE', $path), 'error');
		}
		return $result;
	}
}
