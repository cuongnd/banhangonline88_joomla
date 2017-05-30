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
$id_workgroup = JRequest::getInt('id_workgroup', 0);

// Get the itemid of the component
$sql = "SELECT `id`
		FROM `#__menu`
		WHERE `link`='index.php?option=com_maqmahelpdesk&view=mainpage'
		  AND `published`=1";
$database->setQuery($sql);
$mgmitemid = $database->loadResult();

$params->def('class_sfx', '');
$params->def('qk_create_ticket', '1');
$params->def('qk_create_kb', '1');
$params->def('qk_create_glossary', '1');
$params->def('qk_create_announcement', '1');
$params->def('qk_announcements', '1');
$params->def('qk_tasks', '1');

$output = '';
$link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $mgmitemid;

// Check if is from support
$database->setQuery("SELECT COUNT(*) FROM #__support_permission p, #__support_workgroup w WHERE p.id_user='" . $user->id . "' AND w.id = p.id_workgroup");
$is_support = $database->loadResult();

$database->setQuery("SELECT p.manager FROM #__support_permission p, #__support_workgroup w  WHERE p.id_user='" . $user->id . "' AND p.id_workgroup='" . $id_workgroup . "' AND p.id_workgroup=w.id");
$supportOptions = null;
$usertype = $database->loadResult(); //Set support staff user type (Support user = 5; Support team leader = 6; Support mgr = 7;)

// Check if is a client
$is_client = 0;
$database->setQuery("SELECT u.id_client FROM #__support_client_users u, #__support_client c WHERE u.id_user='" . $user->id . "' AND c.id=u.id_client");
$database->loadResult() ? $is_client = $database->loadResult() : '';

// Get workgroups
if ($option != 'com_maqmahelpdesk' || $task == '') {
    $database->setQuery("SELECT id, wkdesc, wkabout, logo FROM #__support_workgroup WHERE `show`='1' ORDER BY ordering");
    $rows = $database->loadObjectList();

    // Verify permitions to the workgroups
    $wkids = '';
    if ($is_support) {
        for ($i = 0; $i < count($rows); $i++) {
            $row = $rows[$i];
            $database->setQuery("SELECT COUNT(*) FROM #__support_permission WHERE id_workgroup='" . $row->id . "' AND id_user='" . $user->id . "'");
            if ($database->loadResult() > 0) {
                $wkids .= $row->id . ',';
            }
        }
        $wkids = substr($wkids, 0, strlen($wkids) - 1);

        // Get workgroups
        $database->setQuery("SELECT id, wkdesc, wkabout, logo FROM #__support_workgroup WHERE id IN (" . $wkids . ") ORDER BY ordering");
        $rows = $database->loadObjectList();

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
        $database->setQuery("SELECT id, wkdesc, wkabout, logo FROM #__support_workgroup WHERE id IN (" . $wkids . ") ORDER BY ordering");
        $rows = $database->loadObjectList();
    }

    for ($i = 0; $i < count($rows); $i++) {
        $row = $rows[$i];
        $link_menu = JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $mgmitemid . "&id_workgroup=" . $row->id . "&task=workgroup_view");
        $output .= '<tr align="left"><td><a href="' . $link_menu . '" class="mainlevel' . $params->get('class_sfx') . '">' . $row->wkdesc . '</a></td></tr>';
    }
} else {
    // id="active_menu -> put inside <a/> tag
    if ($params->get('qk_create_ticket')) {
        $link_menu = JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=ticket_new');
        $active_menu = ($task == 'ticket_new' ? ' id="active_menu' : '');
        $output .= '<tr align="left"><td><a href="' . $link_menu . '" class="mainlevel' . $params->get('class_sfx') . '"' . $active_menu . '>' . JText::_('qk_create_ticket') . '</a></td></tr>';
    }
    if ($params->get('qk_create_kb') && $is_support > 0) {
        $link_menu = JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=kb_new');
        $active_menu = ($task == 'kb_new' ? ' id="active_menu' : '');
        $output .= '<tr align="left"><td><a href="' . $link_menu . '" class="mainlevel' . $params->get('class_sfx') . '"' . $active_menu . '>' . JText::_('qk_create_kb') . '</a></td></tr>';
    }
    if ($params->get('qk_create_glossary') && $is_support > 0) {
        $link_menu = JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=kb_addglossary');
        $active_menu = ($task == 'kb_addglossary' ? ' id="active_menu' : '');
        $output .= '<tr align="left"><td><a href="' . $link_menu . '" class="mainlevel' . $params->get('class_sfx') . '"' . $active_menu . '>' . JText::_('qk_create_glossary') . '</a></td></tr>';
    }
    /*if( $params->get('qk_create_announcement') && $is_support > 0 ) {
         $active_menu = ( $task=='' ? ' id="active_menu' : '' );
         $output .= '<tr align="left"><td><a href="'.$link_menu.'" class="mainlevel'.$params->get( 'class_sfx' ).'"'.$active_menu.'>'.JText::_('qk_create_announcement').'</a></td></tr>';
     }*/
    if ($params->get('qk_announcements')) {
        $link_menu = JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=announce_list');
        $active_menu = ($task == 'announce_list' ? ' id="active_menu' : '');
        $output .= '<tr align="left"><td><a href="' . $link_menu . '" class="mainlevel' . $params->get('class_sfx') . '"' . $active_menu . '>' . JText::_('qk_announcements') . '</a></td></tr>';
    }
    if ($params->get('qk_tasks') && $is_support > 0) {
        $link_menu = JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=calendar_view');
        $active_menu = ($task == 'calendar_view' ? ' id="active_menu' : '');
        $output .= '<tr align="left"><td><a href="' . $link_menu . '" class="mainlevel' . $params->get('class_sfx') . '"' . $active_menu . '>' . JText::_('qk_tasks') . '</a></td></tr>';
    }
}
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <?php echo $output; ?>
</table>