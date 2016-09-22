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
class productmarketViewProductmarket extends hikamarketView {

	const ctrl = 'product';
	const name = 'HIKAMARKET_PRODUCTMARKET';
	const icon = 'product';

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKAMARKET_COMPONENT.'.'.$this->getName();
		$fct = $this->getLayout();
		$ret = true;
		if(method_exists($this, $fct))
			$ret = $this->$fct($params);
		if($ret !== false)
			parent::display($tpl);
	}

	public function shop_block($params = null) {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$config = hikamarket::config();
		$this->assignRef('config', $config);

		$currencyClass = hikamarket::get('shop.class.currency');
		$this->assignRef('currencyClass', $currencyClass);
		$currencyType = hikamarket::get('shop.type.currency');
		$this->assignRef('currencyType', $currencyType);
		$popup = hikamarket::get('shop.helper.popup');
		$this->assignRef('popup', $popup);

		$data = null;
		$product_id = 0;
		$product_type = 'main';

		if(!empty($params)) {
			$product_id = (int)$params->get('product_id');
			$product_type = $params->get('product_type');
		}

		if(hikamarket::level(1) && $product_id > 0) {
			$feeClass = hikamarket::get('class.fee');
			$data = $feeClass->getProduct($product_id);
		}

		$this->assignRef('data', $data);
		$this->assignRef('product_id', $product_id);
		$this->assignRef('product_type', $product_type);
	}

	public function shop_form($params = null) {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$config = hikamarket::config();
		$this->assignRef('config', $config);
		$popup = hikamarket::get('shop.helper.popup');
		$this->assignRef('popup', $popup);

		$this->loadRef(array(
			'nameboxType' => 'type.namebox',
		));

		$product_type = 'main';
		$product_id = 0;
		$product_vendor_id = 0;

		if(!empty($params)) {
			$product_id = (int)$params->get('product_id');
			$product_vendor_id = (int)$params->get('product_vendor_id');
			$product_type = $params->get('product_type');
		}

		$this->assignRef('product_id', $product_id);
		$this->assignRef('product_vendor_id', $product_vendor_id);
		$this->assignRef('product_type', $product_type);
	}

	public function waitingapproval() {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$ctrl = '';
		$this->paramBase = HIKAMARKET_COMPONENT.'.'.$this->getName().'.waitingapproval';
		hikamarket::setTitle(JText::_('WAITING_APPROVAL_LIST'), self::icon, self::ctrl.'&task=waitingapproval');

		$config = hikamarket::config();
		$this->assignRef('config', $config);

		$this->loadRef(array(
			'toggleClass' => 'helper.toggle',
			'imageHelper' => 'shop.helper.image',
			'currencyHelper' => 'shop.class.currency',
			'childdisplayType' => 'shop.type.childdisplay',
			'shopCategoryType' => 'type.shop_category',
		));

		$manage = hikamarket::acl('product/edit');
		$this->assignRef('manage', $manage);

		$product_action_delete = hikamarket::acl('product/delete');
		$this->assignRef('product_action_delete', $product_action_delete);

		$cancelUrl = urlencode(base64_encode(hikamarket::completeLink('product&task=waitingapproval')));
		$this->assignRef('cancelUrl', $cancelUrl);

		global $Itemid;
		$url_itemid = '';
		if(!empty($Itemid))
			$url_itemid = '&Itemid='.$Itemid;
		$this->assignRef('Itemid', $Itemid);

		$cfg = array(
			'table' => 'shop.product',
			'main_key' => 'product_id',
			'order_sql_value' => 'product.product_id'
		);

		$vendorType = hikamarket::get('type.filter_vendor');
		$this->assignRef('vendorType', $vendorType);

		$default_sort_value = trim($config->get('product_listing_default_sort_value', $cfg['order_sql_value']));
		if(empty($default_sort_value))
			$default_sort_value = $cfg['order_sql_value'];
		$default_sort_dir = trim($config->get('product_listing_default_sort_dir', 'asc'));
		if(empty($default_sort_dir) || !in_array($default_sort_dir, array('asc', 'desc')))
			$default_sort_dir = 'asc';

		$pageInfo = $this->getPageInfo($default_sort_value, $default_sort_dir);

		$pageInfo->filter->vendors = $app->getUserStateFromRequest($this->paramBase.'.filter_vendors', 'filter_vendors', -1, 'int');

		$filters = array(
			'main' => 'product.product_parent_id = 0',
			'product_type' => 'product.product_type = \'waiting_approval\''
		);
		$searchMap = array(
			'product.product_name',
			'product.product_description',
			'product.product_id',
			'product.product_code'
		);
		$select = array();
		$join = '';

		if($pageInfo->filter->vendors >= 0) {
			$select['parent_product_name'] = 'parent_product.product_name as parent_product_name';
			$join = ' LEFT JOIN '.hikamarket::table('shop.product').' AS parent_product ON product.product_parent_id = parent_product.product_id AND parent_product.product_vendor_id != product.product_vendor_id AND product.product_vendor_id > 0 ';
		}

		$fieldsClass = hikamarket::get('shop.class.field');
		$categories = array();
		$fields = $fieldsClass->getData('backend_listing', 'product', false, $categories);
		$this->assignRef('fields', $fields);
		$this->assignRef('fieldsClass', $fieldsClass);

		foreach($fields as $fieldName => $oneExtraField) {
			$searchMap[] = 'product.' . $fieldName;
		}

		if($pageInfo->filter->vendors == 0) {
			$filters['main'] .= ' OR parent_product.product_id != 0 ) AND (product.product_vendor_id IN (0, 1)';
		} elseif( $pageInfo->filter->vendors > 1) {
			$filters['main'] .= ' OR parent_product.product_id != 0 ) AND (product.product_vendor_id = '.(int)$pageInfo->filter->vendors;
		}

		$order = '';
		$this->processFilters($filters, $order, $searchMap, array('product.'));

		$query = 'FROM '.hikamarket::table($cfg['table']).' AS product '.$join.$filters.$order;
		$db->setQuery('SELECT DISTINCT product.*' . (empty($select)?'':',') . implode(',', $select) . ' ' . $query, (int)$pageInfo->limit->start, (int)$pageInfo->limit->value);

		$rows = $db->loadObjectList();

		$products = array();
		$vendor_ids = array();
		foreach($rows as &$product) {
			$product->prices = array();
			$product->file_name = $product->product_name;
			if(!isset($products[$product->product_id])) {
				$products[$product->product_id] =& $product;
			} else if(!is_array($products[$product->product_id])) {
				$old =& $products[$product->product_id];
				unset($products[$product->product_id]);
				$products[$product->product_id] = array(&$old, &$product);
			} else {
				$products[$product->product_id][] =& $product;
			}

			if((int)$product->product_vendor_id > 0)
				$vendor_ids[ (int)$product->product_vendor_id ] = (int)$product->product_vendor_id;
		}
		unset($product);
		$this->assignRef('products', $rows);

		$this->loadPricesImages($products);

		$db->setQuery('SELECT COUNT(DISTINCT(product.product_id)) '.$query);
		$pageInfo->elements = new stdClass();
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);

		$vendors = array();
		if(!empty($vendor_ids)) {
			$query = 'SELECT vendor_id, vendor_name FROM ' . hikamarket::table('vendor') . ' WHERE vendor_id IN ('.implode(',', $vendor_ids).')';
			$db->setQuery($query);
			$vendors = $db->loadObjectList('vendor_id');
		}
		$this->assignRef('vendors', $vendors);

		$this->toolbar = array(
			'|',
			array('name' => 'pophelp', 'target' => 'vendor'),
			'dashboard'
		);
		$this->getPagination();

		$this->getOrdering('a.ordering', true);
		if(!empty($this->ordering->ordering)) {
			$this->toolbar['ordering']['display'] = true;
		}

		return true;
	}

	private function loadPricesImages(&$products) {
		if(empty($products))
			return;

		$db = JFactory::getDBO();
		$db->setQuery('SELECT * FROM '.hikamarket::table('shop.price').' WHERE price_product_id IN ('.implode(',', array_keys($products)).')');
		$prices = $db->loadObjectList();
		if(!empty($prices)) {
			foreach($prices as $price) {
				if(!isset($products[$price->price_product_id]) )
					continue;

				if(!is_array($products[$price->price_product_id])) {
					$products[$price->price_product_id]->prices[] = $price;
				} else {
					foreach($products[$price->price_product_id] as $p) {
						$p->prices[] = $price;
					}
				}
			}
		}
		unset($prices);

		$db->setQuery('SELECT * FROM '.hikamarket::table('shop.file').' WHERE file_ref_id IN ('.implode(',', array_keys($products)).') AND file_type=\'product\' ORDER BY file_ref_id ASC, file_ordering ASC, file_id ASC');
		$images = $db->loadObjectList();
		if(!empty($images)) {
			foreach($images as $image) {
				if(!isset($products[(int)$image->file_ref_id]))
					continue;

				if(!is_array($products[(int)$image->file_ref_id])) {
					if(isset($products[(int)$image->file_ref_id]->file_ref_id))
						continue;

					foreach(get_object_vars($image) as $key => $name) {
						$products[(int)$image->file_ref_id]->$key = $name;
					}
				} else {
					$p = reset($products[(int)$image->file_ref_id]);
					if(isset($p->file_ref_id))
						continue;

					foreach($products[(int)$image->file_ref_id] as $p) {
						foreach(get_object_vars($image) as $key => $name) {
							$p->$key = $name;
						}
					}
				}
			}
		}
	}

	public function listing() {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$singleSelection = JRequest::getVar('single', false);
		$confirm = JRequest::getVar('confirm', true, '', 'boolean');

		$elemStruct = array(
			'product_id',
			'product_name',
			'product_code'
		);

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();

		$pageInfo->search = $app->getUserStateFromRequest($this->paramBase.'.search', 'search', '', 'string');
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($this->paramBase.'.filter_order', 'filter_order', 'product.product_id','cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($this->paramBase.'.filter_order_dir', 'filter_order_dir', 'desc',	'word');
		$pageInfo->limit->value = $app->getUserStateFromRequest($this->paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		if(empty($pageInfo->limit->value))
			$pageInfo->limit->value = 500;
		$pageInfo->limit->start = $app->getUserStateFromRequest($this->paramBase.'.limitstart', 'limitstart', 0, 'int');
		$pageInfo->filter->filter_partner = $app->getUserStateFromRequest($this->paramBase.'.filter_partner', 'filter_partner', '', 'int');

		$filters = array();
		$searchMap = array(
			'product.product_name',
			'product.product_code'
		);

		if(!empty($pageInfo->search)){
			if(HIKASHOP_J30)
				$searchVal = '\'%'.$db->escape($pageInfo->search,true).'%\'';
			else
				$searchVal = '\'%'.$db->getEscaped($pageInfo->search,true).'%\'';
			$filters[] = '('.implode(' LIKE '.$searchVal.' OR ',$searchMap).' LIKE '.$searchVal.')';
		}

		$options = JRequest::getString('opt', '');
		if(!empty($options)) {
			$options = explode(',', $options);
			foreach($options as $option) {
				list($cmd, $val) = explode('-', $option, 2);
				switch($cmd) {
					case 'product_type':
						$f = substr($val,0,1);
						if($f == '!') {
							$filters[] = 'NOT (product.product_type = ' . $db->Quote(substr($val,1)) . ')';
						} else {
							$filters[] = '(product.product_type = ' . $db->Quote($val).')';
						}
						break;
				}
			}
		}

		$order = '';
		if(!empty($pageInfo->filter->order->value)){
			$order = ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}
		if(!empty($filters)){
			$filters = ' WHERE '. implode(' AND ',$filters);
		}else{
			$filters = '';
		}

		$query = ' FROM '.hikamarket::table('shop.product').' AS product '.$filters.$order;
		$db->setQuery('SELECT product.*'.$query, (int)$pageInfo->limit->start, (int)$pageInfo->limit->value);
		$rows = $db->loadObjectList();
		if(!empty($rows)) {
			foreach($rows as $k => $row) {
				if(!empty($row->user_params)) {
					$rows[$k]->user_params = hikamarket::unserialize($row->user_params);
				}
			}
		}

		$db->setQuery('SELECT COUNT(*)'.$query);
		$pageInfo->elements = new stdClass();
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);
		jimport('joomla.html.pagination');
		if($pageInfo->limit->value == 500)
			$pageInfo->limit->value = 100;
		$pagination = new JPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$this->assignRef('rows', $rows);
		$this->assignRef('singleSelection', $singleSelection);
		$this->assignRef('confirm', $confirm);
		$this->assignRef('elemStruct', $elemStruct);
		$this->assignRef('pageInfo', $pageInfo);
		$this->assignRef('pagination', $pagination);
	}

	public function selection(){
		$this->listing();
	}

	public function useselection() {
		$users = JRequest::getVar('cid', array(), '', 'array');
		$rows = array();
		$data = '';

		$elemStruct = array(
			'product_name',
			'product_code'
		);

		if(!empty($users)) {
			JArrayHelper::toInteger($users);
			$db = JFactory::getDBO();
			$query = 'SELECT a.*, b.* FROM '.hikamarket::table('user','shop').' AS a LEFT JOIN '.hikamarket::table('users', false).' AS b ON a.user_cms_id = b.id WHERE a.user_id IN ('.implode(',',$users).')';
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			if(!empty($rows)) {
				$data = array();
				foreach($rows as $v) {
					$d = '{id:'.$v->product_id;
					foreach($elemStruct as $s) {
						if($s == 'id')
							continue;
						$d .= ','.$s.':"'. str_replace('"', '\"', $v->$s).'"';
					}
					$data[] = $d.'}';
				}
				$data = '['.implode(',', $data).']';
			}
		}
		$this->assignRef('rows', $rows);
		$this->assignRef('data', $data);

		$confirm = JRequest::getVar('confirm', true, '', 'boolean');
		$this->assignRef('confirm', $confirm);
		if($confirm) {
			$js = 'window.addEvent("domready", function(){window.top.hikamarket.submitBox('.$data.');});';
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}
	}
}
