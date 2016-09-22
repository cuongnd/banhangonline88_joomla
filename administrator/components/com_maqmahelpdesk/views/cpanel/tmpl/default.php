<div class="contentarea">
    <div class="contentbar row-fluid">
        <div class="span8">
			<?php include JPATH_SITE . '/administrator/components/com_maqmahelpdesk/modules/resume.php';?>
			<?php include JPATH_SITE . '/administrator/components/com_maqmahelpdesk/modules/daily_chart.php';?>
        </div>
        <div class="span4">
			<?php include JPATH_SITE . '/administrator/components/com_maqmahelpdesk/modules/status_chart.php';?>
        </div>
    </div>
    <div class="contentbar row-fluid">
        <div class="span3">
            <h4 style="margin-left:5px;"><?php echo JText::_('activities_subject');?></h4>
			<?php include JPATH_SITE . '/administrator/components/com_maqmahelpdesk/modules/activities.php';?>
        </div>
        <div class="span3">
            <h4><?php echo JText::_('calendar');?></h4>
			<?php include JPATH_SITE . '/administrator/components/com_maqmahelpdesk/modules/tasks.php';?>
        </div>
        <div class="span3">
            <h4><?php echo JText::_('latest_tickets');?></h4>
			<?php include JPATH_SITE . '/administrator/components/com_maqmahelpdesk/modules/tickets.php';?>
        </div>
        <div class="span3">
            <h4><?php echo JText::_('kb');?></h4>
			<?php include JPATH_SITE . '/administrator/components/com_maqmahelpdesk/modules/kb.php';?>
        </div>
    </div>
</div>
<div class="mqmclear"></div>