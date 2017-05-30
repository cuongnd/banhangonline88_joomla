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
class packViewPack extends hikaserialView {

	const ctrl = 'pack';
	const name = 'HIKA_PACKS';
	const icon = 'pack';

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKASERIAL_COMPONENT.'.'.$this->getName();
		$function = $this->getLayout();
		if(method_exists($this,$function))
			$this->$function();
		parent::display($tpl);
	}

	public function listing() {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		hikaserial::setTitle(JText::_(self::name), self::icon, self::ctrl);

		$config = hikaserial::config();
		$this->assignRef('config',$config);

		$toggleClass = hikaserial::get('helper.toggle');
		$this->assignRef('toggleClass', $toggleClass);
		$packGeneratorType = hikaserial::get('type.pack_generator');
		$this->assignRef('packGeneratorType', $packGeneratorType);
		$serialStatusType = hikaserial::get('type.serial_status');
		$this->assignRef('serialStatusType', $serialStatusType);

		$filterType = $app->getUserStateFromRequest($this->paramBase.".filter_type", 'filter_type', 0, 'int');

		$task = JRequest::getVar('task', '');
		if(empty($task))
			$task = 'listing';
		$this->assignRef('task', $task);

		$singleSelection = JRequest::getVar('single', false);
		$confirm = JRequest::getVar('confirm', true);

		$cfg = array(
			'table' => 'pack',
			'main_key' => 'pack_id',
			'order_sql_value' => 'a.pack_id'
		);

		$elemStruct = array(
			'pack_name',
			'pack_data',
			'pack_generator'
		);

		$manage = true;
		$this->assignRef('manage', $manage);

		$pageInfo = new stdClass();
		$pageInfo->search = $app->getUserStateFromRequest($this->paramBase.".search", 'search', '', 'string');

		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($this->paramBase.".filter_order", 'filter_order', 'a.pack_id','cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($this->paramBase.".filter_order_Dir", 'filter_order_Dir',	'desc',	'word');

		$pageInfo->limit = new stdClass();
		$pageInfo->limit->value = $app->getUserStateFromRequest($this->paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		if(empty($pageInfo->limit->value))
			$pageInfo->limit->value = 500;
		$pageInfo->limit->start = $app->getUserStateFromRequest($this->paramBase.'.limitstart', 'limitstart', 0, 'int');
		$pageInfo->filter->filter_partner = $app->getUserStateFromRequest($this->paramBase.".filter_partner",'filter_partner','','int');

		$pageInfo->search = JString::strtolower($app->getUserStateFromRequest($this->paramBase.".search", 'search', '', 'string'));
		$this->assignRef('pageInfo', $pageInfo);

		$filters = array();
		$searchMap = array(
			'a.pack_id',
			'a.pack_name',
			'a.pack_data',
			'a.pack_generator'
		);

		if(!empty($pageInfo->search)){
			if(!HIKASHOP_J30) {
				$searchVal = '\'%' . $db->getEscaped(JString::strtolower($pageInfo->search), true) . '%\'';
			} else {
				$searchVal = '\'%' . $db->escape(JString::strtolower($pageInfo->search), true) . '%\'';
			}
			$filters[] = '('.implode(' LIKE '.$searchVal.' OR ',$searchMap).' LIKE '.$searchVal.')';
		}
		if(!empty($filters)){
			$filters = ' WHERE '. implode(' AND ',$filters);
		} else {
			$filters = '';
		}

		$order = '';
		if(!empty($pageInfo->filter->order->value)){
			$order = ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$query = ' FROM '.hikaserial::table('pack').' AS a '.$filters.$order;
		$db->setQuery('SELECT * '.$query, (int)$pageInfo->limit->start, (int)$pageInfo->limit->value);

		$rows = $db->loadObjectList();
		if(!empty($pageInfo->search)) {
			$rows = hikaserial::search($pageInfo->search, $rows, 'pack_id');
		}
		$this->assignRef('rows', $rows);

		$db->setQuery('SELECT COUNT(*)'.$query);
		$pageInfo->elements = new stdClass();
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);

		$query = 'SELECT serial_pack_id, serial_status, COUNT(serial_id) AS counter FROM ' . hikaserial::table('serial') . ' GROUP BY serial_pack_id, serial_status ORDER BY serial_pack_id, serial_status';
		$db->setQuery($query);
		$dbcounters = $db->loadObjectList();
		$counters = array();
		foreach($dbcounters as $counter) {
			if(!isset($counters[$counter->serial_pack_id])) {
				$counters[$counter->serial_pack_id] = array();
			}
			$counters[$counter->serial_pack_id][$counter->serial_status] = $counter->counter;
		}
		unset($dbcounters);
		$this->assignRef('counters', $counters);

		$this->toolbar = array(
			array('name' => '|', 'display' => $manage),
			array('name' => 'publishList', 'display' => $manage),
			array('name' => 'unpublishList', 'display' => $manage),
			array('name' => 'addNew', 'display' => $manage),
			array('name' => 'editList', 'display' => $manage),
			array('name' => '|', 'display' => $manage),
			array('name' => 'deleteList', 'display' => $manage),
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

		$this->assignRef('singleSelection', $singleSelection);
		$this->assignRef('confirm', $confirm);
		$this->assignRef('elemStruct', $elemStruct);
		$this->assignRef('pageInfo', $pageInfo);
	}

	public function form() {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		hikaserial::loadJslib('tooltip');
		$ctrl = '';
		$cid = '';

		$cancelUrl = urlencode(base64_encode(hikaserial::completeLink('pack')));
		$this->assignRef('cancelUrl', $cancelUrl);

		$config = hikaserial::config();
		$this->assignRef('config',$config);

		$pack = new stdClass();
		$pack_id = hikaserial::getCID();
		if( !empty($pack_id)) {
			$packClass = hikaserial::get('class.pack');
			$pack = $packClass->get($pack_id);
			$task = 'edit';
			$cid = '&cid[]='.$pack_id;
			$cancelUrl = urlencode(base64_encode(hikaserial::completeLink('pack&task=edit&cid[]='.$pack_id)));
		} else {
			$task = 'add';
		}
		$ctrl .= '&task='.$task.$cid;
		$this->assignRef('task', $task);
		$this->assignRef('pack', $pack);

		$editor = hikaserial::get('shop.helper.editor');
		$editor->name = 'pack_description';
		$editor->content = @$pack->pack_description;
		$editor->height = 200;
		$this->assignRef('editor', $editor);

		$this->loadRef(array(
			'packGeneratorType' => 'type.pack_generator',
			'packDataType' => 'type.pack_data',
			'serialStatusType' => 'type.serial_status'
		));

		$query = 'SELECT a.*, b.* FROM ' . hikaserial::table('product_pack') . ' AS a INNER JOIN ' . hikaserial::table('shop.product') . ' AS b ON a.product_id = b.product_id WHERE a.pack_id = ' . $pack_id ;
		$db->setQuery($query);
		$products = $db->loadObjectList();
		$this->assignRef('products', $products);

		$query = 'SELECT serial_status, COUNT(serial_id) AS counter FROM ' . hikaserial::table('serial') . ' WHERE serial_pack_id = ' . $pack_id . ' GROUP BY serial_status ORDER BY serial_status';
		$db->setQuery($query);
		$dbcounters = $db->loadObjectList();
		$counters = array('total' => 0);
		foreach($dbcounters as $counter) {
			$counters['total'] += $counter->counter;
			$counters[$counter->serial_status] = $counter->counter;
		}
		unset($dbcounters);
		$this->assignRef('counters', $counters);

		$hikamarket = hikaserial::initMarket();
		$this->assignRef('hikamarket', $hikamarket);

		if(!empty($hikamarket)) {
			$nameboxMarketType = hikamarket::get('type.namebox');
			$this->assignRef('nameboxMarketType', $nameboxMarketType);
			$joomlaAclMarketType = hikamarket::get('type.joomla_acl');
			$this->assignRef('joomlaAclMarketType', $joomlaAclMarketType);
		}

		hikaserial::setTitle(JText::_(self::name), self::icon, self::ctrl.$ctrl);
		$this->toolbar = array(
			array('name' => 'link', 'icon' => 'upload', 'alt' => JText::_('GENERATE'), 'url' => hikaserial::completeLink('pack&task=generate&cid[]='.$pack_id), 'display' => !empty($pack->pack_generator)),
			'save',
			'apply',
			'hikacancel',
			'|',
			array('name' => 'pophelp', 'target' => self::ctrl.'-form')
		);
	}

	public function generate($tpl = null) {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		JHTML::_('behavior.modal');
		$ctrl = '';
		$pack_id = hikaserial::getCID();

		if(empty($pack_id)) {
			$app->redirect(hikaserial::completeLink('pack&task=list'));
		}

		$packClass = hikaserial::get('class.pack');
		$pack = $packClass->get($pack_id);
		$task = 'generate';
		$cid = '&cid[]='.$pack_id;
		$ctrl .= '&task='.$task.$cid;
		$this->assignRef('task', $task);
		$this->assignRef('pack', $pack);

		$generator = null;
		if(substr($pack->pack_generator, 0, 4) == 'plg.') {
			$pluginName = substr($pack->pack_generator, 4);
			if(strpos($pluginName,'-') !== false){
				list($pluginName,$pluginId) = explode('-', $pluginName, 2);
				$pack->$pluginName = $pluginId;
			}
			$generator = hikaserial::import('hikaserial', $pluginName);
		}

		if(!empty($generator) && (!method_exists($generator, 'canPopulate') || $generator->canPopulate() != true)) {
			$app->enqueueMessage(JText::_('PLUGIN_GENERATOR_CAN_NOT_POPULATE'));
			$app->redirect(hikaserial::completeLink('pack&task=edit&cid[]='.$pack_id, false, true));
		}

		$populateFormData = '';
		if(!empty($generator) && method_exists($generator, 'populateForm')) {
			$populateFormData = $generator->populateForm($pack);
		}
		$this->assignRef('populateFormData', $populateFormData);

		$packGeneratorType = hikaserial::get('type.pack_generator');
		$this->assignRef('packGeneratorType', $packGeneratorType);
		$serialStatusType = hikaserial::get('type.serial_status');
		$this->assignRef('serialStatusType', $serialStatusType);

		hikaserial::setTitle(JText::_(self::name), self::icon, self::ctrl.$ctrl);

		$this->toolbar = array(
			array('name' => 'custom', 'icon' => 'upload', 'task' => 'generate', 'alt' => JText::_('GENERATE'), 'check' => false),
			array('name' => 'hikacancel', 'url' => hikaserial::completeLink('pack&task=edit&cid[]='.$pack_id)),
			'|',
			array('name' => 'pophelp', 'target' => self::ctrl.'-form')
		);
	}

	public function select() {
		$this->listing();
	}

	public function useselection() {
		$packs = JRequest::getVar('cid', array(), '', 'array');
		$rows = array();
		$data = '';
		$confirm = JRequest::getVar('confirm', true);
		$singleSelection = JRequest::getVar('single', false);

		$elemStruct = array(
			'pack_name',
			'pack_data',
			'pack_generator'
		);

		if(!empty($packs)) {
			JArrayHelper::toInteger($users);
			$db = JFactory::getDBO();
			$query = 'SELECT a.* FROM '.hikaserial::table('pack').' AS a WHERE a.pack_id IN ('.implode(',',$packs).')';
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
}
