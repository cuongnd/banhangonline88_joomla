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
class hikaserialPluginClass extends hikaserialClass {

	protected $tables = array('plugin');
	protected $pkeys = array('plugin_id');
	protected $deleteToggle = array('plugin' => array('plugin_type', 'plugin_id'));
	protected $toggle = array('plugin_published' => 'plugin_id');

	public function save(&$element, $reorder = true) {
		$status = parent::save($element);
		if($status && empty($element->plugin_id)) {
			$element->plugin_id = $status;
			if($reorder) {
				$orderClass = hikaserial::get('helper.order');
				$orderClass->pkey = 'plugin_id';
				$orderClass->table = 'plugin';
				$orderClass->groupMap = 'plugin_type';
				$orderClass->groupVal = $element->plugin_type;
				$orderClass->orderingMap = 'plugin_ordering';
				$orderClass->reOrder();
			}
		}
		return $status;
	}

	public function delete($elements) {
		$status = parent::delete($elements);
		if($status) {
			$app = JFactory::getApplication();
			$orderClass = hikaserial::get('helper.order');
			$orderClass->pkey = 'plugin_id';
			$orderClass->table = 'plugin';
			$orderClass->groupMap = 'plugin_type';
			$orderClass->orderingMap = 'plugin_ordering';
			$orderClass->groupVal = $app->getUserStateFromRequest(HIKASERIAL_COMPONENT.'.plugin_plugin_type', 'plugin_plugin_type', '');
			$orderClass->reOrder();
		}
		return $status;
	}
}
