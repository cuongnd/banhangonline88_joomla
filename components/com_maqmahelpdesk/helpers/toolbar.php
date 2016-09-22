<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package    MaQma_Helpdesk
 * @copyright  (C) 2006-2012 Components Lab, Lda.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class HelpdeskToolbar
{
	static function Create()
	{
		global $is_manager, $clientOptions, $usertype, $show_toolbar;

		$session = JFactory::getSession();
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$supportConfig = HelpdeskUtility::GetConfig();
		$workgroupSettings = HelpdeskDepartment::GetSettings();
		$is_support = HelpdeskUser::IsSupport();
		$is_client = HelpdeskUser::IsClient();

		// Not logged doesn't show
		if (!$user->id)
			return;

		// Check if it's show on this menu item
		if (!$show_toolbar)
			return;

		// Get variables
		$Itemid = JRequest::getInt('Itemid', 0);
		$id_workgroup = JRequest::getInt('id_workgroup', 0);
		$print = JRequest::getInt('print', 0);
		$tmpl = JRequest::getVar('tmpl', '', 'string');

		// Check if it's print to get out
		if ($print || $tmpl == 'component')
			return;

		// Get workgroups the user have permissions
		$wkids = $session->get('wkids', '', 'maqmahelpdesk');
		$sql = "SELECT id, wkdesc, wkabout, logo, shortdesc, `wkticket`, `bugtracker`, `enable_discussions`
				FROM #__support_workgroup
				WHERE id IN (" . $wkids . ") AND `show`='1'
				ORDER BY ordering, wkdesc";
		$database->setQuery($sql);
		$workgroups = $database->loadObjectList();

		// Any tickets enabled?
		$sql = "SELECT COUNT(*) AS TOTAL
				FROM #__support_workgroup
				WHERE `wkticket`=1 AND `show`=1";
		$database->setQuery($sql);
		$tickets_enabled = $database->loadResult();

		// Any discussions enabled?
		$sql = "SELECT COUNT(*) AS TOTAL
				FROM #__support_workgroup
				WHERE `enable_discussions`=1 AND `show`=1";
		$database->setQuery($sql);
		$discussions_enabled = $database->loadResult();

		// Any bugtrackers enabled?
		$sql = "SELECT COUNT(*) AS TOTAL
				FROM #__support_workgroup
				WHERE `bugtracker`=1 AND `show`=1";
		$database->setQuery($sql);
		$bugtrackers_enabled = $database->loadResult();

		// Get number of tickets
		$sql = "SELECT COUNT(*) AS TOTAL
				FROM #__support_ticket AS t
					 INNER JOIN #__support_status AS s ON s.id=t.id_status
				WHERE s.status_group='O'
				  AND t.id_workgroup IN (" . $wkids . ") " .
			(!$is_support ? ($is_client ? "AND t.id_client=" . $is_client : "AND t.id_user=" . $user->id) : "");
		$database->setQuery($sql);
		$tickets = $database->loadResult();

		// Get discussions without answers
		$sql = "SELECT COUNT(*)
				FROM `#__support_discussions`
				WHERE `published`=1
				  AND `status`=0
				  AND id_workgroup IN (" . $wkids . ") " .
			(!$is_support ? "AND id_user=" . $user->id : "");
		$database->setQuery($sql);
		$discussions = $database->loadResult();

		// Get tasks open for today or previous days
		$sql = "SELECT COUNT(*)
				FROM `#__support_task`
				WHERE `status`='O'
				  AND `date_time`<'" . date("Y-m-d 23:59:59") . "'
				  AND id_user IN (" . $user->id . ")";
		$database->setQuery($sql);
		$tasks = $database->loadResult();

		// Get bug trackers not RESOLVED or CLOSED if support agent or the open bugs opened by logged user
		$sql = "SELECT COUNT(*)
				FROM `#__support_bugtracker`
				WHERE `status`<>'C'
				  AND `status`<>'R' " .
			($is_support ? "AND (`id_assign`=0 OR `id_assign`=" . $user->id . ")" : "AND `id_user`=" . $user->id);
		$database->setQuery($sql);
		$bugs = $database->loadResult();

		// Get downloads for the user
		$sql = "SELECT COUNT(*)
				FROM `#__support_dl_access` AS a
					 INNER JOIN `#__support_dl` AS d ON d.`id`=a.`id_download`
					 INNER JOIN `#__support_client_users` AS cu ON cu.`id_client`=a.id_user
					 LEFT JOIN `#__support_dl_category` AS c ON c.`id`=d.`id_category`
				WHERE cu.`id_user`=" . $user->id . "
				ORDER BY c.`cname`, d.`pname`";
		$database->setQuery($sql);
		$downloads = $database->loadResult();

		$tickets_link = ($id_workgroup ? 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my' : 'javascript:;');
		$discussions_link = ($id_workgroup ? 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=discussions' : 'javascript:;');
		$bugtracker_link = ($id_workgroup ? 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker' : 'javascript:;'); ?>

		<div class="maqmahelpdesk">
	<div class="navbar">
	<div class="navbar-inner">
	<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
		<span class="ico-bar"></span>
		<span class="ico-bar"></span>
		<span class="ico-bar"></span>
	</button>
	<div class="nav-collapse collapse">
	<ul class="nav">
		<li class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle" href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>"><img
					src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/help.png" width="16"
					alt="<?php echo JText::_('workgroups');?>"/> <span class="visible-phone"><?php echo JText::_("WORKGROUPS");?></span> <b class="caret"></b></a>
			<ul class="dropdown-menu">
				<?php for ($i = 0; $i < count($workgroups); $i++): ?>
					<li><a
							href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $workgroups[$i]->id;?>&task=workgroup_view"><img
								src="<?php echo ($workgroups[$i]->logo != '' ? 'media/com_maqmahelpdesk/images/logos/' . $workgroups[$i]->logo : 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/workgroups.png')?>"
								width="16" alt="<?php echo $workgroups[$i]->wkdesc;?>"/> <?php echo $workgroups[$i]->wkdesc;?></a>
					</li>
				<?php endfor;?>
			</ul>
		</li>
		<!-- Tickets -->
		<?php if (($id_workgroup && $workgroupSettings->wkticket) || ($tickets_enabled && !$id_workgroup)): ?>
			<li class="dropdown">
				<a data-toggle="dropdown"
				   class="dropdown-toggle"
				   href="<?php echo $tickets_link;?>"><img
						src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/tickets.png"
						width="16"
						alt="<?php echo JText::_('tickets');?>"/> <?php echo JText::_('tickets');?>
					<?php if ((int) $tickets): ?>
						<span class="lbl lbl-important"><?php echo (int) $tickets;?></span>
					<?php endif; ?>
					<b class="caret"></b></a>
				<?php if (count($workgroups)): ?>
					<ul class="dropdown-menu">
						<?php for ($i = 0; $i < count($workgroups); $i++): ?>
							<?php if ($workgroups[$i]->wkticket): ?>
								<li><a
										href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $workgroups[$i]->id;?>&task=ticket_my"><?php echo $workgroups[$i]->wkdesc;?>
										<?php if (HelpdeskDepartment::Stats($workgroups[$i]->id, 'ticket') > 0):?>
											<span
												class="lbl lbl-important"><?php echo HelpdeskDepartment::Stats($workgroups[$i]->id, 'ticket');?></span>
										<?php endif;?>
									</a>
								</li>
							<?php endif;?>
						<?php endfor;?>
					</ul>
				<?php endif;?>
			</li>
		<?php endif;?>
		<!-- /Tickets -->
		<!-- Public discussions -->
		<?php if (($id_workgroup && $workgroupSettings->enable_discussions) || ($discussions_enabled && !$id_workgroup)): ?>
			<li class="dropdown">
				<a data-toggle="dropdown"
				   class="dropdown-toggle"
				   href="<?php echo $discussions_link;?>"><img
						src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/discussions.png"
						width="16"
						alt="<?php echo JText::_('discussions');?>"/> <?php echo JText::_('discussions');?>
					<?php if ((int)$discussions): ?>
						<span class="lbl lbl-important"><?php echo (int)$discussions;?></span>
					<?php endif; ?>
					<b class="caret"></b></a>
				<?php if (count($workgroups)): ?>
					<ul class="dropdown-menu">
						<?php for ($i = 0; $i < count($workgroups); $i++): ?>
							<?php if ($workgroups[$i]->enable_discussions): ?>
								<li><a
										href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $workgroups[$i]->id;?>&task=discussions"> <?php echo $workgroups[$i]->wkdesc;?>
										<?php if (HelpdeskDepartment::Stats($workgroups[$i]->id, 'discussion') > 0):?>
											<span
												class="lbl lbl-important"><?php echo HelpdeskDepartment::Stats($workgroups[$i]->id, 'discussion');?></span>
										<?php endif;?>
									</a>
								</li>
							<?php endif;?>
						<?php endfor;?>
					</ul>
				<?php endif;?>
			</li>
		<?php endif;?>
		<!-- /Public discussions -->
		<!-- Bug tracker -->
		<?php if (($id_workgroup && $workgroupSettings->bugtracker) || ($bugtrackers_enabled && !$id_workgroup)): ?>
			<li class="dropdown">
				<a data-toggle="dropdown"
				   class="dropdown-toggle"
				   href="<?php echo $bugtracker_link;?>"><img
						src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/bug.png"
						width="16"
						alt="<?php echo JText::_('bugtracker');?>"/> <?php echo JText::_('bugtracker');?>
					<?php if ((int)$bugs): ?>
						<span class="lbl lbl-important"><?php echo (int)$bugs;?></span>
					<?php endif; ?>
					<b class="caret"></b></a>
				<?php if (count($workgroups)): ?>
					<ul class="dropdown-menu">
						<?php for ($i = 0; $i < count($workgroups); $i++): ?>
							<?php if ($workgroups[$i]->bugtracker): ?>
								<li><a
										href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $workgroups[$i]->id;?>&task=bugtracker"> <?php echo $workgroups[$i]->wkdesc;?>
										<?php if(HelpdeskDepartment::Stats($workgroups[$i]->id, 'bugtracker') > 0):?>
											<span
												class="lbl lbl-important"><?php echo HelpdeskDepartment::Stats($workgroups[$i]->id, 'bugtracker');?></span>
										<?php endif;?>
									</a>
								</li>
							<?php endif;?>
						<?php endfor;?>
					</ul>
				<?php endif;?>
			</li>
		<?php endif;?>
		<!-- /Bug tracker -->
	</ul>

	<!-- My Account -->
	<?php if ($id_workgroup): ?>
		<ul class="nav pull-right">
			<?php if ($workgroupSettings->use_account || $is_support):
				$my_account = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=users_profile';
			else:
				$my_account = '#';
			endif;?>
			<li class="dropdown"><a data-toggle="dropdown"
			                        class="dropdown-toggle" href="#"><img
						src="<?php echo HelpdeskUser::GetAvatar($user->id);?>" width="16"
						alt="<?php echo JText::_('wk_profile');?>"/> <?php echo JText::_('wk_profile');?> <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<?php if($my_account != '#'):?>
					<li><a href="<?php echo $my_account;?>"><img
								src="<?php echo HelpdeskUser::GetAvatar($user->id);?>" width="16"
								alt="<?php echo JText::_('wk_profile');?>"/> <?php echo JText::_('wk_profile');?></a>
						<?php endif;?>
						<?php if ($user->id > 0 && $workgroupSettings->use_bookmarks) : ?>
					<li><a
							href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=my_bookmark"><img
								src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/bookmarks.png"
								width="16" alt="<?php echo JText::_('wk_bookmarks');?>"/> <?php echo JText::_('wk_bookmarks');?></a>
					</li>
				<?php endif;?>
					<?php if ($is_client > 0 && $clientOptions->manager): ?>
						<li><a
								href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=client_view&id=<?php echo $is_client;?>"><img
									src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/clients.png"
									width="16"
									alt="<?php echo JText::_('wk_client_profile');?>"/> <?php echo JText::_('wk_client_profile');?></a>
						</li>
					<?php endif;?>
					<?php if ($workgroupSettings->wkkb && $is_support): ?>
						<li><a
								href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=my_kb"><img
									src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/kb.png"
									width="16" alt="<?php echo JText::_('wk_myarticles');?>"/> <?php echo JText::_('wk_myarticles');?>
							</a></li>
					<?php endif;?>
					<?php if ($workgroupSettings->wkdownloads && $downloads): ?>
						<li><a
								href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=my_downloads"><img
									src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/files.png"
									width="16" alt="<?php echo JText::_('my_downloads');?>"/> <?php echo JText::_('my_downloads');?></a>
						</li>
					<?php endif;?>
					<?php if ($is_support): ?>
						<li><a
								href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=calendar_view"><img
									src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/calendar.png"
									width="16"
									alt="<?php echo JText::_('wk_tasks');?>"/> <?php echo JText::_('wk_tasks');?>
								<?php if ((int)$tasks): ?>
									<span class="lbl lbl-important"><?php echo (int)$tasks;?></span>
								<?php endif; ?></a></li>
						<?php if($supportConfig->manual_times):?>
							<li><a
									href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=timesheet_manage"><img
										src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/calendar.png"
										width="16"
										alt="<?php echo JText::_('TIMES');?>"/> <?php echo JText::_('TIMES');?></a></li>
						<?php endif;?>
					<?php endif;?>
					<?php if ($supportConfig->bbb_url != '' && $supportConfig->bbb_apikey): ?>
						<li><a
								href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=meetings"><img
									src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/meetings.png"
									width="16" alt="<?php echo JText::_('meetings');?>"/> <?php echo JText::_('meetings');?></a></li>
					<?php endif;?>
					<?php if ($is_support && $workgroupSettings->use_activity): ?>
						<li><a
								href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=timesheet"><img
									src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/timesheet.png"
									width="16" alt="<?php echo JText::_('timesheet');?>"/> <?php echo JText::_('timesheet');?></a></li>
					<?php endif;?>
					<li><a
							href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_analysis"><img
								src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/table.png"
								width="16"
								alt="<?php echo JText::_('tickets_analysis');?>"/> <?php echo JText::_('tickets_analysis');?></a>
					</li>
					<?php if ($workgroupSettings->wkticket && (($supportConfig->unregister && !$user->id && $supportConfig->anonymous_tickets && !$workgroupSettings->contract) || $user->id)): ?>
						<?php if (($is_support && $usertype > 5) || ($is_client && $is_manager)): ?>
							<li><a
									href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_report"><img
										src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/table.png"
										width="16"
										alt="<?php echo JText::_('report_tickets');?>"/> <?php echo JText::_('report_tickets');?></a>
							</li>
						<?php endif; ?>
					<?php endif;?>
				</ul>
			</li>
			<!-- /My Account -->
			<!-- Creation shortcuts -->
			<?php if (!$is_support && $workgroupSettings->wkticket && (($supportConfig->unregister && !$user->id && $supportConfig->anonymous_tickets && !$workgroupSettings->contract) || $user->id)) : ?>
				<li><a
						href="<?php echo JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_new')?>"><img
							src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/add.png"
							width="16" alt="<?php echo JText::_('qk_create_ticket');?>"/> <?php echo JText::_('qk_create_ticket');?>
					</a>
				</li>
			<?php elseif ($is_support): ?>
				<li class="dropdown"><a data-toggle="dropdown"
				                        class="dropdown-toggle" href="javascript:;"><img
							src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/circle_add.png"
							width="16" alt="<?php echo JText::_('create');?>"/> <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<?php if ($workgroupSettings->wkticket) : ?>
							<li><a
									href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_new"><img
										src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/tickets.png"
										width="16" alt="<?php echo JText::_('ticket');?>"/> <?php echo JText::_('ticket');?></a></li>
						<?php endif;?>
						<?php if ($workgroupSettings->wkkb) : ?>
							<li><a
									href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=kb_new"><img
										src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/kb.png"
										width="16" alt="<?php echo JText::_('kb');?>"/> <?php echo JText::_('kb');?></a></li>
						<?php endif;?>
						<?php if ($workgroupSettings->wkglossary) : ?>
							<li><a
									href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=glossary_add"><img
										src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/glossary.png"
										width="16" alt="<?php echo JText::_('glossary');?>"/> <?php echo JText::_('glossary');?></a></li>
						<?php endif;?>
					</ul>
				</li>
			<?php endif;?>
			<!-- /Creation shortcuts -->
		</ul>
	<?php endif;?>
	<!-- /When in a department -->
	</div>
	</div>
	</div>
		</div><?php
	}
}
