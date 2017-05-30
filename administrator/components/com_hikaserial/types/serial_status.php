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
class hikaserialSerial_statusType {

	protected $values = array();

	public function getValues() {
		return array(
			'free' => JText::_('SERIAL_FREE'),
			'reserved' => JText::_('SERIAL_RESERVED'),
			'assigned' => JText::_('SERIAL_ASSIGNED'),
			'used' => JText::_('SERIAL_USED'),
			'unassigned' => JText::_('SERIAL_UNASSIGNED'),
			'deleted' => JText::_('SERIAL_DELETED')
		);
	}

	public function get($key) {
		$values = $this->getValues();
		if(isset($values[$key]))
			return $values[$key];
		return $key;
	}

	public function load($addEmpty = false) {
		$this->values = array();
		if($addEmpty === true) {
			$this->values[''] = JHTML::_('select.option', '', JText::_('SERIAL_STATUS_ALL'));
		}
		$statuses = $this->getValues();
		foreach($statuses as $key => $val) {
			$this->values[$key] = JHTML::_('select.option', $key, $val);
		}
	}

	public function display($map, $value, $addEmpty = false, $adminFilter = false) {
		$this->load($addEmpty);
		$extra = 'class="inputbox" size="1"';
		if($adminFilter) {
			$extra .= ' onchange="document.adminForm.submit();"';
		}
		return JHTML::_('select.genericlist', $this->values, $map, $extra, 'value', 'text', $value);
	}

	public function displayMultiple($map, $values, $mode = 'list') {
		$this->load(false);

		if($mode == 'checkbox') {
			if(substr($map, -2) != '[]')
				$map .= '[]';
			$extra = '';

			$ret = JHTML::_('select.radiolist', $this->values, $map, $extra, 'value', 'text', $values);
			$ret = str_replace(array('type="radio"', 'selected="selected"'), array('type="checkbox"', 'checked="checked"'), $ret);
			return $ret;
		}
		if($mode == 'list') {
			if(substr($map, -2) != '[]')
				$map .= '[]';
			$extra = '';

			$extra.=' multiple="multiple"';
			return JHTML::_('select.genericlist', $this->values, $map, $extra, 'value', 'text', $values);
		}

		if(empty($values))
			$values = array();
		else if(is_string($values))
			$values = explode(',', $values);

		$shopConfig = hikaserial::config(false);
		hikaserial::loadJslib('otree');

		if(substr($map,-2) == '[]')
			$map = substr($map,0,-2);
		$id = str_replace(array('[',']'),array('_',''),$map);
		$ret = '<div class="nameboxes" id="'.$id.'" onclick="window.oNameboxes[\''.$id.'\'].focus(\''.$id.'_text\');">';
		if(!empty($values)) {
			foreach($values as $key) {
				if(isset($this->values[$key]))
					$name = $this->values[$key]->text;
				else
					$name = JText::sprintf('UNKNOWN_STATUS_X', $key);

				$ret .= '<div class="namebox" id="'.$id.'_'.$key.'">'.
					'<input type="hidden" name="'.$map.'[]" value="'.$key.'"/>'.$name.
					' <a class="closebutton" href="#" onclick="window.oNameboxes[\''.$id.'\'].unset(this,\''.$key.'\');window.oNamebox.cancelEvent();return false;"><span>X</span></a>'.
					'</div>';
			}
		}

		$ret .= '<div class="namebox" style="display:none;" id="'.$id.'tpl">'.
				'<input type="hidden" name="{map}" value="{key}"/>{name}'.
				' <a class="closebutton" href="#" onclick="window.oNameboxes[\''.$id.'\'].unset(this,\'{key}\');window.oNamebox.cancelEvent();return false;"><span>X</span></a>'.
				'</div>';

		$ret .= '<div class="nametext">'.
			'<input id="'.$id.'_text" type="text" style="width:50px;min-width:60px" onfocus="window.oNameboxes[\''.$id.'\'].focus(this);" onkeyup="window.oNameboxes[\''.$id.'\'].search(this);" onchange="window.oNameboxes[\''.$id.'\'].search(this);"/>'.
			'<span style="position:absolute;top:0px;left:-2000px;visibility:hidden" id="'.$id.'_span">span</span>'.
			'</div>';

		$data = array();
		foreach($this->values as $key => $value) {
			if(empty($key))
				continue;
			$data[$key] = $value->text;
		}

		$namebox_options = array(
			'mode' => 'list',
			'img_dir' => HIKASHOP_IMAGES,
			'map' => $map,
			'min' => $shopConfig->get('namebox_search_min_length', 3),
			'multiple' => true
		);

		$ret .= '<div style="clear:both;float:none;"></div></div>
<div class="namebox-popup">
	<div id="'.$id.'_olist" style="display:none;" class="oList namebox-popup-content"></div>
</div>
<script type="text/javascript">
new window.oNamebox(
	\''.$id.'\',
	'.json_encode($data).',
	'.json_encode($namebox_options).'
);';
			if(!empty($values)) {
				$ret .= '
try{
	window.oNameboxes[\''.$id.'\'].content.block('.json_encode($values).');
}catch(e){}';
			}

			$ret .= '
</script>';
		return $ret;
	}
}
