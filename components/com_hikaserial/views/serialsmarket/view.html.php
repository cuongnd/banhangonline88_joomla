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
class serialsmarketViewserialsmarket extends hikamarketView {

	public function display($tpl = null, $params = null) {
		$this->params =& $params;
		$fct = $this->getLayout();
		if(method_exists($this, $fct))
			$this->$fct();
		parent::display($tpl);
	}

	public function packs($tpl = null) {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$ctrl = '';
		$this->paramBase = HIKAMARKET_COMPONENT.'.'.$this->getName().'.listing';

		$vendor = hikamarket::loadVendor(true, false);
		$this->assignRef('vendor', $vendor);

		$marketConfig = hikamarket::config();
		$this->assignRef('marketConfig', $marketConfig);

		hikamarket::loadJslib('tooltip');

		$this->loadRef(array(
			'toggleHelper' => 'helper.toggle'
		));

		global $Itemid;
		$url_itemid = '';
		if(!empty($Itemid))
			$url_itemid = '&Itemid='.$Itemid;
		$this->assignRef('Itemid', $Itemid);

		$cfg = array(
			'table' => 'pack',
			'main_key' => 'pack_id',
			'order_sql_value' => 'pack.pack_id'
		);

		$pageInfo = $this->getPageInfo($cfg['order_sql_value']);
		if(hikamarket::level(1)) {
			$pageInfo->filter->vendors = $app->getUserStateFromRequest($this->paramBase.'.filter_vendors', 'filter_vendors', 0, 'int');
		}

		$filters = array();
		$searchMap = array(
			'pack.pack_name',
			'pack.pack_id'
		);
		$order = '';
		$join = '';

		$target_vendor_id = 0;
		if(hikamarket::level(1)) {
			$acl = null;
			if($vendor->vendor_id > 1) {
				$target_vendor_id = $vendor->vendor_id;
				$acl = array();
				$accesses = explode(',', $vendor->vendor_access);
				foreach($accesses as $ax) {
					if(substr($ax,0,1) != '@')
						continue;
					$ax_id = (int)substr($ax,1);
					if($ax_id > 0)
						$acl[] = $ax_id;
				}
			} else {
				$vendorType = hikamarket::get('type.filter_vendor');
				$this->assignRef('vendorType', $vendorType);
				if($pageInfo->filter->vendors >= 0) {
					if($pageInfo->filter->vendors > 1)
						$target_vendor_id = (int)$pageInfo->filter->vendors;
					else
						$filters[] = 'pack.pack_vendor_id <= 1';
				}
			}

			if(!empty($target_vendor_id)) {
				$join = ' LEFT JOIN '.hikaserial::table('product_pack').' AS pp ON pack.pack_id = pp.pack_id '.
						' LEFT JOIN '.hikaserial::table('shop.product').' AS product ON pp.product_id = product.product_id ';
				$filters[] = '(pack.pack_vendor_id = ' . (int)$target_vendor_id.' OR product.product_vendor_id = '.(int)$target_vendor_id.')';
			}
		}

		$this->processFilters($filters, $order, $searchMap);

		$query = 'FROM '.hikaserial::table($cfg['table']).' AS pack '.$join.$filters.$order;
		$db->setQuery('SELECT pack.* '.$query, (int)$pageInfo->limit->start, (int)$pageInfo->limit->value);

		$rows = $db->loadObjectList($cfg['main_key']);
		$this->assignRef('packs', $rows);

		$db->setQuery('SELECT COUNT(*) '.$query);
		$pageInfo->elements = new stdClass();
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);

		if(!empty($rows)) {
			$pack_ids = array_keys($rows);
			JArrayHelper::toInteger($pack_ids);

			$own_packs = array();
			$other_packs = array();

			foreach($rows as &$row) {
				$row->pack_id = (int)$row->pack_id;
				if(!empty($row->pack_params) && is_string($row->pack_params))
					$row->pack_params = hikaserial::unserialize($row->pack_params);
				$row->products = array();
				$row->serials = array();
				$row->total_serials = 0;

				if($target_vendor_id > 0) {
					if((int)$row->pack_vendor_id == $target_vendor_id)
						$own_packs[ (int)$row->pack_id ] = (int)$row->pack_id;
					else
						$other_packs[ (int)$row->pack_id ] = (int)$row->pack_id;
				}
			}
			unset($row);

			if($target_vendor_id == 0) {
				$query = 'SELECT COUNT(serial.serial_id) as count, serial.serial_pack_id, serial.serial_status FROM '.hikaserial::table('serial').' AS serial '.
					' WHERE serial.serial_pack_id IN ('.implode(',', $pack_ids).') '.
					' GROUP BY serial.serial_pack_id, serial.serial_status;';
			} else {
				$query = '';
				$or_filters = array();

				if(!empty($own_packs)) {
					$or_filters[] = 'serial.serial_pack_id IN ('.implode(',', $own_packs).')';
				}
				if(!empty($other_packs)) {
					$or_filters[] = 'serial.serial_pack_id IN ('.implode(',', $other_packs).') AND hk_order.order_type = '.$db->Quote('subsale').' AND hk_order.order_vendor_id = '.(int)$target_vendor_id.'';
				}

				if(!empty($or_filters)) {
					$query = 'SELECT COUNT(serial.serial_id) as count, serial.serial_pack_id, serial.serial_status '.
						' FROM '.hikaserial::table('serial').' AS serial '.
						' LEFT JOIN '.hikaserial::table('shop.order').' AS hk_order ON (serial.serial_order_id > 0 AND serial.serial_order_id = hk_order.order_parent_id) '.
						' WHERE ('.implode(') OR (', $or_filters) . ') '.
						' GROUP BY serial.serial_pack_id, serial.serial_status;';
				}
			}
			if(!empty($query)) {
				$db->setQuery($query);
				$serials_stats = $db->loadObjectList();
			} else
				$serials_stats = array();

			foreach($serials_stats as $stat) {
				if((int)$stat->serial_pack_id == 0 || !isset($rows[ (int)$stat->serial_pack_id ]))
					continue;
				$rows[ (int)$stat->serial_pack_id ]->serials[] = $stat;
				$rows[ (int)$stat->serial_pack_id ]->total_serials += (int)$stat->count;
			}

			$filters = array(
				'pack_id' => 'pp.pack_id IN ('.implode(',', $pack_ids).')',
				'vendor' => 'product.product_vendor_id = '.$target_vendor_id,
				'product_type' => 'product.product_type = '.$db->Quote('main')
			);
			if($target_vendor_id == 0)
				unset($filters['vendor']);

			$query = 'SELECT pp.pack_id, pp.quantity, product.product_id, product.product_name, product.product_code, product.product_published, product_vendor_id '.
					' FROM '.hikaserial::table('product_pack').' AS pp '.
					' INNER JOIN '.hikaserial::table('shop.product').' AS product ON product.product_id = pp.product_id '.
					' WHERE ('.implode(') AND (', $filters).')';
			$db->setQuery($query);
			$product_stats = $db->loadObjectList();

			foreach($product_stats as $stat) {
				$rows[ (int)$stat->pack_id ]->products[] = $stat;
			}
		}

		$this->toolbar = array(
			array(
				'icon' => 'back',
				'name' => JText::_('HIKA_BACK'),
				'url' => hikamarket::completeLink('vendor')
			),
			array(
				'icon' => 'new',
				'name' => JText::_('HIKA_NEW'),
				'url' => hikamarket::completeLink('serials&task=packadd'),
				'pos' => 'right',
				'acl' => hikamarket::acl('plugins/hikaserial/pack/add')
			)
		);

		$this->getPagination();
	}

	public function pack($tpl = null) {
		$vendor = hikamarket::loadVendor(true, false);
		$this->assignRef('vendor', $vendor);

		$marketConfig = hikamarket::config();
		$this->assignRef('marketConfig', $marketConfig);

		$this->loadRef(array(
			'packClass' => 'serial.class.pack',
			'editor' => 'shop.helper.editor',
			'packDataType' => 'serial.type.pack_data',
			'packGeneratorType' => 'serial.type.pack_generator',
			'nameboxMarketType' => 'type.namebox'
		));

		$pack_id = hikamarket::getCID('pack_id');
		$pack = $this->packClass->get($pack_id);
		$this->assignRef('pack', $pack);

		$this->editor->setEditor($marketConfig->get('editor', ''));
		$this->editor->name = 'pack_description';
		$this->editor->content = @$pack->pack_description;
		$this->editor->height = 150;
		if($marketConfig->get('editor_disable_buttons', 0))
			$this->editor->options = false;

		$this->toolbar = array(
			array(
				'icon' => 'back',
				'name' => JText::_('HIKA_BACK'),
				'url' => hikamarket::completeLink('serials')
			),
			'apply' => array(
				'url' => '#apply',
				'linkattribs' => 'onclick="return window.hikamarket.submitform(\'packapply\',\'hikamarket_pack_form\');"',
				'icon' => 'apply',
				'name' => JText::_('HIKA_APPLY'), 'pos' => 'right'
			),
			'save' => array(
				'url' => '#save',
				'linkattribs' => 'onclick="return window.hikamarket.submitform(\'packsave\',\'hikamarket_pack_form\');"',
				'icon' => 'save',
				'name' => JText::_('HIKA_SAVE'), 'pos' => 'right'
			)
		);
	}

	public function stats($tpl = null) {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$ctrl = '';
		$this->paramBase = HIKAMARKET_COMPONENT.'.'.$this->getName().'.listing';

		$vendor = hikamarket::loadVendor(true, false);
		$this->assignRef('vendor', $vendor);

		$marketConfig = hikamarket::config();
		$this->assignRef('marketConfig', $marketConfig);

		hikamarket::loadJslib('tooltip');

		$pack_id = JRequest::getInt('pack_id', 0);
		$this->assignRef('pack_id', $pack_id);
		$product_id = JRequest::getInt('product_id', 0);
		$this->assignRef('product_id', $product_id);

		$acls = array(
			'user/show' => hikamarket::acl('user/show'),
			'user/listing' => hikamarket::acl('user/listing'),
			'order/show' => hikamarket::acl('order/show'),
			'order/listing' => hikamarket::acl('order/listing'),
		);
		$this->assignRef('acls', $acls);

		global $Itemid;
		$url_itemid = '';
		if(!empty($Itemid))
			$url_itemid = '&Itemid='.$Itemid;
		$this->assignRef('Itemid', $Itemid);

		$cfg = array(
			'table' => 'serial',
			'main_key' => 'serial_id',
			'order_sql_value' => 'serial.serial_id'
		);

		$pageInfo = $this->getPageInfo($cfg['order_sql_value']);
		$pageInfo->limit->value = $app->getUserStateFromRequest($this->paramBase.'.list_limit', 'limit', $app->getCfg('list_limit')*2, 'int');

		if(hikamarket::level(1)) {
			$productClass = hikaserial::get('shop.class.product');
			$product = $productClass->get($product_id);
			if((int)$product->product_vendor_id > 1)
				$pageInfo->filter->vendors = (int)$product->product_vendor_id;
		}

		$filters = array(
			'serial_pack' => 'serial.serial_pack_id = '.(int)$pack_id
		);
		$searchMap = array();
		$order = '';
		$join = '';

		if(!empty($pageInfo->filter->vendors) && $pageInfo->filter->vendors > 1) {
			$join = ' INNER JOIN '.hikaserial::table('shop.order').' AS hk_order ON serial.serial_order_id = hk_order.order_parent_id '.
					' INNER JOIN '.hikaserial::table('shop.order_product').' AS hk_order_product ON hk_order_product.order_product_id = serial.serial_order_product_id ';
			$filters = array_merge($filters, array(
				'hk_order.order_type = '.$db->Quote('subsale'),
				'hk_order.order_vendor_id = '.(int)$pageInfo->filter->vendors,
				'hk_order_product.product_id = '.(int)$product_id,
			));
		} else {
			$join = ' INNER JOIN '.hikaserial::table('shop.order').' AS hk_order ON serial.serial_order_id = hk_order.order_id '.
					' INNER JOIN '.hikaserial::table('shop.order_product').' AS hk_order_product ON hk_order_product.order_product_id = serial.serial_order_product_id ';
			$filters = array_merge($filters, array(
				'hk_order.order_type = '.$db->Quote('sale'),
				'hk_order.order_vendor_id = '.(int)$pageInfo->filter->vendors,
				'hk_order_product.product_id = '.(int)$product_id,
			));
		}

		$this->processFilters($filters, $order, $searchMap);

		$query = 'FROM '.hikaserial::table($cfg['table']).' AS serial '.$join.$filters.$order;
		$db->setQuery('SELECT serial.*, hk_order.order_id '.$query, (int)$pageInfo->limit->start, (int)$pageInfo->limit->value);

		$rows = $db->loadObjectList($cfg['main_key']);
		$this->assignRef('serials', $rows);

		$db->setQuery('SELECT COUNT(*) '.$query);
		$pageInfo->elements = new stdClass();
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);

		if(!empty($rows)) {
			$user_id = array();
			$order_id = array();
			foreach($rows as &$row) {
				$user_id[ (int)$row->serial_user_id ] = (int)$row->serial_user_id;
				$order_id[ (int)$row->order_id ] = (int)$row->order_id;
			}

			$users = array();
			if(!empty($user_id)) {
				$query = 'SELECT hk_user.user_id, juser.name, hk_user.user_email '.
					' FROM '.hikaserial::table('shop.user').' AS hk_user '.
					' LEFT JOIN '.hikaserial::table('joomla.users').' AS juser ON hk_user.user_cms_id = juser.id '.
					' WHERE hk_user.user_id IN ('.implode(',', $user_id).')';
				$db->setQuery($query);
				$users = $db->loadObjectList('user_id');
			}
			$this->assignRef('users', $users);

			$orders = array();
			if(!empty($order_id)) {
				$query = 'SELECT hk_order.order_number, hk_order.order_id '.
					' FROM '.hikaserial::table('shop.order').' AS hk_order '.
					' WHERE hk_order.order_id IN ('.implode(',', $order_id).')';
				$db->setQuery($query);
				$orders = $db->loadObjectList('order_id');
			}
			$this->assignRef('orders', $orders);
		}

		$this->toolbar = array(
			array(
				'icon' => 'back',
				'name' => JText::_('HIKA_BACK'),
				'url' => hikamarket::completeLink('serials&task=packs')
			),
			array(
				'icon' => 'report',
				'name' => JText::_('HIKA_EXPORT'),
				'url' => hikamarket::completeLink('serials&task=export&pack_id='.$pack_id.'&product_id='.$product_id),
				'pos' => 'right',
				'acl' => hikamarket::acl('plugins/hikaserial/serial/export')
			),
		);

		$this->getPagination();
	}

	public function export($tpl = null) {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$vendor = hikamarket::loadVendor(true, false);
		$this->assignRef('vendor', $vendor);

		$marketConfig = hikamarket::config();
		$this->assignRef('marketConfig', $marketConfig);

		$pack_id = JRequest::getInt('pack_id', 0);
		$this->assignRef('pack_id', $pack_id);
		$product_id = JRequest::getInt('product_id', 0);
		$this->assignRef('product_id', $product_id);

		$acls = array(
			'user/show' => hikamarket::acl('user/show'),
			'user/listing' => hikamarket::acl('user/listing'),
			'order/show' => hikamarket::acl('order/show'),
			'order/listing' => hikamarket::acl('order/listing'),
		);

		$target_vendor_id = 0;
		if(hikamarket::level(1)) {
			$productClass = hikaserial::get('shop.class.product');
			$product = $productClass->get($product_id);
			if((int)$product->product_vendor_id > 1)
				$target_vendor_id = (int)$product->product_vendor_id;
		}

		$select = array(
			'serial.serial_data',
			'serial.serial_status'
		);
		$tables = array(
			hikaserial::table('serial').' AS serial'
		);
		$filters = array(
			'serial.serial_pack_id = '.(int)$pack_id
		);

		if($target_vendor_id == 0) {
			$tables[] = 'INNER JOIN '.hikaserial::table('shop.order').' AS hk_order ON serial.serial_order_id = hk_order.order_id';
			$tables[] = 'INNER JOIN '.hikaserial::table('shop.order_product').' AS hk_order_product '.
						' ON (hk_order_product.order_product_id = serial.serial_order_product_id AND hk_order_product.order_id = hk_order.order_id)';

			$filters = array_merge($filters, array(
				'hk_order.order_type = '.$db->Quote('sale'),
				'hk_order.order_vendor_id = '.(int)$target_vendor_id,
				'hk_order_product.product_id = '.(int)$product_id,
			));
		} else {
			$tables[] = 'INNER JOIN '.hikaserial::table('shop.order').' AS hk_order ON serial.serial_order_id = hk_order.order_parent_id';
			$tables[] = 'INNER JOIN '.hikaserial::table('shop.order_product').' AS hk_order_product '.
						' ON (hk_order_product.order_product_id = serial.serial_order_product_id AND hk_order_product.order_id = hk_order.order_parent_id)';

			$filters = array_merge($filters, array(
				'hk_order.order_type = '.$db->Quote('subsale'),
				'hk_order.order_vendor_id = '.(int)$target_vendor_id,
				'hk_order_product.product_id = '.(int)$product_id,
			));
		}

		if($acls['user/show']) {
			$tables[] = 'LEFT JOIN '.hikaserial::table('shop.user').' AS hk_user ON hk_order.order_user_id = hk_user.user_id';
			$select[] = 'hk_user.user_email';
		}

		if($acls['order/show']) {
			$select[] = 'hk_order.order_number';
		}

		$query = 'SELECT '.implode(',', $select).' FROM '.implode(' ', $tables).' WHERE ('.implode(') AND (', $filters).')';
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$this->assignRef('rows', $rows);
	}

	public function import($tpl = null) {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$vendor = hikamarket::loadVendor(true, false);
		$this->assignRef('vendor', $vendor);

		$marketConfig = hikamarket::config();
		$this->assignRef('marketConfig', $marketConfig);

		$pack_id = JRequest::getInt('pack_id', 0);
		$packClass = hikaserial::get('class.pack');
		$pack = $packClass->get($pack_id);
		$this->assignRef('pack', $pack);

		$this->toolbar = array(
			array(
				'icon' => 'back',
				'name' => JText::_('HIKA_BACK'),
				'url' => hikamarket::completeLink('serials&task=packs')
			),
			array(
				'icon' => 'import',
				'name' => JText::_('IMPORT'),
				'url' => '#import',
				'linkattribs' => 'onclick="return window.hikamarket.submitform(\'import\',\'hikamarket_serial_import_form\');"',
				'pos' => 'right',
				'acl' => hikamarket::acl('plugins/hikaserial/serial/import')
			),
		);
	}
}
