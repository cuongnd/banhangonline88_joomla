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
class hikamarketUserClass extends hikamarketClass {

	protected $tables = array('shop.user');
	protected $pkeys = array('user_id');

	protected $userClass = null;

	public function frontSaveForm() {
		$app = JFactory::getApplication();
		if($app->isAdmin())
			return false;

		$vendor_id = hikamarket::loadVendor(false, false);
		if($vendor_id > 1) return false;
		if(!hikamarket::acl('user/edit')) return false;

		$ret = false;
		$fieldsClass = hikamarket::get('class.field');
		$shopfieldsClass = hikamarket::get('shop.class.field');

		$user_id = hikamarket::getCID('user_id');

		$oldUser = null;
		if(!empty($user_id))
			$oldUser = $this->get($user_id);
		$area = 'display:vendor_user_show=1;display:vendor_user_edit=1';
		$user = $fieldsClass->getFilteredInput('user', $oldUser, false, 'data', false, $area);
		if(empty($user))
			return false;
		$user->user_id = $user_id;

		$formData = JRequest::getVar('data', array(), '', 'array');

		if(hikamarket::acl('user/edit/email') && isset($formData['user']['user_email']))
			$user->user_email = $formData['user']['user_email'];
		else
			unset($user->user_email);
		unset($user->default_address);

		$ret = $this->save($user);

		if($ret) {
			if(hikamarket::acl('user/edit/address') && !empty($formData['user']['default_address'])) {
				$default_address = (int)$formData['user']['default_address'];
				$addressClass = hikamarket::get('class.address');
				$newDefault = $addressClass->get($default_address);
				if(!empty($newDefault) && !empty($newDefault->address_published) && (int)$newDefault->address_user_id == $user_id && empty($newDefault->address_default)) {
					$newDefault->address_default = 1;
					$addressClass->save($newDefault);
				}
			}
		}

		return $ret;
	}

	public function get($id, $type = 'hikashop', $geoloc = false) {
		if(empty($this->userClass))
			$this->userClass = hikamarket::get('shop.class.user');
		return $this->userClass->get($id, $type, $geoloc);
	}

	public function register($redirect = false) {
		$app = JFactory::getApplication();
		$config = hikamarket::config();
		$shopConfig = hikamarket::config(false);

		$user = clone(JFactory::getUser());
		$authorize = JFactory::getACL();

		jimport('joomla.application.component.helper');
		$usersConfig = JComponentHelper::getParams('com_users');
		if($usersConfig->get('allowUserRegistration') == '0') {
			JError::raiseError(403, JText::_('Access Forbidden'));
			return false;
		}

		$newUsertype = $usersConfig->get('new_usertype');
		if(!$newUsertype)
			$newUsertype = (!HIKASHOP_J16) ? 'Registered' : 2;

		$userGroupRegistration = $config->get('user_group_registration', '');
		if(HIKASHOP_J16 && !empty($userGroupRegistration) && (int)$userGroupRegistration > 0) {
			$newUsertype = (int)$userGroupRegistration;
		}

		$fieldClass = hikamarket::get('shop.class.field');
		if(empty($this->userClass))
			$this->userClass = hikamarket::get('shop.class.user');
		$old = null;

		$registerData = $fieldClass->getInput('register', $old, true);
		$userData = $fieldClass->getInput('user', $old, true);

		$addressData = new stdClass();
		if($shopConfig->get('address_on_registration',1)) {
			$addressData = $fieldClass->getInput('address', $old, true);
		}

		if($registerData === false || $addressData === false || $userData === false)
			return false;

		if(empty($registerData->name)) {
			$registerData->name = @$addressData->address_firstname .
				(!empty($addressData->address_middle_name) ? ' ' . $addressData->address_middle_name : '') .
				(!empty($addressData->address_lastname) ? ' ' . $addressData->address_lastname : '');
			if(empty($registerData->name) && !empty($registerData->email)){
				$parts = explode('@', $registerData->email);
				$registerData->name = array_shift($parts);
			}
		}

		if($config->get('registration_ask_password', 1) == 0) {
			$registerData->username = $registerData->email;
			jimport('joomla.user.helper');
			$registerData->password = JUserHelper::genRandomPassword();
			$registerData->password2 = $registerData->password;
		}

		if($config->get('registration_email_is_username', 0) == 1)
			$registerData->username = $registerData->email;

		$data = array(
			'name' => @$registerData->name,
			'username' => @$registerData->username,
			'email' => @$registerData->email,
			'password' => @$registerData->password,
			'password2' => @$registerData->password2
		);
		JRequest::setVar('main_user_data', $data);
		if(!empty($addressData->address_vat)) {
			$vat = hikamarket::get('shop.helper.vat');
			if(!$vat->isValid($addressData)) {
				$app->enqueueMessage(JText::_('VAT_NUMBER_NOT_VALID'));
				return false;
			}
		}


		if(HIKASHOP_J16)
			$data['groups'] = array( $newUsertype => $newUsertype );

		if(HIKASHOP_J25) {
			$jconfig = JFactory::getConfig();
			if(HIKASHOP_J30)
				$locale = $jconfig->get('language');
			else
				$locale = $jconfig->getValue('config.language');
			$data['params'] = array(
				'site_language' => $locale,
				'language' => $locale
			);
		}
		if(!$user->bind($data, 'usertype'))
			JError::raiseError(500, $user->getError());

		$user->set('id', 0);
		if(!HIKASHOP_J16) {
			$user->set('usertype', $newUsertype);
			$user->set('gid', $authorize->get_group_id('', $newUsertype, 'ARO'));
		}

		$date = JFactory::getDate();
		if(HIKASHOP_J30)
			$user->set('registerDate', $date->toSql());
		else
			$user->set('registerDate', $date->toMySQL());

		$useractivation = $usersConfig->get('useractivation');
		if($useractivation > 0) {
			jimport('joomla.user.helper');
			if(HIKASHOP_J30) {
				$user->set('activation', JApplication::getHash( JUserHelper::genRandomPassword()) );
			} else {
				$user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
			}
			$user->set('block', '1');
		}

		if( !$user->save() ) {
			JError::raiseWarning('', JText::_($user->getError()));
			return false;
		}
		$this->userClass->get(false);
		$newUser = $this->userClass->get($user->id, 'cms');

		if(!empty($newUser)) {
			$userData->user_id = $newUser->user_id;
		} elseif(!empty($user->id)) {
			$userData->user_cms_id = $user->id;
		} else {
			$userData->user_email = $registerData->email;
		}

		if($shopConfig->get('affiliate_registration', 0)) {
			if(JRequest::getInt('hikashop_affiliate_checkbox', 0)) {
				$userData->user_partner_activated = 1;
				$registerData->user_partner_activated = 1;
			}
		}

		$this->user_id = $this->userClass->save($userData);

		if($shopConfig->get('address_on_registration', 1)) {
			if(isset($addressData->address_id)) {
				unset($addressData->address_id);
			}

			$registerData->user_id = $this->user_id;
			if(!empty($addressData)) {
				$addressData->address_user_id = $this->user_id;
				$addressClass = hikamarket::get('shop.class.address');
				$this->address_id = $addressClass->save($addressData);
			}
		}

		$mailClass = hikamarket::get('shop.class.mail');
		$registerData->user_data =& $userData;
		$registerData->address_data =& $addressData;
		$registerData->password = preg_replace('/[\x00-\x1F\x7F]/', '', @$registerData->password); // Disallow control chars in the email
		$registerData->active = $useractivation;
		$vars = '';
		if(function_exists('json_encode')) {
			$vars = urlencode( base64_encode( json_encode( array(
						'passwd' => $registerData->password,
						'username' => $registerData->username
				))));
		}

		global $Itemid;
		$url = '';
		if(!empty($Itemid)) {
			$url = '&Itemid=' . $Itemid;
		}

		$lang = JFactory::getLanguage();
		$lang->load('com_user', JPATH_SITE);
		$locale = strtolower(substr($lang->get('tag'), 0, 2));

		$registerData->activation_url = HIKASHOP_LIVE . 'index.php?option=com_hikamarket&ctrl=vendor&task=activate&activation='.urlencode($user->get('activation')).'&infos='.$vars.'&id='.$this->user_id.$url.'&lang='.$locale;
		$mail = $mailClass->get('user_account', $registerData);
		if(!empty($registerData->email)) {
			$mail->subject = JText::sprintf($mail->subject, @$registerData->name, HIKASHOP_LIVE);
			$mail->dst_email =& $registerData->email;
			if(!empty($registerData->name)) {
				$mail->dst_name =& $registerData->name;
			} else {
				$mail->dst_name = '';
			}
			$mailClass->sendMail($mail);

			jimport('joomla.application.component.helper');
			$params = JComponentHelper::getParams('com_users');
			if($params->get('mail_to_admin', '0')) {
				$mail = $mailClass->get('user_account_admin_notification', $registerData);
				$mail->subject = JText::sprintf($mail->subject, @$registerData->name, HIKASHOP_LIVE);
				if(empty($mail->dst_email))
					$mail->dst_email = explode(',', $shopConfig->get('from_email'));
				$mailClass->sendMail($mail);
			}
		}

		if($useractivation > 0 && $redirect) {
			$message = JText::_('HIKA_REG_COMPLETE_ACTIVATE');
			$app->enqueueMessage($message);
			if($page == 'checkout') {
				$message  = JText::_('WHEN_CLICKING_ACTIVATION');
				$app->enqueueMessage($message);
			}
			$app->redirect(hikamarket::completeLink('shop.checkout&task=activate_page&lang='.$locale, false, true));
		} elseif(file_exists(JPATH_ROOT.DS.'components'.DS.'com_comprofiler'.DS.'comprofiler.php')) {
			$newUser = $this->userClass->get($this->user_id);
			$this->userClass->addAndConfirmUserInCB($newUser, $addressData);
		}

		return $registerData;
	}

	public function &getNameboxData($typeConfig, &$fullLoad, $mode, $value, $search, $options) {
		$ret = array(
			0 => array(),
			1 => array()
		);

		$sqlJoins = array();
		$sqlFilters = array('juser.block = 0');
		if(!empty($options['filters'])) {
			foreach($options['filters'] as $filter) {
			}
		}

		$vendor_id = hikamarket::loadVendor(false);
		if($vendor_id > 1) {
			$sqlJoins['customer_vendor'] = 'INNER JOIN ' . hikamarket::table('customer_vendor') . ' AS customer ON user.user_id = customer.customer_id';
			$sqlFilters['vendor'] = 'customer.vendor_id = ' . (int)$vendor_id;
		}

		if(!empty($search)) {
			$searchMap = array('user.user_id', 'juser.name', 'user.user_email');
			if(!HIKASHOP_J30)
				$searchVal = '\'%' . $this->db->getEscaped(JString::strtolower($search), true) . '%\'';
			else
				$searchVal = '\'%' . $this->db->escape(JString::strtolower($search), true) . '%\'';
			$sqlFilters['search'] = '('.implode(' LIKE '.$searchVal.' OR ', $searchMap).' LIKE '.$searchVal.')';
		}

		$sqlSort = 'user.user_id';
		if(!empty($options['sort']) && $options['sort'] == 'name')
			$sqlSort = 'user.user_name';

		$max = 30;

		$query = 'SELECT user.user_id, (CASE WHEN juser.name IS NULL THEN user.user_email ELSE juser.name END) AS name, user.user_email '.
			' FROM ' . hikamarket::table('shop.user') . ' AS user '.
			' LEFT JOIN ' . hikamarket::table('joomla.users') . ' AS juser ON user.user_cms_id = juser.id ' . implode(' ', $sqlJoins) .
			' WHERE ('.implode(') AND (', $sqlFilters).') '.
			' ORDER BY '.$sqlSort;
		$this->db->setQuery($query, 0, $max+1);
		$users = $this->db->loadObjectList('user_id');
		if(count($users) > $max) {
			$fullLoad = false;
			array_pop($users);
		}

		if(!empty($value) && !is_array($value) && (int)$value > 0) {
			$value = (int)$value;
			if(isset($users[$value])) {
				$ret[1] = $users[$value];
			} else {
				$query = 'SELECT user.user_id, (CASE WHEN juser.name IS NULL THEN user.user_email ELSE juser.name END) AS name, user.user_email '.
					' FROM ' . hikamarket::table('shop.user') . ' AS user '.
					' LEFT JOIN ' . hikamarket::table('joomla.users') . ' AS juser ON user.user_cms_id = juser.id'.
					' WHERE user.user_id = ' . $value;
				$this->db->setQuery($query);
				$ret[1] = $this->db->loadObject();
			}
		} else if(!empty($value) && is_array($value)) {
			JArrayHelper::toInteger($value);
			$query = 'SELECT user.user_id, (CASE WHEN juser.name IS NULL THEN user.user_email ELSE juser.name END) AS name, user.user_email '.
				' FROM ' . hikamarket::table('shop.user') . ' AS user '.
				' LEFT JOIN ' . hikamarket::table('joomla.users') . ' AS juser ON user.user_cms_id = juser.id'.
				' WHERE user.user_id IN (' . implode(',', $value) . ')';
			$this->db->setQuery($query);
			$ret[1] = $this->db->loadObject('vendor_id');
		}

		if(!empty($users))
			$ret[0] = $users;
		return $ret;
	}

}
