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
class hikaserialConsumerClass extends hikaserialClass {

	protected $tables = array('consumer');
	protected $pkeys = array('consumer_id');
	protected $deleteToggle = array('consumer' => array('consumer_type', 'consumer_id'));
	protected $toggle = array('consumer_published' => 'consumer_id');

	public function save(&$element, $reorder = true) {
		$status = parent::save($element);
		if($status && empty($element->consumer_id)) {
			$element->consumer_id = $status;
			if($reorder) {
				$orderClass = hikaserial::get('helper.order');
				$orderClass->pkey = 'consumer_id';
				$orderClass->table = 'consumer';
				$orderClass->groupMap = 'consumer_type';
				$orderClass->groupVal = $element->consumer_type;
				$orderClass->orderingMap = 'consumer_ordering';
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
			$orderClass->pkey = 'consumer_id';
			$orderClass->table = 'consumer';
			$orderClass->groupMap = 'consumer_type';
			$orderClass->orderingMap = 'consumer_ordering';
			$orderClass->groupVal = $app->getUserStateFromRequest(HIKASERIAL_COMPONENT.'.consumer_plugin_type', 'consumer_plugin_type', '');
			$orderClass->reOrder();
		}
		return $status;
	}
}
