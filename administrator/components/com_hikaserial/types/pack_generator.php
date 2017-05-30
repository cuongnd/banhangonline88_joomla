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
class hikaserialPack_generatorType {

	protected $values = array();

	public function load() {
		if(!empty($this->values))
			return;

		$this->values[''] = JText::_('HIKAS_NONE');

		JPluginHelper::importPlugin('hikaserial');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onPackGeneratorTypeDisplay', array(&$this->values));
	}

	public function get($name) {
		$this->load();
		if(isset($this->values[$name])) {
			return $this->values[$name];
		}
		return null;
	}

	public function display($map, $value) {
		$this->load();
		$data = array();
		foreach($this->values as $key => $val) {
			$data[] = JHTML::_('select.option', $key, $val);
		}
		return JHTML::_('select.genericlist', $data, $map, 'class="inputbox" size="1"', 'value', 'text', $value);
	}
}
