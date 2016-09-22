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
//if( $option != 'com_maqmahelpdesk' || $task == '' ) {
//}else{
$active_menu = ($task == '' ? '' : '');

// Get latest KB articles
$database->setQuery("SELECT k.id, k.kbcode, k.kbtitle, k.views, u.name, k.date_created, k.date_updated, ( sum( r.rate ) / count( r.id ) ) AS rating FROM #__support_kb AS k INNER JOIN #__users AS u ON u.id = k.id_user LEFT JOIN #__support_rate AS r ON r.id_table = k.id AND r.source = 'K' WHERE k.publish = '1' GROUP BY k.id, k.kbcode, k.kbtitle, k.views ORDER BY k.views DESC  LIMIT 0, 10");
$kbarticles = $database->loadObjectList();

for ($i = 0; $i < count($kbarticles); $i++) {
    $kbarticle = $kbarticles[$i];

    if ($id_workgroup == 0) {
        $sql = "SELECT c.id_workgroup FROM #__support_category AS c INNER JOIN #__support_kb_category AS kc ON kc.id_category=c.id WHERE kc.id_kb='" . $kbarticle->id . "' LIMIT 0, 1";
        $database->setQuery($sql);
        $lnkworkgroup = $database->loadResult();
    } else {
        $lnkworkgroup = $id_workgroup;
    }

    $link_menu = JRoute::_($link . '&id_workgroup=' . $lnkworkgroup . '&task=kb_view&id=' . $kbarticle->id);
    $output .= '<tr align="left">';
    $output .= '<td>';
    $output .= '<a href="' . $link_menu . '" class="sublevel' . $params->get('class_sfx') . '">' . $kbarticle->kbtitle . '</a>';
    $output .= '</td>';
    $output .= '</tr>';
}
//}
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <?php echo $output; ?>
</table>