<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Required helpers
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/users.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/user_fields.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/users/tmpl/default.php";

// Set toolbar and page title
HelpdeskUsersAdminHelper::addToolbar($task);
HelpdeskUsersAdminHelper::setDocument($task);

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

$id = JRequest::getVar('id', '', '', 'int');

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'users', $task, $id);

switch ($task) {
	case "fieldsnew":
		editCustomField(0);
		break;

	case "fieldsedit":
		editCustomField($cid[0]);
		break;

	case "fieldssave":
		saveCustomField();
		break;

	case "fieldsremove":
		removeCustomField($cid);
		break;

	case 'saveorder':
		saveOrder();
		break;

	case "fields":
		showCustomField();
		break;

	case 'new':
		editUser(0);
		break;

	case 'edit':
		editUser($cid[0]);
		break;

	case 'save':
	case 'apply':
		saveUser($task);
		break;

	case 'cancel':
		cancelUser();
		break;

	case 'contact':
		$contact_id = JRequest::getVar('contact_id', '', 'POST', 'string');
		$mainframe->redirect('index.php?option=com_contact&task=editA&id=' . $contact_id);
		break;

	default:
		showUsers();
		break;
}

function showCustomField()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$session = JFactory::getSession();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));
	HelpdeskUtility::AppendResource('draganddrop.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// get the total number of records
	$sql = "SELECT count(*)
			FROM #__support_user_fields";
	$database->setQuery($sql);
	$total = $database->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$sql = "SELECT u.id, f.caption, f.ftype, u.required
			FROM #__support_user_fields u, #__support_custom_fields f
			WHERE u.id_field=f.id AND f.cftype='U'
			ORDER BY u.ordering";
	$database->setQuery($sql, $pageNav->limitstart, $pageNav->limit);
	$rows = $database->loadObjectList();

	users_fields_html::show($rows, $pageNav);
}

function editCustomField($uid = 0)
{
	$database = JFactory::getDBO();

	$row = new MaQmaHelpdeskTableUserFields($database);
	$row->load($uid);

	// Build Custom Fields select list
	$sql = "SELECT `id` AS value, `caption` AS text FROM #__support_custom_fields WHERE cftype='U' ORDER BY `caption`";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
	$lists['fields'] = JHTML::_('select.genericlist', $rows_wk, 'id_field', 'class="inputbox" size="1"', 'value', 'text', $row->id_field);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');
	$lists['required'] = HelpdeskForm::SwitchCheckbox('radio', 'required', $captions, $values, $row->required, 'switch');

	// Build Custom Fields select list
	$sql = "SELECT MAX(`ordering`)
			FROM #__support_user_fields";
	$database->setQuery($sql);
	$lists['ordering'] = $database->loadResult();

	users_fields_html::edit($row, $lists);
}

function saveCustomField()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableUserFields($database);
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!$row->bind($_POST))
	{
		echo "<script type='text/javascript'> alert('" . $row->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->check())
	{
		echo "<script type='text/javascript'> alert('" . $row->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store())
	{
		echo "<script type='text/javascript'> alert('" . $row->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->checkin();

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=users_fields");
}

function removeCustomField($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('wkfield_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_user_fields WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	//mosRedirect( "index.php?option=com_maqmahelpdesk&task=users_fields" );
	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=users_fields");
}

function saveOrder()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$orders = JRequest::getVar('contentTable', array(0), '', 'array');

	for ($i = 1; $i < (count($orders) - 1); $i++) {
		$sql = "UPDATE `#__support_user_fields`
				SET `ordering`=$i
				WHERE `id`=" . $orders[$i];
		$database->setQuery($sql);
		$database->query();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=users_fields", JText::_('new_ordering_save'));
}

function showUsers()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	$session = JFactory::getSession();
	$filter_logged = intval($mainframe->getUserStateFromRequest("filter_loggedcom_maqmahelpdesk", 'filter_logged', 0));
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));
	$search = JRequest::getVar('search', $session->get('filter_users', '', 'maqmahelpdesk'), 'POST', 'string');
	$search = (trim(JString::strtolower($search)));
	$session->set('filter_users', $search, 'maqmahelpdesk');
	$where = array();

	if ($search != "") {
		$where[] = "(a.username LIKE '%$search%' OR a.email LIKE '%$search%' OR a.name LIKE '%$search%')";
	}
	if ($filter_logged == 1) {
		$where[] = "s.userid = a.id";
	} else if ($filter_logged == 2) {
		$where[] = "s.userid IS NULL";
	}

	$query = "SELECT COUNT(a.id)"
		. "\n FROM #__users AS a";

	if ($filter_logged == 1 || $filter_logged == 2) {
		$query .= "\n INNER JOIN #__session AS s ON s.userid = a.id";
	}

	$query .= (count($where) ? "\n WHERE " . implode(' AND ', $where) : '');
	$database->setQuery($query);
	$total = $database->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$query = "SELECT a.*, c.clientname AS clientname"
		. "\n FROM #__users AS a"
		. "\n LEFT JOIN #__support_client_users AS cu ON a.id = cu.id_user"
		. "\n LEFT JOIN #__support_client AS c ON c.id = cu.id_client";

	if ($filter_logged == 1 || $filter_logged == 2) {
		$query .= "\n INNER JOIN #__session AS s ON s.userid = a.id";
	}

	$query .= (count($where) ? "\n WHERE " . implode(' AND ', $where) : "")
		. "\n GROUP BY a.id";
	$database->setQuery($query, $pageNav->limitstart, $pageNav->limit);
	$rows = $database->loadObjectList();

	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	$template = 'SELECT COUNT(s.userid) FROM #__session AS s WHERE s.userid = %d';
	$n = count($rows);
	for ($i = 0; $i < $n; $i++)
	{
		$row = &$rows[$i];
		$query = sprintf($template, intval($row->id));
		$database->setQuery($query);
		$row->loggedin = $database->loadResult();
	}

	// get list of Log Status for dropdown filter
	$logged[] = JHTML::_('select.option', 0, JText::_('select_log'));
	$logged[] = JHTML::_('select.option', 1, JText::_('loggedin'));
	$lists['logged'] = JHTML::_('select.genericlist', $logged, 'filter_logged', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $filter_logged);

	HTML_users::showUsers($rows, $pageNav, $search, $lists);
}

/**
 * Edit the user
 * @param int The user ID
 * @param string The URL option
 */
function editUser($uid = '0')
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');
	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	$sql = "select * from #__users where id='" . $uid . "'";
	$database->setQuery($sql);
	$row = $database->loadObject();

	// Get Users Custom Fields
	if ($uid > 0)
	{
		// Check if record exists in SC User table
		$sql = "select count(*) from #__support_users where id_user='" . $uid . "'";
		$database->setQuery($sql);
		if ($database->loadResult() == 0)
		{
			$sql = "insert into `#__support_users`(id_user, avatar) values('" . $uid . "', '" . JURI::root() . "media/com_maqmahelpdesk/images/avatars/anonymous.png')";
			$database->setQuery($sql);
			$database->query();
		}

		$sql = "SELECT phone, fax, mobile, address1, address2, zipcode, location, city, country, avatar, id_schedule, vacances
				FROM #__support_users
				WHERE id_user='" . $row->id . "'";
		$database->setQuery($sql);
		$userInfo = null;
		$userInfo = $database->loadObject();

		$sql = "SELECT c.clientname AS clientname
				FROM #__support_client_users AS u, #__support_client AS c
				WHERE c.id = u.id_client AND u.id_user='" . $uid . "' ";
		$database->setQuery($sql);
		$clientname = $database->loadResult();
		if ($clientname)
		{
			$userInfo->clientname = $clientname;
		}
		else
		{
			$userInfo->clientname = JText::_('no_client');
		}
	}
	else
	{
		$userInfo = new stdClass();
		$userInfo->avatar = '';
		$userInfo->phone = '';
		$userInfo->fax = '';
		$userInfo->mobile = '';
		$userInfo->address1 = '';
		$userInfo->address2 = '';
		$userInfo->zipcode = '';
		$userInfo->location = '';
		$userInfo->city = '';
		$userInfo->country = '';
		$userInfo->clientname = '';
		$userInfo->id_schedule = '';
		$userInfo->vacances = '';
	}

	// Get user schedules
	$database->setQuery("SELECT id, profile FROM #__support_schedule ORDER BY id, profile ");
	$schedules_list = $database->loadObjectList();
	$numb_schedules = sizeof($schedules_list);

	if ($numb_schedules > 0)
	{
		$arr = array();
		for ($i = 0; $i < $numb_schedules; $i++) {
			$arr[] = get_object_vars($schedules_list[$i]);
		}
		$schedules[] = JHTML::_('select.option', '0', JText::_('schedule_select'));
		for ($i = 0; $i < $numb_schedules; $i++)
		{
			$schedules[] = JHTML::_('select.option', $arr[$i]['id'], $arr[$i]['profile']);
		}
		$lists['schedules'] = JHTML::_('select.genericlist', $schedules, 'id_schedule', 'class="inputbox" size="1"', 'value', 'text', $userInfo->id_schedule);
	}
	else
	{
		ob_start();
		echo "<input type='hidden' name='id_schedule' value='0' />";
		echo "<span style='color:red;' >" . JText::_('no_schedule_list_alert') . "</span>";
		$sc = ob_get_contents();
		ob_end_clean();
		$lists['schedules'] = $sc;
	}

	$lists['vacances'] = $userInfo->vacances;

	$database->setQuery("SELECT uf.id, uf.id_field, uf.ordering, uf.required, cf.caption, cf.ftype, cf.value, cf.size, cf.maxlength FROM #__support_user_fields uf INNER JOIN #__support_custom_fields cf ON cf.id=uf.id_field WHERE cf.cftype='U' ORDER BY uf.ordering");
	$cfields = $database->loadObjectList();

	HTML_users::edituser($row, $uid, $userInfo, $cfields, $lists);
}

function saveUser($task)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$user = JFactory::getUser();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$sql = "select * from #__users where id='" . JRequest::getVar('id', '', 'POST', 'int') . "'";
	$database->setQuery($sql);
	$row = $database->loadObject();

	// support center normal fields
	$database->setQuery("DELETE FROM #__support_users WHERE id_user='" . $row->id . "'");
	$database->query();

	$database->setQuery("INSERT INTO #__support_users(id_user, phone, fax, mobile, address1, address2, zipcode, location, city, country, avatar) VALUES('" . JRequest::getVar('id', '', 'POST', 'int') . "', '" . JRequest::getVar('phone', '', 'POST', 'string') . "', '" . JRequest::getVar('fax', '', 'POST', 'string') . "', '" . JRequest::getVar('mobile', '', 'POST', 'string') . "', '" . JRequest::getVar('address1', '', 'POST', 'string') . "', '" . JRequest::getVar('address2', '', 'POST', 'string') . "', '" . JRequest::getVar('zipcode', '', 'POST', 'string') . "', '" . JRequest::getVar('location', '', 'POST', 'string') . "', '" . JRequest::getVar('city', '', 'POST', 'string') . "', '" . JRequest::getVar('country', '', 'POST', 'string') . "', '" . JRequest::getVar('avatar', HelpdeskUser::GetAvatar(JRequest::getVar('id', '', 'POST', 'int')), 'POST', 'string') . "')");
	$database->query();
	echo "<p>" . $database->getErrorMsg();

	// support center custom fields
	$database->setQuery("DELETE FROM #__support_user_values WHERE id_user='" . $row->id . "'");
	$database->query();

	$database->setQuery("SELECT uf.id, uf.id_field FROM #__support_user_fields uf INNER JOIN #__support_custom_fields cf ON cf.id=uf.id_field WHERE cf.cftype='U'");
	$cfields = $database->loadObjectList();

	for ($x = 0; $x < count($cfields); $x++) {
		$cfield = $cfields[$x];
		$database->setQuery("INSERT INTO #__support_user_values(id_user, id_field, `value`) VALUES( '" . JRequest::getVar('id', '', 'POST', 'int') . "', '" . $cfield->id_field . "', '" . JRequest::getVar('custom' . $cfield->id_field, '', 'POST', 'string') . "')");

		$database->query();
	}
	switch ($task) {
		case 'apply':
			$msg = JText::_('user_save_changes') . ': ' . $row->name;
			$mainframe->redirect('index.php?option=com_maqmahelpdesk&task=users_edit&id=' . $row->id, $msg);
			break;

		case 'save':
		default:
			$msg = JText::_('user_save') . ': ' . $row->name;
			$mainframe->redirect('index.php?option=com_maqmahelpdesk&task=users', $msg);
			break;
	}
}

function cancelUser()
{
	$mainframe = JFactory::getApplication();
	$mainframe->redirect('index.php?option=com_maqmahelpdesk&task=view');
}
