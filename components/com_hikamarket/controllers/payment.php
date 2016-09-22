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
class paymentMarketController extends hikamarketController {
	protected $rights = array(
		'display' => array(),
		'add' => array(),
		'edit' => array('toggle'),
		'modify' => array(),
		'delete' => array('delete')
	);
	protected $type = 'payment';
	protected $config = null;

	public function __construct($config = array(), $skip = false) {
		parent::__construct($config, $skip);
		$this->config = hikamarket::config();
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
			if($task == 'toggle' && !hikamarket::acl('paymentplugin/edit/published'))
				return false;
			if($task == 'delete' && !hikamarket::acl('paymentplugin/delete'))
				return false;
			if(!hikamarket::isVendorPlugin($plugin_id, 'payment'))
				return false;
			return true;
		}
		return parent::authorize($task);
	}
}
