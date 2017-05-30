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

// Get the itemid of the component
$sql = "SELECT `id`
		FROM `#__menu`
		WHERE `link`='index.php?option=com_maqmahelpdesk&view=mainpage'
		  AND `published`=1";
$database->setQuery($sql);
$mgmitemid = $database->loadResult();
$id_workgroup = JRequest::getInt('id_workgroup', 0);

$is_client = 0;
if ($id_workgroup == 0) {
    $database->setQuery("SELECT u.id_client FROM #__support_client_users u, #__support_client c WHERE u.id_user='" . $user->id . "' AND c.id=u.id_client");
    $database->loadResult() ? $is_client = $database->loadResult() : '';
}

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

$active_menu = ($task == '' ? '' : '');

// Get workgroups
$database->setQuery("SELECT id, wkdesc, wkabout, logo FROM #__support_workgroup WHERE `show`='1' ORDER BY ordering");
$rows = $database->loadObjectList();

// Get announcements
$sql = "SELECT id, `date`, introtext, bodytext, frontpage, urgent, sent, id_workgroup 
		FROM #__support_announce 
		WHERE frontpage='1' " . ($is_support ? "" : "AND (id_client='0' OR id_client='" . $is_client . "') ") . ($id_workgroup == 0 ? "AND id_workgroup='0'" : "AND (id_workgroup='0' OR id_workgroup='" . $id_workgroup . "')") . "
		ORDER BY date DESC 
		LIMIT 0, 5";
$database->setQuery($sql);
$announces = $database->loadObjectList();

for ($i = 0; $i < count($announces); $i++) {
    $announce = $announces[$i];

    $wkids = "";
    if ($id_workgroup == 0) {
        if ($is_support) {
            for ($i = 0; $i < count($rows); $i++) {
                $row = $rows[$i];
                $database->setQuery("SELECT COUNT(*) FROM #__support_permission WHERE id_workgroup='" . $row->id . "' AND id_user='" . $user->id . "'");
                if ($database->loadResult() > 0) {
                    $wkids .= $row->id . ',';
                }
            }
            $wkids = substr($wkids, 0, strlen($wkids) - 1);

            // Get workgroups where the user is as support staff
            $database->setQuery("SELECT id, wkdesc, wkabout, logo FROM #__support_workgroup WHERE id IN (" . $wkids . ") AND `show`='1' ORDER BY ordering LIMIT 0, 1");

        } elseif ($is_client > 0) {
            for ($i = 0; $i < count($rows); $i++) {
                $row = $rows[$i];
                $database->setQuery("SELECT COUNT(*) FROM #__support_client_wk WHERE (id_workgroup='" . $row->id . "' OR id_workgroup='0') AND id_client='" . $is_client . "'");
                if ($database->loadResult() > 0) {
                    $wkids .= $row->id . ',';
                }
            }
            $wkids = substr($wkids, 0, strlen($wkids) - 1);

            // Get workgroups
            $database->setQuery("SELECT id, wkdesc, wkabout, logo FROM #__support_workgroup WHERE id IN (" . $wkids . ") AND `show`='1' ORDER BY ordering LIMIT 0, 1");
        }
        $lnkworkgroup = $database->loadResult();
    } else {
        $lnkworkgroup = $id_workgroup;
    }

    $link_menu = JRoute::_($link . '&id_workgroup=' . $lnkworkgroup . '&task=announce_view&id=' . $announce->id);
    $output .= '<tr align="left">';
    $output .= '<td>';
    $output .= '<a href="' . $link_menu . '" class="sublevel' . $params->get('class_sfx') . '">' . $announce->introtext . '</a>';
    $output .= '</td>';
    $output .= '</tr>';
} ?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <?php echo $output; ?>
</table>