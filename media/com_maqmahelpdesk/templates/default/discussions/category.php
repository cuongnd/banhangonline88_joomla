<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('discussions');?></h2>

    <form id="maqmaSearchForm" name="maqmaSearchForm" action="<?php echo JRoute::_("index.php");?>" method="post"
          class="well well-small">
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup; ?>"/>
        <input type="hidden" name="id_category" value="<?php echo $id_category; ?>"/>
        <input type="hidden" name="task" value="discussions_category"/>

	    <div class="input-append">
            <input id="searchinput" name="searchinput" type="text" value="<?php echo $searchinput;?>" class="span6" />
            <button type="submit" class="btn" id="searchDiscussions"><i class="ico-search"></i> <?php echo JText::_('search');?></button>
            <a id="postQuestion"
               href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=discussions_question&id_category=<?php echo $id_category;?>"
               class="btn btn-success"><i class="ico-comment ico-white"></i> <?php echo JText::_('ask_question');?></a>
        </div>
    </form>

    <p class="clear"><a
        href="<?php echo JURI::root();?>index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup; ?>&id_category=<?php echo $id_category; ?>&task=discussions_rss&format=raw"
        target="_blank"><img src="components/com_maqmahelpdesk/images/rssicon.jpg"
                             align="absmiddle"/> <?php echo JText::_('subscribe_discussions_rss');?></a></p>

    <?php if (count($rows)): ?>

    <div id="discussions">
	    <div class="post-box-wrapper">
		    <div class="post-box">
			    <ol class="messagelist"><?php
			        foreach ($rows as $row)
			        { ?>
					    <li>
					        <h3><a
							        href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=discussions_view&id_category=<?php echo $id_category;?>&id=<?php echo $row->id;?>"
							        title="<?php echo $row->title;?>"><?php echo $row->title;?></a><?php echo (!$row->published ? ' <sup class="unpublished">' . JText::_('unpublished') . '</sup>' : '');?>
					        </h3>
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
				    } ?>
				</ol>
	        </div>
	    </div>

    <?php else: ?>

    <?php echo JText::_('no_discussions'); ?>

    <?php endif;?>

</div>