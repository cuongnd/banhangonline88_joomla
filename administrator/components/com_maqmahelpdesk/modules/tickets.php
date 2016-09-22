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
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/ticket.php');

$database->setQuery("SELECT t.id, t.subject, w.wkdesc, t.date, t.duedate, t.assign_to, t.id_status, t.id_priority"
		. "\nFROM #__support_ticket t, #__support_workgroup w"
		. "\nWHERE t.id_workgroup=w.id"
		. "\nORDER BY `date` DESC"
		. "\nLIMIT 7"
);
$rows = $database->loadObjectList(); ?>

<table class="table table-striped table-bordered noleftborder" cellspacing="0">
    <tbody><?php
	foreach ($rows as $row)
	{ ?>
    <tr>
        <td><?php echo $row->subject;?><br /><span class="lbl"><?php echo $row->wkdesc;?></span></td>
        <td><?php echo $row->date;?></td>
    </tr><?php
	}
	if (count($rows) == 0)
	{
		echo '<tr><td>' . JText::_('no_tickets') . '</td></tr>';
	} ?>
    </tbody>
</table>
