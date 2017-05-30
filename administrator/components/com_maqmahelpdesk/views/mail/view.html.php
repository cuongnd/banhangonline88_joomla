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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/mail.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/mail_fetch.php';
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/mail_ignore.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/mail/tmpl/default.php";

// Set toolbar and page title
HelpdeskMailAdminHelper::addToolbar($task);
HelpdeskMailAdminHelper::setDocument($task);

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'mail', $task, $cid[0]);

switch ($task) {
	case "categories":
		getCategories();
		break;
	case "newignore":
		editMailIgnore(0);
		break;
	case "editignore":
		editMailIgnore($cid[0]);
		break;
	case "saveignore":
		saveMailIgnore();
		break;
	case "removeignore":
		removeMailIgnore($cid);
		break;
	case "publishignore":
		publishMailIgnore($cid, 1);
		break;
	case "unpublishignore":
		publishMailIgnore($cid, 0);
		break;
	case "mailignore":
		showMailIgnore();
		break;
	case "new":
		editMail(0);
		break;
	case "edit":
		editMail($cid[0]);
		break;
	case "save":
		saveMail();
		break;
	case "remove":
		removeMail($cid);
		break;
	case "publish":
		publishMail($cid, 1);
		break;
	case "unpublish":
		publishMail($cid, 0);
		break;
	case "copy":
		copyMail($cid);
		break;
	default:
		showMail();
		break;
}

function copyMail($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();

	$sql = "INSERT INTO `#__support_mail_fetch`(`id_workgroup`, `email`, `server`, `port`, `username`, `password`, `type`, `remove`, `extra_info`, `queue`, `id_status`, `id_category`, `label`, `notls`, `thrash`, `ssl`, `published`)
			SELECT `id_workgroup`, `email`, `server`, `port`, `username`, `password`, `type`, `remove`, `extra_info`, `queue`, `id_status`, `id_category`, `label`, `notls`, `thrash`, `ssl`, `published`
			FROM `#__support_mail_fetch`
			WHERE `id` IN (" . implode(',', $cid) . ")";
	$database->setQuery($sql);
	$database->query();

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=mail", JText::_('RECORDS_DUPLICATED'));
}

function getCategories($id_workgroup = 0, $id_category = 0, $echo = true)
{
	$database = JFactory::getDBO();

	$id_workgroup = JRequest::getInt('id_workgroup', $id_workgroup);
	$id_category = JRequest::getInt('id_category', $id_category);

	// Build Workgroups select list
	$sql = "SELECT `id` AS value, `name` AS text
			FROM `#__support_category`
			WHERE `id_workgroup`=" . (int) $id_workgroup . " AND `show`=1 AND `tickets`=1
			ORDER BY `name`";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();
	$rows = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows);
	$list = JHTML::_('select.genericlist', $rows, 'id_category', 'class="inputbox" size="1"', 'value', 'text', $id_category);

	if ($echo)
	{
		echo $list;
	}
	else
	{
		return $list;
	}
}

function showMail()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_mail_fetch");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$database->setQuery("SELECT m.id, w.wkdesc, m.server, m.username, m.email, m.published"
			. "\nFROM #__support_mail_fetch m, #__support_workgroup w"
			. "\nWHERE m.id_workgroup=w.id"
			. "\nORDER BY w.wkdesc",
		$pageNav->limitstart, $pageNav->limit
	);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	mail_html::show($rows, $pageNav);
}

function editMail($uid = 0)
{
	$database = JFactory::getDBO();
	$row = new MaQmaHelpdeskTableMailFetch($database);
	$row->load($uid);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');
	$lists['remove'] = HelpdeskForm::SwitchCheckbox('radio', 'remove', $captions, $values, $row->remove, 'switch');
	$lists['queue'] = HelpdeskForm::SwitchCheckbox('radio', 'queue', $captions, $values, $row->queue, 'switch');
	$lists['notls'] = HelpdeskForm::SwitchCheckbox('radio', 'notls', $captions, $values, $row->notls, 'switch');
	$lists['ssl'] = HelpdeskForm::SwitchCheckbox('radio', 'ssl', $captions, $values, $row->ssl, 'switch');
	$lists['published'] = HelpdeskForm::SwitchCheckbox('radio', 'published', $captions, $values, $row->published, 'switch');

	// Build Server Type select list
	$ftypelist[] = JHTML::_('select.option', '0', JText::_('selectlist'));
	$ftypelist[] = JHTML::_('select.option', 'pop', 'POP');
	$ftypelist[] = JHTML::_('select.option', 'pop3', 'POP3');
	$ftypelist[] = JHTML::_('select.option', 'imap', 'IMAP');
	$lists['type'] = JHTML::_('select.genericlist', $ftypelist, 'type', 'class="inputbox" size="1"', 'value', 'text', $row->type);

	// Build Workgroups select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup ORDER BY wkdesc";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();
	$rows = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows, 'id_workgroup', 'class="inputbox" size="1" onchange="GetCategories();"', 'value', 'text', $row->id_workgroup);

	// Build Status select list
	$sql = "SELECT `id` AS value, `description` AS text FROM #__support_status WHERE status_group='O' AND `show`=1 ORDER BY description";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();
	$rows = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows);
	$lists['status'] = JHTML::_('select.genericlist', $rows, 'id_status', 'class="inputbox" size="1"', 'value', 'text', $row->id_status);

	// Build Category select list
	$lists['category'] = getCategories($row->id_workgroup, $row->id_category, false);

	mail_html::edit($row, $lists);
}

function saveMail()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableMailFetch($database);
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

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=mail");
}

function removeMail($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('email_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_mail_fetch WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=mail");
}

function showMailIgnore()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_mail_fetch_ignore");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$sql = "SELECT `id`, `field`, `operator`, `published`, `value`
			FROM `#__support_mail_fetch_ignore`
			ORDER BY `field`";
	$database->setQuery($sql, $pageNav->limitstart, $pageNav->limit);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	mailignore_html::show($rows, $pageNav);
}

function editMailIgnore($uid = 0)
{
	$database = JFactory::getDBO();
	$row = new MaQmaHelpdeskTableMailIgnore($database);
	$row->load($uid);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');
	$lists['published'] = HelpdeskForm::SwitchCheckbox('radio', 'published', $captions, $values, $row->published, 'switch');

	// Build Field select list
	//$ftypelist[] = JHTML::_('select.option', '0', JText::_('selectlist'));
	$fieldlist[] = JHTML::_('select.option', 'subject', JText::_('subject'));
	$lists['field'] = JHTML::_('select.genericlist', $fieldlist, 'field', 'class="inputbox" size="1"', 'value', 'text', $row->field);

	// Build Operator select list
	//$ftypelist[] = JHTML::_('select.option', '0', JText::_('selectlist'));
	$operatorlist[] = JHTML::_('select.option', '=', JText::_('equal_to'));
	$lists['operator'] = JHTML::_('select.genericlist', $operatorlist, 'operator', 'class="inputbox" size="1"', 'value', 'text', $row->operator);

	mailignore_html::edit($row, $lists);
}

function saveMailIgnore()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableMailIgnore($database);
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

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=mail_mailignore");
}

function removeMailIgnore($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('email_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_mail_fetch_ignore WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=mail_mailignore");
}

function publishMail($cid = null, $publish = 1)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!is_array($cid) || count($cid) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script type='text/javascript'> alert('" . JText::_('wk_select_action') . " $action'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode(',', $cid);
	$database->setQuery("UPDATE #__support_mail_fetch SET `published`='$publish' WHERE id IN (" . $cids . ")");

	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=mail");
}
