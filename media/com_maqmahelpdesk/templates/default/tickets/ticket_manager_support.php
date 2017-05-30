<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_tickets_manager');?></h2>

	<form id="adminForm" name="adminForm" method="post" action="<?php echo JRoute::_("index.php");?>" class="well">
	    <?php echo JHtml::_('form.token'); ?>

	    <div id="filters">
		    <div class="row-fluid">
			    <div class="span12">
				    <a id="close-filters-advanced" href="javascript:;" onclick="SearchView('spl');"
				       class="filters-collapse"></a>
				    <span><?php echo JText::_('filters');?></span>
			    </div>
			</div>
		    <div class="row-fluid">
			    <div class="span4">
				    <small><?php echo JText::_('search');?>:</small>
				    <br/>
				    <input type="text" name="filter_search" class="inputbox" size="25"
				           value="<?php echo urldecode(htmlspecialchars($filter_search)); ?>"/>
				</div>
			    <div class="span4">
				    <small><?php echo JText::_('user');?>:</small>
				    <br/>
				    <input id="ac_me" name="ac_me" type="text" size="35" value="<?php echo $ac_me;?>"/>
				    <input type="hidden" id="filter_user" name="filter_user" value="<?php echo $filter_user; ?>"/>
				</div>
			    <div class="span4" id="adv-tview">
				    <small><?php echo JText::_('view');?>:</small>
				    <br/>
				    <?php echo $lists['tview'];?>
				</div>
			</div>
		    <div class="row-fluid">
			    <div class="span12">
				    <hr height="1" color="#c5c5c5" size="1"/>
				</div>
			</div>
		    <div class="row-fluid">
			    <div class="span6">
				    <div id="adv-options">
					    <div class="btn-group">
						    <a href="javascript:;" onclick="OpenViews();"
						       title="<?php echo JText::_('manage_views'); ?>"
						       class="btn"><?php echo JText::_('manage_views'); ?></a>
						    <a href="javascript:;" onclick="OpenTicket();" title="<?php echo JText::_('open_ticket');?>"
						       class="btn"><?php echo JText::_('open_ticket');?></a>
						    <a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid; ?>&id_workgroup=<?php echo $id_workgroup; ?>&task=ticket_new"
						       title="<?php echo JText::_('qk_create_ticket');?>"
						       class="btn"><?php echo JText::_('qk_create_ticket');?></a>
					    </div>
				    </div>
			    </div>
			    <div class="span6">
				    <div id="adv-buttons" class="btn-group" style="text-align:right;">
					    <button type="button" class="btn" onclick="javascript:resetFilter(1);"
					            title="<?php echo JText::_('defaults_filters');?>"><i class="ico-trash"></i> <?php echo JText::_('reset');?></button>
					    <button id="searchtickets" type="submit" class="btn btn-inverse"
					            title="<?php echo JText::_('search');?>"><i class="ico-search ico-white"></i> <?php echo JText::_('search');?></button>
				    </div>
			    </div>
		    </div>
	    </div>
	    <div id="filters-simple">
		    <div class="row-fluid">
			    <div class="span12">
				    <p><a id="close-filters-simple" href="javascript:;" onclick="SearchView('adv');" class="filters-expand"></a>
					    <span><?php echo JText::_('filters');?></span></p>
			    </div>
			</div>
		    <div class="row-fluid">
			    <div class="span12">
				    <div class="row-fluid">
					    <div id="filters1" class="span6"></div>
					    <div id="filters2" class="span6"></div>
					</div>
			    </div>
			</div>
	    </div>

	    <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	    <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>"/>
	    <input type="hidden" id="limit" name="limit" value="<?php echo $limit; ?>"/>
	    <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup; ?>"/>
	    <input type="hidden" name="order" value="<?php echo $order; ?>"/>
	    <input type="hidden" name="orderby" value="<?php echo $orderby; ?>"/>
	    <input type="hidden" name="task" value="ticket_my"/>
	</form>

	<br/>

	<?php if (count($tickets)):?>
		<div class="btn-toolbar" style="height:30px;">
			<div class="btn-group pull-right flrg">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
					<?php echo JText::_("ORDERING") . ": " . HelpdeskTicket::GetOrdering($orderby);?>
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
					<li><a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_my&tview=<?php echo $ticket_view;?>&orderby=t.ticketmask&order=<?php echo ($order == 'DESC' ? 'ASC' : 'DESC') . $filter_link;?>"><?php echo JText::_("TICKETID");?> - <?php echo ($order == 'ASC' ? JText::_("ORDER_ASC") : JText::_("ORDER_DESC"));?></a></li>
					<li><a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_my&tview=<?php echo $ticket_view;?>&orderby=t.subject&order=<?php echo ($order == 'DESC' ? 'ASC' : 'DESC') . $filter_link;?>"><?php echo JText::_("SUBJECT");?> - <?php echo ($order == 'ASC' ? JText::_("ORDER_ASC") : JText::_("ORDER_DESC"));?></a></li>
					<li><a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_my&tview=<?php echo $ticket_view;?>&orderby=t.date&order=<?php echo ($order == 'DESC' ? 'ASC' : 'DESC') . $filter_link;?>"><?php echo JText::_("CREATED_DATE");?> - <?php echo ($order == 'ASC' ? JText::_("ORDER_ASC") : JText::_("ORDER_DESC"));?></a></li>
					<li><a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_my&tview=<?php echo $ticket_view;?>&orderby=t.duedate&order=<?php echo ($order == 'DESC' ? 'ASC' : 'DESC') . $filter_link;?>"><?php echo JText::_("DUEDATE");?> - <?php echo ($order == 'ASC' ? JText::_("ORDER_ASC") : JText::_("ORDER_DESC"));?></a></li>
					<li><a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_my&tview=<?php echo $ticket_view;?>&orderby=t.last_update&order=<?php echo ($order == 'DESC' ? 'ASC' : 'DESC') . $filter_link;?>"><?php echo JText::_("LAST_UPDATE");?> - <?php echo ($order == 'ASC' ? JText::_("ORDER_ASC") : JText::_("ORDER_DESC"));?></a></li>
					<li><a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_my&tview=<?php echo $ticket_view;?>&orderby=w.wkdesc&order=<?php echo ($order == 'DESC' ? 'ASC' : 'DESC') . $filter_link;?>"><?php echo JText::_("WORKGROUP");?> - <?php echo ($order == 'ASC' ? JText::_("ORDER_ASC") : JText::_("ORDER_DESC"));?></a></li>
					<li><a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_my&tview=<?php echo $ticket_view;?>&orderby=s.description&order=<?php echo ($order == 'DESC' ? 'ASC' : 'DESC') . $filter_link;?>"><?php echo JText::_("STATUS");?> - <?php echo ($order == 'ASC' ? JText::_("ORDER_ASC") : JText::_("ORDER_DESC"));?></a></li>
					<li><a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_my&tview=<?php echo $ticket_view;?>&orderby=u2.name&order=<?php echo ($order == 'DESC' ? 'ASC' : 'DESC') . $filter_link;?>"><?php echo JText::_("TKT_CHNG_STAT_NFY_SUP");?> - <?php echo ($order == 'ASC' ? JText::_("ORDER_ASC") : JText::_("ORDER_DESC"));?></a></li>
					<li><a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_my&tview=<?php echo $ticket_view;?>&orderby=p.description&order=<?php echo ($order == 'DESC' ? 'ASC' : 'DESC') . $filter_link;?>"><?php echo JText::_("PRIORITY");?> - <?php echo ($order == 'ASC' ? JText::_("ORDER_ASC") : JText::_("ORDER_DESC"));?></a></li>
					<li><a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_my&tview=<?php echo $ticket_view;?>&orderby=cy.name&order=<?php echo ($order == 'DESC' ? 'ASC' : 'DESC') . $filter_link;?>"><?php echo JText::_("CATEGORY");?> - <?php echo ($order == 'ASC' ? JText::_("ORDER_ASC") : JText::_("ORDER_DESC"));?></a></li>
					<li><a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_my&tview=<?php echo $ticket_view;?>&orderby=c.clientname&order=<?php echo ($order == 'DESC' ? 'ASC' : 'DESC') . $filter_link;?>"><?php echo JText::_("CLIENT_TH");?> - <?php echo ($order == 'ASC' ? JText::_("ORDER_ASC") : JText::_("ORDER_DESC"));?></a></li>
					<li><a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_my&tview=<?php echo $ticket_view;?>&orderby=t.an_name&order=<?php echo ($order == 'DESC' ? 'ASC' : 'DESC') . $filter_link;?>"><?php echo JText::_("USER_NAME");?> - <?php echo ($order == 'ASC' ? JText::_("ORDER_ASC") : JText::_("ORDER_DESC"));?></a></li>
					<li><a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_my&tview=<?php echo $ticket_view;?>&orderby=t.an_mail&order=<?php echo ($order == 'DESC' ? 'ASC' : 'DESC') . $filter_link;?>"><?php echo JText::_("E_MAIL");?> - <?php echo ($order == 'ASC' ? JText::_("ORDER_ASC") : JText::_("ORDER_DESC"));?></a></li>
				</ul>
			</div>
		</div>

	    <table id="tickettable" class="table table-bordered table-striped" cellspacing="0">
	    <thead>
	    <tr>
	        <th nowrap="nowrap" width="20" class="customerview hidden-phone"><img src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/info.png" align="absmiddle" border="0" hspace="5" class="showPopoverRight" data-original-title="<?php echo JText::_('legend');?>" data-content="<img src='<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/flag-red.png' align='absmiddle' border='0' hspace='2' vspace='2'/> <?php echo JText::_('approve_pending');?><br /><img src='<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/filter.png' align='absmiddle' border='0' hspace='2' vspace='2'/> <?php echo JText::_('ticket_in_queue');?><br /><img src='<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/attach.png' align='absmiddle' border='0' hspace='2' vspace='2'/> <?php echo JText::_('attached_files');?><br /><img src='<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/alert.png' align='absmiddle' border='0' hspace='2' vspace='2'/> <?php echo JText::_('icon_onhold');?><br /><img src='<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/hour.png' align='absmiddle' border='0' hspace='2' vspace='2'/> <?php echo JText::_('icon_progress');?><br /><img src='<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/clock.png' align='absmiddle' border='0' hspace='2' vspace='2'/> <?php echo JText::_('icon_overdue');?><br /><img src='<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/ok.png' align='absmiddle' border='0' hspace='2' vspace='2'/> <?php echo JText::_('icon_closed');?><br /><?php echo JText::_('RED_BACKGROUND');?>" /></th>
	        <th nowrap="nowrap" class="sorting<?php echo ($orderby == 't.subject' ? '_' . $order : '');?>"><a
	            href="<?php echo $lorder_subject;?>"><?php echo JText::_('subject');?></a></th>
	        <th nowrap="nowrap" class="hidden-phone sorting<?php echo ($orderby == 't.last_update' ? '_' . $order : '');?>" width="110"><a
	            href="<?php echo $lorder_updated;?>"><?php echo JText::_('last_activity');?></a></th>
	        <th nowrap="nowrap" class="hidden-phone sorting<?php echo ($orderby == 's.description' ? '_' . $order : '');?>"><a
	            href="<?php echo $lorder_status;?>"><?php echo JText::_('status');?></a></th>
	    </tr>
	    </thead><?php
	        $i = 0;
	        foreach ($tickets as $row) {
	            $ticket_link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $row->id_workgroup . '&task=ticket_view&id=' . $row->dbid . '&orderby=' . $orderby . '&order=' . $order . '&page=' . $page;

	            $assignment = (HelpdeskUser::GetName($row->assign_to) ? HelpdeskUser::GetName($row->assign_to) : JText::_('tooltip_unassigned'));
	            $ticket_messages = HelpdeskTicket::GetMessages($row->dbid);

	            $tmp_tooltip_msg = "<table><tr><td nowrap='nowrap'><b>" . JText::_('ticketid') . '</b>: </td><td>' . $row->ticketid . '</tr>'
	                . "<tr><td nowrap='nowrap'><b>" . JText::_('client_th') . '</b>: </td><td>' . ($row->client != '' ? $row->client : JText::_('no_customer')) . '</tr>'
	                . "<tr><td nowrap='nowrap'><b>" . JText::_('tooltip_assignedto') . '</b>: </td><td>' . $assignment . '</tr>'
	                . "<tr><td nowrap='nowrap'><b>" . JText::_('tmpl_msg24') . '</b>: </td><td>' . $row->an_name . '</tr>'
	                . "<tr><td nowrap='nowrap'><b>" . JText::_('category') . '</b>: </td><td>' . ($row->category == "" ? "-" : $row->category) . '</tr>'
	                . "<tr><td nowrap='nowrap'><b>" . JText::_('status') . '</b>: </td><td>' . $row->status . '</tr>'
	                . "<tr><td nowrap='nowrap'><b>" . JText::_('created_date') . '</b>: </td><td>' . HelpdeskDate::DateOffset($supportConfig->date_short, strtotime($row->date)) . '</tr>'
	                . "<tr><td nowrap='nowrap'><b>" . JText::_('last_update') . '</b>: </td><td>' . HelpdeskDate::DateOffset($supportConfig->date_short, strtotime($row->last_update)) . '</tr>'
	                . "<tr><td nowrap='nowrap'><b>" . JText::_('due_date') . '</b>: </td><td>' . HelpdeskDate::DateOffset($supportConfig->date_short, strtotime($row->duedate)) . '</tr>'
	                . "<tr><td nowrap='nowrap'><b>" . JText::_('num_msgs') . '</b>: </td><td>' . $ticket_messages . '</tr>'
	                . "<tr><td nowrap='nowrap'><b>" . JText::_('rating') . '</b>: </td><td>' . (HelpdeskForm::GetRate($row->dbid, 'T', 0) ? HelpdeskForm::GetRate($row->dbid, 'T', 0) : JText::_('unrated')) . '</tr></table>';

	            $moreinfo = '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/' . HelpdeskTicket::IsDueDateValid($row->duedate, $row->id_priority, $row->id_status, 0, $row->assign_to, 2) . '" align="absmiddle" width="16" height="16" border="0" alt="" />';

	            // Set the status background color
	            if ($row->status_group == 'C') {
	                $statuscolor = 'success';
	            } else {
	                if ($row->duedate < date("Y-m-d")) {
	                    $statuscolor = 'important';
	                } elseif (JString::substr($row->duedate, 0, 10) == date("Y-m-d")) {
	                    $statuscolor = 'warning';
	                } elseif (JString::substr($row->duedate, 0, 10) > date("Y-m-d")) {
	                    $statuscolor = 'default';
	                }
	            }

	            // Get previous status
	            $database->setQuery("SELECT id_status FROM #__support_log WHERE id_ticket='" . $row->dbid . "' AND id_status <> '" . $row->id_status . "' AND id_status <> '' AND id_status <> '0' ORDER BY date_time DESC LIMIT 1");
	            $old_id_status = $database->loadResult();

	            // Get last author and last message
	            $sql = "SELECT u.id, u.name, tr.message, tr.date, t.an_name
						FROM #__support_ticket_resp AS tr
							 INNER JOIN #__support_ticket AS t ON t.id=tr.id_ticket
							 LEFT JOIN #__users AS u ON tr.id_user=u.id
						WHERE tr.id_ticket=" . $row->dbid . "
						ORDER BY tr.id DESC
						LIMIT 0, 1";
	            $database->setQuery($sql);
	            $last_user = '';
	            $avatar = '';
	            $last_message = '';
	            $last_time = '';
	            $row_last_user = $database->loadObject();
	            if (isset($row_last_user->message)) {
	                $last_user = ($row_last_user->name!='' ? $row_last_user->name : $row_last_user->an_name);
	                $avatar = HelpdeskUser::GetAvatar((int) $row_last_user->id);
	                $last_message = $row_last_user->message;
	                $last_time = strtotime($row_last_user->date);
	            }

	            // Fill the last message with the original message
	            if ($last_user == '' && $last_message == '') {
	                $last_user = $row->an_name;
	                $avatar = HelpdeskUser::GetAvatar((int) $row->id_user);
	                $last_message = $row->message;
	                $last_time = strtotime($row->date);
	            }

	            // Check if user is online for JBolo
	            if (HelpdeskUser::IsOnline($row->id_user) && $row->dbid && $supportConfig->integrate_jbolo) {
	                $online_status = '<a style="text-decoration:none;" href="javascript:void(0)" onclick="javascript:chatFromTicket(' . $row->id_user . "," . $row->ticketid . ')">
	                              <span style="color:green;">
	                              <img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/chat.png" align="absmiddle" hspace="5"> ' . $row->an_name .
	                    '</span></a>';
	            } else {
	                $online_status = $row->an_name;
	            } ?>

	            <tr id="ticket-<?php echo $row->ticketid;?>"
	                class="<?php echo (!$ticket_messages ? 'noreply' : '');?>">
	                <td width="20" class="hidden-phone">
	                    <input type="checkbox" id="ticketchk-<?php echo $row->ticketid;?>" name="ticketchk[]"
	                           value="<?php echo $row->dbid;?>" class="ticketchk"/>
	                </td>
	                <td>
		                <img src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/logos/<?php echo ($row->logo!='' ? $row->logo : 'folder.png');?>" align="right" alt="<?php echo $row->workgroup;?>" border="0" width="36" height="36" class="hidden-phone" />
		                <img src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/info.png" align="absmiddle" border="0" hspace="5" class="showPopoverRight" data-original-title="<?php echo strip_tags(JText::_('summary'));?>" data-content="<?php echo str_replace('"', "'", $tmp_tooltip_msg);?>">
	                    <span class="lbl lbl-<?php echo $statuscolor;?>" style="font-size:100%;"><?php echo $row->ticketid;?></span>
	                    <a href="<?php echo JRoute::_($ticket_link);?>" class="showPopoverLarger" data-original-title="<?php echo strip_tags(JText::_('message'));?>" data-content="<?php echo strip_tags(str_replace('"', "'", substr($row->message, 0, 500)), '<br><i><u><b>');?>"><b><?php echo $row->subject;?></b></a><br />
	                    <?php echo (HelpdeskTicket::NumberOfAttachments($row->dbid) ? '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/attach.png" align="absmiddle" border="0" />' : ''); ?>
	                    <?php echo ($row->approval && !$row->approved ? '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/flag-red.png" align="absmiddle" border="0" />' : '');?>
	                    <?php echo ($row->queue ? '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/filter.png" align="absmiddle" border="0" />' : '');?>
	                    <a href="javascript:;" class="tooltip-right" title="<?php echo $row->sticky ? JText::_('UNSTICKY') : JText::_('STICKY');?>" onclick="TicketSticky(this,<?php echo $row->dbid;?>,<?php echo $row->id_workgroup;?>,<?php echo $Itemid;?>,<?php echo $row->sticky ? 0 : 1;?>,1);"><img src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/<?php echo $row->sticky ? 'lock' : 'unlock';?>.png" alt="" align="absmiddle" border="0"/></a>
	                    &bull; <?php echo JText::_('author') . ' ' . $online_status;?>
		                <?php echo ($row->internal ? '<span class="color:#ff0000;">' . JText::_("INTERNAL_TICKET") . '</span>' : '');?>
	                </td>
	                <td nowrap="nowrap" class="showPopoverLarger hidden-phone" data-original-title="<?php echo strip_tags(JText::_('last_message'));?>"
	                    data-content="<?php echo strip_tags(str_replace('"', "'", substr($last_message, 0, 500)), '<br><i><u><b>');?>"><?php
	                    echo HelpdeskDate::TimeAgo($row->last_update); ?><br/>
	                    <img class="useravatar" src="<?php echo $avatar;?>" width="16" height="16" align="absmiddle" /> <em><?php echo $last_user;?></em>
	                </td>
	                <td nowrap="nowrap" class="hidden-phone">
	                    <div class="btn-group pull-right">
	                        <button class="btn dropdown-toggle" data-toggle="dropdown" type="button"
	                           id="statuschange<?php echo $row->dbid;?>"
	                           style="<?php echo ($row->color != '' ? 'color:' . $row->color . ';' : '');?>"><?php echo $row->status;?>
	                            <span class="caret"></span></button>
	                        <ul class="dropdown-menu">
	                            <?php echo HelpdeskTicket::BuildStatusList($row->id_status, $old_id_status, false, $row->dbid, $id_workgroup);?>
	                        </ul>
	                    </div>
	                </td>
	            </tr><?php
	            $i++;
	        } ?>
	    </table>
	    <div style="font-size:10px;" class="hidden-phone">
	        <form id="bulkactions" name="bulkactions" method="post" target="_blank">
	            <?php echo JHtml::_('form.token'); ?>
	            <input type="hidden" id="option" name="option" value="com_maqmahelpdesk"/>
		        <input type="hidden" id="Itemid" name="Itemid" value="<?php echo $Itemid;?>"/>
	            <input type="hidden" id="task" name="task" value="pdf_ticketbulk"/>
	            <input type="hidden" id="format" name="format" value="raw"/>
	            <input type="hidden" id="ids" name="ids" value=""/>
	            <div class="btn-toolbar" style="padding-left:40px;background:url('<?php echo JURI::base();?>media/com_maqmahelpdesk/images/dtree/joinbottom.gif') no-repeat 10px 8px;">
			        <div class="btn-group">
				        <a href="javascript:;" class="btn" onclick="BulkPrint();"><i class="ico-print"></i> <?php echo JText::_("PRINT");?></a>
			        </div>
			        <div class="btn-group">
				        <a href="javascript:;" class="btn" onclick="BulkDelete();"><i class="ico-trash"></i> <?php echo JText::_("DELETE");?></a>
				    </div>
			        <div class="btn-group dropup">
			            <button class="btn dropdown-toggle" data-toggle="dropdown" type="button"><i class="ico-refresh"></i> <?php echo JText::_("STATUS");?> <span class="caret"></span></button>
			            <ul class="dropdown-menu">
					        <?php echo HelpdeskTicket::BuildStatusList(0, 0, false, 0, 0, true);?>
				        </ul>
				    </div>
	            </div>
	        </form>
	    </div>

	    <?php
	    if ($pages > 1)
	    {
	        $start = (($page + 1) - 15) < 2 ? 1 : (($page + 1) - 15);
	        $end = (($page + 1) + 15) <= $pages ? (($page + 1) + 15) : $pages;?>
	        <div class="pagination pagination-right">
	            <select id="limitchange" name="limitchange" onchange="$jMaQma('#limit').val(this.value);$jMaQma('#adminForm').submit();">
	                <option value="25">25</option>
	                <option value="50">50</option>
	                <option value="100">100</option>
	            </select>
	            <ul>
	                <li><a href="<?php echo $plink . '&page=0';?>"><?php echo JText::_('table_fpage');?></a></li>
	                <?php for ($i = $start; $i <= $end; $i++): ?>
	                <li class="<?php echo ($i - 1) == $page ? 'active' : '';?>"><a
	                    href="<?php echo $plink . '&page=' . ($i - 1);?>"><?php echo $i;?></a></li>
	                <?php endfor;?>
	                <li><a href="<?php echo $plink . '&page=' . ($pages - 1);?>"><?php echo JText::_('table_lpage');?></a></li>
	            </ul>
	        </div><?php
	    } ?>

	    <br/>

	<?php else:?>

	<?php echo '<h4 style="text-align:center;">'.JText::_('no_tickets').'</h4>';?>

	<?php endif;?>

	<div id="OpenTicket" style="display:none;width:350px;" class="modal">
	    <div class="modal-header">
	        <a class="close" data-dismiss="modal">&times;</a>
	        <h3><?php echo JText::_('open_ticket'); ?></h3>
	    </div>
	    <div class="modal-body">
	        <div>
	            <p><?php echo JText::_('ticket_number');?> <input type="text" id="ticket_number" name="ticket_number"
	                                                              size="10"/>
	                <img src="<?php echo JURI::root();?>components/com_maqmahelpdesk/images/loading.gif" alt=""
	                     style="display:none;"/></p>
	            <span style="color:#ff0000;"></span>
	        </div>
	    </div>
	    <div class="modal-footer">
	        <a href="javascript:;" onclick="CheckTicket(0);" class="btn"><?php echo JText::_('open_ticket'); ?></a>
	        <a href="javascript:;" onclick="CheckTicket(1);" class="btn"><?php echo JText::_('open_ticket_window');?></a>
	    </div>
	</div>

	<div id="OpenViews" style="display:none;width:600px;" class="modal">
	    <div class="modal-header">
	        <a class="close" data-dismiss="modal">&times;</a>

	        <h3><?php echo JText::_('views_manager');?></h3>
	    </div>
	    <div class="modal-body"></div>
	    <div class="modal-footer"></div>
	</div>

	<script type="text/javascript">
	    $jMaQma(document).ready(function ($) {
	        $jMaQma('#limitchange').val(<?php echo $limit;?>);

	        SearchView('spl');

	        <?php if ($filter_search != ''): ?>
	        $jMaQma('#tickettable').highlight('<?php echo addslashes($filter_search);?>');
	        <?php endif;?>

	        $jMaQma("#ac_me").autocompletemqm("<?php echo JURI::root();?>index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&task=ajax_getuser&format=print", {
	            selectFirst:false,
	            scroll:true,
	            scrollHeight:300,
	            formatItem:function (data, i, n, value) {
	                return '<img src="' + data[5] + '" width="32" height="32" align="left">' + data[1] + '<br />' + data[4] + (data[3] != '' ? '<br><em>' + data[3] + '</em>' : '');
	            },
	            selectExecute:function (data) {
	                $jMaQma("#filter_user").val(data[0]);
	                $jMaQma("#ac_me").val(data[1]);
	            }
	        });

	        $jMaQma('#tickettable').css("width", "100%");
	    });

	    function CheckTicket(WINDOW) {
	        if ($jMaQma("#OpenTicket #ticket_number").val() != '') {
	            $jMaQma("#OpenTicket span").html('');
	            $jMaQma("#OpenTicket p img").show();
	            $jMaQma.ajax({
	                url:"<?php echo JURI::root();?>index.php",
	                dataType:"json",
	                data:"option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&task=ticket_checkticket&tmpl=component&format=raw&id=" + $jMaQma("#OpenTicket #ticket_number").val(),
	                success:function (data) {
	                    $jMaQma("#OpenTicket p img").hide();

	                    if (data.output == 'OK') {
	                        url = '<?php echo JURI::root();?>index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=' + data.id_workgroup + '&task=ticket_view&id=' + data.id;
	                        if (WINDOW == 0) {
	                            window.location = url;
	                        } else {
	                            window.open(url, 'mywindow', 'toolbar=yes,location=yes,directories=yes,status=yes,menubar=yes,scrollbars=yes,copyhistory=yes,resizable=yes')
	                        }
	                    } else {
	                        $jMaQma("#OpenTicket span").html('ERROR: Ticket does not exist!');
	                    }
	                }
	            });
	        }
	    }

	    function OpenViews() {
	        $jMaQma("#OpenViews .modal-footer").html('<img src="<?php echo JURI::root();?>components/com_maqmahelpdesk/images/loading.gif" alt="" align="absmiddle" /> <?php echo JText::_('loading');?>');
	        $jMaQma("#OpenViews").modal("show");
	        $jMaQma("#OpenViews .modal-body").load(
	            "<?php echo JURI::root();?>index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid; ?>&id_workgroup=<?php echo $id_workgroup; ?>&task=ticket_views&tmpl=component&format=raw",
	            function () {
	                $jMaQma("#OpenViews .modal-footer").html('<a href="javascript:;" onclick="ViewEdit(0);" class="btn btn-success"><?php echo JText::_('create');?></a><a href="javascript:;" onclick="$jMaQma(\'#OpenViews\').modal(\'hide\');" class="btn" data-dismiss="modal"><?php echo JText::_('close');?></a>');
	            }
	        );
	    }

	    function OpenTicket() {
	        $jMaQma("#OpenTicket").modal("show");
	    }

	    function ViewEdit(ID) {
	        $jMaQma("#OpenViews .modal-footer").html('<img src="<?php echo JURI::root();?>components/com_maqmahelpdesk/images/loading.gif" alt="" align="absmiddle" /> <?php echo JText::_('loading');?>');
	        $jMaQma.ajax({
	            url:"<?php echo JURI::root();?>index.php",
	            type:"POST",
	            data:"option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_editview&id=" + ID + "&tmpl=component&format=raw",
	            success:function (data) {
	                $jMaQma("#OpenViews .modal-body").html(data);
	                $jMaQma("#OpenViews .modal-footer").html('<a href="javascript:;" onclick="ViewSave();" class="btn btn-success"><?php echo JText::_('save');?></a><a href="javascript:;" onclick="OpenViews();" class="btn"><?php echo JText::_('cancel');?></a>');
	            }
	        });
	    }

	    function ViewSave() {
	        $jMaQma("#OpenViews .modal-footer").html('<img src="<?php echo JURI::root();?>components/com_maqmahelpdesk/images/loading.gif" alt="" align="absmiddle" /> <?php echo JText::_('loading');?>');
	        $jMaQma.ajax({
	            url:"<?php echo JURI::root();?>index.php",
	            type:"POST",
	            data:$jMaQma("#viewform").serialize(),
	            success:function (data) {
	                $jMaQma("#OpenViews .modal-body").html(data);
	                $jMaQma("#OpenViews .modal-footer").html('<a href="javascript:;" onclick="ViewEdit(0);" class="btn btn-success"><?php echo JText::_('create');?></a><a href="javascript:;" onclick="$jMaQma(\'#OpenViews\').modal(\'hide\');" class="btn" data-dismiss="modal"><?php echo JText::_('close');?></a>');
	            }
	        });
	    }

	    function ViewDelete(ID) {
	        if (confirm("<?php echo JText::_('confirm_view_delete');?>")) {
	            $jMaQma("#OpenViews .modal-footer").html('<img src="<?php echo JURI::root();?>components/com_maqmahelpdesk/images/loading.gif" alt="" align="absmiddle" /> <?php echo JText::_('loading');?>');
	            $jMaQma.ajax({
	                url:"<?php echo JURI::root();?>index.php",
	                type:"POST",
	                data:"option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=ticket_deleteview&id=" + ID + "&tmpl=component&format=raw",
	                success:function (data) {
	                    $jMaQma("#OpenViews .modal-body").html(data);
	                    $jMaQma("#OpenViews .modal-footer").html('<a href="javascript:;" onclick="ViewEdit(0);" class="btn btn-success"><?php echo JText::_('create');?></a><a href="javascript:;" onclick="$jMaQma(\'#OpenViews\').modal(\'hide\');" class="btn" data-dismiss="modal"><?php echo JText::_('close');?></a>');
	                }
	            });
	        }
	    }

	    var CategoryOptions = '<?php echo str_replace("\r\n", "", str_replace("\n", "", addslashes($lists['views_category'])));?>';
	    var StatusOptions = '<?php echo str_replace("\r\n", "", str_replace("\n", "", addslashes($lists['views_status'])));?>';
	    var StatusGroupOptions = '<?php echo str_replace("\r\n", "", str_replace("\n", "", addslashes($lists['views_status_group'])));?>';
	    var AssignOptions = '<?php echo str_replace("\r\n", "", str_replace("\n", "", addslashes($lists['views_assign'])));?>';
	    var WorkgroupOptions = '<?php echo str_replace("\r\n", "", str_replace("\n", "", addslashes($lists['views_workgroup'])));?>';
	    var ApprovedOptions = '<select id=\"value[]\" name=\"value[]\" class=\"\"><option value=\"1\"><?php echo JText::_("JYES");?></option><option value=\"0\"><?php echo JText::_("JNO");?></option></select>';
	    var FreeSearch = '<input type="text" name="value[]" size="10" value="" />';

	    function SetValueOptions(OBJ) {
	        switch ($jMaQma(OBJ).val()) {
	            case 't.id_status':
	                $jMaQma(OBJ).parent().parent().find(".valuep").html(StatusOptions);
	                break;
	            case 's.status_group':
	                $jMaQma(OBJ).parent().parent().find(".valuep").html(StatusGroupOptions);
	                break;
	            case 't.id_priority':
	                $jMaQma(OBJ).parent().parent().find(".valuep").html(PriorityOptions);
	                break;
	            case 't.assign_to':
	                $jMaQma(OBJ).parent().parent().find(".valuep").html(AssignOptions);
	                break;
	            case 't.id_workgroup':
	                $jMaQma(OBJ).parent().parent().find(".valuep").html(WorkgroupOptions);
	                break;
	            case 't.id_category':
	                $jMaQma(OBJ).parent().parent().find(".valuep").html(CategoryOptions);
	                break;
	            case 't.approved':
	                $jMaQma(OBJ).parent().parent().find(".valuep").html(ApprovedOptions);
	                break;
	        }
	    }

	    function RemoveParameter(OBJ) {
	        $jMaQma(OBJ).parent().parent().remove();
	    }

	    function AddParameter() {
	        $jMaQma(".dynfields:first").clone().appendTo("#dynafields").find(".valuep").html('');
	    }
	</script>

</div>