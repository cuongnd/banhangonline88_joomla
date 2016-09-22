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
class usersmarketViewUsersmarket extends hikamarketView {

	const ctrl = 'users';
	const name = 'HIKA_USERS';
	const icon = 'generic';

	public function display($tpl = null)
	{
		$this->paramBase = HIKAMARKET_COMPONENT.'.'.$this->getName();
		$function = $this->getLayout();
		if(method_exists($this,$function))
			$this->$function();
		parent::display($tpl);
	}

	public function listing() {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$fieldsClass = hikamarket::get('shop.class.field');

		$fields = $fieldsClass->getData('backend_listing', 'user', false);
		$singleSelection = JRequest::getVar('single', false);
		$confirm = JRequest::getVar('confirm', true, '', 'boolean');

		$elemStruct = array(
			'user_email',
			'user_cms_id',
			'name',
			'username',
			'email'
		);

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();

		$pageInfo->search = $app->getUserStateFromRequest($this->paramBase.'.search', 'search', '', 'string');
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($this->paramBase.'.filter_order', 'filter_order', 'hu.user_id','cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($this->paramBase.'.filter_order_dir', 'filter_order_dir', 'desc',	'word');
		$pageInfo->limit->value = $app->getUserStateFromRequest($this->paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		if(empty($pageInfo->limit->value))
			$pageInfo->limit->value = 500;
		$pageInfo->limit->start = $app->getUserStateFromRequest($this->paramBase.'.limitstart', 'limitstart', 0, 'int');
		$pageInfo->filter->filter_partner = $app->getUserStateFromRequest($this->paramBase.'.filter_partner', 'filter_partner', '', 'int');

		$filters = array();
		$searchMap = array(
			'hu.user_id',
			'hu.user_email',
			'u.username',
			'u.email',
			'u.name'
		);
		foreach($fields as $field) {
			$searchMap[] = 'hu.'.$field->field_namekey;
		}

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
					case 'user_vendor_id':
						$f = substr($val,0,1);
						if($f == '!') {
							$filters[] = 'NOT (hu.user_vendor_id = ' . (int)$val . ')';
						} else {
							$filters[] = '(hu.user_vendor_id = ' . (int)$val.')';
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

		$query = ' FROM '.hikamarket::table('user','shop').' AS hu LEFT JOIN '.hikamarket::table('users',false).' AS u ON hu.user_cms_id = u.id '.$filters.$order;
		$db->setQuery('SELECT hu.*,u.*'.$query, (int)$pageInfo->limit->start, (int)$pageInfo->limit->value);
		$rows = $db->loadObjectList();
		$fieldsClass->handleZoneListing($fields, $rows);
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
		$this->assignRef('fieldsClass', $fieldsClass);
		$this->assignRef('fields', $fields);
	}

	public function selection(){
		$this->listing();
	}

	public function useselection() {
		$users = JRequest::getVar('cid', array(), '', 'array');
		$rows = array();
		$data = '';

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
			$query = 'SELECT a.*, b.* FROM '.hikamarket::table('user','shop').' AS a LEFT JOIN '.hikamarket::table('users', false).' AS b ON a.user_cms_id = b.id WHERE a.user_id IN ('.implode(',',$users).')';
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			if(!empty($rows)) {
				$data = array();
				foreach($rows as $v) {
					$d = '{id:'.$v->user_id;
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
