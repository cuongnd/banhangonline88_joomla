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
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/form.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/jomsocial.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/utility.php';

$id = JRequest::getVar('id', 0, '', 'int');

// Activities logger
HelpdeskUtility::ActivityLog('site', 'discussions', $task, $id);

switch ($task)
{
	case "save":
		HelpdeskValidation::ValidPermissions($task, 'DI') ? saveQA() : HelpdeskValidation::NoAccessQuit();
		break;
	case "view":
		HelpdeskValidation::ValidPermissions($task, 'DI') ? viewQA() : HelpdeskValidation::NoAccessQuit();
		break;
	case "search":
		HelpdeskValidation::ValidPermissions($task, 'DI') ? searchQA() : HelpdeskValidation::NoAccessQuit();
		break;
	case "question":
		HelpdeskValidation::ValidPermissions($task, 'DI') ? questionQA() : HelpdeskValidation::NoAccessQuit();
		break;
	case "answer":
		HelpdeskValidation::ValidPermissions($task, 'DI') ? answerQA() : HelpdeskValidation::NoAccessQuit();
		break;
	case "accept":
		HelpdeskValidation::ValidPermissions($task, 'DI') ? acceptQA() : HelpdeskValidation::NoAccessQuit();
		break;
	case "vote":
		HelpdeskValidation::ValidPermissions($task, 'DI') ? voteQA() : HelpdeskValidation::NoAccessQuit();
		break;
	case "publish":
		HelpdeskValidation::ValidPermissions($task, 'DI') ? publishQA() : HelpdeskValidation::NoAccessQuit();
		break;
	case "delete":
		HelpdeskValidation::ValidPermissions($task, 'DI') ? deleteQA() : HelpdeskValidation::NoAccessQuit();
		break;
	case "category":
		showQACategory();
		break;
	case "leaderboard":
		showQALeaderBoard();
		break;
	case "customiit":
		HelpdeskValidation::ValidPermissions($task, 'DI') ? custom_iit() : HelpdeskValidation::NoAccessQuit();
		break;
	default:
		showQA();
		break;
}

function custom_iit()
{
	$tmplfile = HelpdeskTemplate::GetFile('customiit/index');
	include $tmplfile;
}

function showQALeaderBoard()
{
	global $supportOptions;

	$CONFIG = new JConfig();
	$mainframe = JFactory::getApplication();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$user = JFactory::getUser();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$id_category = JRequest::getVar('id_category', 0, '', 'int');
	$task = JRequest::getVar('task','');
	$limit = intval(JRequest::getVar('limit', '20', '', 'string'));
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));
	$searchinput = JRequest::getVar('searchinput','');

	$db = JFactory::getDBO();
	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// Sets the title
	HelpdeskUtility::PageTitle('LeaderBoard');
	// Display toolbar
	HelpdeskToolbar::Create();

	$user_name = JRequest::getVar('user','');
	if(!$user_name)
	{
		$sql = "SELECT id_user, COUNT(*)
			FROM #__support_discussions_messages
			WHERE id_user > 0
			GROUP BY id_user
			LIMIT 20";
		$db->setQuery($sql);
		$rows = $db->loadObjectList();
		//print_r($rows);die;
		$tmplfile = HelpdeskTemplate::GetFile('discussions/leaderboard');
	}
	else
	{
		$linkA = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=discussions_leaderboard&dtype=answer&user=' . $user_name;
		$link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=discussions_leaderboard&dtype=question&user=' . $user_name;

		$dtype = JRequest::getVar('dtype','question');

		//-------------------------------------prepare total
		if ($user_name)
		{
			$user_info = JFactory::getUser($user_name);
			$where = " WHERE d.`id_user`='".$user_info->id."'";
		}
		//print_r($rows);
		$sql = "SELECT COUNT(*)
				FROM `#__support_discussions` AS d
					 INNER JOIN `#__users` AS u ON u.`id` = d.`id_user`
					 LEFT JOIN `#__support_users` AS su ON su.`id_user` = u.`id`
				" . $where;
		$db->setQuery($sql);
		$totalQ = $db->loadResult();
		//print_r($totalQ);
		//print_r($rows);
		$sqlA = "SELECT COUNT(*)
				FROM `#__support_discussions_messages` AS d
					 INNER JOIN `#__users` AS u ON u.`id` = d.`id_user`
					 LEFT JOIN `#__support_users` AS su ON su.`id_user` = u.`id`
				" . $where;
		$db->setQuery($sqlA);
		$totalA = $db->loadResult();

		//end--------------------------------------

		//print_r($dtype);
		// Get posts
		// Build WHERE clause


		$limit = $limit ? $limit : $CONFIG->list_limit;
		if($dtype =='question')
		{
			$sql = "SELECT distinct(d.`id`), u.`name`, d.`date_created`, d.`title`, d.`content`, d.`published`, d.`status`, d.`converted`, d.`votes`, d.`views`, d.`tags`, d.`id_user`, su.`avatar`
					FROM `#__support_discussions` AS d
						 INNER JOIN `#__users` AS u ON u.`id` = d.`id_user`
						 LEFT JOIN `#__support_users` AS su ON su.`id_user` = u.`id`
						 LEFT JOIN `#__support_discussions_messages` AS m ON m.`id_discussion` = d.`id`
					" . $where . "
					ORDER BY d.`id` DESC
					LIMIT " . $limitstart . ", " . $limit;
			//print_r($sql);
			$db->setQuery($sql);
			$rows = $db->loadObjectList();

			if ($totalQ <= $limit) $limitstart = 0;
			jimport('joomla.html.pagination');
			$pageNav = new JPagination($totalQ, $limitstart, $limit);

			// Takes care of pagination
			$pagelinks = $pageNav->getPagesLinks($link);
			$pagecounter = $pageNav->getPagesCounter();
		}

		if($dtype =='answer')
		{
			$sqlA = "SELECT m.`id` as mid, d.`id` as id, u.`name`, d.`date_created`, d.`title`, d.`content`, d.`votes`, d.`tags`, d.`id_user`, su.`avatar`
					FROM `#__support_discussions_messages` AS m
						 INNER JOIN `#__users` AS u ON u.`id` = m.`id_user`
						 LEFT JOIN `#__support_users` AS su ON su.`id_user` = u.`id`
						 LEFT JOIN `#__support_discussions` AS d ON m.`id_discussion` = d.`id`
					WHERE m.`id_user`='".$user_info->id."'
					ORDER BY d.`id` DESC
					LIMIT " . $limitstart . ", " . $limit;
			//print_r($sqlA);
			$db->setQuery($sqlA);
			$rows = $db->loadObjectList();

			//print_r($totalA);
			if ($totalA <= $limit) $limitstart = 0;
			jimport('joomla.html.pagination');
			$pageNavQ = new JPagination($totalA, $limitstart, $limit);

			// Takes care of pagination
			$pagelinks = $pageNavQ->getPagesLinks($linkA);
			$pagecounter = $pageNavQ->getPagesCounter();
		}
		$tmplfile = HelpdeskTemplate::GetFile('discussions/profile');
	}
	include $tmplfile;
}

function showQA()
{
	global $supportOptions;

	$CONFIG = new JConfig();
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$id_category = JRequest::getVar('id_category', 0, '', 'int');
	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// Sets the title
	HelpdeskUtility::PageTitle('showDiscussions');

	// Get categories
	$sql = "SELECT c.`id`, c.`name`, COUNT(d.`id`) AS total
			FROM `#__support_category` AS c
				 LEFT JOIN `#__support_discussions` AS d ON d.`id_category` = c.`id`
			WHERE c.`discussions`=1 AND c.`show`=1 AND c.`id_workgroup`=" . $id_workgroup . "
			GROUP BY c.`id`, c.`name`
			ORDER BY c.`name` ASC";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	if (count($rows) == 1) {
		$link = "index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=discussions_category&id_category=" . $rows[0]->id;
		$mainframe->redirect($link);
	}

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('discussions/discussions');
	include $tmplfile;
}

function showQACategory()
{
	global $supportOptions;

	$CONFIG = new JConfig();
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$id_category = JRequest::getVar('id_category', 0, '', 'int');
	$limit = intval(JRequest::getVar('limit', '', '', 'string'));
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));
	$searchinput = JRequest::getVar('searchinput', '', '', 'string');

	// Sets the title
	HelpdeskUtility::PageTitle('showDiscussions');

	// Build WHERE clause
	$where = '';
	if (!$is_support) {
		$where .= 'WHERE d.`published`=1';
	}
	if ($searchinput != '') {
		$where .= ($where == '' ? 'WHERE ' : ' AND ');
		$where .= "(";
		$where .= "d.`title` LIKE '%" . $database->escape($searchinput) . "%' OR d.`content` LIKE '%" . $database->escape($searchinput) . "%' OR d.`tags` LIKE '%" . $database->escape($searchinput) . "%'";
		$where .= ")";
	}
	$where .= ($where == '' ? 'WHERE ' : ' AND ');
	$where .= "(";
	$where .= "d.`id_category`=" . $id_category;
	$where .= ")";

	// Get posts
	$limit = $limit ? $limit : $CONFIG->list_limit;
	$sql = "SELECT d.`id`, u.`name`, d.`date_created`, d.`title`, d.`content`, d.`published`, d.`status`, d.`converted`, d.`votes`, d.`views`, d.`tags`, d.`id_user`, su.`avatar`, COUNT(m.`id`) AS messages
			FROM `#__support_discussions` AS d
				 INNER JOIN `#__users` AS u ON u.`id` = d.`id_user`
				 LEFT JOIN `#__support_users` AS su ON su.`id_user` = u.`id`
				 LEFT JOIN `#__support_discussions_messages` AS m ON m.`id_discussion` = d.`id`
			" . $where . "
			GROUP BY d.`id`, u.`name`, d.`date_created`, d.`title`, d.`content`, d.`published`, d.`status`, d.`converted`
			ORDER BY d.`id` DESC 
			LIMIT " . $limitstart . ", " . $limit;
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	$sql = "SELECT COUNT(*)
			FROM `#__support_discussions` AS d
				 INNER JOIN `#__users` AS u ON u.`id` = d.`id_user`
				 LEFT JOIN `#__support_users` AS su ON su.`id_user` = u.`id`
			" . $where;
	$database->setQuery($sql);
	$total = $database->loadResult();

	if ($total <= $limit) $limitstart = 0;
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	// Takes care of pagination
	$link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=discussions&id_category=' . $id_category;
	$pagelinks = $pageNav->getPagesLinks($link);
	$pagecounter = $pageNav->getPagesCounter();

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('discussions/category');
	include $tmplfile;
}

function viewQA()
{
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$session = JFactory::getSession();
	$document = JFactory::getDocument();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$user = JFactory::getUser();
	$id = JRequest::getVar('id', 0, '', 'int');
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$id_category = JRequest::getVar('id_category', 0, '', 'int');
	$calculation1 = rand(1,9);
	$calculation2 = rand(1,9);
	$calculation = $calculation1 + $calculation2;
	$session->set("calculation", $calculation);

	HelpdeskUtility::AppendResource('helpdesk.discussions.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('prettify.css', JURI::root() . 'media/com_maqmahelpdesk/css/', 'css');
	HelpdeskUtility::AppendResource('prettify.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js');
	
	// Get question details
	$sql = "SELECT d.`id`, d.`tags`, d.`date_created`, d.`title`, d.`content`, d.`published`, d.`status`, d.`converted`, d.`views`, d.`votes`, u.`name`, su.`avatar`, d.`id_user`
			FROM `#__support_discussions` AS d
				 INNER JOIN `#__users` AS u ON u.`id` = d.`id_user`
				 LEFT JOIN `#__support_users` AS su ON su.`id_user` = u.`id`
			WHERE d.`id`=" . $id . (!$is_support ? " AND d.published = 1" : "");
	$database->setQuery($sql);
	$row = $database->loadObject();

	// Get messages
	$sql = "SELECT m.`id`, m.`id_user`, m.`is_support`, m.`date_created`, m.`content`, m.`votes`, su.`avatar`, u.`name`, m.`published`
			FROM `#__support_discussions_messages` AS m
				 INNER JOIN `#__users` AS u ON u.`id` = m.`id_user`
				 LEFT JOIN `#__support_users` AS su ON su.`id_user` = m.`id_user`
			WHERE m.`id_discussion` = " . $row->id . (!$is_support ? " AND m.published = 1" : "") . "
			ORDER BY m.`id` ASC";
	$database->setQuery($sql);
	$messages = $database->loadObjectList();

	// Increment views (only if not the author)
	if ($row->id_user != $user->id) {
		$sql = "UPDATE `#__support_discussions`
				SET `views`=(`views`+1)
				WHERE `id`=" . $id;
		$database->setQuery($sql);
		$database->query();
	}

	// Avatar
	$avatar = HelpdeskUser::GetAvatar($row->id_user);

	// Set title
	HelpdeskUtility::PageTitle('viewDiscussion', $row->title);

	// Set the page title, keywords and metadata
	$document->title = $row->title . ' - ' . JText::_('DISCUSSIONS');
	$document->description = JString::substr(strip_tags($row->content), 0, 75);

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('discussions/view');
	include $tmplfile;
}

function acceptQA()
{
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$is_support = HelpdeskUser::IsSupport();
	$supportConfig = HelpdeskUtility::GetConfig();
	$id_discussion = intval(JRequest::getVar('id_discussion', 0, '', 'int'));
	$id_message = intval(JRequest::getVar('id_message', 0, '', 'int'));

	if ($user->id) {
		// Get message author
		$sql = "SELECT `id_user`
				FROM `#__support_discussions_messages`
				WHERE `id`=" . $id_message;
		$database->setQuery($sql);
		$author = $database->loadResult();

		// Post in JomSocial wall
		if ($supportConfig->js_post_question_wall) {
			$sql = "SELECT `title`
					FROM `#__support_discussions`
					WHERE `id` = $id_discussion";
			$database->setQuery($sql);
			$title = $database->loadResult();

			$sql = "SELECT u.`name`
					FROM `#__users` AS u
						 INNER JOIN `#__discussions_messages` AS m ON m.`id_user`=u.`id`
					WHERE m.`id`=";

			$comment = sprintf(JText::_("js_answer_selected"), JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=discussions_view&id=" . $id_discussion), $title, $title);
			HelpdeskJomSocial::Post($user->id, $comment, $title);
		}

		// Give points in JomSocial
		HelpdeskJomSocial::Points($author, 'maqma.answer.selected');

		// Updates the database
		$sql = "UPDATE `#__support_discussions`
				SET `status`=$id_message
				WHERE `id` = $id_discussion
				  AND `id_user` = " . $user->id;
		$database->setQuery($sql);
		echo $database->query() . '|' . JText::_('answer_was_selected');
	} else {
		echo '0|';
	}
}

function voteQA()
{
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$is_support = HelpdeskUser::IsSupport();
	$updown = JRequest::getVar('updown', '', '', 'string');
	$id_discussion = intval(JRequest::getVar('id_discussion', 0, '', 'int'));
	$id_message = intval(JRequest::getVar('id_message', 0, '', 'int'));

	// Check if already voted
	$sql = "SELECT `id`
			FROM `#__support_discussions_votes`
			WHERE `id_discussion`=$id_discussion AND `id_user`=" . $user->id;
	$database->setQuery($sql);
	$vote_id = (int)$database->loadResult();

	if ($vote_id) {
		echo '0|' . JText::_('already_voted_discussion');
		return;
	}

	if ($user->id) {
		// Get message author
		$sql = "SELECT `id_user`
				FROM `#__support_discussions_messages`
				WHERE `id`=" . $id_message;
		$database->setQuery($sql);
		$author = $database->loadResult();

		// Post in JomSocial wall
		if ($supportConfig->js_post_votes_wall) {
			$sql = "SELECT `title`
					FROM `#__support_discussions`
					WHERE `id` = $id_discussion";
			$database->setQuery($sql);
			$title = $database->loadResult();

			$comment = sprintf(JText::_("js_question_vote"), JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=discussions_view&id=" . $id_discussion), $title, $title);
			HelpdeskJomSocial::Post($user->id, $comment, $title);
		}

		// Give points in JomSocial
		HelpdeskJomSocial::Points($author, 'maqma.answer.vote');

		// Record vote for user in this discussion
		$sql = "INSERT INTO `#__support_discussions_votes`(`id_user`, `id_discussion`, `id_message`, `date_created`, `vote`)
				VALUES(" . $user->id . ", $id_discussion, $id_message, '" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S") . "', " . ($updown == '+' ? 1 : -1) . ")";
		$database->setQuery($sql);
		$database->query();

		// Update votes
		if ($id_message) {
			$sql = "UPDATE `#__support_discussions_messages`
					SET `votes`=(`votes`" . ($updown == 'up' ? '+' : '-') . "1)
					WHERE `id` = $id_message";
		} else {
			$sql = "UPDATE `#__support_discussions`
					SET `votes`=(`votes`" . ($updown == 'up' ? '+' : '-') . "1)
					WHERE `id` = $id_discussion";
		}
		$database->setQuery($sql);
		echo $database->query() . '|' . JText::_('voted_answer');
	} else {
		echo '0|';
	}
}

function saveQA()
{
	$CONFIG = new JConfig();
	$mainframe = JFactory::getApplication();
	$session = JFactory::getSession();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$is_support = HelpdeskUser::IsSupport();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$id_category = JRequest::getVar('id_category', 0, '', 'int');
	$title = JRequest::getVar('title', '', '', 'string');
	$content = JRequest::getVar('question_content', '', '', 'string', 2);
	$tags = JRequest::getVar('tags', '', '', 'string');
	$valcalc = JRequest::getVar('valcalc', 0, '', 'int');
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if ($user->id || (!$user->id && $supportConfig->discussions_anonymous && $valcalc==$session->get("calculation"))) {
		// Get Workgroup Options
		$database->setQuery("SELECT * FROM #__support_workgroup WHERE id=" . $id_workgroup);
		$wkoptions = $database->loadObject();

		// Duplication check
		$sql = "SELECT COUNT(*)
				FROM `#__support_discussions`
				WHERE `id_user`=" . $user->id . "
				  AND `id_workgroup`=" . $id_workgroup . "
				  AND `title`=" . $database->quote($title) . "
				  AND `content`=" . $database->quote(nl2br($content));
		$database->setQuery($sql);

		if ($database->loadResult() == 0) {
			// Insert into database
			$sql = "INSERT INTO `#__support_discussions`(`tags`, `id_user`, `id_workgroup`, `date_created`, `title`, `content`, `published`, `id_category`)
					VALUES(" . $database->quote(str_replace(' ', ',', $tags)) . "," . $user->id . ", $id_workgroup, '" . date("Y-m-d H:i:s") . "', " . $database->quote($title) . ", " . $database->quote(nl2br($content)) . "," . ($supportConfig->discussions_moderated ? 0 : 1) . "," . $id_category . ")";
			$database->setQuery($sql);
			$database->query();
			$id = $database->insertid();

			// Post in JomSocial wall
			if ($supportConfig->js_post_question_wall) {
				$comment = sprintf(JText::_("js_created_question"), JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=discussions_view&id_category=" . $id_category . "&id=" . $id), $title, $title);
				HelpdeskJomSocial::Post($user->id, $comment, $title);
			}

			// Send notification
			$vars = array(
				'[title]' => $title,
				'[question]' => nl2br($content),
				'[author]' => $user->name,
				'[url]' => JURI::root() . JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=discussions_view&id=' . $id . '&id_category=' . $id_category)
			);
			$body = HelpdeskTemplate::Parse($vars, 'discussion_new');
			$mailer = JFactory::getMailer();
			$mailer->setSender($CONFIG->mailfrom, $CONFIG->fromname);
			$mailer->addRecipient($wkoptions->wkmail_address);
			$mailer->setSubject(JText::_('mail_subject'));
			$mailer->setBody($body);
			$mailer->IsHTML(true);
			$sendmail = $mailer->Send();

			// Redirect to discussions page
			$mainframe->redirect(JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=discussions&id_category=' . $id_category . '&msgtype=i&msg=' . urlencode($supportConfig->discussions_moderated ? JText::_('saved_pending') : JText::_('saved_available'))));
		} else {
			// Redirect to discussions page
			$mainframe->redirect(JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=discussions&id_category=' . $id_category));
		}
	}
}

function answerQA()
{
	$CONFIG = new JConfig();
	$database = JFactory::getDBO();
	$session = JFactory::getSession();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$is_support = HelpdeskUser::IsSupport();
	$id_discussion = intval(JRequest::getVar('id_discussion', 0, '', 'int'));
	$answer = JRequest::getVar('answer', '', '', 'string', 2);
	$valcalc = JRequest::getVar('valcalc', 0, '', 'int');
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$Itemid = JRequest::getInt('Itemid', 0);

	if ($user->id || (!$user->id && $supportConfig->discussions_anonymous && $valcalc==$session->get("calculation"))) {
		// Get Workgroup Options
		$database->setQuery("SELECT * FROM #__support_workgroup WHERE id='" . $id_workgroup . "'");
		$wkoptions = $database->loadObject();

		// Insert the answer
		$sql = "INSERT INTO `#__support_discussions_messages`(`id_discussion`, `id_user`, `is_support`, `date_created`, `content`, `published`)
				VALUES(" . $id_discussion . ", " . $user->id . ", " . $is_support . ", '" . date("Y-m-d H:i:s") . "', " . $database->quote(nl2br($answer)) . ", " . ($supportConfig->discussions_moderated ? 0 : 1) . ")";
		$database->setQuery($sql);
		echo $database->query() . '|' . JText::_('answer_saved');

		// Get question details
		$sql = "SELECT `id`, `title`, `content`, `id_category`
				FROM #__support_discussions 
				WHERE id='" . $id_discussion . "'";
		$database->setQuery($sql);
		$discussion = $database->loadObject();

		// Post in JomSocial wall
		if ($supportConfig->js_post_answer_wall) {
			$comment = sprintf(JText::_("js_created_answer"), JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=discussions_view&id=" . $id_discussion . "&id_category=" . $discussion->id_category), $discussion->title, $discussion->title);
			HelpdeskJomSocial::Post($user->id, $comment, $answer);
		}

		// Send notification
		$vars = array(
			'[title]' => $discussion->title,
			'[question]' => nl2br($discussion->content),
			'[answer]' => nl2br($answer),
			'[author]' => $user->name,
			'[url]' => JURI::root() . JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=discussions_view&id=' . $id_discussion . "&id_category=" . $discussion->id_category . '#answer' . $database->insertid())
		);
		$body = HelpdeskTemplate::Parse($vars, 'discussion_answer');
		$mailer = JFactory::getMailer();
		$mailer->setSender($CONFIG->mailfrom, $CONFIG->fromname);
		$mailer->addRecipient($wkoptions->wkmail_address);
		$mailer->setSubject(JText::_('mail_subject_answer') . ($supportConfig->discussions_moderated ? ' - ' . JText::_('unpublished') : ''));
		$mailer->setBody($body);
		$mailer->IsHTML(true);
		$mailer->Send();
	} else {
		echo '0|';
	}
}

function questionQA()
{
	$mainframe = JFactory::getApplication();
	$session = JFactory::getSession();
	$document = JFactory::getDocument();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$id_category = JRequest::getVar('id_category', 0, '', 'int');
	$Itemid = JRequest::getVar('Itemid', 0, '', 'int');
	$lists['id_category'] = HelpdeskForm::BuildCategories(0, false, false, false, false, false, false, null, true);
	$calculation1 = rand(1,9);
	$calculation2 = rand(1,9);
	$calculation = $calculation1 + $calculation2;
	$session->set("calculation", $calculation);
	
	$document->addScriptDeclaration( 'var MQM_QA_TITLE = "'.addslashes(JText::_('required_title')).'";' );
	$document->addScriptDeclaration( 'var MQM_QA_QUESTION = "'.addslashes(JText::_('required_question')).'";' );
	$document->addScriptDeclaration( 'var MQM_QA_TAGS = "'.addslashes(JText::_('required_tags')).'";' );
	$document->addScriptDeclaration( 'var MQM_QA_CANCEL = "'.addslashes(JText::_('cancel_question')).'";' );
	HelpdeskUtility::AppendResource('helpdesk.discussions.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// Set title
	HelpdeskUtility::PageTitle('newDiscussion');

	// Set the page title, keywords and metadata
	$document->title = JText::_('ask_question') . ' - ' . JText::_('DISCUSSIONS');

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('discussions/form');
	include $tmplfile;
}

function publishQA()
{
	$database = JFactory::getDBO();
	$is_support = HelpdeskUser::IsSupport();
	$id_discussion = intval(JRequest::getVar('id_discussion', 0, '', 'int'));
	$id_message = intval(JRequest::getVar('id_message', 0, '', 'int'));

	if ($is_support) {
		if ($id_message) {
			$sql = "UPDATE `#__support_discussions_messages`
					SET `published`=1
					WHERE `id` = $id_message";
		} else {
			$sql = "UPDATE `#__support_discussions`
					SET `published`=1
					WHERE `id` = $id_discussion";
		}
		$database->setQuery($sql);
		echo $database->query() . '|' . JText::_('question_published');
	} else {
		echo '0|';
	}
}

function deleteQA()
{
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$is_support = HelpdeskUser::IsSupport();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$id = JRequest::getInt('id', 0);

	$sql = "DELETE FROM `#__support_discussions`
			WHERE `id` = $id";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM `#__support_discussions_messages`
			WHERE `id_discussion` = $id";
	$database->setQuery($sql);
	$database->query();

	// Redirect to discussions page
	$mainframe->redirect(JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=discussions&msgtype=i&msg=' . urlencode(JText::_('discussion_delete'))));
}