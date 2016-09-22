<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_my_bookmarks');?></h2>

    <?php echo JText::_('bookmarks_header');?>

    <p>&nbsp;</p>

    <h3><?php echo JText::_('tickets');?></h3><?php
    if (count($tickets)) {
        ?>
        <table class="table table-striped table-bordered" cellspacing="0">
            <thead>
            <tr>
                <th width="100"><?php echo JText::_('id');?></th>
                <th><?php echo JText::_('subject');?></th>
                <th width="120"><?php echo JText::_('date_created');?></th>
                <th width="75"><?php echo JText::_('status');?></th>
                <th width="43"><?php echo JText::_('remove');?></th>
            </tr>
            </thead>
	        <tbody><?php
            $i = 0;
            foreach ($tickets_rows as $row)
            {
                ?>
                <tr>
                    <td width="100" nowrap="nowrap"><a
                        href="<?php echo $row['link'];?>"><?php echo $row['ticketid'];?></a></td>
                    <td><a href="<?php echo $row['link'];?>"><?php echo $row['subject'];?></a></td>
                    <td width="120"><?php echo $row['date_created'];?></td>
                    <td><?php echo $row['status'];?></td>
                    <td><a href="<?php echo $row['delete_bookmark'];?>"><img
                        src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/delete.png"
                        border="0" align="absmiddle"/></a></td>
                </tr><?php
            } ?>
	        </tbody>
        </table><?php
    } else {
        echo JText::_('no_tickets_bookmarks');
    }?>

    <p>&nbsp;</p>

    <h3><?php echo JText::_('kb_articles');?></h3><?php
    if (count($articles)) {
        ?>
        <table class="table table-striped table-bordered" cellspacing="0">
            <thead>
            <tr>
                <th width="250"><?php echo JText::_('title');?></th>
                <th width="150"><?php echo JText::_('user');?></th>
                <th width="120"><?php echo JText::_('date_created');?></th>
                <th width="120"><?php echo JText::_('date_updated');?></th>
                <th width="15"><?php echo JText::_('remove');?></th>
            </tr>
            </thead>
	        <tbody><?php
            $i = 0;
            foreach ($articles_rows as $row)
            {
                ?>
                <tr>
                    <td><a href="<?php echo $row['link'];?>"><?php echo $row['title'];?></a></td>
                    <td><?php echo $row['author'];?></td>
                    <td><?php echo $row['date_created'];?></td>
                    <td><?php echo $row['date_updated'];?></td>
                    <td><a href="<?php echo $row['delete_bookmark'];?>"><img
                        src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/delete.png"
                        border="0" align="absmiddle"/></a></td>
                </tr><?php
            } ?>
	        </tbody>
        </table><?php
    } else {
        echo JText::_('no_articles_bookmarks');
    } ?>

</div>