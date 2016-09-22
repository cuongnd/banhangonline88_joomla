<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('my_downloads');?></h2><?php

    if (count($rows)) {
        ?>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th><?php echo JText::_('category');?></th>
                <th><?php echo JText::_('dl_product');?></th>
                <th class="tac"><?php echo JText::_('serial_number');?></th>
                <th width="120" class="tac"><?php echo JText::_('start');?></th>
                <th width="120" class="tac"><?php echo JText::_('end');?></th>
                <th width="100">&nbsp;</th>
            </tr>
            </thead>
	        <tbody><?php
            $i = 0;
            foreach ($rows as $row)
            {
                $start = strtotime($row->servicefrom);
                $current = strtotime(date("Y-m-d")) - $start;
                $end = strtotime($row->serviceuntil) - $start;
				$percentage = 100 - (($current * 100) / $end);
				$percentage	= ($percentage < 0 ? 0 : $percentage); ?>
                <tr>
                    <td><?php echo $row->cname;?></td>
                    <td><?php echo $row->pname;?></td>
                    <td class="tac"><?php echo $row->serialno;?></td>
                    <td width="120" class="tac"><?php echo HelpdeskDate::DateOffset($supportConfig->date_short,strtotime($row->servicefrom));?></td>
                    <td width="120" class="tac"><?php echo HelpdeskDate::DateOffset($supportConfig->date_short,strtotime($row->serviceuntil));?></td>
                    <td>
                        <small><em><?php echo number_format($percentage,0); ?>%</em></small>
                        <div class="progress progress-<?php echo ($percentage>50 ? 'success' : ($percentage<=15 ? 'danger' : 'warning')); ?>">
                            <div class="bar" style="width:<?php echo $percentage;?>%;"></div>
                        </div>
                    </td>
                </tr><?php
            } ?>
	        </tbody>
        </table><?php
    } else {
        echo JText::_('no_downloads');
    }?>

</div>