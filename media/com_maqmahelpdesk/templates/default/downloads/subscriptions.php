<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_subscriptions');?></h2>

    <?php if (count($rows)): ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <lang:dl_category/>
            </td>
            <td>
                <lang:dl_product/>
            </td>
            <td>
                <lang:dl_subsdate/>
            </td>
            <td>
                <lang:dl_lastupdated/>
            </td>
            <td width="20">&nbsp;</td>
        </tr>
        <?php foreach ($subscriptions as $row): ?>
        <tr>
            <td><a href="<?php echo $row['category_link'];?>"><?php echo $row['category'];?></a></td>
            <td><a href="<?php echo $row['product_link'];?>"><?php echo $row['product'];?></a></td>
            <td><?php echo $row['date_subscription'];?></td>
            <td><?php echo $row['date_updated'];?></td>
            <td width="20"><a href="<?php echo $row['delete_link'];?>"><img
                src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/delete.png"
                border="0" alt="<?php echo JText::_('delete');?>"/></a></td>
        </tr>
        <?php endforeach;?>
        <tr>
            <td colspan="5"><br/>&nbsp;</td>
        </tr>
    </table>
    <?php else: ?>

    <img src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/info.png" border="0"
         align="absmiddle"/> <?php echo JText::_('dl_no_subs'); ?>

    <?php endif;?>

</div>