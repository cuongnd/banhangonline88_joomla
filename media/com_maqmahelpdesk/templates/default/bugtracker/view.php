<div class="maqmahelpdesk container-fluid">

	<h2><?php echo $row->title; ?></h2>

    <?php if ($is_support): ?>
    <p align="right">
        <a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&id_category=<?php echo $row->id_category;?>&task=bugtracker_post&id=<?php echo $row->id;?>" class="btn"><?php echo JText::_('edit');?></a>
        &nbsp;&nbsp;
        <a href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&id_category=<?php echo $row->id_category;?>&task=bugtracker_delete&id=<?php echo $row->id;?>" class="btn btn-danger"><?php echo JText::_('delete');?></a>
    </p>
    <?php endif;?>

    <div id="bugtracker">
        <form id="bugtrackerForm" name="bugtrackerForm" class="form-horizontal" action="index.php" method="post">
            <?php echo JHtml::_('form.token'); ?>
            <input type="hidden" name="option" value="com_maqmahelpdesk" />
            <input type="hidden" name="task" value="bugtracker_reply" />
            <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
            <input type="hidden" name="id" value="<?php echo $row->id;?>" />
            <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup;?>" />
            <input type="hidden" name="date_created" value="<?php echo $row->date_created;?>" />
            <input type="hidden" name="date_updated" value="<?php echo $row->date_updated;?>" />
            <input type="hidden" name="id_product" value="0" />
            <input type="hidden" name="id_version" value="0" />
            <input type="hidden" name="id_version_fix" value="0" />
            <input type="hidden" name="id_assign" value="<?php echo $row->id_assign;?>" />

            <div class="control-group row-fluid">
                <label class="control-label nopadding"><?php echo JText::_('created');?></label>
                <div class="controls" style="padding-top:5px;"><?php echo HelpdeskDate::LongDate($row->date_created)?></div>
            </div>
            <div class="control-group row-fluid">
                <label class="control-label nopadding"><?php echo JText::_('date_updated');?></label>
                <div class="controls" style="padding-top:5px;"><?php echo HelpdeskDate::LongDate($row->date_updated)?></div>
            </div>
            <div class="control-group row-fluid">
                <label class="control-label nopadding"><?php echo JText::_('type');?></label>
                <div class="controls" style="padding-top:5px;"><?php echo JText::_('bug_type_'.$row->type);?></div>
            </div>
            <div class="control-group row-fluid">
                <label class="control-label nopadding"><?php echo JText::_('category');?></label>
                <div class="controls" style="padding-top:5px;"><?php echo $row->category?></div>
            </div>
            <div class="control-group row-fluid">
                <label class="control-label nopadding"><?php echo JText::_('priority');?></label>
                <div class="controls" style="padding-top:5px;"><?php echo JText::_('bug_priority_'.$row->priority)?></div>
            </div>
            <div class="control-group row-fluid">
                <label class="control-label nopadding"><?php echo JText::_('user');?></label>
                <div class="controls" style="padding-top:5px;"><?php echo $row->requester?></div>
            </div>
            <div class="control-group row-fluid">
                <label class="control-label nopadding"><?php echo JText::_('tpl_assignedto');?></label>
                <div class="controls" style="padding-top:5px;"><?php echo $row->agent?></div>
            </div>
            <div class="control-group row-fluid">
                <label class="control-label nopadding"><?php echo JText::_('status');?></label>
                <div class="controls" style="padding-top:5px;"><?php echo JText::_('bug_status_'.$row->status)?></div>
            </div>

	        <div class="post-box-wrapper">
		        <div class="post-box">
			        <ol class="messagelist">
				        <li>
					        <a name="answer<?php echo $message->id;?>"></a>
					        <div class="message-body">
						        <img alt="" src="<?php echo HelpdeskUser::GetAvatar($row->id_user);?>" class="message-avatar hidden-mobile" height="60" width="60">
						        <div class="message-arrow"></div>
						        <div class="message-box">
							        <div class="message-author">
								        <strong><?php echo $row->requester;?></strong>
								        <small><?php echo HelpdeskDate::LongDate($row->date_created);?></small>
							        </div>
							        <div class="message-text"><?php
								        echo HelpdeskUtility::AddLines($row->content);
								        $reply_attachs = HelpdeskBugTracker::GetMessageAttachments($row->id, 0);
								        for ($c=0; $c<count($reply_attachs); $c++)
								        {
									        $reply_attach = $reply_attachs[$c];
									        $link = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker_download&id=' . $reply_attach->id_file . '&extid=' . $reply_attach->id);?>
									        <p>
									        <img src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/attach.png" alt="" align="left" />
									        <a href="<?php echo $link;?>"><?php echo $reply_attach->filename;?></a>
									        </p><?php
								        } ?>
							        </div>
						        </div>
					        </div>
				        </li><?php
				        foreach ($messages as $message):?>
					        <li>
						        <a name="answer<?php echo $message->id;?>"></a>
						        <div class="message-body">
							        <img alt="" src="<?php echo HelpdeskUser::GetAvatar($message->id_user);?>" class="message-avatar hidden-mobile" height="60" width="60">
							        <div class="message-arrow"></div>
							        <div class="message-box">
								        <div class="message-author">
									        <strong><?php echo $message->name;?></strong>
									        <small><?php echo HelpdeskDate::LongDate($message->date_created);?></small>
								        </div>
								        <div class="message-text"><?php
									        echo $message->content;
									        $reply_attachs = HelpdeskBugTracker::GetMessageAttachments($row->id, $message->id);
									        for ($c=0; $c<count($reply_attachs); $c++)
									        {
										        $reply_attach = $reply_attachs[$c];
										        $link = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker_download&id=' . $reply_attach->id_file . '&extid=' . $reply_attach->id);?>
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
	        </div>

            <p>&nbsp;</p>
            <?php if ($user->id): ?>
            <h3 style="border-bottom:1px solid #DFE0E1;"><?php echo JText::_('your_answer');?></h3>
            <p><textarea id="reply" name="reply" class="redactor_<?php echo ($is_support ? 'agent' : 'user'); ?>"></textarea></p>
            <p><a href="javascript:;" onclick="$jMaQma('#bugtrackerForm').submit();" class="btn btn-success"><?php echo JText::_('save');?></a></p>
            <?php endif;?>
        </form>
    </div>
</div>