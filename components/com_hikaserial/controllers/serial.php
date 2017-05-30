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
class serialController extends hikaserialController {

	protected $rights = array(
		'display' => array(
			'check','consume',
			'select','useselection',
			'listing',
		),
		'add' => array(),
		'edit' => array(),
		'modify' => array(),
		'delete' => array()
	);

	public function __construct($config = array(), $skip = false) {
		parent::__construct($config, $skip);
	}

	public function check() {
		JRequest::setVar('layout', 'check');
		return $this->display();
	}

	public function select() {
		if(!hikaserial::initMarket())
			return false;

		if(!hikamarket::loginVendor())
			return false;
		$marketConfig = hikamarket::config();
		if(!$marketConfig->get('frontend_edition',0))
			return false;
		if(!hikamarket::acl('product/edit/plugin/hikaserial'))
			return hikamarket::deny('vendor', JText::sprintf('HIKAM_ACTION_DENY', JText::sprintf('HIKAM_ACT_PLUGIN', HIKASERIAL_NAME)));

		JRequest::setVar('layout', 'select');
		return parent::display();
	}

	public function useselection(){
		if(!hikaserial::initMarket())
			return false;

		if(!hikamarket::loginVendor())
			return false;
		$marketConfig = hikamarket::config();
		if(!$marketConfig->get('frontend_edition',0))
			return false;
		if(!hikamarket::acl('product/edit/plugin/hikaserial'))
			return hikamarket::deny('vendor', JText::sprintf('HIKAM_ACTION_DENY', JText::sprintf('HIKAM_ACT_PLUGIN', HIKASERIAL_NAME)));

		JRequest::setVar('layout', 'useselection');
		return parent::display();
	}

	public function consume() {
		$formData = JRequest::getVar('hikaserial', array(), '', 'array');
		JRequest::setVar('layout', 'consume');

		if(empty($formData) || empty($formData['serial_data']))
			return $this->display();

		$config = hikaserial::config();
		$serialClass = hikaserial::get('class.serial');
		$serial_data = $formData['serial_data'];
		$serial_extra_data = null;
		if(!empty($formData['serial_extra_data'])) {
			$serial_extra_data = array();
			if(is_array($formData['serial_extra_data']))
				$serial_extra_data = $formData['serial_extra_data'];
			else
				$serial_extra_data = array($formData['serial_extra_data']);
		}
		$serial = null;

		$pack = null;
		if(!empty($formData['pack_name']) && is_string($formData['pack_name']))
			$pack = $formData['pack_name'];
		if(!empty($formData['pack_id']))
			$pack = (int)$formData['pack_id'];

		$user_id = 0;
		if(empty($formData['format'])) {
			JRequest::checkToken('request') || die('Invalid Token');
			$user_id = hikaserial::loadUser();
			if(empty($user_id) && $config->get('forbidden_consume_guest', 1)) {
				$app = JFactory::getApplication();
				$app->enqueueMessage(JText::_('CONSUME_NOT_LOGGED'), 'error');
				return $this->display();
			}
			if(empty($user_id))
				$user_id = null;

			$filters = null;
			if($pack !== null) {
				if(is_int($pack)) {
					$filters = array('pack.pack_id = ' . $pack);
				} else {
					$db = JFactory::getDBO();
					$filters = array('pack.pack_name = ' . $db->Quote($pack));
				}
			}

			$serials = $serialClass->find($serial_data, $filters, array('serial_user_id DESC', 'serial_id ASC'));
		} else {
			$serials = $serialClass->check($serial_data, $pack);
			JRequest::setVar('layout', 'consumed');
		}

		if(count($serials) == 1) {
			$serial = reset($serials);
		} else {
			$assigned_status = $config->get('assigned_serial_status', 'assigned');
			foreach($serials as $s) {
				if(($s->serial_user_id == $user_id || $s->serial_user_id ==  0 || $user_id === 0) && ($s->serial_status == $assigned_status)) {
					$serial = $s;
				}
				if($serial != null)
					break;
			}
		}

		if(empty($serial) || empty($serial->serial_id)) {
			JRequest::setVar('consumed_serial', false);
			return $this->display();
		}

		$ret = false;
		$assigned_status = $config->get('assigned_serial_status', 'assigned');

		if($serial->serial_status != $assigned_status) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('HIKASERIAL_ALREADY_USED'), 'error');
		} else {
			$packClass = hikaserial::get('class.pack');
			$serial_pack = $packClass->get((int)$serial->serial_pack_id);
			$checkUser = !empty($serial_pack->pack_params->consume_user_assign);
			$ret = $serialClass->consume($serial->serial_id, $serial_extra_data, $checkUser);
		}

		$return_url = JRequest::getVar('return_url');
		if($ret && !empty($return_url) && hikaserial::disallowUrlRedirect($return_url)) {
			$app = JFactory::getApplication();
			$app->redirect($return_url);
		}

		if($ret) {
			$serial = $serialClass->get($serial->serial_id, true);
			JRequest::setVar('consumed_serial', $serial);
			JRequest::setVar('layout', 'consumed');
		} else {
			JRequest::setVar('consumed_serial', false);
		}
		return $this->display();
	}

	public function listing() {
		$user = JFactory::getUser();
		if($user->guest) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('PLEASE_LOGIN_FIRST'));
			global $Itemid;
			$url = '';
			if(!empty($Itemid)) { $url = '&Itemid=' . $Itemid; }
			if(!HIKASHOP_J16) {
				$url = 'index.php?option=com_user&view=login' . $url;
			} else {
				$url = 'index.php?option=com_easysocial&view=login' . $url;
			}
			$app->redirect(JRoute::_($url . '&return='.urlencode(base64_encode(hikaserial::currentUrl())), false));
		}

		JRequest::setVar('layout', 'listing');
		return $this->display();
	}
}
