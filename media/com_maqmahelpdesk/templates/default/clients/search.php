<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_clients');?></h2>

    <form name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="post">
        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup;?>"/>
        <input type="hidden" name="task" value="client_list"/>
	    <?php echo JHtml::_('form.token'); ?>

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <b><?php echo JText::_('search_in');?>:</b>
                    <?php echo $searchby;?>
                </td>
                <td>
                    <b><?php echo JText::_('search_keyword');?>:</b>
                    <input type="text" class="inputbox" size="50" name="searchfor"
                           value="<?php echo urldecode(htmlspecialchars($searchfor));?>"/>
                </td>
                <td>
                    <button type="submit" class="btn"><?php echo JText::_('search');?></button>
                </td>
            </tr>
        </table><?php

        if (count($clients)) { ?>
            <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><?php echo JText::_('name');?></th>
                    <th width="200"><?php echo JText::_('contact');?></th>
                    <th nowrap><?php echo JText::_('phone');?></th>
                </tr>
                </thead>
                <tbody><?php
                    $i = 0;
                    foreach ($clients_rows as $row) {
                        ?>
                    <tr class="<?php echo (!$i ? 'first' : ($i % 2 ? 'even' : ''));?>">
                        <td><a href="<?php echo $row['link'];?>"><?php echo $row['name'];?></a></td>
                        <td><a href="mailto:<?php echo $row['email'];?>"><?php echo $row['contactname'];?></a></td>
                        <td nowrap><?php echo $row['phone'];?></td>
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
                    <li><a
                        href="<?php echo $plink . '&page=' . ($pages - 1);?>"><?php echo JText::_('table_lpage');?></a>
                    </li>
                </ul>
            </div><?php
        } else {
            echo JText::_('no_clients_found');
        } ?>
    </form>

</div>