<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('bugtracker');?></h2>

    <form id="maqmaSearchBugs" name="maqmaSearchBugs" action="<?php echo JRoute::_("index.php");?>" method="post"
          class="well well-small">
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup; ?>"/>
        <input type="hidden" name="task" value="bugtracker"/>

        <div class="input-append">
            <input id="searchinput" name="searchinput" type="text" value="<?php echo $searchinput;?>" class="span6" />
            <button type="submit" class="btn" id="searchDiscussions"><i class="ico-search"></i> <?php echo JText::_('search');?></button>
            <a id="postQuestion"
               href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=bugtracker_post"
               class="btn btn-success"><i class="ico-comment ico-white"></i> <?php echo JText::_('create');?></a>
        </div>

	    <br class="clear" />

	    <div class="row-fluid">
		    <div class="span3"><?php echo $lists['type'];?></div>
		    <div class="span3"><?php echo HelpdeskForm::BuildCategories($category, false, true, false, false, true, false, false, false);?></div>
		    <div class="span3"><?php echo $lists['status'];?></div>
		    <div class="span3"><?php echo $lists['assignment'];?></div>
	    </div>

    </form><?php

    if (count($rows)) { ?>
        <table id="tickettable" class="table table-bordered table-striped" cellspacing="0">
            <thead>
            <tr>
                <th nowrap="nowrap" class="sorting<?php echo ($orderby == 'b.slug' ? '_' . $order : '');?>" width="60"><a
                    href="<?php echo $lorder_slug;?>"><?php echo JText::_('code');?></a></th>
                <th nowrap="nowrap" class="sorting<?php echo ($orderby == 'b.title' ? '_' . $order : '');?>"><a
                    href="<?php echo $lorder_title;?>"><?php echo JText::_('title');?></a></th>
                <th nowrap="nowrap" class="sorting<?php echo ($orderby == 'requester' ? '_' . $order : '');?>"><a
                    href="<?php echo $lorder_requester;?>"><?php echo JText::_('user');?></a></th>
                <th nowrap="nowrap" class="sorting<?php echo ($orderby == 'agent' ? '_' . $order : '');?>"><a
                    href="<?php echo $lorder_agent;?>"><?php echo JText::_('tpl_assignedto');?></a></th>
                <th nowrap="nowrap" class="sorting<?php echo ($orderby == 'b.date_updated' ? '_' . $order : '');?>" width="120"><a
                    href="<?php echo $lorder_updated;?>"><?php echo JText::_('date_updated');?></a></th>
                <th nowrap="nowrap" class="sorting<?php echo ($orderby == 'b.status' ? '_' . $order : '');?>"><a
                    href="<?php echo $lorder_status;?>"><?php echo JText::_('status');?></a></th>
            </tr>
            </thead>
            <tbody><?php
            $i = 0;
            foreach ($rows as $row) { ?>
                <tr>
                    <td class="priority_<?php echo $row->priority;?>"><a
                        href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup; ?>&task=bugtracker_view&id=<?php echo $row->id;?>"><?php echo JString::strtoupper($row->slug);?></a>
                    </td>
                    <td><a
                        href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup; ?>&task=bugtracker_view&id=<?php echo $row->id;?>"><?php echo $row->title;?></a>
                    </td>
                    <td><?php echo $row->requester;?></td>
                    <td><?php echo $row->agent;?></td>
                    <td><?php echo HelpdeskDate::DateOffset($supportConfig->date_short,strtotime($row->date_updated));?></td>
                    <td><?php echo JText::_('bug_status_' . $row->status);?></td>
                </tr><?php
            } ?>
            </tbody>
        </table><?php

        $start = (($page + 1) - 15) < 2 ? 1 : (($page + 1) - 15);
        $end = (($page + 1) + 15) <= $pages ? (($page + 1) + 15) : $pages;?>
        <div class="pagination pagination-right">
            <ul>
                <li><a href="<?php echo $plink . '&page=0';?>"><?php echo JText::_('table_fpage');?></a></li>
                <?php for ($i = $start; $i <= $end; $i++): ?>
                <li class="<?php echo ($i - 1) == $page ? 'active' : '';?>"><a
                    href="<?php echo $plink . '&page=' . ($i - 1);?>"><?php echo $i;?></a></li>
                <?php endfor;?>
                <li><a href="<?php echo $plink . '&page=' . ($pages - 1);?>"><?php echo JText::_('table_lpage');?></a>
                </li>
            </ul>
        </div><?php
    } else {
        echo JText::_('no_bugtrackers');
    } ?>

</div>
