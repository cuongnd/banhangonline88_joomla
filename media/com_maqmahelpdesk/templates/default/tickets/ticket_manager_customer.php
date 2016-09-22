<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_tickets_manager');?></h2>

    <form id="adminForm" name="adminForm" method="post" action="<?php echo JRoute::_("index.php");?>">
        <?php echo JHtml::_('form.token'); ?>

        <table id="filters-simple" width="100%">
            <tr>
                <td colspan="3">
                    <p><?php echo JText::_('search');?>:
                        <input type="text" name="filter_search" class="inputbox" size="90" style="padding:5px;"
                               value="<?php echo urldecode(htmlspecialchars($filter_search)); ?>"/></p>
                </td>
            </tr>
            <tr>
                <td>
                    <p><?php echo JText::_('status');?>:
                        <?php echo $lists['status']; ?></p>
                </td>
                <td>
                    <p><?php echo JText::_('category');?>:
                        <?php echo $lists['category']; ?></p>
                </td>
                <?php if ($usertype == 2) : ?>
                <td>
                    <p><?php echo JText::_('user');?>:
                        <?php echo $lists['users']; ?></p>
                </td>
                <?php endif; ?>
            </tr>
            <tr>
                <td colspan="3">
                    <hr height="1" color="#c5c5c5" size="1"/>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div style="float:left;width:50%;">
                        <a class="btn"
                           href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid; ?>&id_workgroup=<?php echo $id_workgroup; ?>&task=ticket_new"
                           title="<?php echo JText::_('qk_create_ticket');?>"><?php echo JText::_('qk_create_ticket');?></a>
                    </div>
                    <div style="float:left;width:50%;text-align:right;">
                        <button type="button" class="btn" onclick="javascript:resetFilter(1);"
                                title="<?php echo JText::_('defaults_filters');?>"><?php echo JText::_('reset');?></button>
                        <button type="submit" class="btn btn-success"
                                title="<?php echo JText::_('defaults_filters');?>"><?php echo JText::_('search');?></button>
                    </div>
                </td>
            </tr>
        </table>

        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>"/>
        <input type="hidden" id="limit" name="limit" value="<?php echo $limit; ?>"/>
        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup; ?>"/>
	    <input type="hidden" name="order" value="<?php echo $order; ?>"/>
	    <input type="hidden" name="orderby" value="<?php echo $orderby; ?>"/>
        <input type="hidden" name="task" value="ticket_my"/>
    </form>

    <p>&nbsp;</p>

    <?php if (count($tickets)):?>
	<p class="tar">
		<?php echo JText::_('legend');?> <img src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/info.png" align="absmiddle" border="0" hspace="5" class="showPopoverLeft" data-original-title="<?php echo JText::_('legend');?>" data-content="<img src='<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/flag-red.png' align='absmiddle' border='0' hspace='2' vspace='2'/> <?php echo JText::_('approve_pending');?><br /><img src='<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/filter.png' align='absmiddle' border='0' hspace='2' vspace='2'/> <?php echo JText::_('ticket_in_queue');?><br /><img src='<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/attach.png' align='absmiddle' border='0' hspace='2' vspace='2'/> <?php echo JText::_('attached_files');?><br /><img src='<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/alert.png' align='absmiddle' border='0' hspace='2' vspace='2'/> <?php echo JText::_('icon_onhold');?><br /><img src='<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/hour.png' align='absmiddle' border='0' hspace='2' vspace='2'/> <?php echo JText::_('icon_progress');?><br /><img src='<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/clock.png' align='absmiddle' border='0' hspace='2' vspace='2'/> <?php echo JText::_('icon_overdue');?><br /><img src='<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/ok.png' align='absmiddle' border='0' hspace='2' vspace='2'/> <?php echo JText::_('icon_closed');?><br /><?php echo JText::_('RED_BACKGROUND');?>" />
	</p>
    <table id="tickettable" width="100%" class="table table-bordered table-striped" cellspacing="0">
        <thead>
        <tr>
            <th nowrap="nowrap" class="sorting<?php echo ($orderby == 't.subject' ? '_' . $order : '');?>"><a
                href="<?php echo $lorder_subject;?>"><?php echo JText::_('subject');?></a></th>
            <th nowrap="nowrap" class="sorting<?php echo ($orderby == 't.last_update' ? '_' . $order : '');?> hidden-phone" width="110"><a
                href="<?php echo $lorder_updated;?>"><?php echo JText::_('last_activity');?></a></th>
            <th nowrap="nowrap" class="sorting<?php echo ($orderby == 's.description' ? '_' . $order : '');?> hidden-phone"><a
                href="<?php echo $lorder_status;?>"><?php echo JText::_('status');?></a></th>
        </tr>
        </thead><?php
        $i = 0;
        foreach ($tickets as $row) {
            $ticket_link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $row->id_workgroup . '&task=ticket_view&id=' . $row->dbid . '&page=' . $page;

            $assignment = (HelpdeskUser::GetName($row->assign_to) ? JText::_('assigned_hidden') : JText::_('tooltip_unassigned'));

            $tmp_tooltip_msg = "<table><tr><td nowrap='nowrap'><b>" . JText::_('ticketid') . '</b>: </td><td>' . $row->ticketid . '</tr>'
                . "<tr><td nowrap='nowrap'><b>" . JText::_('client_th') . '</b>: </td><td>' . ($row->client != '' ? $row->client : JText::_('no_customer')) . '</tr>'
                . "<tr><td nowrap='nowrap'><b>" . JText::_('tooltip_assignedto') . '</b>: </td><td>' . $assignment . '</tr>'
                . "<tr><td nowrap='nowrap'><b>" . JText::_('tmpl_msg24') . '</b>: </td><td>' . $row->an_name . '</tr>'
                . "<tr><td nowrap='nowrap'><b>" . JText::_('category') . '</b>: </td><td>' . ($row->category == "" ? "-" : $row->category) . '</tr>'
                . "<tr><td nowrap='nowrap'><b>" . JText::_('status') . '</b>: </td><td>' . $row->status . '</tr>'
                . "<tr><td nowrap='nowrap'><b>" . JText::_('created_date') . '</b>: </td><td>' . HelpdeskDate::DateOffset($supportConfig->date_short, strtotime($row->date)) . '</tr>'
                . "<tr><td nowrap='nowrap'><b>" . JText::_('last_update') . '</b>: </td><td>' . HelpdeskDate::DateOffset($supportConfig->date_short, strtotime($row->last_update)) . '</tr>'
                . "<tr><td nowrap='nowrap'><b>" . JText::_('num_msgs') . '</b>: </td><td>' . HelpdeskTicket::GetMessages($row->dbid) . '</tr>'
                . "<tr><td nowrap='nowrap'><b>" . JText::_('rating') . '</b>: </td><td>' . (HelpdeskForm::GetRate($row->dbid, 'T', 0) ? HelpdeskForm::GetRate($row->dbid, 'T', 0) : JText::_('unrated')) . '</tr></table>';

            $moreinfo = '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/' . HelpdeskTicket::IsDueDateValid($row->duedate, $row->id_priority, $row->id_status, 0, $row->assign_to, 2) . '" align="absmiddle" width="16" height="16" border="0" alt="" />';

            // Set the status background color
            if ($row->status_group == 'C')
            {
                $statuscolor = 'success';
            }
            else
            {
                if ($row->duedate < date("Y-m-d"))
                {
                    $statuscolor = 'important';
                }
                elseif (JString::substr($row->duedate, 0, 10) == date("Y-m-d"))
                {
                    $statuscolor = 'warning';
                }
                elseif (JString::substr($row->duedate, 0, 10) > date("Y-m-d"))
                {
                    $statuscolor = 'default';
                }
            }

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
            if (isset($row_last_user->message))
            {
                $last_user = ($row_last_user->name!='' ? $row_last_user->name : $row_last_user->an_name);
                $avatar = HelpdeskUser::GetAvatar((int) $row_last_user->id);
                $last_message = $row_last_user->message;
                $last_time = strtotime($row_last_user->date);
            }

            // Fill the last message with the original message
            if ($last_user == '' && $last_message == '')
            {
                $last_user = $row->an_name;
                $avatar = HelpdeskUser::GetAvatar((int) $row->id_user);
                $last_message = $row->message;
                $last_time = strtotime($row->date);
            } ?>
            <tr>
                <td>
	                <img src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/logos/<?php echo ($row->logo!='' ? $row->logo : 'folder.png');?>" align="right" alt="<?php echo $row->workgroup;?>" border="0" width="36" height="36" class="hidden-phone" />
	                <img src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/info.png" align="absmiddle" border="0" hspace="5" class="showPopoverRight" data-original-title="<?php echo strip_tags(JText::_('summary'));?>" data-content="<?php echo str_replace('"', "'", $tmp_tooltip_msg);?>">
                    <span class="lbl lbl-<?php echo $statuscolor;?>" style="font-size:100%;"><?php echo $row->ticketid;?></span>
                    <a href="<?php echo JRoute::_($ticket_link);?>" class="showPopoverLarger" data-original-title="<?php echo strip_tags(JText::_('message'));?>" data-content="<?php echo strip_tags(str_replace('"', "'", substr($row->message, 0, 500)), '<br><i><u><b>');?>"><b><?php echo $row->subject;?></b></a><br />
                    <?php echo (HelpdeskTicket::NumberOfAttachments($row->dbid) ? '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/attach.png" align="absmiddle" border="0" />' : ''); ?>
                    <?php echo ($row->approval && !$row->approved ? '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/flag-red.png" align="absmiddle" border="0" />' : '');?>
                    <?php echo $moreinfo;?> &bull; <?php echo JText::_('author') . ': ' . $row->an_name; ?>
                </td>
                <td nowrap="nowrap" class="showPopoverLarger hidden-phone" data-original-title="<?php echo strip_tags(JText::_('last_message'));?>"
                    data-content="<?php echo strip_tags(str_replace('"', "'", substr($last_message, 0, 500)), '<br><i><u><b>');?>"><?php
                    echo HelpdeskDate::TimeAgo($row->last_update); ?><br/>
                    <img class="useravatar" src="<?php echo $avatar;?>" width="16" height="16" align="absmiddle" /> <em><?php echo $last_user;?></em>
                </td>
                <td class="hidden-phone" nowrap="nowrap"><span
                    style="<?php echo ($row->color != '' ? 'color:' . $row->color . ';' : '');?>"><?php echo $row->status;?></span>
                </td>
            </tr><?php
            $i++;
        } ?>
    </table>

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

    <script type="text/javascript">
    $jMaQma(document).ready(function ($) {
        $jMaQma('#limitchange').val(<?php echo $limit;?>);
        <?php if ($filter_search != ''): ?>
        $jMaQma('#tickettable').highlight('<?php echo addslashes($filter_search);?>');
        <?php endif;?>
    });
    </script>

</div>