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

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/glossary.php';

$id = JRequest::getInt('id', 0);

// Activities logger
HelpdeskUtility::ActivityLog('site', 'glossary', $task, $id);

switch ($task)
{
	case "add":
		HelpdeskValidation::ValidPermissions($task, 'G') ? editGlossary(0) : HelpdeskValidation::NoAccessQuit();
		break;

	case "edit":
		HelpdeskValidation::ValidPermissions($task, 'G') ? editGlossary($id) : HelpdeskValidation::NoAccessQuit();
		break;

	case "save":
		HelpdeskValidation::ValidPermissions($task, 'G') ? saveGlossary() : HelpdeskValidation::NoAccessQuit();
		break;

	case "category":
		HelpdeskValidation::ValidPermissions($task, 'G') ? showGlossaryCategory() : HelpdeskValidation::NoAccessQuit();
		break;

	default:
		HelpdeskValidation::ValidPermissions($task, 'G') ? showGlossary() : HelpdeskValidation::NoAccessQuit();
		break;
}

function showGlossary()
{
	HelpdeskUtility::AppendResource('helpdesk.glossary.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js');

	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$id_category = JRequest::getInt('id_category', 0);

	// Sets the page title
	HelpdeskUtility::PageTitle('Glossary');

	// Get glossary categories
	$sql = "SELECT c.`id`, c.`name`, COUNT(g.`id`) AS total
			FROM `#__support_category` AS c
				 INNER JOIN `#__support_glossary` AS g ON g.`id_category`=c.`id`
			WHERE c.`show`=1
			  AND c.`glossary`=1
			  AND g.`published`=1
			  AND c.`id_workgroup`=$id_workgroup
			GROUP BY c.`id`, c.`name`
			ORDER BY c.`name`";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	// If only one exists redirect to it automatically
	if (count($rows) == 1)
	{
		$link = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=glossary_category&id_category=' . $rows[0]->id);
		$mainframe->redirect($link);
	}

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('glossary/glossary');
	include $tmplfile;
}

function showGlossaryCategory()
{
	HelpdeskUtility::AppendResource('helpdesk.glossary.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js');

	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$id_category = JRequest::getVar('id_category', 0, '', 'int');

	// Sets the page title
	HelpdeskUtility::PageTitle('Glossary');

	// Get glossary terms
	$sql = "SELECT id, term as title, description as term
			FROM #__support_glossary 
			WHERE published='1' AND id_category=" . $id_category . " " . ($user->id > 0 ? '' : "AND anonymous_access='1'") . "
			ORDER BY UCASE(term)";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	$link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=glossary';

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('glossary/category');
	include $tmplfile;
}

function saveGlossary()
{
	$Itemid = JRequest::getInt('Itemid', 0);
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$is_support = HelpdeskUser::IsSupport();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$msg = '';
	$id = JRequest::getVar('id', 0, 'POST', 'int');
	$id_workgroup = JRequest::getVar('id_workgroup', 0, 'POST', 'int');
	$id_category = JRequest::getVar('id_category', 0, 'POST', 'int');
	$term = JRequest::getVar('term', '', 'POST', 'string');
	$description = JRequest::getVar('description', '', 'POST', 'string', 2);
	$publish = JRequest::getVar('publish', 1, 'POST', 'int');
	$anonymous_access = JRequest::getVar('anonymous_access', 1, 'POST', 'int');

	// Insert or update term
	if (!$id)
	{
		$sql = "INSERT INTO #__support_glossary(term, description, published, anonymous_access, id_category)
				VALUES(" . $database->quote($term) . ", " . $database->quote($description) . ", '" . $publish . "', '" . $anonymous_access . "', '" . $id_category . "')";
		$database->setQuery($sql);
		$database->query();
		$msg = JText::_('glossary_created');
	}
	elseif ($id > 0 && $is_support)
	{
		$sql = "UPDATE #__support_glossary
				SET term=" . $database->quote($term) . ", description=" . $database->quote($description) . ", published='" . $publish . "', anonymous_access='" . $anonymous_access . "', id_category='" . $id_category . "'
				WHERE id='" . $id . "'";
		$database->setQuery($sql);
		$database->query();
		$msg = JText::_('glossary_updated');
	}

	if ($msg != '')
	{
		HelpdeskUtility::AddGlobalMessage($msg, 'i');
	}

	$mainframe->redirect('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=glossary');
}

function editGlossary($id)
{
	$database = JFactory::getDBO();
	$document = JFactory::getDocument();
	$editor = JFactory::getEditor();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);

	$document->addScriptDeclaration('var MQM_GLOSSARY_TERM = "'.addslashes(JText::_('required_term')).'";');
	$document->addScriptDeclaration('var MQM_GLOSSARY_CANCEL = "'.addslashes(JText::_('tmpl_ticket_cancelquestion')).'";');
	$document->addScriptDeclaration('function CheckHTMLEditor() { '.$editor->save('description').' }');
	HelpdeskUtility::AppendResource('helpdesk.glossary.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js');

	// Sets the page title
	if ($id > 0) {
		HelpdeskUtility::PageTitle('editGlossary');
	} else {
		HelpdeskUtility::PageTitle('newGlossary');
	}

	// Get Glossary Term
	$row = new MaQmaHelpdeskTableGlossary($database);
	$row->load($id);

	// Set fields
	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');
	$lists['show'] = HelpdeskForm::SwitchCheckbox('radio', 'published', $captions, $values, $row->published, 'switch');
	$lists['anonymous'] = HelpdeskForm::SwitchCheckbox('radio', 'anonymous_access', $captions, $values, $row->anonymous_access, 'switch');
	$lists['category'] = HelpdeskForm::BuildCategories($row->id_category, false, true, false, false, false, true);

	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('glossary/form');
	include $tmplfile;
}
