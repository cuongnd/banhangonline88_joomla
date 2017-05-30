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
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/form.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/user.php');

$id = JRequest::getVar('id', 0, '', 'int');

// Activities logger
HelpdeskUtility::ActivityLog('site', 'forms', $task, $id);

switch ($task) {
	case "save":
		saveForm();
		break;

	default:
		showForm($id);
		break;
}


function showForm($id)
{
	$database = JFactory::getDBO();
	$Itemid = JRequest::getInt('Itemid', 0);

	$form = NULL;
	$database->setQuery("SELECT `id`, `name`, `description`, `redirect`, `show`, `layout` FROM #__support_form WHERE id='" . $id . "'");
	$form = $database->loadObject();
}

function saveForm()
{
	$database = JFactory::getDBO();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	// Get the Form ID
	$id = JRequest::getVar('id', 0, 'POST', 'int');

	// Get the form info
	$database->setQuery("SELECT * FROM #__support_form WHERE id='" . $id . "'");
	$rowForm = NULL;
	$rowForm = $database->loadObject();

	// Get the form actions
	$database->setQuery("SELECT * FROM #__support_form_action WHERE id_form='" . $id . "'");
	$rowActions = $database->loadObjectList();

	// Get the form fields
	$database->setQuery("SELECT * FROM #__support_form_field WHERE id_form='" . $id . "' ORDER BY `order`");
	$rowFields = $database->loadObjectList();

	$save_db = 0;
	$output = 0;
	$emails = '';
	$includes = '';
	$user = '';

	for ($x = 0; $x < count($rowActions); $x++) {
		$rowAction = $rowActions[$x];

		switch ($rowAction->type) {
			case 'email':
				$emails .= $rowAction->value . ',';
				break;
			case 'show':
				$output = 1;
				break;
			case 'include':
				$includes .= $rowAction->value . ',';
				break;
			case 'db':
				$save_db = 1;
				break;
			case 'user':
				$user .= $rowAction->value . ',';
				break;
		}
	}

	$emails = JString::substr($emails, 0, strlen($emails) - 1);
	$includes = JString::substr($includes, 0, strlen($includes) - 1);
	$user = JString::substr($user, 0, strlen($user) - 1);

	HelpdeskForm::checkForm($id, 1);
	HelpdeskForm::saveForm($id, $rowForm, $save_db, $output, $emails, $includes, $user);
	unset($scForm);
}
