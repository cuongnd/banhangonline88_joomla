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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/update.php';

// HTML dependency
require_once "components/com_maqmahelpdesk/views/update/tmpl/default.php";

// Set toolbar and page title
HelpdeskUpdateAdminHelper::addToolbar($task);
HelpdeskUpdateAdminHelper::setDocument();

$id = JRequest::getInt('id', 0);
$value = JRequest::getInt('value', 0);

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'update', $task, $id);

switch ($task) {
	case "publish":
		publish($id, $value);
		showStep1();
		break;

	default:
		showStep1();
		break;
}

function publish($id, $value)
{
	$database = JFactory::getDBO();
	$database->setQuery("UPDATE #__support_addon SET publish=" . $value . " WHERE id=" . $id);
	$database->query();
}

function showStep1()
{
	$database = JFactory::getDBO();

	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// Get installed add-on's - Add-On's
	$database->setQuery("SELECT id, sname, lname, description, iscore, version, execution, menu, date, publish FROM #__support_addon ORDER BY sname");
	$rowsAddOns = $database->loadObjectList();

	update_html::showStep1($rowsAddOns);
}
