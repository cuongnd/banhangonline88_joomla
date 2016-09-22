<div class="maqmahelpdesk container-fluid">

    <h2><?php echo JText::_('timesheet') . ' - ' . JText::_('pathway_edit');?></h2>

    <p class="tar">
        <small><span class="required">*</span> <b><?php echo JText::_("field_required_desc");?></b></small>
    </p>

    <form name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="post" class="form-horizontal">
		<?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup;?>"/>
        <input type="hidden" name="id" value="<?php echo $row->id;?>"/>
        <input type="hidden" id="id_client" name="id_client" value="<?php echo $row->id_client;?>"/>
        <input type="hidden" name="task" value="timesheet_save"/>

        <div class="control-group row-fluid">
            <label class="control-label" for="getclient"><?php echo JText::_('client_name');?></label>
            <div class="controls">
                <input type="text" id="getclient" name="getclient" value="<?php echo $row->clientname;?>" />
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="year"><?php echo JText::_('year');?> <span
                    class="required">*</span></label>
            <div class="control span9">
				<?php echo $lists['year'];?>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="month"><?php echo JText::_('month');?> <span
                    class="required">*</span></label>
            <div class="control span9">
				<?php echo $lists['month'];?>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="day"><?php echo JText::_('day');?> <span
                    class="required">*</span></label>
            <div class="control span9">
				<?php echo $lists['day'];?>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="time"><?php echo JText::_('time');?> <span
                    class="required">*</span></label>
            <div class="control span9">
                <input type="text" id="time" name="time" size="50" maxlength="5" class="span4 timepicker" value="<?php echo $row->time;?>" />
            </div>
        </div>

        <div class="form-actions">
            <div class="btn-group">
                <button type="button" class="btn btn-success" name="insert"
                        onclick="javascript:submitbutton('insert');"><i class="ico-plus ico-white"></i> <?php echo JText::_('save');?></button>
                <button type="button" class="btn btn-link"
                        onclick="javascript:history.go(-1);"><?php echo JText::_('cancel');?></button>
            </div>
        </div>

    </form>

    <div id="alertMessage" class="modal">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">&times;</a>
            <h3><?php echo JText::_('warning');?></h3>
        </div>
        <div class="modal-body"><p></p></div>
        <div class="modal-footer">
            <a href="javascript:;"
               class="btn alertclose"><?php echo JText::_('close');?></a>
        </div>
    </div>

</div>