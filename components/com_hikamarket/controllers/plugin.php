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
class pluginMarketController extends hikamarketController {
	protected $rights = array(
		'display' => array('show', 'listing'),
		'add' => array('add'),
		'edit' => array('edit', 'toggle',),
		'modify' => array('apply', 'save'), // , 'saveorder'
		'delete' => array()
	);

	protected $ordering = array(
		'type' => 'plugin',
		'pkey' => 'plugin_id',
		'table' => 'shop.plugin',
		'groupMap' => 'plugin_type',
		'orderingMap' => 'plugin_ordering',
		'groupVal' => 0
	);

	protected $type = 'plugin';
	protected $config = null;

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

		$plugin_type = JRequest::getCmd('plugin_type', 'payment');
		if(!in_array($plugin_type, array('payment','shipping', 'generic')))
			return hikamarket::deny('vendor', JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_PLUGIN_LISTING')));
		if(!hikamarket::acl($plugin_type.'plugin/listing'))
			return hikamarket::deny('vendor', JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_PLUGIN_LISTING')));

		$vendor_id = hikamarket::loadVendor(false);
		if($vendor_id === null || ((int)$vendor_id > 1 && (int)$this->config->get('plugin_vendor_config', 0) == 0))
			return hikamarket::deny('vendor', JText::_('HIKAM_PAGE_DENY'));

		JRequest::setVar('layout', 'listing');
		return parent::display();
	}

	public function saveorder(){
		if( !hikamarket::loginVendor() )
			return false;
		if( !$this->config->get('frontend_edition',0) )
			return false;
		$plugin_type = JRequest::getCmd('plugin_type', '');
		if(!in_array($plugin_type, array('payment','shipping', 'generic')))
			return hikamarket::deny('vendor', JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_PLUGIN_EDIT')));
		if( !hikamarket::acl($plugin_type.'plugin/edit') )
			return hikamarket::deny('plugin', JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_PLUGIN_EDIT')));

		$vendor_id = hikamarket::loadVendor(false);
		if($vendor_id === null || ((int)$vendor_id > 1 && (int)$this->config->get('plugin_vendor_config', 0) == 0))
			return hikamarket::deny('plugin&plugin_type='.$plugin_type, JText::_('HIKAM_PAGE_DENY'));

		$plugin_id = hikamarket::getCID('plugin_id');
		if(!hikamarket::isVendorPlugin($plugin_id, $plugin_type))
			return hikamarket::deny('plugin&plugin_type='.$plugin_type, JText::_('HIKAM_PAGE_DENY'));

		$this->ordering['groupVal'] = $plugin_id;
		return parent::saveorder();
	}

	public function authorize($task) {
		if($task == 'toggle' || $task == 'delete') {
			$completeTask = JRequest::getCmd('task');
			$plugin_id = (int)substr($completeTask, strrpos($completeTask, '-') + 1);

			if(!hikamarket::loginVendor())
				return false;
			if(!$this->config->get('frontend_edition',0))
				return false;
			if(!JRequest::checkToken('request'))
				return false;
			if($task == 'toggle' && !hikamarket::acl('genericplugin/edit/published'))
				return false;
			if($task == 'delete' && !hikamarket::acl('genericplugin/delete'))
				return false;
			if(!hikamarket::isVendorPlugin($plugin_id, 'plugin'))
				return false;
			return true;
		}
		return parent::authorize($task);
	}

	public function show() {
		$this->edit();
	}

	public function edit() {
		if( !hikamarket::loginVendor() )
			return false;
		if( !$this->config->get('frontend_edition',0) )
			return false;

		$plugin_type = JRequest::getCmd('plugin_type', '');
		if(!in_array($plugin_type, array('payment','shipping', 'generic')))
			return hikamarket::deny('vendor', JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_PLUGIN_EDIT')));
		if( !hikamarket::acl($plugin_type.'plugin/edit') )
			return hikamarket::deny('plugin', JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_PLUGIN_EDIT')));

		$plugin_id = hikamarket::getCID('plugin_id');
		if(!empty($plugin_id) && !hikamarket::isVendorPlugin($plugin_id, $plugin_type))
			return hikamarket::deny('plugin&plugin_type='.$plugin_type, JText::_('HIKAM_PAGE_DENY'));

		if(empty($plugin_id)) {
			if( !hikamarket::acl($plugin_type.'plugin/add') )
				return hikamarket::deny('plugin', JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_PLUGIN_EDIT')));
			$vendor_id = hikamarket::loadVendor(false);
			if($vendor_id === null || ((int)$vendor_id > 1 && (int)$this->config->get('plugin_vendor_config', 0) == 0))
				return hikamarket::deny('plugin&plugin_type='.$plugin_type, JText::_('HIKAM_PAGE_DENY'));
		}

		JRequest::setVar('layout', 'form');
		return parent::display();
	}

	public function add() {
		if( !hikamarket::loginVendor() )
			return false;
		if( !$this->config->get('frontend_edition',0) )
			return false;
		$plugin_type = JRequest::getCmd('plugin_type', '');
		if(!in_array($plugin_type, array('payment','shipping', 'generic')))
			return hikamarket::deny('vendor', JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_PLUGIN_EDIT')));
		if( !hikamarket::acl($plugin_type.'plugin/add') )
			return hikamarket::deny('plugin', JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_PLUGIN_EDIT')));

		$vendor_id = hikamarket::loadVendor(false);
		if($vendor_id === null || ((int)$vendor_id > 1 && (int)$this->config->get('plugin_vendor_config', 0) == 0))
			return hikamarket::deny('plugin&plugin_type='.$plugin_type, JText::_('HIKAM_PAGE_DENY'));


		JRequest::setVar('layout', 'add');
		return parent::display();
	}

	public function store() {
		if(!hikamarket::loginVendor())
			return false;
		if( !$this->config->get('frontend_edition',0) )
			return false;
		$plugin_type = JRequest::getCmd('plugin_type', 'payment');
		if(!in_array($plugin_type, array('payment','shipping', 'generic')))
			return hikamarket::deny('vendor', JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_PLUGIN_EDIT')));
		if( !hikamarket::acl($plugin_type.'plugin/edit') )
			return hikamarket::deny('plugin', JText::sprintf('HIKAM_ACTION_DENY', JText::_('HIKAM_ACT_PLUGIN_EDIT')));

		$vendor_id = hikamarket::loadVendor(false);
		if($vendor_id === null || ((int)$vendor_id > 1 && (int)$this->config->get('plugin_vendor_config', 0) == 0))
			return hikamarket::deny('plugin&plugin_type='.$plugin_type, JText::_('HIKAM_PAGE_DENY'));

		$pluginClass = hikamarket::get('class.plugin');
		if( $pluginClass === null )
			return false;
		$status = $pluginClass->frontSaveForm();
		if($status) {
			JRequest::setVar('cid', $status);
			JRequest::setVar('fail', null);
		}
		return $status;
	}
}
