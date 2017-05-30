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

jimport('joomla.filesystem.archive');

// Include helpers
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/file.php';
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/tools.php';

require_once "components/com_maqmahelpdesk/views/tools/tmpl/default.php";

// Set toolbar and page title
HelpdeskToolsAdminHelper::addToolbar($task);
HelpdeskToolsAdminHelper::setDocument($task);

// get parameters from the URL or submitted form
$cid = JRequest::getVar('cid', array(0), '', 'array');

if (!is_array($cid)) {
	$cid = array(0);
}

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'tools', $task, $cid[0]);

switch ($task)
{
	case "db1":
		DB1();
		break;
	case "db2":
		DB2();
		break;
	case "billets1":
		Billets1();
		break;
	case "billets2":
		Billets2();
		break;
	case "ambrasubs1":
		Ambrasubs1();
		break;
	case "ambrasubs2":
		Ambrasubs2();
		break;
	case "rstickets1":
		RSTickets1();
		break;
	case "rstickets2":
		RSTickets2();
		break;
	case "deletetickets1":
		DeleteTickets1();
		break;
	case "deletetickets2":
		DeleteTickets2();
		break;
	case "pdf":
		InstallPDF();
		break;
	case "countries":
		InstallCountries();
		break;
}

function InstallPDF()
{
	$CONFIG = new JConfig();
	$mainframe = JFactory::getApplication();

	$url = 'http://versions.imaqma.com/helpdesk/pdf.zip';
	$path = $CONFIG->tmp_path;

	HelpdeskFile::downloadFileFromServer($url, $path, 'pdf.zip');

	JArchive::extract($path . '/pdf.zip', $path);

	JFolder::delete(JPATH_SITE . '/components/com_maqmahelpdesk/pdf/mpdf/iccprofiles/');
	JFolder::delete(JPATH_SITE . '/components/com_maqmahelpdesk/pdf/mpdf/ttfonts/');

	JFolder::move($CONFIG->tmp_path . '/iccprofiles/', JPATH_SITE . '/components/com_maqmahelpdesk/pdf/mpdf/iccprofiles/');
	JFolder::move($CONFIG->tmp_path . '/ttfonts/', JPATH_SITE . '/components/com_maqmahelpdesk/pdf/mpdf/ttfonts/');

	JFile::delete($path . '/pdf.zip');

	$mainframe->redirect("index.php?option=com_maqmahelpdesk", JText::_("PDF_INSTALL_MSG"));
}

function InstallCountries()
{
	$CONFIG = new JConfig();
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();

	$url = 'http://versions.imaqma.com/helpdesk/countries.csv';
	$path = $CONFIG->tmp_path;

	HelpdeskFile::downloadFileFromServer($url, $path, 'countries.csv');

	$sql = "TRUNCATE `#__support_country`;";
	$database->setQuery($sql);
	$database->query();

	$file = fopen($path . '/countries.csv', "r");

	while (!feof($file))
	{
		$line = fgets($file);

		if ($line != '')
		{
			$line = explode(',', $line);
			$sql = "INSERT INTO `#__support_country`(`startip`, `endip`, `countrycode`, `countryname`)
					VALUES('" . $line[0] . "', '" . $line[1] . "', '" . trim($line[2]) . "', '" . trim($line[3]) . "')";
			$database->setQuery($sql);
			$database->query();
		}
	}

	fclose($file);

	JFile::delete($path . '/countries.csv');

	$mainframe->redirect("index.php?option=com_maqmahelpdesk", JText::_("COUNTRIES_INSTALL_MSG"));
}

function RSTickets1($executed = 0)
{
	$database = JFactory::getDBO();
	$ispro = JRequest::getInt('ispro', 0);
	$iskb = JRequest::getInt('iskb', 0);
	$prefix = ($ispro ? 'pro' : '');

	// Tickets
	$sql = "SELECT COUNT(*) FROM #__rstickets" . $prefix . "_tickets";
	$database->setQuery($sql);
	$lists['tickets'] = $database->loadResult();

	// Ticket attachments
	$sql = "SELECT COUNT(*) FROM #__rstickets" . $prefix . ($ispro ? '_ticket' : '') . "_files";
	$database->setQuery($sql);
	$lists['files'] = $database->loadResult();

	// Ticket messages
	$sql = "SELECT COUNT(*) FROM #__rstickets" . $prefix . "_ticket_message" . ($ispro ? 's' : '');
	$database->setQuery($sql);
	$lists['messages'] = $database->loadResult();

	// Departments
	$sql = "SELECT COUNT(*) FROM #__rstickets" . $prefix . "_departments";
	$database->setQuery($sql);
	$lists['departments'] = $database->loadResult();

	// Custom fields
	$sql = "SELECT COUNT(*) FROM #__rstickets" . $prefix . "_custom_fields";
	$database->setQuery($sql);
	$lists['cfields'] = $database->loadResult();

	// Staff and staff permissions
	$sql = "SELECT COUNT(*) FROM #__rstickets" . $prefix . "_staff";
	$database->setQuery($sql);
	$lists['staff'] = $database->loadResult();

	// Only pro tables
	if ($ispro) {
		// KB categories
		$sql = "SELECT COUNT(*) FROM #__rsticketspro_kb_categories";
		$database->setQuery($sql);
		$lists['kb_categories'] = $database->loadResult();

		// KB articles
		$sql = "SELECT COUNT(*) FROM #__rsticketspro_kb_content";
		$database->setQuery($sql);
		$lists['kb_articles'] = $database->loadResult();

		// Priorities
		$sql = "SELECT COUNT(*) FROM #__rsticketspro_priorities";
		$database->setQuery($sql);
		$lists['priorities'] = $database->loadResult();

		// Statuses
		$sql = "SELECT COUNT(*) FROM #__rsticketspro_statuses";
		$database->setQuery($sql);
		$lists['statuses'] = $database->loadResult();

		// Notes
		$sql = "SELECT COUNT(*) FROM #__rsticketspro_ticket_notes";
		$database->setQuery($sql);
		$lists['notes'] = $database->loadResult();

		// Ticket log
		$sql = "SELECT COUNT(*) FROM #__rsticketspro_ticket_history";
		$database->setQuery($sql);
		$lists['log'] = $database->loadResult();
	}

	tools_html::rstickets1($lists, $executed, $ispro, $iskb);
}

function RSTickets2()
{
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();
	$ispro = JRequest::getInt('ispro', 0);
	$iskb = JRequest::getInt('iskb', 0);
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	// RSTicket Pro
	if ($ispro)
	{
		if (!$iskb)
		{
			$sql = "DELETE FROM `#__support_permission`";
			$database->setQuery($sql);
			$database->query();
			$sql = "INSERT INTO `#__support_permission`(`id`, `id_user`, `id_workgroup`, `manager`, `can_delete`)
					SELECT `id`, `user_id`, `department_id`, 7, 1 
					FROM `#__rsticketspro_staff_to_department`";
			$database->setQuery($sql);
			$database->query();

			$sql = "DELETE FROM `#__support_status`";
			$database->setQuery($sql);
			$database->query();
			$sql = "INSERT INTO `#__support_status`(`id`, `description`, `show`, `ordering`, `ticket_side`)
					SELECT `id`, `name`, `published`, `ordering`, 0
					FROM `#__rsticketspro_statuses`";
			$database->setQuery($sql);
			$database->query();

			$sql = "DELETE FROM `#__support_priority`";
			$database->setQuery($sql);
			$database->query();
			$sql = "INSERT INTO `#__support_priority`(`id`, `description`, `show`, `timevalue`, `timeunit`)
					SELECT `id`, `name`, `published`, 1, 'D'
					FROM `#__rsticketspro_priorities`";
			$database->setQuery($sql);
			$database->query();

			$sql = "DELETE FROM `#__support_note`";
			$database->setQuery($sql);
			$database->query();
			$sql = "INSERT INTO `#__support_note`(`id`, `id_ticket`, `id_user`, `date_time`, `note`, `show`)
					SELECT `id`, `ticket_id`, `user_id`, FROM_UNIXTIME(`date`), `text`, 0
					FROM `#__rsticketspro_ticket_notes`";
			$database->setQuery($sql);
			$database->query();

			$sql = "DELETE FROM `#__support_log`";
			$database->setQuery($sql);
			$database->query();
			$sql = "INSERT INTO `#__support_log`(`id`, `id_ticket`, `id_user`, `date_time`, `log`, `log_reserved`)
					SELECT `id`, `ticket_id`, `user_id`, FROM_UNIXTIME(`date`), 'Migrated', 'Migrated'
					FROM `#__rsticketspro_ticket_history`";
			$database->setQuery($sql);
			$database->query();

			$sql = "DELETE FROM `#__support_ticket_resp`";
			$database->setQuery($sql);
			$database->query();
			$sql = "INSERT INTO `#__support_ticket_resp`(`id`, `id_ticket`, `id_user`, `date`, `message`, `reply_summary`)
					SELECT `id`, `ticket_id`, `user_id`, FROM_UNIXTIME(`date`), `message`, ''
					FROM `#__rsticketspro_ticket_messages`";
			$database->setQuery($sql);
			$database->query();

			$sql = "DELETE FROM `#__support_custom_fields`";
			$database->setQuery($sql);
			$database->query();
			$sql = "INSERT INTO `#__support_custom_fields`(`id`, `caption`, `ftype`, `value`, `cftype`, `tooltip`)
					SELECT `id`, `label`, CASE WHEN `type`='freetext' THEN 'note' ELSE `type` END, `values`, 'W', `description`
					FROM `#__rsticketspro_custom_fields`";
			$database->setQuery($sql);
			$database->query();

			$sql = "DELETE FROM `#__support_wk_fields`";
			$database->setQuery($sql);
			$database->query();
			$sql = "INSERT INTO `#__support_wk_fields`(`id`, `id_workgroup`, `required`, `ordering`)
					SELECT `id`, `department_id`, `required`, `ordering`
					FROM `#__rsticketspro_custom_fields`";
			$database->setQuery($sql);
			$database->query();

			$sql = "DELETE FROM `#__support_field_value`";
			$database->setQuery($sql);
			$database->query();
			$sql = "INSERT INTO `#__support_field_value`(`id_field`, `id_ticket`, `newfield`)
					SELECT `custom_field_id`, `ticket_id`, `value`
					FROM `#__rsticketspro_custom_fields_values`";
			$database->setQuery($sql);
			$database->query();

			$sql = "DELETE FROM `#__support_workgroup`";
			$database->setQuery($sql);
			$database->query();
			$sql = "INSERT INTO `#__support_workgroup`(`id`, `wkdesc`, `wkabout`, `wkkb`, `wkticket`, `wkmail_address`, `wkmail_address_name`, `id_priority`, `show`, `ordering`)
					SELECT `id`, `name`, '', 1, 1, `email_address`, `email_address_fullname`, `priority_id`, `published`, `ordering`
					FROM `#__rsticketspro_departments`";
			$database->setQuery($sql);
			$database->query();

			$sql = "DELETE FROM `#__support_file` where `source`='T'";
			$database->setQuery($sql);
			$database->query();
			$sql = "INSERT INTO `#__support_file`(`id_file`, `id`, `date`, `id_user`, `source`, `filename`, `public`, `description`)
					SELECT `id`, `ticket_id`, now(), 0, 'T', md5(concat(id, ' ', ticket_message_id)), 1, ''
					FROM `#__rsticketspro_ticket_files`";
			$database->setQuery($sql);
			$database->query();

			$sql = "DELETE FROM `#__support_ticket`";
			$database->setQuery($sql);
			$database->query();
			$sql = "INSERT INTO `#__support_ticket`(`id`, `id_workgroup`, `assign_to`, `id_user`, `an_name`, `an_mail`, `ticketmask`, `subject`, `id_status`, `id_priority`, `date`, `last_update`, `duedate`, `ipaddress`, `sticky`, `message`, `day_week`)
					SELECT t.`id`, t.`department_id`, t.`staff_id`, t.`customer_id`, u.`name`, u.`email`, t.`code`, t.`subject`, t.`status_id`, t.`priority_id`, FROM_UNIXTIME(t.`date`), FROM_UNIXTIME(t.`last_reply`), FROM_UNIXTIME(t.`last_reply`), t.`ip`, t.`flagged`, '" . JText::_('migrated') . "', 0
					FROM `#__rsticketspro_tickets` AS t
						 INNER JOIN `#__users` AS u ON u.`id`=t.`customer_id`";
			$database->setQuery($sql);
			$database->query();

			$sql = "DELETE FROM `#__support_rate` where `source`='T'";
			$database->setQuery($sql);
			$database->query();
			$sql = "INSERT INTO `#__support_rate`(`id_table`, `source`, `rate`, `id_user`, `date`)
					SELECT `id`, 'T', `feedback`, `customer_id`, FROM_UNIXTIME(`last_reply`)
					FROM `#__rsticketspro_tickets`";
			$database->setQuery($sql);
			$database->query();

			// Copy ticket attachments
			if ($ispro) {
				$folder = JPATH_SITE . '/components/com_rsticketspro/assets/files/';
			} else {
				$folder = JPATH_SITE . '/components/com_rstickets/files/';
			}
			if ($handle = opendir($folder)) {
				while (false !== ($entry = readdir($handle))) {
					if ($entry != '.' && $entry != '..' && file_exists($folder . $entry)) {
						copy($folder . $entry, $supportConfig->docspath . $entry);
					}
				}
				closedir($handle);
			}

			// Update status workflows
			$sql = "SELECT `id`
					FROM `#__support_status`";
			$database->setQuery($sql);
			$rows = $database->loadObjectList();
			$status = '';
			for ($i = 0; $i < count($rows); $i++) {
				$status .= $rows[$i]->id . '#';
			}
			$sql = "UPDATE `#__support_status`
					SET `status_workflow`='" . $status . "'";
			$database->setQuery($sql);
			$database->query();
		}

		$sql = "DELETE FROM `#__support_category`";
		$database->setQuery($sql);
		$database->query();
		$sql = "INSERT INTO `#__support_category`(`id`, `name`, `show`, `id_workgroup`, `parent`, `kb`, `level`)
				SELECT `id`, `name`, `published`, 0, `parent_id`, 1, 1
				FROM `#__rsticketspro_kb_categories`";
		$database->setQuery($sql);
		$database->query();

		$sql = "DELETE FROM `#__support_kb`";
		$database->setQuery($sql);
		$database->query();
		$sql = "INSERT INTO `#__support_kb`(`id`, `id_user`, `kbtitle`, `content`, `publish`, `views`, `date_created`, `date_updated`, `anonymous_access`)
				SELECT `id`, 0, `name`, `text`, `published`, `hits`, FROM_UNIXTIME(`created`), FROM_UNIXTIME(`modified`), CASE WHEN `private`=1 THEN 2 ELSE `private` END
				FROM `#__rsticketspro_kb_content`";
		$database->setQuery($sql);
		$database->query();

		$sql = "DELETE FROM `#__support_kb_category`";
		$database->setQuery($sql);
		$database->query();
		$sql = "INSERT INTO `#__support_kb_category`(`id_category`, `id_kb`)
				SELECT `category_id`, `id`
				FROM `#__rsticketspro_kb_content`";
		$database->setQuery($sql);
		$database->query();
	} else {
		// Get default priority
		$sql = "SELECT `id`
				FROM `#__support_priority`
				WHERE `isdefault`=1";
		$database->setQuery($sql);
		$priority = $database->loadResult();

		// Copy ticket attachments
		$folder = JPATH_SITE . '/components/com_rstickets/files/';

		if ($handle = opendir($folder)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != '.' && $entry != '..' && file_exists($folder . $entry)) {
					copy($folder . $entry, $supportConfig->docspath . $entry);
				}
			}
			closedir($handle);
		}

		// Update status workflows
		$sql = "SELECT `id`
				FROM `#__support_status`";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();
		$status = '';
		for ($i = 0; $i < count($rows); $i++) {
			$status .= $rows[$i]->id . '#';
		}
		$sql = "UPDATE `#__support_status`
				SET `status_workflow`='" . $status . "'";
		$database->setQuery($sql);
		$database->query();
	}

	RSTickets1(1);
}

function Billets1($executed = 0)
{
	$database = JFactory::getDBO();

	$sql = "SELECT COUNT(*) FROM #__billets_tickets";
	$database->setQuery($sql);
	$lists['tickets'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__billets_categories";
	$database->setQuery($sql);
	$lists['categories'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__billets_ticketstates";
	$database->setQuery($sql);
	$lists['status'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__billets_files";
	$database->setQuery($sql);
	$lists['files'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__billets_comments";
	$database->setQuery($sql);
	$lists['notes'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__billets_messages";
	$database->setQuery($sql);
	$lists['messages'] = $database->loadResult();

	tools_html::billets1($lists, $executed);
}

function Billets2()
{
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	// Get default priority
	$sql = "SELECT `id`
			FROM `#__support_priority`
			WHERE `isdefault`=1";
	$database->setQuery($sql);
	$priority = $database->loadResult();

	// Clean attachments
	$sql = "DELETE FROM `#__support_file` WHERE `source`='T'";
	$database->setQuery($sql);
	$database->query();

	// Clean notes
	$sql = "DELETE FROM `#__support_note`";
	$database->setQuery($sql);
	$database->query();

	// Clean messages
	$sql = "DELETE FROM `#__support_ticket_resp`";
	$database->setQuery($sql);
	$database->query();

	// Clean categories
	$sql = "DELETE FROM `#__support_category`";
	$database->setQuery($sql);
	$database->query();

	// Clean statuses
	$sql = "DELETE FROM `#__support_status`";
	$database->setQuery($sql);
	$database->query();

	// Clean tickets
	$sql = "DELETE FROM `#__support_ticket`";
	$database->setQuery($sql);
	$database->query();

	// Import attachments
	$sql = "INSERT INTO `#__support_file`(`id_file`, `id`, `date`, `id_user`, `source`, `filename`, `public`, `description`)
			SELECT `id`, `ticketid`, `datetime`, `userid`, 'T', `physicalname`, 1, `` FROM `#__billets_files`";
	$database->setQuery($sql);
	$database->query();

	// Import notes
	$sql = "INSERT INTO `#__support_note`(`id`, `id_ticket`, `id_user`, `date_time`, `note`, `show`)
			SELECT `id`, `ticketid`, `userid`, `datetime`, `message`, 1 FROM `#__billets_comments`";
	$database->setQuery($sql);
	$database->query();

	// Import messages
	$sql = "INSERT INTO `#__support_ticket_resp`(`id`, `id_ticket`, `id_user`, `date`, `message`, `reply_summary`)
			SELECT `id`, `ticketid`, `userid_from`, `datetime`, `message`, `subject` FROM `#__billets_messages`";
	$database->setQuery($sql);
	$database->query();

	// Import categories
	$sql = "INSERT INTO `#__support_category`(`id`, `name`, `show`, `id_workgroup`, `parent`)
			SELECT `id`, `title`, `enabled`, 1, `parent` FROM `#__billets_categories` WHERE `isroot`!=1";
	$database->setQuery($sql);
	$database->query();

	// Import statuses
	$sql = "INSERT INTO `#__support_status`(`id`, `description`, `show`, `isdefault`, `status_group`, `user_access`, `allow_old_status_back`, `ticket_side`, `status_workflow`)
			SELECT `id`, `title`, `enabled`, 0, 'O', 1, 1, 0, '' FROM `#__billets_ticketstates`";
	$database->setQuery($sql);
	$database->query();

	// Import tickets
	$sql = "INSERT INTO `#__support_ticket`(`id`, `id_user`, `subject`, `message`, `id_status`, `date`, `last_update`, `id_category`, `id_priority`, `ticketmask`, `id_client`, `day_week`, `an_name`, `an_mail`, `duedate`, `id_workgroup`)
			SELECT `id`, `sender_userid`, `title`, `description`, `stateid`, `created_datetime`, `last_modified_datetime`, `categoryid`, " . $priority . ", CONCAT('" . rand(10, 99) . "',`id`,'" . rand(1000, 9999) . "'), 0, 0, '', '', `last_modified_datetime`, 1 FROM `#__billets_tickets`";
	$database->setQuery($sql);
	$database->query();

	// Copy ticket attachments
	$folder = JPATH_SITE . '/images/attachmentsbillets/';
	if ($handle = opendir($folder)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != '.' && $entry != '..' && file_exists($folder . $entry)) {
				copy($folder . $entry, $supportConfig->docspath . $entry);
			}
		}
		closedir($handle);
	}

	// Update status workflows
	$sql = "SELECT `id`
			FROM `#__support_status`";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();
	$status = '';
	for ($i = 0; $i < count($rows); $i++) {
		$status .= $rows[$i]->id . '#';
	}
	$sql = "UPDATE `#__support_status`
			SET `status_workflow`='" . $status . "'";
	$database->setQuery($sql);
	$database->query();

	// Updates some extra details on tickets
	/*
		 actualizar os dias da semana nos tickets
		 actualizar os ticketmasks nos tickets
		 actualizar o nome e o email da pessoa nos tickets
	  */

	Billets1(1);
}

function Ambrasubs1($executed = 0)
{
	$database = JFactory::getDBO();

	$sql = "SELECT COUNT(*) FROM #__billets_tickets";
	$database->setQuery($sql);
	$lists['tickets'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__billets_categories";
	$database->setQuery($sql);
	$lists['categories'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__billets_ticketstates";
	$database->setQuery($sql);
	$lists['status'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__billets_files";
	$database->setQuery($sql);
	$lists['files'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__billets_comments";
	$database->setQuery($sql);
	$lists['notes'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__billets_messages";
	$database->setQuery($sql);
	$lists['messages'] = $database->loadResult();

	tools_html::ambrasubs1($lists, $executed);
}

function Ambrasubs2()
{
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	// Get default priority

	// Clean categories
	$sql = "DELETE FROM `#__support_dl_category`";
	$database->setQuery($sql);
	$database->query();

	// Import categories
	$sql = "INSERT INTO `#__support_dl_category`(`id`, `name`, `show`, `id_workgroup`, `parent`)
			SELECT `id`, `title`, `enabled`, 1, `parent` FROM `#__billets_categories` WHERE `isroot`!=1";
	$database->setQuery($sql);
	$database->query();

	// Copy ticket attachments
	$folder = JPATH_SITE . '/images/attachmentsbillets/';
	if ($handle = opendir($folder)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != '.' && $entry != '..' && file_exists($folder . $entry)) {
				copy($folder . $entry, $supportConfig->docspath . $entry);
			}
		}
		closedir($handle);
	}

	Ambrasubs1(1);
}

function DB1()
{
	$database = JFactory::getDBO();

	$sql = "SELECT COUNT(*) FROM #__support_category WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup)";
	$database->setQuery($sql);
	$lists['categories'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_kb_category WHERE id_category NOT IN (SELECT id FROM #__support_category)";
	$database->setQuery($sql);
	$lists['kb_categories'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_announce WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup) AND id_workgroup>0";
	$database->setQuery($sql);
	$lists['announces'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_wk_fields WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup)";
	$database->setQuery($sql);
	$lists['wk_fields'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_field_value WHERE id_field NOT IN (SELECT id FROM #__support_wk_fields)";
	$database->setQuery($sql);
	$lists['field_values'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_permission WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup)";
	$database->setQuery($sql);
	$lists['support_staff'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_kb_comment WHERE id_kb NOT IN (SELECT id FROM #__support_kb)";
	$database->setQuery($sql);
	$lists['kb_comments'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_kb_category WHERE id_kb NOT IN (SELECT id FROM #__support_kb) OR id_category NOT IN (SELECT id FROM #__support_category)";
	$database->setQuery($sql);
	$lists['kb_categories'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_client_users WHERE id_client NOT IN (SELECT id FROM #__support_client)";
	$database->setQuery($sql);
	$lists['client_users'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_ticket_resp WHERE id_ticket NOT IN (SELECT id FROM #__support_ticket)";
	$database->setQuery($sql);
	$lists['ticket_replies'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_rate WHERE id_table NOT IN (SELECT id FROM #__support_ticket) AND source='T'";
	$database->setQuery($sql);
	$lists['ticket_rates'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_rate WHERE id_table NOT IN (SELECT id FROM #__support_kb) AND source='K'";
	$database->setQuery($sql);
	$lists['kb_rates'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_client_wk WHERE (id_workgroup NOT IN (SELECT id FROM #__support_workgroup) AND id_workgroup>0) OR id_client NOT IN (SELECT id FROM #__support_client)";
	$database->setQuery($sql);
	$lists['client_wks'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_client_info WHERE id_client NOT IN (SELECT id FROM #__support_client)";
	$database->setQuery($sql);
	$lists['client_info'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_contract WHERE id_client NOT IN (SELECT id FROM #__support_client)";
	$database->setQuery($sql);
	$lists['client_contracts'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_note WHERE id_ticket NOT IN (SELECT id FROM #__support_ticket)";
	$database->setQuery($sql);
	$lists['ticket_notes'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_task WHERE id_ticket NOT IN (SELECT id FROM #__support_ticket)";
	$database->setQuery($sql);
	$lists['tasks'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_ticket WHERE id_priority  NOT IN (SELECT id FROM #__support_priority)";
	$database->setQuery($sql);
	$lists['tickets_priorities'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_ticket WHERE id_status  NOT IN (SELECT id FROM #__support_status)";
	$database->setQuery($sql);
	$lists['tickets_status'] = $database->loadResult();

	tools_html::db1($lists, 0);
}

function DB2()
{
	$database = JFactory::getDBO();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$sql = "SELECT COUNT(*) FROM #__support_category WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup)";
	$database->setQuery($sql);
	$lists['categories'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_kb_category WHERE id_category NOT IN (SELECT id FROM #__support_category)";
	$database->setQuery($sql);
	$lists['kb_categories'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_announce WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup) AND id_workgroup>0";
	$database->setQuery($sql);
	$lists['announces'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_wk_fields WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup)";
	$database->setQuery($sql);
	$lists['wk_fields'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_field_value WHERE id_field NOT IN (SELECT id FROM #__support_wk_fields)";
	$database->setQuery($sql);
	$lists['field_values'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_permission WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup)";
	$database->setQuery($sql);
	$lists['support_staff'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_kb_comment WHERE id_kb NOT IN (SELECT id FROM #__support_kb)";
	$database->setQuery($sql);
	$lists['kb_comments'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_kb_category WHERE id_kb NOT IN (SELECT id FROM #__support_kb) OR id_category NOT IN (SELECT id FROM #__support_category)";
	$database->setQuery($sql);
	$lists['kb_categories'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_client_users WHERE id_client NOT IN (SELECT id FROM #__support_client)";
	$database->setQuery($sql);
	$lists['client_users'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_ticket_resp WHERE id_ticket NOT IN (SELECT id FROM #__support_ticket)";
	$database->setQuery($sql);
	$lists['ticket_replies'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_rate WHERE id_table NOT IN (SELECT id FROM #__support_ticket) AND source='T'";
	$database->setQuery($sql);
	$lists['ticket_rates'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_rate WHERE id_table NOT IN (SELECT id FROM #__support_kb) AND source='K'";
	$database->setQuery($sql);
	$lists['kb_rates'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_client_wk WHERE (id_workgroup NOT IN (SELECT id FROM #__support_workgroup) AND id_workgroup>0) OR id_client NOT IN (SELECT id FROM #__support_client)";
	$database->setQuery($sql);
	$lists['client_wks'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_client_info WHERE id_client NOT IN (SELECT id FROM #__support_client)";
	$database->setQuery($sql);
	$lists['client_info'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_contract WHERE id_client NOT IN (SELECT id FROM #__support_client)";
	$database->setQuery($sql);
	$lists['client_contracts'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_note WHERE id_ticket NOT IN (SELECT id FROM #__support_ticket)";
	$database->setQuery($sql);
	$lists['ticket_notes'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_task WHERE (id_ticket NOT IN (SELECT id FROM #__support_ticket)) AND id_ticket > 0";
	$database->setQuery($sql);
	$lists['tasks'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_ticket WHERE id_priority  NOT IN (SELECT id FROM #__support_priority)";
	$database->setQuery($sql);
	$lists['tickets_priorities'] = $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_ticket WHERE id_status  NOT IN (SELECT id FROM #__support_status)";
	$database->setQuery($sql);
	$lists['tickets_status'] = $database->loadResult();

	// Delete from database
	$sql = "DELETE FROM #__support_category WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup)";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM #__support_kb_category WHERE id_category NOT IN (SELECT id FROM #__support_category)";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM #__support_announce WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup) AND id_workgroup>0";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM #__support_wk_fields WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup)";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM #__support_field_value WHERE id_field NOT IN (SELECT id FROM #__support_wk_fields)";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM #__support_permission WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup)";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM #__support_kb_comment WHERE id_kb NOT IN (SELECT id FROM #__support_kb)";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM #__support_kb_category WHERE id_kb NOT IN (SELECT id FROM #__support_kb) OR id_category NOT IN (SELECT id FROM #__support_category)";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM #__support_client_users WHERE id_client NOT IN (SELECT id FROM #__support_client)";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM #__support_ticket_resp WHERE id_ticket NOT IN (SELECT id FROM #__support_ticket)";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM #__support_rate WHERE id_table NOT IN (SELECT id FROM #__support_ticket) AND source='T'";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM #__support_rate WHERE id_table NOT IN (SELECT id FROM #__support_kb) AND source='K'";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM #__support_client_wk WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup) OR id_client NOT IN (SELECT id FROM #__support_client)";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM #__support_client_info WHERE id_client NOT IN (SELECT id FROM #__support_client)";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM #__support_contract WHERE id_client NOT IN (SELECT id FROM #__support_client)";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM #__support_note WHERE id_ticket NOT IN (SELECT id FROM #__support_ticket)";
	$database->setQuery($sql);
	$database->query();

	$sql = "DELETE FROM #__support_task WHERE (id_ticket NOT IN (SELECT id FROM #__support_ticket)) AND id_ticket > 0";
	$database->setQuery($sql);
	$database->query();

	$sql = "SELECT ID FROM #__support_priority WHERE isdefault = '1'";
	$database->setQuery($sql);
	$default_priority = $database->loadResult();
	$sql = "UPDATE #__support_ticket SET id_priority='" . $default_priority . "' WHERE (id_priority NOT IN (SELECT id FROM #__support_priority))";
	$database->setQuery($sql);

	$sql = "SELECT ID FROM #__support_status WHERE isdefault = '1'";
	$database->setQuery($sql);
	$default_status = $database->loadResult();
	$sql = "UPDATE #__support_ticket SET id_status='" . $default_status . "' WHERE (id_status NOT IN (SELECT id FROM #__support_status))";
	$database->setQuery($sql);


	tools_html::db1($lists, 1);
}

function DeleteTickets1()
{
	tools_html::deleteTickets(false);
}

function DeleteTickets2()
{
	$database = JFactory::getDBO();
	JRequest::checkToken() or jexit('FALSE|Invalid Token');

	$sql = "TRUNCATE `#__support_bbb` ;";
	$database->setQuery($sql);
	$database->query();

	$sql = "TRUNCATE `#__support_bbb_invites` ;";
	$database->setQuery($sql);
	$database->query();

	$sql = "TRUNCATE `#__support_bbb_links` ;";
	$database->setQuery($sql);
	$database->query();

	$sql = "TRUNCATE `#__support_kb` ;";
	$database->setQuery($sql);
	$database->query();

	$sql = "TRUNCATE `#__support_kb_category` ;";
	$database->setQuery($sql);
	$database->query();

	$sql = "TRUNCATE `#__support_log` ;";
	$database->setQuery($sql);
	$database->query();

	$sql = "TRUNCATE `#__support_note` ;";
	$database->setQuery($sql);
	$database->query();

	$sql = "TRUNCATE `#__support_ticket` ;";
	$database->setQuery($sql);
	$database->query();

	$sql = "TRUNCATE `#__support_ticket_resp` ;";
	$database->setQuery($sql);
	$database->query();

	tools_html::deleteTickets(true);
}
