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
class hikamarketPaymentmethodsType {

	protected $values = array();

	protected function load($vendor_id) {
		if(!empty($this->values[$vendor_id]))
			return;

		$db = JFactory::getDBO();

		$this->values[$vendor_id] = array( 0 => 'manual' );

		$payments = array('paypal');
		foreach($payments as &$p) {
			$p = $db->Quote($p);
			unset($p);
		}
		$query = 'SELECT payment_id, payment_type FROM ' . hikamarket::table('shop.payment') . ' WHERE payment_type IN (' . implode(',', $payments).')';

		$db->setQuery($query);
		$methods = $db->loadObjectList();
		foreach($methods as $m) {
			$this->values[$vendor_id][$m->payment_id] = $m->payment_type;
		}
	}

	public function get($vendor_id, $payment_id) {
		$this->load($vendor_id);
		if(!empty($this->values[$vendor_id][$payment_id]))
			return $this->values[$vendor_id][$payment_id];
		return null;
	}

	public function display($vendor_id, $map, $values, $attribute = 'size="1"') {
		$this->load($vendor_id);
		$items = array();
		foreach($this->values[$vendor_id] as $key => $text) {
			$items[] = JHTML::_('select.option', $key, $text);
		}
		return JHTML::_('select.genericlist', $items, $map, 'class="inputbox" '.$attribute, 'value', 'text', $values);
	}
}
