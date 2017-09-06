<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.6.3
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class CartViewCart extends hikashopView {
	var $ctrl = 'cart';
	var $nameListing = 'HIKASHOP_CHECKOUT_CART';
	var $nameForm = 'HIKASHOP_CHECKOUT_CART';
	var $icon = 'cart';

	function display($tpl = null) {
		$this->paramBase = HIKASHOP_COMPONENT.'.'.$this->getName();
		$function = $this->getLayout();
		if(method_exists($this,$function))
			$this->$function();
		parent::display($tpl);
	}

	function setName() {
		$cart_type = JRequest::getString('cart_type', 'cart');

		if($cart_type == 'wishlist') {
			$this->nameListing = 'WISHLIST';
			$this->nameForm = 'WISHLIST';
			$this->icon = 'wishlist';
		}
	}

	function listing() {
		$this->setName();

		$app = JFactory::getApplication();
		$database = JFactory::getDBO();
		$config =& hikashop_config();

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->search = $app->getUserStateFromRequest( $this->paramBase.".search", 'search', '', 'string' );
		$pageInfo->filter->order->value = $app->getUserStateFromRequest( $this->paramBase.".filter_order", 'filter_order',	'a.cart_modified','cmd' );
		$pageInfo->filter->order->dir	= $app->getUserStateFromRequest( $this->paramBase.".filter_order_Dir", 'filter_order_Dir',	'desc',	'word' );
		$pageInfo->limit->value = $app->getUserStateFromRequest( $this->paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$pageInfo->limit->start = $app->getUserStateFromRequest( $this->paramBase.'.limitstart', 'limitstart', 0, 'int' );

		$popup = (JRequest::getString('tmpl') === 'component');
		$this->assignRef('popup', $popup);

		if(JRequest::getString('cart_type', 'cart') == 'cart')
			$filters = array('a.cart_type=\'cart\'');
		else
			$filters = array('a.cart_type=\'wishlist\'');
		$searchMap = array('a.cart_id','a.user_id','a.cart_name','a.cart_coupon','a.cart_type');
		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.hikashop_getEscaped(JString::strtolower(trim($pageInfo->search)),true).'%\'';
			$filters[] =  implode(" LIKE $searchVal OR ",$searchMap)." LIKE $searchVal";
		}
		$groupBy = 'GROUP BY a.cart_id';
		$order = '';
		if(!empty($pageInfo->filter->order->value)){
			$order = ' ORDER BY '.$pageInfo->filter->order->value.' '.strtoupper($pageInfo->filter->order->dir);
		}
		if(!empty($filters)){
			$filters = ' WHERE '. implode(' AND ',$filters);
		}else{
			$filters = '';
		}

		$from = 'FROM '.hikashop_table('cart').' AS a';
		$query = $from.' '.$filters.' '.$groupBy.' '.$order;
		$database->setQuery('SELECT a.*'.$query,(int)$pageInfo->limit->start,(int)$pageInfo->limit->value);
		$rows = $database->loadObjectList();

		if(!empty($pageInfo->search)){
			$rows = hikashop_search($pageInfo->search,$rows,'cart_id');
		}
		$database->setQuery('SELECT COUNT(*) '.$from.' '.$filters.' '.$groupBy);
		$pageInfo->elements = new stdClass();
		$pageInfo->elements->total = $database->loadResult();
		$pageInfo->elements->page = count($rows);
		$currencyClass = hikashop_get('class.currency');
		$this->assignRef('currencyHelper',$currencyClass);

		$currencyClass = hikashop_get('class.currency');
		$productClass = hikashop_get('class.product');
		$main_currency = (int)$config->get('main_currency',1);
		$currency_id = hikashop_getCurrency();
		if($config->get('tax_zone_type','shipping')=='billing'){
			$zone_id = hikashop_getZone('billing');
		}else{
			$zone_id = hikashop_getZone('shipping');
		}
		$discount_before_tax = (int)$config->get('discount_before_tax',0);

		$cartClass = hikashop_get('class.cart');
		foreach($rows as $k => $row) {
			if($row->cart_id != null) {
				$cartClass->cart = new stdClass();
				$cartClass->cart->cart_id = (int)$row->cart_id;
				$cartClass->cart->cart_type = JRequest::getString('cart_type', 'cart');
				$rows[$k]->full_cart = $cartClass->loadFullCart(false,true,true);
				$rows[$k]->price = isset($rows[$k]->full_cart->full_total->prices[0]->price_value)?$rows[$k]->full_cart->full_total->prices[0]->price_value:0;
				$rows[$k]->quantity = isset($rows[$k]->full_cart->number_of_items)?$rows[$k]->full_cart->number_of_items:0;
				$rows[$k]->currency = isset($rows[$k]->full_cart->full_total->prices[0]->price_currency_id)?$rows[$k]->full_cart->full_total->prices[0]->price_currency_id:$config->get('main_currency',1);
			}
		}

		$cart = hikashop_get('helper.cart');
		$this->assignRef('cart', $cart);
		$this->assignRef('carts', $rows);
		$pageInfo->elements->total = count($rows);
		$this->assignRef('pageInfo', $pageInfo);
		$this->getPagination();

		$manageUser = hikashop_isAllowed($config->get('acl_user_manage', 'all'));
		$this->assignRef('manageUser', $manageUser);
		$pageInfo->manageUser = $manageUser;

		$manage = hikashop_isAllowed($config->get('acl_wishlist_manage','all'));
		$this->assignRef('manage', $manage);

		hikashop_setTitle(JText::_($this->nameListing), $this->icon, $this->ctrl);

		$this->toolbar = array(
			array('name' => 'addNew', 'display' => $manage),
			array('name' => 'editList', 'display' => $manage),
			array('name' => 'deleteList', 'display' => hikashop_isAllowed($config->get('acl_wishlist_delete', 'all'))),
			'|',
			array('name' => 'pophelp', 'target' => $this->ctrl.'-listing'),
			'dashboard'
		);
	}

	function form() {
		$this->setName();

		$cart_id = hikashop_getCID('cart_id',false)?hikashop_getCID('cart_id',false):0;
		if(empty($cart_id))
			$cart_id = JRequest::getInt('cart_id', 0);

		$cartClass = hikashop_get('class.cart');
		if(!empty($cart_id)) {
			$cartClass->cart = new stdClass();
			$cartClass->cart->cart_id = (int)$cart_id;
			$cartClass->cart->cart_type = JRequest::getVar('cart_type','cart');
			$element = $cartClass->loadFullCart(true,true,true);
			$cart = $cartClass->cart;
			$rows = $element->products;
			$task = 'edit';
		} else {
			$element = $cart = new stdClass();

			$cart->cart_id = 0;
			$cart->cart_name = '';
			$cart->cart_type = JRequest::getVar('cart_type','cart');
			$cart->cart_modified = time();

			$rows = null;
			$task = 'add';
		}
		$this->assignRef('rows',$rows);

		if(hikashop_level(2)){
			$fieldsClass=hikashop_get('class.field');
			$this->assignRef('fieldsClass',$fieldsClass);
		}

		$products_ids = array();
		$productClass = hikashop_get('class.product');

		$user = null;
		if(!is_null($cart) && isset($cart->user_id) && $cart->user_id != 0){
			$userClass = hikashop_get('class.user');
			$user = $userClass->get($cart->user_id,'cms');
			if(is_null($user))
				$user = $userClass->get($cart->user_id);
		}

		$this->assignRef('user', $user);
		$this->assignRef('cart', $cart);
		$this->assignRef('element', $element);

		$popup = hikashop_get('helper.popup');
		$this->assignRef('popup', $popup);
		$toggle = hikashop_get('helper.toggle');
		$this->assignRef('toggle',$toggle);

		hikashop_setTitle(JText::_($this->nameForm),$this->icon,$this->ctrl.'&task='.$task.'&cart_id='.$cart_id);

		$this->toolbar = array(
			'save',
			'apply',
			'cancel',
			'|',
			array('name' => 'pophelp', 'target' => $this->ctrl.'-listing')
		);
	}

	function addCharacteristics(&$element,&$mainCharacteristics,&$characteristics) {
		$element->characteristics = $mainCharacteristics[$element->product_id][0];
		if(!empty($element->characteristics) && is_array($element->characteristics)) {
			foreach($element->characteristics as $k => $characteristic) {
				if(!empty($mainCharacteristics[$element->product_id][$k])) {
					$element->characteristics[$k]->default=end($mainCharacteristics[$element->product_id][$k]);
				} else {
					$app = JFactory::getApplication();
					$app->enqueueMessage('The default value of one of the characteristics of that product isn\'t available as a variant. Please check the characteristics and variants of that product');
				}
			}
		}

		if(empty($element->variants))
			return;

		foreach($characteristics as $characteristic) {
			foreach($element->variants as $k => $variant) {
				if($variant->product_id != $characteristic->variant_product_id)
					continue;

				$element->variants[$k]->characteristics[$characteristic->characteristic_parent_id] = $characteristic;
				$element->characteristics[$characteristic->characteristic_parent_id]->values[$characteristic->characteristic_id] = $characteristic;

				if($this->selected_variant_id && $variant->product_id == $this->selected_variant_id) {
					$element->characteristics[$characteristic->characteristic_parent_id]->default = $characteristic;
				}
			}
		}

		if(isset($_REQUEST['hikashop_product_characteristic'])){
			if(is_array($_REQUEST['hikashop_product_characteristic'])){
				JArrayHelper::toInteger($_REQUEST['hikashop_product_characteristic']);
				$chars = $_REQUEST['hikashop_product_characteristic'];
			}else{
				$chars = JRequest::getCmd('hikashop_product_characteristic','');
				$chars = explode('_',$chars);
			}
			if(!empty($chars)){
				foreach($element->variants as $k => $variant){
					$chars = array();
					foreach($variant->characteristics as $val){
						$i = 0;
						$ordering = @$element->characteristics[$val->characteristic_parent_id]->ordering;
						while(isset($chars[$ordering])&& $i < 30){
							$i++;
							$ordering++;
						}
						$chars[$ordering] = $val;
					}
					ksort($chars);
					$element->variants[$k]->characteristics=$chars;
					$variant->characteristics=$chars;

					$choosed = true;
					foreach($variant->characteristics as $characteristic){
						$ok = false;
						foreach($chars as $k => $char){
							if(!empty($char)){
								if($characteristic->characteristic_id==$char){
									$ok = true;
									break;
								}
							}
						}
						if(!$ok) {
							$choosed=false;
						} else {
							$element->characteristics[$characteristic->characteristic_parent_id]->default=$characteristic;
						}
					}
					if($choosed){
						break;
					}
				}
			}
		}

		foreach($element->variants as $k => $variant) {
			$temp = array();
			foreach($element->characteristics as $k2 => $characteristic2) {
				if(empty($variant->characteristics))
					continue;
				foreach($variant->characteristics as $k3 => $characteristic3) {
					if($k2 == $k3) {
						$temp[$k3] = $characteristic3;
						break;
					}
				}
			}
			$element->variants[$k]->characteristics = $temp;
		}
	}

	public function customer_set() {
		$users = JRequest::getVar('cid', array(), '', 'array');
		$rows = array();
		$data = '';
		$singleSelection = true;
		$cart_id = JRequest::getInt('cart_id', 0);

		$elemStruct = array(
			'user_email',
			'user_cms_id',
			'name',
			'username',
			'email'
		);

		$set_address = JRequest::getInt('set_user_address', 0);

		if(!empty($users)) {
			JArrayHelper::toInteger($users);
			$db = JFactory::getDBO();
			$query = 'SELECT a.*, b.* FROM '.hikashop_table('user').' AS a LEFT JOIN '.hikashop_table('users', false).' AS b ON a.user_cms_id = b.id WHERE a.user_id IN ('.implode(',',$users).')';
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
					if($set_address && $singleSelection)
						$d .= ',updates:[\'billing\',\'history\']';
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
		$this->assignRef('singleSelection', $singleSelection);
		$this->assignRef('cart_id', $cart_id);
	}
}
