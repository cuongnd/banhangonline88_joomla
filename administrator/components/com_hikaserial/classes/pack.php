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
class hikaserialPackClass extends hikaserialClass {

	protected $tables = array('pack');
	protected $pkeys = array('pack_id');
	protected $toggle = array('pack_published' => 'pack_id');

	public function get($element, $default = null) {
		$ret = parent::get($element, $default);
		if(!empty($ret->pack_params))
			$ret->pack_params = hikaserial::unserialize($ret->pack_params);

		return $ret;
	}

	public function save(&$element) {
		if(!isset($element->pack_params)) {
			$element->pack_params = null;
		}
		$unserializedParams = $element->pack_params;
		$element->pack_params = serialize($element->pack_params);

		$ret = parent::save($element);

		$element->pack_params = $unserializedParams;

		return $ret;
	}

	public function saveForm() {
		$pack = new stdClass();
		$pack->pack_id = hikaserial::getCID('pack_id');
		$formData = JRequest::getVar('data', array(), '', 'array');
		foreach($formData['pack'] as $col => $val) {
			hikaserial::secureField($col);
			$pack->$col = strip_tags($val);
		}
		$pack->pack_params = null;
		if(!empty($formData['pack_params'])) {
			$pack->pack_params = new stdClass();
			foreach($formData['pack_params'] as $k => $v) {
				hikaserial::secureField($k);
				$pack->pack_params->$k = $v;
			}
		}
		$pack->pack_description = JRequest::getVar('pack_description', '', '', 'string', JREQUEST_ALLOWRAW);

		$status = $this->save($pack);

		if($status) {

		}
		return $status;
	}

	public function frontSaveForm() {
		$app = JFactory::getApplication();
		if($app->isAdmin() || !hikaserial::initMarket() || !hikamarket::acl('plugins/hikaserial/pack/edit'))
			return false;

		$marketConfig = hikamarket::config();
		$vendor_id = hikamarket::loadVendor(false);

		$pack = new stdClass();
		$pack->pack_id = hikaserial::getCID('pack_id');

		$new = empty($pack->pack_id);
		if($new && !hikamarket::acl('plugins/hikaserial/pack/add'))
			return false;

		$formData = JRequest::getVar('data', array(), '', 'array');
		foreach($formData['pack'] as $col => $val) {
			hikaserial::secureField($col);
			$pack->$col = strip_tags($val);
		}
		unset($pack->pack_params);

		unset($pack->pack_description);
		if(hikamarket::acl('plugins/hikaserial/pack/edit/description')) {
			$pack->pack_description = JRequest::getVar('pack_description', '', '', 'string', JREQUEST_ALLOWRAW);
			if((int)$marketConfig->get('vendor_safe_product_description', 1)) {
				$safeHtmlFilter = JFilterInput::getInstance(null, null, 1, 1);
				$pack->pack_description = $safeHtmlFilter->clean($pack->pack_description, 'string');
			}
		}

		if(hikamarket::acl('plugins/hikaserial/pack/edit/generator')) {
			$packgeneratorType = hikaserial::get('type.pack_generator');
			$gen = $packgeneratorType->get($pack->pack_generator);
			if($gen === null)
				unset($pack->pack_generator);
		} else
			unset($pack->pack_generator);

		if(!hikamarket::acl('plugins/hikaserial/pack/edit/name')) unset($pack->pack_name);
		if(!hikamarket::acl('plugins/hikaserial/pack/edit/data')) unset($pack->pack_data);
		if(!hikamarket::acl('plugins/hikaserial/pack/edit/published')) unset($pack->pack_published);

		unset($pack->pack_manage_access);

		if($vendor_id > 1 || !hikamarket::acl('plugins/hikaserial/pack/edit/vendor'))
			unset($pack->pack_vendor_id);

		if($new && $vendor_id > 1)
			$pack->pack_vendor_id = (int)$vendor_id;

		$status = $this->save($pack);

		if($status) {

		}
		return $status;
	}

	public function delete($elements) {
		if(!is_array($elements)) {
			$elements = array($elements);
		}
		JArrayHelper::toInteger($elements);

		$query = 'SELECT serial_pack_id, count(*) as `cpt` FROM '.hikaserial::table('serial').' WHERE serial_pack_id IN ( '.implode(',',$elements).');';
		$this->db->setQuery($query);
		$serialPacks = $this->db->loadObjectList();
		$exclude = array();
		foreach($serialPacks as $serialPack) {
			if($serialPack->cpt > 0) {
				$exclude[] = $serialPack->serial_pack_id;
			}
		}
		if(!empty($exclude)) {
			$elements = array_diff($elements, $exclude);
		}
		return parent::delete($elements);
	}

	public function checkQuantity(&$pack) {
		if(empty($pack->pack_params->stock_level_notify) || $pack->pack_params->stock_level_notify <= 0)
			return true;

		$status = 'free';
		$query = 'SELECT count(*) as qty FROM ' . hikaserial::table('serial') . ' AS a WHERE a.serial_status = '.$this->db->Quote($status).' AND a.serial_pack_id='.$pack->pack_id;
		$this->db->setQuery($query);
		$pack->current_quantity = $this->db->loadResult();

		if(!empty($pack->pack_params->stock_level_notify) && (int)$pack->current_quantity <= (int)$pack->pack_params->stock_level_notify) {
			$mailClass = hikaserial::get('class.mail');
			$mail = $mailClass->load('pack_quantity_low', $pack);

			if(!empty($mail)) {
				$mail->subject = JText::sprintf($mail->subject, HIKASERIAL_LIVE);
				$shopConfig =& hikaserial::config(false);
				if(!empty($pack->email))
					$mail->dst_email = $pack->email;
				else
					$mail->dst_email = $shopConfig->get('from_email');

				if(!empty($pack->name))
					$mail->dst_name = $pack->name;
				else
					$mail->dst_name = $shopConfig->get('from_name');

				$mailClass->sendMail($mail);
			}
		}
		unset($pack->current_quantity);
		return true;
	}

	public function isVendorPack($pack_id, $vendor_id = -1) {
		static $vendorPackCache = array();

		if(!hikaserial::initMarket())
			return false;

		if($vendor_id === null || $vendor_id == -1) {
			$vendor_id = hikamarket::loadVendor(false, false);
			if($vendor_id == null)
				return false;
		}

		if(!empty($vendor_id) && (int)$vendor_id == 0)
			return false;
		if(!empty($pack_id) && (int)$pack_id <= 0)
			return false;

		if($vendor_id == 1)
			return true;

		if((int)$vendor_id > 1 && (int)$pack_id > 0) {
			if(empty($vendorPackCache[$vendor_id]))
				$vendorPackCache[$vendor_id] = array();

			if(isset($vendorPackCache[$vendor_id][$pack_id]))
				return $vendorPackCache[$vendor_id][$pack_id];

			$db = JFactory::getDBO();
			$query = 'SELECT count(pack.pack_id) '.
				' FROM ' . hikaserial::table('pack') . ' AS pack '.
				' LEFT JOIN '.hikaserial::table('product_pack').' AS pp ON pack.pack_id = pp.pack_id '.
				' LEFT JOIN '.hikaserial::table('shop.product').' AS product ON pp.product_id = product.product_id '.
				' WHERE pack.pack_id = '. (int)$pack_id . ' AND (product.product_vendor_id = ' . (int)$vendor_id . ' OR (product.product_vendor_id = 0 AND pack.pack_vendor_id = ' . (int)$vendor_id . '))';
			$db->setQuery($query);
			$vendorPackCache[$vendor_id][$pack_id] = ((int)$db->loadResult() == 1);

			return $vendorPackCache[$vendor_id][$pack_id];
		}
		return true;
	}

	public function &getNameboxData($typeConfig, &$fullLoad, $mode, $value, $search, $options) {

		$ret = array(
			0 => array(),
			1 => array()
		);

		$db = JFactory::getDBO();
		$app = JFactory::getApplication();

		$limit = (int)@$typeConfig['limit'];
		if(!empty($options['limit']))
			$limit = (int)$options['limit'];
		if(empty($limit))
			$limit = 30;

		$filters = array(
			'published' => 'pack_published = 1'
		);

		if(!empty($search)) {
			$searchStr = "'%" . ((HIKASHOP_J30) ? $db->escape($search, true) : $db->getEscaped($search, true) ) . "%'";
			$filters['search'] = 'pack_name LIKE ' . $searchStr;
		}

		if(!$app->isAdmin() && hikaserial::initMarket()) {
			$vendor = hikamarket::loadVendor(true);
			if($vendor->vendor_id > 1) {
				$acl = array();
				$accesses = explode(',', $vendor->vendor_access);
				foreach($accesses as $ax) {
					if(substr($ax,0,1) != '@')
						continue;
					$ax_id = (int)substr($ax,1);
					if($ax_id > 0)
						$acl[] = $ax_id;
				}

				if(empty($acl)) {
					$filters['vendor'] = '(pack_vendor_id = '.(int)$vendor->vendor_id.') OR (pack_vendor_id = 0 AND pack_manage_access = \'all\')';
				} else {
					$filters['vendor'] = '(pack_vendor_id = '.(int)$vendor->vendor_id.') OR (pack_vendor_id = 0 AND (pack_manage_access = \'all\' OR pack_manage_access LIKE \'%,'.implode(',%\' OR pack_manage_access LIKE \'%,', $acl).',%\'))';
				}
			}
		}

		$query = 'SELECT pack_id, pack_name, pack_data, pack_generator '.
			' FROM ' . hikaserial::table('pack') .
			' WHERE ('.implode(') AND (', $filters).')'.
			' ORDER BY pack_name';

		$db->setQuery($query, 0, $limit);
		$packs = $db->loadObjectList('pack_id');
		foreach($packs as $pack) {
			$ret[0][$pack->pack_id] = $pack;
		}

		if(count($packs) == $limit)
			$fullLoad = false;

		if(!empty($value)) {
			if(!is_array($value))
				$value = array($value);

			if($fullLoad) {
				foreach($value as $v) {
					if(isset($ret[0][(int)$v]))
						$ret[1][(int)$v] = $ret[0][(int)$v];
				}
			} else {
				$values = array_merge($value);
				JArrayHelper::toInteger($values);

				$query = 'SELECT pack_id, pack_name, pack_data, pack_generator '.
					' FROM ' . hikaserial::table('pack') .
					' WHERE pack_id IN ('.implode(',', $values).') '.
					' ORDER BY pack_name';
				$db->setQuery($query);
				$ret[1] = $db->loadObjectList('pack_id');
			}
		}

		return $ret;
	}
}
