<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: dbcleanup.php 646 2012-05-22 08:20:58Z pdaniel $
 * $LastChangedDate: 2012-05-22 09:20:58 +0100 (Ter, 22 Mai 2012) $
 *
 */

// Ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$CONFIG = new JConfig();
$database = JFactory::getDBO();

$SecretWord = JRequest::getCmd('SecretWord', '', '', 'string');

if ($CONFIG->secret != $SecretWord) {
	return false;
}

$database->setQuery("SELECT publish FROM #__support_addon WHERE sname='dbcleanup'");
$published = $database->loadResult();

if ($published)
{
	$output = "Output of the Database Clean-Up Add-On\n";

	$sql = "SELECT COUNT(*) FROM #__support_ticket WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup)";
	$database->setQuery($sql);
	$output .= "\n- Tickets: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_category WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup)";
	$database->setQuery($sql);
	$output .= "\n- Categories: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_kb_category WHERE id_category NOT IN (SELECT id FROM #__support_category)";
	$database->setQuery($sql);
	$output .= "\n- KB Categories: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_announce WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup) AND id_workgroup>0";
	$database->setQuery($sql);
	$output .= "\n- Announces: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_wk_fields WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup)";
	$database->setQuery($sql);
	$output .= "\n- Custom Fields: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_field_value WHERE id_field NOT IN (SELECT id FROM #__support_wk_fields)";
	$database->setQuery($sql);
	$output .= "\n- Custom Fields Values: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_permission WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup)";
	$database->setQuery($sql);
	$output .= "\n- Support Staff: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_kb_comment WHERE id_kb NOT IN (SELECT id FROM #__support_kb)";
	$database->setQuery($sql);
	$output .= "\n- KB Comments: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_kb_category WHERE id_kb NOT IN (SELECT id FROM #__support_kb) OR id_category NOT IN (SELECT id FROM #__support_category)";
	$database->setQuery($sql);
	$output .= "\n- KB Categories: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_client_users WHERE id_client NOT IN (SELECT id FROM #__support_client)";
	$database->setQuery($sql);
	$output .= "\n- Client Users: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_ticket_resp WHERE id_ticket NOT IN (SELECT id FROM #__support_ticket)";
	$database->setQuery($sql);
	$output .= "\n- Tickets Activities: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_rate WHERE id_table NOT IN (SELECT id FROM #__support_ticket) AND source='T'";
	$database->setQuery($sql);
	$output .= "\n- Tickets Ratings: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_rate WHERE id_table NOT IN (SELECT id FROM #__support_kb) AND source='K'";
	$database->setQuery($sql);
	$output .= "\n- KB Ratings: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_client_wk WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup) OR id_client NOT IN (SELECT id FROM #__support_client)";
	$database->setQuery($sql);
	$output .= "\n- Clients Workgroups: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_client_info WHERE id_client NOT IN (SELECT id FROM #__support_client)";
	$database->setQuery($sql);
	$output .= "\n- Clients Information: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_contract WHERE id_client NOT IN (SELECT id FROM #__support_client)";
	$database->setQuery($sql);
	$output .= "\n- Clients Contracts: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_note WHERE id_ticket NOT IN (SELECT id FROM #__support_ticket)";
	$database->setQuery($sql);
	$output .= "\n- Tickets Notes: " . $database->loadResult();

	$sql = "SELECT COUNT(*) FROM #__support_task WHERE (id_ticket NOT IN (SELECT id FROM #__support_ticket)) AND id_ticket > 0";
	$database->setQuery($sql);
	$output .= "\n- Tasks: " . $database->loadResult();

	// Deletes from the tables
	$sql = "DELETE FROM #__support_ticket WHERE id_workgroup NOT IN (SELECT id FROM #__support_workgroup)";
	$database->setQuery($sql);
	$database->query();

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

	print '<pre>';
	print $output;
	print '</pre>';

	JUtility::sendMail($CONFIG->mailfrom, $CONFIG->fromname, $CONFIG->mailfrom, 'Database Clean-Up', $output);
} else {
	print '<p>' . JText::_('addon_not_ published') . '.';
}
