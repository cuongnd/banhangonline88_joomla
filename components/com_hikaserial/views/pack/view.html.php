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

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKASERIAL_COMPONENT.'.'.$this->getName();
		$function = $this->getLayout();
		if(method_exists($this,$function))
			$this->$function();
		parent::display($tpl);
	}

	public function select() {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$config = hikaserial::config();
		$this->assignRef('config',$config);

		$toggleClass = hikaserial::get('helper.toggle');
		$this->assignRef('toggleClass', $toggleClass);
		$packGeneratorType = hikaserial::get('type.pack_generator');
		$this->assignRef('packGeneratorType', $packGeneratorType);
		$serialStatusType = hikaserial::get('type.serial_status');
		$this->assignRef('serialStatusType', $serialStatusType);

		$filterType = $app->getUserStateFromRequest($this->paramBase.".filter_type", 'filter_type', 0, 'int');

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
			$searchVal = '\'%'.$db->getEscaped($pageInfo->search,true).'%\'';
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
