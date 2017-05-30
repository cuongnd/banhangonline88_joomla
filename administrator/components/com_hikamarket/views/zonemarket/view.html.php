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
class zonemarketViewzonemarket extends HikamarketView {

	const ctrl = 'zone';
	const name = 'HIKA_ZONE';
	const icon = 'generic';

	public function display($tpl = null) {
		$this->paramBase = HIKAMARKET_COMPONENT.'.'.$this->getName();
		$function = $this->getLayout();
		if(method_exists($this,$function))
			$this->$function();
		parent::display($tpl);
	}

	public function listing() {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$singleSelection = JRequest::getVar('single', 0);
		$confirm = JRequest::getVar('confirm', 1);

		$elemStruct = array(
			'zone_code_3',
			'zone_code_2',
			'zone_name_english',
			'zone_name',
			'zone_id'
		);

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();
		$pageInfo->search = $app->getUserStateFromRequest($this->paramBase.".search", 'search', '', 'string');
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($this->paramBase.".filter_order", 'filter_order', 'zone.zone_id','cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($this->paramBase.".filter_order_Dir", 'filter_order_Dir',	'desc', 'word');
		$pageInfo->limit->value = $app->getUserStateFromRequest($this->paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		if(empty($pageInfo->limit->value))
			$pageInfo->limit->value = 500;
		$pageInfo->limit->start = $app->getUserStateFromRequest($this->paramBase.'.limitstart', 'limitstart', 0, 'int');
		$pageInfo->filter->filter_partner = $app->getUserStateFromRequest($this->paramBase.".filter_partner",'filter_partner','','int');

		$join = '';
		$filters = array();
		$searchMap = array(
			'zone.zone_code_3',
			'zone.zone_code_2',
			'zone.zone_name_english',
			'zone.zone_name',
			'zone.zone_id'
		);

		$selectedType = $app->getUserStateFromRequest($this->paramBase.'.filter_type','filter_type','','string');
		if(!empty($selectedType)) {
			$filters[] = 'zone.zone_type = '.$db->Quote($selectedType);
			if($selectedType == 'state') {
				$selectedCountry = $app->getUserStateFromRequest($this->paramBase.'.filter_country','filter_country',0,'int');
				if($selectedCountry) {
					$join .= ' INNER JOIN '.hikamarket::table('shop.zone_link').' AS zl ON zl.zone_child_namekey = zone.zone_namekey '.
							' INNER JOIN '.hikamarket::table('shop.zone').' as z2 ON zl.zone_parent_namekey = z2.zone_namekey ';
					$filters[] = 'z2.zone_id = '.$db->Quote($selectedCountry);
				}
			}
		}


		if(!empty($pageInfo->search)) {
			if(HIKASHOP_J30)
				$searchVal = '\'%' . $db->escape(JString::strtolower($pageInfo->search), true) . '%\'';
			else
				$searchVal = '\'%' . $db->getEscaped(JString::strtolower($pageInfo->search), true) . '%\'';
			$filters[] = implode(' LIKE '.$searchVal.' OR ',$searchMap).' LIKE '.$searchVal;
		}
		$order = '';
		if(!empty($pageInfo->filter->order->value) && substr($pageInfo->filter->order->value, 0, 5) == 'zone.') {
			$order = ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}
		if(!empty($filters)) {
			$filters = ' WHERE '. implode(' AND ',$filters);
		} else {
			$filters = '';
		}

		$query = ' FROM '.hikamarket::table('shop.zone').' AS zone '.$join.$filters.$order;
		$db->setQuery('SELECT zone.*'.$query, (int)$pageInfo->limit->start, (int)$pageInfo->limit->value);
		$rows = $db->loadObjectList();
		$db->setQuery('SELECT COUNT(*)'.$query);
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);
		jimport('joomla.html.pagination');
		if($pageInfo->limit->value == 500)
			$pageInfo->limit->value = 100;
		$pagination = new JPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$filters = new stdClass();
		$zoneType = hikamarket::get('shop.type.zone');
		$filters->type = $zoneType->display('filter_type', $selectedType);
		if($selectedType == 'state') {
			$countryType = hikamarket::get('shop.type.country');
			$filters->country = $countryType->display('filter_country', $selectedCountry);
		}else{
			$filters->country = '';
		}
		$this->assignRef('filters',$filters);

		$this->assignRef('rows', $rows);
		$this->assignRef('singleSelection', $singleSelection);
		$this->assignRef('confirm', $confirm);
		$this->assignRef('elemStruct', $elemStruct);
		$this->assignRef('pageInfo', $pageInfo);
		$this->assignRef('pagination', $pagination);
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
		$zones = JRequest::getVar('cid', array(), '', 'array');
		$rows = array();
		$data = '';
		$confirm = JRequest::getVar('confirm', true);
		$singleSelection = JRequest::getVar('single', false);

		$elemStruct = array(
			'zone_code_3',
			'zone_code_2',
			'zone_name_english',
			'zone_name',
			'zone_id'
		);

		if(!empty($users)) {
			JArrayHelper::toInteger($zones);
			$db = JFactory::getDBO();
			$query = 'SELECT * FROM '.hikamarket::table('shop.zone').' WHERE zone_id IN ('.implode(',',$zones).')';
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
			$js = 'window.hikashop.ready(function(){window.top.hikamarket.submitBox('.$data.');});';
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}
	}
}
