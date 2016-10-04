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

class JLiveChatControllerOperators extends CMSController
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
		$this->configureSEF();

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

	function new_operator()
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

	function create_new_operator()
	{
		JRequest::checkToken() or die('Invalid Token');

		$mainframe = & JFactory::getApplication();

		$operator = & CMSModel::getInstance('OperatorAdmin', 'JLiveChatModel');

		$altName = JRequest::getVar('alt_name', null, 'method');
		$monitorPermission = JRequest::getInt('monitor_permission_value', 1, 'method');
		$operator2operatorPermission = JRequest::getInt('operator2operator_permission_value', 1, 'method');
		$messagePermission = JRequest::getInt('message_permission_value', 1, 'method');
		$useSSL = JRequest::getInt('use_ssl_value', 0, 'method');
		$ipBlockerPermission = JRequest::getInt('ipblocker_value', 1, 'method');

		$department = JRequest::getVar('department', null, 'method');

		$operatorId = $operator->createNewOperator($altName, $department);

		$operator->setPermission($operatorId, 'website_monitor', $monitorPermission);
		$operator->setPermission($operatorId, 'operator2operator', $operator2operatorPermission);
		$operator->setPermission($operatorId, 'messages', $messagePermission);
		$operator->setPermission($operatorId, 'use_ssl', $useSSL);
		$operator->setPermission($operatorId, 'ipblocker', $ipBlockerPermission);

		return $mainframe->redirect('index.php?option=com_jlivechat&view=operators', JText::_('OPERATOR_CREATED_SUCCESSFULLY'));
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

	function configureSEF()
	{
		$sefConfigFile = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_sh404sef'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.sef.php';

		if(file_exists($sefConfigFile))
		{
			if(is_writable($sefConfigFile))
			{
				$handle = fopen($sefConfigFile, "r");
				$contents = fread($handle, filesize($sefConfigFile));
				fclose($handle);

				$contents = preg_replace('@(\$shSecEnableSecurity[\s]*=[\s]*)"[^"]+"@', '$1"0"', $contents, 1);
				$contents = preg_replace('@(\$shSecLogAttacks[\s]*=[\s]*)"[^"]+"@', '$1"0"', $contents, 1);
				$contents = preg_replace('@(\$shSecActivateAntiFlood[\s]*=[\s]*)"[^"]+"@', '$1"0"', $contents, 1);
				$contents = preg_replace('@(\$shSecAntiFloodOnlyOnPOST[\s]*=[\s]*)"[^"]+"@', '$1"0"', $contents, 1);
				$contents = preg_replace('@(\$shSecCheckPOSTData[\s]*=[\s]*)"[^"]+"@', '$1"0"', $contents, 1);

				$fp = fopen($sefConfigFile, 'w');
				fwrite($fp, $contents);
				fclose($fp);
			}
		}
	}

	function get_operators()
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
		$sortname = JRequest::getVar('sort', 'sort_order', 'method');
		$sortorder = JRequest::getVar('dir', 'asc', 'method');
		$query = JRequest::getVar('query', null, 'method');
		$qtype = JRequest::getVar('qtype', null, 'method');

		$operators = & CMSModel::getInstance('OperatorAdmin', 'JLiveChatModel');
		$settings = & CMSModel::getInstance('SettingAdmin', 'JLiveChatModel');

		$operators->setSort($sortname, $sortorder);

		$operators->setState('limit', $resultsPerPage);
		$operators->setState('limitstart', $limitstart);

		if($query && $qtype)
		{
			// Apply where clause
			$operators->setFilter($query, $qtype);
		}

		if($sortname && $sortorder)
		{
			// Apply where clause
			$operators->setSort($sortname, $sortorder);
		}

		$websiteOperators = $operators->getData();
		$pagination = $operators->getPagination();

		if(count($websiteOperators) > 0)
		{
			foreach($websiteOperators as $a => $operator)
			{
				$websiteOperators[$a]['accept_chat_timeout'] = '<span class="hasTip" title="'.JText::_('OPERATOR_ACCEPT_TIMEOUT_HELPTIP').'">'.$websiteOperators[$a]['accept_chat_timeout'].'</span>';

				if(!isset($websiteOperators[$a]['operator_name']))
				{
					$websiteOperators[$a]['operator_name'] = '<span class="hasTip" title="'.JText::_('UPDATED_BY_DESKTOP_APPLICATION').'">- Not Set Yet -</span>';
				}

				if($websiteOperators[$a]['is_enabled'] == 1)
				{
					$websiteOperators[$a]['is_enabled'] = '<a class="green" href="javascript: void(0);" onclick="toggleOperatorStatus('.$websiteOperators[$a]['operator_id'].');">'.JText::_('ENABLED').'</span>';
				}
				else
				{
					$websiteOperators[$a]['is_enabled'] = '<a class="red" href="javascript: void(0);" onclick="toggleOperatorStatus('.$websiteOperators[$a]['operator_id'].');">'.JText::_('DISABLED').'</span>';
				}

				if(isset($websiteOperators[$a]['operator_params']->operator_ip))
				{
					$websiteOperators[$a]['last_auth_date'] .= '<br />'.JText::_('From').' '.$websiteOperators[$a]['operator_params']->operator_ip;
				}

				if(!isset($websiteOperators[$a]['accept_chat_timeout']))
				{
					$websiteOperators[$a]['accept_chat_timeout'] = JText::_('UNKNOWN');
				}

				if(!isset($websiteOperators[$a]['department']))
				{
					$websiteOperators[$a]['department'] = '- None -';
				}

				// Operator Mobile Access Key Code
				$websiteOperators[$a]['mobile_key_code'] = strtoupper(substr($websiteOperators[$a]['auth_key'], 0, 15));

				$websiteOperators[$a]['sort_order'] = '<input style="width: 20px;" size="2" type="text" name="sort_order_'.$websiteOperators[$a]['operator_id'].'" value="'.$websiteOperators[$a]['sort_order'].'" />&nbsp;&nbsp;';
				$websiteOperators[$a]['sort_order'] .= '<a href="javascript: void(0);" onclick="moveUpSortOrder('.$websiteOperators[$a]['operator_id'].');"><img src="components/com_jlivechat/assets/images/arrows/up.gif" width="13" height="15" border="0" alt="Move Up" /></a>&nbsp;<a href="javascript: void(0);" onclick="moveDownSortOrder('.$websiteOperators[$a]['operator_id'].');"><img src="components/com_jlivechat/assets/images/arrows/down.gif" width="13" height="15" border="0" alt="Move Down" /></a>';

				$websiteOperators[$a]['options'] = '<a id="operator_edit'.$websiteOperators[$a]['operator_id'].'" href="index.php?option=com_jlivechat&view=operators&task=edit&operator_id='.$websiteOperators[$a]['operator_id'].'">'.JText::_('EDIT_OPERATOR').'</a>';
				$websiteOperators[$a]['options'] .= '<div class="clr" style="padding-bottom: 5px;">&nbsp;</div>';
				$websiteOperators[$a]['options'] .= '<a id="operator_accesskey'.$websiteOperators[$a]['operator_id'].'" href="index.php?option=com_jlivechat&view=operators&task=download_operator_key&operator_id='.$websiteOperators[$a]['operator_id'].'&t='.time().'">'.JText::_('DOWNLOAD_ACCESS_KEY').'</a>';


				$websiteOperators[$a]['options'] .= '<script type="text/javascript">
							var operatorKeyBtn'.$websiteOperators[$a]['operator_id'].' = new YAHOO.widget.Button("operator_accesskey'.$websiteOperators[$a]['operator_id'].'");
							var operatorEditBtn'.$websiteOperators[$a]['operator_id'].' = new YAHOO.widget.Button("operator_edit'.$websiteOperators[$a]['operator_id'].'");
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
			'Records' => $websiteOperators
		);

		echo json_encode($output);

		jexit();
	}

	function toggle_operator_status()
	{
		$operators = & CMSModel::getInstance('OperatorAdmin', 'JLiveChatModel');

		$operators->toggleStatus(JRequest::getInt('operator_id', null, 'method'));

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->refreshSettings();
		$hostedModeObj->queueForceSync('#__jlc_operator');
		$hostedModeObj->forceSyncToRemote();

		jexit();
	}

	function download_operator_key()
	{
		$operators = & CMSModel::getInstance('OperatorAdmin', 'JLiveChatModel');

		if(JRequest::getInt('operator_id', null, 'method'))
		{
			return $operators->downloadKeyFile(JRequest::getInt('operator_id', null, 'method'));
		}
	}

	function edit()
	{
		$mainframe = & JFactory::getApplication();

		if(!JRequest::getVar('operator_id', null, 'method'))
		{
			return $mainframe->redirect('index.php?option=com_jlivechat&view=operators', JText::_('UNKNOWN_ERROR_OCCURRED'), 'error');
		}

		$viewName = JRequest::getCmd('view');

		$document = & JFactory::getDocument();
		$vType = $document->getType();

		// Get/Create the view
		$view = & $this->getView($viewName, $vType);

		// Set the layout
		$view->setLayout('default');

		return $view->display('edit');
	}

	function update_operator()
	{
		JRequest::checkToken() or die('Invalid Token');

		$mainframe = & JFactory::getApplication();

		if(JRequest::getInt('operator_id', null, 'method'))
		{
			$operatorId = JRequest::getInt('operator_id', null, 'method');
			$altName = JRequest::getVar('alt_name', null, 'method');
			$monitorPermission = JRequest::getInt('monitor_permission_value', 1, 'method');
			$operator2operatorPermission = JRequest::getInt('operator2operator_permission_value', 1, 'method');
			$messagePermission = JRequest::getInt('message_permission_value', 1, 'method');
			$useSSL = JRequest::getInt('use_ssl_value', 0, 'method');
			$ipBlockerPermission = JRequest::getInt('ipblocker_value', 1, 'method');

			$department = JRequest::getVar('department', null, 'method');

			$operator = & CMSModel::getInstance('OperatorAdmin', 'JLiveChatModel');

			$operator->updateOperator($operatorId, $altName, $department);

			$operator->setPermission($operatorId, 'website_monitor', $monitorPermission);
			$operator->setPermission($operatorId, 'operator2operator', $operator2operatorPermission);
			$operator->setPermission($operatorId, 'messages', $messagePermission);
			$operator->setPermission($operatorId, 'use_ssl', $useSSL);
			$operator->setPermission($operatorId, 'ipblocker', $ipBlockerPermission);

			$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
			$hostedModeObj->refreshSettings();
			$hostedModeObj->queueForceSync('#__jlc_operator');
			$hostedModeObj->forceSyncToRemote();
		}

		return $mainframe->redirect('index.php?option=com_jlivechat&view=operators', JText::_('OPERATOR_UPDATED_SUCCESSFULLY'));
	}

	function move_up_key_sort_order()
	{
		$operatorId = JRequest::getInt('o', null, 'method');

		$model = & CMSModel::getInstance('OperatorAdmin', 'JLiveChatModel');

		$model->moveUpSortOrder($operatorId);
		$model->fixOrders();

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->refreshSettings();
		$hostedModeObj->queueForceSync('#__jlc_operator');
		$hostedModeObj->forceSyncToRemote();

		jexit();
	}

	function move_down_key_sort_order()
	{
		$operatorId = JRequest::getVar('o', null, 'method');

		$model = & CMSModel::getInstance('OperatorAdmin', 'JLiveChatModel');

		$model->moveDownSortOrder($operatorId);
		$model->fixOrders();

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->refreshSettings();
		$hostedModeObj->queueForceSync('#__jlc_operator');
		$hostedModeObj->forceSyncToRemote();

		jexit();
	}

	function save_operators()
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

		$model = & CMSModel::getInstance('OperatorAdmin', 'JLiveChatModel');
		$model->saveSortOrders($newSorts);
		$model->fixOrders();

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->refreshSettings();
		$hostedModeObj->queueForceSync('#__jlc_operator');
		$hostedModeObj->forceSyncToRemote();

		$uri->delVar('task');

		return $mainframe->redirect($uri->toString(), JText::_('NEW_SORT_SAVED_SUCCESSFULLY'));
	}

	function delete_operators()
	{
		$mainframe = & JFactory::getApplication();

		$selectedOperatorsList = JRequest::getVar('selected_operators', '', 'method');

		$selectedOperatorsList = preg_replace('@[,]+$@', '', $selectedOperatorsList);

		if(strlen($selectedOperatorsList) > 0)
		{
			$deleteOperatorsArr = explode(',', $selectedOperatorsList);

			$model = & CMSModel::getInstance('OperatorAdmin', 'JLiveChatModel');

			if(count($deleteOperatorsArr) > 0)
			{
				foreach($deleteOperatorsArr as $operatorId)
				{
					$model->deleteOperator($operatorId);
				}

				$model->fixKeyOrders();
			}
		}

		$hostedModeObj = & CMSModel::getInstance('HostedModeAdmin', 'JLiveChatModel');
		$hostedModeObj->refreshSettings();
		$hostedModeObj->queueForceSync('#__jlc_operator');
		$hostedModeObj->forceSyncToRemote();

		$uri = & JFactory::getURI();
		$uri->delVar('task');

		return $mainframe->redirect($uri->toString(), JText::_('OPERATORS_DELETED_SUCCESSFULLY'));
	}
}