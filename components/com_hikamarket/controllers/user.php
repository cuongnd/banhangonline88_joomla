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
class userMarketController extends hikamarketController {
	protected $rights = array(
		'display' => array('listing','selection','useselection','state','show','address','getaddresslist'),
		'add' => array(),
		'edit' => array(),
		'modify' => array('apply','save'),
		'delete' => array()
	);

	protected $type = 'user';
	protected $config = null;

	public function __construct($config = array(), $skip = false) {
		parent::__construct($config, $skip);
		if(!$skip)
			$this->registerDefaultTask('listing');
		$this->config = hikamarket::config();
	}

	public function edit() {
		return $this->show();
	}

	public function show() {
		if(!hikamarket::loginVendor())
			return false;
		if(!$this->config->get('frontend_edition',0))
			return false;

		$customer_id = hikamarket::getCID();
		$vendor_id = hikamarket::loadVendor(false, false);
		if($vendor_id > 1 && !hikamarket::isVendorCustomer($customer_id))
			return false;

		if(!hikamarket::acl('user/show'))
			return false;

		JRequest::setVar('layout', 'show');
		return parent::display();
	}

	public function address() {
		if(!hikamarket::loginVendor())
			return false;
		if(!$this->config->get('frontend_edition',0))
			return false;

		$vendor_id = hikamarket::loadVendor(false, false);
		if($vendor_id > 1)
			return false;

		if(!hikamarket::acl('user/edit/address'))
			return false;

		JRequest::setVar('layout', 'address');

		$tmpl = JRequest::getCmd('tmpl', '');
		$subtask = JRequest::getCmd('subtask', '');
		if($subtask == 'edit')
			JRequest::setVar('edition', true);

		if($subtask == 'save') {
			JRequest::checkToken('request') || die('Invalid Token');

			$user_id = JRequest::getInt('user_id');
			if($user_id > 0) {
				$addressClass = hikamarket::get('class.address');
				$result = $addressClass->frontSaveForm($user_id, 'display:vendor_user_edit=1');
			}
			if(empty($result)) {
				JRequest::setVar('edition', true);
			} else {
				JRequest::setVar('previous_cid', $result->previous_id);
				JRequest::setVar('cid', $result->id);
			}
		}

		if($subtask == 'delete') {
			JRequest::checkToken('request') || die('Invalid Token');
			$address_id = hikamarket::getCID('address_id');
			$user_id = JRequest::getInt('user_id');
			$addressClass = hikamarket::get('class.address');
			$addr = $addressClass->get($address_id);
			if(!empty($addr) && $addr->address_user_id == $user_id) {
				$ret = $addressClass->delete($addr);

				if($tmpl == 'component') {
					ob_end_clean();
					if(!empty($ret))
						echo '1';
					else
						echo '0';
					exit;
				}

				$app = JFactory::getApplication();
				if($ret)
					$app->enqueueMessage(JText::_('ADDRESS_DELETED_WITH_SUCCESS'));
				else
					$app->enqueueMessage(JText::_('ADDRESS_NOT_DELETED'), 'error');
				$app->redirect( hikamarket::completeLink('user&task=show&cid=' . $user_id) );
			}
			return false;
		}

		if($tmpl == 'component') {
			JRequest::setVar('hidemainmenu', 1);
			ob_end_clean();
			parent::display();
			exit;
		}
		return parent::display();
	}

	public function listing() {
		if(!hikamarket::loginVendor())
			return false;
		if(!$this->config->get('frontend_edition',0))
			return false;
		if(!hikamarket::acl('user/listing'))
			return false;

		JRequest::setVar('layout', 'listing');
		return parent::display();
	}

	public function store() {
		if(!hikamarket::loginVendor())
			return false;
		if(!$this->config->get('frontend_edition',0))
			return false;
		$vendor_id = hikamarket::loadVendor(false, false);
		if($vendor_id > 1)
			return false;

		$redirection = 'user';
		if(!hikamarket::acl('user/listing'))
			$redirection = 'vendor';
		if( !hikamarket::acl('user/edit') )
			return hikamarket::deny($redirection, JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_USER_EDIT')));

		$userClass = hikamarket::get('class.user');
		if( $userClass === null )
			return false;
		$status = $userClass->frontSaveForm();
		if($status) {
			JRequest::setVar('cid', $status);
			JRequest::setVar('fail', null);
		}

		return $status;
	}

	public function selection() {
		if(!hikamarket::loginVendor())
			return false;
		if(!$this->config->get('frontend_edition',0))
			return false;
		JRequest::setVar('layout', 'selection');
		return parent::display();
	}

	public function useselection() {
		if(!hikamarket::loginVendor())
			return false;
		if(!$this->config->get('frontend_edition',0))
			return false;
		JRequest::setVar('layout', 'useselection');
		return parent::display();
	}

	public function state() {
		if(!hikamarket::loginVendor())
			return false;
		if(!$this->config->get('frontend_edition',0))
			return false;
		JRequest::setVar('layout', 'state');
		return parent::display();
	}

	public function getAddressList() {
		while(ob_get_level())
			@ob_end_clean();

		if(!hikamarket::loginVendor() || !$this->config->get('frontend_edition',0)) {
			echo '[]';
			exit;
		}

		$user_id = JRequest::getInt('user_id', 0);
		$address_type = JRequest::getCmd('address_type', '');
		$displayFormat = JRequest::getVar('displayFormat', '{address_mini_format}');
		$search = JRequest::getVar('search', null);

		if(!hikamarket::isVendorCustomer($user_id, null, true)) {
			echo '[]';
			exit;
		}

		$nameboxType = hikamarket::get('type.namebox');
		$options = array(
			'url_params' => array(
				'USER_ID' => $user_id,
				'ADDR_TYPE' => $address_type,
			),
			'displayFormat' => $displayFormat
		);

		$ret = $nameboxType->getValues($search, 'address', $options);
		if(!empty($ret)) {
			echo json_encode($ret);
			exit;
		}
		echo '[]';
		exit;
	}
}
