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
class hikaserialGeneratorClass extends hikaserialClass {

	protected $tables = array('generator');
	protected $pkeys = array('generator_id');
	protected $deleteToggle = array('generator' => array('generator_type', 'generator_id'));
	protected $toggle = array('generator_published' => 'generator_id');

	public function save(&$element, $reorder = true) {
		$status = parent::save($element);
		if($status && empty($element->generator_id)) {
			$element->generator_id = $status;
			if($reorder) {
				$orderClass = hikaserial::get('helper.order');
				$orderClass->pkey = 'generator_id';
				$orderClass->table = 'generator';
				$orderClass->groupMap = 'generator_type';
				$orderClass->groupVal = $element->generator_type;
				$orderClass->orderingMap = 'generator_ordering';
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
			$orderClass->pkey = 'generator_id';
			$orderClass->table = 'generator';
			$orderClass->groupMap = 'generator_type';
			$orderClass->orderingMap = 'generator_ordering';
			$orderClass->groupVal = $app->getUserStateFromRequest(HIKASERIAL_COMPONENT.'.generator_plugin_type', 'generator_plugin_type', '');
			$orderClass->reOrder();
		}
		return $status;
	}
}
