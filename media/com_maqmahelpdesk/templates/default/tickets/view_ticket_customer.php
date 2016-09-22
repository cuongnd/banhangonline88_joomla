<div class="maqmahelpdesk container-fluid">

<h2><?php echo $row->subject; ?></h2>

<?php if (isset($client_details) && $client_details->approval && $client_details->manager && !$row->approved): ?>
<div class="alert alert-error">
    <p><?php echo JText::_('approve_pending');?></p>
	<?php if ($is_manager): ?>
    <p><a
            href="<?php echo JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_approve&id=' . $row->id);?>"
            class="btn btn-success"><?php echo JText::_('approve_ticket');?></a></p>
	<?php endif;?>
</div>
	<?php endif;?>

<div>
    <div id="rating"></div>
    <div id="tickettools">
        <div class="btn-group">
            <a href="<?php echo JURI::root() . 'index.php?option=com_maqmahelpdesk&task=pdf_ticket&id=' . $row->id . '&format=raw';?>"
               target="_blank" class="btn"
               title="<?php echo JText::_('pdf_version');?>"><?php echo JText::_('pdf_version');?></a>
			<?php if ($workgroupSettings->use_bookmarks): ?>
            <a href="<?php echo JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_bookmark&id=' . $row->id);?>"
               class="btn"><?php echo JText::_('add_to_bookmarks');?></a>
			<?php endif;?>
        </div>
    </div>
</div>
<div class="clear"></div>

<?php if ($closed_by_manager): ?>
	<?php echo JText::_('closed_by_manager_info'); ?>
	<?php endif;?>

<div>
    <div style="float:left;"><span class="lbl lbl-<?php echo $status_color;?>" style="font-size:20px;font-weight:bold;">#<?php echo $row->ticketmask;?></span>
    </div>
    <div style="float:right;"><em><?php echo JText::_('date');?>:
        <b><?php echo HelpdeskDate::DateOffset($supportConfig->date_long, strtotime($row->date));?></b></em></div>
    <div class="clear"></div>
</div>

<form id="adminForm" name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="post"
      enctype="multipart/form-data" class="form-horizontal">
<?php echo JHtml::_('form.token'); ?>
<input type="hidden" name="task" value=""/>
<input type="hidden" name="clientrate" value="<?php echo $clientvalue;?>"/>
<input type="hidden" name="replysubject" value=""/>
<input type="hidden" name="id_activity_type" value="0"/>
<input type="hidden" name="id_activity_rate" value="0"/>
<input type="hidden" name="start_time" value="0"/>
<input type="hidden" name="end_time" value="0"/>
<input type="hidden" name="break_time" value="0"/>
<input type="hidden" name="replytime" value="0"/>
<input type="hidden" name="tickettravel" value="0"/>
<input type="hidden" name="assign_to" value="<?php echo $row->assign_to;?>"/>
<input type="hidden" name="old_assign" value="<?php echo $row->assign_to;?>"/>
<input type="hidden" name="old_status" value="<?php echo $row->id_status;?>"/>
<input type="hidden" name="old_priority" value="<?php echo $row->id_priority;?>"/>
<input type="hidden" name="id_assign" value="<?php echo $row->assign_to;?>"/>
<input type="hidden" name="old_duedate_date" value="<?php echo $old_duedate_date;?>"/>
<input type="hidden" name="old_duedate_hour" value="<?php echo $old_duedate_hour;?>"/>
<input type="hidden" name="old_id_category" value="<?php echo $row->id_category;?>"/>
<input type="hidden" id="id_category" name="id_category" value="<?php echo $row->id_category;?>"/>
<input type="hidden" name="is_editreplied" value="0"/>
<input type="hidden" name="originalmsg" value="0"/>
<input type="hidden" name="option" value="com_maqmahelpdesk"/>
<input type="hidden" id="Itemid" name="Itemid" value="<?php echo $Itemid;?>"/>
<input type="hidden" name="id_workgroup" id="id_workgroup" value="<?php echo $id_workgroup;?>"/>
<input type="hidden" name="id_directory" id="id_directory" value="<?php echo $row->id_directory;?>"/>
<input type="hidden" name="id" value="<?php echo $row->id;?>"/>
<input type="hidden" name="last_status" value="<?php echo $old_status;?>"/>
<input type="hidden" name="duedate_date" id="duedate_date" value="<?php echo $duedate_date;?>"/>
<input type="hidden" name="duedate_hours" id="duedate_hours" value="<?php echo $duedate_hour;?>"/>
<input type="hidden" name="now_date" value=""/>
<input type="hidden" id="page" name="page" value="<?php echo $page;?>"/>

<div class="control-group row-fluid">
    <label class="control-label"><?php echo JText::_('client_name');?></label>
    <div class="controls" style="padding-top:5px;">
		<?php echo HelpdeskClient::GetName($row->id_user);?>
    </div>
</div>
<div class="control-group row-fluid">
    <label class="control-label"><?php echo JText::_('user');?></label>
    <div class="controls">
		<?php echo $row->an_name;?>
    </div>
</div>
<div class="control-group row-fluid">
    <label class="control-label"><?php echo JText::_('status');?></label>
    <div class="controls">
		<?php echo $status;?>
    </div>
</div>
<div class="control-group row-fluid">
    <label class="control-label"><?php echo JText::_('category');?></label>
    <div class="controls">
		<?php echo $category;?>
    </div>
</div>
<div class="control-group row-fluid">
    <label class="control-label"><?php echo JText::_('assignto');?></label>
    <div class="controls">
		<?php echo $assign;?>
    </div>
</div>
<div class="control-group row-fluid">
    <label class="control-label"><?php echo JText::_('source');?></label>
    <div class="controls">
		<?php echo $source_desc;?>
    </div>
</div>
<div class="control-group row-fluid">
    <label class="control-label"><?php echo JText::_('priority');?></label>
    <div class="controls">
		<?php echo $priority;?>
    </div>
</div>

<?php if (is_object($directory) && ($supportConfig->integrate_mtree || $supportConfig->integrate_sobi)): ?>
<div class="control-group row-fluid">
    <label class="control-label"><?php echo JText::_('listing');?></label>
    <div class="controls">
		<?php echo $directory->directory_name;?>
    </div>
</div>
	<?php endif;?>

<?php
if (count($customfields))
{
	$section = '';
	foreach ($cfields_rows as $rowloop)
	{
		if ($section != $rowloop['section'])
		{
			$section = $rowloop['section']; ?>
        <div class="span12 issection cfieldsection-<?php echo JFilterOutput::stringURLSafe($rowloop['section']);?>">
            <label class="control-label" style="font-size:120%;padding:5px 10px;"><?php echo $rowloop['section'];?></label>
        </div><?php
		}
		$cfclass = '';
		if ($rowloop['id_category'] == '') {
			$cfclass = ' cat0';
		} else {
			$cfclasses = explode(",", $rowloop['id_category']);
			foreach ($cfclasses as $cfclassid) {
				$cfclass .= ' cat' . $cfclassid;
			}
		} ?>
    <div id="cf<?php echo $rowloop['id'];?>" class="control-group row-fluid <?php echo ($rowloop['ftype'] == 'note' ? 'note' : 'field');?> cfield<?php echo $cfclass;?> cfieldsection-<?php echo JFilterOutput::stringURLSafe($rowloop['section']);?>">
	    <?php if ($rowloop['ftype'] != 'note') : ?>
        <label class="control-label" for="custom<?php echo $rowloop['id'];?>">
			<?php echo $rowloop['caption'];?>
			<?php if ($rowloop['required']): ?> <span class="required">*</span><?php endif;?>
        </label>
	    <?php endif; ?>
        <div class="controls">
			<?php echo $rowloop['field'];?>
			<?php if ($rowloop['tooltip']!=''):?>
            <span class="help-block"><?php echo $rowloop['tooltip'];?></span>
			<?php endif;?>
        </div>
    </div><?php
	}
} ?>

<?php echo $cfields_hiddenfield;?>

<ul id="tab" class="nav nav-tabs">
    <li class="active"><a href="#messages"
                          data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/comments.png" align="absmiddle" /> ' . JText::_('activity_history'); ?></a>
    </li>
    <li><a href="#attachments" onclick="FileNotify(<?php echo $Itemid;?>);"
           data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/attach.png" align="absmiddle" /> ' . JText::_('attachments') . ' <span class="badge">' . $count_ticketAttachs . '</span>';?></a>
    </li>
    <li><a href="#logs"
           data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/table.png" align="absmiddle" /> ' . JText::_('activity_logs');?></a>
    </li>
	<?php if ($supportConfig->bbb_url != '' && $supportConfig->bbb_apikey): ?>
    <li><a href="#meetings"
           data-toggle="tab"><?php echo '<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/meetings.png" align="absmiddle" /> ' . JText::_('meetings');?></a>
    </li>
	<?php endif;?>
</ul>

<div id="my-tab-content" class="tab-content">
<div class="tab-pane fade in active" id="messages">
    <div class="post-box-wrapper">
        <div class="post-box">
            <ol class="messagelist"><?php
				foreach ($activities_rows as $rowloop):?>
                    <li>
                        <div class="message-body">
                            <img alt="" src="<?php echo $rowloop['avatar'];?>" class="message-avatar hidden-mobile" height="60" width="60">
                            <div class="message-arrow hidden-mobile"></div>
                            <div class="message-box <?php echo ($rowloop['msgtype'] != 'message' ? 'note' : '');?>">
                                <div class="message-author">
                                    <strong><?php echo $rowloop['user'];?></strong>
                                    <small><?php echo HelpdeskDate::LongDate($rowloop['date']);?></small>
                                </div>
                                <div class="message-text"><?php
									echo ($rowloop['reply_summary'] != '' ? '<p><span style="color:#666;font-size:11px;">' . JText::_('summary') . ':</span> ' . $rowloop['reply_summary'] . '</p>' : '');
									echo $rowloop['message'];
									$reply_attachs = HelpdeskTicket::GetMessageAttachments($row->id, $rowloop["id"]);
									for ($c=0; $c<count($reply_attachs); $c++)
									{
										$reply_attach = $reply_attachs[$c];
										$link = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_download&id=' . $reply_attach->id_file . '&extid=' . $reply_attach->id);?>
                                        <p>
                                            <img src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/attach.png" alt="" align="left" />
                                            <a href="<?php echo $link;?>"><?php echo $reply_attach->filename;?></a>
                                        </p><?php
									} ?>
                                </div>
                            </div>
                        </div>
                    </li><?php
				endforeach; ?>
            </ol>
        </div>
    </div><?php

	if (!$closed_by_manager):?>
        <h3><img
                src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/circle_add.png"
                align="absmiddle" border="0" alt=""/> <?php echo JText::_('add_reply');?></h3>
        <div id="AddReply" name="AddReply">
            <div class="control-group row-fluid">
                <label class="control-label" for="reply_summary"><?php echo JText::_('summary');?></label>
                <div class="controls">
                    <input type="text" id="reply_summary" name="reply_summary" value="" maxlength="150">
                </div>
            </div>
            <div class="control-group row-fluid">
                <label class="control-label" for="reply"><?php echo JText::_('activity');?></label>
                <div class="controls">
                    <textarea id="reply" name="reply" class="redactor_user"></textarea>
                </div>
            </div>

			<?php if ($supportConfig->extra_email_notification): ?>
            <div class="control-group row-fluid">
                <label class="control-label">CC</label>
                <div class="controls">
                    <input id="cc_report" name="cc_report" type="text" />
                    <div id="cc_emails"></div>
                </div>
            </div>
            <div class="control-group row-fluid">
                <label class="control-label">BCC</label>
                <div class="controls">
                    <input id="bcc_report" name="bcc_report" type="text" />
                    <div id="bcc_emails"></div>
                </div>
            </div>
			<?php endif;?>

			<?php if($supportConfig->screenr_account!='' && $supportConfig->screenr_api_id!=''):?>
            <div class="alert alert-info" style="margin-left:150px;">
                <div id="button_to_record"><a href="javascript:;" onclick="CreateScreenr();"><?php echo JText::_('SCREENR_ATTACH_SCREENCAST');?></a></div>
                <div id="is_recording" style="display:none;"><?php echo JText::_('SCREENR_MSG_RECORDING');?></div>
            </div>
			<?php endif;?>

			<?php if ($supportConfig->attachs_num > 0 && $supportConfig->public_attach): ?>
            <div class="control-group row-fluid">
                <div class="control-label hidden-phone"></div>
                <div class="controls">
                    <a href="javascript:;" onclick="AddAttachment();" title="<?php echo JText::_('add_attachment');?>" class="btn">
                        <i class="ico-upload"></i> <?php echo JText::_('add_attachment');?>
                    </a>
                </div>
            </div>
            <div id="AddAttachment" name="AddAttachment" style="display:none;">
                <div class="control-group row-fluid">
                    <div class="control-label hidden-phone"></div>
                    <div class="controls">
                        <div class="alert alert-info">
                            <p><?php echo JText::_("ALLOWED_TYPES");?>: <b><?php echo $supportConfig->extensions;?></b><br />
								<?php echo JText::_("MAXALLOWED");?>: <b><?php echo HelpdeskFile::FormatFileSize($supportConfig->maxAllowed);?></b></p>
                        </div>
                    </div>
                </div>
				<?php foreach ($attachs as $rowloop): ?>
                <div class="control-group row-fluid">
                    <label class="control-label" for="file<?php echo $rowloop['number'];?>"><?php echo JText::_('file');?> (<?php echo $rowloop['number'];?>)</label>
                    <div class="controls">
                        <input type="file" id="file<?php echo $rowloop['number'];?>" name="file<?php echo $rowloop['number'];?>" />
                        &nbsp;
                        <a href="javascript:;" onclick="$jMaQma('#file<?php echo $rowloop['number'];?>').val('');" class="btn"><i class="ico-remove"></i></a>
                    </div>
                </div>
                <div class="control-group row-fluid">
                    <label class="control-label" for="desc<?php echo $rowloop['number'];?>"><?php echo JText::_('description');?></label>
                    <div class="controls">
                        <textarea id="desc<?php echo $rowloop['number'];?>" name="desc<?php echo $rowloop['number'];?>" cols="48" rows="5"></textarea>
                    </div>
                </div>
				<?php echo $rowloop['available']; ?>
				<?php endforeach;?>
            </div>
			<?php endif;?>
        </div><?php
	endif; ?>
</div>

<div id="attachments" class="tab-pane fade"><?php
	if (count($ticketAttachs)):?>
        <table class="table table-striped table-bordered" cellspacing="0">
            <thead>
            <tr>
                <th class="title"><?php echo JText::_('date');?></th>
                <th class="title"><?php echo JText::_('filename');?></th>
                <th class="title"><?php echo JText::_('description');?></th>
                <th class="title"><?php echo JText::_('attachs_tools');?></th>
            </tr>
            </thead>
            <tbody>
				<?php $i = 0;
				foreach ($ticket_attachs as $rowloop):?>
                <tr>
                    <td><?php echo $rowloop['date'];?></td>
                    <td><a href="<?php echo $rowloop['link'];?>"><?php echo $rowloop['filename'];?></a></td>
                    <td><?php echo $rowloop['description'];?></td>
                    <td width="50" align="center"><?php echo $rowloop['tools'];?></td>
                </tr><?php
					$i++;
				endforeach;?>
            </tbody>
        </table>
		<?php else: ?>
        <p><span style="color:#ff0000;"><?php echo JText::_('no_attachments');?></span></p>
		<?php endif; ?>
</div>

<div id="logs" class="tab-pane fade">
    <table class="table table-striped table-bordered" cellspacing="0">
        <tbody><?php
		$i = 0;
		$pdate = '';
		foreach ($ticketLogs as $rowloop):
			$time = date("H:i", strtotime($rowloop->date));
			$date = HelpdeskDate::DateOffset($supportConfig->dateonly_format, strtotime($rowloop->date));
			if ($pdate != $date) {
				$pdate = $date; ?>
            <tr class="row">
                <td class="log date" valign="top"><?php echo $date;?></td>
                <td class="log daterow" width="20" valign="top"><?php echo $time;?></td>
                <td class="log daterow"
                    valign="top"><?php echo ($rowloop->image != '' ? '<img src="media/com_maqmahelpdesk/images/logs/' . $rowloop->image . '" align="absmiddle" alt="" />' : '');?> <?php echo $rowloop->message;?></td>
            </tr><?php
			} else {
				?>
            <tr class="row">
                <td valign="top"></td>
                <td class="log empty" width="20" valign="top"><?php echo $time;?></td>
                <td class="log empty"
                    valign="top"><?php echo ($rowloop->image != '' ? '<img src="media/com_maqmahelpdesk/images/logs/' . $rowloop->image . '" align="absmiddle" alt="" />' : '');?> <?php echo $rowloop->message;?></td>
            </tr><?php
			}

			$i++;
		endforeach;?>

        </tbody>
    </table>
</div>

<?php if ($supportConfig->bbb_url != '' && $supportConfig->bbb_apikey): ?>
<div id="meetings"><?php
	if (count($ticketMeetings)): ?>
        <table class="table table-striped table-bordered" cellspacing="0">
            <thead>
            <tr>
                <td class="title"><?php echo JText::_('date');?></td>
                <td class="title"><?php echo JText::_('hour');?></td>
                <td class="title">Invites</td>
                <td class="title">Links</td>
            </tr>
            </thead>
            <tbody>
				<?php $i = 0;
				foreach ($ticketMeetings as $rowloop):
					$sql = "SELECT `invite`
								FROM `#__support_bbb_invites`
								WHERE `id_meeting`=" . $rowloop->id;
					$database->setQuery($sql);
					$invites = $database->loadObjectList();

					$sql = "SELECT `link`
								FROM `#__support_bbb_links`
								WHERE `id_meeting`=" . $rowloop->id;
					$database->setQuery($sql);
					$links = $database->loadObjectList();?>

                <tr>
                    <td valign="top"><?php echo $rowloop->meeting_date;?></td>
                    <td valign="top"><?php echo $rowloop->meeting_hours;?></td>
                    <td valign="top"><?php
						for ($x = 0; $x < count($invites); $x++) {
							echo $invites[$x]->invite . "<br />";
						} ?>
                    </td>
                    <td valign="top"><?php
						for ($x = 0; $x < count($links); $x++) {
							echo $links[$x]->link . "<br />";
						} ?>
                    </td>
                </tr><?php
					$i++;
				endforeach;?>
            </tbody>
        </table>
		<?php else: ?>
        <p><span style="color:#ff0000;">No meetings created for this ticket!</span></p>
		<?php endif; ?>
</div>
	<?php endif;?>
</div>

<p>&nbsp;</p>
<small><span class="required">*</span> <b><?php echo JText::_('field_required_desc');?></b></small>
<div class="form-actions">
    <div style="float:left;width:50%;text-align:left;">
        &nbsp;&nbsp;
        <button type="button" class="btn btn-success" id="ticket_reply"
                onclick="submitbutton('ticket_reply');"><?php echo JText::_('save');?></button>
    </div>
    <div style="float:left;width:50%;text-align:right;">
        <a href="javascript:;" name="ticket_cancel" onclick="Cancel();"><?php echo JText::_('cancel');?></a>
        &nbsp;&nbsp;
    </div>
</div>
</form>

</div>