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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class JAdminModelUserAdmin extends CMSModel
{
    /**
     * Items total
     * @var integer
     */
    var $_total = null;
    var $_data = array();

    /**
     * Pagination object
     * @var object
     */
    var $_pagination = null;

    var $_whereColumn = null;
    var $_whereValue = null;

    var $_sortColumn = null;
    var $_sortOrder = null;
    
    var $_settings = null;

    var $_fieldSeperator = "\t";

    var $_availablePermissions = array(
	25 => array(
	    'label' => 'USER_MANAGER',
	    'option' => 'com_users',
	    'task' => 'view'
	),

	23 => array(
	    'label' => 'LOGIN_AS_USER'
	),

	24 => array(
	    'label' => 'IP_BLOCKER'
	),

	1 => array(
	    'label' => 'MEDIA_MANAGER',
	    'option' => 'com_media'
	),

	2 => array(
	    'label' => 'GLOBAL_CONFIG',
	    'option' => 'com_config'
	),

	3 => array(
	    'label' => 'MENU_MANAGER',
	    'option' => 'com_menus',
	),

	4 => array(
	    'label' => 'MENU_TRASH',
	    'option' => 'com_trash',
	    'task' => 'viewMenu'
	),

	5 => array(
	    'label' => 'ARTICAL_MANAGER',
	    'option' => 'com_content'
	),

	6 => array(
	    'label' => 'ARTICAL_TRASH',
	    'option' => 'com_trash',
	    'task' => 'viewContent'
	),

	7 => array(
	    'label' => 'SECTION_MANAGER',
	    'option' => 'com_sections',
	    'scope' => 'content'
	),

	8 => array(
	    'label' => 'CATEGORY_MANAGER',
	    'option' => 'com_categories'
	),

	9 => array(
	    'label' => 'FRONTPAGE_MANAGER',
	    'option' => 'com_frontpage',
	),

	10 => array(
	    'label' => 'FRONTPAGE_MANAGER',
	    'option' => 'com_frontpage',
	),

	11 => array(
	    'label' => 'INSTALL_UNINSTALL',
	    'option' => 'com_installer',
	),

	12 => array(
	    'label' => 'MODULE_MANAGER',
	    'option' => 'com_modules',
	),

	13 => array(
	    'label' => 'PLUGIN_MANAGER',
	    'option' => 'com_plugins',
	),

	14 => array(
	    'label' => 'TEMPLATE_MANAGER',
	    'option' => 'com_templates',
	),

	15 => array(
	    'label' => 'LANGUAGE_MANAGER',
	    'option' => 'com_languages',
	),

	16 => array(
	    'label' => 'READ_MESSAGES',
	    'option' => 'com_messages',
	),

	17 => array(
	    'label' => 'WRITE_MESSAGE',
	    'option' => 'com_messages',
	    'task' => 'add'
	),

	18 => array(
	    'label' => 'MASS_MAIL',
	    'option' => 'com_massmail'
	),

	19 => array(
	    'label' => 'GLOBAL_CHECKIN',
	    'option' => 'com_checkin'
	),

	20 => array(
	    'label' => 'CLEAN_CACHE',
	    'option' => 'com_cache'
	),

	21 => array(
	    'label' => 'JOOMLA_HELP',
	    'option' => 'com_admin',
	    'task' => 'help'
	),

	22 => array(
	    'label' => 'SYSTEM_INFO',
	    'option' => 'com_admin',
	    'task' => 'sysinfo'
	)
    );

    function __construct()
    {
	$this->JAdminModelUserAdmin();
    }

    function JAdminModelUserAdmin()
    {
	parent::__construct();

	$mainframe =& JFactory::getApplication();

	require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'jadmdateadmin.php';

	$this->_settings =& CMSModel::getInstance('SettingAdmin', 'JAdminModel');

	// Get pagination request variables
	$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

	// In case limit has been changed, adjust it
	$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

	$this->setState('limit', $limit);
	$this->setState('limitstart', $limitstart);

	$this->_loadPermissions();
    }

    function _getOptions()
    {
	$allOptions = array();
	
	foreach($this->_availablePermissions as $permId => $perm)
	{
	    if(isset($perm['option'])) $allOptions[] = "'".$perm['option']."'";
	}

	$allOptions = array_unique($allOptions);

	return $allOptions;
    }

    function _loadPermissions()
    {
	foreach($this->_availablePermissions as $permId => $perm)
	{
	    $this->_availablePermissions[$permId]['label'] = JText::_($this->_availablePermissions[$permId]['label']);
	}

	$sql = "SELECT
		    `name`,
		    element AS 'option'
		FROM #__extensions
		WHERE `element` NOT IN (".implode(',', $this->_getOptions()).",'com_user','com_wrapper','com_mailto','com_cpanel')
		AND type = 'component';";
	$this->_db->setQuery($sql);
	$results = $this->_db->loadAssocList();

	if(!empty($results))
	{
	    foreach($results as $row)
	    {
		$this->_availablePermissions[$row['option']] = array(
							'label' => $row['name'],
							'option' => $row['option']
							);
	    }
	}
    }

    function getAvailablePermissions()
    {
	return $this->_availablePermissions;
    }

    function setFilter($columnName, $value)
    {
	$this->_whereColumn = $columnName;
	$this->_whereValue = $value;
    }

    function setSort($columnName, $order)
    {
	$this->_sortColumn = $columnName;
	$this->_sortOrder = $order;
    }

    function _buildQuery()
    {
	$sql = "SELECT 
		    jlco.user_id,
		    jlco.fullname,
		    jlco.department,
		    jlco.sort_order,
		    jlco.params,
		    jlco.is_enabled,
		    jlco.cdate,
		    jlco.mdate,
		    jlco.last_auth_date,
		    jlco.auth_key 
		FROM #__jadm_user jlco ";

	if($this->_whereColumn && $this->_whereValue)
	{
	    $sql .= " WHERE ".$this->_whereColumn." = '".$this->_whereValue."' ";
	}

	if($this->_sortColumn && $this->_sortOrder)
	{
	    $sql .= " ORDER BY ".$this->_sortColumn." ".$this->_sortOrder;
	}
	else
	{
	    $sql .= " ORDER BY jlco.sort_order ASC";
	}

	return $sql;
    }

    function getData()
    {
	$mainframe =& JFactory::getApplication();

	// if data hasn't already been obtained, load it
	if(empty($this->_data))
	{
	    $query = $this->_buildQuery();

	    $this->_db->setQuery( $query, $this->getState('limitstart'), $this->getState('limit') );
	    
	    $this->_data = $this->_db->loadAssocList();

	    if(!empty($this->_data))
	    {
		foreach($this->_data as $a => $row)
		{
		    $this->_data[$a]['params'] = json_decode($this->_data[$a]['params']);

		    if(!is_object($this->_data[$a]['params']))
		    {
			$this->_data[$a]['params'] = new stdClass();
		    }

		    if($this->_data[$a]['last_auth_date'] > 0)
		    {
			$date = new JAdminModelJADMDateAdmin($this->_data[$a]['last_auth_date']);

			$this->_data[$a]['last_auth_date'] = $date->toFormat('%m/%d/%Y %H:%M:%S');
		    }
		    else
		    {
			$this->_data[$a]['last_auth_date'] = JText::_('UNKNOWN');
		    }
		}
	    }
	}
	
	return $this->_data;
    }

    function getTotal()
    {
	// Load the content if it doesn't already exist
	if (empty($this->_total))
	{
	    $query = $this->_buildQuery();
	    $this->_total = $this->_getListCount($query);
	}
	
	return $this->_total;
    }

    function getPagination()
    {
	// Load the content if it doesn't already exist
	if (empty($this->_pagination))
	{
	    jimport('joomla.html.pagination');
	    $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
	}

	return $this->_pagination;
    }

    function getDepartments()
    {
	$sql = "SELECT 
		    DISTINCT(department) AS 'department' 
		FROM #__jadm_user 
		ORDER BY department ASC;";
	$this->_db->setQuery($sql);

	return $this->_db->loadAssocList();
    }

    function createNew($mappedToAdminId=42, $descName=null, $department=null)
    {
	$date = new JAdminModelJADMDateAdmin();

	// No double quotes allowed
	if($department) $department = str_replace('"', '', $department);

	$defaultParams = new stdClass();

	$defaultParams->website_monitor = 1;
	$defaultParams->ipblocker = 1;
	$defaultParams->use_ssl = 0;

	$data = new stdClass();

	$data->fullname = $descName;
	$data->department = $department;
	$data->sort_order = $this->getLastSortOrder();
	$data->params = json_encode($defaultParams);
	$data->is_enabled = 1;
	$data->cdate = $date->toUnix();
	$data->mdate = $data->cdate;
	$data->auth_key = $this->_generateAuthKey();
	$data->internal_user_id = $mappedToAdminId;

	$this->_db->insertObject('#__jadm_user', $data, 'user_id');

	return $this->_db->insertid();
    }

    function updateUser($adminId, $mappedToAdminId=42, $descName=null, $department=null)
    {
	if(!$adminId) return false;
	
	$date = new JAdminModelJADMDateAdmin();

	// No double quotes allowed
	if($department) $department = str_replace('"', '', $department);
	
	$data = new stdClass();

	$data->user_id = (int)$adminId;
	$data->fullname = $descName;
	$data->department = $department;
	$data->mdate = $date->toUnix();

	$this->_db->updateObject('#__jadm_user', $data, 'user_id');

	$this->_settings->touchSettings();

	return true;
    }

    function setPermission($adminId, $name, $val)
    {
	$perms = $this->getPermissions($adminId);

	if(!is_object($perms)) $perms = new stdClass();

	$perms->$name = $val;

	$data = new stdClass();

	$data->user_id = $adminId;
	$data->params = json_encode($perms);

	$this->_db->updateObject('#__jadm_user', $data, 'user_id');

	$this->_settings->touchSettings();

	return true;
    }

    function getPermissions($adminId)
    {
	$sql = "SELECT params FROM #__jadm_user 
		WHERE user_id = ".(int)$adminId."
		LIMIT 1;";
	$this->_db->setQuery($sql);

	$perms = $this->_db->loadResult();

	return json_decode($perms);
    }

    function getUser($adminId)
    {
	$sql = "SELECT
		    jlco.user_id,
		    jlco.fullname,
		    jlco.department,
		    jlco.sort_order,
		    jlco.params,
		    jlco.is_enabled,
		    jlco.cdate,
		    jlco.mdate,
		    jlco.last_auth_date,
		    jlco.internal_user_id, 
		    u.name,
		    u.username
		FROM #__jadm_user jlco

		INNER JOIN #__users u
		ON u.id = jlco.internal_user_id 

		WHERE jlco.user_id = ".(int)$adminId."
		LIMIT 1;";

	$this->_db->setQuery($sql);
	
	$result = $this->_db->loadAssoc();
	
	if(isset($result['params']))
	{
	    $result['params'] = json_decode($result['params']);
	}
    
	return $result;
    }

    function getLastSortOrder()
    {
	$sql = "SELECT MAX(sort_order) FROM #__jadm_user;";
	$this->_db->setQuery($sql);

	$maxSort = (int)$this->_db->loadResult();

	++$maxSort;

	return $maxSort;
    }

    function _generateAuthKey()
    {
	return substr(md5(uniqid(rand(), true)).md5(uniqid(rand(), true)), 0, rand(52, 64));
    }

    function toggleStatus($userId=null)
    {
	if(empty($userId)) return false;

	$sql = "SELECT 
		    is_enabled 
		FROM #__jadm_user 
		WHERE user_id = ".(int)$userId."
		LIMIT 1;";
	$this->_db->setQuery($sql);

	if($this->_db->loadResult() == 1)
	{
	    // Currently enabled, disable
	    $newStatus = 0;
	}
	else
	{
	    // Currently disabled, enable
	    $newStatus = 1;
	}

	$data = new stdClass();

	$data->user_id = (int)$userId;
	$data->is_enabled = $newStatus;

	$this->_db->updateObject('#__jadm_user', $data, 'user_id');

	return true;
    }

    function downloadKeyFile($adminId, $forceDownload=true)
    {
	$mainframe =& JFactory::getApplication();

	$authKey = $this->_getAuthKey($adminId);

        $uri =& JFactory::getUri();

	$uri->setScheme('http'); // Force HTTP

	$settingsObj =& CMSModel::getInstance('SettingAdmin', 'JAdminModel');
	
	$callbackUri = preg_replace('@[^/]+/[^/]+$@', 'index.php', $uri->toString());
	$callbackUri .= '?option=com_jadmin&view=api&task=api&format=raw&no_html=1&do_not_log=true';
        
        // required for IE, otherwise Content-disposition is ignored
        if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');

        $ctype='application/octet-stream';

        $keyContents = $callbackUri.$this->_fieldSeperator.$authKey;
        $keyContents = base64_encode($keyContents);

        $keySize = strlen($keyContents);

	if($forceDownload)
	{
	    $filename = $uri->toString(array('host'));
	    $filename .= '_'.date('m_d_Y');
	    $filename = preg_replace('@[^-a-zA-Z0-9_]+@', '_', $filename);
	    $filename .= '.jkf';

	    header('Content-Type: application/force-download');
	    header('Content-Disposition: attachment; filename="'.$filename.'"');
	    header('Content-Transfer-Encoding: binary');
	    header('Accept-Ranges: bytes');
	    header('Cache-control: private');
	    header('Pragma: private');
	    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	    header('Content-Length: '.$keySize);

	    echo $keyContents;

	    flush();
	    jexit();
	}
	else
	{
	    return $keyContents;
	}
    }

    function _getAuthKey($userId)
    {
	$sql = "SELECT auth_key FROM #__jadm_user 
		WHERE user_id = ".(int)$userId."
		LIMIT 1;";
	$this->_db->setQuery($sql);

	return $this->_db->loadResult();
    }

    function moveUpSortOrder($adminId)
    {
	$dateObj = new JAdminModelJADMDateAdmin();
	
	$newSortOrder = $this->getSortOrder($adminId)-1;

	if($newSortOrder < 1) $newSortOrder = 1;

	$sql = "UPDATE #__jadm_user SET
		    sort_order = sort_order+1,
		    mdate = ".$dateObj->toUnix()." 
		WHERE sort_order >= ".(int)($newSortOrder).";";
        $this->_db->setQuery($sql);
	$this->_db->query();

	$sql = "UPDATE #__jadm_user SET
		    sort_order = ".$newSortOrder.",
		    mdate = ".$dateObj->toUnix()." 
		WHERE user_id = ".(int)$adminId.";";
        $this->_db->setQuery($sql);
	$this->_db->query();

	return true;
    }

    function moveDownSortOrder($adminId)
    {
	$dateObj = new JAdminModelJADMDateAdmin();
	
	$newSortOrder = $this->getSortOrder($adminId)+1;

	if($newSortOrder < 1) $newSortOrder = 1;

	$sql = "UPDATE #__jadm_user SET
		    sort_order = sort_order-1,
		    mdate = ".$dateObj->toUnix()." 
		WHERE sort_order >= ".(int)$newSortOrder.";";
        $this->_db->setQuery($sql);
	$this->_db->query();

	$sql = "UPDATE #__jadm_user SET
		    sort_order = ".$newSortOrder.",
		    mdate = ".$dateObj->toUnix()." 
		WHERE user_id = ".(int)$adminId.";";
        $this->_db->setQuery($sql);
	$this->_db->query();

	return true;
    }

    function getSortOrder($adminId)
    {
	$sql = "SELECT sort_order FROM #__jadm_user 
		WHERE user_id = ".(int)$adminId."
		LIMIT 1;";
	$this->_db->setQuery($sql);

	return $this->_db->loadResult();
    }

    function saveSortOrders($sortArray)
    {
	if(empty($sortArray)) return false;
	
	$dateObj = new JAdminModelJADMDateAdmin();

	foreach($sortArray as $userId => $newSortOrder)
	{
	    $data = new stdClass();

	    $data->user_id = (int)$userId;
	    $data->sort_order = (int)$newSortOrder;
	    $data->mdate = $dateObj->toUnix();
	    
	    $this->_db->updateObject('#__jadm_user', $data, 'user_id');
	}

	return true;
    }

    function delete($userId)
    {
	// Delete any IP Blocker rules
	$sql = "DELETE FROM #__jadm_ipblocker
		WHERE user_id = ".(int)$userId.";";
	$this->_db->setQuery($sql);
	
	$sql = "DELETE FROM #__jadm_user
		WHERE user_id = ".(int)$userId.";";
	$this->_db->setQuery($sql);
	$this->_db->query();

	return true;
    }

    function getGroups()
    {
	$sql = "SELECT
		    id,
		    name
		FROM #__core_acl_aro_groups
		WHERE id NOT IN (17,28,29,30);";

	$this->_db->setQuery( $sql );
	
	return $this->_db->loadAssocList();
    }

    function getAll()
    {
	$this->setState('limit', null);
	$this->setState('limitstart', null);

	return $this->getData();
    }

    function fixOrders()
    {
	$dateObj = new JAdminModelJADMDateAdmin();
	
	$sql = "SELECT SQL_NO_CACHE
		    user_id
		FROM #__jadm_user

		ORDER BY sort_order ASC;";
        $this->_db->setQuery($sql);

	$results = $this->_db->loadAssocList();

	for($a = 0; $a < count($results); $a++)
	{
	    $data = new stdClass();

	    $data->user_id = $results[$a]['user_id'];
	    $data->sort_order = $a+1;
	    $data->mdate = $dateObj->toUnix();

	    $this->_db->updateObject('#__jadm_user', $data, 'user_id');
	}

	return true;
    }

    function fixKeyOrders()
    {
	return $this->fixOrders();
    }

    function getAdminUsers()
    {
	$sql = "SELECT
		    u.id,
		    u.name,
		    u.username 
		FROM #__users u 

		INNER JOIN #__user_usergroup_map um
		ON um.user_id = u.id 

		INNER JOIN #__usergroups ug 
		ON ug.id = um.group_id  
 
		WHERE ug.title IN ('Super Users', 'Super Administrator', 'Administrator', 'Manager')
		GROUP BY u.id 
		ORDER BY u.username ASC;";
	$this->_db->setQuery($sql);
	
	return $this->_db->loadAssocList();
    }

    function isGatewayAuthKeyValid($authKey)
    {
	$dateObj = new JAdminModelJADMDateAdmin();

	$expireTime = $dateObj->toUnix()-100;

	// Remove Expired Rows First
	$sql = "DELETE FROM #__jadm_user_keys
		WHERE is_expired = 1
		OR cdate <= ".$expireTime.";";
	$this->_db->setQuery($sql);
	$this->_db->query();

	$sql = "SELECT
		    j.`key_id`,
		    j.`user_id`,
		    j.`auth_key`,
		    j.`is_expired`,
		    j.`cdate`,
		    j.`mdate`
		FROM #__jadm_user_keys j
		WHERE j.auth_key = ".$this->_db->Quote($authKey)."
		AND j.is_expired = 0
		LIMIT 1;";
	$this->_db->setQuery($sql);

	$result = $this->_db->loadAssoc();

	$sql = "UPDATE #__jadm_user_keys SET
		    is_expired = 1,
		    mdate = ".$dateObj->toUnix()."
		WHERE auth_key = ".$this->_db->Quote($authKey).";";
	$this->_db->setQuery($sql);
	$this->_db->query();

	return $result;
    }

    function loginAsAdminUser($adminId)
    {
	$user =& JFactory::getUser();

	$session =& JFactory::getSession();
	$session->set('jadmin_admin_id', $adminId);

	if($user->get('id') > 0) return true;

	$sql = "SELECT internal_user_id FROM #__jadm_user
		WHERE user_id = ".(int)$adminId." 
		LIMIT 1;";
	$this->_db->setQuery($sql);

	$internalAdminId = $this->_db->loadResult();
	
	// Log admin in
	$options = array();

	$user =& JUser::getInstance($internalAdminId);

	$response = new stdClass();
	$response->email = $user->email;
	$response->username = $user->username;
	$response->fullname = $user->name;
	$response->status = 1;
	$response->error_message = '';
	$response->type = 'Joomla';
	$response->password = '';

	// Import the user plugin group
	jimport('joomla.plugin.helper');

	JPluginHelper::importPlugin('user');

	$mainframe =& JFactory::getApplication();
	
	// OK, the credentials are authenticated.  Lets fire the onLogin event
	return $mainframe->triggerEvent('onUserLogin', array((array)$response, $options));
    }

    function enforceAdminPermissions()
    {
	$user =& JFactory::getUser();

	if($user->get('id') > 0)
	{
	    $session =& JFactory::getSession();

	    $jadminAdminId = $session->get('jadmin_admin_id');

	    if(!empty($jadminAdminId))
	    {
		// This admin user has logged in using jadmin
		// Enforce JAdmin User Permissions
		$currentOption = JRequest::getCmd('option');
		$currentTask = JRequest::getCmd('task');
		
		// If the option is empty, then they are on the control panel page
		if(empty($currentOption) || ($currentOption == 'com_login' && $currentTask == 'logout')) return true;
		
		$adminUser = $this->getUser($jadminAdminId);

		if(isset($adminUser['params']->permissions))
		{
		    $currentUriPermissionId = $this->getCurrentUriPermissionId();
		    
		    if(!$currentUriPermissionId) $this->accessDenied();
		    
		    if(!in_array($currentUriPermissionId, $adminUser['params']->permissions)) $this->accessDenied();
		}
	    }

	    return true;
	}

	return false;
    }

    function getCurrentUriPermissionId()
    {
	$uri =& JFactory::getURI();
	$uriParts = $uri->getQuery(true);

	if(empty($uriParts)) $uriParts = JRequest::get('method');
	
	foreach($this->_availablePermissions as $permissionId => $permissionDetails)
	{
	    unset($permissionDetails['label']);

	    $permissionCheckResults = array();

	    foreach($permissionDetails as $key => $value)
	    {
		$isMatch = true;
		
		if(!isset($uriParts[$key]))
		{
		    $isMatch = false;
		}
		else
		{
		    if($uriParts[$key] == $value)
		    {
			// We have a match
			$isMatch = true;
		    }
		    else
		    {
			$isMatch = false;
		    }
		}

		$permissionCheckResults[$key] = $isMatch;
	    }

	    if(!empty($permissionCheckResults))
	    {
		$allCheckOut = true;
		
		foreach($permissionCheckResults as $checkResult)
		{
		    if(!$checkResult)
		    {
			$allCheckOut = false;
		    }
		}

		if($allCheckOut)
		{
		    // This is the permission we are looking for
		    return $permissionId;
		}
	    }
	}

	return false;
    }

    function accessDenied()
    {
	echo 'Access Denied<br /><br />';
	echo '<input type="button" value="Go Back" onclick="history.go(-1)">&nbsp;&nbsp;<input type="button" value="Home" onclick="document.location.href=\'index.php\';">';
	jexit();
    }
}
