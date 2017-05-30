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
class discountMarketController extends hikamarketController {
	protected $rights = array(
		'display' => array('show', 'listing'),
		'add' => array('add'),
		'edit' => array('edit'),
		'modify' => array('apply', 'save', 'toggle'),
		'delete' => array('delete')
	);

	public function __construct($config = array(), $skip = false) {
		parent::__construct($config, $skip);
		if(!$skip)
			$this->registerDefaultTask('listing');
		$this->config = hikamarket::config();
	}

	public function listing() {
		if(!hikamarket::loginVendor())
			return false;
		if(!$this->config->get('frontend_edition',0))
			return false;
		if(!hikamarket::acl('discount/listing'))
			return hikamarket::deny('vendor', JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_DISCOUNT_LISTING')));
		JRequest::setVar('layout', 'listing');
		return parent::display();
	}

	public function add() {
		if( !hikamarket::loginVendor() )
			return false;
		if( !$this->config->get('frontend_edition',0) )
			return false;

		$redirection = 'discount';
		if(!hikamarket::acl('discount/listing'))
			$redirection = 'vendor';
		if( !hikamarket::acl('discount/add') ) {
			return hikamarket::deny($redirection, JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_DISCOUNT_ADD')));
		}

		JRequest::setVar('layout', 'show');
		return parent::display();
	}

	public function edit() {
		if( !hikamarket::loginVendor() )
			return false;
		if( !$this->config->get('frontend_edition',0) )
			return false;

		$redirection = 'discount';
		if(!hikamarket::acl('discount/listing'))
			$redirection = 'vendor';
		if( !hikamarket::acl('discount/edit') ) {
			return hikamarket::deny($redirection, JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_DISCOUNT_EDIT')));
		}

		$discount_id = hikamarket::getCID('discount_id');
		if(!hikamarket::isVendorDiscount($discount_id))
			return hikamarket::deny($redirection, JText::_('HIKAM_PAGE_DENY'));

		JRequest::setVar('layout', 'show');
		return parent::display();
	}

	public function store() {
		if(!hikamarket::loginVendor())
			return false;
		if( !$this->config->get('frontend_edition',0) )
			return false;
		$redirection = 'discount';
		if(!hikamarket::acl('discount/listing'))
			$redirection = 'vendor';
		if( !hikamarket::acl('discount/edit') )
			return hikamarket::deny($redirection, JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_DISCOUNT_EDIT')));

		$discountClass = hikamarket::get('class.discount');
		if( $discountClass === null )
			return false;
		$status = $discountClass->frontSaveForm();
		if($status) {
			JRequest::setVar('cid', $status);
			JRequest::setVar('fail', null);
		}

		return $status;
	}

	public function authorize($task) {
		if($task == 'toggle' || $task == 'delete') {
			$completeTask = JRequest::getCmd('task');
			$discount_id = (int)substr($completeTask, strrpos($completeTask, '-') + 1);

			if(!hikamarket::loginVendor())
				return false;
			if(!$this->config->get('frontend_edition',0))
				return false;
			if(!JRequest::checkToken('request'))
				return false;
			if($task == 'toggle' && !hikamarket::acl('discount/edit/published'))
				return false;
			if($task == 'delete' && !hikamarket::acl('discount/delete'))
				return false;
			if(!hikamarket::isVendorDiscount($discount_id))
				return false;
			return true;
		}
		return parent::authorize($task);
	}
}
