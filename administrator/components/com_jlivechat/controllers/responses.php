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

class JLiveChatControllerResponses extends CMSController
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

	function create_response()
	{
		$mainframe = & JFactory::getApplication();

		JRequest::checkToken() or die('Invalid Token');

		$name = trim(JRequest::getVar('response_name', null, 'method'));
		$category = trim(JRequest::getVar('response_category', null, 'method'));
		$txt = trim(JRequest::getVar('response_txt', '', 'method', 'string', JREQUEST_ALLOWRAW));

		$errors = array();

		if(strlen($name) < 1 || strlen($name) > 150)
		{
			$errors[] = JText::_('ENTER_VALID_RESPONSE_NAME');
		}

		if(count($errors) == 0)
		{
			$model = & $this->getModel('ResponseAdmin', 'JLiveChatModel');

			$model->createNewResponse($name, $txt, $category);

			return $mainframe->redirect('index.php?option=com_jlivechat&view=responses', JText::_('CREATED_SUCCESSFULLY'));
		}
		else
		{
			foreach($errors as $msg)
			{
				$mainframe->enqueueMessage($msg, 'error');
			}
		}

		return $this->new_response();
	}

	function update_response()
	{
		$mainframe = & JFactory::getApplication();

		$responseId = JRequest::getInt('response_id', 0, 'method');
		$name = trim(JRequest::getVar('response_name', null, 'method'));
		$category = trim(JRequest::getVar('response_category', null, 'method'));
		$txt = trim(JRequest::getVar('response_txt', '', 'method', 'string', JREQUEST_ALLOWRAW));

		$errors = array();

		if(strlen($name) < 1 || strlen($name) > 150)
		{
			$errors[] = JText::_('ENTER_VALID_RESPONSE_NAME');
		}

		if(count($errors) == 0)
		{
			$model = & $this->getModel('ResponseAdmin', 'JLiveChatModel');

			$model->updateResponse($responseId, $name, $txt, $category);

			return $mainframe->redirect('index.php?option=com_jlivechat&view=responses', JText::_('UPDATED_SUCCESSFULLY'));
		}
		else
		{
			foreach($errors as $msg)
			{
				$mainframe->enqueueMessage($msg, 'error');
			}
		}

		return $this->edit();
	}

	function get_responses()
	{
		$mainframe = & JFactory::getApplication();

		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-type: text/x-json");

		$limitstart = JRequest::getInt('startIndex', 0, 'method');
		$page = JRequest::getInt('page', 1, 'method');
		$resultsPerPage = JRequest::getInt('rp', 15, 'method');
		$sortname = JRequest::getVar('sort', 'response_sort_order', 'method');
		$sortorder = JRequest::getVar('dir', 'asc', 'method');
		$query = JRequest::getVar('query', null, 'method');
		$qtype = JRequest::getVar('qtype', null, 'method');

		$responses = & CMSModel::getInstance('ResponseAdmin', 'JLiveChatModel');

		$responses->setSort($sortname, $sortorder);

		$responses->setState('limit', $resultsPerPage);
		$responses->setState('limitstart', $limitstart);

		if($query && $qtype)
		{
			// Apply where clause
			$responses->setFilter($query, $qtype);
		}

		if($sortname && $sortorder)
		{
			// Apply where clause
			$responses->setSort($sortname, $sortorder);
		}

		$data = $responses->getData();
		$pagination = $responses->getPagination();

		if(count($data) > 0)
		{
			foreach($data as $a => $item)
			{
				$data[$a]['response_txt'] = trim(strip_tags($data[$a]['response_txt']));
				$data[$a]['response_txt'] = substr($data[$a]['response_txt'], 0, 30).'...';

				if($data[$a]['response_enabled'] == 1)
				{
					$data[$a]['response_enabled'] = '<a class="green" href="javascript: void(0);" onclick="toggleStatus('.$data[$a]['response_id'].');">'.JText::_('ENABLED').'</span>';
				}
				else
				{
					$data[$a]['response_enabled'] = '<a class="red" href="javascript: void(0);" onclick="toggleStatus('.$data[$a]['response_id'].');">'.JText::_('DISABLED').'</span>';
				}

				if(!isset($data[$a]['response_category']))
				{
					$data[$a]['response_category'] = '- None -';
				}

				$data[$a]['response_sort_order'] = '<input style="width: 25px;" size="2" type="text" name="sort_order_'.$data[$a]['response_id'].'" value="'.$data[$a]['response_sort_order'].'" />&nbsp;&nbsp;';
				$data[$a]['response_sort_order'] .= '<a href="javascript: void(0);" onclick="moveUpSortOrder('.$data[$a]['response_id'].');"><img src="components/com_jlivechat/assets/images/arrows/up.gif" width="13" height="15" border="0" alt="Move Up" /></a>&nbsp;<a href="javascript: void(0);" onclick="moveDownSortOrder('.$data[$a]['response_id'].');"><img src="components/com_jlivechat/assets/images/arrows/down.gif" width="13" height="15" border="0" alt="Move Down" /></a>';

				$data[$a]['options'] = '<a id="response_edit'.$data[$a]['response_id'].'" href="index.php?option=com_jlivechat&view=responses&task=edit&response_id='.$data[$a]['response_id'].'">'.JText::_('EDIT_RESPONSE2').'</a>';

				$data[$a]['options'] .= '<script type="text/javascript">
							var responseEditBtn'.$data[$a]['response_id'].' = new YAHOO.widget.Button("response_edit'.$data[$a]['response_id'].'");
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

	function toggle_status()
	{
		$model = & CMSModel::getInstance('ResponseAdmin', 'JLiveChatModel');
		$model->toggleStatus(JRequest::getInt('response_id', null, 'method'));

		jexit();
	}

	function move_up_key_sort_order()
	{
		$operatorId = JRequest::getInt('o', null, 'method');

		$model = & CMSModel::getInstance('ResponseAdmin', 'JLiveChatModel');

		$model->moveUpSortOrder($operatorId);
		$model->fixOrders();

		jexit();
	}

	function move_down_key_sort_order()
	{
		$itemId = JRequest::getVar('o', null, 'method');

		$model = & CMSModel::getInstance('ResponseAdmin', 'JLiveChatModel');

		$model->moveDownSortOrder($itemId);
		$model->fixOrders();

		jexit();
	}

	function delete_responses()
	{
		$mainframe = & JFactory::getApplication();

		$selectedList = JRequest::getVar('selected_records', '', 'method');

		$selectedList = preg_replace('@[,]+$@', '', $selectedList);

		if(strlen($selectedList) > 0)
		{
			$deleteArr = explode(',', $selectedList);

			$model = & CMSModel::getInstance('ResponseAdmin', 'JLiveChatModel');

			if(count($deleteArr) > 0)
			{
				foreach($deleteArr as $responseId)
				{
					$model->deleteResponse($responseId);
				}

				$model->fixOrders();
			}
		}

		$uri = & JFactory::getURI();
		$uri->delVar('task');

		return $mainframe->redirect($uri->toString(), JText::_('ITEMS_DELETED_SUCCESSFULLY'));
	}

	function new_response()
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

		if(!JRequest::getVar('response_id', null, 'method'))
		{
			return $mainframe->redirect('index.php?option=com_jlivechat&view=responses', JText::_('UNKNOWN_ERROR_OCCURRED'), 'error');
		}

		$viewName = JRequest::getCmd('view');

		$document = & JFactory::getDocument();
		$vType = $document->getType();

		// Get/Create the view
		$view = & $this->getView($viewName, $vType);

		// Set the layout
		$view->setLayout('default');

		$model = & CMSModel::getInstance('ResponseAdmin', 'JLiveChatModel');

		$view->assign('response', $model->getResponse(JRequest::getVar('response_id', null, 'method')));

		return $view->display('edit');
	}

	function save_responses()
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

		$model = & CMSModel::getInstance('ResponseAdmin', 'JLiveChatModel');
		$model->saveSortOrders($newSorts);
		$model->fixOrders();

		$uri->delVar('task');

		return $mainframe->redirect($uri->toString(), JText::_('NEW_SORT_SAVED_SUCCESSFULLY'));
	}
}