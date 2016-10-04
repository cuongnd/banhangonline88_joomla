<?php
/**
 * @package JAdmin!
 * @version 1.5.4.3
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

class JAdminControllerUsers extends CMSController
{
	/**
	 * Display the view
	 */
	function display()
	{
		$mainframe = & JFactory::getApplication();

		if(!function_exists('curl_init')) $mainframe->enqueueMessage(JText::_('CURL_NOT_INSTALLED'), 'error');

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

	function new_user()
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

	function create_new()
	{
		JRequest::checkToken() or die('Invalid Token');

		$mainframe = & JFactory::getApplication();

		$user = & CMSModel::getInstance('UserAdmin', 'JAdminModel');

		$descName = JRequest::getVar('desc_name', null, 'method');
		$mappedToAdminId = JRequest::getInt('mapped_to_admin_value', 42, 'method');
		$monitorPermission = JRequest::getInt('monitor_permission_value', 1, 'method');
		$useSSL = JRequest::getInt('use_ssl_value', 0, 'method');
		$ipBlockerPermission = JRequest::getInt('ipblocker_value', 1, 'method');
		$department = JRequest::getVar('department', null, 'method');
		$permissions = JRequest::getVar('permissions', array(), 'method');
		$ipRestrict = JRequest::getVar('ip_restrict', null, 'method');

		if($mappedToAdminId < 1) $mappedToAdminId = 42;

		$userId = $user->createNew($mappedToAdminId, $descName, $department);

		$user->setPermission($userId, 'website_monitor', $monitorPermission);
		$user->setPermission($userId, 'use_ssl', $useSSL);
		$user->setPermission($userId, 'ipblocker', $ipBlockerPermission);
		$user->setPermission($userId, 'permissions', $permissions);
		$user->setPermission($userId, 'ip_restrict', array());

		if(!empty($ipRestrict))
		{
			preg_match_all('@([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)@', $ipRestrict, $matches);

			if(!empty($matches[1])) $user->setPermission($userId, 'ip_restrict', $matches[1]);
		}

		return $mainframe->redirect('index.php?option=com_jadmin&view=users', JText::_('USER_CREATED_SUCCESSFULLY'));
	}

	function checkPlugin()
	{
		$db = & JFactory::getDBO();

		$sql = "UPDATE #__extensions SET
		    enabled = 1
		WHERE element = 'jadmin'
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

	function get_users()
	{
		$mainframe = & JFactory::getApplication();

		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate("D, d M Y H:i:s").'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: text/x-json');

		$limitstart = JRequest::getInt('startIndex', 0, 'method');
		$page = JRequest::getInt('page', 1, 'method');
		$resultsPerPage = JRequest::getInt('rp', 10, 'method');
		$sortname = JRequest::getVar('sort', 'sort_order', 'method');
		$sortorder = JRequest::getVar('dir', 'asc', 'method');
		$query = JRequest::getVar('query', null, 'method');
		$qtype = JRequest::getVar('qtype', null, 'method');

		$model = & CMSModel::getInstance('UserAdmin', 'JAdminModel');
		$settings = & CMSModel::getInstance('SettingAdmin', 'JAdminModel');

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

		$websiteUsers = $model->getData();
		$pagination = $model->getPagination();

		if(!empty($websiteUsers))
		{
			foreach($websiteUsers as $a => $operator)
			{
				if(isset($websiteUsers[$a]['params']->user_ip))
				{
					$websiteUsers[$a]['user_ip'] = $websiteUsers[$a]['params']->user_ip;
				}
				else
				{
					$websiteUsers[$a]['user_ip'] = JText::_('UNKNOWN');
				}

				$websiteUsers[$a]['last_auth_date'] .= '<br />'.$websiteUsers[$a]['user_ip'];

				// Operator Mobile Access Key Code
				$websiteUsers[$a]['mobile_key_code'] = strtoupper(substr($websiteUsers[$a]['auth_key'], 0, 15));

				if($websiteUsers[$a]['is_enabled'] == 1)
				{
					$websiteUsers[$a]['is_enabled'] = '<a class="green" href="javascript: void(0);" onclick="toggleStatus('.$websiteUsers[$a]['user_id'].');">'.JText::_('ENABLED').'</span>';
				}
				else
				{
					$websiteUsers[$a]['is_enabled'] = '<a class="red" href="javascript: void(0);" onclick="toggleStatus('.$websiteUsers[$a]['user_id'].');">'.JText::_('DISABLED').'</span>';
				}

				if(empty($websiteUsers[$a]['department'])) $websiteUsers[$a]['department'] = '- None -';

				$websiteUsers[$a]['sort_order'] = '<input style="width: 25px;" size="2" type="text" name="sort_order_'.$websiteUsers[$a]['user_id'].'" value="'.$websiteUsers[$a]['sort_order'].'" />&nbsp;&nbsp;';
				$websiteUsers[$a]['sort_order'] .= '<a href="javascript: void(0);" onclick="moveUpSortOrder('.$websiteUsers[$a]['user_id'].');"><img src="components/com_jadmin/assets/images/arrows/up.gif" width="13" height="15" border="0" alt="Move Up" /></a>&nbsp;<a href="javascript: void(0);" onclick="moveDownSortOrder('.$websiteUsers[$a]['user_id'].');"><img src="components/com_jadmin/assets/images/arrows/down.gif" width="13" height="15" border="0" alt="Move Down" /></a>';

				$websiteUsers[$a]['options'] = '<a id="edit'.$websiteUsers[$a]['user_id'].'" href="index.php?option=com_jadmin&view=users&task=edit&user_id='.$websiteUsers[$a]['user_id'].'">'.JText::_('EDIT_USER').'</a>';
				$websiteUsers[$a]['options'] .= '<div class="clr" style="padding-bottom: 5px;">&nbsp;</div>';
				$websiteUsers[$a]['options'] .= '<a id="accesskey'.$websiteUsers[$a]['user_id'].'" href="index.php?option=com_jadmin&view=users&task=download_key&user_id='.$websiteUsers[$a]['user_id'].'&t='.time().'">'.JText::_('DOWNLOAD_ACCESS_KEY').'</a>';


				$websiteUsers[$a]['options'] .= '<script type="text/javascript">
							var keyBtn'.$websiteUsers[$a]['user_id'].' = new YAHOO.widget.Button("accesskey'.$websiteUsers[$a]['user_id'].'");
							var editBtn'.$websiteUsers[$a]['user_id'].' = new YAHOO.widget.Button("edit'.$websiteUsers[$a]['user_id'].'");
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
			'Records' => $websiteUsers
		);

		echo json_encode($output);

		jexit();
	}

	function toggle_status()
	{
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate("D, d M Y H:i:s").'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');

		$operators = & CMSModel::getInstance('UserAdmin', 'JAdminModel');

		$operators->toggleStatus(JRequest::getInt('uid', null, 'method'));

		jexit();
	}

	function download_key()
	{
		$model = & CMSModel::getInstance('UserAdmin', 'JAdminModel');

		if(JRequest::getInt('user_id', null, 'method'))
		{
			return $model->downloadKeyFile(JRequest::getInt('user_id', null, 'method'));
		}
	}

	function edit()
	{
		$mainframe = & JFactory::getApplication();

		if(!JRequest::getVar('user_id', null, 'method'))
		{
			return $mainframe->redirect('index.php?option=com_jadmin&view=users', JText::_('UNKNOWN_ERROR_OCCURRED'), 'error');
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

	function update_user()
	{
		JRequest::checkToken() or die('Invalid Token');

		$mainframe = & JFactory::getApplication();

		if(JRequest::getInt('user_id', null, 'method'))
		{
			$userId = JRequest::getInt('user_id', null, 'method');
			$descName = JRequest::getVar('desc_name', null, 'method');
			$mappedToAdminId = JRequest::getInt('mapped_to_admin_value', 42, 'method');
			$monitorPermission = JRequest::getInt('monitor_permission_value', 1, 'method');
			$useSSL = JRequest::getInt('use_ssl_value', 0, 'method');
			$ipBlockerPermission = JRequest::getInt('ipblocker_value', 1, 'method');
			$department = JRequest::getVar('department', null, 'method');
			$permissions = JRequest::getVar('permissions', array(), 'method');
			$ipRestrict = JRequest::getVar('ip_restrict', null, 'method');

			$settings = & CMSModel::getInstance('SettingAdmin', 'JAdminModel');
			$model = & CMSModel::getInstance('UserAdmin', 'JAdminModel');

			$model->updateUser($userId, $mappedToAdminId, $descName, $department);

			$model->setPermission($userId, 'website_monitor', $monitorPermission);
			$model->setPermission($userId, 'use_ssl', $useSSL);
			$model->setPermission($userId, 'ipblocker', $ipBlockerPermission);
			$model->setPermission($userId, 'permissions', $permissions);
			$model->setPermission($userId, 'ip_restrict', array());

			if(!empty($ipRestrict))
			{
				preg_match_all('@([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)@', $ipRestrict, $matches);

				if(!empty($matches[1]))
				{
					$model->setPermission($userId, 'ip_restrict', $matches[1]);
				}
			}

			$settings->touchSettings();
		}

		return $mainframe->redirect('index.php?option=com_jadmin&view=users', JText::_('UPDATED_SUCCESSFULLY'));
	}

	function move_up_key_sort_order()
	{
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate("D, d M Y H:i:s").'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');

		$userId = JRequest::getInt('o', null, 'method');

		$model = & CMSModel::getInstance('UserAdmin', 'JAdminModel');

		$model->moveUpSortOrder($userId);
		$model->fixOrders();

		jexit();
	}

	function move_down_key_sort_order()
	{
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate("D, d M Y H:i:s").'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');

		$userId = JRequest::getVar('o', null, 'method');

		$model = & CMSModel::getInstance('UserAdmin', 'JAdminModel');

		$model->moveDownSortOrder($userId);
		$model->fixOrders();

		jexit();
	}

	function save_users()
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

		$model = & CMSModel::getInstance('UserAdmin', 'JAdminModel');
		$model->saveSortOrders($newSorts);
		$model->fixOrders();

		$uri->delVar('task');

		return $mainframe->redirect($uri->toString(), JText::_('NEW_SORT_SAVED_SUCCESSFULLY'));
	}

	function delete_users()
	{
		$mainframe = & JFactory::getApplication();

		$selectedList = JRequest::getVar('selected_rows', '', 'method');

		$selectedList = preg_replace('@[,]+$@', '', $selectedList);

		if(strlen($selectedList) > 0)
		{
			$deleteUsersArr = explode(',', $selectedList);

			$model = & CMSModel::getInstance('UserAdmin', 'JAdminModel');

			if(!empty($deleteUsersArr))
			{
				foreach($deleteUsersArr as $userId)
				{
					$model->delete($userId);
				}

				$model->fixKeyOrders();
			}
		}

		$uri = & JFactory::getURI();
		$uri->delVar('task');

		return $mainframe->redirect($uri->toString(), JText::_('DELETED_SUCCESSFULLY'));
	}
}