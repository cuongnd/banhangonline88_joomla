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

$imagesMenuPath = "../media/com_maqmahelpdesk/images/themes/" . $supportConfig->theme_icon . '/16px'; ?>
<div class="ui-app">
<div class="navbar">
<div class="navbar-inner">
<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
    <span class="ico-bar"></span>
    <span class="ico-bar"></span>
    <span class="ico-bar"></span>
</button>
<div class="container-fluid">
<a href="index.php?option=com_maqmahelpdesk" class="brand">Helpdesk</a>

<div class="nav-collapse collapse">
<ul class="nav">
<li><a href='index.php?option=com_maqmahelpdesk'
       title='<?php echo (JText::_('control_panel')); ?>'><?php echo (JText::_('control_panel')); ?></a>
</li>
<li class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle active"
                        href="#"><?php echo (JText::_('settings')); ?> <b class="caret"></b></a>
    <ul class="dropdown-menu">
        <li><a href='index.php?option=com_maqmahelpdesk&task=config'><img
                src='<?php echo $imagesMenuPath; ?>/config.png' align='absmiddle'
                border='0'/> <?php echo JText::_('configuration'); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=category'><img
                src='<?php echo $imagesMenuPath; ?>/categories.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('categories')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=priority'><img
                src='<?php echo $imagesMenuPath; ?>/priorities.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('priorities')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=status'><img
                src='<?php echo $imagesMenuPath; ?>/status.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('status')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=customfield'><img
                src='<?php echo $imagesMenuPath; ?>/custom_fields.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('cfields')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=replies'><img
                src='<?php echo $imagesMenuPath; ?>/replies.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('predefined_replies')); ?></a></li>
        <li class="divider"></li>
        <li class="nav-header"><?php echo (JText::_('actitivity_options')); ?></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=rates'><img
                src='<?php echo $imagesMenuPath; ?>/activities_rates.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('activity_rates')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=types'><img
                src='<?php echo $imagesMenuPath; ?>/activity_types.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('activity_types')); ?></a></li>
        <li class="divider"></li>
        <li class="nav-header"><?php echo (JText::_('expedient')); ?></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=holidays'><img
                src='<?php echo $imagesMenuPath; ?>/holidays.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('holidays')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=schedule'><img
                src='<?php echo $imagesMenuPath; ?>/schedule.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('schedules')); ?></a></li>
        <li class="divider"></li>
        <li class="nav-header"><?php echo (JText::_('contracts')); ?></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=contracts'><img
                src='<?php echo $imagesMenuPath; ?>/contract_type.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('contract_types')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=components'><img
                src='<?php echo $imagesMenuPath; ?>/components.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('contract_components')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=contracts_fields'><img
                src='<?php echo $imagesMenuPath; ?>/fields.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('users_cfield_menu')); ?></a></li>
    </ul>
</li>
<li class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle active"
                        href="#"><?php echo (JText::_('workgroups')); ?> <b class="caret"></b></a>
    <ul class="dropdown-menu">
		<?php if ($supportConfig->use_department_groups) : ?>
        <li><a href='index.php?option=com_maqmahelpdesk&task=groups'><img
                src='<?php echo $imagesMenuPath; ?>/workgroup_settings.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('department_groups')); ?></a></li>
		<?php endif;?>
        <li><a href='index.php?option=com_maqmahelpdesk&task=workgroup'><img
                src='<?php echo $imagesMenuPath; ?>/workgroups.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('manage')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=wkfields'><img
                src='<?php echo $imagesMenuPath; ?>/fields.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('cfield_assign_menu')); ?></a></li>
    </ul>
</li>
<li class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle active"
                        href="#"><?php echo (JText::_('clients_users')); ?> <b class="caret"></b></a>
    <ul class="dropdown-menu">
        <li><a href='index.php?option=com_maqmahelpdesk&task=client'><img
                src='<?php echo $imagesMenuPath; ?>/clients.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('clients_manager')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=group'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img
                src='<?php echo $imagesMenuPath; ?>/groups.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('groups')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=users'><img
                src='<?php echo $imagesMenuPath; ?>/users.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('users_manager')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=users_fields'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img
                src='<?php echo $imagesMenuPath; ?>/fields.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('users_cfield_menu')); ?></a></li>
    </ul>
</li>
<li><a href='index.php?option=com_maqmahelpdesk&task=staff'><?php echo (JText::_('support_staff')); ?></a>
</li>
<li class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle active"
                        href="#"><?php echo (JText::_('applications')); ?> <b class="caret"></b></a>
    <ul class="dropdown-menu">
        <li><a href='index.php?option=com_maqmahelpdesk&task=announce'><img
                src='<?php echo $imagesMenuPath; ?>/announcements.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('announcements')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=bugtracker'><img
                src='<?php echo $imagesMenuPath; ?>/bug.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('bugtracker')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=discussions'><img
                src='<?php echo $imagesMenuPath; ?>/discussions.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('discussions')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=forms'><img
                src='<?php echo $imagesMenuPath; ?>/forms.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('forms')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=glossary'><img
                src='<?php echo $imagesMenuPath; ?>/glossary.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('glossary')); ?></a></li>
        <li><a href="index.php?option=com_maqmahelpdesk&task=troubleshooter"><img
                src='<?php echo $imagesMenuPath; ?>/troubleshooter.png' align='absmiddle'
                border='0'/> <?php echo JText::_('troubleshooter'); ?></a></li>
        <li class="divider"></li>
        <li class="nav-header"><?php echo (JText::_('downloads')); ?></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=dlcategory'><img
                src='<?php echo $imagesMenuPath; ?>/categories.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('categories')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=product'><img
                src='<?php echo $imagesMenuPath; ?>/files.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('files')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=customer'><img
                src='<?php echo $imagesMenuPath; ?>/access.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('clients_access')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=licenses'><img
                src='<?php echo $imagesMenuPath; ?>/licenses.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('licenses')); ?></a></li>
        <li class="divider"></li>
        <li class="nav-header"><?php echo (JText::_('kb')); ?></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=kb_search'><img
                src='<?php echo $imagesMenuPath; ?>/kb.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('manage')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=kb_moderate'><img
                src='<?php echo $imagesMenuPath; ?>/comments.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('moderate_comments')); ?></a></li>
        <li class="divider"></li>
        <li class="nav-header"><?php echo (JText::_('tasks')); ?></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=calendar_new'><img
                src='<?php echo $imagesMenuPath; ?>/add.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('add_task')); ?></a></li>
    </ul>
</li>
<li class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle active"
                        href="#"><?php echo (JText::_('reports')); ?> <b class="caret"></b></a>
    <ul class="dropdown-menu">
        <li class="nav-header"><?php echo (JText::_('analysis')); ?></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=reports&report=wkanalysis'><img
                src='<?php echo $imagesMenuPath; ?>/charts.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('wk_analysis')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=reports&report=clientanalysis'><img
                src='<?php echo $imagesMenuPath; ?>/charts.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('client_analysis')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=reports&report=supportanalysis'><img
                src='<?php echo $imagesMenuPath; ?>/charts.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('support_analysis')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=reports&report=geo'><img
                src='<?php echo $imagesMenuPath; ?>/users.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('REPORTS_GEO')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=reports&report=ratings'><img
                src='<?php echo $imagesMenuPath; ?>/favorite.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('RATINGS_REPORT')); ?></a></li>
        <li class="divider"></li>
        <li class="nav-header"><?php echo (JText::_('monthly_reports')); ?></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=reports&report=clientm'><img
                src='<?php echo $imagesMenuPath; ?>/table.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('client_report')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=reports&report=clientmdetail'><img
                src='<?php echo $imagesMenuPath; ?>/table.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('detail_client_report')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=reports&report=status'><img
                src='<?php echo $imagesMenuPath; ?>/table.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('status_report2')); ?></a>
        <li class="divider"></li>
        <li class="nav-header"><?php echo (JText::_('download_stats')); ?></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=stats'><img
                src='<?php echo $imagesMenuPath; ?>/charts.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('downloads')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=stats_hits'><img
                src='<?php echo $imagesMenuPath; ?>/charts.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('pagehits')); ?></a></li>
        <li class="divider"></li>
        <li class="nav-header"><?php echo (JText::_('timesheet')); ?></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=reports&report=timesheets'><img
                src='<?php echo $imagesMenuPath; ?>/timesheet.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('simple')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=reports&report=timesheetd'><img
                src='<?php echo $imagesMenuPath; ?>/timesheet.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('detailed')); ?></a></li>
        <li class="divider"></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=reports&report=duedate'><img
                src='<?php echo $imagesMenuPath; ?>/priorities.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('duedate_report')); ?></a></li>
        <li class="divider"></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=reports_builder'><img
                src='<?php echo $imagesMenuPath; ?>/report_builder.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('custom_reports')); ?></a></li><?php
		$database->setQuery('SELECT `id`, `title`, `description` FROM #__support_reports ORDER BY `title`');
		$reports = $database->loadObjectList();
		if (count($reports) > 0) {
			$xx = 10;
			for ($z = 0; $z < count($reports); $z++) {
				$row = $reports[$z]; ?>
                <li><a
                        href='index.php?option=com_maqmahelpdesk&task=reports_builderreport&id=<?php echo $row->id; ?>'><img
                        src='<?php echo $imagesMenuPath; ?>/report_builder.png' align='absmiddle'
                        border='0'/> <?php echo ($row->title); ?></a></li><?php
				$xx++;
			} ?><?php
		} ?>
    </ul>
</li>
<li class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle active"
                        href="#"><?php echo (JText::_('tools')); ?> <b class="caret"></b></a>
    <ul class="dropdown-menu">
        <li><a href='index.php?option=com_maqmahelpdesk&task=update'><img
                src='<?php echo $imagesMenuPath; ?>/installer.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('tools')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=tools_db1'><img
                src='<?php echo $imagesMenuPath; ?>/database-clean.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('database_cleanup')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=autoclose'><img
                src='<?php echo $imagesMenuPath; ?>/clean.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('autoclose')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=tools_deletetickets1'><img
                src='<?php echo $imagesMenuPath; ?>/clean.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('DELETE_TICKETS')); ?></a></li>
        <li class="divider"></li>
        <li class="nav-header"><?php echo (JText::_('export')); ?></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=reports&report=ticketsexport'><img
                src='<?php echo $imagesMenuPath; ?>/export.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('export_data')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=export'><img
                src='<?php echo $imagesMenuPath; ?>/config.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('export_options')); ?></a></li>
        <li class="divider"></li>
        <li class="nav-header"><?php echo (JText::_('import_from')); ?></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=tools_billets1'><img
                src='<?php echo $imagesMenuPath; ?>/installer.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('import_billets')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=tools_rstickets1&ispro=1'><img
                src='<?php echo $imagesMenuPath; ?>/installer.png' align='absmiddle'
                border='0'/> <?php echo JText::_('import_rstickets_pro') . ' (' . JText::_('ALL') . ')'; ?></a>
        </li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=tools_rstickets1&ispro=1&iskb=1'><img
                src='<?php echo $imagesMenuPath; ?>/installer.png' align='absmiddle'
                border='0'/> <?php echo JText::_('import_rstickets_pro') . ' (' . JText::_('KNOWLEDGE_BASE') . ')'; ?>
        </a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=tools_rstickets1'><img
                src='<?php echo $imagesMenuPath; ?>/installer.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('import_rstickets')); ?></a></li>
        <!-- <li><a href='index.php?option=com_maqmahelpdesk&task=tools_ambrasubs1'><img src='<?php echo $imagesMenuPath; ?>/installer.png' align='absmiddle' border='0' /> <?php echo (JText::_('import_ambrasubs')); ?></a></li> -->
    </ul>
</li><?php

$database->setQuery("SELECT * FROM #__support_addon WHERE menu!='' ORDER BY lname");
$addons = $database->loadObjectList();
if (count($addons) > 0) {
	?>
<li class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle active"
                        href="#"><?php echo (JText::_('addons')); ?> <b class="caret"></b></a>
    <ul class="dropdown-menu">
        <li class="nav-header"><?php echo (JText::_('email_fetch')); ?></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=mail'><img
                src='<?php echo $imagesMenuPath; ?>/config.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('configuration')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=mail_mailignore'><img
                src='<?php echo $imagesMenuPath; ?>/filter.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('FETCHING_IGNORE_LIST')); ?></a></li>
        <li><a href='index.php?option=com_maqmahelpdesk&task=mails'><img
                src='<?php echo $imagesMenuPath; ?>/logs.png' align='absmiddle'
                border='0'/> <?php echo (JText::_('logs')); ?></a></li><?php
		$xx = 2;
		if (count($addons)) {
			?>
            <li class="divider"></li><?php
		}
		for ($z = 0; $z < count($addons); $z++) {
			$row        = $addons[$z];
			$rowOptions = explode(',', $row->menu);
			if (count($rowOptions) == 1) {
				?>
                <li><a
                        href='index.php?option=com_maqmahelpdesk&task=addon-<?php echo $row->sname; ?>_<?php echo $row->menu; ?>'><img
                        src='<?php echo $imagesMenuPath; ?>/config.png' align='absmiddle'
                        border='0'/> <?php echo ($row->lname); ?></a></li><?php
			} else {
				?>
                <li class="divider"></li>
                <li><?php echo ($row->lname); ?></li><?php
				$zz = 1;
				for ($zz = 0; $zz < count($rowOptions); $zz++) {
					?>
                    <li><a
                            href='index.php?option=com_maqmahelpdesk&task=addon-<?php echo $row->sname; ?>_<?php echo $rowOptions[$zz]; ?>'><img
                            src='../includes/js/ThemeOffice/component.png' align='absmiddle'
                            border='0'/> <?php echo ucfirst($rowOptions[$zz]); ?></a></li><?php
					$zz++;
				}
			}
			$xx++;
		} ?>
    </ul>
</li><?php
} ?>

<li><a href="#AboutInfo" data-toggle="modal"><?php echo addslashes(JText::_('about')); ?></a></li>
</ul>
</div>
<!-- /.nav-collapse -->
</div>
</div>
</div>