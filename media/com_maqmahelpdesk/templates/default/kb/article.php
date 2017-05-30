<div class="maqmahelpdesk container-fluid">

	<h2><?php echo $article->kbtitle; ?></h2>

    <div>
        <div id="kbtools">
            <div class="btn-group">
                <?php if ($is_support): ?>
                <a href="<?php echo JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_edit&id=' . $article->id);?>"
                   class="btn icon edit" title="<?php echo JText::_('edit');?>"><?php echo JText::_('edit');?></a>
                <?php endif;?>
                <a href="javascript:void window.open('<?php echo JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_print&id=' . $id . '&tmpl=component&format=raw');?>', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');"
                   class="btn hidden-phone"
                   title="<?php echo JText::_('print_article');?>"><?php echo JText::_('print_article');?></a>
                <a href="<?php echo JRoute::_(JURI::root() . 'index.php?option=com_maqmahelpdesk&task=pdf_kb&id=' . $id . '&format=raw');?>"
                   class="btn hidden-phone" title="<?php echo JText::_('pdf_version');?>"
                   target="_blank"><?php echo JText::_('pdf_version');?></a>
                <?php if ($workgroupSettings->use_bookmarks): ?>
                <a href="<?php echo JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_bookmark&id=' . $id);?>"
                   class="btn"
                   title="<?php echo JText::_('add_to_bookmarks');?>"><?php echo JText::_('add_to_bookmarks');?></a>
                <?php endif;?>
				<?php if ($supportConfig->kb_enable_comments):?>
                <a href="#kb_comment" data-toggle="modal" class="btn icon chat hidden-phone"
                   title="<?php echo JText::_('add_comment');?>"><?php echo JText::_('add_comment');?></a>
	            <?php endif;?>
            </div>
        </div>
	    <?php if ($supportConfig->kb_enable_rating):?>
        <div id="rating"></div>
        <div id="kbrating">(<?php echo HelpdeskKB::GetVotes($article->id, 'K');?> <?php echo JText::_('votes');?>)</div>
	    <?php endif;?>
    </div>
    <div class="clear"></div>

    <div id="kbcontent">
        <?php echo ($supportConfig->kb_popinfo ? HelpdeskGlossary::Popup(str_replace('\"', '"', $article->text)) : str_replace('\"', '"', $article->text));?>
        <?php if ($supportConfig->kbsocial): ?>
        <div style="margin-top:20px;">
            <div style="float:left;"><a href="http://twitter.com/share" class="twitter-share-button"
                                        data-count="horizontal">Tweet</a>
                <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
            </div>
            <div style="float:left;">
                <iframe
                    src="http://www.facebook.com/plugins/like.php?href=<?php echo JURI::current();?>&amp;layout=button_count&amp;show_faces=false&amp;width=250&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21"
                    scrolling="no" frameborder="0" style="height:62px;width:120px;" allowTransparency="true"></iframe>
            </div>
            <div style="float:left;">
                <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
                <g:plusone annotation="inline" width="120"></g:plusone>
            </div>
        </div>
        <?php endif;?>
    </div>
    <div class="clear"></div>

    <div id="kbother" class="row-fluid well well-small">
	    <div class="span4"><b><?php echo JText::_('author');?></b><br/><?php echo $article->name;?></div>
	    <div class="span2"><b><?php echo JText::_('date_created');?></b><br/><?php echo $article->date_created;?></div>
	    <div class="span2"><b><?php echo JText::_('date_updated');?></b><br/><?php echo $article->date_updated;?></div>
	    <div class="span2"><b><?php echo JText::_('views');?></b><br/><?php echo $article->views;?></div>
	    <div class="span2"><b><?php echo JText::_('code');?></b><br/><?php echo $article->kbcode;?></div>
    </div>
    <div class="clear"></div>

    <?php if (count($attachs)) : ?>
    <div id="attachments">
        <h3><?php echo JText::_('attachments');?> (<?php echo count($attachs);?>)</h3>
        <table width="100%" border="0" cellpadding="0">
            <tr>
                <th nowrap="nowrap"><b><?php echo JText::_('link');?></b></th>
                <th nowrap="nowrap"><b><?php echo JText::_('description');?></b></th>
            </tr>
            <?php foreach ($attachs_rows as $row): ?>
            <tr>
                <td valign="top"><a href="<?php echo $row['link'];?>"><?php echo $row['filename'];?></a></td>
                <td valign="top"><?php echo $row['description'];?></td>
            </tr>
            <?php endforeach;?>
        </table>
    </div>
    <?php endif;?>

    <?php if (count($related)): ?>
    <p>&nbsp;</p>
    <div id="kbrelated">
        <h3><?php echo JText::_('related_articles');?> (<?php echo count($related);?>)</h3>
        <?php foreach ($related_rows as $row): ?>
        <div class="kbrelated_link"><a href="<?php echo $row['link'];?>"
                                       title="<?php echo $row['article'];?>"><?php echo $row['article'];?></a></div>
        <?php endforeach;?>
    </div>
    <div class="clear"></div>
    <?php endif;?>

    <?php if ($supportConfig->kb_enable_comments && count($comments)): ?>
    <p>&nbsp;</p>
    <h3><?php echo JText::_('user_comments');?> (<?php echo count($comments);?>)</h3>
    <?php foreach ($comments_rows as $row): ?>
    <div class="message">
        <div class="left-user">
            <p><img src="<?php echo $row['avatar'];?>" class="mqmavatar" border="0" alt=""/></p>

            <p><?php echo $row['user'];?></p>

            <p><?php echo JText::_('posted_at') . ' ' . HelpdeskDate::LongDate($row['date']);?></p>
        </div>
        <div class="item-preview shadowed previewholder">
            <div class="left-arrow"></div>
            <div class="inner-boundary">
                <div class="inner-border">
                    <div class="item_preview">
                        <?php echo $row['comment'];?>
                    </div>
                </div>
            </div>
        </div>
        <br style="clear:both;"/>
    </div>
    <?php endforeach; ?>
    <?php endif;?>

	<?php if ($supportConfig->kb_enable_comments):?>
    <div id="kb_comment" style="display:none;width:625px;" class="modal fade">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">&times;</a>

            <h3><?php echo JText::_('add_comment'); ?></h3>
        </div>
        <div class="modal-body">
            <form id="kb_comment_form" name="kb_comment_form" method="post"
                  action="<?php echo JRoute::_("index.php");?>">
                <?php echo JHtml::_('form.token'); ?>
                <input type="hidden" name="option" value="com_maqmahelpdesk"/>
                <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
                <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup;?>"/>
                <input type="hidden" name="id" value="<?php echo $article->id;?>"/>
                <input type="hidden" name="task" value="kb_comment"/>
                <textarea class="inputbox" style="width:600px;height:275px;" name="comment"></textarea>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="$jMaQma('#kb_comment_form').submit();" class="btn btn-success"><?php echo JText::_('add_comment');?></button>
            <a href="javascript:;" onclick="$jMaQma('#kb_comment').modal('hide');" class="btn" data-dismiss="modal"><?php echo JText::_('cancel');?></a>
        </div>
    </div>
	<?php endif;?>

    <script type="text/javascript">
        // TODO - remove from here
        $jMaQma(document).ready(function () {
            $jMaQma('#rating').rater('<?php echo JURI::root();?>index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&task=ajax_rating&task2=kb_rate&id=<?php echo $article->id;?>&format=raw', {style:'basic', maxvalue:5, curvalue:<?php echo HelpdeskForm::GetRate($article->id, 'K', 0);?>});
        });
    </script>

</div>