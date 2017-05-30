<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_announcements');?></h2>

    <?php echo JText::_('announcements_header');?>

    <p>&nbsp;</p>

    <?php if (count($rows)): ?>
    <table class="table table-striped table-bordered">
	    <thead>
	    <tr>
		    <th><?php echo JText::_('date'); ?></th>
		    <th><?php echo JText::_('title'); ?></th>
		    <th class="tac"><?php echo JText::_('urgent'); ?></th>
	    </tr>
	    </thead>
	    <tbody><?php
        for ($i = 0; $i < count($rows); $i++)
        {
            $row = $rows[$i];
            $link = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=announce_view&id=' . $row->id); ?>
            <tr>
	            <td><?php echo HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($row->date));?></td>
	            <td><a href="<?php echo $link;?>" title="<?php echo $row->intro;?>"><?php echo $row->intro;?></a></td>
	            <td class="tac"><?php echo ($row->type ? '<span class="lbl lbl-important">' . JText::_("JYES") . '</span>' : '<span class="lbl lbl-success">' . JText::_("JNO") . '</span>');?></td>
            </tr><?php
        } ?>
	    </tbody>
    </table>
    <?php else: ?>
    <?php echo JText::_('no_announcements'); ?>
    <?php endif;?>

    <p>&nbsp;</p>

    <?php echo $pagelinks;?> <?php echo $pagecounter;?>

</div>