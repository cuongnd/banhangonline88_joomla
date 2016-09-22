<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('timesheet');?></h2>

	<div style="overflow-x:scroll;">

		<form name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="post">
	        <?php echo JHtml::_('form.token'); ?>
	        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
	        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup;?>"/>
	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="task" value="timesheet"/>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
	            <tr>
	                <td><?php echo JText::_('year');?>: <?php echo $lists['year'];?></td>
	                <td><?php echo JText::_('month');?>: <?php echo $lists['month'];?></td>
	                <td><input type="submit" name="submit2" class="btn btn-success" value="<?php echo JText::_('show');?>"/>
	                </td>
	            </tr>
	        </table>
	        <br/>
	    </form>

	    <?php echo $reporting->UserTimesheet($year, $month, $id_workgroup, $id_client, $type, $user->id);?>

	</div>
</div>