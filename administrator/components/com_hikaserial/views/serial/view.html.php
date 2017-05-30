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
class serialViewSerial extends hikaserialView {

	const ctrl = 'serial';
	const name = 'HIKA_SERIALS';
	const icon = 'serial';

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKASERIAL_COMPONENT.'.'.$this->getName();
		$fct = $this->getLayout();
		if(method_exists($this, $fct)) {
			if($this->$fct($params) === false)
				return false;
		}
		parent::display($tpl);
	}

	public function listing($tpl = null) {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		hikaserial::setTitle(JText::_(self::name), self::icon, self::ctrl);

		$config = hikaserial::config();
		$this->assignRef('config', $config);
		$shopConfig = hikaserial::config(false);
		$this->assignRef('shopConfig', $shopConfig);

		$serialStatusType = hikaserial::get('type.serial_status');
		$this->assignRef('serialStatusType', $serialStatusType);
		$packType = hikaserial::get('type.pack');
		$this->assignRef('packType', $packType);

		$task = JRequest::getVar('task', '');
		if(empty($task))
			$task = 'listing';
		$this->assignRef('task', $task);

		$filterType = $app->getUserStateFromRequest($this->paramBase.'.filter_type', 'filter_type', 0, 'int');

		$singleSelection = JRequest::getVar('single', false);
		$this->assignRef('singleSelection', $singleSelection);
		$confirm = JRequest::getVar('confirm', true);
		$this->assignRef('confirm', $confirm);

		$elemStruct = array(
			'serial_data',
			'serial_id',
			'serial_pack_id',
			'serial_status',
			'pack_name'
		);
		$this->assignRef('elemStruct', $elemStruct);

		$cfg = array(
			'table' => 'serial',
			'main_key' => 'serial_id',
			'order_sql_value' => 'a.serial_id'
		);

		$manage = true; // TODO
		$this->assignRef('manage', $manage);
		$manage_shop_order = hikaserial::isAllowed($shopConfig->get('acl_order_manage', 'all'));
		$this->assignRef('manage_shop_order', $manage_shop_order);
		$manage_shop_user = hikaserial::isAllowed($shopConfig->get('acl_user_manage', 'all'));
		$this->assignRef('manage_shop_user', $manage_shop_user);

		$pageInfo = new stdClass();
		$filters = array();

		$oldFilters = new stdClass();
		$oldFilters->serial_status = $app->getUserState($this->paramBase.'.filter_status', '');
		$oldFilters->pack = $app->getUserState($this->paramBase.'.filter_pack', '');

		$pageInfo->filter = new stdClass();
		$pageInfo->filter->serial_status = $app->getUserStateFromRequest($this->paramBase.'.filter_status', 'filter_status', '', 'string');
		$pageInfo->filter->pack = $app->getUserStateFromRequest($this->paramBase.'.filter_pack', 'filter_pack', '', 'string');
		$pageInfo->filter->order = new stdClass();
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($this->paramBase.'.filter_order', 'filter_order', $cfg['order_sql_value'], 'cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($this->paramBase.'.filter_order_Dir', 'filter_order_Dir', 'asc', 'word');

		$pageInfo->limit = new stdClass();
		$pageInfo->limit->value = $app->getUserStateFromRequest($this->paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		if(empty($pageInfo->limit->value))
			$pageInfo->limit->value = 500;
		if((JRequest::getVar('search') != $app->getUserState($this->paramBase.'.search')) || ($pageInfo->filter->serial_status != $oldFilters->serial_status) || ($pageInfo->filter->pack != $oldFilters->pack)) {
			$app->setUserState($this->paramBase.'.limitstart', 0);
			$pageInfo->limit->start = 0;
		} else {
			$pageInfo->limit->start = $app->getUserStateFromRequest($this->paramBase.'.limitstart', 'limitstart', 0, 'int');
		}

		$pageInfo->search = JString::strtolower($app->getUserStateFromRequest($this->paramBase.'.search', 'search', '', 'string'));
		$this->assignRef('pageInfo', $pageInfo);

		$filters = array();
		$searchMap = array(
			'a.serial_id',
			'a.serial_data',
			'a.serial_status',
			'b.pack_name',
			'd.username'
		);

		if(!empty($pageInfo->search)) {
			if(!HIKASHOP_J30) {
				$searchVal = '\'%' . $db->getEscaped(JString::strtolower($pageInfo->search), true) . '%\'';
			} else {
				$searchVal = '\'%' . $db->escape(JString::strtolower($pageInfo->search), true) . '%\'';
			}
			$filters[] = '('.implode(' LIKE '.$searchVal.' OR ',$searchMap).' LIKE '.$searchVal.')';
		}
		if(!empty($pageInfo->filter->serial_status)) {
			$filters[] = ' a.serial_status = ' . $db->quote($pageInfo->filter->serial_status);
		}
		if(!empty($pageInfo->filter->pack)) {
			if((int)$pageInfo->filter->pack > 0) {
				$filters[] = ' b.pack_id = ' . (int)$pageInfo->filter->pack;
			} else {
				$filters[] = ' b.pack_name = ' . $db->quote($pageInfo->filter->pack);
			}
		}
		if(!empty($filters)) {
			$filters = ' WHERE '. implode(' AND ', $filters);
		} else {
			$filters = '';
		}

		$order = '';
		if(!empty($pageInfo->filter->order->value)) {
			$order = ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$query = 'FROM '.hikaserial::table($cfg['table']).' AS a INNER JOIN '.
			hikaserial::table('pack') . ' AS b ON a.serial_pack_id = b.pack_id LEFT JOIN '.
			hikaserial::table('shop.user') . ' AS c ON a.serial_user_id = c.user_id LEFT JOIN '.
			hikaserial::table('users', false) . ' AS d ON c.user_cms_id = d.id LEFT JOIN '.
			hikaserial::table('shop.order') . ' AS e ON a.serial_order_id = e.order_id '.
			$filters.$order;
		$db->setQuery('SELECT * '.$query, (int)$pageInfo->limit->start, (int)$pageInfo->limit->value);

		$rows = $db->loadObjectList();
		if(!empty($pageInfo->search)) {
			$rows = hikaserial::search($pageInfo->search, $rows, $cfg['main_key']);
		}
		$this->assignRef('rows',$rows);

		JPluginHelper::importPlugin('hikaserial');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onDisplaySerials', array(&$rows, 'back-serial-listing'));

		$db->setQuery('SELECT COUNT(*) '.$query);
		$pageInfo->elements = new stdClass();
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);

		$this->toolbar = array(
			array(
				'name' => 'popup',
				'alt' => 'HIKA_EXPORT',
				'icon' => 'export',
				'url' => ('index.php?option='.HIKASERIAL_COMPONENT.'&ctrl=serial&task=exportconf&tmpl=component'),
				'title' => 'HIKA_EXPORT',
				'footer' => true,
				'width' => 480, 'height' => 260
			),
			'|',
			'addNew',
			'editList',
			array('name' => 'deleteList'),
			'|',
			array('name' => 'pophelp', 'target' => self::ctrl),
			'dashboard'
		);

		jimport('joomla.html.pagination');
		if($pageInfo->limit->value == 500)
			$pageInfo->limit->value = 100;
		$pagination = new JPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);
		$this->assignRef('pagination', $pagination);

		$doOrdering = !$filterType;
		$this->assignRef('doOrdering', $doOrdering);
		if($doOrdering) {
			$ordering = new stdClass();
			$ordering->ordering = false;
			$ordering->orderUp = 'orderup';
			$ordering->orderDown = 'orderdown';
			$ordering->reverse = false;
			if($pageInfo->filter->order->value == 'a.ordering') {
				$ordering->ordering = true;
				if($pageInfo->filter->order->dir == 'desc') {
					$ordering->orderUp = 'orderdown';
					$ordering->orderDown = 'orderup';
					$ordering->reverse = true;
				}
			}
			$this->assignRef('ordering', $ordering);
		}
	}

	public function select() {
		$this->listing();
	}

	public function useselection() {
		$serials = JRequest::getVar('cid', array(), '', 'array');
		$rows = array();
		$data = '';
		$confirm = JRequest::getVar('confirm', true);
		$singleSelection = JRequest::getVar('single', false);

		$elemStruct = array(
			'serial_data',
			'pack_id',
			'pack_name',
			'pack_data',
			'pack_generator'
		);

		if(!empty($serials)) {
			JArrayHelper::toInteger($users);
			$db = JFactory::getDBO();
			$query = 'SELECT a.* FROM '.hikaserial::table('serial').' AS a INNER JOIN '.hikaserial::table('pack').' AS b ON a.serial_pack_id = b.pack_id WHERE a.serial_id IN ('.implode(',',$serials).')';
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			if(!empty($rows)) {
				$data = array();
				foreach($rows as $v) {
					$d = '{id:'.$v->pack_id;
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
			$js = 'window.hikashop.ready(function(){window.top.hikaserial.submitBox('.$data.');});';
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}
	}

	public function applyexportconf() {
		$data = '';
		$formData = JRequest::getVar('data', array(), '', 'array');
		if(!empty($formData['export'])) {
			$data = array();
			foreach($formData['export'] as $key => $value) {
				$data[] = $key . '=' . $value;
			}
			$data = implode(';', $data);
		}

		$js = 'window.hikashop.ready(function(){'.
			' var el = window.top.document.getElementById("hikaserial_export_data");'.
			' if(el) { el.value = "'.$data.'"; }'.
			' el.form["task"].value = "export";'.
			' el.form.submit();'.
			' window.top.hikashop.closeBox();'.
			' el.form["task"].value = "";'.
			'});';
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
		return false;
	}

	public function export() {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$ids = JRequest::getVar('cid', array(), '', 'array');

		$export_params = array();
		$export_data = JRequest::getString('export_data', '');
		if(!empty($export_data)) {
			$export_datas = explode(';', $export_data);
			foreach($export_datas as $d) {
				if(strpos($d, '=') !== false) {
					list($k, $v) = explode('=', $d, 2);
					$export_params[$k] = $v;
				}
			}
		}
		$this->assignRef('export_params', $export_params);

		$filters = array();

		if(empty($ids)) {
			$pageInfo = new stdClass();

			$pageInfo->filter = new stdClass();
			$pageInfo->filter->serial_status = $app->getUserStateFromRequest($this->paramBase.'.filter_status', 'filter_status', '', 'string');
			$pageInfo->filter->pack = $app->getUserStateFromRequest($this->paramBase.'.filter_pack', 'filter_pack', '', 'string');

			$pageInfo->search = JString::strtolower($app->getUserStateFromRequest($this->paramBase.'.search', 'search', '', 'string'));

			$searchMap = array(
				'serial.serial_id',
				'serial.serial_data',
				'serial.serial_status',
				'pack.pack_name',
				'user.username'
			);

			if(!empty($pageInfo->search)) {
				$searchVal = '\'%' . $db->getEscaped(JString::strtolower($pageInfo->search), true) . '%\'';
				$filters[] = '('.implode(' LIKE '.$searchVal.' OR ', $searchMap).' LIKE '.$searchVal.')';
			}
			if(!empty($pageInfo->filter->serial_status)) {
				$filters[] = ' serial.serial_status = ' . $db->quote($pageInfo->filter->serial_status);
			}
			if(!empty($pageInfo->filter->pack)) {
				if((int)$pageInfo->filter->pack > 0) {
					$filters[] = ' pack.pack_id = ' . (int)$pageInfo->filter->pack;
				} else {
					$filters[] = ' pack.pack_name = ' . $db->quote($pageInfo->filter->pack);
				}
			}
		} else {
			JArrayHelper::toInteger($ids, 0);
			$filters[] = 'serial.serial_id IN ('.implode(',',$ids).')';
		}

		if(!empty($filters)) {
			$filters = ' WHERE '. implode(' AND ', $filters);
		} else {
			$filters = '';
		}

		$query = 'FROM '.hikaserial::table('serial').' AS serial INNER JOIN '.
			hikaserial::table('pack') . ' AS pack ON serial.serial_pack_id = pack.pack_id LEFT JOIN '.
			hikaserial::table('shop.user') . ' AS user ON serial.serial_user_id = user.user_id LEFT JOIN '.
			hikaserial::table('users', false) . ' AS users ON user.user_cms_id = users.id LEFT JOIN '.
			hikaserial::table('shop.order') . ' AS shop_order ON serial.serial_order_id = shop_order.order_id '.
			$filters;

		$db->setQuery('SELECT * '.$query);
		$rows = $db->loadObjectList();
		$this->assignRef('rows',$rows);
	}

	public function form() {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		JHTML::_('behavior.modal');
		$ctrl = '';
		$cid = '';

		$cancelUrl = urlencode(base64_encode(hikaserial::completeLink('serial')));
		$this->assignRef('cancelUrl', $cancelUrl);

		$config = hikaserial::config();
		$this->assignRef('config',$config);
		$shopConfig = hikaserial::config(false);
		$this->assignRef('shopConfig', $shopConfig);

		$popup = hikaserial::get('shop.helper.popup');
		$this->assignRef('popup', $popup);

		$manage = true; // TODO
		$this->assignRef('manage', $manage);
		$manage_shop_order = hikaserial::isAllowed($shopConfig->get('acl_order_manage', 'all'));
		$this->assignRef('manage_shop_order', $manage_shop_order);
		$manage_shop_user = hikaserial::isAllowed($shopConfig->get('acl_user_manage', 'all'));
		$this->assignRef('manage_shop_user', $manage_shop_user);

		$serial = null;
		$serial_id = hikaserial::getCID();
		if( !empty($serial_id)) {
			$serialClass = hikaserial::get('class.serial');
			$serial = $serialClass->get($serial_id, true);
			$task = 'edit';
			$cid = '&cid[]='.$serial_id;
			$cancelUrl = urlencode(base64_encode(hikaserial::completeLink('serial&task=edit&cid[]='.$serial_id)));
		} else {
			$task = 'add';
		}
		$ctrl .= '&task='.$task.$cid;
		$this->assignRef('task', $task);
		$this->assignRef('serial', $serial);

		JPluginHelper::importPlugin('hikaserial');
		$dispatcher = JDispatcher::getInstance();
		$serials = array(&$serial);
		$dispatcher->trigger('onDisplaySerials', array(&$serials, 'back-serial-form'));

		$packType = hikaserial::get('type.pack');
		$this->assignRef('packType', $packType);

		$serialStatusType = hikaserial::get('type.serial_status');
		$this->assignRef('serialStatusType', $serialStatusType);

		hikaserial::setTitle(JText::_(self::name), self::icon, self::ctrl.$ctrl);
		$this->toolbar = array(
			'|',
			'save',
			'apply',
			'hikacancel',
			'|',
			array('name' => 'pophelp', 'target' => self::ctrl.'-form')
		);
	}
}
