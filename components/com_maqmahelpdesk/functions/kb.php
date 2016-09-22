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
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/file.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/form.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/glossary.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/jomsocial.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/kb.php';
require_once JPATH_ADMINISTRATOR . '/components/com_search/helpers/search.php';

$rate = JRequest::getVar('rate', 0, '', 'int');
$extid = JRequest::getVar('extid', 0, '', 'int');
$parent = JRequest::getVar('parent', 0, '', 'int');
$exact_phrase = JRequest::getVar('exact_phrase', '', '', 'string');
$one_word = JRequest::getVar('one_word', '', '', 'string');
$all_words = JRequest::getVar('all_words', '', '', 'string');
$exclude_words = JRequest::getVar('exclude_words', '', '', 'string');
$id_category = JRequest::getVar('id_category', 0, '', 'int');
$from_date = JRequest::getVar('from_date', '', '', 'string');
$to_date = JRequest::getVar('to_date', '', '', 'string');
$limit = JRequest::getVar('limit', '', '', 'string');
$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
$id = JRequest::getVar('id', 0, '', 'int');
$ticket = JRequest::getVar('ticket', 0, '', 'int');
$comment = JRequest::getVar('comment', '', '', 'string');

// Activities logger
HelpdeskUtility::ActivityLog('site', 'kb', $task, $id);

switch ($task)
{
	case "view":
		HelpdeskValidation::ValidPermissions($task, 'K') ? viewKB($id, 0) : HelpdeskValidation::NoAccessQuit();
		break;

	case "print":
		HelpdeskValidation::ValidPermissions($task, 'K') ? viewKB($id, 1) : HelpdeskValidation::NoAccessQuit();
		break;

	case "new":
		HelpdeskValidation::ValidPermissions($task, 'K') ? editKB(0) : HelpdeskValidation::NoAccessQuit();
		break;

	case "edit":
		HelpdeskValidation::ValidPermissions($task, 'K') ? editKB($id) : HelpdeskValidation::NoAccessQuit();
		break;

	case "convert":
		HelpdeskValidation::ValidPermissions($task, 'K') ? editKB(0, $ticket) : HelpdeskValidation::NoAccessQuit();
		break;

	case "save":
		HelpdeskValidation::ValidPermissions($task, 'K') ? saveKB() : HelpdeskValidation::NoAccessQuit();
		break;

	case "faqcomment":
		HelpdeskValidation::ValidPermissions($task, 'FAQ') ? commentKB($id, $comment, 1) : HelpdeskValidation::NoAccessQuit();
		break;

	case "comment":
		HelpdeskValidation::ValidPermissions($task, 'K') ? commentKB($id, $comment, 0) : HelpdeskValidation::NoAccessQuit();
		break;

	case "bookmark":
		HelpdeskValidation::ValidPermissions($task, 'K') ? bookmarkKB($id) : HelpdeskValidation::NoAccessQuit();
		break;

	case "list":
		HelpdeskValidation::ValidPermissions($task, 'K') ? showKB($parent, $limit, $limitstart) : HelpdeskValidation::NoAccessQuit();
		break;

	case "search":
		HelpdeskValidation::ValidPermissions($task, 'K') ? searchKB() : HelpdeskValidation::NoAccessQuit();
		break;

	case "download":
		HelpdeskValidation::ValidPermissions($task, 'K') ?
			HelpdeskFile::Download($id, $extid, 'K') : HelpdeskValidation::NoAccessQuit();
		break;

	case "faq":
		HelpdeskValidation::ValidPermissions($task, 'FAQ') ? showFAQ($parent) : HelpdeskValidation::NoAccessQuit();
		break;

	case "faqview":
		HelpdeskValidation::ValidPermissions($task, 'FAQ') ? viewKB($id, 0, 1) : HelpdeskValidation::NoAccessQuit();
		break;
}

function bookmarkKB($id)
{
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();

	$sql = "SELECT COUNT(*)
			FROM #__support_bookmark
			WHERE id_user='" . $user->id . "'
			  AND id_bookmark='" . $id . "'
			  AND source='K'";
	$database->setQuery($sql);
	$exists = $database->loadResult();

	if ($exists == 0)
	{
		$sql = "INSERT INTO #__support_bookmark(id_user, id_bookmark, source)
				VALUES('" . $user->id . "', '" . $id . "', 'K')";
		$database->setQuery($sql);
		$database->query();
		$msg = JText::_('article_bookmark');
		$msgtype = 'i';
	}
	else
	{
		$msg = JText::_('bookmark_kb');
		$msgtype = 'w';
	}

	$mainframe->redirect('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' .
		$id_workgroup . '&task=kb_view&id=' . $id . '&msg=' . urlencode($msg) . '&msgtype=' . $msgtype);
}

function showFAQ($parent)
{
	global $supportOptions, $is_manager;

	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	HelpdeskUtility::AppendResource('helpdesk.faq.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js');

	// Set title
	HelpdeskUtility::PageTitle('showFAQ');

	if (!$supportConfig->faq_single_page)
	{
		$database->setQuery("SELECT c.name FROM #__support_category as c WHERE c.id=" . $parent);
		$category_name = $database->loadResult();
		$categories_all = CategoriesTree($parent, 1);

		$sql = "SELECT c.id, c.`name`, COUNT(k.id) AS articles,
					   CONCAT('index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" .
					   $id_workgroup . "&task=kb_faq&parent=', c.id) as link
				FROM #__support_category as c
					 LEFT JOIN #__support_kb_category as kc ON c.id=kc.id_category
					 LEFT JOIN #__support_kb as k ON kc.id_kb=k.id AND k.publish='1' AND k.approved='1'
							    AND k.anonymous_access<=" . (!$user->id ? '0' : ($is_support ? '2' : '1')) . "
							    AND k.faq='1'
				WHERE c.`show`='1'
				  AND c.kb=1
				  AND c.id_workgroup='" . $id_workgroup . "'
				  AND c.parent='" . $parent . "'
				GROUP BY c.id, c.`name`
				ORDER BY c.`ordering`, c.`name`";
		$database->setQuery($sql);
		$categories = $database->loadObjectList();

		// Get knowledge base articles of the selected category
		$sql = "SELECT k.id, k.kbcode as code, k.kbtitle as title, k.views, u.name as author, k.date_created,
					   k.date_updated, k.content
				FROM #__support_category as c, #__support_kb as k, #__support_kb_category as kc, #__users as u
				WHERE c.`show`='1'
				  AND c.kb=1
				  AND c.id_workgroup='" . $id_workgroup . "'
				  AND c.id=kc.id_category
				  AND kc.id_kb=k.id
				  AND k.publish='1' " .
				  ($supportConfig->kb_approvement && $is_manager < 7 ? "AND k.approved=1" : "") . "
				  AND kc.id_category='" . $parent . "'
				  AND u.id=k.id_user
				  AND k.anonymous_access<=" . (!$user->id ? '0' : ($is_support ? '2' : '1')) . "
				  AND (k.faq='1'" . ($supportConfig->faq_kb_hits ? " OR ((k.faq='0' OR k.faq='1') AND k.views>=" . $supportConfig->faq_kb_nhits . ")" : "") . ")
				  GROUP BY k.id, k.kbcode, k.kbtitle, k.views
				  ORDER BY k.ordering, k.kbtitle DESC";
		$database->setQuery($sql);
		$articles = $database->loadObjectList();

		// If it's in root and there's no articles in root then will get the Top 10 articles by rating
		if ($parent == 0 && count($articles) == 0)
		{
			$titlelist = JText::_('top10voted');
			$titlelisthead = JText::_('main_category');
			$sql = "SELECT k.id, k.kbcode as code, k.kbtitle as title, k.views, u.name as author, k.date_created, k.date_updated, (sum(r.rate)/count(r.id)) as rating, k.content
					FROM #__support_category as c, #__support_kb as k, #__support_kb_category as kc, #__users as u, #__support_rate as r
					WHERE r.source='K'
					  AND r.id_table=k.id
					  AND c.`show`='1'
					  AND c.kb=1
					  AND c.id_workgroup='" . $id_workgroup . "'
					  AND c.id=kc.id_category
					  AND kc.id_kb=k.id
					  AND k.publish='1'
					  AND k.approved='1'
					  AND u.id=k.id_user
					  AND k.anonymous_access<=" . (!$user->id ? '0' : ($is_support ? '2' : '1')) . "
					  AND (k.faq='1'" . ($supportConfig->faq_kb_hits ? " OR ((k.faq='0' OR k.faq='1') AND k.views>=" . $supportConfig->faq_kb_nhits . ")" : "") . ")
					GROUP BY k.id, k.kbcode, k.kbtitle, k.views
					ORDER BY views DESC";
			$database->setQuery($sql);
			$articles = $database->loadObjectList();
			$total = count($articles);
		}
		else
		{
			$total = 0;
		}

		$i = 1;
		foreach ($articles as $key2 => $value2)
		{
			if (is_object($value2))
			{
				foreach ($value2 as $key3 => $value3)
				{
					$articles_rows[$i][$key3] = $value3;
					if ($key3 == 'id')
					{
						$articles_rows[$i]['link'] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_faqview&id=' . $value3);
						$articles_rows[$i]['link_edit'] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_edit&id=' . $value3);
						$articles_rows[$i]['rate_image'] = (HelpdeskForm::GetRate($value3, 'K', 0) ? HelpdeskForm::GetRate($value3, 'K', 1) : JText::_('unrated'));
						$articles_rows[$i]['rate'] = (HelpdeskForm::GetRate($value3, 'K', 0) ? HelpdeskForm::GetRate($value3, 'K', 0) : JText::_('unrated'));
					}
					if ($key3 == 'date_created')
					{
						$articles_rows[$i]['date_created'] = HelpdeskDate::DateOffset($supportConfig->date_short, strtotime($value3));
					}
					if ($key3 == 'date_updated')
					{
						$articles_rows[$i]['date_updated'] = HelpdeskDate::DateOffset($supportConfig->date_short, strtotime($value3));
					}
					if ($key3 == 'title')
					{
						$articles_rows[$i]['title'] = str_replace("\'", "'", str_replace('\"', '"', $value3));
					}
					if ($key3 == 'content')
					{
						$articles_rows[$i]['content'] = str_replace("\'", "'", str_replace('\"', '"', $value3));
					}
				}
			}
			$i++;
		}
	}
	else
	{
		$sql = "SELECT c.id, c.`name`
				FROM #__support_category as c
				WHERE c.`show`=1 AND c.kb=1 AND c.id_workgroup=" . $id_workgroup . "
				GROUP BY c.id, c.`name`
				ORDER BY c.`ordering`, c.`name`";
		$database->setQuery($sql);
		$categories = $database->loadObjectList();
	}

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('faq/faq' . ($supportConfig->faq_single_page ? '_single_page' : ''));
	include $tmplfile;
}

function showKB($parent, $limit, $limitstart)
{
	global $supportOptions, $is_manager;

	$CONFIG = new JConfig();
	$database = JFactory::getDBO();
	$document = JFactory::getDocument();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$page = JRequest::getVar('page', 0, '', 'int');
	$limit = JRequest::getVar('limit', 32, '', 'int');
	$limitstart = ($page * $limit);
	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// Get knowledge base categories
	$sql = "SELECT c.id, c.`name`, COUNT(k.id) AS articles
			FROM #__support_category as c, #__support_kb as k, #__support_kb_category as kc 
			WHERE c.`show`='1'
 			  AND c.kb=1 
			  AND c.id_workgroup='" . $id_workgroup . "'
			  AND c.id=kc.id_category 
			  AND kc.id_kb=k.id 
			  AND k.publish='1' 
			  AND k.approved='1' 
			  AND c.parent='" . $parent . "'
			  AND k.anonymous_access<=" . (!$user->id ? '0' : ($is_support ? '2' : '1')) . "
			GROUP BY c.id, c.`name` 
			ORDER BY c.`ordering`, c.`name`";
	$database->setQuery($sql);
	$categories = $database->loadObjectList();
	$categories_all = CategoriesTree($parent);

	$i = 1;
	foreach ($categories as $key2 => $value2) {
		if (is_object($value2)) {
			foreach ($value2 as $key3 => $value3) {
				$categories_rows[$i][$key3] = $value3;

				if ($key3 == 'id') {
					$categories_rows[$i]['link'] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_list&parent=' . $value3);

					if (!$parent) {
						// Get articles from category
						$sql = "SELECT k.id, k.kbtitle as `title`, 'article' AS type, CONCAT('index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=kb_view&id=', k.id) as link
								FROM #__support_category as c, #__support_kb as k, #__support_kb_category as kc, #__users as u 
								WHERE c.`show`='1' 
								  AND c.kb=1 
								  AND c.id_workgroup='" . $id_workgroup . "'
								  AND c.id=kc.id_category 
								  AND kc.id_kb=k.id 
								  AND k.publish='1' " . ($supportConfig->kb_approvement && $is_manager < 7 ? "AND k.approved=1" : "") . "
								  AND kc.id_category='" . $value3 . "'
								  AND u.id=k.id_user
								  AND k.anonymous_access<=" . (!$user->id ? '0' : ($is_support ? '2' : '1')) . "
								GROUP BY k.id, k.kbcode, k.kbtitle, k.views 
								ORDER BY k.ordering, k.date_updated DESC
								LIMIT 0, 5";
						$database->setQuery($sql);
						$artcategories = $database->loadAssocList();

						// If number of articles is below 5 try to fill with categories
						$subcategories = array();
						if (count($artcategories) < 5) {
							$sql = "SELECT c.id, c.`name` AS title, 'category' AS type, CONCAT('index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=kb_list&parent=', c.id) as link
									FROM #__support_category as c 
										 LEFT JOIN #__support_kb_category as kc ON c.id=kc.id_category 
										 LEFT JOIN #__support_kb as k ON kc.id_kb=k.id AND k.publish='1' AND k.approved='1' AND k.anonymous_access<=" . (!$user->id ? '0' : ($is_support ? '2' : '1')) . "
									WHERE c.`show`='1' 
									  AND c.kb=1 
									  AND c.id_workgroup='" . $id_workgroup . "'
									  AND c.parent='" . $categories_rows[$i]['id'] . "'
									GROUP BY c.id, c.`name`
									ORDER BY c.`ordering`, c.`name`
									LIMIT 0, " . (5 - count($artcategories));
							$database->setQuery($sql);
							$subcategories = $database->loadAssocList();
						}

						if (count($subcategories) && count($subcategories)) {
							$categories_rows[$i]['catarticles'] = array_merge($subcategories, $artcategories);
						} elseif (count($subcategories) && !count($artcategories)) {
							$categories_rows[$i]['catarticles'] = $subcategories;
						} elseif (!count($subcategories) && count($artcategories)) {
							$categories_rows[$i]['catarticles'] = $artcategories;
						}
					}
				}
			}
		}

		$i++;
	}

	$database->setQuery("SELECT c.name FROM #__support_category as c, #__support_kb as k, #__support_kb_category as kc, #__users as u WHERE c.`show`='1' AND c.kb=1 AND c.id_workgroup='" . $id_workgroup . "' AND c.id=kc.id_category AND kc.id_kb=k.id AND k.publish='1' AND kc.id_category='" . $parent . "' AND u.id=k.id_user " . ($user->id > 0 ? '' : "AND k.anonymous_access='1'"));
	$category_name = $database->loadResult();
	$titlelist = $category_name;

	// Set the page title, keywords and metadata
	$document->title = ($category_name!='' ? $category_name : HelpdeskDepartment::GetName($id_workgroup)) . ' - ' . JText::_('knowledge_base');
	$document->description = JString::substr(strip_tags($category_name), 0, 75);

	// Set title
	HelpdeskUtility::PageTitle('showKB', $titlelist);

	// Get knowledge base articles of the selected category
	$sql = "SELECT k.id, k.kbcode as `code`, k.kbtitle as `title`, k.views, u.name as author, k.date_created, k.date_updated, k.id_user, c.name, k.approved
			FROM #__support_category as c, 
				 #__support_kb as k, 
				 #__support_kb_category as kc, 
				 #__users as u 
			WHERE c.`show`='1' 
			  AND c.kb=1 
			  AND c.id_workgroup='" . $id_workgroup . "'
			  AND c.id=kc.id_category 
			  AND kc.id_kb=k.id 
			  AND k.publish='1' " . ($supportConfig->kb_approvement && $is_manager < 7 ? "AND k.approved=1" : "") . "
			  AND kc.id_category='" . $parent . "'
			  AND u.id=k.id_user 
			  AND k.anonymous_access<=" . (!$user->id ? '0' : ($is_support ? '2' : '1')) . "
			ORDER BY k.ordering, k.kbtitle ASC 
			LIMIT " . $limitstart . ", " . $limit;
	$database->setQuery($sql);
	$articles = $database->loadObjectList();

	$sql = "SELECT COUNT(k.id)
			FROM #__support_category as c, 
				 #__support_kb as k, 
				 #__support_kb_category as kc, 
				 #__users as u 
			WHERE c.`show`='1' 
			  AND c.kb=1 
			  AND c.id_workgroup='" . $id_workgroup . "'
			  AND c.id=kc.id_category 
			  AND kc.id_kb=k.id 
			  AND k.publish='1' " .
			  ($supportConfig->kb_approvement && $is_manager < 7 ? "AND k.approved=1" : "") . "
			  AND kc.id_category='" . $parent . "'
			  AND u.id=k.id_user 
			  AND k.anonymous_access<=" . (!$user->id ? '0' : ($is_support ? '2' : '1'));
	$database->setQuery($sql);
	$total = $database->loadResult();

	$i = 1;
	foreach ($articles as $key2 => $value2) {
		if (is_object($value2)) {
			foreach ($value2 as $key3 => $value3) {
				$articles_rows[$i][$key3] = $value3;

				if ($key3 == 'id') {
					$link_edit = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_edit&id=' . $value3);
					$articles_rows[$i]['link'] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_view&id=' . $value3);
					$articles_rows[$i]['rate_image'] = (HelpdeskForm::GetRate($value3, 'K', 0) ? HelpdeskForm::GetRate($value3, 'K', 1) : JText::_('unrated'));
					$articles_rows[$i]['rate'] = (HelpdeskForm::GetRate($value3, 'K', 0) ? HelpdeskForm::GetRate($value3, 'K', 0) : JText::_('unrated'));
				}

				if ($key3 == 'date_created')
					$articles_rows[$i]['date_created'] = HelpdeskDate::DateOffset($supportConfig->date_short, strtotime($value3));

				if ($key3 == 'date_updated')
					$articles_rows[$i]['date_updated'] = HelpdeskDate::DateOffset($supportConfig->date_short, strtotime($value3));

				if ($key3 == 'title')
					$articles_rows[$i]['title'] = str_replace("\'", "'", str_replace('\"', '"', $value3));
			}
		}

		if ($i / 2 == round($i / 2)) {
			$articles_rows[$i]['case'] = 1;
		} else {
			$articles_rows[$i]['case'] = 0;
		}

		$articles_rows[$i]['link_edit'] = ($is_support ? '<a href="' . $link_edit . '"><img src="media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/edit.png" border="0" align="absmiddle" /></a>' : '');

		$i++;
	}

	// Takes care of pagination
	$plink = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_list&parent=' . $parent;
	$pages = ceil($total / $limit);

	// Display toolbar
	HelpdeskToolbar::Create();

	if ($parent) {
		$tmplfile = HelpdeskTemplate::GetFile('kb/page');
		include $tmplfile;
	} else {
		$tmplfile = HelpdeskTemplate::GetFile('kb/frontpage');
		include $tmplfile;
	}
}

function searchKB()
{
	global $supportOptions;

	$database = JFactory::getDBO();
	$document = JFactory::getDocument();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$parent = JRequest::getVar('parent', 0, '', 'int');
	$exact_phrase = JRequest::getVar('exact_phrase', '', '', 'string');
	$one_word = JRequest::getVar('one_word', '', '', 'string');
	$all_words = JRequest::getVar('all_words', '', '', 'string');
	$exclude_words = JRequest::getVar('exclude_words', '', '', 'string');
	$id_category = JRequest::getVar('id_category', 0, '', 'int');
	$from_date = JRequest::getVar('from_date', '', '', 'string');
	$to_date = JRequest::getVar('to_date', '', '', 'string');
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	// Set the page title, keywords and metadata
	$document->title = JText::_('knowledge_base');

	// Set title
	HelpdeskUtility::PageTitle('searchKB');

	// Treat the search variables
	$where = '';
	if ($exact_phrase != '')
	{
		$where .= "AND (k.kbtitle LIKE '%" . $database->escape($exact_phrase) . "%' OR k.content LIKE '%" . $database->escape($exact_phrase) . "%')";
	}
	if ($one_word != '')
	{
		$words = explode(' ', $one_word);
		$where .= "AND (";
		for ($i = 0; $i < count($words); $i++)
		{
			$where .= ($i == 0 ? '' : 'OR ') . "k.kbtitle LIKE '%" . $database->escape($words[$i]) . "%' OR k.content LIKE '%" . $database->escape($words[$i]) . "%'";
		}
		$where .= ")";
	}
	if ($all_words != '')
	{
		$words = explode(' ', $all_words);
		$where .= "AND (";
		for ($i = 0; $i < count($words); $i++)
		{
			$where .= ($i == 0 ? '' : 'AND ') . "k.kbtitle LIKE '%" . $database->escape($words[$i]) . "%' AND k.content LIKE '%" . $database->escape($words[$i]) . "%'";
		}
		$where .= ")";
	}
	if ($exclude_words != '')
	{
		$words = explode(' ', $exclude_words);
		$where .= "AND (";
		for ($i = 0; $i < count($words); $i++)
		{
			$where .= ($i == 0 ? '' : 'OR ') . "k.kbtitle NOT LIKE '%" . $database->escape($words[$i]) . "%' OR k.content NOT LIKE '%" . $database->escape($words[$i]) . "%'";
		}
		$where .= ")";
	}
	if ($from_date != '')
	{
		$where .= " AND (k.date_created >= " . $database->quote($from_date) . ")";
	}
	if ($to_date != '')
	{
		$where .= " AND (k.date_created <= " . $database->quote($to_date) . ")";
	}

	// Get knowledge base articles of the selected category
	$sql = "SELECT DISTINCT(k.id), k.kbcode as code, k.kbtitle as title, k.views, u.name as author, k.date_created, k.date_updated, k.approved, k.content
			FROM #__support_category as c, #__support_kb as k, #__support_kb_category as kc, #__users as u
			WHERE c.`show`='1'
			  AND c.kb=1
			  AND c.id_workgroup='" . $id_workgroup . "'
			  AND c.id=kc.id_category
			  AND kc.id_kb=k.id
			  AND k.publish='1'
			  AND k.approved='1'
			  AND u.id=k.id_user
			  AND k.anonymous_access<=" . (!$user->id ? '0' : ($is_support ? '2' : '1')) . "
			  $where
			GROUP BY k.id, k.kbcode, k.kbtitle, k.views
			ORDER BY k.ordering, k.kbtitle ASC";
	$database->setQuery($sql);
	$articles = $database->loadObjectList();

	$i = 1;
	foreach ($articles as $key2 => $value2) {
		if (is_object($value2)) {
			foreach ($value2 as $key3 => $value3) {
				$articles_rows[$i][$key3] = $value3;

				if ($key3 == 'id') {
					$link_edit = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_edit&id=' . $value3);
					$articles_rows[$i]['link'] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_view&id=' . $value3);
					$articles_rows[$i]['rate_image'] = (HelpdeskForm::GetRate($value3, 'K', 0) ? HelpdeskForm::GetRate($value3, 'K', 1) : JText::_('unrated'));
					$articles_rows[$i]['rate'] = (HelpdeskForm::GetRate($value3, 'K', 0) ? HelpdeskForm::GetRate($value3, 'K', 0) : JText::_('unrated'));
				}

				if ($key3 == 'date_created')
					$articles_rows[$i]['date_created'] = HelpdeskDate::DateOffset($supportConfig->date_short, strtotime($value3));

				if ($key3 == 'date_updated')
					$articles_rows[$i]['date_updated'] = HelpdeskDate::DateOffset($supportConfig->date_short, strtotime($value3));

				if ($key3 == 'title')
					$articles_rows[$i]['title'] = str_replace("\'", "'", str_replace('\"', '"', $value3));

				if ($key3 == 'content')
				{
					$articles_rows[$i]['content'] = SearchHelper::prepareSearchContent($value3, $exact_phrase);
					$searchRegex = '#(';
					$searchRegex .= preg_quote($exact_phrase, '#');
					$searchRegex .= ')#iu';

					$articles_rows[$i]['content'] = preg_replace($searchRegex, '<span class="highlight">\0</span>', $articles_rows[$i]['content']);
				}
			}
		}

		if ($i / 2 == round($i / 2)) {
			$articles_rows[$i]['case'] = 1;
		} else {
			$articles_rows[$i]['case'] = 0;
		}

		$articles_rows[$i]['link_edit'] = ($is_support ? '<a href="' . $link_edit . '"><img src="media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/edit.png" border="0" align="absmiddle" /></a>' : '');

		$i++;
	}

	// Build categories select list
	$lists['category'] = Parent();

	if (!$is_support) {
		$supportOptions->manager = 0;
	}

	ob_start();
	echo JHTML::Calendar($from_date, 'from_date', 'from_date', '%Y-%m-%d', array('class' => 'inputbox', 'style' => 'width:50px;', 'maxlength' => '10'));
	$content_from_date = ob_get_contents();
	ob_end_clean();

	ob_start();
	echo JHTML::Calendar($to_date, 'to_date', 'to_date', '%Y-%m-%d', array('class' => 'inputbox', 'style' => 'width:50px;', 'maxlength' => '10'));
	$content_to_date = ob_get_contents();
	ob_end_clean();

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('kb/search');
	include $tmplfile;
}

function Parent()
{
	global $parent;

	$database = JFactory::getDBO();

	// get a list of the menu items
	$query = "SELECT c.id, c.name, c.name AS title, c.show, w.wkdesc as workgroup, c.parent, c.parent AS parent_id"
		. "\nFROM #__support_category as c, #__support_workgroup as w"
		. "\nWHERE c.id_workgroup=w.id AND c.kb=1"
		. "\n ORDER BY c.parent";
	$database->setQuery($query);
	$mitems = $database->loadObjectList();

	// establish the hierarchy of the menu
	$children = array();
	// first pass - collect children
	foreach ($mitems as $v) {
		$pt = $v->parent_id;
		$list = @$children[$pt] ? $children[$pt] : array();
		array_push($list, $v);
		$children[$pt] = $list;
	}
	// second pass - get an indent list of the items
	$list = JHTML::_('menu.treerecurse', 0, '	 ', array(), $children, 9999, 0, 0);

	// assemble menu items to the array
	$mitems = array();
	$mitems[] = JHTML::_('select.option', '0', JText::_('top'));
	$this_treename = '';
	foreach ($list as $item) {
		if ($this_treename) {
			if (strpos($item->treename, $this_treename) === false) {
				$mitems[] = JHTML::_('select.option', $item->id, $item->treename);
			}
		} else {
			$mitems[] = JHTML::_('select.option', $item->id, $item->treename);
		}
	}
	$parent_cats = JHTML::_('select.genericlist', $mitems, 'parent', 'class="inputbox" size="1"', 'value', 'text', $parent);
	return $parent_cats;
}

function viewKB($id, $print = 0, $faq = 0)
{
	$mainframe = JFactory::getApplication();
	$document = JFactory::getDocument();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	HelpdeskUtility::AppendResource('rating.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js');

	// If it's the print version shows icon to print and to close
	if ($print) {
		$img_src = JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/';
		echo '<style type="text/css" media="print">';
		echo '.exclude {';
		echo '	visibility: hidden;';
		echo '  display: none;';
		echo '}';
		echo '</style>';
		echo '<div align="right" class="exclude">';
		echo '<img src="' . $img_src . '16px/print.png" border="0" onClick="javascript:window.print();" style="cursor: pointer;" title="' . JText::_('print') . '" />';
		echo '&nbsp;';
		echo '<img src="' . $img_src . '16px/close.png" border="0" onClick="javascript:window.close();" style="cursor: pointer;" title="' . JText::_('close') . '" />';
		echo '</div>';
	}

	// Updates the number of views of the article
	if (!$is_support && !$print) {
		$database->setQuery("UPDATE #__support_kb SET views=(views+1) WHERE id='" . $id . "'");
		$database->query();
	}

	// Get article
	$sql = "SELECT k.id, k.kbcode, k.kbtitle, k.content AS text, k.keywords, k.views, k.date_created, k.date_updated, u.name
			FROM #__support_kb as k, 
				 #__users as u 
			WHERE u.id=k.id_user 
			  AND k.publish='1' 
			  AND k.id=" . $id;
	$database->setQuery($sql);
	$article = null;
	$article = $database->loadObject();
	$article->link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_view&id=' . $id;
	$article->text = str_replace('\"', '"', $article->text);
	$article->text = str_replace("\'", "'", $article->text);
	$article->kbtitle = str_replace("\'", "'", $article->kbtitle);
	$article->kbtitle = str_replace('\"', '"', $article->kbtitle);

	// Sets the page title
	HelpdeskUtility::PageTitle($faq ? 'viewFAQ' : 'viewKB', $article->kbtitle);

	// Get article attachments
	$database->setQuery("SELECT id, id_file, filename, description FROM #__support_file WHERE id='" . $article->id . "' AND source='K' AND public='1'");
	$attachs = $database->loadObjectList();

	$i = 1;
	foreach ($attachs as $key2 => $value2) {
		$tmpid = 0;
		$tmpid_file = 0;

		if (is_object($value2)) {
			foreach ($value2 as $key3 => $value3) {
				$attachs_rows[$i][$key3] = $value3;

				if ($key3 == 'id')
					$tmpid = $value3;

				if ($key3 == 'id_file')
					$tmpid_file = $value3;
			}
		}

		$link = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_download&id=' . $tmpid_file . '&extid=' . $tmpid);
		$attachs_rows[$i]['link'] = $link;

		$i++;
	}

	// Get article comments
	$database->setQuery("SELECT c.id, c.id_user, c.`comment`, c.`date`, u.avatar FROM #__support_kb_comment AS c LEFT JOIN #__support_users AS u ON u.id_user=c.id_user WHERE publish='1' AND id_kb='" . $article->id . "'");
	$comments = $database->loadObjectList();

	$i = 1;
	foreach ($comments as $key2 => $value2) {
		if (is_object($value2)) {
			foreach ($value2 as $key3 => $value3) {
				$comments_rows[$i][$key3] = $value3;
				if ($key3 == 'id_user')
					$comments_rows[$i]['user'] = ($value3 == 0 ? JText::_('anonymous') : HelpdeskUser::GetName($value3));
				if ($key3 == 'date')
					$comments_rows[$i]['date'] = HelpdeskDate::DateOffset($supportConfig->date_short, strtotime($value3));
				if ($key3 == 'comment')
					$comments_rows[$i]['comment'] = nl2br($value3);
				if ($key3 == 'avatar') {
					$comments_rows[$i]['avatar'] = HelpdeskUser::GetAvatar($comments_rows[$i]['id_user']);
				}
			}
		}

		$i++;
	}

	// Get related articles
	$related = null;
	if ($article->keywords != '') {
		$words = explode(',', $article->keywords);
		$where = '';

		for ($i = 0; $i < count($words); $i++)
		{
			$where .= ($i == 0 ? '' : ' OR ') . "keywords LIKE '%" . $database->escape($words[$i]) . "%'";
		}

		$database->setQuery("SELECT id, kbtitle AS article FROM #__support_kb WHERE ($where) AND id!='" . $article->id . "'");
		$related = $database->loadObjectList();

		$i = 1;
		foreach ($related as $key2 => $value2) {
			if (is_object($value2)) {
				foreach ($value2 as $key3 => $value3) {
					$related_rows[$i][$key3] = $value3;

					if ($key3 == 'id')
						$link = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_view&id=' . $value3);
					$related_rows[$i]['link'] = $link;
				}
			}

			$i++;
		}
	}

	// Set the page title, keywords and metadata
	$document->title = $article->kbtitle . ' - ' . JText::_('knowledge_base');
	$document->description = JString::substr(strip_tags($article->text), 0, 75);

	// Get user name if any
	if ($user->id > 0)
	{
		$database->setQuery("SELECT name FROM #__users as u WHERE id='" . $user->id . "'");
		$user_name = $database->loadResult();
	} else {
		$user_name = JText::_('anonymous');
	}

	$tmplfile = ($print ? 'print' : 'article');

	$dispatcher =& JDispatcher::getInstance();
	JPluginHelper::importPlugin('maqmahelpdesk');
	$dispatcher->trigger('onBeforeDisplayContent', array(& $article));
	$article->text = JHTML::_('content.prepare', $article->text);

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('kb/' . $tmplfile);
	include $tmplfile;
}

function commentKB($id, $comment, $faq)
{
	$CONFIG = new JConfig();
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$sql = "INSERT INTO `#__support_kb_comment`(`id_user`, `id_kb`, `date`, `comment`, `publish`, `id_workgroup`, `itemid`)
			VALUES('" . $user->id . "', '" . $id . "', '" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M") . "', " . $database->quote($comment) . ", '" . ($supportConfig->kb_moderate ? 0 : 1) . "', '" . $id_workgroup . "', '" . $Itemid . "')";
	$database->setQuery($sql);
	$database->query();
	$msg = JText::_('comment_insert');

	// If not moderated and JomSocial post in wall is enabled
	if (!$supportConfig->kb_moderate && $supportConfig->post_comments_in_wall)
	{
		$sql = "SELECT c.`id_user`, c.`id_kb`, c.`comment`, k.`kbtitle`
				FROM `#__support_kb_comment` AS c
					 INNER JOIN `#__support_kb` AS k ON k.`id`=c.`id_kb`
				WHERE c.`id`='" . $database->insertid() . "'";
		$database->setQuery($sql);
		$row = $database->loadObject();

		$comment = sprintf(JText::_("comment_post_wall"), JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" .
			$Itemid . "&task=kb_view&id_workgroup=" . $id_workgroup . "&id=" . $row->id_kb),
			$row->kbtitle, $row->kbtitle
		);
		HelpdeskJomSocial::Post($row->id_user, $comment, $row->comment);
	}

	// If comments are moderated inform administrator
	if ($supportConfig->kb_moderate)
	{
		// Get the template for the page
		$htmlcode = HelpdeskTemplate::Get('', $id_workgroup, 'mail/kb_new_comment');

		// Get article title
		$database->setQuery("SELECT kbtitle FROM #__support_kb WHERE id='" . $id . "'");
		$kbtitle = $database->loadResult();

		// Replaces message body variables for the values
		$msginfo = str_replace("%kb_title", $kbtitle, $htmlcode); // Replaces the %message
		$msginfo = str_replace("%kb_comment", $comment, $msginfo); // Replaces the %email
		$msginfo = str_replace("%url", JURI::root(), $msginfo); // Replaces the %url

		JUtility::sendMail(($workgroupSettings->wkadmin_email != '' ? $workgroupSettings->wkadmin_email : $CONFIG->mailfrom), ($workgroupSettings->wkmail_address_name != '' ? $workgroupSettings->wkmail_address_name : $CONFIG->sitename) . " <" . ($workgroupSettings->wkadmin_email != '' ? $workgroupSettings->wkadmin_email : $CONFIG->mailfrom) . ">", ($workgroupSettings->wkadmin_email != '' ? $workgroupSettings->wkadmin_email : $CONFIG->mailfrom), JText::_('tkt_new_comment_subj'), $msginfo, 1);
	}

	if ($msg != '')
		HelpdeskUtility::AddGlobalMessage($msg, 'i');

	if ($faq) {
		$mainframe->redirect('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_faqview&id=' . $id);
	} else {
		$mainframe->redirect('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_view&id=' . $id);
	}
}

function saveKB()
{
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$msg = '';
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getVar('id_workgroup', 0, 'POST', 'int');
	$id = JRequest::getInt('id', 0);
	$code = JRequest::getVar('code', '', 'POST', 'string');
	$title = JRequest::getVar('title', '', 'POST', 'string');
	$content = JRequest::getVar('kbcontent', '', 'POST', 'string', 2);
	$keywords = JRequest::getVar('keywords', '', 'POST', 'string');
	$categories = JRequest::getVar('categories', '', 'POST', 'string');
	$approved = JRequest::getInt('approved', 0);
	$publish = ($approved ? JRequest::getInt('show', 1) : 0);
	$anonymous_access = JRequest::getInt('anonymous_access', 0);
	$faq = JRequest::getInt('faq', 1);
	$date = HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S");
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$msg = "";

	// Insert or update article
	if ($id == 0)
	{
		$sql = "INSERT INTO #__support_kb(id_user, kbcode, kbtitle, content, keywords, publish, views, date_created, date_updated, anonymous_access, faq, approved)
				VALUES('" . $user->id . "', " . $database->quote($code) . ", " . $database->quote($title) . ", " . $database->quote($content) . ", " . $database->quote($keywords) . ", '" . $publish . "', '0', '" . $date . "', '" . $date . "', '" . $anonymous_access . "', '" . $faq . "', '" . $approved . "')";
		$database->setQuery($sql);
		$database->query();
		$id = $database->insertid();
		$msg .= JText::_('article_created');
	}
	else
	{
		$sql = "UPDATE #__support_kb
				SET kbcode=" . $database->quote($code) . ",
					kbtitle=" . $database->quote($title) . ",
					content=" . $database->quote($content) . ",
					keywords=" . $database->quote($keywords) . ",
					publish='" . $publish . "',
					date_updated='" . date("Y-m-d H:i:s") . "',
					anonymous_access='" . $anonymous_access . "',
					faq='" . $faq . "',
					approved='" . $approved . "'
				WHERE id='" . $id . "'";
		$database->setQuery($sql);
		$database->query();
		$msg .= JText::_('article_updated');
	}

	// Take care of the JomSocial integration if enabled
	if ($supportConfig->post_kb_creation_in_wall)
	{
		// Get privacy param
		$sql = "SELECT `params`
				FROM `#__community_users`
				WHERE `userid`='" . $user->id . "'";
		$database->setQuery($sql);
		$userParams = $database->loadResult();
		$params = new JParameter($userParams);

		if ($id)
		{
			$comment = sprintf(JText::_("comment_kb_creation_wall_update"), JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&task=kb_view&id_workgroup=" . $id_workgroup . "&id=" . $id), $title, $title);
		}
		else
		{
			$comment = sprintf(JText::_("comment_kb_creation_wall_create"), JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&task=kb_view&id_workgroup=" . $id_workgroup . "&id=" . $id), $title, $title);
		}

		HelpdeskJomSocial::Post($user->id, $comment, strip_tags(JString::substr($content, 0, 100)));
	}

	// Take care of attachments
	for ($xx = 0; $xx < $supportConfig->attachs_num; $xx++)
	{
		$msg .= HelpdeskFile::Upload($id, 'K', "file", $supportConfig->docspath . '/', $_POST['desc' . $xx]);
	}

	// Take care of categories
	if ($categories != '')
	{
		$database->setQuery("DELETE FROM #__support_kb_category WHERE id_kb='" . $id . "'");
		$database->query();

		$category = explode(",", $categories);
		for ($i = 0; $i < count($category); $i++)
		{
			if ($category[$i] > 0) {
				$database->setQuery("INSERT INTO #__support_kb_category(id_category, id_kb) VALUES('" . $category[$i] . "', '" . $id . "')");
				$database->query();
			}
		}
	}

	if ($msg != '')
		HelpdeskUtility::AddGlobalMessage($msg, 'i');

	$mainframe->redirect('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_list&id=' . $id);
}

function editKB($id, $ticket = 0)
{
	global $is_manager;

	$database = JFactory::getDBO();
	$document = JFactory::getDocument();
	$editor = JFactory::getEditor();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	
	$document->addScriptDeclaration('var MQM_KB_CODE = "'.addslashes(JText::_('msg_validate_code')).'";');
	$document->addScriptDeclaration('var MQM_KB_TITLE = "'.addslashes(JText::_('msg_validate_title')).'";');
	$document->addScriptDeclaration('var MQM_KB_CATEGORY = "'.addslashes(JText::_('msg_validate_category')).'";');
	$document->addScriptDeclaration('function CheckHTMLEditor() { '.$editor->save('kbcontent').' }');
	HelpdeskUtility::AppendResource('helpdesk.kb.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	
	// Get article
	if ($id > 0) {
		$sql = "SELECT k.id, k.kbcode as `code`, k.kbtitle as `title`, k.content, k.keywords, k.views, k.date_created, k.date_updated, u.name as author, k.publish, k.anonymous_access, k.faq, k.approved
				FROM #__support_kb AS k
					 INNER JOIN #__users AS u ON u.id=k.id_user  
				WHERE k.publish=1 
				  AND k.id=" . (int)$id;
		$database->setQuery($sql);
		$article = null;
		$article = $database->loadObject();
		$article->content = str_replace('\"', '"', $article->content);
		$article->content = str_replace("\'", "'", $article->content);
		$article->title = str_replace("\'", "'", $article->title);
		$article->title = str_replace('\"', '"', $article->title);
	} else {
		$article = new stdClass();
		$article->id = 0;
		$article->code = '';
		$article->title = '';
		$article->content = '';
		$article->keywords = '';
		$article->views = 0;
		$article->date_created = '';
		$article->publish = 1;
		$article->anonymous_access = 0;
		$article->faq = 0;
		$article->approved = 1;
	}

	$accesslist[] = JHTML::_('select.option', '0', JText::_('everybody'));
	$accesslist[] = JHTML::_('select.option', '1', JText::_('registered_users'));
	$accesslist[] = JHTML::_('select.option', '2', JText::_('support_agents_only'));
	$lists['anonymous'] = JHTML::_('select.genericlist', $accesslist, 'anonymous_access', 'class="inputbox" size="1"', 'value', 'text', $article->anonymous_access);

	// If it's a ticket conversion
	if ($ticket) {
		// Fill KB fields from the ticket
		$sql = "SELECT 0 as id, ticketmask as code, subject as title, message as content, 0 as views, now() as date_created, now() as date_updated, 1 as publish, '' as keywords, '' as resolution, 0 as anonymous_access, 0 as faq
				FROM #__support_ticket 
				WHERE id=" . (int)$ticket;
		$database->setQuery($sql);
		$article = null;
		$article = $database->loadObject();

		// Get the ticket messages to fill in the content
		$sql = "SELECT message
				FROM #__support_ticket_resp 
				WHERE id_ticket=" . (int)$ticket . "
				ORDER BY id";
		$database->setQuery($sql);
		$replies = $database->loadObjectList();
		for ($i = 0; $i < count($replies); $i++) {
			$rowReply = $replies[$i];
			$article->content .= "<hr /><p>" . $rowReply->message . "</p>";
		}
	}

	// Sets the page title
	if ($id) {
		HelpdeskUtility::PageTitle('editKB', $article->title);
	} else {
		HelpdeskUtility::PageTitle('newKB');
	}

	if ($id > 0) {
		$database->setQuery("SELECT id_category FROM #__support_kb_category WHERE id_kb=" . (int)$article->id);
		$categories = $database->loadObjectList();
		$category_list = '';
		for ($i = 0; $i < count($categories); $i++) {
			$category_list .= $categories[$i]->id_category . ',';
		}
		$category_list = JString::substr($category_list, 0, strlen($category_list) - 1);
	} elseif ($ticket > 0) {
		$database->setQuery("SELECT id_category FROM #__support_ticket WHERE id=" . (int)$ticket);
		$categories = $database->loadObjectList();
		$category_list = '';
		for ($i = 0; $i < count($categories); $i++) {
			$category_list .= $categories[$i]->id_category . ',';
		}
		$category_list = JString::substr($category_list, 0, strlen($category_list) - 1);
	} else {
		$category_list = '';
		$categories = '';
	}
	$document->addScriptDeclaration( 'var MQM_KB_CAT_LIST = "'.$category_list.'";' );

	// Set fields
	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('kb/form');
	include $tmplfile;
}

function CategoriesTree($parent, $faq = 0)
{
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$database = JFactory::getDBO();
	$user = JFactory::getUser();

	$cattree = '';

	// get a list of the menu items
	$query = "SELECT c.id, c.name, c.name AS title, c.show, w.wkdesc as workgroup, c.parent, c.parent AS parent_id"
		. "\nFROM #__support_category as c, #__support_workgroup as w"
		. "\nWHERE c.id_workgroup=w.id AND w.id='" . $id_workgroup . "' AND c.`show`='1' AND c.kb=1"
		. "\n ORDER BY c.parent";
	$database->setQuery($query);
	$mitems = $database->loadObjectList();

	if (count($mitems) > 0) {
		// establish the hierarchy of the menu
		$children = array();
		// first pass - collect children
		foreach ($mitems as $v) {
			$pt = $v->parent_id;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;
		}
		// second pass - get an indent list of the items
		$list = JHTML::_('menu.treerecurse', 0, '	 ', array(), $children, 9999, 0, 0);

		// assemble menu items to the array
		//$this_treename = '';
		$i = 0;
		$cattree .= '<table cellspacing="0" cellpadding="0">';
		$cattree .= '<tr>';
		foreach ($list as $item) {
			$i++;
			if ($i == 16) {
				$cattree .= '</td>';
				$i = 1;
			}
			if ($i == 1) {
				$cattree .= '<td width="200" valign="top">';
			}

			$sql = "SELECT COUNT(k.id) AS articles
					FROM #__support_category as c, #__support_kb as k, #__support_kb_category as kc
					WHERE c.`show`='1'
					  AND c.kb=1
					  AND c.id_workgroup='" . $id_workgroup . "'
					  AND c.id=kc.id_category
					  AND kc.id_kb=k.id
					  AND k.publish='1'
					  AND kc.id_category='" . $item->id . "' " .
					  ($user->id > 0 ? '' : "AND k.anonymous_access='1'") .
					  ($faq ? " AND k.faq='1'" : '');
			$database->setQuery($sql);

			$cattree .= '<a href="index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' .
				$id_workgroup . '&task=kb_' . ($faq ? 'faq' : 'list') . '&parent=' . $item->id . '"' .
				($parent == $item->id ? ' class="active_category"' : '') . '>' . $item->treename . '</a>' .
				($database->loadResult() > 0 ? ' (' . $database->loadResult() . ')' : '') . "<br>\n";
		}
		if ($i < 15) {
			$cattree .= '</td>';
		}
		$cattree .= '</tr>';
		$cattree .= '</table>';
	} else {
		$cattree .= JText::_('no_category_in_workgroup');
	}

	return $cattree;
}
