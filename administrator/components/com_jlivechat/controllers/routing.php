<?php
/**
 * @package JLive! Chat
 * @version 4.3.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU Lesser General Public License as published by
  the Free Software Foundation; either version 3 of the License, or (at your
  option) any later version.

  This program is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
  or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
  License for more details.

  You should have received a copy of the GNU Lesser General Public License
  along with this program.  If not, see http://www.gnu.org/licenses/.
 */
/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JLiveChatControllerRouting extends CMSController
{
	/**
	 * Display the view
	 */
	function display()
	{
		$mainframe = & JFactory::getApplication();

		if(!function_exists('curl_init')) $mainframe->enqueueMessage(JText::_('CURL_NOT_INSTALLED'), 'error');

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->sync();

		$this->checkPlugin();

		$viewName = JRequest::getCmd('view');

		$document = & JFactory::getDocument();
		$vType = $document->getType();

		// Get/Create the view
		$view = & $this->getView($viewName, $vType);

		// Get/Create the model
		if($model = & $this->getModel('RoutingAdmin', 'JLiveChatModel'))
		{
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout('default');

		// Display the view
		parent::display(false);
	}

	function checkPlugin()
	{
		$db = & JFactory::getDBO();

		$sql = "UPDATE #__extensions SET
		    enabled = 1
		WHERE element = 'jlivechat'
		AND type = 'plugin';";

		$db->setQuery($sql);

		return $db->query();
	}

	function create_route()
	{
		$mainframe = & JFactory::getApplication();

		JRequest::checkToken() or die('Invalid Token');

		$routeName = trim(JRequest::getVar('route_name', null, 'method'));
		$routeAction = JRequest::getVar('route_action', null, 'method');
		$ringType = JRequest::getVar('ring_type', null, 'method');
		$selectedOperators = JRequest::getVar('selected_operators', array(), 'method');

		$sourceData = JRequest::getVar('source', null, 'method');

		$sourceData['ring_type'] = $ringType;
		$sourceData['selected_operators'] = $selectedOperators;

		$model = & $this->getModel('RoutingAdmin', 'JLiveChatModel');

		$model->addRoute($sourceData, $routeName, $routeAction);

		return $mainframe->redirect('index.php?option=com_jlivechat&view=routing', JText::_('CREATED_SUCCESSFULLY'));
	}

	function update_route()
	{
		$mainframe = & JFactory::getApplication();

		JRequest::checkToken() or die('Invalid Token');

		$routeId = JRequest::getInt('route_id', null, 'method');
		$routeName = trim(JRequest::getVar('route_name', null, 'method'));
		$routeAction = JRequest::getVar('route_action', null, 'method');
		$ringType = JRequest::getVar('ring_type', null, 'method');
		$selectedOperators = JRequest::getVar('selected_operators', array(), 'method');

		$sourceData = JRequest::getVar('source', null, 'method');

		$sourceData['ring_type'] = $ringType;
		$sourceData['selected_operators'] = $selectedOperators;

		$model = & $this->getModel('RoutingAdmin', 'JLiveChatModel');

		$model->updateRoute($routeId, $sourceData, $routeName, $routeAction);

		return $mainframe->redirect('index.php?option=com_jlivechat&view=routing', JText::_('UPDATED_SUCCESSFULLY'));
	}

	function copy()
	{
		$mainframe = & JFactory::getApplication();

		JRequest::checkToken() or die('Invalid Token');

		$selectedRoutes = JRequest::getVar('routes_selected', array(), 'method');

		$model = & $this->getModel('RoutingAdmin', 'JLiveChatModel');
		$model->copyRoute($selectedRoutes);

		return $mainframe->redirect('index.php?option=com_jlivechat&view=routing', JText::_('COPIED_SUCCESSFULLY'));
	}

	function remove()
	{
		$mainframe = & JFactory::getApplication();

		JRequest::checkToken() or die('Invalid Token');

		$selectedRoutes = JRequest::getVar('routes_selected', array(), 'method');

		$model = & $this->getModel('Routing', 'LiveChatModel');
		$model->deleteRoutes($selectedRoutes);

		return $mainframe->redirect('index.php?option=com_jlivechat&view=routing', JText::_('Deleted Successfully!'));
	}

	function save()
	{
		$mainframe = & JFactory::getApplication();

		JRequest::checkToken() or die('Invalid Token');

		return $mainframe->redirect('index.php?option=com_jlivechat&view=routing', 'Settings Saved Successfully!');
	}

	function toggle_route_status()
	{
		$routing = & CMSModel::getInstance('RoutingAdmin', 'JLiveChatModel');

		$routing->toggleStatus(JRequest::getInt('i', null, 'method'));

		jexit();
	}

	function move_up_key_sort_order()
	{
		$routeId = JRequest::getVar('o', null, 'method');

		$model = & CMSModel::getInstance('RoutingAdmin', 'JLiveChatModel');

		$model->moveUpSortOrder($routeId);

		$model->fixOrders();

		jexit();
	}

	function move_down_key_sort_order()
	{
		$routeId = JRequest::getVar('o', null, 'method');

		$model = & CMSModel::getInstance('RoutingAdmin', 'JLiveChatModel');

		$model->moveDownSortOrder($routeId);

		$model->fixOrders();

		jexit();
	}

	function get_routes()
	{
		$mainframe = & JFactory::getApplication();

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-type: text/x-json");

		$limitstart = JRequest::getInt('startIndex', 0, 'method');
		$page = JRequest::getInt('page', 1, 'method');
		$resultsPerPage = JRequest::getInt('rp', 10, 'method');
		$sortname = JRequest::getVar('sort', 'route_sort_order', 'method');
		$sortorder = JRequest::getVar('dir', 'asc', 'method');
		$query = JRequest::getVar('query', null, 'method');
		$qtype = JRequest::getVar('qtype', null, 'method');

		$model = & CMSModel::getInstance('RoutingAdmin', 'JLiveChatModel');
		$operatorAdmin = & CMSModel::getInstance('OperatorAdmin', 'JLiveChatModel');

		$model->setSort($sortname, $sortorder);

		$model->setState('limit', $resultsPerPage);
		$model->setState('limitstart', $limitstart);

		if($query && $qtype)
		{
			// Apply where clause
			$model->setFilter($query, $qtype);
		}

		if($sortname && $sortorder)
		{
			// Apply where clause
			$model->setSort($sortname, $sortorder);
		}

		$data = $model->getData();
		$pagination = $model->getPagination();

		if(count($data) > 0)
		{
			foreach($data as $a => $row)
			{
				$row->source_criteria = '';

				foreach($row->route_source_data as $b => $filter)
				{
					if(is_numeric($b))
					{
						$row->source_criteria .= $filter."\n<br />";
					}
				}

				if($row->route_enabled == 1)
				{
					$row->route_enabled = '<a class="green" href="javascript: void(0);" onclick="toggleRouteStatus('.$row->route_id.');">'.JText::_('ENABLED').'</span>';
				}
				else
				{
					$row->route_enabled = '<a class="red" href="javascript: void(0);" onclick="toggleRouteStatus('.$row->route_id.');">'.JText::_('DISABLED').'</span>';
				}

				if($row->route_action == 'send_to_all_operators')
				{
					$row->route_action = JText::_('SEND_TO_ALL_OPERATORS');
				}
				elseif($row->route_action == 'send_to_selected_operators')
				{
					$row->route_action = JText::_('SEND_TO_SELECTED_OPERATORS');

					if(isset($row->route_source_data->selected_operators))
					{
						if(count($row->route_source_data->selected_operators) > 0)
						{
							$row->route_action .= "\n<br />";

							foreach($row->route_source_data->selected_operators as $selectedOperator)
							{
								$row->route_action .= ' - '.$operatorAdmin->operatorKeyLabel($selectedOperator)."\n<br />";
							}
						}
					}
				}
				elseif($row->route_action == 'send_to_offline')
				{
					$row->route_action = JText::_('SEND_TO_OFFLINE_MESSAGE');
				}

				$row->route_sort_order = '<input style="width: 25px;" size="2" type="text" name="sort_order_'.$row->route_id.'" value="'.$row->route_sort_order.'" />&nbsp;&nbsp;';
				$row->route_sort_order .= '<a href="javascript:void(0);" onclick="moveUpSortOrder('.$row->route_id.');"><img src="components/com_jlivechat/assets/images/arrows/up.gif" width="13" height="15" border="0" alt="Move Up" /></a>&nbsp;<a href="javascript:void(0);" onclick="moveDownSortOrder('.$row->route_id.');"><img src="components/com_jlivechat/assets/images/arrows/down.gif" width="13" height="15" border="0" alt="Move Down" /></a>';

				$row->options = '<a id="route_edit'.$row->route_id.'" href="index.php?option=com_jlivechat&view=routing&task=edit&i='.$row->route_id.'">'.JText::_('EDIT_ROUTE').'</a>';

				$row->options .= '<script type="text/javascript">
							var routeEditBtn'.$row->route_id.' = new YAHOO.widget.Button("route_edit'.$row->route_id.'");
							</script>';
			}
		}

		$output = array(
			'recordsReturned' => $pagination->total,
			'totalRecords' => $pagination->total,
			'startIndex' => $pagination->limitstart,
			'sort' => $sortname,
			'dir' => $sortorder,
			'pageSize' => $resultsPerPage,
			'totalResultsAvailable' => $pagination->total,
			'totalResultsReturned' => $pagination->limit,
			'firstResultPosition' => $pagination->limitstart,
			'Records' => $data
		);

		echo json_encode($output);

		jexit();
	}

	function new_route()
	{
		$viewName = JRequest::getCmd('view');

		$document = & JFactory::getDocument();
		$vType = $document->getType();

		// Get/Create the view
		$view = & $this->getView($viewName, $vType);

		// Set the layout
		$view->setLayout('default');

		return $view->display('new');
	}

	function edit()
	{
		$mainframe = & JFactory::getApplication();

		$routeId = JRequest::getInt('i', null, 'method');

		if($routeId < 1)
		{
			return $mainframe->redirect('index.php?option=com_jlivechat&view=routing');
		}

		$viewName = JRequest::getCmd('view');

		$document = & JFactory::getDocument();
		$vType = $document->getType();

		// Get/Create the view
		$view = & $this->getView($viewName, $vType);

		// Set the layout
		$view->setLayout('default');

		$routing = & CMSModel::getInstance('RoutingAdmin', 'JLiveChatModel');

		$view->assign('route', $routing->getRoute($routeId, true));

		return $view->display('edit');
	}

	function delete_operators()
	{
		$mainframe = & JFactory::getApplication();

		$selectedRoutes = JRequest::getVar('selected_routes', null, 'method');

		if($selectedRoutes)
		{
			$selectedRoutes = preg_replace('@[,\s]+$@', '', $selectedRoutes);
			$selectedRoutes = explode(',', $selectedRoutes);

			if(count($selectedRoutes) > 0)
			{
				$model = & CMSModel::getInstance('RoutingAdmin', 'JLiveChatModel');
				$model->deleteRoutes($selectedRoutes);
			}
		}

		return $mainframe->redirect('index.php?option=com_jlivechat&view=routing', JText::_('DELETED_SUCCESSULLY'));
	}

	function save_routes()
	{
		$mainframe = & JFactory::getApplication();

		$uri = & JFactory::getURI();

		$vars = JRequest::get('method');

		$newSorts = array();

		foreach($vars as $key => $val)
		{
			if(preg_match('@(sort_order_)@i', $key))
			{
				$keyId = (int)preg_replace('@sort_order_@i', '', $key);

				$newSorts[$keyId] = $val;

				$uri->delVar($key);
			}
		}

		$model = & CMSModel::getInstance('RoutingAdmin', 'JLiveChatModel');
		$model->saveSortOrders($newSorts);
		$model->fixOrders();

		$uri->delVar('task');

		return $mainframe->redirect($uri->toString(), JText::_('NEW_SORT_SAVED_SUCCESSFULLY'));
	}
}