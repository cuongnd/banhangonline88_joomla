<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('discussions') . ' - ' . JText::_('pathway_edit');?></h2>

    <p><?php echo sprintf(JText::_('pre_question_explain'), 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_new');?></p>

    <p class="tar">
        <small><span class="required">*</span> <b><?php echo JText::_("field_required_desc");?></b></small>
    </p>

    <form name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="post" class="form-horizontal">
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup;?>"/>
        <input type="hidden" name="id_category" value="<?php echo $id_category;?>"/>
        <input type="hidden" name="task" value="discussions_save"/>

        <div class="control-group row-fluid">
            <label class="control-label" for="title"><?php echo JText::_('title');?> <span
                class="required">*</span></label>

            <div class="control span9">
                <input type="text" id="title" name="title"
                       class="span12" value=""/>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="description"><?php echo JText::_('question');?> <span
                class="required">*</span></label>

            <div class="control span9">
                <textarea id="question_content"
                          name="question_content"
                          class="redactor_<?php echo ($is_support ? 'agent' : 'user'); ?>"></textarea>

                <p class="help-block"><?php echo JText::_('insert_code_explain');?></p>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="tags"><?php echo JText::_('tags');?> <span
                class="required">*</span></label>

            <div class="control span9">
                <input type="text" id="tags" name="tags"
                       class="span12" value="" maxlength="50"/>
            </div>
        </div>
        <div class="control-group row-fluid">
            <div class="control span9">
                <?php echo sprintf(JText::_('validate_calculation'), $calculation1, $calculation2);?> <input type="text" size="5" maxlength="2" id="valcalc" name="valcalc" />
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success"
                    onclick="submitbutton();"><?php echo JText::_('save');?></button>
            <button type="button" class="btn" onclick="Cancel();"><?php echo JText::_('cancel');?></button>
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