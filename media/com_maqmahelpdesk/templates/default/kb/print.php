<div class="maqmahelpdesk container-fluid">

	<h2><?php echo $article->kbtitle; ?></h2>

    <table width="100%">
        <tr>
            <td valign="top">
                <table width="100%">
                    <tr>
                        <td colspan="2"><?php echo $article->text;?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table width="100%">
                                <tr>
                                    <td colspan="2"><?php echo JText::_('attachments');?> (<?php echo count($attachs);?>
                                        )
                                    </td>
                                </tr>
                                <?php if (count($attachs)) : ?>
                                <?php foreach ($attachs_rows as $row): ?>
                                    <tr>
                                        <td valign="top"><?php echo $row['filename'];?></td>
                                        <td valign="top"><?php echo $row['description'];?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="3"><?php echo JText::_('no_attachments');?></td>
                                </tr>
                                <?php endif;?>
                            </table>
                        </td>
                    </tr>
					<?php if ($supportConfig->kb_enable_comments && count($comments)): ?>
                    <tr>
                        <td colspan="2">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table width="100%">
                                <tr>
                                    <td colspan="3"><?php echo JText::_('user_comments');?>
                                        (<?php echo count($comments);?>)
                                    </td>
                                </tr>
                                <?php if (count($comments)): ?>
                                <?php foreach ($comments_rows as $row): ?>
                                    <tr>
                                        <td valign="top"><?php echo $row['date'];?></td>
                                        <td valign="top"><?php echo $row['user'];?></td>
                                        <td valign="top"><?php echo $row['comment'];?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="3"><?php echo JText::_('no_user_comments');?></td>
                                </tr>
                                <?php endif;?>
                            </table>
                        </td>
                    </tr>
	                <?php endif;?>
                </table>
            </td>
            <td valign="top" width="150">
                <table width="100%" class="moduletable">
                    <tr>
                        <th class="contentpane" colspan="2"><?php echo JText::_('additional');?></th>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('code');?>:</td>
                        <td><?php echo $article->kbcode;?></td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('author');?>:</td>
                        <td><?php echo $article->name;?></td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('date_created');?>:</td>
                        <td><?php echo $article->date_created;?></td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('date_updated');?>:</td>
                        <td><?php echo $article->date_updated;?></td>
                    </tr>
                    <tr>
                        <td><?php echo JText::_('views');?>:</td>
                        <td><?php echo $article->views;?></td>
                    </tr>
                </table>
                <br/>
                <table width="100%" class="moduletable">
                    <tr>
                        <th class="contentpane"><?php echo JText::_('related_articles');?>
                            (<?php echo count($related);?>)
                        </th>
                    </tr>
                    <?php if (count($related)): ?>
                    <?php foreach ($related_rows as $row): ?>
                        <tr>
                            <td><?php echo $row['article'];?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td><?php echo JText::_('no_related');?></td>
                    </tr>
                    <?php endif;?>
                </table>
                <br/>
	            <?php if ($supportConfig->kb_enable_rating):?>
                <table width="100%" class="moduletable">
                    <tr>
                        <th class="contentpane"><?php echo JText::_('rate_module');?></th>
                    </tr>
                    <tr>
                        <td height="20"><?php echo HelpdeskForm::GetRate($article->id, 'K', 1);?>
                            (<?php echo HelpdeskKB::GetVotes($article->id, 'K');?> <?php echo JText::_('votes');?>)
                        </td>
                    </tr>
                </table>
                <br/>
	            <?php endif;?>
            </td>
        </tr>
    </table>

</div>