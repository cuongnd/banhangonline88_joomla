<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_kb') . ' - ' . JText::_('pathway_edit');?></h2>

    <p class="tar">
        <small><span class="required">*</span> <b><?php echo JText::_("field_required_desc");?></b></small>
    </p>

    <form id="adminForm" name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="post" enctype="multipart/form-data" class="form-horizontal">
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup;?>"/>
        <input type="hidden" name="id" value="<?php echo $id;?>"/>
        <input type="hidden" name="task" value="kb_save"/>
        <input type="hidden" name="categories" value=""/>

        <div class="control-group row-fluid">
            <label class="control-label" for="code"><?php echo JText::_('code');?></label>
            <div class="control span9">
                <input type="text" id="code" name="code" class="span12" value="<?php echo $article->code;?>" maxlength="150">
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="title"><?php echo JText::_('title');?> <span class="required">*</span></label>
            <div class="control span9">
                <input type="text" id="title" name="title" class="span12" value="<?php echo $article->title;?>">
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="categories"><?php echo JText::_('categories');?> <span class="required">*</span></label>
            <div class="control span9">
	            <?php echo HelpdeskForm::BuildCategories(0, false, true, true, true);?>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="keywords"><?php echo JText::_('keywords');?></label>
            <div class="control span9">
                <input type="text" id="keywords" name="keywords" class="span12" value="<?php echo $article->keywords;?>">
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="kbcontent"><?php echo JText::_('activity');?> <span class="required">*</span></label>
            <div class="control span9">
			    <?php echo $editor->display('kbcontent',$article->content, '500', '500', '75', '20');?>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="faq"><?php echo JText::_('show_faq');?> <span class="required">*</span></label>
            <div class="control span9">
	            <?php echo HelpdeskForm::SwitchCheckbox('radio', 'faq', $captions, $values, $article->faq, 'switch');?>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="anonymous"><?php echo JText::_('access');?> <span class="required">*</span></label>
            <div class="control span9">
	            <?php echo $lists['anonymous'];?>
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="publish"><?php echo JText::_('published');?> <span class="required">*</span></label>
            <div class="control span9">
	            <?php echo HelpdeskForm::SwitchCheckbox('radio', 'show', $captions, $values, $article->publish, 'switch');?>
            </div>
        </div>
	    <?php if ($supportConfig->kb_approvement && $is_manager < 7): ?>
        <div class="control-group row-fluid">
	            <label class="control-label"><?php echo JText::_('approved');?> <span class="required">*</span></label>
                <div class="control span9">
				    <?php echo HelpdeskForm::SwitchCheckbox('radio', 'approved', $captions, $values, $article->approved, 'switch');?>
	            </div>
	        </div>
	    <?php else:?>
            <input type="hidden" name="approved" value="<?php echo $article->approved;?>" />
	    <?php endif;?>

	    <h3><?php echo JText::_('attachs_details');?></h3>
        <div class="control-group row-fluid">
            <label class="control-label" for="file"><?php echo JText::_('file');?></label>
            <div class="control span9">
                <input class="input-file" id="file" name="file" type="file">
            </div>
        </div>
        <div class="control-group row-fluid">
            <label class="control-label" for="desc0"><?php echo JText::_('description');?></label>
            <div class="control span9">
                <textarea class="span12" id="desc0" name="desc0" style="height:100px;"></textarea>
            </div>
        </div>

        <div class="form-actions" style="margin-left:-10px;margin-right:-10px;margin-bottom:-21px;">
            <button type="button" class="btn btn-success" id="submit2" name="submit2" onclick="formvalidate();">
			    <?php echo JText::_('save');?>
            </button>
            <button type="button" class="btn btn-link" name="btncancel" onclick="Cancel();">
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
            <a href="javascript:;" onclick="$jMaQma('#alertMessage').modal('hide');" class="btn" data-dismiss="modal"><?php echo JText::_('close');?></a>
        </div>
    </div>

</div>