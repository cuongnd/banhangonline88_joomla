<div class="maqmahelpdesk container-fluid">

	<h2><?php echo ($id ? $categoryTitle : JText::_('pathway_downloads')); ?></h2>

    <!-- CATEGORIES -->
    <?php if (count($rowsCat)): ?>
    <?php foreach ($categories as $row): ?>
        <div class="depicon">
            <?php if ($supportConfig->downloads_badges):?>
            <div class="flag"><img src="components/com_maqmahelpdesk/images/<?php echo $row['image_folder'];?>" alt=""/></div>
            <?php endif;?>
            <a href="<?php echo $row['link'];?>" style="text-decoration:none;">
                <img src="media/com_maqmahelpdesk/images/logos/<?php echo $row['image'];?>" align="left" class="logo"
                     alt="<?php echo $row['title'];?>"/>
                <span style="font-size:16px;font-weight:bold;"><?php echo $row['title'];?></span><br/>
                <small><?php echo $row['description_short'];?></small>
            </a>
        </div>
        <?php endforeach; ?>
    <?php endif;?>

    <div class="clear"></div>

    <!-- PRODUCTS -->
    <?php if (count($rows_edited)): ?>
    <?php foreach ($products as $row): ?>
        <div class="depicon">
            <a href="<?php echo $row['link'];?>" style="text-decoration:none;"><img src="<?php echo $row['image'];?>"
                                                                                    align="left" class="logo"
                                                                                    alt="<?php echo $row['title'];?>"/>
                <span style="font-size:16px;font-weight:bold;"><?php echo $row['title'];?></span><br/>
                <small><?php echo $row['description_short'];?></small>
            </a>
        </div>
        <?php endforeach; ?>
    <?php endif;?>

    <div class="clear"></div>

    <!-- LEGEND -->
    <?php if ($supportConfig->downloads_badges):?>
    <div>
        <small>
            <img src="components/com_maqmahelpdesk/images/legend-green.png" width="16" align="absmiddle"/> <?php echo JText::_('dl_today');?>&nbsp;&nbsp;&nbsp;
            <img src="components/com_maqmahelpdesk/images/legend-blue.png" width="16" align="absmiddle"/> <?php echo JText::_('dl_week');?>&nbsp;&nbsp;&nbsp;
            <img src="components/com_maqmahelpdesk/images/legend-yellow.png" width="16" align="absmiddle"/> <?php echo JText::_('dl_1week');?>&nbsp;&nbsp;&nbsp;
            <img src="components/com_maqmahelpdesk/images/legend-red.png" width="16" align="absmiddle"/> <?php echo JText::_('dl_1month');?>
        </small>
    </div>
    <?php endif;?>

    <!-- SUBSCRIPTIONS -->
    <?php if ($user->id && $supportConfig->download_notification): ?>
    <div class="alert">
        <img src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/alert.png"
             align="absmiddle"/> <?php echo JText::_('dl_seesubs');?> <a
        href="<?php echo JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=downloads_subscriptions');?>"><b><?php echo JText::_('tmpl_msg10');?></b></a>.
    </div>
    <?php endif;?>

</div>