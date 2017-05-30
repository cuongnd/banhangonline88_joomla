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
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/client.php';
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/client.php';

// Include helpers
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/file.php');

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/client.php';
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/client_contract.php';
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/client_info.php';
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/client_users.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/client/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/client/tmpl/edit.php";

// Set toolbar and page title
HelpdeskClientAdminHelper::addToolbar($task);
HelpdeskClientAdminHelper::setDocument();

// Get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');

if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'client', $task, $cid[0]);

switch ($task)
{
	case "new":
		editClient(0);
		break;

	case "edit":
		editClient($cid[0]);
		break;

	case "save":
		saveClient(0);
		break;

	case "apply":
		saveClient(1);
		break;

	case "delete":
		removeClient($cid);
		break;

	case "saveuser":
		saveUser();
		editClient(JRequest::getVar('id_client', 0, 'POST', 'int'));
		break;

	case "edituser":
		editUser(JRequest::getVar('id', 0, '', 'int'));
		editClient(JRequest::getVar('id_client', 0, 'POST', 'int'));
		break;

	case "deluser":
		deleteUser(JRequest::getVar('id', 0, '', 'int'), JRequest::getVar('id_client', 0, '', 'int'));
		editClient(JRequest::getVar('id_client', 0, '', 'int'));
		break;

	case "manager":
		changeManager(JRequest::getVar('action', 0, '', 'int'), JRequest::getVar('id_user', 0, '', 'int'), JRequest::getVar('id_client', 0, '', 'int'));
		editClient(JRequest::getVar('id_client', 0, '', 'int'));
		break;

	case "savecontract":
		saveContract();
		editClient(JRequest::getVar('id_client', 0, '', 'int'));
		break;

	case "delcontract":
		deleteContract();
		editClient(JRequest::getVar('id_client', 0, '', 'int'));
		break;

	case "publishcontract":
		publishContract();
		editClient(JRequest::getVar('id_client', 0, '', 'int'));
		break;

	case "savefile":
		saveFile();
		editClient(JRequest::getVar('id_client', 0, '', 'int'));
		break;

	case "delfile":
		deleteClientFile();
		editClient(JRequest::getVar('id_client', 0, '', 'int'));
		break;

	case "saveinfo":
		saveInfo();
		editClient(JRequest::getVar('id_client', 0, '', 'int'));
		break;

	case "delinfo":
		deleteInfo();
		editClient(JRequest::getVar('id_client', 0, '', 'int'));
		break;

	case "publishinfo":
		publishInfo();
		editClient(JRequest::getVar('id_client', 0, '', 'int'));
		break;

	case "download":
		HelpdeskFile::Download(JRequest::getVar('id', 0, '', 'int'), 0, 'C');
		break;

	default:
		showClient();
		break;
}

function showClient()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$session = JFactory::getSession();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));
	$filter = JRequest::getVar('filter', $session->get('filter_client', '', 'maqmahelpdesk'), 'POST', 'string');
	$filter = ($filter);
	$session->set('filter_client', $filter, 'maqmahelpdesk');
	HelpdeskUtility::AppendResource('client_manager.js', JURI::root() . 'media/com_maqmahelpdesk/js/administrator/', 'js', true);

	// Get the total number of records
	$sql = "SELECT count(*)
			FROM #__support_client 
			WHERE `clientname` LIKE '%" . $filter . "%'";
	$database->setQuery($sql);
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$sql = "SELECT c.id, c.clientname, c.description, c.country, c.block, c.slug, c.`logo`
			FROM #__support_client c 
			" . ($filter != '' ? "WHERE c.`clientname` LIKE '%" . $filter . "%' " : '') . "
			ORDER BY c.clientname";
	$database->setQuery($sql, $pageNav->limitstart, $pageNav->limit);
	$rows = $database->loadObjectList();

	MaQmaHtmlDefault::display($rows, $pageNav, $filter);
}

function editClient($uid = 0)
{
	$database = JFactory::getDBO();
	$session = JFactory::getSession();
	$document = JFactory::getDocument();

	$document->addScriptDeclaration('var IMQM_NAME_REQUIRED = "' . addslashes(JText::_('name_required')) . '";');
	$document->addScriptDeclaration('var IMQM_SESSION_ID = "' . $session->getId() . '";');
	HelpdeskUtility::AppendResource('autocomplete.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('client_edit.js', JURI::root() . 'media/com_maqmahelpdesk/js/administrator/', 'js', true);

	$row = new MaQmaHelpdeskTableClient($database);
	$row->load($uid);

	// Build Workgroups select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text
			FROM #__support_workgroup
			ORDER BY wkdesc";
	$database->setQuery($sql);
	$wk = $database->loadObjectList();
	$wk = array_merge(array(JHTML::_('select.option', '0', JText::_('all'))), $wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $wk, 'id_workgroup', 'class="inputbox" size="6" multiple="multiple"', 'value', 'text', '');

	// Build Support Staff select list
	$sql = "SELECT DISTINCT(u.`id`) AS value, u.`name` AS text
			FROM #__support_permission p, #__users u
			WHERE p.id_user=u.id
			ORDER BY u.name";
	$database->setQuery($sql);
	$rows_staff = $database->loadObjectList();
	$rows_staff = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_staff);
	$lists['auto_assign'] = JHTML::_('select.genericlist', $rows_staff, 'autoassign', 'class="inputbox" size="1"', 'value', 'text', $row->autoassign);

	// Build Client Workgroups
	$sql = "SELECT id, wkdesc
			FROM #__support_workgroup
			ORDER BY wkdesc ASC";
	$database->setQuery($sql);
	$workgroups = $database->loadObjectList();
	$lists['wks'] = $workgroups;

	// Build Contract Templates select list
	$sql = "SELECT `id` AS value, `name` AS text
			FROM #__support_contract_template
			ORDER BY `name`";
	$database->setQuery($sql);
	$ctempl = $database->loadObjectList();
	$ctempl = array_merge(array(JHTML::_('select.option', '0', JText::_('select3'))), $ctempl);
	$lists['contract'] = JHTML::_('select.genericlist', $ctempl, 'id_contract', 'class="inputbox" size="1"', 'value', 'text', 0);

	// Build Components select list
	$sql = "SELECT `id` AS value, `name` AS text FROM #__support_components ORDER BY `name`";
	$database->setQuery($sql);
	$comptempl = $database->loadObjectList();
	$lists['components'] = JHTML::_('select.genericlist', $comptempl, 'id_component', 'class="inputbox" size="6" multiple="multiple" onchange="PrepareComponents();"', 'value', 'text', '');

	// Get the Client Users list
	$database->setQuery("SELECT u.username, u.name, su.phone, su.fax, su.mobile, u.email, c.manager, u.id, c.id_client FROM #__users u INNER JOIN #__support_client_users c ON u.id=c.id_user LEFT JOIN #__support_users su ON su.id_user = u.id WHERE c.id_client=" . (int)$row->id . " ORDER BY u.name");
	$users = $database->loadObjectList();

	// Get the Client Docs list
	$database->setQuery("SELECT d.* FROM #__support_client_docs d, #__support_client c WHERE d.id_client=c.id AND c.id=" . (int)$row->id);
	$clientdocs = $database->loadObjectList();

	// Get the Client Contracts list
	$database->setQuery("SELECT c.*, u.name, t.name as contracttmpl, u.username FROM #__support_contract_template t, #__support_client cl, #__support_contract c LEFT JOIN #__users u ON c.id_user=u.id WHERE cl.id=c.id_client AND cl.id='" . (int)$row->id . "' AND t.id=c.id_contract ORDER BY c.date_start DESC");
	$contracts = $database->loadObjectList();

	// Get the Client Downloads list
	$database->setQuery("SELECT c.cname, d.pname, a.isactive, a.serialno, a.servicefrom, a.serviceuntil, a.id FROM #__support_dl d, #__support_dl_category AS c, #__support_dl_access a WHERE d.id=a.id_download AND d.id_category=c.id AND a.id_user='" . (int)$row->id . "' ORDER BY c.cname, d.pname");
	$downloads = $database->loadObjectList();

	// Get the Client Tickets list
	$database->setQuery("SELECT t.id, t.subject, w.wkdesc, t.date, s.description as status, t.an_name, t.assign_to, t.last_update, t.ticketmask, t.source, t.id_workgroup FROM #__support_ticket t, #__support_workgroup w, #__support_status s WHERE t.id_client='" . (int)$row->id . "' AND t.id_client<>0 AND t.id_status=s.id AND t.id_workgroup=w.id ORDER BY t.date DESC");
	$tickets = $database->loadObjectList();

	// Get the Client Information Records
	$database->setQuery("SELECT * FROM #__support_client_info WHERE id_client='" . (int)$row->id . "' ORDER BY date ASC");
	$inforecords = $database->loadObjectList();

	// Build Support Staff select list
	$sql = "SELECT DISTINCT(u.`id`) AS value, u.`name` AS text FROM #__support_permission p, #__users u WHERE p.id_user=u.id ORDER BY u.name";
	$database->setQuery($sql);
	$rows_staff = $database->loadObjectList();
	$rows_staff = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_staff);
	$lists['staff'] = JHTML::_('select.genericlist', $rows_staff, 'id_user', 'class="inputbox" size="1"', 'value', 'text', 0);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists['manager'] = HelpdeskForm::SwitchCheckbox('radio', 'manager', $captions, $values, 0, 'switch');
	$lists['manager2'] = HelpdeskForm::SwitchCheckbox('radio', 'manager', $captions, $values, $row->manager, 'switch');
	$lists['block'] = HelpdeskForm::SwitchCheckbox('radio', 'block', $captions, $values, $row->block, 'switch');
	$lists['approval'] = HelpdeskForm::SwitchCheckbox('radio', 'approval', $captions, $values, $row->approval, 'switch');
	$lists['available'] = HelpdeskForm::SwitchCheckbox('radio', 'available', $captions, $values, 1, 'switch');

	// Get contract custom fields
	$sql = "SELECT kf.id, kf.id_field, kf.ordering, kf.required, cf.caption, cf.ftype, cf.value, cf.size, cf.maxlength
			FROM #__support_contract_fields kf
				 INNER JOIN #__support_custom_fields cf ON cf.id=kf.id_field
			WHERE cf.cftype='C'
			ORDER BY kf.ordering";
	$database->setQuery($sql);
	$cfields = $database->loadObjectList();

	// Get clients custom fields
	$sql = "SELECT cf.id, cf.id AS id_field, '0' AS ordering, '0' AS required, cf.caption, cf.ftype, cf.value, cf.size, cf.maxlength
			FROM #__support_custom_fields cf
			WHERE cf.cftype='L'
			ORDER BY cf.caption";
	$database->setQuery($sql);
	$lists['cfields'] = $database->loadObjectList();

	MaQmaHtmlEdit::display($row, $users, $clientdocs, $lists, $contracts, $tickets, $inforecords, $downloads, $cfields);
}

function saveClient($apply)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableClient($database);
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!$row->bind($_POST)) {
		echo "<script type='text/javascript'> alert('" . $row->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->check()) {
		echo "<script type='text/javascript'> alert('" . $row->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script type='text/javascript'> alert('" . $row->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->checkin();

	$database->setQuery("DELETE FROM #__support_client_wk WHERE id_client='" . $row->id . "'");
	$database->query();

	$wks = JRequest::getVar('depaccess', '', '', 'array');
	for ($i = 0; $i < count($wks); $i++)
	{
		$wk = $wks[$i];
		$app_announcements = JRequest::getVar('app_announcements_' . $wk, '', '', 'string');
		$app_bugtracker = JRequest::getVar('app_bugtracker_' . $wk, '', '', 'string');
		$app_discussions = JRequest::getVar('app_discussions_' . $wk, '', '', 'string');
		$app_glossary = JRequest::getVar('app_glossary_' . $wk, '', '', 'string');
		$app_trouble = JRequest::getVar('app_trouble_' . $wk, '', '', 'string');
		$app_downloads = JRequest::getVar('app_downloads_' . $wk, '', '', 'string');
		$app_kb = JRequest::getVar('app_kb_' . $wk, '', '', 'string');
		$app_faq = JRequest::getVar('app_faq_' . $wk, '', '', 'string');
		$app_ticket = JRequest::getVar('app_ticket_' . $wk, '', '', 'string');

		if (is_numeric($app_announcements))
		{
			$sql = "INSERT INTO #__support_client_wk(id_workgroup, id_client, app_announcements, app_bugtracker, app_discussions, app_glossary, app_trouble, app_downloads, app_kb, app_faq, app_ticket)
					VALUES(" . (int) $wks[$i] . ", " . (int) $row->id . ", " . (int) $app_announcements . ", " . (int) $app_bugtracker . ", " . (int) $app_discussions . ", " . (int) $app_glossary . ", " . (int) $app_trouble . ", " . (int) $app_downloads . ", " . (int) $app_kb . ", " . (int) $app_faq . ", " . (int) $app_ticket . ")";
			$database->setQuery($sql);
			$database->query();
		}
	}

	// Saves the logo
	if ($_FILES['logo']['name'] != '')
	{
		// Check if the folder exists, if not creates it
		if (!is_dir(JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/'))
		{
			mkdir(JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/');
		}
		$msg = HelpdeskFile::Upload($row->id, 'CLI', "logo", JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/', '', 0, 0, 'image');
	}

	// Delete Custom fields
	$sql = "DELETE FROM #__support_client_field_value
			WHERE id_client=" . $row->id;
	$database->setQuery($sql);
	$database->query();

	// Get custom fields
	$sql = "SELECT c.id
			FROM #__support_custom_fields AS c
			WHERE c.`cftype`='L'
			ORDER BY c.caption";
	$database->setQuery($sql);
	$customfields = $database->loadObjectList();

	// Insert values
	for ($x = 0; $x < count($customfields); $x++)
	{
		$cField = $customfields[$x];
		$custom_val = stripslashes(JRequest::getVar('custom' . $cField->id, '', '', 'string'));
		$sql = "INSERT INTO #__support_client_field_value(id_field, id_client, value)
				VALUES(" . (int) $cField->id . ", " . (int) $row->id . ", " . $database->quote($custom_val) . ")";
		$database->setQuery($sql);
		$database->query();
	}

	if ($apply)
	{
		$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=client_edit&cid[0]=" . $row->id);
	}
	else
	{
		$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=client");
	}
}

function removeClient($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1)
	{
		echo "<script type='text/javascript'> alert('" . JText::_('client_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid))
	{
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_client WHERE id IN (" . $cids . ")");
		if (!$database->query())
		{
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		//Delete User associations with this client
		$database->setQuery("DELETE FROM #__support_client_users WHERE id_client IN (" . $cids . ")");
		if (!$database->query())
		{
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		//Delete Workgroup Permissions for this client
		$database->setQuery("DELETE FROM #__support_client_wk WHERE id_client IN (" . $cids . ")");
		if (!$database->query())
		{
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		//Delete Contracts for this client
		$database->setQuery("DELETE FROM #__support_contract WHERE id_client IN (" . $cids . ")");
		if (!$database->query())
		{
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		//Delete Announcements for this client
		$database->setQuery("DELETE FROM #__support_announce WHERE id_client IN (" . $cids . ")");
		if (!$database->query())
		{
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		//Delete Documents for this client
		$database->setQuery("DELETE FROM #__support_client_docs WHERE id_client IN (" . $cids . ")");
		if (!$database->query())
		{
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		//Delete Notes for this client
		$database->setQuery("DELETE FROM #__support_client_info WHERE id_client IN (" . $cids . ")");
		if (!$database->query())
		{
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=client");
}

function saveUser()
{
	$database = JFactory::getDBO();
	$id_user = JRequest::getInt('id_user', 0);
	$id_client = JRequest::getInt('id_client', 0);
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if ($id_user)
	{
		$row = new MaQmaHelpdeskTableClientUser($database);

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

		// Update tickets from the user to belong to client
		// Mark the user tickets as approved
		$sql = "UPDATE `#__support_ticket`
				SET `id_client`=" . $id_client . ",
				    `approved`=1
				WHERE `id_user`=" . $id_user;
		$database->setQuery($sql);
		$database->query();
	}
}

function editUser($id)
{
	$database = JFactory::getDBO();

	$phone = JRequest::getVar('phone', '', 'POST', 'string');
	$mobile = JRequest::getVar('mobile', '', 'POST', 'string');
	$fax = JRequest::getVar('fax', '', 'POST', 'string');
	$manager = JRequest::getVar('manager', '', 'POST', 'int');

	$sql = "UPDATE #__support_client_users
			SET phone=" . $database->quote($phone) . ",
				mobile=" . $database->quote($mobile) . ",
				fax=" . $database->quote($fax) . ",
				manager=" . $database->quote($manager) . "
			WHERE id='" . $id . "'";
	$database->setQuery($sql);
	$database->query();
}

function changeManager($action, $id_user, $id_client)
{
	$database = JFactory::getDBO();

	$sql = "UPDATE #__support_client_users
			SET manager=" . (int) $action . "
			WHERE id_client=" . (int) $id_client . " AND id_user=" . (int) $id_user;
	$database->setQuery($sql);
	$database->query();
}

function deleteUser($id_user, $id_client)
{
	$database = JFactory::getDBO();

	// Remove user from client
	$sql = "DELETE FROM #__support_client_users
			WHERE id_user='" . $id_user . "' AND id_client='" . $id_client . "'";
	$database->setQuery($sql);
	$database->query();

	// Update tickets from the user
	$sql = "UPDATE `#__support_ticket`
			SET `id_client`=0
			WHERE `id_user`=" . $id_user;
	$database->setQuery($sql);
	$database->query();
}

function saveContract()
{
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();
	$row = new MaQmaHelpdeskTableClientContract($database);

	if (!$row->bind($_POST)) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->check()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->checkin();

	// Gets info from the contract template
	$ctempl = null;
	$database->setQuery("SELECT unit, val FROM #__support_contract_template WHERE id='" . $row->id_contract . "'");
	$ctempl = $database->loadObject();

	// Updates the client contract
	$database->setQuery("UPDATE #__support_contract SET unit='" . $ctempl->unit . "', value='" . $ctempl->val . "', creation_date='" . HelpdeskDate::DateOffset("%Y-%m-%d") . "'" . ($row->contract_number == '' ? ", contract_number='" . $row->id . "'" : "") . " WHERE id='" . $row->id . "'");
	$database->query();

	$database->setQuery("DELETE FROM #__support_contract_comp WHERE id_contract='" . $row->id . "'");
	$database->query();

	$wks = JRequest::getVar("id_component", array(), "", "array");
	foreach($wks as $wk)
	{
		$sql = "INSERT INTO #__support_contract_comp(id_component, id_contract)
				VALUES('" . $wk . "', '" . $row->id . "')";
		$database->setQuery($sql);
		$database->query();
	}

	//////////////////////////////////////
	//	Update Contract Custom Fields	//
	//////////////////////////////////////
	$database->setQuery("DELETE FROM #__support_contract_fields_values WHERE id_contract='" . $row->id . "'");
	$database->query();

	$database->setQuery("SELECT f.id_field, c.caption FROM #__support_contract_fields AS f INNER JOIN #__support_custom_fields AS c ON c.id=f.id_field ORDER BY f.ordering");

	$customfields = $database->loadObjectList();
	if (!$database->query()) {
		HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
	}

	$x = 0;
	$cfields_array = array();
	for ($x = 0; $x < count($customfields); $x++) {
		$contractField = $customfields[$x];
		$custom_val = stripslashes(JRequest::getVar('custom' . $contractField->id_field, '', '', 'string'));
		$cfields_array_tmp = array('%cfield' . $contractField->id_field . '_caption%' => $contractField->caption,
			'%cfield' . $contractField->id_field . '_value%' => HelpdeskUtility::String2HTML($custom_val));
		$cfields_array = array_merge($cfields_array, $cfields_array_tmp);
		$database->setQuery("INSERT INTO #__support_contract_fields_values(id_field, id_contract, value) VALUES('" . $contractField->id_field . "', '" . $row->id . "', " . $database->quote($custom_val) . ")");
		if (!$database->query()) {
			HelpdeskUtility::AddGlobalMessage(JText::_('tkt_dberror') . '<br />' . $database->getErrorMsg(), 'e', $database->stderr(1));
		}
	}
}

function deleteContract()
{
	$database = JFactory::getDBO();

	$database->setQuery("DELETE FROM #__support_contract WHERE id='" . JRequest::getVar('id', 0, '', 'int') . "'");
	$database->query();

	$database->setQuery("DELETE FROM #__support_contract_fields_values WHERE id_contract='" . JRequest::getVar('id', 0, '', 'int') . "'");
	$database->query();

}

function publishContract()
{
	$database = JFactory::getDBO();

	$action = JRequest::getVar('action', '', 'GET', 'string');

	$database->setQuery("UPDATE #__support_contract SET `status`='" . ($action == 'I' ? 'A' : 'I') . "' WHERE id='" . JRequest::getVar('id', 0, '', 'int') . "'");
	$database->query();
}

function saveFile()
{
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();

	$description = JRequest::getVar('description', '', 'POST', 'string');
	HelpdeskFile::Upload(JRequest::getVar('id_client', 0, 'POST', 'int'), 'C', "file", $supportConfig->docspath . 'docs/', $description);
}

function deleteClientFile()
{
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();
	unlink($supportConfig->docspath . 'docs/' . JRequest::getVar('filename', '', '', 'string'));
	$database->setQuery("DELETE FROM #__support_client_docs WHERE id='" . JRequest::getVar('id', 0, '', 'int') . "'");
	$database->query();
}

function saveInfo()
{
	$database = JFactory::getDBO();

	$row = new MaQmaHelpdeskTableClientInfo($database);

	if (!$row->bind($_POST)) {
		echo "<script type='text/javascript'> alert('" . $row->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->check()) {
		echo "<script type='text/javascript'> alert('" . $row->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}
	if (!$row->store()) {
		echo "<script type='text/javascript'> alert('" . $row->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$row->checkin();
}

function deleteInfo()
{
	$database = JFactory::getDBO();

	$database->setQuery("DELETE FROM #__support_client_info WHERE id='" . JRequest::getVar('id', 0, '', 'int') . "'");
	$database->query();
}

function publishInfo()
{
	$database = JFactory::getDBO();

	$database->setQuery("UPDATE #__support_client_info SET published='" . JRequest::getVar('action', 0, '', 'int') . "' WHERE id='" . JRequest::getVar('id', 0, '', 'int') . "'");
	$database->query();
}
