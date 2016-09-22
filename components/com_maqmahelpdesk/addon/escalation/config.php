<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: config.php 652 2012-05-24 19:42:09Z pdaniel $
 * $LastChangedDate: 2012-05-24 20:42:09 +0100 (Qui, 24 Mai 2012) $
 *
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Include helpers
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/client.php';
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/contracts.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/department.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/priority.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/status.php';

// Set toolbar and page title
HelpdeskContractsAdminHelper::addToolbar($task, 'escalation');
HelpdeskContractsAdminHelper::setDocument($task);

switch ($task)
{
	default:
	case 'config':
		escalationShowConfig();
		break;

	case 'saveconfig':
		escalationSaveConfig();
		break;

	case 'delete':
		escalationDeleteConfig();
		break;
}

function getCategories($id_workgroup = 0, $id_category = 0, $echo = true)
{
	$database = JFactory::getDBO();

	$id_workgroup = JRequest::getInt('id_workgroup', $id_workgroup);
	$id_category = JRequest::getInt('id_category', $id_category);

	// Build Workgroups select list
	$sql = "SELECT `id` AS value, `name` AS text
			FROM `#__support_category`
			WHERE `id_workgroup`=" . (int)$id_workgroup . " AND `show`=1 AND `tickets`=1
			ORDER BY `name`";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();
	$rows = array_merge(array(JHTML::_('select.option', '0', JText::_('selectlist'))), $rows);
	$list = JHTML::_('select.genericlist', $rows, 'id_category', 'class="inputbox" size="1"', 'value', 'text', $id_category);

	if ($echo) {
		echo $list;
	} else {
		return $list;
	}
}

function escalationDeleteConfig()
{
	$database = JFactory::getDBO();

	$database->setQuery("DELETE FROM #__support_escalation_config WHERE id=" . $database->quote($_GET['id']));
	$database->query();

	escalationShowConfig('<p>' . JText::_('sla_rule_deleted') . '</p>');
}

function escalationShowConfig($message = '')
{
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();
	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	$database->setQuery("SELECT * FROM #__support_escalation_config ORDER BY ordering");
	$rows = $database->loadObjectList();

	// Build Workgroup select list
	$sql = "SELECT `id` AS value, `wkdesc` AS text FROM #__support_workgroup";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', '- Select -')), $rows_wk);
	$lists['workgroup'] = JHTML::_('select.genericlist', $rows_wk, 'id_workgroup', 'class="inputbox" size="1" onchange="GetCategories();"', 'value', 'text', '0');

	// Build Assign To select list
	$sql = "SELECT DISTINCT(u.`id`) AS value, u.`name` AS text FROM #__users u, #__support_permission p WHERE u.id=p.id_user ORDER BY u.name";
	$database->setQuery($sql);
	$assign_wk = $database->loadObjectList();
	$assign_wk = array_merge(array(JHTML::_('select.option', '0', '- Select -')), $assign_wk);
	$lists['assign'] = JHTML::_('select.genericlist', $assign_wk, 'id_assign', 'class="inputbox" size="1"', 'value', 'text', '0');

	// JP 10.02.2009 status and assign trigger
	$lists['assign_trigger'] = JHTML::_('select.genericlist', $assign_wk, 'id_assign_trigger', 'class="inputbox" size="1"', 'value', 'text', '0');

	// Build Priority select list
	$sql = "SELECT `id` AS value, `description` AS text FROM #__support_priority WHERE `show`='1' ORDER BY description";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', '- Select -')), $rows_wk);
	$lists['priority'] = JHTML::_('select.genericlist', $rows_wk, 'id_priority', 'class="inputbox" size="1"', 'value', 'text', '0');

	// Build Priority select list
	$sql = "SELECT `id` AS value, `description` AS text FROM #__support_status WHERE `show`='1' ORDER BY description";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', '- Select -')), $rows_wk);
	$lists['status'] = JHTML::_('select.genericlist', $rows_wk, 'id_status', 'class="inputbox" size="1"', 'value', 'text', '0');

	// Status and assign trigger
	$lists['status_trigger'] = JHTML::_('select.genericlist', $rows_wk, 'id_status_trigger', 'class="inputbox" size="1"', 'value', 'text', '0');

	// Build the categories list
	$lists['category'] = getCategories(0, 0, false);

	// Build Client select list
	$sql = "SELECT `id` AS value, `clientname` AS text FROM #__support_client ORDER by clientname";
	$database->setQuery($sql);
	$rows_wk = $database->loadObjectList();
	$rows_wk = array_merge(array(JHTML::_('select.option', '0', '- Client -')), $rows_wk);
	$lists['client'] = JHTML::_('select.genericlist', $rows_wk, 'id_client', 'class="inputbox" size="1" onchange="changeDynaList(\'id_user\', ordersOS, document.adminForm.id_client.options[document.adminForm.id_client.selectedIndex].value, originalPos, originalOrderOS);"', 'value', 'text', '0');

	$sub_os = array();

	for ($i = 0, $n = count($rows_wk); $i < $n; $i++) {
		$wkrow = &$rows_wk[$i];
		$sub_os[0][] = JHTML::_('select.option', $wkrow->value, '- User -');
	}

	// Build Users select list
	$sql = "SELECT u.`id` AS value, u.`name` AS text, c.id_client FROM #__users u, #__support_client_users c WHERE c.id_user=u.id ORDER BY u.name";
	$database->setQuery($sql);
	$rows_subcat = $database->loadObjectList();
	$allusers = '';
	for ($i = 0, $n = count($rows_subcat); $i < $n; $i++) {
		$sub_os[$rows_subcat[$i]->value][] = JHTML::_('select.option', $rows_subcat[$i]->id_client, addslashes($rows_subcat[$i]->text));
		$allusers .= $rows_subcat[$i]->value . ',';
	}
	$allusers = JString::substr($allusers, 0, strlen($allusers) - 1);
	$sql = "SELECT u.`id` AS value, u.`name` AS text FROM #__users u " . ($allusers != '' ? "WHERE u.id NOT IN (" . $database->quote($allusers) . ")" : '') . " ORDER BY u.name";
	$database->setQuery($sql);
	$rows_subcat = $database->loadObjectList();
	for ($i = 0, $n = count($rows_subcat); $i < $n; $i++) {
		$sub_os[$rows_subcat[$i]->value][] = JHTML::_('select.option', 0, addslashes($rows_subcat[$i]->text));
	} ?>

<script language="javascript" text="text/javascript">
	function GetCategories() {
		$jMaQma.ajax({
			url:"index.php?option=com_maqmahelpdesk&task=mail_categories&id_workgroup=" + $jMaQma("#id_workgroup").val() + "&format=raw",
			success:function (data) {
				$jMaQma("#categoryField").html(data);
			}
		});
	}

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
</script><?php

	if ($message != '') {
		echo HelpdeskUtility::ShowSCMessage($message) . '<br />';
	} ?>

<form name="adminForm" method="POST" action="index.php">

	<div class="tabbable tabs-left contentarea">
		<ul class="nav nav-tabs equalheight">
			<li class="active"><a href="#tab1" data-toggle="tab"><img
				src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/config.png"
				border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('existing_rules');?></a></li>
			<li><a href="#tab2" data-toggle="tab"><img
				src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/add.png"
				border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('add_rule');?></a></li>
		</ul>
		<div class="tab-content contentbar withleft">
			<div id="tab1" class="tab-pane active equalheight">
				<?php if (count($rows)):?>
				<table class="table table-striped table-bordered ontop">
					<thead>
					<tr>
						<th class="title" colspan="4"><?php echo JText::_('triggers'); ?></th>
						<th class="title" colspan="3"><?php echo JText::_('filters'); ?></th>
						<th class="title" colspan="5"><?php echo JText::_('actions'); ?></th>
						<th class="title" width="20"></th>
					</tr>
					<tr>
						<th class="title"><?php echo JText::_('days_without_reply'); ?></th>
						<th class="title"><?php echo JText::_('days_opened'); ?></th>
						<th class="title"><?php echo JText::_('status'); ?></th>
						<th class="title"><?php echo JText::_('assignment'); ?></th>
						<th class="title"><?php echo JText::_('workgroups'); ?></th>
						<th class="title"><?php echo JText::_('clients'); ?></th>
						<th class="title"><?php echo JText::_('user'); ?></th>
						<th class="title"><?php echo JText::_('assignment'); ?></th>
						<th class="title"><?php echo JText::_('priority'); ?></th>
						<th class="title"><?php echo JText::_('status'); ?></th>
						<th class="title"><?php echo JText::_('category'); ?></th>
						<th class="title" width="20"><?php echo JText::_('ordering'); ?></th>
						<th class="title" width="20"></th>
					</tr>
					</thead>
					<tbody><?php
						$k = 0;
						for ($i = 0; $i < count($rows); $i++) {
							$row = $rows[$i];

							print '<tr class="row' . $k . '">';
							print '<td>' . $row->days_reply . '</td>';
							print '<td>' . $row->days_open . '</td>';
							print '<td>' . HelpdeskStatus::GetName($row->id_status_trigger) . '</td>';
							print '<td>' . HelpdeskUser::GetName($row->id_assign_trigger) . '</td>';
							print '<td>' . HelpdeskDepartment::GetName($row->id_workgroup) . '</td>';
							print '<td>' . HelpdeskClient::GetName($row->id_user) . '</td>';
							print '<td>' . HelpdeskUser::GetName($row->id_user) . '</td>';
							print '<td>' . HelpdeskUser::GetName($row->id_assign) . '</td>';
							print '<td>' . HelpdeskPriority::GetName($row->id_priority) . '</td>';
							print '<td>' . HelpdeskStatus::GetName($row->id_status) . '</td>';
							print '<td>' . HelpdeskCategory::GetName($row->id_category) . '</td>';
							print '<td width="20">' . $row->ordering . '</td>';
							print '<td width="20"><a href="' . JRoute::_('index.php?option=com_maqmahelpdesk&task=addon-escalation_delete&addonfile=config&id=' . $row->id) . '"><img src="../media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/delete.png" border="0" alt="Delete" /></a></td>';
							print '</tr>';

							$k = 1 - $k;
						} ?>
					</tbody>
				</table>
				<?php else:?>
				<div class="detailmsg">
					<h1><?php echo JText::_('register_not_found'); ?></h1>
				</div>
				<?php endif;?>
			</div>
			<div id="tab2" class="tab-pane equalheight">
				<p style="margin-top:10px;"><?php echo JText::_('order'); ?>: <input type="text" class="inputbox"
																					 name="ordering" value="" size="4"
																					 maxlength="2"/></p>

				<table class="table table-striped table-bordered ontop">
					<thead>
					<tr>
						<th class="title"><?php echo JText::_('triggers'); ?></th>
						<th class="title"><?php echo JText::_('filters'); ?></th>
						<th class="title"><?php echo JText::_('actions'); ?></th>
					</tr>
					</thead>
					<tr>
						<td valign="top">
							<table class="admintable" cellspacing="1" width="100%">
								<tr>
									<td nowrap valign="top" class="key">
										<span class="editlinktip hasTip"
											  title="<?php echo htmlspecialchars(JText::_('days_wo_sup_reply') . '::' . JText::_('ticket_days_wo_replay_tooltip')); ?>"><?php echo JText::_('days_wo_sup_reply'); ?></span>
									</td>
									<td>
										<input type="text" class="inputbox" name="days_reply" value="0" size="5"
											   maxlength="2"/>
									</td>
								</tr>
								<tr>
									<td nowrap valign="top" class="key">
										<span class="editlinktip hasTip"
											  title="<?php echo htmlspecialchars(JText::_('days_ticket_s_open') . '::' . JText::_('ticket_days_open_escalation_tooltip')); ?>"><?php echo JText::_('days_ticket_s_open'); ?></span>
									</td>
									<td>
										<input type="text" class="inputbox" name="days_open" value="0" size="5"
											   maxlength="2"/>
									</td>
								</tr>

								<!-- status and assign trigger -->
								<tr>
									<td nowrap valign="top" class="key">
										<span class="editlinktip hasTip"
											  title="<?php echo htmlspecialchars(JText::_('ticket_status') . '::' . JText::_('ticket_status_tootip')); ?>"><?php echo JText::_('ticket_status'); ?></span>
									</td>
									<td><?php echo $lists['status_trigger']; ?></td>
								</tr>
								<tr>
									<td nowrap valign="top" class="key">
										<span class="editlinktip hasTip"
											  title="<?php echo htmlspecialchars(JText::_('ticket_assignment') . '::' . JText::_('ticket_assignment_tooltip')); ?>"><?php echo JText::_('ticket_assignment'); ?></span>
									</td>
									<td><?php echo $lists['assign_trigger']; ?></td>
								</tr>
								<tr>
									<td colspan="2">* <?php echo JText::_('trigger_once'); ?></td>
								</tr>

							</table>
						</td>
						<td valign="top">
							<table class="admintable" cellspacing="1" width="100%">
								<tr>
									<td nowrap valign="top" class="key"><?php echo JText::_('workgroups'); ?>:</td>
									<td><?php echo $lists['workgroup']; ?></td>
								</tr>
								<tr>
									<td nowrap valign="top" class="key"><?php echo JText::_('clients'); ?>:</td>
									<td><?php echo $lists['client']; ?></td>
								</tr>
								<tr>
									<td nowrap valign="top" class="key"><?php echo JText::_('user'); ?>:</td>
									<td>
										<script language="javascript" type="text/javascript">
											<!--
											writeDynaList('class="inputbox" name="id_user" size="1"', ordersOS, originalPos, originalPos, originalOrderOS);
											//-->
										</script>
									</td>
								</tr>
								<tr>
									<td colspan="2">* <?php echo JText::_('workgroup_required'); ?></td>
								</tr>

							</table>
						</td>
						<td valign="top">
							<table class="admintable" cellspacing="1" width="100%">
								<tr>
									<td nowrap valign="top" class="key"><?php echo JText::_('ticket_assignment'); ?>:
									</td>
									<td><?php echo $lists['assign']; ?></td>
								</tr>
								<tr>
									<td nowrap valign="top" class="key"><?php echo JText::_('ticket_status'); ?>:</td>
									<td><?php echo $lists['status']; ?></td>
								</tr>
								<tr>
									<td nowrap valign="top" class="key"><?php echo JText::_('ticket_priority'); ?>:</td>
									<td><?php echo $lists['priority']; ?></td>
								</tr>
								<tr>
									<td nowrap valign="top" class="key"><?php echo JText::_('ticket_category'); ?>:</td>
									<td id="categoryField"><?php echo $lists['category']; ?></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<input type="hidden" name="option" value="com_maqmahelpdesk"/>
	<input type="hidden" name="task" value="addon-escalation_saveconfig"/>
	<input type="hidden" name="addonfile" value="config"/>
</form>

<script type='text/javascript'>
	$jMaQma(document).ready(function () {
		$jMaQma(".equalheight").equalHeights();
	});
</script><?php
}

function escalationSaveConfig()
{
	$id_assign = $_POST['id_assign_trigger'];
	$id_status = $_POST['id_status_trigger'];
	$days_open = $_POST['days_open'];
	$days_reply = $_POST['days_reply'];

	if ((int) $days_reply > 0 || (int) $days_open > 0 || (int) $id_status > 0 || (int) $id_assign > 0)
	{
		$database = JFactory::getDBO();
		$sql = "INSERT INTO #__support_escalation_config(id_workgroup, id_assign, id_priority, id_category, id_status, id_client, id_user, days_reply, days_open, ordering, id_status_trigger, id_assign_trigger)
				VALUES(" . $database->quote($_POST['id_workgroup']) . ", " . $database->quote($_POST['id_assign']) . ", " . $database->quote($_POST['id_priority']) . ", " . $database->quote($_POST['id_category']) . ", " . $database->quote($_POST['id_status']) . ", " . $database->quote($_POST['id_client']) . ", " . $database->quote($_POST['id_user']) . ", " . $database->quote($days_reply) . ", " . $database->quote($days_open) . ", " . $database->quote($_POST['ordering']) . ", " . $database->quote($id_status) . ", " . $database->quote($id_assign) . ")";
		$database->setQuery($sql);
		$database->query();
	}

	escalationShowConfig('<p>' . JText::_('add_new_escalation_rule') . '</p>');
}
