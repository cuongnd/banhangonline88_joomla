<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_tasks');?></h2>

    <form id="calendarForm" name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="POST">
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup;?>"/>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" name="task" value="calendar_view"/>
        <input type="hidden" id="year" name="year" value="<?php echo $year;?>"/>
        <input type="hidden" id="month" name="month" value="<?php echo $month;?>"/>

	    <div class="btn-toolbar">
		    <div class="btn-group">
			    <a href="<?php echo JRoute::_($link . 'calendar_view');?>" class="btn"><?php echo JText::_("month");?></a>
			    <a href="<?php echo JRoute::_($link . 'calendar_week');?>" class="btn"><?php echo JText::_("week");?></a>
			    <a href="<?php echo JRoute::_($link . 'calendar_day');?>" class="btn"><?php echo JText::_("day");?></a>
			    <a href="<?php echo JRoute::_($link . 'calendar_list');?>" class="btn"><?php echo JText::_("list");?></a>
		    </div>
		    <div class="btn-group">
			    <img src="<?php echo $imgpath;?>info.png" align="absmiddle" border="0" hspace="5" class="showPopover" data-original-title="<?php echo JText::_('legend');?>" data-content="<?php echo htmlspecialchars('<img src=' . $imgpath . 'status.png align=absmiddle border=0 hspace=2 vspace=2 /> ' . JText::_('task_ticket') . '<br /><img src=' . $imgpath . 'flag-yellow.png align=absmiddle border=0 hspace=2 vspace=2 /> ' . JText::_('task_isopen') . '<br /><img src=' . $imgpath . 'flag-green.png align=absmiddle border=0 hspace=2 vspace=2 /> ' . JText::_('task_isclosed') . '<br /><img src=' . $imgpath . 'flag-red.png align=absmiddle border=0 hspace=2 vspace=2 /> ' . JText::_('task_isdue'));?>" />
		    </div>
		    <div class="btn-group pull-right">
			    <a href="<?php echo JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=calendar_add");?>" class="btn btn-success"><i class="ico-plus ico-white"></i><?php echo JText::_("tasks_add");?></a>
		    </div>
		    <div class="btn-group pull-right">
			    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				    <?php echo HelpdeskDate::GetMonthName(str_pad($month,2,0,STR_PAD_LEFT));?>
				    <span class="caret"></span>
			    </a>
			    <ul class="dropdown-menu">
				    <?php for ($i = 1; $i <= 12; $i++):?>
				    <li><a href="javascript:;" onclick="$jMaQma('#month').val('<?php echo str_pad($i,2,0,STR_PAD_LEFT);?>');$jMaQma('#calendarForm').submit();"><?php echo HelpdeskDate::GetMonthName(str_pad($i,2,0,STR_PAD_LEFT));?></a></li>
				    <?php endfor; ?>
			    </ul>
		    </div>
		    <div class="btn-group pull-right">
			    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				    <?php echo $year;?>
				    <span class="caret"></span>
			    </a>
			    <ul class="dropdown-menu">
				    <?php for ($i = HelpdeskDate::MinYear(); $i <= HelpdeskDate::DateOffset("%Y"); $i++):?>
				        <li><a href="javascript:;" onclick="$jMaQma('#year').val('<?php echo $year;?>');$jMaQma('#calendarForm').submit();"><?php echo $i;?></a></li>
				    <?php endfor; ?>
			    </ul>
		    </div>
	    </div>
    </form>

    <p>&nbsp;</p>

    <?php echo HelpdeskTask::Calendar($year, $month, 3, NULL, $supportConfig->week_start); ?>

</div>