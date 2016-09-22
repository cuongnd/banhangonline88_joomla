<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_new');?> - <?php echo HelpdeskDepartment::GetName($id_workgroup);?></h2>

    <p class="tar"><small><span class="required">*</span> <b><?php echo JText::_('field_required_desc');?></b></small></p>

	<form id="adminForm" name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="post" enctype="multipart/form-data"
	      onsubmit="return JSValidDueDate();" class="form-horizontal">
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="option" value="com_maqmahelpdesk" />
	<input type="hidden" id="Itemid" name="Itemid" value="<?php echo $Itemid;?>" />
	<input type="hidden" name="id_workgroup" id="id_workgroup" value="<?php echo $id_workgroup;?>" />
	<input type="hidden" name="id_directory" id="id_directory" value="<?php echo $id_directory;?>" />
	<input type="hidden" name="id" value="0" />
	<input type="hidden" name="task" value="ticket_save" />
	<input type="hidden" id="id_user" name="id_user" value="<?php echo ($duplicate ? HelpdeskTicket::GetTicketField($duplicate, 'id_user') : 0 );?>" />
	<input type="hidden" id="id_client" name="id_client" value="<?php echo ($duplicate ? HelpdeskTicket::GetTicketField($duplicate, 'id_client') : 0 );?>" />
    <input type="hidden" name="now_date" value=""/>

	<!-- User selection / creation -->
	<div id="userarea" class="control-group row-fluid input-append">
		<label class="control-label" for="ac_me"><?php echo JText::_('user');?> <span class="required">*</span> </label>
		<div class="controls">
			<input id="ac_me" name="ac_me" type="text" value="<?php echo ($duplicate ? HelpdeskTicket::GetTicketField($duplicate, 'an_name') : '' );?>" />
			<a href="javascript:UserView();" class="btn" title="<?php echo JText::_('user_details');?>"><i class="ico-user"></i></a>
			<a id="addUserBtn" class="btn btn-success" href="javascript:;" onclick="$jMaQma('#adduser').show();"><i class="ico-plus ico-white"></i></a>
		</div>
	</div>
	<div id="adduser" class="popin">
	    <div class="alert"><?php echo JText::_('user_in_ticket');?></div>
		<div class="control-group row-fluid">
			<label class="control-label" for="username"><?php echo JText::_('name');?></label>
			<div class="controls">
				<input type="text" id="username" name="username" value="">
			</div>
		</div>
		<div class="control-group row-fluid">
			<label class="control-label" for="usermail"><?php echo JText::_('email');?></label>
			<div class="controls">
				<input type="text" id="usermail" name="usermail" value="">
			</div>
		</div>
		<div class="control-group row-fluid">
			<label class="control-label" for="userpassword"><?php echo JText::_('password');?></label>
			<div class="controls">
				<input type="text" id="userpassword" name="userpassword" value="">
			</div>
		</div>
		<div class="control-group row-fluid">
			<label class="control-label" for="userregister"><?php echo JText::_('register_user');?></label>
			<div class="controls">
				<label class="checkbox inline">
					<input type="radio" id="userregister0" name="userregister" value="0"> <?php echo JText::_('MQ_NO');?>
				</label>
				<label class="checkbox inline">
					<input type="radio" id="userregister1" name="userregister" value="1" checked="checked"> <?php echo JText::_('MQ_YES');?>
				</label>
			</div>
		</div>
		<div class="form-actions" style="margin-left:-10px;margin-right:-10px;margin-bottom:-21px;">
			<button id="cancelAddUserBtn" type="button" class="btn"><?php echo JText::_('cancel');?></button>
		</div>
	</div>

	<!-- Ticket "header" fields -->
	<div class="control-group row-fluid">
		<label class="control-label" for="internal"><?php echo JText::_('INTERNAL_TICKET');?> <span class="required">*</span></label>
		<div class="controls">
			<?php echo $lists['internal'];?>
		</div>
	</div>
	<div class="control-group row-fluid">
		<label class="control-label" for="subject"><?php echo JText::_('subject');?> <span class="required">*</span></label>
		<div class="controls">
			<input type="text" id="subject" name="subject" value="<?php echo ($duplicate ? HelpdeskTicket::GetTicketField($duplicate, 'subject') : '' );?>">
		</div>
	</div>
	<div class="control-group row-fluid">
		<label class="control-label" for="id_assign"><?php echo JText::_('assignto');?></label>
		<div class="controls">
			<?php echo $lists['assign'];?>
		</div>
	</div>
	<div class="control-group row-fluid">
		<label class="control-label" for="priority"><?php echo JText::_('priority');?> <span class="required">*</span></label>
		<div class="controls">
			<?php echo $lists['priority'];?>
		</div>
	</div>
	<div class="control-group row-fluid">
		<label class="control-label" for="category"><?php echo JText::_('category');?> <span class="required">*</span></label>
		<div class="controls">
			<?php echo $lists['category'];?>
		</div>
	</div>
	<div class="control-group row-fluid">
		<label class="control-label" for="id_status"><?php echo JText::_('status');?> <span class="required">*</span></label>
		<div class="controls">
			<?php echo $lists['status'];?>
		</div>
	</div>
	<div class="control-group row-fluid">
		<label class="control-label" for="source"><?php echo JText::_('source');?> <span class="required">*</span></label>
		<div class="controls">
			<?php echo $lists['source'];?>
		</div>
	</div>
	<div class="control-group row-fluid">
        <label class="control-label" for="duedate"><?php echo JText::_('duedate');?> <span class="required">*</span></label>
		<div class="controls">
 			<?php echo JHTML::Calendar($duedate_date, 'duedate_date', 'duedate_date', '%Y-%m-%d', array('maxlength' => '10', 'style' => 'width:100px;')); ;?>
            <input type="text" class="timepicker" id="duedate_hours" name="duedate_hours" maxlength="5" value="08:00"/>
        </div>
	</div>

	<!-- Custom fields --><?php
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
	            <label class="control-label" for="custom<?php echo $rowloop['id'];?>">
		            <?php if ($rowloop['ftype'] != 'note') : ?>
			            <?php echo $rowloop['caption'];?>
			            <?php if ($rowloop['required']): ?> <span class="required">*</span><?php endif;?>
		            <?php endif; ?>
	            </label>
	            <div class="controls">
		            <?php echo $rowloop['field'];?>
		            <?php if ($rowloop['tooltip']!=''):?>
                        <span class="help-block"><?php echo $rowloop['tooltip'];?></span>
		            <?php endif;?>
	            </div>
	        </div><?php
	    }
	} ?>

	<!-- Problem field -->
    <div class="control-group row-fluid">
        <label class="control-label" for="problem"><?php echo JText::_('message');?> <span class="required">*</span></label>
        <div class="controls">
	        <?php if($supportConfig->editor == 'builtin'):?>
            <textarea id="problem" name="problem" class="redactor_agent"><?php echo ($duplicate ? HelpdeskTicket::GetTicketField($duplicate, 'problem') : '' );?></textarea>
	        <?php else:?>
	        <?php echo $editor->display('problem', ($duplicate ? HelpdeskTicket::GetTicketField($duplicate, 'problem') : '' ), '100%', '400', '75', '20');?>
			<?php endif;?>
        </div>
    </div>

	<!-- Email CC and BCC -->
	<?php if ($supportConfig->extra_email_notification): ?>
    <div class="control-group row-fluid">
        <label class="control-label" for="cc_report"><?php echo JText::_('cc');?></label>
        <div class="controls">
            <input id="cc_report" name="cc_report" type="text" />
            <div id="cc_emails"></div>
        </div>
    </div>
    <div class="control-group row-fluid">
        <label class="control-label" for="bcc_report"><?php echo JText::_('bcc');?></label>
        <div class="controls">
            <input id="bcc_report" name="bcc_report" type="text" />
            <div id="bcc_emails"></div>
        </div>
    </div>
	<?php endif;?>

	<!-- Add Reply -->
    <div class="control-group row-fluid">
        <div class="control-label hidden-phone"></div>
        <div class="controls">
            <a href="javascript:;" onclick="AddReply();" title="<?php echo JText::_('add_reply');?>" class="btn">
	            <i class="ico-comment"></i> <?php echo JText::_('add_reply');?>
            </a>
        </div>
    </div>
	<div id="AddReply" name="AddReply" style="display:none;">
        <div class="control-group row-fluid">
            <label class="control-label" for="reply_date"><?php echo JText::_('tmpl_msg27');?></label>
            <div class="controls">
				<?php echo JHTML::Calendar('', 'reply_date', 'reply_date', '%Y-%m-%d', array('maxlength' => '10', 'style' => 'width:100px;')); ;?>
                <input type="text" class="timepicker" id="reply_hours" name="reply_hours" maxlength="5" value=""/>
                <span class="help-block"><?php echo JText::_("SET_ACTIVITY_DATE");?></span>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="reply_summary"><?php echo JText::_('summary');?></label>
            <div class="controls">
                <input type="text" id="reply_summary" name="reply_summary" value="" maxlength="150">
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="reply"><?php echo JText::_('activity');?></label>
            <div class="controls">
	            <?php if($supportConfig->editor == 'builtin'):?>
                <textarea id="reply" name="reply" class="redactor_agent"></textarea>
	            <?php else:?>
	            <?php echo $editor->display('reply', '', '100%', '400', '75', '20');?>
	            <?php endif;?>
            </div>
        </div>
		<?php if ($workgroupSettings->use_activity):?>
	        <div class="control-group row-fluid">
	            <label class="control-label span4"></label>
	            <div class="control span3">
		            <?php echo JText::_('tmpl_msg12');?>:<br/><?php echo $lists['activity_type'];?>
	            </div>
	            <div class="control span4">
		            <?php echo JText::_('tmpl_msg13');?>:<br/><?php echo $lists['activity_rate'];?>
	            </div>
	        </div>
	        <div class="control-group row-fluid">
	            <label class="control-label"></label>
	            <div class="control span2">
		            <?php echo JText::_('tmpl_msg14');?>:<br/>
	                <input type="text" class="timepicker" id="start_time" name="start_time" maxlength="5" value="08:00" onchange="GetLabourTime();" onblur="GetLabourTime();"/>
	            </div>
	            <div class="control span2">
		            <?php echo JText::_('tmpl_msg15');?>:<br/>
	                <input type="text" class="timepicker" id="end_time" name="end_time" maxlength="5" value="08:00" onchange="GetLabourTime();" onblur="GetLabourTime();" />
	            </div>
	            <div class="control span2">
		            <?php echo JText::_('tmpl_msg16');?>:<br/>
	                <input type="text" class="timepicker" id="break_time" name="break_time" maxlength="5" value="00:00" onchange="GetLabourTime();" onblur="GetLabourTime();" />
	            </div>
	            <div class="control span3">
		            <?php echo JText::_('tmpl_msg17');?>:<br/>
	                <input style="width:100px;" style="text-align:center;" type="text" value="" name="replytime" readonly />
	            </div>
	        </div>
			<?php if ($supportConfig->use_travel): ?>
		        <div class="control-group row-fluid">
		            <label class="control-label"></label>
		            <div class="control span3">
						<?php echo JText::_('tmpl_msg20');?>:<br/>
		                <input type="text" class="timepicker" id="tickettravel" name="tickettravel" maxlength="5" value="<?php echo $clienttravel;?>" />
		            </div>
		            <div class="control span3">
						<?php echo JText::_('tmpl_msg30');?>:<br/><?php echo $lists['travel'];?>
		            </div>
		        </div>
			<?php else: ?>
	            <input type="hidden" id="tickettravel" name="tickettravel" value="0"/>
			<?php endif; ?>
		<?php else: ?>
	        <input type="hidden" name="id_activity_type" value="0"/>
	        <input type="hidden" name="id_activity_rate" value="0"/>
	        <input type="hidden" name="start_time" id="start_time" value="0"/>
	        <input type="hidden" name="end_time" value="0"/>
	        <input type="hidden" name="break_time" value="0"/>
	        <input type="hidden" name="replytime" value="0"/>
	        <input type="hidden" name="tickettravel" value="0"/>
		<?php endif;?>
	</div>

	<!-- Add Attachment -->
	<?php if ($supportConfig->attachs_num > 0): ?>
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
        <div class="control-group row-fluid">
            <label class="control-label" for="available<?php echo $rowloop['number'];?>"><?php echo JText::_('AVAILABLE_CUSTOMER');?></label>
            <div class="controls">
	            <?php echo $rowloop['available'];?>
            </div>
        </div>
		<?php endforeach;?>
	</div>
	<?php endif;?>

	<!-- Buttons -->
    <div class="form-actions" style="margin-left:-10px;margin-right:-10px;margin-bottom:-21px;">
        <button type="button" class="btn btn-success" id="ticket_save" name="ticket_save" onclick="submitbutton('ticket_save');">
	        <?php echo JText::_('save');?>
        </button>
        <button type="button" class="btn btn-link" name="ticket_cancel" onclick="Cancel();">
	        <?php echo JText::_('cancel');?>
        </button>
    </div>

	</form>

	<!-- Alert window -->
	<div id="alertMessage" class="modal" style="display:none;">
	    <div class="modal-header">
	        <a class="close" data-dismiss="modal">&times;</a>
	        <h3><?php echo JText::_('warning');?></h3>
	    </div>
	    <div class="modal-body"><p></p></div>
	    <div class="modal-footer">
	        <a href="javascript:;" onclick="$jMaQma('#alertMessage').modal('hide');" data-dismiss="modal"
	           class="btn"><?php echo JText::_('close');?></a>
	    </div>
	</div>

</div>