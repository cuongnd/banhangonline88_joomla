<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_new');?> - <?php echo HelpdeskDepartment::GetName($id_workgroup);?></h2>

	<form id="adminForm" name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="post" enctype="multipart/form-data"
	      onSubmit="return JSValidDueDate();" class="form-horizontal">
	    <?php echo JHtml::_('form.token'); ?>
	    <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	    <input type="hidden" id="Itemid" name="Itemid" value="<?php echo $Itemid;?>"/>
	    <input type="hidden" name="id_workgroup" id="id_workgroup" value="<?php echo $id_workgroup;?>"/>
	    <input type="hidden" name="id_directory" id="id_directory" value="<?php echo $id_directory;?>"/>
	    <input type="hidden" name="id" value="0"/>
	    <input type="hidden" name="task" value="ticket_save"/>
	    <input type="hidden" name="assign_to" id="assign_to" value="<?php echo $workgroupSettings->auto_assign;?>"/>
	    <input type="hidden" name="id_user" value="<?php echo $user->id;?>"/>
	    <input type="hidden" name="id_client" value="<?php echo $is_client;?>"/>
	    <input type="hidden" name="id_status" value="<?php echo HelpdeskStatus::GetDefault();?>"/>
	    <input type="hidden" name="source" value="W"/>
	    <input type="hidden" name="reply" value=""/>
	    <input type="hidden" name="now_date" value=""/>
	    <input type="hidden" name="duedate_date_default" value=""/>
	    <input type="hidden" id="duedate_date" name="duedate_date" value=""/>
	    <input type="hidden" name="duedate_hours" id="duedate_hours" value=""/>

        <div class="control-group row-fluid">
            <label class="control-label" for="subject"><?php echo JText::_('subject');?> <span class="required">*</span></label>
            <div class="controls">
                <input type="text" id="subject" name="subject" />
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

	    <?php echo $cfields_hiddenfield;?>

        <!-- Problem field -->
        <div class="control-group row-fluid">
            <label class="control-label" for="problem"><?php echo JText::_('message');?> <span class="required">*</span></label>
            <div class="controls">
                <textarea id="problem" name="problem" class="redactor_user"></textarea>
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
			<?php endforeach;?>
        </div>
		<?php endif;?>

        <div class="form-actions" style="margin-left:-10px;margin-right:-10px;margin-bottom:-21px;">
            <button type="button" class="btn btn-success" id="ticket_save" name="ticket_save" onclick="submitbutton('ticket_save');">
				<?php echo JText::_('save');?>
            </button>
            <button type="button" class="btn btn-link" name="ticket_cancel" onclick="Cancel();">
				<?php echo JText::_('cancel');?>
            </button>
        </div>
	</form>

	<div id="alertMessage" class="modal">
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