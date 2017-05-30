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

// Include helpers
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/announcements.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/form.php';

// Include output
require_once "components/com_maqmahelpdesk/views/announce/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/announce/tmpl/edit.php";
require_once "components/com_maqmahelpdesk/views/announce/tmpl/send.php";

// Include model
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/models/announcements.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/announcements.php';

// Set toolbar and page title
HelpdeskAnnouncementsAdminHelper::addToolbar($task);
HelpdeskAnnouncementsAdminHelper::setDocument();

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');

if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'announce', $task, $cid[0]);

switch ($task) {
	case "new":
		editAnnounce(0);
		break;

	case "edit":
		editAnnounce($cid[0]);
		break;

	case "save":
		saveAnnounce();
		break;

	case "remove":
		removeAnnounce($cid);
		break;

	case "publish":
		publishAnnounce($cid, 0);
		break;

	case "unpublish":
		publishAnnounce($cid, 1);
		break;

	case "preparesend":
		prepareSend();
		break;

	case "send":
		executeSend();
		break;

	default:
		showAnnounce();
		break;
}

function showAnnounce()
{
	$database = JFactory::getDBO();
	$CONFIG = new JConfig();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));
	HelpdeskUtility::AppendResource('announcements_manager.js', JURI::root() . 'media/com_maqmahelpdesk/js/administrator/', 'js', true);

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_announce");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$database->setQuery("SELECT *"
			. "\nFROM #__support_announce"
			. "\nORDER BY date DESC",
		$pageNav->limitstart, $pageNav->limit
	);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	MaQmaHtmlDefault::display($rows, $pageNav);
}

function editAnnounce($uid = 0)
{
	$supportConfig = HelpdeskUtility::GetConfig();
	$database = JFactory::getDBO();
	$editor = JFactory::getEditor();
	$document = JFactory::getDocument();
	$document->addScriptDeclaration('var IMQM_TITLE_REQUIRED = "' . addslashes(JText::_('TITLE_REQUIRED')) . '";');
	$document->addScriptDeclaration('function CheckHTMLEditor() { ' . ($supportConfig->editor == 'builtin' ? '' : $editor->save('bodytext')) . ' return true; }');
	HelpdeskUtility::AppendResource('announcements_edit.js', JURI::root() . 'media/com_maqmahelpdesk/js/administrator/', 'js', true);

	$row = new MaQmaHelpdeskTableAnnouncements($database);
	$row->load($uid);
	$row->bodytext = str_replace('\"', '"', $row->bodytext);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists['frontpage'] = HelpdeskForm::SwitchCheckbox('radio', 'frontpage', $captions, $values, $row->frontpage, 'switch');
	$lists['urgent'] = HelpdeskForm::SwitchCheckbox('radio', 'urgent', $captions, $values, $row->urgent, 'switch');

	// Build Workgroup select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup ORDER BY wkdesc";
	$database->setQuery($sql);
	$rows_staff = $database->loadObjectList();
	$rows_staff = array_merge(array(JHTML::_('select.option', '0', JText::_('all_workgroups'))), $rows_staff);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_staff, 'id_workgroup', 'class="inputbox" size="1"', 'value', 'text', $row->id_workgroup);

	// Build Client select list
	$sql = "SELECT `id` AS value, `clientname` AS text FROM #__support_client ORDER BY clientname";
	$database->setQuery($sql);
	$rows_staff = $database->loadObjectList();
	$rows_staff = array_merge(array(JHTML::_('select.option', '0', JText::_('all_clients'))), $rows_staff);
	$lists['client'] = JHTML::_('select.genericlist', $rows_staff, 'id_client', 'class="inputbox" size="1"', 'value', 'text', $row->id_client);

	MaQmaHtmlEdit::display($row, $lists);
}

function saveAnnounce()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableAnnouncements($database);
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

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=announce");
}

function removeAnnounce($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('announce_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_announce WHERE id IN (" . $cids . ")");
		$database->query();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=announce");
}

function publishAnnounce($cid = null, $publish = 1)
{
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!is_array($cid) || count($cid) < 1)
	{
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script> alert('" . JText::_('announce_action') . " " . $action . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (!MaQmaModelAnnouncements::state($cid, $publish))
	{
		echo "<script type='text/javascript'> window.history.go(-1); </script>\n";
		exit();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=announce");
}

function prepareSend()
{
	$database = JFactory::getDBO();
	HelpdeskUtility::AppendResource('announcements_send.js', JURI::root() . 'media/com_maqmahelpdesk/js/administrator/', 'js', true);

	// get the total number of records
	$database->setQuery("SELECT MAX(date) AS maxdate, MIN(date) AS mindate, COUNT(*) AS total"
			. "\nFROM #__support_announce"
			. "\nWHERE sent='0'"
			. "\nORDER BY date DESC"
	);

	$announces = null;
	$announces = $database->loadObject();

	$database->setQuery("SELECT COUNT(*) FROM #__users");
	$n_users = $database->loadResult();

	MaQmaHtmlSend::display($announces, $n_users);
}

function executeSend()
{
	$database = JFactory::getDBO();
	$CONFIG = new JConfig();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	// Get the announcements (available to all users)
	$sql = "SELECT id, `date`, introtext, bodytext, frontpage, urgent
			FROM #__support_announce
			WHERE sent='0' AND id_workgroup='0' AND id_client='0'
			ORDER BY date DESC";
	$database->setQuery($sql);
	$announces = $database->loadObjectList();

	$database->setQuery("SELECT id, email FROM #__users");
	$users = $database->loadObjectList();

	if (!get_cfg_var('safe_mode')) {
		$time_limit = ini_get('max_execution_time');
		set_time_limit(0);
	}

	// Send email
	$dd = 0;
	foreach ($users as $row)
	{
		$dd++;
		echo "<hr />" . $dd;

		// Check if user belong to a client
		$database->setQuery("SELECT id_client FROM #__support_client_users WHERE id_user='" . $row->id . "'");
		$id_client = $database->loadResult();
		echo "<p>id_client: " . $id_client;

		if ($id_client > 0) {
			// Check the workgroup permissions for the client
			$database->setQuery("SELECT id_workgroup FROM #__support_client_wk WHERE id_client='" . $id_client . "'");
			$wks = $database->loadObjectList();

			if (count($wks) > 0) {
				$all_wks = 0;
				$wks_where = "AND (id_workgroup='0' OR id_workgroup IN (";
				for ($xxx = 0; $xxx < count($wks); $xxx++) {
					$all_wks = $wks[$xxx]->id_workgroup == 0 ? 1 : 0;
					$wks_where .= $wks[$xxx]->id_workgroup . ',';
				}
				$wks_where = $all_wks ? '' : JString::substr($wks_where, 0, strlen($wks_where) - 1) . '))';
			} else {
				$wks_where = '';
			}

			$database->setQuery("SELECT id, `date`, introtext, bodytext, frontpage, urgent"
					. "\nFROM #__support_announce"
					. "\nWHERE sent='0' " . $wks_where . " AND (id_client='0' OR id_client='" . $id_client . "')"
					. "\nORDER BY date DESC"
			);
			$announces2 = $database->loadObjectList();
		} else {
			$announces2 = array();
		}

		if (count($announces) || count($announces2))
		{
			$htmlBody = buildAnnouncesTable($announces, $announces2);
			echo "<p>" . $CONFIG->mailfrom . "<p>" . $CONFIG->fromname . "<p>" . $row->email . "<p>" . $CONFIG->sitename . "<p>" . htmlentities($htmlBody);
			unset($mailer);
			$mailer = JFactory::getMailer();
			$mailer->addRecipient($row->email);
			$mailer->setSender($CONFIG->mailfrom, $CONFIG->fromname);
			$mailer->setSubject($CONFIG->sitename);
			$mailer->setBody($htmlBody);
			$mailer->IsHTML(true);
			$mailer->Send();
			echo "<p>Announcement sent: " . $row->email;
		}
	}

	if (!get_cfg_var('safe_mode'))
	{
		set_time_limit($time_limit);
	}


	// Updates the announcements
	$database->setQuery("UPDATE #__support_announce SET sent='1'");
	$database->query();

	$msg = str_replace('%1', count($users), JText::_('announce_sent'));
	echo "<p>" . $msg;

	$mainframe->redirect('index.php?option=com_maqmahelpdesk&task=announce', $msg);
}

function buildAnnouncesTable($announces, $announces2)
{
	$rows = array();

	for ($xxx = 0; $xxx < count($announces); $xxx++)
	{
		$row = $announces[$xxx];
		$rows [$row->id] = array(
			'date' => $row->date,
			'introtext' => $row->introtext,
			'bodytext' => $row->bodytext,
			'urgent' => $row->urgent,
		);
	}

	if (is_array($announces2))
	{
		for ($xxx = 0; $xxx < count($announces2); $xxx++)
		{
			$row = $announces2[$xxx];
			$rows [$row->id] = array(
				'date' => $row->date,
				'introtext' => $row->introtext,
				'bodytext' => $row->bodytext,
				'urgent' => $row->urgent,
			);
		}
	}

	$rows = sort_by($rows, 'date', 'desc');

	$htmlBody = '';
	$htmlBody.= '<table width="100%">';
	foreach ($rows as $row)
	{
		$htmlBody.= '<tr><td>' . JText::_('date') . ': ' . $row['date'] . '</td></tr>';
		$htmlBody.= '<tr><td>' . JText::_('title') . ':</td></tr>';
		$htmlBody.= '<tr><td>' . $row['introtext'] . '</td></tr>';
		$htmlBody.= '<tr><td>' . JText::_('body') . ':</td></tr>';
		$htmlBody.= '<tr><td>' . $row['bodytext'] . '</td></tr>';
		if ($row['urgent'])
		{
			$htmlBody .= '<tr><td align="center"><b><u>' . JText::_('announce_urgent') . '</u></b></td></tr>';
		}
		$htmlBody.= '<tr><td><-></td></tr>';
	}
	$htmlBody.= '</table>';

	return $htmlBody;
}

function sort_by($array, $keyname = null, $sortby)
{
	$myarray = $inarray = array();

	foreach ($array as $i => $befree)
	{
		$myarray[$i] = $array[$i][$keyname];
	}

	switch ($sortby)
	{
		case 'asc':
			asort($myarray);
			break;
		case 'arsort':
			arsort($myarray);
			break;
		case 'natcasesor':
			natcasesort($myarray);
			break;
	}

	foreach ($myarray as $key => $befree)
	{
		$inarray[$key] = $array[$key];
	}

	return $inarray;
}
