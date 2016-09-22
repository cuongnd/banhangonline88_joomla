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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/custom_field.php';

// Include tables
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/tables/custom_field.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/customfield/tmpl/default.php";
require_once "components/com_maqmahelpdesk/views/customfield/tmpl/edit.php";

// Set toolbar and page title
HelpdeskCustomFieldAdminHelper::addToolbar($task);
HelpdeskCustomFieldAdminHelper::setDocument();

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');
if (!is_array($cid)) {
	$cid = array(0);
}

$ftype = JRequest::getVar('ftype', '', '', 'string');

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'customfield', $task, $cid[0]);

switch ($task) {
	case "new":
		editCustomField(0);
		break;

	case "edit":
		editCustomField($cid[0]);
		break;

	case "save":
		saveCustomField();
		break;

	case "remove":
		removeCustomField($cid);
		break;

	case "publish":
		publishCustomField($cid, 1);
		break;

	case "unpublish":
		publishCustomField($cid, 0);
		break;

	default:
		showCustomField($ftype);
		break;
}

/**
 * Compiles a list of workgroups
 * @param string The component name
 */
function showCustomField($ftype)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$limit = $mainframe->getUserStateFromRequest("global.list.limit", 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = intval(JRequest::getVar('limitstart', 0, '', 'int'));

	$where = '';
	if ($ftype != '') {
		$where = " WHERE cftype=" . $database->quote($ftype);
	}

	// get the total number of records
	$database->setQuery("SELECT count(*) FROM #__support_custom_fields" . $where);
	$total = $database->loadResult();
	echo $database->getErrorMsg();

	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	$database->setQuery("SELECT id, caption, ftype, value, cftype"
			. "\nFROM #__support_custom_fields"
			. "\n" . $where
			. "\nORDER BY caption",
		$pageNav->limitstart, $pageNav->limit
	);

	$rows = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}

	// Build the field type select list
	$cftypelist[] = JHTML::_('select.option', '', JText::_('all'));
	$cftypelist[] = JHTML::_('select.option', 'W', JText::_('wk_field'));
	$cftypelist[] = JHTML::_('select.option', 'U', JText::_('users_field'));
	$cftypelist[] = JHTML::_('select.option', 'C', JText::_('contract_field'));
	$cftypelist[] = JHTML::_('select.option', 'D', JText::_('downloads') . ' - ' . JText::_('CLIENT_ACCESS'));
	$cftypelist[] = JHTML::_('select.option', 'L', JText::_('WK_CLIENTS'));
	$lists['cftype'] = JHTML::_('select.genericlist', $cftypelist, 'ftype', 'class="inputbox" size="1" onChange="document.adminForm.submit();"', 'value', 'text', $ftype);

	MaQmaHtmlDefault::display($rows, $pageNav, $lists);
}

/**
 * Compiles information to add or edit a custom field
 * @param database A database connector object
 * @param string The component name
 */
function editCustomField($uid = 0)
{
	global $lists;

	$database = JFactory::getDBO();
	HelpdeskUtility::AppendResource('autogrowtextarea.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	$row = new MaQmaHelpdeskTableCustomField($database);
	$row->load($uid);

	$row->value = str_replace('\"', '"', $row->value);
	$row->value = str_replace("\'", "'", $row->value);

	// Build the field type select list
	$cftypelist[] = JHTML::_('select.option', '', JText::_('selectlist'));
	$cftypelist[] = JHTML::_('select.option', 'W', JText::_('wk_field'));
	$cftypelist[] = JHTML::_('select.option', 'U', JText::_('users_field'));
	$cftypelist[] = JHTML::_('select.option', 'C', JText::_('contract_field'));
	$cftypelist[] = JHTML::_('select.option', 'D', JText::_('downloads') . ' - ' . JText::_('CLIENT_ACCESS'));
	$cftypelist[] = JHTML::_('select.option', 'L', JText::_('WK_CLIENTS'));
	$lists['cftype'] = JHTML::_('select.genericlist', $cftypelist, 'cftype', 'class="inputbox" size="1"', 'value', 'text', $row->cftype);

	MaQmaHtmlEdit::display($row, $lists);
}

/**
 * Saves the workgroup
 * @param database A database connector object
 * @param string The name of the component
 */
function saveCustomField()
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	$row = new MaQmaHelpdeskTableCustomField($database);
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

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=customfield");
}

/**
 * Deletes one or more categories from the categories table
 * @param database A database connector object
 * @param string The name of the category section
 * @param array An array of unique category id numbers
 */
function removeCustomField($cid)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (count($cid) < 1) {
		echo "<script type='text/javascript'> alert('" . JText::_('cfield_delete') . "'); window.history.go(-1);</script>\n";
		exit;
	}

	if (count($cid)) {
		$cids = implode(',', $cid);
		$database->setQuery("DELETE FROM #__support_custom_fields WHERE id IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
		$database->setQuery("DELETE FROM #__support_wk_fields WHERE id_field IN (" . $cids . ")");
		if (!$database->query()) {
			echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		}
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=customfield");
}

/**
 * Publishes or Unpublishes one or more workgroups
 * @param database A database connector object
 * @param array An array of unique workgroup id numbers
 * @param integer 0 if unpublishing, 1 if publishing
 * @param string The name of the component
 */
function publishCustomField($cid = null, $publish = 1)
{
	$database = JFactory::getDBO();
	$mainframe = JFactory::getApplication();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	if (!is_array($cid) || count($cid) < 1) {
		$action = $publish ? 'publish' : 'unpublish';
		echo "<script type='text/javascript'> alert('" . JText::_('cfield_action') . " $action'); window.history.go(-1);</script>\n";
		exit;
	}

	$database->setQuery("UPDATE #__support_custom_field SET `show`='$publish' WHERE id IN (" . $cids . ")");

	if (!$database->query()) {
		echo "<script type='text/javascript'> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
		exit();
	}

	$mainframe->redirect("index.php?option=com_maqmahelpdesk&task=customfield");
}

?>