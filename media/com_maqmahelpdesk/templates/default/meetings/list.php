<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('meetings');?></h2>

    <?php if (count($rows)): ?>
    <table class="table table-striped table-bordered" cellspacing="0">
        <thead>
        <tr>
            <td class="title"><?php echo JText::_('date');?></td>
            <td class="title"><?php echo JText::_('hour');?></td>
            <td class="title"><?php echo JText::_('ticket');?></td>
            <td class="title"><?php echo JText::_('invites');?></td>
        </tr>
        </thead>
        <tbody><?php
            $i = 0;
            foreach ($rows as $rowloop):
                $sql = "SELECT `invite`
					FROM `#__support_bbb_invites` 
					WHERE `id_meeting`=" . $rowloop->id;
                $database->setQuery($sql);
                $invites = $database->loadObjectList(); ?>

            <tr>
                <td valign="top"><?php echo HelpdeskDate::DateOffset($supportConfig->date_short,strtotime($rowloop->meeting_date));?></td>
                <td valign="top"><?php echo $rowloop->meeting_hours;?></td>
                <td valign="top"><?php echo '<a href="' . JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $rowloop->id_ticket) . '">#' . $rowloop->ticketmask . '</a> - ' . $rowloop->subject;?></td>
                <td valign="top"><?php
                    for ($x = 0; $x < count($invites); $x++) {
                        echo $invites[$x]->invite . "<br />";
                    } ?>
                </td>
            </tr><?php
                $i++;
            endforeach;?>
        </tbody>
    </table>
    <?php else: ?>
    <p><span style="color:#ff0000;"><?php echo JText::_('no_meetings');?></span></p>
    <?php endif; ?>

</div>