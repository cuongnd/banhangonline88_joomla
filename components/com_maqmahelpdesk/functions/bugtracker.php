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
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/bugtracker.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/file.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/form.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/jomsocial.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/utility.php');

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/bugtracker.php';

$id = JRequest::getInt('id', 0);
$extid = JRequest::getInt('extid', 0);

// Activities logger
HelpdeskUtility::ActivityLog('site', 'bugtracker', $task, $id);

switch ($task)
{
	case "save":
		HelpdeskValidation::ValidPermissions($task, 'BUG') ? saveBugtracker() : HelpdeskValidation::NoAccessQuit();
		break;
	case "view":
		HelpdeskValidation::ValidPermissions($task, 'BUG') ? viewBugtracker() : HelpdeskValidation::NoAccessQuit();
		break;
	case "search":
		HelpdeskValidation::ValidPermissions($task, 'BUG') ? showBugtracker() : HelpdeskValidation::NoAccessQuit();
		break;
	case "reply":
		HelpdeskValidation::ValidPermissions($task, 'BUG') ? replyBugtracker() : HelpdeskValidation::NoAccessQuit();
		break;
	case "delete":
		HelpdeskValidation::ValidPermissions($task, 'BUG') ? deleteBugtracker() : HelpdeskValidation::NoAccessQuit();
		break;
	case "post":
		HelpdeskValidation::ValidPermissions($task, 'BUG') ? postBugtracker() : HelpdeskValidation::NoAccessQuit();
		break;
	case "download":
		HelpdeskFile::Download($id, $extid, 'B');
		break;
	default:
		showBugtracker();
		break;
}

function showBugtracker()
{
	global $supportOptions;

	$CONFIG = new JConfig();
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();

	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$page = JRequest::getVar('page', 0, '', 'int');
	$limit = JRequest::getVar('limit', 20, '', 'int');
	$limitstart = ($page * $limit);
	$searchinput = JRequest::getVar('searchinput', '', '', 'string');
	$type = JRequest::getVar('type', 0);
	$category = JRequest::getVar('filter_category', 0);
	$status = JRequest::getVar('status', 0);
	$assignment = JRequest::getInt('assignment', 0);
	$order = JRequest::getVar('order', 'DESC', '', 'string');
	$orderby = JRequest::getVar('orderby', 'b.date_updated', '', 'string');

	// Sets the title
	HelpdeskUtility::PageTitle('showBugtracker');

	// Build WHERE clause
	$where = '';
	if ($searchinput != '')
	{
		$where .= ' AND ';
		$where .= "(";
		$where .= "b.`title` LIKE '%" . $database->escape($searchinput) . "%' OR b.`content` LIKE '%" . $database->escape($searchinput) . "%'";
		$where .= ")";
	}
	if ($type != '')
	{
		$where .= ' AND b.`type`=' . $database->quote($type);
	}
	if ($category != '')
	{
		$where .= ' AND b.`id_category`=' . $database->quote($category);
	}
	if ($status != '')
	{
		$where .= ' AND b.`status`=' . $database->quote($status);
	}
	if ($assignment != '')
	{
		$where .= ' AND b.`id_assign`=' . $assignment;
	}

	// Build Status select list
	$filters = null;
	$filters[] = JHTML::_('select.option', '0', JText::_('status'));
	$filters[] = JHTML::_('select.option', 'P', JText::_('bug_status_p'));
	$filters[] = JHTML::_('select.option', 'O', JText::_('bug_status_o'));
	$filters[] = JHTML::_('select.option', 'I', JText::_('bug_status_i'));
	$filters[] = JHTML::_('select.option', 'R', JText::_('bug_status_r'));
	$filters[] = JHTML::_('select.option', 'C', JText::_('bug_status_c'));
	$filters[] = JHTML::_('select.option', 'D', JText::_('bug_status_d'));
	$lists['status'] = JHTML::_('select.genericlist', $filters, 'status', '', 'value', 'text', $status);

	// Build Type select list
	$filters = null;
	$filters[] = JHTML::_('select.option', '0', JText::_('type'));
	$filters[] = JHTML::_('select.option', 'B', JText::_('bug_type_b'));
	$filters[] = JHTML::_('select.option', 'I', JText::_('bug_type_i'));
	$filters[] = JHTML::_('select.option', 'N', JText::_('bug_type_n'));
	$filters[] = JHTML::_('select.option', 'R', JText::_('bug_type_r'));
	$lists['type'] = JHTML::_('select.genericlist', $filters, 'type', '', 'value', 'text', $type);

	// Build Assignment select list
	$sql = "SELECT u.`id` AS value, u.`name` AS text
			FROM `#__support_permission` AS p
				 INNER JOIN `#__users` AS u ON u.`id`=p.`id_user`
			WHERE p.`bugtracker`=1 AND p.`id_workgroup`=" . $id_workgroup . "
			ORDER BY u.`name`";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('tpl_assignedto'))), $rows_wk);
	$lists['assignment'] = JHTML::_('select.genericlist', $rows_wk, 'assignment', ' id="assignment" class="inputbox"', 'value', 'text', $assignment);

	// Get bugtracker items
	$limit = $limit ? $limit : $CONFIG->list_limit;
	$sql = "SELECT b.`id`, b.`slug`, b.`date_created`, b.`date_updated`, b.`title`, b.`status`, b.`type`, b.`priority`, u.`name` AS requester, u2.`name` AS agent, c.`name` AS category
			FROM `#__support_bugtracker` AS b
				 INNER JOIN `#__users` AS u ON u.`id` = b.`id_user`
				 INNER JOIN `#__support_category` AS c ON c.`id` = b.`id_category`
				 LEFT JOIN `#__users` AS u2 ON u2.`id` = b.`id_assign`
			WHERE b.`id_workgroup`= " . $id_workgroup . $where . "
			ORDER BY " . $orderby . " " . $order . "
			LIMIT " . $limitstart . ", " . $limit;
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	$sql = "SELECT COUNT(*)
			FROM `#__support_bugtracker` AS b
			WHERE b.`id_workgroup`= " . $id_workgroup . $where;
	$database->setQuery($sql);
	$total = $database->loadResult();

	// Ordering & Pagination
	$lorder_slug = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker&orderby=b.slug&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
	$lorder_created = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker&orderby=b.date_created&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
	$lorder_updated = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker&orderby=b.date_updated&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
	$lorder_title = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker&orderby=b.title&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
	$lorder_status = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker&orderby=b.status&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
	$lorder_type = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker&orderby=b.type&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
	$lorder_priority = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker&orderby=b.priority&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
	$lorder_requester = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker&orderby=requester&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
	$lorder_agent = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker&orderby=agent&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
	$lorder_category = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker&orderby=category&order=' . ($order == 'DESC' ? 'ASC' : 'DESC');
	$plink = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker&orderby=' . str_replace(',', '', $orderby) . '&order=' . $order;
	$pages = ceil($total / $limit);

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('bugtracker/list');
	include $tmplfile;
}

function postBugtracker()
{
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$document = JFactory::getDocument();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$id = JRequest::getInt('id', 0);
	$Itemid = JRequest::getVar('Itemid', 0, '', 'int');
	$date = date("Y-m-d H:i:s");

	$document->addScriptDeclaration( 'var MQM_BUG_TITLE = "'.addslashes(JText::_('required_title')).'";' );
	$document->addScriptDeclaration( 'var MQM_BUG_PRIORITY = "'.addslashes(JText::_('bug_priority_required')).'";' );
	$document->addScriptDeclaration( 'var MQM_BUG_TYPE = "'.addslashes(JText::_('bug_type_required')).'";' );
	$document->addScriptDeclaration( 'var MQM_BUG_DESC = "'.addslashes(JText::_('bug_description_required')).'";' );
	$document->addScriptDeclaration( 'var MQM_BUG_CATEGORY = "'.addslashes(JText::_('bug_category_required')).'";' );
	$document->addScriptDeclaration( 'var MQM_BUG_CANCEL = "'.addslashes(JText::_('bug_cancel')).'";' );
	HelpdeskUtility::AppendResource('helpdesk.bugtracker.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// Set title
	HelpdeskUtility::PageTitle('newBugtracker');

	// Set the page title, keywords and metadata
	$document->title = JText::_('create') . ' - ' . JText::_('bugtracker');

	// Display toolbar
	HelpdeskToolbar::Create();

	// If edit than get details
	$row = new MaQmaHelpdeskTableBugtracker($database);
	$row->load($id);
	if (!$id) {
		$row->status = $supportConfig->bug_status;
		$row->date_created = $date;
		$row->date_updated = $date;
	}else{
		$row->date_updated = $date;
	}

	// Build Assignment select list
	$sql = "SELECT u.`id` AS value, u.`name` AS text
			FROM `#__support_permission` AS p
				 INNER JOIN `#__users` AS u ON u.`id`=p.`id_user`
			WHERE p.`bugtracker`=1 AND p.`id_workgroup`=" . $id_workgroup . "
			ORDER BY u.`name`";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
	$lists['id_assign'] = JHTML::_('select.genericlist', $rows_wk, 'id_assign', ' id="id_assign" class="inputbox"', 'value', 'text', $row->id_assign);

	$tmplfile = HelpdeskTemplate::GetFile('bugtracker/form');
	include $tmplfile;
}

function viewBugtracker()
{
	HelpdeskUtility::AppendResource('prettify.css', JURI::root() . 'media/com_maqmahelpdesk/css/', 'css');
	HelpdeskUtility::AppendResource('prettify.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js');

	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$document = JFactory::getDocument();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$id = JRequest::getVar('id', 0, '', 'int');
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);

	// Get question details
	$sql = "SELECT b.`id`, b.`slug`, b.`date_created`, b.`date_updated`, b.`title`, b.`status`, b.`type`, b.`priority`, u.`name` AS requester, u2.`name` AS agent, c.`name` AS category, b.`id_user`, b.`id_assign`, b.`content`, b.`id_category`
			FROM `#__support_bugtracker` AS b
				 INNER JOIN `#__users` AS u ON u.`id` = b.`id_user`
				 INNER JOIN `#__support_category` AS c ON c.`id` = b.`id_category`
				 LEFT JOIN `#__users` AS u2 ON u2.`id` = b.`id_assign`
			WHERE b.`id`=" . $id;
	$database->setQuery($sql);
	$row = $database->loadObject();

	// Get messages
	$sql = "SELECT m.`id`, m.`id_user`, m.`date_created`, m.`content`, u.`name`, m.`published`
			FROM `#__support_bugtracker_messages` AS m
				 INNER JOIN `#__users` AS u ON u.`id` = m.`id_user`
			WHERE m.`id_bugtracker` = " . $row->id . "
			ORDER BY m.`id` ASC";
	$database->setQuery($sql);
	$messages = $database->loadObjectList();

	// Set title
	HelpdeskUtility::PageTitle('viewBugtracker', $row->title);

	// Set the page title, keywords and metadata
	$document->title = $row->title . ' - ' . JText::_('bugtracker');
	$document->description = JString::substr(strip_tags($row->content), 0, 75);

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('bugtracker/view');
	include $tmplfile;
}

function saveBugtracker()
{
	$CONFIG = new JConfig();
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$is_support = HelpdeskUser::IsSupport();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id = JRequest::getInt('id', 0);
	$edit = $id;
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$title = JRequest::getVar('title', '', '', 'string');
	$content = JRequest::getVar('description', '', '', 'string', 2);
	$priority = JRequest::getInt('priority', 0);
	$id_category = JRequest::getInt('id_category', 0);
	$id_assign = JRequest::getInt('id_assign', 0);
	$type = JRequest::getVar('type', '', '', 'string');
	$status = JRequest::getVar('status', '', '', 'string');
	$date_created = JRequest::getVar('date_created', '', '', 'string');
	$date_updated = JRequest::getVar('date_updated', '', '', 'string');
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	// Insert into database
	if ($id) {
		$sql = "UPDATE `#__support_bugtracker`
				SET `priority`=" . $database->quote($priority) . ",
					`date_updated`=" . $database->quote($date_updated) . ",
					`title`=" . $database->quote($title) . ",
					`content`=" . $database->quote($content) . ",
					`status`=" . $database->quote($status) . ",
					`type`=" . $database->quote($type) . ",
					`id_assign`=" . $database->quote($id_assign) . "
				WHERE `id`=" . $id;
		$database->setQuery($sql);
		$database->query();
	}else{
		$sql = "INSERT INTO `#__support_bugtracker`(`id_user`, `id_assign`, `id_workgroup`, `id_category`, `priority`, `date_created`, `date_updated`, `title`, `content`, `status`, `type`)
				VALUES(" . $user->id . ", $id_assign, $id_workgroup, $id_category, $priority, '$date_created', '$date_updated', " . $database->quote($title) . ", " . $database->quote($content) . ", " . $database->quote($status) . ", " . $database->quote($type) . ")";
		$database->setQuery($sql);
		$database->query();
		$id = $database->insertid();

		// Update Slug
		$slug = HelpdeskUtility::CreateSlug($type . $id);
		$sql = "UPDATE `#__support_bugtracker`
			SET `slug`='$slug'
			WHERE `id`=$id";
		$database->setQuery($sql);
		$database->query();
	}

	// File upload
	$msg = '';
	if (isset($_FILES['file']['name'])) {
		HelpdeskFile::Upload($id, 'B', "file", $supportConfig->docspath . '/bugtracker/');
	}

	// Send notification
	/*$vars = array(
				 '[title]'	=> $title,
				 '[question]' => nl2br($content),
				 '[author]'   => $user->name,
				 '[url]'	  => JURI::root().JRoute::_('index.php?option=com_maqmahelpdesk&Itemid='.$Itemid.'&id_workgroup='.$id_workgroup.'&task=bugtracker_view&id='.$id)
			 );
	 $body = HelpdeskTemplate::Parse( $vars, 'bugtracker_new' );
	 $mailer = JFactory::getMailer();
	 $mailer->setSender( array($CONFIG->fromname, $CONFIG->mailfrom) );
	 $mailer->addRecipient( $wkoptions->wkmail_address );
	 $mailer->setSubject( JText::_('mail_subject')  );
	 $mailer->setBody( $body );
	 $mailer->IsHTML( true );
	 $sendmail = $mailer->Send();*/

	// Redirect to bugtracker page
	$link = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker&msgtype=s&msg=' . urlencode(($id ? JText::_('bug_updated') : JText::_('bug_created'))));
	$mainframe->redirect($link);
}

function replyBugtracker()
{
	$CONFIG = new JConfig();
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$is_support = HelpdeskUser::IsSupport();
	$id_bugtracker = intval(JRequest::getVar('id', 0, '', 'int'));
	$answer = JRequest::getVar('reply', '', '', 'string', 2);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$Itemid = JRequest::getInt('Itemid', 0);
	$published = ($is_support ? 1 : 0);
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if ($user->id)
	{
		// Get Workgroup Options
		$database->setQuery("SELECT * FROM #__support_workgroup WHERE id='" . $id_workgroup . "'");
		$wkoptions = $database->loadObject();

		// Insert the answer
		$sql = "INSERT INTO `#__support_bugtracker_messages`(`id_bugtracker`, `id_user`, `date_created`, `content`, `published`)
				VALUES(" . $id_bugtracker . ", " . $user->id . ", '" . date("Y-m-d H:i:s") . "', " . $database->quote(nl2br($answer)) . ", " . $published . ")";
		$database->setQuery($sql);
		$database->query();

		// Get question details
		$sql = "SELECT `id`, `title`, `content`
				FROM #__support_bugtracker 
				WHERE id='" . $id_bugtracker . "'";
		$database->setQuery($sql);
		$bugtracker = $database->loadObject();

		// Post in JomSocial wall
		if ($supportConfig->js_post_bugtracker_wall && $published)
		{
			$comment = sprintf(JText::_("POST_BUG_ANSWER_WALL"), JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=bugtracker_view&id=" . $bugtracker->id), $bugtracker->title);
			HelpdeskJomSocial::Post($user->id, $comment, $answer);
		}

		// Send notification
		/*$vars = array(
					  '[title]'	=> $bugtracker->title,
					  '[question]' => nl2br($bugtracker->content),
					  '[answer]'   => nl2br($answer),
					  '[author]'   => $user->name,
					  '[url]'	  => JURI::root().JRoute::_('index.php?option=com_maqmahelpdesk&Itemid='.$Itemid.'&id_workgroup='.$id_workgroup.'&task=bugtracker_view&id='.$id_bugtracker.'#answer'.$database->insertid())
				  );
		  $body = HelpdeskTemplate::Parse( $vars, 'bugtracker_answer' );
		  $mailer = JFactory::getMailer();
		  $mailer->setSender( array($CONFIG->fromname, $CONFIG->mailfrom) );
		  $mailer->addRecipient( $wkoptions->wkmail_address );
		  $mailer->setSubject( JText::_('mail_subject_answer') );
		  $mailer->setBody( $body );
		  $mailer->IsHTML( true );
		  $mailer->Send();*/

		// Redirect to bugtracker page
		$link = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker&msgtype=s&msg=' . urlencode(JText::_('bug_answer_saved')));
		$mainframe->redirect($link);
	}
	else
	{
		echo '<script> history.go(-1); </script>';
	}
}

function deleteBugtracker()
{
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$is_support = HelpdeskUser::IsSupport();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$id = JRequest::getInt('id', 0);
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$sql = "DELETE FROM `#__support_bugtracker`
			WHERE `id` = $id";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM `#__support_bugtracker_messages`
			WHERE `id_bugtracker` = $id";
	$database->setQuery($sql);
	$database->query();

	// Redirect to discussions page
	$mainframe->redirect(JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker&msgtype=i&msg=' . urlencode(JText::_('discussion_delete'))));
}
