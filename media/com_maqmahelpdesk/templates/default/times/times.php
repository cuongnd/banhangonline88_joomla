<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('timesheet');?></h2>

	<form name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="post">
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup;?>"/>
        <input type="hidden" name="option" value="com_maqmahelpdesk" />
        <input type="hidden" name="task" value="timesheet_manage" />
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td><?php echo JText::_('year');?>: <?php echo $lists['year'];?></td>
                <td><?php echo JText::_('month');?>: <?php echo $lists['month'];?></td>
                <td><input type="submit" name="submit2" class="btn btn-inverse" value="<?php echo JText::_('show');?>"/></td>
            </tr>
        </table>
        <br/>
    </form>

	<p><a href="<?php echo JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=timesheet_edit");?>" class="btn btn-success"><i class="ico-plus ico-white"></i> <?php echo JText::_('add');?></a></p>

	<?php if(count($rows)):?>
		<table class="table table-bordered table-striped" cellspacing="0">
			<thead>
			<tr>
				<td><?php echo JText::_("TIME_SINGLE_DAY");?></td>
				<td><?php echo JText::_("CLIENT");?></td>
				<td><?php echo JText::_("TIME");?></td>
			</tr>
			</thead>
			<tbody>
			<?php foreach($rows as $row):?>
			<tr>
				<td><?php echo $row->day;?></td>
				<td><?php echo $row->clientname;?></td>
				<td align="right"><?php echo gmdate("H:i:s", $row->total);?></td>
			</tr>
			<?php endforeach;?>
			</tbody>
		</table>
	<?php endif;?>

</div>