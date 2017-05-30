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
class hikaserialPackType {

	protected $values = array();

	public function __construct() {
		$this->app = JFactory::getApplication();
	}

	public function load($addEmpty = false) {
		$query = 'SELECT pack_id, pack_name FROM ' . hikaserial::table('pack').' ORDER BY pack_name ASC';
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$packs = $db->loadObjectList('pack_id');
		if(!empty($packs)){
			if($addEmpty){
				$this->values[0] = JHTML::_('select.option', '', JText::_('PACKS_ALL'));
			}
			foreach($packs as $pack){
				$this->values[(int)$pack->pack_id] = JHTML::_('select.option', (int)$pack->pack_id, $pack->pack_name);
			}
		}
	}

	public function display($map, $value, $autoSubmit = false, $addEmpty = false) {
		if(empty($this->values)){
			$this->load($addEmpty);
		}
		$extra = 'class="inputbox" size="1"';
		if($autoSubmit)
			$extra .= ' onchange="document.adminForm.submit();"';
		return JHTML::_('select.genericlist', $this->values, $map, $extra, 'value', 'text', $value);
	}

	public function displaySingle($map, $value, $delete = false) {
		if(empty($this->nameboxType))
			$this->nameboxType = hikaserial::get('shop.type.namebox');

		return $this->nameboxType->display(
			$map,
			$value,
			hikashopNameboxType::NAMEBOX_SINGLE,
			'plg.hikaserial.pack',
			array(
				'delete' => $delete,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>'
			)
		);
	}

	public function displayMultiple($map, $values) {
		if(empty($values))
			$values = array();
		else if(is_string($values))
			$values = explode(',', $values);

		if(empty($this->nameboxType))
			$this->nameboxType = hikaserial::get('shop.type.namebox');

		return $this->nameboxType->display(
			$map,
			$values,
			hikashopNameboxType::NAMEBOX_MULTIPLE,
			'plg.hikaserial.pack',
			array(
				'delete' => true,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>'
			)
		);
	}
}
