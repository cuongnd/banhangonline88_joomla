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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/glossary.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/glossary.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/glossary/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/glossary/tmpl/edit.php";

// Set toolbar and page title
HelpdeskGlossaryAdminHelper::addToolbar($task);
HelpdeskGlossaryAdminHelper::setDocument();

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'glossary', $task, $cid[0]);

switch ($task) {
	case "new":
		editGlossary(0);
		break;

	case "edit":
		editGlossary($cid[0]);
		break;

	case "save":
		saveGlossary();
		break;

	case "remove":
		removeGlossary($cid);
		break;

	case "publish":
		publishGlossary($cid, 1);
		break;

	case "unpublish":
		publishGlossary($cid, 0);
		break;

	default:
		showGlossary();
		break;
}

function showGlossary()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_glossary");
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$database->setQuery("SELECT g.id, g.term, g.description, g.published, g.anonymous_access, c.name AS category FROM #__support_glossary AS g LEFT JOIN #__support_category AS c ON c.id=g.id_category ORDER BY g.term",
		$pageNav->limitstart, $pageNav->limit
	);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	MaQmaHtmlDefault::display($rows, $pageNav);
}

function editGlossary($uid = 0)
{
	$database = JFactory::getDBO();
	$row = new MaQmaHelpdeskTableGlossary($database);
	// load the row from the db table
	$row->load($uid);

	$captions = array(JText::_('MQ_NO'), JText::_('MQ_YES'));
	$values = array('0', '1');

	$lists['published'] = HelpdeskForm::SwitchCheckbox('radio', 'published', $captions, $values, $row->published, 'switch');
	$lists['anonymous'] = HelpdeskForm::SwitchCheckbox('radio', 'anonymous_access', $captions, $values, $row->anonymous_access, 'switch');
	$lists['id_category'] = HelpdeskForm::BuildCategories($row->id_category, false, false, false, false, false, true);

	MaQmaHtmlEdit::display($row, $lists);
}

function saveGlossary()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');
	$id = JRequest::getVar('id', 0, 'POST', 'int');
	$id_workgroup = JRequest::getVar('id_workgroup', 0, 'POST', 'int');
	$id_category = JRequest::getVar('id_category', 0, 'POST', 'int');
	$term = JRequest::getVar('term', '', 'POST', 'string');
	$description = JRequest::getVar('description', '', 'POST', 'string', 2);
	$publish = JRequest::getVar('publish', 1, 'POST', 'int');
	$anonymous_access = JRequest::getVar('anonymous_access', 1, 'POST', 'int');

	if (!$id)
	{
		$sql = "INSERT INTO #__support_glossary(term, description, published, anonymous_access, id_category)
				VALUES(" . $database->quote($term) . ", " . $database->quote($description) . ", '" . $publish . "', '" . $anonymous_access . "', '" . $id_category . "')";
		$database->setQuery($sql);
		$database->query();
		$id = $database->insertid();
	}
	elseif ($id)
	{
		$sql = "UPDATE #__support_glossary
				SET term=" . $database->quote($term) . ", description=" . $database->quote($description) . ", published='" . $publish . "', anonymous_access='" . $anonymous_access . "', id_category='" . $id_category . "'
				WHERE id='" . $id . "'";
		$database->setQuery($sql);
		$database->query();
	}

	$search = '\"';
	$replace = '"';
	$sql = "UPDATE `#__support_glossary`
			SET `description`=REPLACE(`description`, '" . $search . "', '" . $replace . "')
			WHERE `id`=" . $id;
	$database->setQuery($sql);
	$database->query();

	$search = '\&quot;';
	$replace = '"';
	$sql = "UPDATE `#__support_glossary`
			SET `description`=REPLACE(`description`, '" . $search . "', '" . $replace . "')
			WHERE `id`=" . $id;
	$database->setQuery($sql);
	$database->query();

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=glossary");
}

function removeGlossary($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1)
	{
		echo "<script type='text/javascript'> alert('" . JText::_('glossary_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid))
	{
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_glossary WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=glossary");
}

function publishGlossary($cid = null, $publish = 1)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!is_array($cid) || count($cid) < 1)
	{
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script type='text/javascript'> alert('" . JText::_('glossary_action') . " " . $action . "'); window.history.go(-1);</script>\n";
		exit;
	}

	$cids = implode(',', $cid);
	$count = count($cid);

	$database->setQuery("UPDATE #__support_glossary SET published='$publish' WHERE id IN (" . $cids . ")");

	if (!$database->query())
	{
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=glossary");
}
