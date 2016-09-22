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

global $task, $id_workgroup;

$database = JFactory::getDBO();
$user = JFactory::getUser();
$task = JRequest::getVar('task', '', '', 'string');
$id_workgroup = JRequest::getInt('id_workgroup', 0);

// Get the itemid of the component
$sql = "SELECT `id`
		FROM `#__menu`
		WHERE `link`='index.php?option=com_maqmahelpdesk&view=mainpage'
		  AND `published`=1";
$database->setQuery($sql);
$mgmitemid = $database->loadResult();

$params->def('class_sfx', '');

$output = '';
$link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $mgmitemid;

// Check if is from support
$database->setQuery("SELECT COUNT(*) FROM #__support_permission p, #__support_workgroup w WHERE p.id_user='" . $user->id . "' AND w.id = p.id_workgroup");
$is_support = $database->loadResult();

$database->setQuery("SELECT p.manager FROM #__support_permission p, #__support_workgroup w  WHERE p.id_user='" . $user->id . "' AND p.id_workgroup='" . $id_workgroup . "' AND p.id_workgroup=w.id");
$supportOptions = null;
$usertype = $database->loadResult(); //Set support staff user type (Support user = 5; Support team leader = 6; Support mgr = 7;)

// Check if is a client
$database->setQuery("SELECT u.id_client FROM #__support_client_users u, #__support_client c WHERE u.id_user='" . $user->id . "' AND c.id=u.id_client");
$database->loadResult() ? $is_client = $database->loadResult() : '';

// Get workgroups
if ($option != 'com_maqmahelpdesk' || $task == '') {
    $output .= JText::_('no_workgroup_selected');

} else {
    // Get tickets
    $sql = "SELECT t.id, t.subject
			FROM #__support_ticket AS t
				 INNER JOIN #__support_status AS s ON s.id=t.id_status" .
        ($is_support ? "WHERE t.assign_to='" . $user->id . "' OR t.id_user='" . $user->id . "'" . ($usertype != 5 ? " OR t.id_user='0'" : "") : "WHERE t.id_user='" . $user->id . "'") . " AND s.status_group='O'
			ORDER BY t.date DESC LIMIT 0, 5";
    $database->setQuery($sql);
    $tickets = $database->loadObjectList();

    for ($i = 0; $i < count($tickets); $i++) {
        $ticket = $tickets[$i];

        $link_menu = JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $ticket->id);
        $output .= '<p><a href="' . $link_menu . '" class="sublevel' . $params->get('class_sfx') . '">' . $ticket->subject . '</a></p>';
    }
}

echo $output;
