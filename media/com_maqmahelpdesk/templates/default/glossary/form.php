<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_glossary') . ' - ' . JText::_('pathway_edit');?></h2>

    <p class="tar">
        <small><span class="required">*</span> <b><?php echo JText::_("field_required_desc");?></b></small>
    </p>

    <form name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="post" class="form-horizontal">
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup;?>"/>
        <input type="hidden" name="id" value="<?php echo $row->id;?>"/>
        <input type="hidden" name="task" value="glossary_save"/>

        <div class="control-group row-fluid">
            <label class="control-label" for="id_category"><?php echo JText::_('category');?> <span
                class="required">*</span></label>

            <div class="control span9">
                <?php echo $lists['category'];?>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="term"><?php echo JText::_('term');?> <span
                class="required">*</span></label>

            <div class="control span9">
                <input type="text" name="term" size="50" class="span12" value="<?php echo $row->term;?>"
                     />
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="published"><?php echo JText::_('published');?> <span
                class="required">*</span></label>

            <div class="control span9">
                <?php echo $lists['show'];?>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="anonymous_access"><?php echo JText::_('anonymous_access');?> <span
                class="required">*</span></label>

            <div class="control span9">
                <?php echo $lists['anonymous'];?>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="description"><?php echo JText::_('description');?> <span class="required">*</span></label>

            <div class="control span9">
                <?php echo $editor->display('description', $row->description, '100%', '400', '75', '20');?>
            </div>
        </div>

        <div class="form-actions">
            <div class="btn-group">
                <button type="button" class="btn btn-success" name="insert"
                        onclick="javascript:submitbutton('insert');"><?php echo JText::_('save');?></button>
                <button type="button" class="btn"
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