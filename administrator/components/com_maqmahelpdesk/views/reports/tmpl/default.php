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

/**
 * @package MaQma Helpdesk
 */
class reports_html
{
	static function ExportTicketsStep1($lists, $exports, $sub_os)
	{
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$supportConfig = HelpdeskUtility::GetConfig();

		$supportConfig->date_short ? $tmp_format = $supportConfig->date_short : $tmp_format = 'd/m/Y H:i';
		$imgpath = JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/'; ?>

	<script language="javascript" text="text/javascript">
		<!--
		var originalOrderOS = '0';
		var originalPos = '0';

		var ordersOS = new Array();
			<?php
			$i = 0;
			foreach ($sub_os as $k => $items) {
				foreach ($items as $v) {
					echo "\n	ordersOS[" . $i++ . "] = new Array( '$v->value', '$k', '$v->text' );";
				}
			} ?>

			Joomla.submitbutton = function (pressbutton) {
				var form = document.adminForm;

				if (form.export_profile_id.value == 0) {
					alert("<?php echo JText::_('export_required'); ?>");
				} else {
					form.submit();
				}
			}<?php

			print 'a = new Array();' . "\n";

			for ($i = 0; $i < count($lists['profiles']); $i++) {
				$row_profile = $lists['profiles'][$i];

				print 'a[' . ($i + 1) . '] = new Array();' . "\n";
				print 'a[' . ($i + 1) . '][0] = "' . $row_profile->id . '";' . "\n";
				print 'a[' . ($i + 1) . '][1] = "' . $row_profile->workgroup . '";' . "\n";
				print 'a[' . ($i + 1) . '][2] = "' . $row_profile->client . '";' . "\n";
				print 'a[' . ($i + 1) . '][3] = "' . $row_profile->user . '";' . "\n";
				print 'a[' . ($i + 1) . '][4] = "' . $row_profile->status . '";' . "\n";
			} ?>

		function SelectRecords() {
			var ProfileObj = document.adminForm.export_profile_id;
			var StatusObj = document.adminForm.id_export_statuses;
			var WorkgroupObj = document.adminForm.selwk;
			var ClientObj = document.adminForm.client;
			var UserObj = document.adminForm.id_user;

			for (i = 1; i < WorkgroupObj.length; i++) {
				for (z = 1; z < a.length; z++) {
					if (WorkgroupObj[i].value == a[z][1] && ProfileObj.value == a[z][0]) {
						WorkgroupObj[i].selected = true;
					}
				}
			}

			for (i = 1; i < ClientObj.length; i++) {
				for (z = 1; z < a.length; z++) {
					if (ClientObj[i].value == a[z][2] && ProfileObj.value == a[z][0]) {
						ClientObj[i].selected = true;
					}
				}
			}

			for (i = 1; i < UserObj.length; i++) {
				for (z = 1; z < a.length; z++) {
					if (UserObj[i].value == a[z][3] && ProfileObj.value == a[z][0]) {
						UserObj[i].selected = true;
					}
				}
			}

			for (i = 1; i < StatusObj.length; i++) {
				for (z = 1; z < a.length; z++) {
					if (StatusObj[i].value == a[z][4] && ProfileObj.value == a[z][0]) {
						StatusObj[i].selected = true;
					}
				}
			}
		}
		//-->
	</script>

	<form id="adminForm" name="adminForm" action="<?php echo JURI::root();?>index.php" method="post"
		  onSubmit="return CheckFields();">
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="user" value="<?php echo $user->id; ?>"/>
		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="task" value="ajax_cvs"/>
		<input type="hidden" name="format" value="raw"/>

		<table class="admintable" cellspacing="1" width="100%">
			<tr>
				<td colspan="2">
					<?php echo JText::_('export_desc'); ?>
				</td>
			</tr>
			<tr>
				<td nowrap valign="top" class="key">
					<span rel="tooltip"
						  data-original-title="<?php echo htmlspecialchars(JText::_('export_profile') . '::' . JText::_('select_export_profile')); ?>"><?php echo JText::_('export_profile'); ?></span>
				</td>
				<td>
					<?php echo $lists['profile'];?>
				</td>
			</tr>
		</table>

		<br/>

		<table class="admintable" cellspacing="1" width="100%">
			<tr>
				<td nowrap valign="top" class="key">
					<span rel="tooltip"
						  data-original-title="<?php echo htmlspecialchars(JText::_('workgroup') . '::' . JText::_('export_wk_tooltip')); ?>"><?php echo JText::_('workgroup'); ?></span>
				</td>
				<td width="74" align="left" nowrap><?php echo $lists['workgroup'];?></td>
				<td width="100" align="left">&nbsp;</td>
				<td nowrap valign="top" class="key">
					<span rel="tooltip"
						  data-original-title="<?php echo htmlspecialchars(JText::_('client') . '::' . JText::_('export_client_tooltip')); ?>"><?php echo JText::_('client'); ?></span>
				</td>
				<td width="100" align="left" nowrap><?php echo $lists['client'];?></td>
			</tr>
			<tr>
				<td nowrap valign="top" class="key">
					<span rel="tooltip"
						  data-original-title="<?php echo htmlspecialchars(JText::_('status') . '::' . JText::_('export_status_tooltip')); ?>"><?php echo JText::_('status'); ?></span>
				</td>
				<td width="100" align="left" nowrap><?php echo $lists['statuses'];?></td>
				<td width="100" align="left">&nbsp;</td>
				<td nowrap valign="top" class="key">
					<span rel="tooltip"
						  data-original-title="<?php echo htmlspecialchars(JText::_('user') . '::' . JText::_('export_user_tooltip')); ?>"><?php echo JText::_('user'); ?></span>
				</td>
				<td width="100" align="left" nowrap>
					<script language="javascript" type="text/javascript">
						<!--
						writeDynaList('class="inputbox" name="id_user" size="1"', ordersOS, originalPos, originalPos, originalOrderOS);
						//-->
					</script>
				</td>
			</tr>

			<tr>
				<td nowrap valign="top" class="key">
					Tickets
				</td>
				<td width="100" align="left" nowrap>
					<?php echo $lists['month'];?>
					<?php echo $lists['year']; ?>
				</td>
				<td width="100" align="left">&nbsp;</td>
				<td nowrap valign="top" class="key">
				</td>
				<td width="100" align="left" nowrap>
				</td>
			</tr>

		</table>
	</form>


	<br/><br/>


	<table class="adminform">
		<tr>
			<th><?php echo JText::_('export_history'); ?></th>
		</tr>
	</table>


	<table class="adminlist" cellspacing="1" width="100%">
		<thead>
		<tr>
			<th width="20" nowrap><?php echo JText::_('id'); ?></th>
			<th width="10" nowrap><?php echo JText::_('export_date'); ?></th>
			<th nowrap><?php echo JText::_('exported_by'); ?></th>
			<th nowrap><?php echo JText::_('profile_used'); ?></th>
			<th nowrap><?php echo JText::_('records'); ?></th>
			<th nowrap><?php echo JText::_('hits'); ?></th>
			<th nowrap><?php echo JText::_('tools'); ?></th>
		</tr>
		</thead>
		<tbody>
			<?php

			if (count($exports) == 0) {
				print '<tr><td colspan="7"><br />' . JText::_('empty') . '<br /></td></tr>';

			} else {
				for ($i = 0; $i < count($exports); $i++) {
					$row_exp = $exports[$i];

					switch ($row_exp->export_type) {
						case 'A':
							$export_type_name = JText::_('activities');
							break;
						case 'T':
							$export_type_name = JText::_('tickets');
							break;
						case 'C':
							$export_type_name = JText::_('client');
							break;
						case 'U':
							$export_type_name = JText::_('users');
							break;
					}

					$database->setQuery("SELECT count(*) FROM #__support_ticket WHERE id_export='" . $row_exp->id . "'");
					$export_locked_items = $database->loadResult();

					if ($export_locked_items) {
						$export_locked_status = '<a href="index.php?option=com_maqmahelpdesk&task=reports&report=unlock&id=' . $row_exp->id . '"><img align="absmiddle" hspace="5" src="' . $imgpath . 'lock.png" border="0" alt="' . JText::_('unlock_tickets') . '" name="' . JText::_('unlock_tickets') . '" title="' . JText::_('unlock_tickets') . '" /></a>';
					} else {
						$export_locked_status = '<span rel="tooltip" data-original-title="' . JText::_('export_options_used') . '::' . JText::_('no_locked') . '"><img src="' . $imgpath . 'info.png" align="absmiddle" border="0" hspace="5" style="cursor:help; cursor:hand;" /></span>';
					}

					$export_tools_download = '<a href="index.php?option=com_maqmahelpdesk&task=ajax_cvs&export_task=view&id=' . $row_exp->id . '"><img align="absmiddle" src="' . $imgpath . 'export.png" border="0" alt="' . JText::_('download') . '" name="' . JText::_('download') . '" title="' . JText::_('download') . '" hspace="5" /></a>';

					$export_tools_template = '<a href="index.php?option=com_maqmahelpdesk&task=ajax_cvs&export_task=tpl&id=' . $row_exp->id . '"><img align="absmiddle" src="' . $imgpath . 'templates.png" border="0" alt="' . JText::_('download_template') . '" name="' . JText::_('download_template') . '" title="' . JText::_('download_template') . '" hspace="5" /></a>';

					$export_tools_delete = '<a href="index.php?option=com_maqmahelpdesk&task=reports&report=delete&id=' . $row_exp->id . '" onclick="javascript:return confirm(\'Are you sure you want to delete this export history item?\')"><img align="absmiddle" src="' . $imgpath . 'delete.png" border="0" alt="' . JText::_('delete') . '" name="' . JText::_('delete') . '" title="' . JText::_('delete') . '" hspace="5" /></a>';

					$export_date = '<a href="index.php?option=com_maqmahelpdesk&task=ajax_cvs&export_task=view&id=' . $row_exp->id . '">' . date($tmp_format, HelpdeskDate::ParseDate($row_exp->export_date, "%Y-%m-%d %H:%M:%S")) . '</a>';

					$export_id = '<a href="index.php?option=com_maqmahelpdesk&task=ajax_cvs&export_task=view&id=' . $row_exp->id . '">' . $row_exp->id . '</a>';


					print '<tr>';
					print '	 <td width="20" nowrap>' . $export_id . '</td>';
					print '	 <td width="10" nowrap>' . $export_date . '</td>';
					print '	 <td nowrap>' . $row_exp->export_author . '</td>';
					print '	 <td nowrap>' . $row_exp->profile_name . ' (' . $export_type_name . ' type)</td>';
					print '	 <td nowrap>' . $row_exp->num_records . '</td>';
					print '	 <td nowrap>' . $row_exp->hits . '</td>';
					print '	 <td>' . $export_tools_download . '&nbsp;' . $export_tools_template . $export_locked_status . $export_tools_delete . '</td>';
					print '</tr>';
				}
			}
			?>
		</tbody>
	</table>

	<script type='text/javascript'>SelectRecords();</script>

	<?php
	}

	static function Analysis($analyze, $year, $month, $id_workgroup, $id_client, $id_user, $lists, $print, $sub_os)
	{
		include(JPATH_SITE . '/components/com_maqmahelpdesk/includes/reports.php');

		// set the title
		switch ($analyze) {
			case 'W':
				$title = JText::_('wk_analysis');
				$report = 'wkanalysis';
				break;
			case 'C':
				$title = JText::_('client_analysis');
				$report = 'clientanalysis';
				break;
			case 'S':
				$title = JText::_('support_analysis');
				$report = 'supportanalysis';
				break;
			case 'TS':
				$title = JText::_('timesheet');
				$report = 'timesheets';
				break;
			case 'TD':
				$title = JText::_('timesheet');
				$report = 'timesheetd';
				break;
		}

		?>

	<script language="javascript" text="text/javascript">
		<!--
		var originalOrderOS = '<?php echo $id_user; ?>';
		var originalPos = '<?php echo $id_client; ?>';

		var ordersOS = new Array();
			<?php
			$i = 0;
			foreach ($sub_os as $k => $items) {
				foreach ($items as $v) {
					echo "\n	ordersOS[" . $i++ . "] = new Array( '$v->value', '$k', '$v->text' );";
				}
			} ?>
		//-->
	</script>

	<form id="adminForm" name="adminForm" action="index.php">
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="task" value="reports"/>
		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="report" value="<?php echo $report; ?>"/>
		<table class="adminheading" border="0">
			<tr>
				<!--th class="reports">< ?php echo $title; ?></th-->
				<?php		if (!$print) {
				if ($analyze == 'C' || $analyze == 'TS' || $analyze == 'TD') {
					?>
					<td><?php echo $lists['client']; ?></td>
					<td>
						<script language="javascript" type="text/javascript">
							<!--
							writeDynaList('class="inputbox" name="id_user" size="1" onChange="document.adminForm.submit();"', ordersOS, originalPos, originalPos, originalOrderOS);
							//-->
						</script>
					</td>
					<?php } elseif ($analyze == 'S') { ?>
					<td><?php echo $lists['assign']; ?></td>
					<?php } ?>
				<td><?php echo $lists['workgroup']; ?></td>
				<td><?php echo $lists['month']; ?></td>
				<td><?php echo $lists['year']; ?></td>
				<?php } ?>
			</tr>
		</table>
		<table class="admintable" cellspacing="1" width="100%">
			<tr>
				<td>
					<table width="100%">
						<?php			$reporting = new SupportReports();
						switch ($analyze) {
							case 'W':
								echo $reporting->WorkgroupAnalysis($year, $month, $id_workgroup, $id_client, $id_user);
								break;

							case 'S':
								echo $reporting->StaffAnalysis($year, $month, $id_workgroup, $id_client, $id_user);
								break;

							case 'C':
								echo $reporting->ClientAnalysis($year, $month, $id_workgroup, $id_client, $id_user);
								break;

							case 'TS':
								echo $reporting->Timesheet($year, $month, $id_workgroup, $id_client, 'S');
								break;

							case 'TD':
								echo $reporting->Timesheet($year, $month, $id_workgroup, $id_client, 'D');
								break;
						} ?>
					</table>
				</td>
			</tr>
		</table>
	</form><?php
	}

	static function showBuilder(&$rows, &$pageNav)
	{
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

	<form action="index.php" method="post" id="adminForm" name="adminForm">
		<?php echo JHtml::_('form.token'); ?>
		<table class="adminlist">
			<thead>
			<tr>
				<th width="20" align="right">#</th>
				<th width="20">
					<input type="checkbox" id="checkall-toggle" name="checkall-toggle" value=""
						   onClick="Joomla.checkAll(this);"/>
				</th>
				<th class="title"><?php echo JText::_('title'); ?></th>
				<th class="title"><?php echo JText::_('description'); ?></th>
				<th class="title" width="35"><?php echo JText::_('run'); ?></th>
			</tr>
			</thead>
			<tbody>
				<?php

				if (count($rows) == 0) {
					?>
				<tr>
					<td colspan="5"><?php echo JText::_('register_not_found'); ?></td>
				</tr><?php
				} else {
					$k = 0;
					for ($i = 0, $n = count($rows); $i < $n; $i++) {
						$row = &$rows[$i]; ?>
					<tr class="<?php echo "row$k"; ?>">
						<td width="20" align="right"><span class="lbl"><?php echo $row->id; ?></span></td>
						<td width="20"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
						<td>
							<a href="#reports_builderedit"
							   onClick="return listItemTask('cb<?php echo $i;?>','reports_builderedit')">
								<?php echo $row->title; ?>
							</a>
						</td>
						<td><?php echo $row->description; ?></td>
						<td width="35" align="center"><a
							href="index.php?option=com_maqmahelpdesk&task=reports_builderreport&id=<?php echo $row->id; ?>"><img
							src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/charts.png"
							border="0"/></a></td>
						<?php
						$k = 1 - $k;
						?>
					</tr>
						<?php
					} // for loop
				} // if ?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="5"><?php echo $pageNav->getListFooter(); ?></td>
			</tr>
			</tfoot>
		</table>

		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="task" value="reports_builder"/>
		<input type="hidden" name="boxchecked" value="0"/>
	</form>
	<?php
	}

	static function editBuilder(&$row, $lists, $sub_os)
	{
		?>
	<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function (pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'reports_builder') {
                Joomla.submitform(pressbutton);
				return;
			}

			if (form.title.value == "") {
				alert("<?php echo JText::_('title_required'); ?>");
			} else {
				Joomla.submitform(pressbutton, document.getElementById('adminForm'));
			}
		}

		var originalOrderOS = '<?php echo ($row->id == 0 ? 0 : $row->f_user); ?>';
		var originalPos = '<?php echo ($row->id == 0 ? 0 : $row->f_client); ?>';

		var ordersOS = new Array();
			<?php
			$i = 0;
			foreach ($sub_os as $k => $items) {
				foreach ($items as $v) {
					echo "\n	ordersOS[" . $i++ . "] = new Array( '$v->value', '$k', '$v->text' );";
				}
			} ?>
	</script>

	<form action="index.php" method="post" id="adminForm" name="adminForm">
		<?php echo JHtml::_('form.token'); ?>
		<?php
		$GLOBALS['title_editBuilder'] = ($row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('report');
		?>

	<table class="adminform">
		<tr>
			<th><?php echo JText::_('report_information'); ?></th>
		</tr>
	</table>


	<table class="admintable" cellspacing="1" width="100%">
		<tr>
			<td nowrap valign="top" class="key">
				<span rel="tooltip"
					  data-original-title="<?php echo htmlspecialchars(JText::_('title')); ?>"><?php echo JText::_('title'); ?></span>
			</td>
			<td>
				<input class="text_area" type="text" name="title" value="<?php echo $row->title; ?>" size="50"
					   maxlength="50"/>
			</td>
		</tr>
		<tr>
			<td nowrap valign="top" class="key">
				<span rel="tooltip"
					  data-original-title="<?php echo htmlspecialchars(JText::_('description')); ?>"><?php echo JText::_('description'); ?></span>
			</td>
			<td>
				<textarea id="" name="" style="width:500px;height:150px;"><?php echo $row->description;?></textarea>
			</td>
		</tr>
		<tr>
			<td nowrap valign="top" class="key">
				<span rel="tooltip"
					  data-original-title="<?php echo htmlspecialchars(JText::_('layout')); ?>"><?php echo JText::_('layout'); ?></span>
			</td>
			<td>
				<table>
					<tr>
						<td>
							<div align="center"><input type="radio" name="layout"
													   value="1" <?php echo $row->layout == 1 ? 'checked' : '' ?>><img
								src="../components/com_maqmahelpdesk/images/layout1.png" align="absmiddle"/>

								<div>
						</td>
						<td>
							<div align="center"><input type="radio" name="layout"
													   value="2" <?php echo $row->layout == 2 ? 'checked' : '' ?>><img
								src="../components/com_maqmahelpdesk/images/layout2.png" align="absmiddle"/>

								<div>
						</td>
						<td>
							<div align="center"><input type="radio" name="layout"
													   value="3" <?php echo $row->layout == 3 ? 'checked' : '' ?>><img
								src="../components/com_maqmahelpdesk/images/layout3.png" align="absmiddle"/>

								<div>
						</td>
						<td>
							<div align="center"><input type="radio" name="layout"
													   value="4" <?php echo $row->layout == 4 ? 'checked' : '' ?>><img
								src="../components/com_maqmahelpdesk/images/layout4.png" align="absmiddle"/>

								<div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td nowrap valign="top" class="key">
				<span rel="tooltip"
					  data-original-title="<?php echo htmlspecialchars(JText::_('report_type')); ?>"><?php echo JText::_('report_type'); ?></span>
			</td>
			<td><?php echo $lists['report_type']; ?></td>
		</tr>
		<tr>
			<td nowrap valign="top" class="key">
				<span rel="tooltip"
					  data-original-title="<?php echo htmlspecialchars(JText::_('report_detail') . '::' . JText::_('extended_desc')); ?>"><?php echo JText::_('report_detail'); ?></span>
			</td>
			<td>
				<input type="radio" name="report_type"
					   value="S" <?php echo $row->report_type == 'S' ? 'checked' : ''; ?> /><?php echo JText::_('simple'); ?>
				&nbsp;&nbsp;<input type="radio" name="report_type"
								   value="E" <?php echo $row->report_type == 'E' ? 'checked' : ''; ?> /> <?php echo JText::_('extended'); ?>
			</td>
		</tr>
	</table>


	<br/>


	<table class="adminform">
		<tr>
			<th><?php echo JText::_('chart_options'); ?></th>
		</tr>
	</table>

	<table class="admintable" cellspacing="1" width="100%">
		<tr>
			<td nowrap valign="top" class="key">
				<span rel="tooltip"
					  data-original-title="<?php echo htmlspecialchars(JText::_('chart_type')); ?>"><?php echo JText::_('chart_type'); ?></span>
			</td>
			<td><?php echo $lists['chart_type']; ?></td>
		</tr>
		<tr>
			<td nowrap valign="top" class="key">
				<span rel="tooltip"
					  data-original-title="<?php echo htmlspecialchars(JText::_('show_percs')); ?>"><?php echo JText::_('show_percs'); ?></span>
			</td>
			<td><?php echo $lists['chart_percentage']; ?></td>
		</tr>
		<tr>
			<td nowrap valign="top" class="key">
				<span rel="tooltip"
					  data-original-title="<?php echo htmlspecialchars(JText::_('chart_width')); ?>"><?php echo JText::_('chart_width'); ?></span>
			</td>
			<td>
				<input class="text_area" type="text" name="chart_width" value="<?php echo $row->chart_width; ?>"
					   size="5" maxlength="3"/>
			</td>
		</tr>
		<tr>
			<td nowrap valign="top" class="key">
				<span rel="tooltip"
					  data-original-title="<?php echo htmlspecialchars(JText::_('chart_height')); ?>"><?php echo JText::_('chart_height'); ?></span>
			</td>
			<td>
				<input class="text_area" type="text" name="chart_height" value="<?php echo $row->chart_height; ?>"
					   size="3" maxlength="3"/>
			</td>
		</tr>
	</table>

	<br/>

	<table class="adminform">
		<tr>
			<th class="title"><?php echo JText::_('criteria'); ?>:</th>
			<th class="title"><?php echo JText::_('group_by'); ?>:</th>

		</tr>
	</table>

	<table class="admintable" cellspacing="1" width="100%">
		<tr>
			<td valign="top">
				<table width="100%">
					<tr>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('workgroup')); ?>"><?php echo JText::_('workgroup'); ?></span>
						</td>
						<td><?php echo $lists['workgroup']; ?> <input type="radio" name="sf_workgroup"
																	  value="1" <?php echo $row->sf_workgroup == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
							&nbsp;&nbsp;&nbsp;<input type="radio" name="sf_workgroup"
													 value="0" <?php echo $row->sf_workgroup == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
						</td>
					</tr>
					<tr>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('category')); ?>"><?php echo JText::_('category'); ?></span>
						</td>
						<td><?php echo $lists['category']; ?> <input type="radio" name="sf_category"
																	 value="1" <?php echo $row->sf_category == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
							&nbsp;&nbsp;&nbsp;<input type="radio" name="sf_category"
													 value="0" <?php echo $row->sf_category == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
						</td>
					</tr>
					<tr>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('priority')); ?>"><?php echo JText::_('priority'); ?></span>
						</td>
						<td><?php echo $lists['priority']; ?> <input type="radio" name="sf_priority"
																	 value="1" <?php echo $row->sf_priority == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
							&nbsp;&nbsp;&nbsp;<input type="radio" name="sf_priority"
													 value="0" <?php echo $row->sf_priority == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
						</td>
					</tr>
					<tr>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('status')); ?>"><?php echo JText::_('status'); ?></span>
						</td>
						<td><?php echo $lists['status']; ?> <input type="radio" name="sf_status"
																   value="1" <?php echo $row->sf_status == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
							&nbsp;&nbsp;&nbsp;<input type="radio" name="sf_status"
													 value="0" <?php echo $row->sf_status == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
						</td>
					</tr>
					<tr>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('client')); ?>"><?php echo JText::_('client'); ?></span>
						</td>
						<td><?php echo $lists['client']; ?> <input type="radio" name="sf_client"
																   value="1" <?php echo $row->sf_client == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
							&nbsp;&nbsp;&nbsp;<input type="radio" name="sf_client"
													 value="0" <?php echo $row->sf_client == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
						</td>
					</tr>
					<tr>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('user')); ?>"><?php echo JText::_('user'); ?></span>
						</td>
						<td>
							<script language="javascript" type="text/javascript">
								<!--
								writeDynaList('class="inputbox" name="f_user" size="1"', ordersOS, originalPos, originalPos, originalOrderOS);
								//-->
							</script>
							<input type="radio" name="sf_user"
								   value="1" <?php echo $row->sf_user == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
							&nbsp;&nbsp;&nbsp;<input type="radio" name="sf_user"
													 value="0" <?php echo $row->sf_user == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
						</td>
					</tr>
					<tr>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('tkt_chng_stat_nfy_sup')); ?>"><?php echo JText::_('tkt_chng_stat_nfy_sup'); ?></span>
						</td>
						<td><?php echo $lists['assign']; ?> <input type="radio" name="sf_staff"
																   value="1" <?php echo $row->sf_staff == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
							&nbsp;&nbsp;&nbsp;<input type="radio" name="sf_staff"
													 value="0" <?php echo $row->sf_staff == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
						</td>
					</tr>
					<tr>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('year')); ?>"><?php echo JText::_('year'); ?></span>
						</td>
						<td><?php echo $lists['year']; ?> <input type="radio" name="sf_year"
																 value="1" <?php echo $row->sf_year == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
							&nbsp;&nbsp;&nbsp;<input type="radio" name="sf_year"
													 value="0" <?php echo $row->sf_year == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
						</td>
					</tr>
					<tr>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('month')); ?>"><?php echo JText::_('month'); ?></span>
						</td>
						<td><?php echo $lists['month']; ?> <input type="radio" name="sf_month"
																  value="1" <?php echo $row->sf_month == '1' ? 'checked' : ''; ?> /><?php echo JText::_('show'); ?>
							&nbsp;&nbsp;&nbsp;<input type="radio" name="sf_month"
													 value="0" <?php echo $row->sf_month == '0' ? 'checked' : ''; ?> /><?php echo JText::_('hide'); ?>
						</td>
					</tr>
				</table>
			</td>
			<td valign="top">
				<table width="100%">
					<tr>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('workgroup')); ?>"><?php echo JText::_('workgroup'); ?></span>
						</td>
						<td align="left"><input type="radio" name="groupby"
												value="WK" <?php echo $row->groupby == 'WK' ? 'checked' : ''; ?> /></td>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('category')); ?>"><?php echo JText::_('category'); ?></span>
						</td>
						<td align="left"><input type="radio" name="groupby"
												value="CA" <?php echo $row->groupby == 'CA' ? 'checked' : ''; ?> /></td>
					</tr>
					<tr>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('client')); ?>"><?php echo JText::_('client'); ?></span>
						</td>
						<td align="left"><input type="radio" name="groupby"
												value="CL" <?php echo $row->groupby == 'CL' ? 'checked' : ''; ?> /></td>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('priority')); ?>"><?php echo JText::_('priority'); ?></span>
						</td>
						<td align="left"><input type="radio" name="groupby"
												value="PR" <?php echo $row->groupby == 'PR' ? 'checked' : ''; ?> /></td>
					</tr>
					<tr>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('status')); ?>"><?php echo JText::_('status'); ?></span>
						</td>
						<td align="left"><input type="radio" name="groupby"
												value="ST" <?php echo $row->groupby == 'ST' ? 'checked' : ''; ?> /></td>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('source')); ?>"><?php echo JText::_('source'); ?></span>
						</td>
						<td align="left"><input type="radio" name="groupby"
												value="SO" <?php echo $row->groupby == 'SO' ? 'checked' : ''; ?> /></td>
					</tr>
					<tr>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('year')); ?>"><?php echo JText::_('year'); ?></span>
						</td>
						<td align="left"><input type="radio" name="groupby"
												value="YR" <?php echo $row->groupby == 'YR' ? 'checked' : ''; ?> /></td>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('month')); ?>"><?php echo JText::_('month'); ?></span>
						</td>
						<td align="left"><input type="radio" name="groupby"
												value="MO" <?php echo $row->groupby == 'MO' ? 'checked' : ''; ?> /></td>
					</tr>
					<tr>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('day')); ?>"><?php echo JText::_('day'); ?></span>
						</td>
						<td align="left"><input type="radio" name="groupby"
												value="DY" <?php echo $row->groupby == 'DY' ? 'checked' : ''; ?> /></td>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('weekday')); ?>"><?php echo JText::_('weekday'); ?></span>
						</td>
						<td align="left"><input type="radio" name="groupby"
												value="WD" <?php echo $row->groupby == 'WD' ? 'checked' : ''; ?> /></td>
					</tr>
					<tr>
						<td nowrap valign="top" class="key">
							<span rel="tooltip"
								  data-original-title="<?php echo htmlspecialchars(JText::_('tkt_chng_stat_nfy_sup')); ?>"><?php echo JText::_('tkt_chng_stat_nfy_sup'); ?></span>
						</td>
						<td align="left"><input type="radio" name="groupby"
												value="AS" <?php echo $row->groupby == 'AS' ? 'checked' : ''; ?> /></td>
						<td width="100"></td>
						<td align="left"></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<input type="hidden" name="option" value="com_maqmahelpdesk"/>
	<input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
	<input type="hidden" name="task" value=""/>
	</form>
	<?php
	}

	static function showReport($report, $sql, $lists, $f_year, $f_month, $f_status, $f_priority, $f_category, $f_workgroup, $f_client, $f_user, $f_staff, $sub_os, $label1)
	{
		$database = JFactory::getDBO();
		$user = JFactory::getUser();

		include_once(JPATH_SITE . '/components/com_maqmahelpdesk/includes/baaGrid.php'); ?>

	<script language="javascript" type="text/javascript">
		var originalOrderOS = '<?php echo $f_user; ?>';
		var originalPos = '<?php echo $f_client; ?>';

		var ordersOS = new Array();
			<?php
			$i = 0;
			foreach ($sub_os as $k => $items) {
				foreach ($items as $v) {
					echo "\n	ordersOS[" . $i++ . "] = new Array( '" . $v->value . "', '" . $k . "', '" . $v->text . "' );";
				}
			} ?>
	</script>

	<form id="adminForm" name="adminForm" action="index.php" method="POST">
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="task" value="reports_builderreport"/>
		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="id" value="<?php echo $report->id; ?>"/>

		<?php
		$GLOBALS['title_showReport'] = $report->title;
		?>

		<?php
		$print = JRequest::getVar('print', 0, '', 'int');
		if ($print == 1) {
			?>
			<h2 class="contentheading"> <?php echo $report->title; ?>	</h2>
			<?php
		} else {
			?>
			<table class="adminheading">
				<tr>
					<td align="right">
						<table>
							<?php if ($report->sf_year) { ?>
							<td><?php echo $lists['year']; ?></td><?php } ?>
							<?php if ($report->sf_month) { ?>
							<td><?php echo $lists['month']; ?></td><?php } ?>
							<?php if ($report->sf_workgroup) { ?>
							<td><?php echo $lists['workgroup'];?></td><?php } ?>
							<?php if ($report->sf_category) { ?>
							<td><?php echo $lists['category']; ?></td><?php } ?>
							<?php if ($report->sf_priority) { ?>
							<td><?php echo $lists['priority']; ?></td><?php } ?>
							<?php if ($report->sf_status) { ?>
							<td><?php echo $lists['status'];?></td><?php } ?>
							<?php if ($report->sf_assign) { ?>
							<td><?php echo $lists['assign']; ?></td><?php } ?>
							<?php if ($report->sf_client) { ?>
							<td><?php echo $lists['client']; ?></td><?php } ?>
							<?php if ($report->sf_user) { ?>
							<td>
								<script language="javascript" type="text/javascript">
									<!--
									writeDynaList('class="inputbox" name="f_user" size="1"', ordersOS, originalPos, originalPos, originalOrderOS);
									//-->
								</script>
							</td>
							<?php } ?>
							<td><input type="submit" name="submit" class="btn btn-success" value="Filter"></td>
						</table>
				</tr>
			</table>
			<br/>
			<?php
		}
		?>

		<?php	if ($report->type == 1) {
		ob_start();
		$grid = new baaGrid ($sql, DB_MYSQL);
		$grid->setTableAttr('class="table table-striped table-bordered" ');
		$grid->setTotal(1, 0);
		$grid->setDateFormat(_DATE_FORMAT);
		$grid->showErrors(1);
		$grid->display();
		$grid_html = ob_get_contents();
		ob_end_clean();
	} elseif ($report->type == 2) {
		$database->setQuery($sql);
		$rows = $database->loadAssocList();

		ob_start();
		$total = 0; ?>
		<table width="100%" class="adminlist">
			<thead>
			<tr>
				<th>&nbsp;</th>
				<th><?php echo JText::_('times'); ?></th>
			</tr>
			</thead>
			<tbody><?php
				for ($i = 0; $i < count($rows); $i++) {
					$row = $rows[$i];
					if ($row[0] != '') {
						$total = $total + $row[1]; ?>
					<tr>
						<td><?php echo $row[0]; ?></td>
						<td style="text-align: right"><?php echo HelpdeskDate::ConvertDecimalsToHoursMinutes($row[1]); ?></td>
					</tr><?php
					}
				} ?>
			</tbody>
			<tfoot>
			<tr>
				<th>&nbsp;</th>
				<th style="text-align: right"><?php echo HelpdeskDate::ConvertDecimalsToHoursMinutes($total); ?></th>
			</tr>
			</tfoot>
		</table><?php
		$grid_html = ob_get_contents();
		ob_end_clean();
	}

		$chart_html = '';

		$database->setQuery($sql);
		$rows = $database->loadAssocList();

		$database->setQuery("SELECT FOUND_ROWS()");
		$columns = $database->loadResult();

		echo '<script type="text/javascript" src="' . JURI::root() . 'components/com_maqmahelpdesk/includes/amcharts/ampie/swfobject.js"></script>';

		switch ($report->chart_type) {
			case 'pie':
				ob_start(); ?>
					<script type="text/javascript" defer="defer">
						// <![CDATA[
						var so = new SWFObject("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/ampie/ampie.swf", "ampie", "<?php echo $report->chart_width; ?>", "<?php echo $report->chart_height; ?>", "8", "#FFFFFF");
						so.addVariable("path", "<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/ampie/");
						so.addVariable("settings_file", encodeURIComponent("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/ampie/ampie_settings.xml"));				// you can set two or more different settings files here (separated by commas)
						so.addVariable("chart_data", encodeURIComponent("%SERIES%"));
						so.addVariable("loading_data", "LOADING DATA");												 // you can set custom "loading data" text here
						so.addVariable("preloader_color", "#999999");
						so.addParam("wmode", "transparent");
						so.write("%CHARTNAME%");
						// ]]>
					</script><?php
				$chart_html = ob_get_contents();
				ob_end_clean();
				break;
			case 'column':
				ob_start(); ?>
					<script type="text/javascript" defer="defer">
						// <![CDATA[
						var so = new SWFObject("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amcolumn/amcolumn.swf", "amcolumn", "<?php echo $report->chart_width; ?>", "<?php echo $report->chart_height; ?>", "8", "#FFFFFF");
						so.addVariable("path", "<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amcolumn/");
						so.addVariable("settings_file", encodeURIComponent("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amcolumn/amcolumn_settings.xml"));				// you can set two or more different settings files here (separated by commas)
						//so.addVariable("additional_chart_settings", encodeURIComponent("<settings><graphs>%LABELS%</graphs></settings>"));	  // you can append some chart settings to the loaded ones
						so.addVariable("chart_data", encodeURIComponent("%SERIES%"));
						so.addVariable("loading_data", "LOADING DATA");												 // you can set custom "loading data" text here
						so.addVariable("preloader_color", "#999999");
						so.addParam("wmode", "transparent");
						so.write("%CHARTNAME%");
						// ]]>
					</script><?php
				$chart_html = ob_get_contents();
				ob_end_clean();
				break;
			case 'bar':
				ob_start(); ?>
					<script type="text/javascript" defer="defer">
						// <![CDATA[
						var so = new SWFObject("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amline/amline.swf", "amline", "<?php echo $report->chart_width; ?>", "<?php echo $report->chart_height; ?>", "8", "#FFFFFF");
						so.addVariable("path", "<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amline/");
						so.addVariable("settings_file", encodeURIComponent("<?php echo JURI::root();?>components/com_maqmahelpdesk/includes/amcharts/amline/amline_settings.xml"));				// you can set two or more different settings files here (separated by commas)
						so.addVariable("additional_chart_settings", encodeURIComponent("<settings><graphs>%LABELS%</graphs></settings>"));	  // you can append some chart settings to the loaded ones
						so.addVariable("chart_data", encodeURIComponent("%SERIES%"));
						so.addVariable("loading_data", "LOADING DATA");												 // you can set custom "loading data" text here
						so.addVariable("preloader_color", "#999999");
						so.addParam("wmode", "transparent");
						so.write("%CHARTNAME%");
						// ]]>
					</script><?php
				$chart_html = ob_get_contents();
				ob_end_clean();
				break;
		}

		$div_id = trim(str_replace(' ', '_', $report->title));
		$series = '';
		$label = "<graph id='0'><title>" . ($report->type == 2 ? JText::_('times') : JText::_('tickets')) . "</title><color>#0D8ECF</color><bullet>round</bullet></graph>";

		for ($i = 0, $n = sizeof($rows); $i < $n; $i++) {
			$row = &$rows[$i];

			if ($row[$label1] != '') {
				$series .= $row[$label1] . ';' . $row[JText::_('tickets')] . '\n';
			}
		}

		$chart_html = '<div id="' . $div_id . '"><strong>You need to upgrade your Flash Player</strong></div>' . str_replace("%SERIES%", $series, str_replace("%CHARTNAME%", $div_id, str_replace("%LABELS%", $label, $chart_html))); ?>

		<table class="admintable" width="100%">
			<?php	if ($report->layout == 1) {
			echo '<tr><td valign="top">' . $grid_html . '</td></tr>';
			echo '<tr><td align="center">' . $chart_html . '</td></tr>';

		} elseif ($report->layout == 2) {
			echo '<tr><td>' . $chart_html . '</td></tr>';
			echo '<tr><td valign="top"  align="center">' . $grid_html . '</td></tr>';

		} elseif ($report->layout == 3) {
			echo '<tr><td width="50%" valign="top">' . $grid_html . '</td>';
			echo '<td width="50%"  align="center">' . $chart_html . '</td></tr>';

		} elseif ($report->layout == 4) {
			echo '<tr><td width="50%">' . $chart_html . '</td>';
			echo '<td width="50%" valign="top">' . $grid_html . '</td></tr>';

		} ?>
		</table>
	</form>
	<?php
	}

	static function ClientMonth($year, $month, $id_workgroup, $id_client, $f_status, $f_customfields, $lists, $print)
	{
		$database = JFactory::getDBO();
		include(JPATH_SITE . '/components/com_maqmahelpdesk/includes/reports.php'); ?>

	<form id="adminForm" name="adminForm" action="index.php">
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="task" value="reports"/>
		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="report" value="clientm"/>
		<table class="adminheading" border="0">
			<tr>
				<?php		if (!$print) { ?>
				<td><?php echo $lists['workgroup']; ?></td>
				<td><?php echo $lists['client']; ?></td>
				<td><?php echo $lists['status']; ?></td>
				<td><?php echo $lists['month']; ?></td>
				<td><?php echo $lists['year']; ?></td>
				<td><?php echo $lists['showcustomfields']; ?></td>
				<?php } ?>
			</tr>
		</table>
		<table class="admintable">
			<tr>
				<td>
					<table width="100%"><?php
						$reporting = new SupportReports();
						echo $reporting->ClientTickets($year, $month, $id_workgroup, $id_client, $f_status, $f_customfields, ''); ?>
					</table>
				</td>
			</tr>
		</table>
	</form>
	<?php
	}

	static function ClientMonthDetail($year, $month, $id_workgroup, $id_client, $f_status, $f_customfields, $lists, $print)
	{
		$database = JFactory::getDBO();
		include(JPATH_SITE . '/components/com_maqmahelpdesk/includes/reports.php'); ?>

	<form id="adminForm" name="adminForm" action="index.php">
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="task" value="reports"/>
		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="report" value="clientmdetail"/>
		<table class="adminheading" border="0">
			<tr>
				<?php		if (!$print) { ?>
				<td><?php echo $lists['workgroup']; ?></td>
				<td><?php echo $lists['client']; ?></td>
				<td><?php echo $lists['status']; ?></td>
				<td><?php echo $lists['month']; ?></td>
				<td><?php echo $lists['year']; ?></td>
				<td><?php echo $lists['showcustomfields']; ?></td>
				<?php } ?>
			</tr>
		</table>
		<table class="admintable">
			<tr>
				<td>
					<table width="100%"><?php
						$reporting = new SupportReports();
						echo $reporting->ClientTickets($year, $month, $id_workgroup, $id_client, $f_status, $f_customfields, 1); ?>
					</table>
				</td>
			</tr>
		</table>
	</form>
	<?php
	}

	static function StatusReport($year, $month, $id_workgroup, $id_client, $f_status, $f_staff, $lists, $print)
	{
		$database = JFactory::getDBO();
		include(JPATH_SITE . '/components/com_maqmahelpdesk/includes/reports.php'); ?>

	<form id="adminForm" name="adminForm" action="index.php">
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="task" value="reports"/>
		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="report" value="status"/>
		<table class="adminheading" border="0">
			<tr>
				<?php		if (!$print) { ?>
				<td><?php echo $lists['month']; ?></td>
				<td><?php echo $lists['year']; ?></td>
				<td><?php echo $lists['status']; ?></td>
				<td><?php echo $lists['workgroup']; ?></td>
				<td><?php echo $lists['assign']; ?></td>
				<?php } ?>
			</tr>
		</table>
		<table class="admintable">
			<tr>
				<td>
					<table width="100%"><?php
						$reporting = new SupportReports();
						echo $reporting->StatusTickets($year, $month, $id_workgroup, $id_client, $f_status, $f_staff, '', 1); ?>
					</table>
				</td>
			</tr>
		</table>
	</form>
	<?php
	}

	static function TicketMonth($year, $month, $id_workgroup, $id_client, $lists, $print)
	{
		$database = JFactory::getDBO();
		include(JPATH_SITE . '/components/com_maqmahelpdesk/includes/reports.php'); ?>

	<form id="adminForm" name="adminForm" action="index.php">
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="task" value="reports"/>
		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="report" value="clientm"/>
		<table class="adminheading" border="0">
			<tr>
				<?php		if (!$print) { ?>
				<td><?php echo $lists['workgroup']; ?></td>
				<td><?php echo $lists['client']; ?></td>
				<td><?php echo $lists['month']; ?></td>
				<td><?php echo $lists['year']; ?></td>
				<?php } ?>
			</tr>
		</table>
		<table class="admintable">
			<tr>
				<td>
					<table width="100%"><?php
						$reporting = new SupportReports();
						echo $reporting->TicketMonth($year, $month, $id_workgroup, $id_client, ''); ?>
					</table>
				</td>
			</tr>
		</table>
	</form>
	<?php
	}

	static function DueDate($id_workgroup, $id_client, $lists, $print)
	{
		include(JPATH_SITE . '/components/com_maqmahelpdesk/includes/reports.php'); ?>

		<form id="adminForm" name="adminForm" action="index.php">
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" name="task" value="reports"/>
			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="report" value="duedate"/>
			<table class="adminheading" border="0">
				<tr>
					<!--th class="reports">< ?php echo JText::_('duedate_report'); ?></th-->
					<?php		if (!$print) { ?>
					<td><?php echo $lists['workgroup']; ?></td>
					<td><?php echo $lists['client']; ?></td>
					<?php } ?>
				</tr>
			</table>
			<table class="adminform">
				<tr>
					<td>
						<table width="100%"><?php
							$reporting = new SupportReports();
							echo $reporting->DueDates($id_workgroup, $id_client); ?>
						</table>
					</td>
				</tr>
			</table>
		</form><?php
	}
}
