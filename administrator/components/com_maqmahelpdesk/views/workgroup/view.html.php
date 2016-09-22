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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/departments.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/file.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/department.php';

// HTML dependency
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/views/workgroup/tmpl/default.php';
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/views/workgroup/tmpl/edit.php';

// Include Joomla! models
require_once JPATH_ADMINISTRATOR . '/components/com_users/models/groups.php';

// Set toolbar and page title
HelpdeskDepartmentsAdminHelper::addToolbar($task);
HelpdeskDepartmentsAdminHelper::setDocument();

// Get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid))
{
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'workgroup', $task, $cid[0]);

switch ($task) {
	case "new":
		editWK(0);
		break;

	case "edit":
		editWK($cid[0]);
		break;

	case "apply":
		saveWK(1);
		break;

	case "save":
		saveWK(0);
		break;

	case "remove":
		removeWK($cid);
		break;

	case "publish":
		publishWK($cid, 1);
		break;

	case "unpublish":
		publishWK($cid, 0);
		break;

	case 'saveorder':
		saveOrder();
		break;

	case "ajax":
		configInclude();
		break;

	case "copy":
		copyWK($cid);
		break;

	default:
		showWK();
		break;
}

function copyWK($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();

	$sql = "INSERT INTO `#__support_workgroup`(`wkdesc`, `logo`, `wkabout`, `wkkb`, `wkemail`, `wkticket`, `show`, `wkmail_address`, `wkmail_address_name`, `wkadmin_email`, `auto_assign`, `trouble`, `contract`, `ordering`, `use_activity`, `anonymous_access`, `hyper_links`, `lim_actwords`, `lim_actwords_chars`, `lim_actmsgs`, `lim_actmsgs_chars`, `lim_actmsgs_lines`, `theme`, `tkt_crt_nfy_mgr`, `tkt_crt_nfy_admin`, `tkt_asgn_old_asgn`, `tkt_asgn_new_asgn`, `tkt_asgn_nfy_usr_one`, `wkfaq`, `wkdownloads`, `wkannounces`, `wkglossary`, `enable_discussions`, `contract_total_disable`, `id_priority`, `use_account`, `use_bookmarks`, `add_mail_tag`, `tkt_nfy_agent`, `digistore`, `shortdesc`, `slug`, `bugtracker`, `id_group`, `support_only`)
			SELECT CONCAT(`wkdesc`, ' - " . JText::_('copy') . "'), `logo`, `wkabout`, `wkkb`, `wkemail`, `wkticket`, `show`, `wkmail_address`, `wkmail_address_name`, `wkadmin_email`, `auto_assign`, `trouble`, `contract`, `ordering`, `use_activity`, `anonymous_access`, `hyper_links`, `lim_actwords`, `lim_actwords_chars`, `lim_actmsgs`, `lim_actmsgs_chars`, `lim_actmsgs_lines`, `theme`, `tkt_crt_nfy_mgr`, `tkt_crt_nfy_admin`, `tkt_asgn_old_asgn`, `tkt_asgn_new_asgn`, `tkt_asgn_nfy_usr_one`, `wkfaq`, `wkdownloads`, `wkannounces`, `wkglossary`, `enable_discussions`, `contract_total_disable`, `id_priority`, `use_account`, `use_bookmarks`, `add_mail_tag`, `tkt_nfy_agent`, `digistore`, `shortdesc`, CONCAT(`slug`, '-" . JText::_('copy') . "'), `bugtracker`, `id_group`, `support_only`
			FROM `#__support_workgroup`
			WHERE `id` IN (" . implode(',', $cid) . ")";
	$database->setQuery($sql);
	$database->query();

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=workgroup", JText::_('RECORDS_DUPLICATED'));
}

function configInclude()
{
	$page = JRequest::getCmd('page', '', '', 'string');
	include_once JPATH_SITE . '/administrator/components/com_maqmahelpdesk/views/ajax/ajax_' . $page . '.php';
}

function showWK()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));
	HelpdeskUtility::AppendResource('draganddrop.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('department_manager.js', JURI::root() . 'media/com_maqmahelpdesk/js/administrator/', 'js', true);

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_workgroup ORDER BY wkdesc");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$sql = "SELECT w.id, w.wkdesc, w.wkmail_address, w.wkmail_address_name, w.show, w.contract, w.ordering, w.slug, w.support_only
			FROM #__support_workgroup w
			ORDER BY w.ordering";
	$database->setQuery($sql, $pageNav->limitstart, $pageNav->limit);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	MaQmaHtmlDefault::display($rows, $pageNav);
}

function editWK($uid = 0)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$document = JFactory::getDocument();
	$editor = JFactory::getEditor();
	$row = new MaQmaHelpdeskTableDepartment($database);

	$document->addScriptDeclaration('var MQM_NAME_REQUIRED = "' . addslashes(JText::_('name_required')) . '";');
	$document->addScriptDeclaration('function CheckHTMLEditor() { ' . $editor->save('wkabout') . ' }');
	HelpdeskUtility::AppendResource('department_edit.js', JURI::root() . 'media/com_maqmahelpdesk/js/administrator/', 'js', true);

	// load the row from the db table
	$row->load($uid);

	HelpdeskUtility::AppendResource('draganddrop.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists['show'] = HelpdeskForm::SwitchCheckbox('radio', 'show', $captions, $values, $row->show, 'switch');
	$lists['trouble'] = HelpdeskForm::SwitchCheckbox('radio', 'trouble', $captions, $values, $row->trouble, 'switch');
	$lists['wkticket'] = HelpdeskForm::SwitchCheckbox('radio', 'wkticket', $captions, $values, $row->wkticket, 'switch');
	$lists['wkkb'] = HelpdeskForm::SwitchCheckbox('radio', 'wkkb', $captions, $values, $row->wkkb, 'switch');
	$lists['wkdownloads'] = HelpdeskForm::SwitchCheckbox('radio', 'wkdownloads', $captions, $values, $row->wkdownloads, 'switch');
	$lists['wkemail'] = HelpdeskForm::SwitchCheckbox('radio', 'wkemail', $captions, $values, $row->wkemail, 'switch');
	$lists['wkfaq'] = HelpdeskForm::SwitchCheckbox('radio', 'wkfaq', $captions, $values, $row->wkfaq, 'switch');
	$lists['wkglossary'] = HelpdeskForm::SwitchCheckbox('radio', 'wkglossary', $captions, $values, $row->wkglossary, 'switch');
	$lists['wkannounces'] = HelpdeskForm::SwitchCheckbox('radio', 'wkannounces', $captions, $values, $row->wkannounces, 'switch');
	$lists['contract'] = HelpdeskForm::SwitchCheckbox('radio', 'contract', $captions, $values, $row->contract, 'switch');
	$lists['use_activity'] = HelpdeskForm::SwitchCheckbox('radio', 'use_activity', $captions, $values, $row->use_activity, 'switch');
	$lists['logo_remove'] = HelpdeskForm::SwitchCheckbox('radio', 'logo_remove', $captions, $values, 0, 'switch');
	$lists['lim_actmsgs'] = HelpdeskForm::SwitchCheckbox('radio', 'lim_actmsgs', $captions, $values, $row->lim_actmsgs, 'switch');
	$lists['lim_actwords'] = HelpdeskForm::SwitchCheckbox('radio', 'lim_actwords', $captions, $values, $row->lim_actwords, 'switch');
	$lists['hyper_links'] = HelpdeskForm::SwitchCheckbox('radio', 'hyper_links', $captions, $values, $row->hyper_links, 'switch');
	$lists['tkt_crt_nfy_mgr'] = HelpdeskForm::SwitchCheckbox('radio', 'tkt_crt_nfy_mgr', $captions, $values, $row->tkt_crt_nfy_mgr, 'switch');
	$lists['tkt_crt_nfy_admin'] = HelpdeskForm::SwitchCheckbox('radio', 'tkt_crt_nfy_admin', $captions, $values, $row->tkt_crt_nfy_admin, 'switch');
	$lists['tkt_asgn_old_asgn'] = HelpdeskForm::SwitchCheckbox('radio', 'tkt_asgn_old_asgn', $captions, $values, $row->tkt_asgn_old_asgn, 'switch');
	$lists['tkt_asgn_new_asgn'] = HelpdeskForm::SwitchCheckbox('radio', 'tkt_asgn_new_asgn', $captions, $values, $row->tkt_asgn_new_asgn, 'switch');
	$lists['tkt_asgn_nfy_usr_one'] = HelpdeskForm::SwitchCheckbox('radio', 'tkt_asgn_nfy_usr_one', $captions, $values, $row->tkt_asgn_nfy_usr_one, 'switch');
	$lists['enable_discussions'] = HelpdeskForm::SwitchCheckbox('radio', 'enable_discussions', $captions, $values, $row->enable_discussions, 'switch');
	$lists['contract_total_disable'] = HelpdeskForm::SwitchCheckbox('radio', 'contract_total_disable', $captions, $values, $row->contract_total_disable, 'switch');
	$lists['use_account'] = HelpdeskForm::SwitchCheckbox('radio', 'use_account', $captions, $values, $row->use_account, 'switch');
	$lists['use_bookmarks'] = HelpdeskForm::SwitchCheckbox('radio', 'use_bookmarks', $captions, $values, $row->use_bookmarks, 'switch');
	$lists['digistore'] = HelpdeskForm::SwitchCheckbox('radio', 'digistore', $captions, $values, $row->digistore, 'switch');
	$lists['bugtracker'] = HelpdeskForm::SwitchCheckbox('radio', 'bugtracker', $captions, $values, $row->bugtracker, 'switch');
	$lists['support_only'] = HelpdeskForm::SwitchCheckbox('radio', 'support_only', $captions, $values, $row->support_only, 'switch');

	$lists['wklogo_browse'] = '<input class="text_area" type="file" name="logo" size="50" maxlength="300" />';
	$lists['lim_actmsgs_chars'] = '<input class="text_area" type="text" name="lim_actmsgs_chars" size="5" maxlength="4" valign="top" value="' . $row->lim_actmsgs_chars . '" />';
	$lists['lim_actmsgs_lines'] = '<input class="text_area" type="text" name="lim_actmsgs_lines" size="5" maxlength="4" valign="top" value="' . $row->lim_actmsgs_lines . '" />';
	$lists['lim_actwords_chars'] = '<input class="text_area" type="text" name="lim_actwords_chars" size="5" maxlength="4" valign="top" value="' . $row->lim_actwords_chars . '" />';

	// Get Joomla! groups
	$lists['groups'] = array();
	$sql = "SELECT `id_group`
			FROM `#__support_department_groups`
			WHERE `id_department`=" . $uid;
	$database->setQuery($sql);
	$groups = $database->loadObjectList();

	foreach($groups as $group)
	{
		$lists['groups'][$group->id_group] = $group->id_group;
	}

	// Build Themes select list
	$directory = JPATH_SITE . '/media/com_maqmahelpdesk/templates/';
	$handle = opendir($directory);
	while ($file = readdir($handle)) {
		$dir = JPath::clean($directory . '/' . $file);
		if (is_dir($dir) && $file != '.' && $file != '..') {
			$langs[] = JHTML::_('select.option', $file);
		}
	}
	closedir($handle);
	$lists['themes'] = JHTML::_('select.genericlist', $langs, 'theme', 'class="inputbox" size="1"', 'value', 'text', $row->theme);

	// E-mail fetching tag
	$mailtag[] = JHTML::_('select.option', 0, JText::_('MQ_NO'));
	$mailtag[] = JHTML::_('select.option', 1, JText::_('MAIL_TAG_ABOVE_ONLY'));
	$mailtag[] = JHTML::_('select.option', 2, JText::_('MAIL_TAG_BOTH'));
	$lists['add_mail_tag'] = JHTML::_('select.genericlist', $mailtag, 'add_mail_tag', 'class="inputbox" size="1"', 'value', 'text', $row->add_mail_tag);

	// Build Support Staff select list
	$sql = "SELECT DISTINCT(u.`id`) AS value, u.`name` AS text FROM #__support_permission p, #__users u WHERE p.id_user=u.id ORDER BY u.name";
	$database->setQuery($sql);
	$rows_staff = $database->loadObjectList();
	$rows_staff = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_staff);
	$lists['auto_assign'] = JHTML::_('select.genericlist', $rows_staff, 'auto_assign', 'class="inputbox" size="1"', 'value', 'text', $row->auto_assign);

	// Build Default Priority select list
	$sql = "SELECT `id` AS value, CONCAT(`description`, ' (', `timevalue`, ' ', `timeunit`,')') AS text FROM `#__support_priority` ORDER BY `description`";
	$database->setQuery($sql);
	$priority = $database->loadObjectList();
	$priority = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $priority);
	$lists['priority'] = JHTML::_('select.genericlist', $priority, 'id_priority', 'class="inputbox" size="1"', 'value', 'text', $row->id_priority);

	// Build Group select list
	$sql = "SELECT `id` AS value, `title` AS text
			FROM `#__support_department_group`
			ORDER BY `title`";
	$database->setQuery($sql);
	$groups = $database->loadObjectList();
	$groups = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $groups);
	$lists['id_group'] = JHTML::_('select.genericlist', $groups, 'id_group', 'class="inputbox" size="1"', 'value', 'text', $row->id_group);

	MaQmaHtmlEdit::display($row, $lists);
}

function saveWK($apply)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$supportConfig = HelpdeskUtility::GetConfig();
	$row = new MaQmaHelpdeskTableDepartment($database);
	$groups = JRequest::getVar("groups", null, "", "array");
	JRequest::checkToken() or jexit('FALSE|Invalid Token');
	$logo_remove = JRequest::getInt("logo_remove", 0);

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

	// Clear user groups
	$sql = "DELETE FROM `#__support_department_groups`
			WHERE `id_department`=" . $row->id;
	$database->setQuery($sql);
	$database->query();

	// Set user groups
	for($i=0; $i<count($groups); $i++)
	{
		$sql = "INSERT INTO `#__support_department_groups`(`id_department`, `id_group`)
				VALUES(" . (int) $row->id . ", " . (int) $groups[$i] . ")";
		$database->setQuery($sql);
		$database->query();
	}

	// Delete current logo if necessary
	if (isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '' && $_POST['old_logo'] != '')
	{
		unlink(JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/' . JRequest::getVar('old_logo', '', 'POST', 'string'));
	}

	// Saves the name of the image in the database
	if (isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '')
	{
		$msg = HelpdeskFile::Upload($row->id, 'W', "logo", JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/');
	}

	// Removes the logo from the selected workgroup
	if ($logo_remove)
	{
		unlink(JPATH_SITE . '/media/com_maqmahelpdesk/images/logos/' . JRequest::getVar('old_logo', '', 'POST', 'string'));
		$database->setQuery("UPDATE #__support_workgroup SET logo=NULL WHERE id='" . $row->id . "'");
		$database->query();
		$msg = JText::_('wk_logo_deleted');
	}

	if ($apply)
	{
		$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=workgroup_edit&cid[0]=" . $row->id);
	}
	else
	{
		$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=workgroup");
	}
}

function removeWK($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('wk_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	// Prevent deletion of last workgroup
	$database->setQuery("SELECT COUNT(*) FROM #__support_workgroup");
	if ($database->loadResult() == 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('wk_delete_last') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		// Delete selected workgroup(s)
		$database->setQuery("DELETE FROM #__support_workgroup WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		// Delete Announcements for this Workgroup
		$database->setQuery("DELETE FROM #__support_announce WHERE id_workgroup IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		// Delete Categories for this Workgroup
		$database->setQuery("DELETE FROM #__support_category WHERE id_workgroup IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		// Delete Client Permissions for this Workgroup
		$database->setQuery("DELETE FROM #__support_client_wk WHERE id_workgroup IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		// Delete Support Users permissions for this Workgroup
		$database->setQuery("DELETE FROM #__support_permission WHERE id_workgroup IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		// Delete Mail Fetch settings for this Workgroup
		$database->setQuery("DELETE FROM #__support_mail_fetch WHERE id_workgroup IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		// Delete Tickets for this Workgroup
		$database->setQuery("DELETE FROM #__support_ticket WHERE id_workgroup IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		// Delete Bookmarks for deleted tickets
		$database->setQuery("DELETE FROM #__support_bookmark WHERE id NOT IN (SELECT id FROM #__support_ticket) AND source='T'");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		// Delete Custom Fields values for deleted tickets
		$database->setQuery("DELETE FROM #__support_field_value WHERE id_ticket NOT IN (SELECT id FROM #__support_ticket)");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}

		// Delete Custom Fields for this Workgroup
		$database->setQuery("DELETE FROM #__support_wk_fields WHERE id_workgroup IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=workgroup");
}

function publishWK($cid = null, $publish = 1)
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
	$database->setQuery("UPDATE #__support_workgroup SET `show`='$publish' WHERE id IN (" . $cids . ")");

	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=workgroup");
}

function saveOrder()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$orders = JRequest::getVar('contentTable', array(0), '', 'array');
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	for ($i = 0; $i < count($orders); $i++)
	{
		$sql = "UPDATE `#__support_workgroup`
				SET `ordering`=$i
				WHERE `id`=" . $orders[$i];
		$database->setQuery($sql);
		$database->query();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=workgroup", JText::_('new_ordering_save'));
}
