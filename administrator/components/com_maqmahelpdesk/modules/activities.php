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
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/date.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/task.php');

$sql = "SELECT l.`id_ticket`, l.`id_user`, l.`date_time`, l.`log`, l.`image`, t.`ticketmask`, t.`id_workgroup`, t.`an_name`, u.`name`, c.`clientname`, su.`avatar`, t.`subject`
		FROM `#__support_log` as l
			 INNER JOIN `#__support_ticket` as t ON t.`id`=l.`id_ticket`
			 LEFT JOIN `#__support_users` AS su ON su.`id_user`=l.`id_user`
			 LEFT JOIN `#__users` AS u ON u.`id`=l.`id_user`
			 LEFT JOIN `#__support_client_users` AS cu ON cu.`id_user`=t.`id_user`
			 LEFT JOIN `#__support_client` AS c ON c.`id`=cu.`id_client`
		ORDER BY l.date_time DESC
		LIMIT 0, 5";
$database->setQuery($sql);
$rows = $database->loadObjectList();

$imgpath = JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px'; ?>

<table class="table table-striped table-bordered noleftborder" cellspacing="0" style="margin-left:5px;"><?php
for ($i = 0; $i < count($rows); $i++) {
	$row = &$rows[$i];

	// Get the user avatar
	$avatar = HelpdeskUser::GetAvatar($row->id_user);

	// Build the row
	echo '<tr class="' . ($i % 2 ? 'even' : 'odd') . '">
				<td>
					#<a href="' . JRoute::_('../index.php?option=com_maqmahelpdesk&id_workgroup=' . $row->id_workgroup . '&task=ticket_view&id=' . $row->id_ticket) . '"><b>' . $row->ticketmask . '</a> - ' . $row->subject . '</b><br />
					<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/logs/' . $row->image . '" style="padding:5px;" align="left" />' . $row->log . '<br />' . $row->date_time . '
				</td>
			 </tr>';
} ?>
<tr>
	<td colspan="2" style="text-align:center;"><a href="index.php?option=com_maqmahelpdesk&task=update"><?php echo JText::_('enable_activities_addon');?></a></td>
</tr>
</table>

