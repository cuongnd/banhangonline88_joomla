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

$lang = JFactory::getLanguage();
$language_tag = $lang->getTag();
$lang->load('com_maqmahelpdesk', JPATH_SITE, $language_tag, true);

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

$prev_wk = '';
$output = '';
$link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $mgmitemid;

// Check if is from support
$sql = "SELECT COUNT(*) 
		FROM #__support_permission p, #__support_workgroup w 
		WHERE p.id_user='" . $user->id . "' AND w.id = p.id_workgroup";
$database->setQuery($sql);
$is_support = $database->loadResult();

$sql = "SELECT p.manager 
		FROM #__support_permission p, #__support_workgroup w  
		WHERE p.id_user='" . $user->id . "' AND p.id_workgroup='" . $id_workgroup . "' AND p.id_workgroup=w.id";
$database->setQuery($sql);
$supportOptions = null;
$usertype = $database->loadResult(); //Set support staff user type (Support user = 5; Support team leader = 6; Support mgr = 7;)

// Check if is a client
$sql = "SELECT u.id_client 
		FROM #__support_client_users u, #__support_client c 
		WHERE u.id_user='" . $user->id . "' AND c.id=u.id_client";
$database->setQuery($sql);
$database->loadResult() ? $is_client = $database->loadResult() : '';

// Get tickets
$sql = "SELECT w.wkdesc, s.description, COUNT(*) AS total
		FROM #__support_ticket AS t
			 INNER JOIN #__support_status AS s ON s.id=t.id_status
			 INNER JOIN #__support_workgroup AS w ON w.id=t.id_workgroup
		WHERE s.status_group='O' " .
    ($is_support ? "AND (t.assign_to='" . $user->id . "' OR t.id_user='" . $user->id . "'" . ($usertype != 5 ? " OR t.id_user=0)" : ")") : "AND t.id_user='" . $user->id . "'") . "
		GROUP BY w.wkdesc, s.description
		ORDER BY t.date DESC ";
$database->setQuery($sql);
$tickets = $database->loadObjectList();

for ($i = 0; $i < count($tickets); $i++) {
    $ticket = $tickets[$i];
    if ($prev_wk != $ticket->wkdesc) {
        $prev_wk = $ticket->wkdesc;
        $output .= '<h4>' . $ticket->wkdesc . '</h4>';
    }
    $output .= '&nbsp;&nbsp;&bull;&nbsp;' . $ticket->description . ': <b>' . $ticket->total . '</b><br />';
}

echo $output;
