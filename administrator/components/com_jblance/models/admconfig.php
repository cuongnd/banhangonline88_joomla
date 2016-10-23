<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	14 March 2012
 * @file name	:	models/admconfig.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Entry point for the component (jblance)
 */
 defined('_JEXEC') or die('Restricted access');

 jimport('joomla.application.component.model');
 
class JblanceModelAdmconfig extends JModelLegacy {
	function __construct(){
		parent :: __construct();
		//$user	= JFactory::getUser();
	}
	
	function getConfig(){
	
		$row = JTable::getInstance('config', 'Table');
		$row->load(1);
	
		// Convert the params field to an array.
		$registry = new JRegistry;
		$registry->loadString($row->params);
		$params = $registry->toObject();
	
		$return[0] = $row;
		$return[1] = $params;
		return $return;
	}
	
	public function getShowUserGroup(){
	
		// Initialize variables
		$app = JFactory::getApplication();
		$db	 = JFactory::getDbo();
	
		$filter_order     = $app->getUserStateFromRequest('com_jblance_filter_order_usrgrp', 'filter_order', 'ug.ordering', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest('com_jblance_filter_order_Dir_usrgrp', 'filter_order_Dir', 'asc', 'word');
		$limit		= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart	= $app->getUserStateFromRequest('com_jblance.limitstart', 'limitstart', 0, 'int');
		
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
		$lists['order_Dir']	= $this->getState('filter_order_Dir');
		$lists['order']     = $this->getState('filter_order');
	
		// Get the total number of records for pagination
		$query	= 'SELECT COUNT(*) FROM #__jblance_usergroup';
		$db->setQuery($query);
		$total = $db->loadResult();
	
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);
	
		$query	= "SELECT ug.*, (SELECT COUNT(*) FROM #__jblance_user u WHERE u.ug_id=ug.id) usercount FROM #__jblance_usergroup ug ".
				  "ORDER BY ordering";
		$db->setQuery($query, $pageNav->limitstart, $pageNav->limit);
		$rows	= $db->loadObjectList();
	
		$return[0] = $rows;
		$return[1] = $pageNav;
		$return[2] = $lists;
		return $return;
	}
	
	//7.Salary Type - edit
	function getEditUserGroup(){
		$app  	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		$row 	= JTable::getInstance('jbusergroup', 'Table');
		$cid 	= $app->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid, array(0));

		$isNew = (empty($cid))? true : false;
		if(!$isNew)
			$row->load($cid[0]);
	
		$fields = $this->getFields();
	
		// Convert the params field to an array.
		$registry = new JRegistry;
		$registry->loadString($row->params);
		$params = $registry->toArray();
	
		$return[0] = $row;
		$return[1] = $fields;
		$return[2] = $params;
	
		return $return;
	}
	
	//2.Membership Plans - show
	function getShowPlan(){
		$app = JFactory::getApplication();
		$db	= JFactory::getDbo();
	
		$filter_order     = $app->getUserStateFromRequest('com_jblance_filter_order_plan', 'filter_order', 'p.ordering', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest('com_jblance_filter_order_Dir_plan', 'filter_order_Dir', 'asc', 'word');
		$limit		= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart	= $app->getUserStateFromRequest('com_jblance.limitstart', 'limitstart', 0, 'int');
	
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
		$lists['order_Dir']	= $this->getState('filter_order_Dir');
		$lists['order']     = $this->getState('filter_order');
		
		$ug_id	 	= $app->getUserStateFromRequest('com_jblance_filter_plan_ug_id', 'ug_id', '', 'int');
		$select = JblanceHelper::get('helper.select');		// create an instance of the class SelectHelper
		$lists['ug_id'] = $select->getSelectUserGroups('ug_id', $ug_id, 'COM_JBLANCE_SELECT_USERGROUP', '', 'onchange="document.adminForm.submit();"');
	
		$where = array();
		if($ug_id != '') 	 $where[] = 'p.ug_id ='.$db->quote($ug_id);
		$where = (count($where) ? ' WHERE ('.implode( ') AND (', $where ) . ')' : '');
	
		$query = "SELECT COUNT(*) FROM #__jblance_plan";
		$db->setQuery($query);
		$total = $db->loadResult();
	
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);
	
		$query = "SELECT p.*, COUNT(s.id) as subscr, ug.name groupName FROM #__jblance_plan p
				  LEFT JOIN #__jblance_plan_subscr AS s ON s.plan_id = p.id
				  LEFT JOIN `#__jblance_usergroup` AS ug ON p.ug_id = ug.id
				  $where
				  GROUP BY p.id
				  ORDER BY p.ordering ASC";
		$db->setQuery($query, $pageNav->limitstart, $pageNav->limit);
		$rows = $db->loadObjectList();
		
		//check for default plan for each user group
		$query = "SELECT id,name FROM #__jblance_usergroup WHERE published=1";
		$db->setQuery($query);
		$usergroups = $db->loadObjectList();
		
		foreach($usergroups as $usergroup){
			$query = "SELECT id FROM #__jblance_plan WHERE default_plan=1 AND ug_id=".$db->quote($usergroup->id);
			$db->setQuery($query);
			$defaultPlanId = $db->loadResult();
			
			if(empty($defaultPlanId)){
				$app->enqueueMessage(JText::sprintf('COM_JBLANCE_NO_DEFAULT_PLAN_FOR_THE_USERGROUP', $usergroup->name), 'error');
				//$return = JRoute::_('index.php?option=com_jblance&view=guest&layout=showfront', false);
			}
		}
	
		$return[0] = $rows;
		$return[1] = $pageNav;
		$return[2] = $lists;
		return $return;
	}
	
	//2.Membership Plans - edit
	function getEditPlan(){
		$app  	= JFactory::getApplication();
		$row 	= JTable::getInstance('plan', 'Table');
		$cid 	= $app->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid, array(0));
		
		$isNew = (empty($cid))? true : false;
		if(!$isNew)
			$row->load($cid[0]);
		
		// Convert the params field to an array.
		$registry = new JRegistry;
		$registry->loadString($row->params);
		$params = $registry->toArray();
	
		$return[0] = $row;
		$return[1] = $params;
	
		return $return;
	}
	
	//7a.Pay Modes - show
	function getShowPaymode(){
		$app 	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		
		$filter_order     = $app->getUserStateFromRequest('com_jblance_filter_order_paymode', 'filter_order', 'p.ordering', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest('com_jblance_filter_order_Dir_paymode', 'filter_order_Dir', 'asc', 'word');
		$limit		= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart	= $app->getUserStateFromRequest('com_jblance.limitstart', 'limitstart', 0, 'int');
		
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
		$lists['order_Dir']	= $this->getState('filter_order_Dir');
		$lists['order']     = $this->getState('filter_order');
	
		$query = "SELECT COUNT(*) FROM #__jblance_paymode p";
		$db->setQuery($query);
		$total = $db->loadResult();
	
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);
	
		$query = "SELECT * FROM #__jblance_paymode p ".
				 "ORDER BY p.ordering";
		$db->setQuery($query, $pageNav->limitstart, $pageNav->limit);
		$rows = $db->loadObjectList();
	
		$return[0] = $rows;
		$return[1] = $pageNav;
		$return[2] = $lists;
	
		return $return;
	}
	
	//7a.Pay Modes - edit
	function getEditPaymode(){
		$app  	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		$cid 	= $app->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid, array(0));
	
		$paymode = JTable::getInstance('paymode', 'Table');
		$paymode->load($cid[0]);
		
		// Convert the params field to an array.
		$registry = new JRegistry;
		$registry->loadString($paymode->params);
		$params = $registry->toObject();
		
		$gwcode = $paymode->gwcode;
		// get the JForm object
		jimport('joomla.form.form');
		$pathToGatewayXML = JPATH_COMPONENT_SITE."/gateways/forms/$gwcode.xml";
		if(file_exists($pathToGatewayXML)){
			$form = JForm::getInstance($gwcode, $pathToGatewayXML, array('control' => 'params', 'load_data' => true));
			$form->bind($params);
		}
		else
			$form = null;
	
		$return[0] = $paymode;
		$return[1] = $params;
		$return[2] = $form;
		return $return;
	}
	
	//7.Custom Field - show
	function getShowCustomField(){
		$app 	= JFactory::getApplication();
		$db		= JFactory::getDbo();
	
		$limit		= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart	= $app->getUserStateFromRequest('com_jblance.limitstart', 'limitstart', 0, 'int');
		$filter_field_type = $app->getUserStateFromRequest('com_jblance.filter_cust_field_type', 'filter_field_type', 'profile', 'string');
		$filter_order     = $app->getUserStateFromRequest('com_jblance_filter_order_custom', 'filter_order', 'c.ordering', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest('com_jblance_filter_order_Dir_custom', 'filter_order_Dir', 'asc', 'word');
		
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
	
		$where = '';
		if(!empty($filter_field_type))
			$where = " WHERE field_for = ".$db->quote($filter_field_type);
	
		$lists['order_Dir']	= $this->getState('filter_order_Dir');
		$lists['order']     = $this->getState('filter_order');
		$lists['field_type'] = $this->getSelectFieldtype('filter_field_type', $filter_field_type, 0, 'onchange="document.adminForm.submit();"');
	
		$query = "SELECT COUNT(*) FROM #__jblance_custom_field c $where";
		$db->setQuery($query);
		$total = $db->loadResult();
	
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $limitstart, $limit);
	
		$query = "SELECT * FROM #__jblance_custom_field c
		$where
		ORDER BY c.ordering";
		$db->setQuery($query/*, $pageNav->limitstart, $pageNav->limit*/);
		$rows = $db->loadObjectList();
	
		$parents = $children = array();
		foreach($rows as $ct){
			if($ct->parent == 0)
				$parents[] = $ct;
			else
				$children[] = $ct;
		}
		$ordered = '';
		
		if(count($parents)){
			foreach($parents as $pt){
				$ordered[] = $pt;
				foreach($children as $ct){
					if($ct->parent == $pt->id){
						$ordered[]= $ct;
					}
				}
			}
			$rows = $ordered;
		}
		
		$return[0] = $rows;
		$return[1] = $pageNav;
		$return[2] = $lists;
		$return[3] = $filter_field_type;
		
		return $return;
	}
	
	//7.Custom Field - edit
	function getEditCustomField(){
		$app 	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		$row 	= JTable::getInstance('custom', 'Table');
		$cid 	= $app->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid, array(0));
		
		$isNew = (empty($cid)) ? true : false;
		if(!$isNew)
			$row->load($cid[0]);
	
		$filter_field_type = $app->getUserStateFromRequest('com_jblance.filter_cust_field_type', 'field_for', 'profile', 'string');
		$lists['field_for'] = $this->getSelectFieldtype('field_for', $filter_field_type, 'profile', 'onchange="document.adminForm.submit();"');
		if($filter_field_type)
			$where = " field_for = ".$db->quote($filter_field_type);
	
		//make selection custom group
		$query = 'SELECT id AS value, field_title AS text FROM #__jblance_custom_field WHERE parent=0 AND'. $where.' ORDER BY ordering';
		$db->setQuery($query);
		$users = $db->loadObjectList();
	
		$types = array();
		foreach($users as $item){
			$types[] = JHtml::_('select.option', $item->value, JText::_($item->text));
		}
		$groups = JHtml::_('select.genericlist', $types, 'parent', 'class="inputbox required" size="8"', 'value', 'text', $row->parent);
	
		$return[0] = $row;
		$return[1] = $groups;
		$return[2] = $lists;
		return $return;
	}
	
	//Email Templates
	function getEmailTemplate(){
		$app  	 = JFactory::getApplication();
		$db 	 = JFactory :: getDbo();
		$tempFor = $app->input->get('tempfor', 'subscr-pending', 'string');
	
		$query = "SELECT * FROM #__jblance_emailtemplate WHERE templatefor = ".$db->Quote($tempFor);
		$db->setQuery($query);
		$template = $db->loadObject();
	
		return $template;
	}
	
	//13.Category - show
	function getShowCategory(){
		$app = JFactory::getApplication();
		$db	= JFactory::getDbo();
		$select = JblanceHelper::get('helper.select');		// create an instance of the class SelectHelper
	
		$limit		= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart	= $app->getUserStateFromRequest('com_jblance.limitstart', 'limitstart', 0, 'int');
		$filter_order     = $app->getUserStateFromRequest('com_jblance_filter_order_category', 'filter_order', 'c.ordering', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest('com_jblance_filter_order_Dir_category', 'filter_order_Dir', 'asc', 'word');
		
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
		$lists['order_Dir']	= $this->getState('filter_order_Dir');
		$lists['order']     = $this->getState('filter_order');
	
		$query = "SELECT COUNT(*) FROM #__jblance_category c";
		$db->setQuery($query);
		$total = $db->loadResult();
	
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);
	
		$query = 'SELECT * FROM #__jblance_category c WHERE c.parent=0 ORDER BY c.ordering';
		$db->setQuery($query);
		$categs = $db->loadObjectList();
	
		// subcategories view as tree
		$tree = array();
	
		foreach($categs as $v) {
			$indent = '';
			$tree[] = $v;
			$tree = $select->getSubcategories($v->id, $indent, $tree, 1);
		}
		$rows = array_slice($tree, $pageNav->limitstart, $pageNav->limit);
	
		$return[0] = $rows;
		$return[1] = $pageNav;
		$return[2] = $lists;
		return $return;
	}
	
	//13.Category - edit
	function getEditCategory(){
		$app  	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		$row 	= JTable::getInstance('category', 'Table');
		$cid 	= $app->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid, array(0));
		
		$isNew = (empty($cid)) ? true : false;
		if(!$isNew)
			$row->load($cid[0]);
	
		return $row;
	}
	
	function getShowBudget(){
		$app = JFactory::getApplication();
		$db	= JFactory::getDbo();
		
		$limit		= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart	= $app->getUserStateFromRequest('com_jblance.limitstart', 'limitstart', 0, 'int');
		$filter_order     = $app->getUserStateFromRequest('com_jblance_filter_order_budget', 'filter_order', 'b.ordering', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest('com_jblance_filter_order_Dir_budget', 'filter_order_Dir', 'asc', 'word');
		
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
		$lists['order_Dir']	= $this->getState('filter_order_Dir');
		$lists['order']     = $this->getState('filter_order');
		
		$query = "SELECT COUNT(*) FROM #__jblance_budget b";
		$db->setQuery($query);
		$total = $db->loadResult();
		
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);
		
		$query = 'SELECT * FROM #__jblance_budget b ORDER BY b.ordering';
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		$return[0] = $rows;
		$return[1] = $pageNav;
		$return[2] = $lists;
		return $return;
	}
	
	function getEditBudget(){
		$app  	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		$row 	= JTable::getInstance('budget', 'Table');
		$cid 	= $app->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid, array(0));
		
		$isNew = (empty($cid)) ? true : false;
		if(!$isNew)
			$row->load($cid[0]);
	
		return $row;
	}
	
	function getShowDuration(){
		$app = JFactory::getApplication();
		$db	= JFactory::getDbo();
		
		$limit		= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart	= $app->getUserStateFromRequest('com_jblance.limitstart', 'limitstart', 0, 'int');
		$filter_order     = $app->getUserStateFromRequest('com_jblance_filter_order_duration', 'filter_order', 'd.ordering', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest('com_jblance_filter_order_Dir_duration', 'filter_order_Dir', 'asc', 'word');
		
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
		$lists['order_Dir']	= $this->getState('filter_order_Dir');
		$lists['order']     = $this->getState('filter_order');
		
		$query = "SELECT COUNT(*) FROM #__jblance_duration d";
		$db->setQuery($query);
		$total = $db->loadResult();
		
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);
		
		$query = 'SELECT * FROM #__jblance_duration d ORDER BY d.ordering';
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		$return[0] = $rows;
		$return[1] = $pageNav;
		$return[2] = $lists;
		return $return;
	}
	
	function getEditDuration(){
		$app  	= JFactory::getApplication();
		$row 	= JTable::getInstance('duration', 'Table');
		$cid 	= $app->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid, array(0));
		
		$isNew = (empty($cid)) ? true : false;
		if(!$isNew)
			$row->load($cid[0]);
	
		return $row;
	}
	
	function getShowLocation(){
		$app 	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		$where 	= array();
	
		$limit			  = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart		  = $app->getUserStateFromRequest('com_jblance.limitstart', 'limitstart', 0, 'int');
		$filter_order     = $app->getUserStateFromRequest('com_jblance_filter_order_location', 'filter_order', 'l.lft', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest('com_jblance_filter_order_Dir_location', 'filter_order_Dir', 'asc', 'word');
		$search			  = $app->getUserStateFromRequest('com_jblance_location_search', 'search', '', 'string');
		if(strpos($search, '"') !== false){
			$search = str_replace(array('=', '<'), '', $search);
		}
		$search = JString::strtolower($search);
	
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
		$orderby = $this->_buildContentOrderBy();
		$lists['order_Dir']	= $this->getState('filter_order_Dir');
		$lists['order']     = $this->getState('filter_order');
		$lists['search'] 	= $search;
		
		if(isset($search) && $search != ''){
			$searchEscaped = $db->quote('%'.$db->escape($search, true).'%', false);
			$where[] = 'l.title LIKE '.$searchEscaped;
		}
		
		$where[] = 'l.extension = '.$db->quote('');
		
		$where = (count($where) ? ' WHERE ('.implode(') AND (', $where) . ')' : '' );
		
		$query = "SELECT * FROM #__jblance_location l".
				 $where.
				 $orderby;
		$db->setQuery($query);
		$db->execute();//echo $query;
		$total = $db->getNumRows();
		
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);
		
		$db->setQuery($query, $pageNav->limitstart, $pageNav->limit);
		$rows = $db->loadObjectList();
	
		// Preprocess the list of items to find ordering divisions.
		$ordering = array();
		foreach ($rows as &$row){
			$ordering[$row->parent_id][] = $row->id;
		}
	
		$return[0] = $rows;
		$return[1] = $pageNav;
		$return[2] = $lists;
		$return[3] = $ordering;
		return $return;
	}
	
	function getEditLocation(){
		$app  	= JFactory::getApplication();
		$row 	= JTable::getInstance('location', 'Table');
		$cid 	= $app->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid, array(0));
	
		$isNew = (empty($cid)) ? true : false;
		if(!$isNew)
			$row->load($cid[0]);
	
		return $row;
	}	
	function getOptimise(){
	
		// Initialize variables
		$app	= JFactory::getApplication();
		$db		= JFactory::getDbo();
		$result = array();
	
		//get list of user ids removed from Joomla user table
		$query = "SELECT user_id FROM #__jblance_user WHERE user_id NOT IN (SELECT id FROM #__users)";
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		$user_ids = implode(',',$db->loadColumn());
		if($num_rows > 0)
			$result[] = $num_rows.' users will be deleted from JoomBri users table';
		
		//if user id is empty, return null
		if(empty($user_ids))
			return null;
	
		//get list of project ids to be removed
		$query = "SELECT id FROM #__jblance_project WHERE assigned_userid IN (".$user_ids.") OR publisher_userid IN (".$user_ids.")";
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		$project_ids = $db->loadColumn();
		if(!empty($project_ids) && is_array($project_ids))
			$project_ids = implode(',', $project_ids);
		else 
			$project_ids = 0;
		if($num_rows > 0)
			$result[] = $num_rows.' Projects will be deleted';
	
		// count entries from bid table
		$query = "SELECT COUNT(id) FROM #__jblance_bid WHERE user_id IN (".$user_ids.") OR project_id IN (".$project_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Bids table';
	
		// count entries from custom field value table
		$query = "SELECT COUNT(id) FROM #__jblance_custom_field_value WHERE userid IN (".$user_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Custom Field Value table';
	
		// count entries from deposit table
		$query = "SELECT COUNT(id) FROM #__jblance_deposit WHERE user_id IN (".$user_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Deposit table';
	
		// count entries from escrow table
		$query = "SELECT COUNT(id) FROM #__jblance_escrow WHERE from_id IN (".$user_ids.") OR to_id IN (".$user_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Escrow table';
		
		// count entries from Expiry Alert table
		$query = "SELECT COUNT(id) FROM #__jblance_expiry_alert WHERE user_id IN (".$user_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Expiry Alert table';
	
		// count entries from Favourite table
		$query = "SELECT COUNT(id) FROM #__jblance_favourite WHERE actor IN (".$user_ids.") OR target IN (".$user_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Favourite table';
	
		// count entries from feeds table
		$query = "SELECT COUNT(id) FROM #__jblance_feed WHERE actor IN (".$user_ids.") OR target IN (".$user_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Feeds table';
	
		// count entries from feeds hide table
		$query = "SELECT COUNT(id) FROM #__jblance_feed_hide WHERE user_id IN (".$user_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Feeds Hide table';
	
		// count entries from forum table
		$query = "SELECT COUNT(id) FROM #__jblance_forum WHERE user_id IN (".$user_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Forum table';
	
		// count entries from message table
		$query = "SELECT COUNT(id) FROM #__jblance_message WHERE idFrom IN (".$user_ids.") OR idTo IN (".$user_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Message table';
	
		// count entries from notify table
		$query = "SELECT COUNT(id) FROM #__jblance_notify WHERE user_id IN (".$user_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Notify table';
	
		// count entries from plan subscr table
		$query = "SELECT COUNT(id) FROM #__jblance_plan_subscr WHERE user_id IN (".$user_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Plan Subscription table';
	
		// count entries from portfolio table
		$query = "SELECT COUNT(id) FROM #__jblance_portfolio WHERE user_id IN (".$user_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Portfolio table';
	
		// count entries from project file table
		$query = "SELECT COUNT(id) FROM #__jblance_project_file WHERE project_id IN (".$project_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Project File table';
	
		// count entries from rating table
		$query = "SELECT COUNT(id) FROM #__jblance_rating WHERE actor IN (".$user_ids.") OR target IN (".$user_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from rating table';
	
		// count entries from report table
		$query = "SELECT COUNT(id) FROM #__jblance_report WHERE (`method` like 'project%' AND params IN ($project_ids)) OR (`method` like 'profile%' AND params IN ($user_ids))";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Report table';
	
		// count entries from reporter table
		$query = "SELECT COUNT(id) FROM #__jblance_report_reporter WHERE user_id IN (".$user_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from reporter table';
	
		// count entries from transaction table
		$query = "SELECT COUNT(id) FROM #__jblance_transaction WHERE user_id IN (".$user_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Transaction table';
	
		// count entries from withdraw table
		$query = "SELECT COUNT(id) FROM #__jblance_withdraw WHERE user_id IN (".$user_ids.")";
		$db->setQuery($query);
		$num_rows = $db->loadResult();
		if($num_rows > 0)
			$result[] = $num_rows.' entries will be deleted from Withdraw table';
	
		$return[0] = $result;
		$return[1] = $user_ids;
		$return[2] = $project_ids;
	
		return $return;
	}
	
	/* Misc Functions */
	function _buildContentOrderBy(){
		$app = JFactory::getApplication();
	
		$orderby = '';
		$filter_order     = $this->getState('filter_order');
		$filter_order_Dir = $this->getState('filter_order_Dir');
	
		/* Error handling is never a bad thing*/
		if(!empty($filter_order) && !empty($filter_order_Dir) ){
			$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		}
	
		return $orderby;
	}
	
	public function &getFields(){
		// Initialize variables
		$app	= JFactory::getApplication();
		$db		= JFactory::getDbo();
	
		$query	= "SELECT * FROM #__jblance_custom_field ".
				  "WHERE field_for=".$db->quote('profile')." ".
				  "ORDER BY ordering";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
	
		$parents = $children = array();
		foreach($rows as $ct){
			if($ct->parent == 0)
				$parents[] = $ct;
			else
				$children[] = $ct;
		}
		$ordered = '';
	
		if(count($parents)){
			foreach($parents as $pt){
				$ordered[] = $pt;
				foreach($children as $ct){
					if($ct->parent == $pt->id){
						$ordered[]= $ct;
					}
				}
			}
			$rows = $ordered;
		}
	
		return $rows;
	}
	
	//7.getSelectDuration
	function getSelectDuration($var, $default, $disabled, $event){
		$option = '';
		if($disabled == 1)
			$option = 'disabled';
	
		$types[] = JHtml::_('select.option', 'days', JText::_('COM_JBLANCE_DAYS'));
		$types[] = JHtml::_('select.option', 'weeks', JText::_('COM_JBLANCE_WEEKS'));
		$types[] = JHtml::_('select.option', 'months', JText::_('COM_JBLANCE_MONTHS'));
		$types[] = JHtml::_('select.option', 'years', JText::_('COM_JBLANCE_YEARS'));
	
		$lists = JHtml::_('select.genericlist', $types, $var, "class=\"input-small\" size=\"1\" $option $event", 'value', 'text', $default);
		return $lists;
	}
	
	//20.getSelectFieldtype
	function getSelectFieldtype($var, $default, $disabled, $event){
		$option = '';
		if($disabled == 1)
			$option = 'disabled';
	
		$types[] = JHtml::_('select.option', 'profile', JText::_('COM_JBLANCE_PROFILE'));
		$types[] = JHtml::_('select.option', 'project', JText::_('COM_JBLANCE_PROJECT'));
	
		$lists 	 = JHtml::_('select.genericlist', $types, $var, "class='inputbox' size='1' $option $event", 'value', 'text', $default);
		return $lists;
	}
	
	function getSelectTheme($var, $default){
		$types[] = JHtml::_('select.option', 'styleGR.css', JText::_('COM_JBLANCE_GREY'));
		/* $types[] = JHtml::_('select.option', 'styleFB.css', JText::_('COM_JBLANCE_FACEBOOK_BLUE'));
		$types[] = JHtml::_('select.option', 'styleJS.css', JText::_('COM_JBLANCE_JOMSOCIAL_GREEN'));
		$types[] = JHtml::_('select.option', 'styleBO.css', JText::_('COM_JBLANCE_BLACK_ORANGE'));
		$types[] = JHtml::_('select.option', 'styleOR.css', JText::_('COM_JBLANCE_ORANGE'));
		$types[] = JHtml::_('select.option', 'styleCS1.css', JText::_('COM_JBLANCE_CUSTOM1'));
		$types[] = JHtml::_('select.option', 'styleCS2.css', JText::_('COM_JBLANCE_CUSTOM2')); */
	
		$lists 	 = JHtml::_('select.genericlist', $types, $var, 'class="inputbox" size="1"', 'value', 'text', $default);
	
		return $lists;
	}
	
	function getselectDateFormat($var, $default){
		$types[] = JHtml::_('select.option', 'd-m-Y', JText::_('dd-mm-yyyy'));
		$types[] = JHtml::_('select.option', 'm-d-Y', JText::_('mm-dd-yyyy'));
		$types[] = JHtml::_('select.option', 'Y-m-d', JText::_('yyyy-mm-dd'));
	
		$lists 	 = JHtml::_('select.genericlist', $types, $var, 'class="input-medium" size="1"', 'value', 'text', $default);
	
		return $lists;
	}
	
	//Get the Joomla user group title for non-super users
	function getJoomlaUserGroupTitles($id){
		$db = JFactory::getDbo();
		$query = "SELECT title FROM #__usergroups ug WHERE ug.id IN ($id)";
		$db->setQuery($query);
		$cats = $db->loadColumn();
		if($cats)
			return implode($cats, ", ");
		else
			return '';
	}
}