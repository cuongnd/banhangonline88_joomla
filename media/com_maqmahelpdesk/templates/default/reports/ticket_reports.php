<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('report_tickets');?></h2>

    <?php if ($is_client && $is_manager): ?>
    <h2><?php echo HelpdeskClient::GetName($user->id)?></h2>
    <?php endif;?>

    <br/>

    <table class="table table-striped table-bordered" cellspacing="0">
        <thead>
        <tr>
            <th width="50"><?php echo JText::_('ticketid');?></th>
            <th><?php echo JText::_('tpl_client');?></th>
            <th><?php echo JText::_('support_team');?></th>
            <th><?php echo JText::_('status');?></th>
            <th><?php echo JText::_('duedate');?></th>
            <th><?php echo JText::_('gfx_representation');?></th>
        </tr>
        </thead>

        <?php
        if (count($tickets)) {
            $i = 0;
            foreach ($tickets_rows as $row) {
                ?>
                <tr class="<?php echo (!$i ? 'first' : ($i % 2 ? 'even' : ''));?>">
                    <td colspan="6"><b><a href="<?php echo $row['link'];?>"><?php echo $row['ticketmask'];?></a>
                        - <?php echo $row['subject'];?></b></td>
                </tr>
                <tr class="<?php echo (!$i ? '' : ($i % 2 ? 'even' : ''));?>">
                    <td>&nbsp;</td>
                    <td><?php echo $row['customer_time2'];?> <br/>
                        <small><?php echo $row['customer_time_percentage'];?>%</small>
                    </td>
                    <td><?php echo $row['support_time2'];?> <br/>
                        <small><?php echo $row['support_time_percentage'];?>%</small>
                    </td>
                    <td><?php echo $row['current_status'];?></td>
                    <td <?php echo $row['overdue'];?>><?php echo $row['due_date'];?></td>
                    <td nowrap="nowrap"><?php echo $row['total_time2'];?> &bull;
                        <small>100%</small>
                        <br/><?php echo $row['time_ratio'];?></td>
                </tr><?php
                $i++;
            }
        } else {
            ?>
            <tr>
                <td colspan="7">
                    <br/><?php echo JText::_('no_tickets');?><br/>
                </td>
            </tr><?php
        } ?>

    </table>

</div>