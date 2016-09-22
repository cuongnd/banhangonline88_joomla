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

$id = JRequest::getVar('id', 0, '', 'int');
$limit = intval(JRequest::getVar('limit', '', '', 'string'));
$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

// Activities logger
HelpdeskUtility::ActivityLog('site', 'announce', $task, $id);

switch ($task)
{
	case "list":
		HelpdeskValidation::ValidPermissions($task, 'A') ? showAnnounces($limit, $limitstart) : HelpdeskValidation::NoAccessQuit();
		break;

	case "view":
		HelpdeskValidation::ValidPermissions($task, 'A') ? showAnnounce($id, 0) : HelpdeskValidation::NoAccessQuit();
		break;

	case "print":
		HelpdeskValidation::ValidPermissions($task, 'A') ? showAnnounce($id, 1) : HelpdeskValidation::NoAccessQuit();
		break;
}

function showAnnounces($limit, $limitstart)
{
	global $supportOptions;

	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$CONFIG = new JConfig();
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();

	// Sets the title
	HelpdeskUtility::PageTitle('showAnnounces');

	// Get knowledge base articles
	$limit = $limit ? $limit : $CONFIG->list_limit;
	$database->setQuery("SELECT id, `date`, introtext AS intro, bodytext AS body, frontpage, urgent as `type`, sent, id_workgroup FROM #__support_announce WHERE (id_workgroup='" . $id_workgroup . "' OR id_workgroup='0') " . ($is_support ? "" : "AND (id_client='0' OR id_client='" . $is_client . "')") . " ORDER BY date DESC LIMIT " . $limitstart . ", " . $limit);
	$rows = $database->loadObjectList();

	$database->setQuery("SELECT COUNT(*) FROM #__support_announce WHERE (id_workgroup='" . $id_workgroup . "' OR id_workgroup='0') " . ($is_support ? "" : "AND (id_client='0' OR id_client='" . $is_client . "')"));
	$total = $database->loadResult();

	if ($total <= $limit) $limitstart = 0;
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	// Takes care of pagination
	$link = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=announce_list');
	$pagelinks = $pageNav->getPagesLinks($link);
	$pagecounter = $pageNav->getPagesCounter();

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('announcements/list');
	include $tmplfile;
}

function showAnnounce($id, $print)
{
	global $supportOptions;

	$mainframe = JFactory::getApplication();
	$document = JFactory::getDocument();
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);

	// If it's the print version shows icon to print and to close
	if ($print)
	{
		$img_src = JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/';
		echo '<style type="text/css" media="' . JText::_('print') . '">';
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

	// Get announcements
	$database->setQuery("SELECT id, `date`, introtext, bodytext, frontpage, urgent, sent, id_workgroup FROM #__support_announce WHERE id='" . $id . "'");
	$announce = null;
	$announce = $database->loadObject();
	$announce->bodytext = str_replace('\"', '"', $announce->bodytext);

	// Set title
	HelpdeskUtility::PageTitle('viewAnnouncements', $announce->introtext);

	// Set the page title, keywords and metadata
	$document->title = $announce->introtext . ' - ' . JText::_('ANNOUNCEMENTS');
	$document->description = JString::substr(strip_tags($announce->bodytext), 0, 75);

	$ok_img = '<img src="media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/ok.png" border="0" align="absmiddle" />';
	$no_img = '<img src="media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/no.png" border="0" align="absmiddle" />';
	$link = JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=announce_print&id=' . $announce->id . '&tmpl=component');

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('announcements/' . ($print == 1 ? 'print' : 'record'));
	include $tmplfile;
}
