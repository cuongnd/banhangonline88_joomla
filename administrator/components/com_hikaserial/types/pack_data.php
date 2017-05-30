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
class hikaserialPack_dataType {

	protected $values = array();

	public function load() {
		$this->values = array();
		$this->values[] = JHTML::_('select.option', 'sql', JText::_('PACK_DATA_SQL'));
		$this->values[] = JHTML::_('select.option', 'none', JText::_('PACK_DATA_NONE'));
	}

	public function display($map, $value) {
		$this->load();
		return JHTML::_('select.genericlist', $this->values, $map, 'class="inputbox" size="1"', 'value', 'text', $value);
	}
}
