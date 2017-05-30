<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class hikadealPricemodeType {
	protected $values;

	public function __construct() {
		$this->values = array(
			'max' => JHTML::_('select.option', 'max', JText::_('HKA_PRICEMODE_MAXIMUM')),
			'first' => JHTML::_('select.option', 'first', JText::_('HKA_PRICEMODE_FIRST_STEP')),
			'reach' => JHTML::_('select.option', 'reach', JText::_('HKA_PRICEMODE_REACHED_STEP')),
		);
	}

	public function display($map, $value, $extra = '') {
		return JHTML::_('select.genericlist', $this->values, $map, $extra, 'value', 'text', $value);
	}
}
