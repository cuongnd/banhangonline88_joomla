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
class usermarketViewusermarket extends HikamarketView {

	const ctrl = 'user';
	const name = 'HIKA_USERS';
	const icon = 'generic';

	public function display($tpl = null) {
		$this->paramBase = HIKAMARKET_COMPONENT.'.'.$this->getName();
		$function = $this->getLayout();
		if(method_exists($this,$function))
			$this->$function();
		parent::display($tpl);
	}

	public function show() {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$ctrl = '';
		$this->paramBase = HIKAMARKET_COMPONENT.'.'.$this->getName().'.edit';

		$vendor = hikamarket::loadVendor(true, false);
		$this->assignRef('vendor', $vendor);

		$config = hikamarket::config();
		$this->assignRef('config', $config);

		$shopConfig = hikamarket::config(false);
		$this->assignRef('shopConfig', $shopConfig);

		$user_id = hikamarket::getCID('user_id');
		$this->assignRef('user_id', $user_id);

		$this->loadRef(array(
			'userClass' => 'shop.class.user',
			'fieldsClass' => 'shop.class.field',
			'addressShopClass' => 'shop.class.address',
			'addressClass' => 'class.address',
			'currencyHelper' => 'shop.class.currency'
		));

		$user = $this->userClass->get($user_id);
		$this->assignRef('user', $user);

		$this->fieldsClass->addJS($null, $null, $null);

		$fields = array();
		$null = null;
		if($this->config->get('address_show_details', 0)) {
			$fields['address'] = $this->fieldsClass->getFields('display:vendor_user_show=1', $null, 'address');
		} else {
			$fields['address'] = $this->fieldsClass->getFields('field_frontcomp', $null, 'address');
		}
		if(hikashop_level(1)) {
			$fields['user'] = $this->fieldsClass->getFields('display:vendor_user_show=1', $user, 'user');
			$this->fieldsClass->jsToggle($fields['user'], $user, 0);
			foreach($fields['user'] as &$field) {
				$field_display = explode(';', trim($field->field_display, ';'));
				$field->vendor_edit = in_array('vendor_user_edit=1', $field_display);
			}
			unset($field);
		}
		$this->fieldsClass->jsToggle($fields['address'], $null, 0);
		$this->assignRef('fields', $fields);

		$addresses = $this->addressShopClass->getByUser($user_id);
		$this->addressShopClass->loadZone($addresses);
		$this->assignRef('addresses', $addresses);

		$order_list_limit = $this->config->get('customer_order_list_limit', 15);
		$filters = array(
			'order_user_id = '.(int)$user_id
		);
		$order_type = 'sale';

		if($vendor->vendor_id > 1) {
			$order_type = 'subsale';
			$filters[] = 'order_vendor_id = ' . (int)$vendor->vendor_id;
		}

		$query = 'SELECT * FROM ' . hikamarket::table('shop.order') . ' WHERE order_type='.$db->Quote($order_type).' AND ('.implode(' OR ', $filters).') ORDER BY order_id DESC';
		$db->setQuery($query, 0, $order_list_limit);
		$orders = $db->loadObjectList();
		$this->assignRef('orders', $orders);

		$query = 'SELECT COUNT(order_id) FROM ' . hikamarket::table('shop.order') . ' WHERE order_type='.$db->Quote($order_type).' AND ('.implode(' OR ', $filters).')';
		$db->setQuery($query);
		$order_count = $db->loadResult();
		$this->assignRef('order_count', $order_count);

		$this->toolbar = array(
			'back' => array(
				'icon' => 'back',
				'name' => JText::_('HIKA_BACK'),
				'url' => hikamarket::completeLink('user')
			),
			'apply' => array(
				'url' => '#apply',
				'linkattribs' => 'onclick="return window.hikamarket.submitform(\'apply\',\'hikamarket_user_form\');"',
				'icon' => 'apply',
				'name' => JText::_('HIKA_APPLY'), 'pos' => 'right',
				'display' => hikamarket::acl('user/edit') && ($vendor->vendor_id <= 1)
			),
			'save' => array(
				'url' => '#save',
				'linkattribs' => 'onclick="return window.hikamarket.submitform(\'save\',\'hikamarket_user_form\');"',
				'icon' => 'save',
				'name' => JText::_('HIKA_SAVE'), 'pos' => 'right',
				'display' => hikamarket::acl('user/edit') && ($vendor->vendor_id <= 1)
			)
		);
	}

	public function listing() {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$vendor = hikamarket::loadVendor(true, false);
		$this->assignRef('vendor', $vendor);

		$fieldsClass = hikamarket::get('shop.class.field');

		$fields = $fieldsClass->getData('display:vendor_user_listing=1', 'user', false);
		$this->assignRef('fields', $fields);
		$singleSelection = JRequest::getVar('single', 0);
		$confirm = JRequest::getVar('confirm', 1);

		$manage = hikamarket::acl('user/edit') || hikamarket::acl('user/show');
		$this->assignRef('manage', $manage);

		$elemStruct = array(
			'user_email',
			'user_cms_id',
			'name',
			'username',
			'email'
		);

		global $Itemid;
		$url_itemid = '';
		if(!empty($Itemid))
			$url_itemid = '&Itemid='.$Itemid;
		$this->assignRef('Itemid', $Itemid);

		$cfg = array(
			'table' => 'shop.user',
			'main_key' => 'user_id',
			'order_sql_value' => 'hkuser.user_id'
		);

		$pageInfo = $this->getPageInfo($cfg['order_sql_value']);

		$filters = array();
		$oder = '';
		$searchMap = array(
			'hkuser.user_id',
			'hkuser.user_email',
			'juser.username',
			'juser.email',
			'juser.name'
		);
		foreach($fields as $field) {
			$searchMap[] = 'hkuser.'.$field->field_namekey;
		}

		$this->processFilters($filters, $order, $searchMap, array('juser.', 'hkuser.'));

		$customerVendorJoin = '';
		if($vendor->vendor_id > 1)
			$customerVendorJoin = ' INNER JOIN '.hikamarket::table('customer_vendor').' AS cv ON hkuser.user_id = cv.customer_id AND cv.vendor_id = '.$vendor->vendor_id . ' ';

		$query = ' FROM '.hikamarket::table('user','shop').' AS hkuser ' . $customerVendorJoin .
			' LEFT JOIN '.hikamarket::table('users',false).' AS juser ON hkuser.user_cms_id = juser.id '.$filters.$order;
		$db->setQuery('SELECT hkuser.*,juser.* '.$query, (int)$pageInfo->limit->start, (int)$pageInfo->limit->value);
		$rows = $db->loadObjectList();

		$fieldsClass->handleZoneListing($fields, $rows);
		foreach($rows as $k => $row) {
			if(!empty($row->user_params)) {
				$rows[$k]->user_params = hikamarket::unserialize($row->user_params);
			}
		}

		$db->setQuery('SELECT COUNT(*) '.$query);
		$pageInfo->elements = new stdClass();
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);

		$this->getPagination();

		$this->assignRef('rows', $rows);
		$this->assignRef('singleSelection', $singleSelection);
		$this->assignRef('confirm', $confirm);
		$this->assignRef('elemStruct', $elemStruct);
		$this->assignRef('pageInfo', $pageInfo);
		$this->assignRef('fieldsClass', $fieldsClass);
		$this->assignRef('fields', $fields);

		$this->toolbar = array(
			array('icon' => 'back', 'name' => JText::_('HIKA_BACK'), 'url' => hikamarket::completeLink('vendor'))
		);
	}

	public function selection(){
		$ctrl = JRequest::getCmd('ctrl');
		$this->assignRef('ctrl', $ctrl);

		$task = 'useselection';
		$this->assignRef('task', $task);

		$afterParams = array();
		$after = JRequest::getString('after', '');
		if(!empty($after)) {
			list($ctrl, $task) = explode('|', $after, 2);

			$afterParams = JRequest::getString('afterParams', '');
			$afterParams = explode(',', $afterParams);
			foreach($afterParams as &$p) {
				$p = explode('|', $p, 2);
				unset($p);
			}
		}
		$this->assignRef('afterParams', $afterParams);

		$this->listing();
	}

	public function useselection() {
		$users = JRequest::getVar('cid', array(), '', 'array');
		$rows = array();
		$data = '';
		$confirm = JRequest::getVar('confirm', true);
		$singleSelection = JRequest::getVar('single', false);

		$elemStruct = array(
			'user_email',
			'user_cms_id',
			'name',
			'username',
			'email'
		);

		if(!empty($users)) {
			JArrayHelper::toInteger($users);
			$db = JFactory::getDBO();
			$query = 'SELECT a.*, b.* FROM '.hikamarket::table('user','shop').' AS a INNER JOIN '.hikamarket::table('users', false).' AS b ON a.user_cms_id = b.id WHERE a.user_id IN ('.implode(',',$users).')';
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			if(!empty($rows)) {
				$data = array();
				foreach($rows as $v) {
					$d = '{id:'.$v->user_id;
					foreach($elemStruct as $s) {
						if($s == 'id')
							continue;
						$d .= ','.$s.':\''. str_replace('"','\'',$v->$s).'\'';
					}
					$data[] = $d.'}';
				}
				if(!$singleSelection)
					$data = '['.implode(',',$data).']';
				else {
					$data = $data[0];
					$rows = $rows[0];
				}
			}
		}
		$this->assignRef('rows', $rows);
		$this->assignRef('data', $data);
		$this->assignRef('confirm', $confirm);
		$this->assignRef('singleSelection', $singleSelection);

		if($confirm == true) {
			$js = 'hikashop.ready(function(){window.parent.hikamarket.submitBox('.$data.');});';
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}
	}

	public function state() {
		$namekey = JRequest::getCmd('namekey','');
		if(!headers_sent()){
			header('Content-Type:text/html; charset=utf-8');
		}
		if(!empty($namekey)){
			$field_namekey = JRequest::getString('field_namekey', '');
			if(empty($field_namekey))
				$field_namekey = 'address_state';

			$field_id = JRequest::getString('field_id', '');
			if(empty($field_id))
				$field_id = 'address_state';

			$field_type = JRequest::getString('field_type', '');
			if(empty($field_type))
				$field_type = 'address';

			$class = hikamarket::get('shop.type.country');
			echo $class->displayStateDropDown($namekey, $field_id, $field_namekey, $field_type);
		}
		exit;
	}

	public function address() {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$ctrl = '';
		$this->paramBase = HIKAMARKET_COMPONENT.'.'.$this->getName().'.edit';

		$vendor = hikamarket::loadVendor(true, false);
		$this->assignRef('vendor', $vendor);

		$config = hikamarket::config();
		$this->assignRef('config', $config);

		$shopConfig = hikamarket::config(false);
		$this->assignRef('shopConfig', $shopConfig);

		$address_id = hikamarket::getCID('address_id');
		$this->loadRef(array(
			'fieldsClass' => 'shop.class.field',
			'addressShopClass' => 'shop.class.address',
			'addressClass' => 'class.address'
		));

		$user_id = JRequest::getInt('user_id');
		$this->assignRef('user_id', $user_id);

		$edit = false;
		if(JRequest::getVar('edition', false) === true)
			$edit = true;
		$this->assignRef('edit', $edit);

		$null = null;
		if(!$edit) {
			$fieldMode = 'field_frontcomp';
			if($this->config->get('address_show_details', 0)) {
				$fieldMode = 'display:vendor_user_show=1';
			}
			$fields = array(
				'address' => $this->fieldsClass->getFields($fieldMode, $null, 'address')
			);
		} else {
			$extra_fields_show = $this->fieldsClass->getFields('display:vendor_user_show=1', $null, 'address');
			$extra_fields_edit = $this->fieldsClass->getFields('display:vendor_user_edit=1', $null, 'address');
			$all_fields = array();
			foreach($extra_fields_show as $fieldname => $field) {
				$all_fields[$field->field_ordering] = $field;
				$all_fields[$field->field_ordering]->fieldname = $fieldname;
			}
			unset($extra_fields_show);
			foreach($extra_fields_edit as $fieldname => $field) {
				if(!isset($all_fields[$field->field_ordering])) {
					$all_fields[$field->field_ordering] = $field;
					$all_fields[$field->field_ordering]->fieldname = $fieldname;
				}
				$all_fields[$field->field_ordering]->vendor_edit = true;
			}
			unset($extra_fields_edit);
			ksort($all_fields);
			$fields = array('address' => array());
			foreach($all_fields as $field) {
				$fieldname = $field->fieldname;
				$fields['address'][$fieldname] = $field;
			}
			unset($all_fields);
		}
		$this->assignRef('fields', $fields);

		$this->fieldsClass->jsToggle($fields['address'], $null, 0);

		$address = $this->addressClass->get($address_id);
		$this->assignRef('address', $address);

		if(@$address->address_user_id != $user_id) {
			$address = new stdClass();
			$address->address_user_id = $user_id;
			$address->address_id = $address_id;
		}
	}
}
