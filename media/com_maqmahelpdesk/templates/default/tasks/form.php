<div class="maqmahelpdesk container-fluid">

	<h2><?php echo ($id ? JText::_('pathway_edit') : JText::_('pathway_new')); ?></h2>

    <p class="tar">
        <small><span class="required">*</span> <b><?php echo JText::_("field_required_desc");?></b></small>
    </p>

    <form id="addTask" name="addTask" action="<?php echo JRoute::_("index.php");?>" method="post" class="form-horizontal">
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup;?>"/>
        <input type="hidden" name="id" value="<?php echo $row->id;?>"/>
        <input type="hidden" name="task" value="calendar_save"/>

        <?php if ($row->id_ticket > 0): ?>
        <div class="control-group row-fluid">
            <label class="control-label"><?php echo JText::_('ticketid');?></label>
            <div class="controls span9">
                <a href="<?php echo $ticket_link;?>"><?php echo $ticket_mask;?></a>
            </div>
        </div>
        <?php endif;?>

        <div class="control-group row-fluid">
            <label class="control-label"><?php echo JText::_('date');?> <span class="required">*</span></label>
            <div class="controls span9">
                <?php echo $content_date;?>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="input01"><?php echo JText::_('time');?> <span class="required">*</span></label>
            <div class="controls span9">
                <input type="text" class="span4 timepicker" name="time" maxlength="5" value="<?php echo JString::substr($row->date_time, 11, 5);?>"/>
            </div>
        </div>
        <?php if ($is_support && $is_manager): ?>
        <div class="control-group row-fluid">
            <label class="control-label" for="id_user"><?php echo JText::_('tmpl_msg24');?> <span class="required">*</span></label>
            <div class="controls span9">
                <?php echo $lists['id_user'];?>
            </div>
        </div>
        <?php else: ?>
        <input type="hidden" name="id_user" value="<?php echo $user->id;?>"/>
        <?php endif;?>
        <div class="control-group row-fluid">
            <label class="control-label" for="status"><?php echo JText::_('open');?> <span
                class="required">*</span></label>
            <div class="controls span9">
                <?php echo $lists['taskstatus'];?>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="description"><?php echo JText::_('description');?> <span class="required">*</span></label>
            <div class="controls span9">
                <textarea id="taskfield" name="taskfield" style="height:125px;" class="span12"><?php echo $row->task;?></textarea>
            </div>
        </div>

        <div class="form-actions" style="margin-left:-10px;margin-right:-10px;margin-bottom:-21px;">
            <button type="submit" class="btn btn-success" id="submit2" name="submit2" onclick="TaskCreateSubmit();">
			    <?php echo JText::_('save');?>
            </button>
            <button type="button" class="btn btn-link" name="btncancel" onclick="cancelTask();">
		        <?php echo JText::_('cancel');?>
            </button>
			<?php if ($row->id): ?>
            <button type="button" class="btn btn-danger" name="btncancel" onclick="deleteTask();">
			    <?php echo JText::_('delete');?>
            </button>
	        <?php endif;?>
        </div>

    </form>

    <script type="text/javascript"><?php
    if ($row->id) {
    	echo '$jMaQma("#id_user").val([' . $row->id_user . ']);';
    } ?>
    </script>

</div>