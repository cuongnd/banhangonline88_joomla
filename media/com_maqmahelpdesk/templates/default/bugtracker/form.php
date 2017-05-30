<div class="maqmahelpdesk container-fluid">
	<h2><?php echo JText::_('bugtracker') . ' - ' . JText::_('pathway_edit');?></h2>

    <p class="tar">
        <small><b><span class="required">*</span> <?php echo JText::_("field_required_desc");?></b></small>
    </p>

    <form name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="post" class="form-horizontal" enctype="multipart/form-data">
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" name="task" value="bugtracker_save"/>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
        <input type="hidden" name="id" value="<?php echo $row->id;?>"/>
        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup;?>"/>
        <input type="hidden" name="date_created" value="<?php echo $row->date_created;?>"/>
        <input type="hidden" name="date_updated" value="<?php echo $row->date_updated;?>"/>
        <input type="hidden" name="id_product" value="0"/>
        <input type="hidden" name="id_version" value="0"/>
        <input type="hidden" name="id_version_fix" value="0"/>

        <div class="control-group row-fluid">
            <label class="control-label" for="title"><?php echo JText::_('title');?> <span class="required">*</span></label>
            <div class="controls">
                <input type="text" id="title" name="title"  class="span12" value="<?php echo $row->title;?>"/>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="priority"><?php echo JText::_('priority');?> <span class="required">*</span></label>
            <div class="controls">
                <select id="priority" name="priority">
                    <option value=""></option>
                    <option value="5"><?php echo JText::_('bug_priority_5');?></option>
                    <option value="4"><?php echo JText::_('bug_priority_4');?></option>
                    <option value="3"><?php echo JText::_('bug_priority_3');?></option>
                    <option value="2"><?php echo JText::_('bug_priority_2');?></option>
                    <option value="1"><?php echo JText::_('bug_priority_1');?></option>
                </select>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="type"><?php echo JText::_('type');?> <span class="required">*</span></label>
            <div class="controls">
                <select id="type" name="type">
                    <option value=""></option>
                    <option value="B"><?php echo JText::_('bug_type_b');?></option>
                    <option value="I"><?php echo JText::_('bug_type_i');?></option>
                    <option value="N"><?php echo JText::_('bug_type_n');?></option>
                    <option value="R"><?php echo JText::_('bug_type_r');?></option>
                </select>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="id_category"><?php echo JText::_('category');?> <span class="required">*</span></label>
            <div class="controls">
                <?php echo HelpdeskForm::BuildCategories(0, false, true, false, false, true, false, false, false);?>
            </div>
        </div>
        <?php if ($is_support):?>
        <div class="control-group row-fluid">
            <label class="control-label" for="id_assign"><?php echo JText::_('tpl_assignedto');?></label>
            <div class="controls">
                <?php echo $lists['id_assign'];?>
            </div>
        </div>
        <?php endif;?>
        <div class="control-group row-fluid">
            <label class="control-label" for="description"><?php echo JText::_('description');?> <span class="required">*</span></label>
            <div class="controls">
                <textarea id="description"
                          name="description"
                          class="redactor_<?php echo ($is_support ? 'agent' : 'user'); ?>"><?php echo $row->content;?></textarea>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="file"><?php echo JText::_('file');?></label>
            <div class="controls">
                <input type="file" id="file" name="file" class="span12" value=""/>
            </div>
        </div>
        <?php if ($row->id && $is_support):?>
	        <div class="control-group row-fluid">
	            <label class="control-label" for="status"><?php echo JText::_('status');?> <span class="required">*</span></label>
	            <div class="controls">
	                <select id="status" name="status">
	                    <option value="P"><?php echo JText::_('bug_status_p');?></option>
	                    <option value="O"><?php echo JText::_('bug_status_o');?></option>
	                    <option value="I"><?php echo JText::_('bug_status_i');?></option>
	                    <option value="R"><?php echo JText::_('bug_status_r');?></option>
	                    <option value="c"><?php echo JText::_('bug_status_c');?></option>
	                    <option value="D"><?php echo JText::_('bug_status_d');?></option>
	                </select>
	            </div>
	        </div>
        <?php else:?>
            <input type="hidden" id="status" name="status" value="<?php echo $row->status;?>" />
        <?php endif;?>

        <div class="form-actions">
            <button type="submit" class="btn btn-success" onclick="submitbutton();"><?php echo JText::_('save');?></button>
            <button type="button" class="btn" onclick="Cancel();"><?php echo JText::_('cancel');?></button>
        </div>

    </form>

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

<?php if ($row->id && $is_support):?>
<script type="text/javascript">
    $jMaQma("#priority").val('<?php echo $row->priority;?>');
    $jMaQma("#type").val('<?php echo $row->type;?>');
    $jMaQma("#id_category").val('<?php echo $row->id_category;?>');
    $jMaQma("#status").val('<?php echo $row->status;?>');
</script>
<?php endif;?>