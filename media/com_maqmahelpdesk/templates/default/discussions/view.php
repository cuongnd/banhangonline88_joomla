<div class="maqmahelpdesk container-fluid">

	<h2><?php echo $row->title; ?></h2>

    <?php if (!$row->published): ?>
    <p align="right"><a href="javascript:;"
                        onclick="DiscussionPublish(<?php echo $id_workgroup;?>,<?php echo $id_category;?>,<?php echo $Itemid;?>,<?php echo $row->id;?>,0);"
                        class="btn btn-success"><?php echo JText::_('publish_question');?></a></p>
    <?php endif;?>

    <?php if ($row->published && $is_support): ?>
    <p align="right"><a
        href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&id_category=<?php echo $id_category;?>&task=discussions_delete&id=<?php echo $row->id;?>"
        class="btn btn-danger"><?php echo JText::_('delete');?></a></p>
    <?php endif;?>

    <div id="discussions">
        <div class="post-box-wrapper">
            <div class="post-box">
                <ol class="messagelist">
                    <li>
                        <div class="message-body">
                            <img alt="" src="<?php echo HelpdeskUser::GetAvatar($row->id_user);?>" class="message-avatar hidden-mobile" height="60" width="60">
                            <div class="message-arrow"></div>
                            <div class="message-box">
                                <div class="message-author">
                                    <strong><?php echo $row->name;?></strong>
                                    <small><?php echo HelpdeskDate::LongDate($row->date_created);?></small>
                                </div>
                                <div class="message-text"><?php
					                echo HelpdeskUtility::AddLines($row->content); ?>
                                    <div class="tags" style="width:65%;"><?php
		                                $tags = explode(',', $row->tags);
		                                foreach ($tags as $tag) {
			                                echo '<span>' . $tag . '</span>';
		                                }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li><?php
				    foreach ($messages as $message):?>
                        <li>
                            <a name="answer<?php echo $message->id;?>"></a>
                            <div class="message-body <?php echo ($row->status == $message->id ? 'selected_answer' : '');?>">
                                <img alt="" src="<?php echo HelpdeskUser::GetAvatar($message->id_user);?>" class="message-avatar hidden-mobile" height="60" width="60">
                                <div class="votes <?php echo ($row->status ? 'selected' : 'normal');?>">
                                    <div class="nrvotes"><?php echo $message->votes;?><br/>
                                        <small><?php echo JText::_('votes');?></small>
                                    </div>
                                    <div class="vote_up"><a href="javascript:;"
                                                            onclick="DiscussionVote(<?php echo $id_workgroup;?>,<?php echo $id_category;?>,<?php echo $Itemid;?>,<?php echo $row->id;?>,<?php echo $message->id;?>,'up');"></a>
                                    </div>
                                    <div class="vote_down"><a href="javascript:;"
                                                              onclick="DiscussionVote(<?php echo $id_workgroup;?>,<?php echo $id_category;?>,<?php echo $Itemid;?>,<?php echo $row->id;?>,<?php echo $message->id;?>,'down');"></a>
                                    </div>
                                </div>
                                <div class="message-arrow"></div>
                                <div class="message-box">
                                    <div class="message-author">
                                        <strong><?php echo $message->name;?></strong>
                                        <small><?php echo HelpdeskDate::LongDate($message->date_created);?></small>
                                    </div>
                                    <div class="message-text"><?php
	                                    echo ($row->status == $message->id ? '<h3 class="selected"><img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/ui/accepted.png" align="left" style="padding-right:5px;" />' . JText::_('accepted_answer') . '</h3>' : '');
									    echo $message->content; ?>

                                        <div class="details">
		                                    <?php if (!$row->status && $row->id_user == $user->id): ?>
                                            <a id="select_button" href="javascript:;"
                                               onclick="DiscussionAccept(<?php echo $id_workgroup;?>,<?php echo $id_category;?>,<?php echo $Itemid;?>,<?php echo $row->id;?>,<?php echo $message->id;?>);"
                                               class="btn btn-success"><?php echo JText::_('accept_answer');?></a>
		                                    <?php endif;?>
		                                    <?php if (!$message->published): ?>
                                            <a href="javascript:;"
                                               onclick="DiscussionPublish(<?php echo $id_workgroup;?>,<?php echo $id_category;?>,<?php echo $Itemid;?>,<?php echo $row->id;?>,<?php echo $message->id;?>);"
                                               class="btn btn-success"><?php echo JText::_('publish_question');?></a>
		                                    <?php endif;?>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </li><?php
				    endforeach; ?>
                </ol>
            </div>
        </div>

        <p>&nbsp;</p>

        <h3 style="border-bottom:1px solid #DFE0E1;"><?php echo JText::_('your_answer');?></h3>
        <?php if ($user->id || (!$user->id && $supportConfig->discussions_anonymous)): ?>
        <?php echo JText::_('can_help_yes'); ?>
        <p><textarea id="answer_text" name="answer_text" class="redactor_<?php echo ($is_support ? 'agent' : 'user'); ?>"></textarea></p>
        <p><?php echo sprintf(JText::_('validate_calculation'), $calculation1, $calculation2);?> <input type="text" size="5" maxlength="2" id="valcalc" name="valcalc" /></p>
        <p><a href="javascript:;"
              onclick="DiscussionReply(<?php echo $id_workgroup;?>,<?php echo $id_category;?>,<?php echo $Itemid;?>,<?php echo $row->id;?>);"
              class="btn btn-success"><?php echo JText::_('post_answer');?></a></p>
        <?php else: ?>
        <?php echo JText::_('can_help'); ?>
        <?php endif;?>
    </div>

</div>