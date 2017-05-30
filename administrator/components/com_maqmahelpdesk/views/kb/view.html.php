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
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/file.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/jomsocial.php');
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/kb.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/kb.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/kb/tmpl/comments.php";
require_once "components/com_maqmahelpdesk/views/kb/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/kb/tmpl/edit.php";

// Set toolbar and page title
HelpdeskKBAdminHelper::addToolbar($task);
HelpdeskKBAdminHelper::setDocument($task);

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}
$id = JRequest::getVar('id', 0, '', 'int');
$extid = JRequest::getVar('extid', 0, '', 'int');
$categories = JRequest::getVar('categories', '', '', 'string');

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'kb', $task, $cid[0]);

switch ($task) {
	case "new":
		editKB(0);
		break;

	case "edit":
		editKB($cid[0]);
		break;

	case "save":
		saveKB($categories, 0);
		break;

	case "apply":
		saveKB($categories, 1);
		break;

	case "remove":
		removeKB($cid);
		break;

	case "publish":
		publishKB($cid, 1);
		break;

	case "unpublish":
		publishKB($cid, 0);
		break;

	case "delattach":
		removeAttach($_REQUEST['id_attach'], $_REQUEST['option'], $_REQUEST['filename']);
		editKB($id);
		break;

	case "delcomment":
		removeComment($_REQUEST['id_comment']);
		editKB($id);
		break;

	case "download":
		HelpdeskFile::Download($id, $extid, 'K');
		break;

	case "moderate":
		moderateComments();
		break;

	case "delcomments":
		removeComments($cid);
		moderateComments();
		break;

	case "publishcomments":
		publishComments($cid);
		moderateComments();
		break;

	case 'saveorder':
		saveOrder();
		break;

	default:
		showKB();
		break;
}

function showKB()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$session = JFactory::getSession();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = JRequest::getInt('limitstart', 0);
	$search_category = JRequest::getInt('filter_category', $session->get('filter_kb_category', 0, 'maqmahelpdesk'));
	$search = JRequest::getVar('search', $session->get('filter_kb', '', 'maqmahelpdesk'), 'POST', 'string');
	$session->set('filter_kb_category', $search_category, 'maqmahelpdesk');
	$session->set('filter_kb', $search, 'maqmahelpdesk');
	HelpdeskUtility::AppendResource('draganddrop.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	$lists['categories'] = HelpdeskForm::BuildCategories($search_category, true, true, false, true);

	// Set where
	$where = array();
	if ($search != '') {
		$where[] = "k.keywords like '%" . ($search) . "%'";
	}
	if ($search_category != 0) {
		$where[] = "k.id IN (SELECT c.id_kb FROM #__support_kb_category AS c WHERE c.id_category=" . $search_category . ")";
	}

	// Get the total number of records
	$sql = "SELECT COUNT(*)
			FROM #__support_kb k " .
		(count($where) ? "WHERE " . implode(" AND ", $where) : "");
	$database->setQuery($sql);
	$total = $database->loadResult();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	// Get articles
	$sql = "SELECT k.id, k.kbtitle, k.publish, k.date_created, k.date_updated, k.kbcode, k.approved, k.slug, k.faq, k.anonymous_access
			FROM #__support_kb k " .
		(count($where) ? "WHERE " . implode(" AND ", $where) : "") . "
			ORDER BY k.ordering, k.kbtitle";
	$database->setQuery($sql, $pageNav->limitstart, $pageNav->limit);
	$rows = $database->loadObjectList();

	MaQmaHtmlDefault::display($rows, $pageNav, $search, $lists['categories']);
}

function editKB($uid = 0)
{
	$database = JFactory::getDBO();
	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	$row = new MaQmaHelpdeskTableKB($database);
	$row->load($uid);

	//Build Workgroups select list
	$query = "SELECT id FROM #__support_workgroup WHERE `show`='1' ORDER BY wkdesc";
	$database->setQuery($query);
	$wkrows = $database->loadObjectList();

	$database->setQuery("SELECT id_category FROM #__support_kb_category WHERE id_kb='" . $row->id . "'");
	$categories = $database->loadObjectList();

	$prodcat = "";
	for ($i = 0; $i < count($categories); $i++) {
		$row_cat = $categories[$i];
		$prodcat .= $row_cat->id_category . (count($categories) - $i > 1 ? ',' : '');
	}

	$lists['categories'] = HelpdeskForm::BuildCategories(0, false, false, true, true);

	// Build Workgroup select list
	$wkrow = &$wkrows[0];
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup WHERE `show`='1' ORDER BY wkdesc";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'id_workgroup', 'class="inputbox" size="1" onChange="ShowCategory()"', 'value', 'text', 0);

	// Get KB Attachments
	$database->setQuery("SELECT * FROM #__support_file WHERE id='" . $row->id . "' AND source='K'");
	$kbAttachs = $database->loadObjectList();

	// Get KB Comments
	$database->setQuery("SELECT c.id, c.id_kb, c.date, c.comment, u.name, c.publish FROM #__support_kb_comment c LEFT JOIN #__users u ON u.id=c.id_user WHERE c.id_kb='" . $row->id . "'");
	$kbComments = $database->loadObjectList();

	// Get KB Rate
	$rate = null;
	$database->setQuery("SELECT SUM(rate) AS ratesum, COUNT(id) AS ratecount FROM #__support_rate WHERE id_table='" . $row->id . "' AND source='K'");
	$rate = $database->loadObject();

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists['faq'] = HelpdeskForm::SwitchCheckbox('radio', 'faq', $captions, $values, $row->faq, 'switch');
	$lists['approved'] = HelpdeskForm::SwitchCheckbox('radio', 'approved', $captions, $values, $row->approved, 'switch');
	$lists['publish'] = HelpdeskForm::SwitchCheckbox('radio', 'publish', $captions, $values, $row->publish, 'switch');

	$attachslist[] = JHTML::_('select.option', '0', JText::_('everybody'));
	$attachslist[] = JHTML::_('select.option', '1', JText::_('registered_users'));
	$attachslist[] = JHTML::_('select.option', '2', JText::_('support_agents_only'));
	$lists['anonymous'] = JHTML::_('select.genericlist', $attachslist, 'anonymous_access', 'class="inputbox" size="1"', 'value', 'text', $row->anonymous_access);

	MaQmaHtmlEdit::display($row, $lists, $kbAttachs, $rate, $wkrows, $kbComments, $prodcat, 0);
}

function saveKB($categories, $apply)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$supportConfig = HelpdeskUtility::GetConfig();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');
	$user = JFactory::getUser();
	$id = JRequest::getInt('id', 0);
	$title = JRequest::getVar('kbtitle', '', '', 'string');
	$slug = JRequest::getVar('slug', '', '', 'string');
	$content = JRequest::getVar('kbcontent', '', '', 'string', 2);
	$keywords = JRequest::getVar('keywords', '', '', 'string');
	$kbcode = JRequest::getVar('kbcode', '', '', 'string');
	$date = HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S");
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$faq = JRequest::getInt('faq', 0);
	$approved = JRequest::getInt('approved', 0);
	$anonymous_access = JRequest::getInt('anonymous_access', 0);
	$publish = JRequest::getInt('publish', 0);
	$msg = '';

	// Insert or Update article
	if ($id) {
		$sql = "UPDATE `#__support_kb`
				SET `kbcode`=" . $database->quote($kbcode) . ",
					`id_user`=" . $user->id . ",
					`kbtitle`=" . $database->quote($title) . ",
					`content`=" . $database->quote($content) . ",
					`keywords`=" . $database->quote($keywords) . ",
					`publish`=" . $publish . ",
					`date_updated`=" . $database->quote($date) . ",
					`anonymous_access`=" . $anonymous_access . ",
					`faq`=" . $faq . ",
					`approved`=" . $approved . ",
					`slug`=" . $database->quote($slug) . "
				WHERE `id`=" . $id;
		$database->setQuery($sql);
		$database->query();
	} else {
		$sql = "SELECT COUNT(*)
				FROM `#__support_kb`
				WHERE `kbtitle`=" . $database->quote($title);
		$database->setQuery($sql);
		$title_check = (int) $database->loadResult();
		$slug = $slug . '-' . HelpdeskDate::DateOffset("%Y%m%d%H%M%S");

		if ($title_check)
		{
			$msg = JText::_("DUPLICATED_TITLE");
		}

		$sql = "INSERT INTO `#__support_kb`(`kbcode`, `id_user`, `kbtitle`, `content`, `keywords`, `publish`, `date_created`, `date_updated`, `anonymous_access`, `faq`, `approved`, `slug`)
				VALUES(" . $database->quote($kbcode) . ", " . $user->id . ", " . $database->quote($title . ($title_check ? ' - ' . $date : '')) . ", " . $database->quote($content) . ", " . $database->quote($keywords) . ", " . $publish . ", " . $database->quote($date) . ", " . $database->quote($date) . ", " . $anonymous_access . ", " . $faq . ", " . $approved . ", " . $database->quote($slug) . ")";
		$database->setQuery($sql);
		$database->query();
		$id = $database->insertid();
	}

	// Get article details
	$sql = "SELECT *
			FROM `#__support_kb`
			WHERE `id`=" . $id;
	$database->setQuery($sql);
	$row = $database->loadObject();

	// Check slug
	if ($row->slug == '')
	{
		$slug = 'KB-' . $row->id;
		$sql = "UPDATE `#__support_kb`
				SET `slug`=" . $database->quote($slug) . "
				WHERE `id`=" . (int) $row->id;
		$database->setQuery($sql);
		$database->query();
	}

	// Get logged user info
	$database->setQuery("SELECT name, email FROM #__users WHERE id='" . $user->id . "'");
	$loggeduser = null;
	$loggeduser = $database->loadObject();

	// Take care of the JomSocial integration if enabled
	if ($supportConfig->post_kb_creation_in_wall) {
		// Get the workgroup of the first category
		$category = explode(",", $categories);
		$sql = "SELECT `id_workgroup`
				FROM `#__support_category`
				WHERE `id` = '" . $category[0] . "'";
		$database->setQuery($sql);
		$id_workgroup = $database->loadResult();

		// Get the Itemid
		$sql = "SELECT `id`
				FROM `#__menu`
				WHERE `link` = 'index.php?option=com_maqmahelpdesk'";
		$database->setQuery($sql);
		$Itemid = $database->loadResult();

		if (JRequest::getVar('id', 0, 'POST', 'int')) {
			$comment = sprintf(JText::_("comment_kb_creation_wall_update"), JRoute::_(JURI::root() . "index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&task=kb_view&id_workgroup=" . $id_workgroup . "&id=" . $row->id), $row->kbtitle, $row->kbtitle);
		} else {
			$comment = sprintf(JText::_("comment_kb_creation_wall_create"), JRoute::_(JURI::root() . "index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&task=kb_view&id_workgroup=" . $id_workgroup . "&id=" . $row->id), $row->kbtitle, $row->kbtitle);
		}

		HelpdeskJomSocial::Post($user->id, $comment, strip_tags(JString::substr($row->content, 0, 100)));
	}

	// Insert categories
	$database->setQuery("DELETE FROM #__support_kb_category WHERE id_kb='" . $row->id . "'");
	$database->query();

	$category = explode(",", $categories);
	for ($i = 0; $i < count($category); $i++)
	{
		$database->setQuery("INSERT INTO #__support_kb_category(id_kb, id_category) values('" . $row->id . "', '" . $category[$i] . "')");
		$database->query();
	}

	// Inserts the attachments
	for ($xx = 0; $xx < $supportConfig->attachs_num; $xx++)
	{
		HelpdeskFile::Upload($row->id, 'K', "file$xx", $supportConfig->docspath . '/', $_POST['desc' . $xx]);
	}

	if ($row->id > 0 && $_POST['fromticket'])
	{
		$database->setQuery("UPDATE #__support_ticket SET id_kb='" . $row->id . "'");
		$database->query();
	}

	if ($apply == true)
	{
		$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=kb_edit&cid=" . $row->id, $msg);
	}
	else
	{
		$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=kb_search", $msg);
	}
}

function removeKB($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$supportConfig = HelpdeskUtility::GetConfig();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('kb_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_kb WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		} else {
			$database->setQuery("DELETE FROM #__support_kb_comment WHERE id_kb IN (" . $cids . ")");
			$database->query();
			$database->setQuery("DELETE FROM #__support_kb_category WHERE id_kb IN (" . $cids . ")");
			$database->query();
			$database->setQuery("DELETE FROM #__support_rate WHERE id_table IN (" . $database->quote($cids) . ") AND source='K'");
			$database->query();
			$database->setQuery("SELECT filename FROM #__support_file WHERE id_table IN (" . $database->quote($cids) . ") AND source='K'");
			$files = $database->loadObjectList();
			for ($x = 0; $x < count($files); $x++) {
				$file = $files[$x];
				unlink($supportConfig->docspath . "/" . $file->filename);
			}
			$database->setQuery("DELETE FROM #__support_file WHERE id_table IN (" . $database->quote($cids) . ") AND source='K'");
			$database->query();
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=kb_search");
}

function publishKB($cid = null, $publish = 1)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$cids = implode(',', $cid);
	$database->setQuery("UPDATE #__support_kb SET `publish`='$publish' WHERE id IN (" . $cids . ")");

	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=kb_search");
}

function removeAttach($id, $filename)
{
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();

	$database->setQuery("DELETE FROM #__support_file WHERE id_file='$id'");
	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
	} else {
		unlink($supportConfig->docspath . "/" . $filename);
	}
}

function removeComment($id)
{
	$database = JFactory::getDBO();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$database->setQuery("DELETE FROM #__support_kb_comment WHERE id='$id'");
	$database->query();
}

function removeComments($cid)
{
	$database = JFactory::getDBO();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$cids = implode(',', $cid);
	$database->setQuery("DELETE FROM #__support_kb_comment WHERE id IN (" . $cids . ")");

	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}
}

function publishComments($cid = null)
{
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$cids = implode(',', $cid);
	$database->setQuery("UPDATE #__support_kb_comment SET `publish`='1' WHERE id IN (" . $cids . ")");

	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	// If JomSocial post comments is enabled must process it here
	if ($supportConfig->post_comments_in_wall) {
		$sql = "SELECT c.`id_user`, c.`id_kb`, c.`comment`, k.`kbtitle`, c.`id_workgroup`, c.`itemid`
				FROM `#__support_kb_comment` AS c
					 INNER JOIN `#__support_kb` AS k ON k.`id`=c.`id_kb`
				WHERE c.`id` IN (" . $cids . ")";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();

		for ($i = 0; $i < count($rows); $i++) {
			$row = $rows[$i];
			$comment = sprintf(JText::_("comment_post_wall"), "index.php?option=com_maqmahelpdesk&Itemid=" . $row->itemid . "&task=kb_view&id_workgroup=" . $row->id_workgroup . "&id=" . $row->id_kb, $row->kbtitle, $row->kbtitle);
			HelpdeskJomSocial::Post($row->id_user, $comment, $row->comment);
		}
	}
}

function moderateComments()
{
	$database = JFactory::getDBO();

	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	// get the total number of records
	$database->setQuery("SELECT COUNT(*) FROM #__support_kb_comment WHERE publish='0' ORDER BY id");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$database->setQuery("SELECT k.kbtitle, c.id, c.comment, u.name, c.date, k.id as id_kb, c.publish FROM #__support_kb_comment AS c INNER JOIN #__support_kb AS k ON c.id_kb=k.id LEFT JOIN #__users AS u ON u.id=c.id_user WHERE c.publish='0' ORDER BY c.id ", $pageNav->limitstart, $pageNav->limit);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	MaQmaHtmlComments::display($rows, $pageNav);
}

function saveOrder()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$orders = JRequest::getVar('contentTable', array(0), '', 'array');
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	for ($i = 0; $i < count($orders); $i++)
	{
		$sql = "UPDATE `#__support_kb`
				SET `ordering`=$i
				WHERE `id`=" . $orders[$i];
		$database->setQuery($sql);
		$database->query();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=kb_search", JText::_('new_ordering_save'));
}
