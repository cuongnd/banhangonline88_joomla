<div class="maqmahelpdesk container-fluid">

	<h2><?php echo $row->title; ?></h2>

    <?php if ($row->registered_only && !$user->id): ?>
    <p><span class="attention"><b><?php echo JText::_('registered_download');?></b></span></p>
    <?php endif;?>

    <table class="helpdesktable" cellpadding="5" cellspacing="0">
        <tr>
            <?php if ($row->image_view != ''): ?>
            <td valign="top" rowspan="6"><img
                src="components/com_maqmahelpdesk/images/downloads/<?php echo $row->image_view;?>"/></td>
            <?php endif;?>
            <td class="odd"><b><?php echo JText::_('dl_updatedon');?></b></td>
            <td class="odd"><?php echo HelpdeskDate::ShortDate($row->date_updated);?></td>
        </tr>
        <tr>
            <td class="even"><b><?php echo JText::_('dl_version2');?></b></td>
            <td class="even"><?php echo $version->version;?></td>
        </tr>
        <tr>
            <td class="odd"><b><?php echo JText::_('views');?></b></td>
            <td class="odd"><?php echo $row->hits;?></td>
        </tr>
        <tr>
            <td class="even"><b><?php echo JText::_('dl_category');?></b></td>
            <td class="even"><?php echo $row->category;?></td>
        </tr>
        <tr>
            <td class="odd"><b><?php echo JText::_('dl_license');?></b></td>
            <td class="odd"><a href="<?php echo JRoute::_($link . 'task=downloads_license&id=' . $row->id_license);?>"
                               target="_blank"><?php echo $row->license;?></a></td>
        </tr>
    </table>

    <br/>

    <div class="btn-group">
        <?php if ($can_download): ?>
        <a href="<?php echo $download_link;?>"
           class="btn btn-success icon arrowdown trackdownload"><?php echo JText::_('dl_download_now');?>
            (<?php echo HelpdeskFile::FormatFileSize(filesize($filename));?>)</a>
        <?php endif;?>
        <?php if ($get_trial): ?>
        <a href="<?php echo $row->evaluation;?>"
           class="btn btn-success icon arrowdown tracktrial"><?php echo JText::_('dl_eval_now');?>
            (<?php echo HelpdeskFile::FormatFileSize(filesize($filename));?>)</a>
        <?php endif;?>
        <?php if ($cant_get): ?>
        <a href="javascript:;" class="btn btn-danger"><?php echo JText::_('dl_noaccess');?></a>
        <?php endif;?>
	    <?php if($row->download_previous):?>
        <a href="<?php echo $curl;?>#allversions" class="btn"><?php echo JText::_('download_previous');?></a>
        <?php endif;?>
    </div>

    <br/>

    <div>
        <h4 class="clearfix"><span class="downloads"><?php echo JText::_('dl_description');?></span>
            <div class="divider">&nbsp;</div>
        </h4>
    </div>
    <?php echo str_replace("\'", "'", $row->description);?>

    <!-- VERSIONS -->
    <?php if (count($rows_versions) && $row->download_previous): ?>
    <a name="allversions"></a>
    <br/>
    <div>
        <h4 class="clearfix"><span class="downloads"><?php echo JText::_('past_versions');?></span>

            <div class="divider">&nbsp;</div>
        </h4>
    </div>
    <table class="table table-striped table-bordered" width="100%" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th align="left"><?php echo JText::_('dl_version2');?></th>
            <th align="left"><?php echo JText::_('dl_description');?></th>
            <th align="center"><?php echo JText::_('dl_filesize');?></th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody><?php
            $i = 0;
            foreach ($versions as $rowv):?>
            <tr>
                <td class="<?php echo (!$i ? 'first' : ($i % 2 ? 'even' : ''));?>"
                    valign="top"><?php echo $rowv['version'];?></td>
                <td class="<?php echo (!$i ? 'first' : ($i % 2 ? 'even' : ''));?>"
                    valign="top"><?php echo $rowv['description'];?></td>
                <td class="<?php echo (!$i ? 'first' : ($i % 2 ? 'even' : ''));?>" valign="top" align="center"
                    nowrap="nowrap"><?php echo $rowv['size'];?></td>
                <td class="<?php echo (!$i ? 'first' : ($i % 2 ? 'even' : ''));?>" valign="top" align="center"
                    nowrap="nowrap"><a href="<?php echo $rowv['download'];?>"
                                       title="<?php echo JText::_("download");?>"><?php echo JText::_("download");?></a>
                </td>
            </tr><?php
                $i++;
            endforeach;?>
        </tbody>
    </table>
    <?php else: ?>

    <img src="media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/info.png" border="0"
         align="absmiddle"/> <?php echo JText::_('dl_no_versions'); ?>

    <?php endif;?>

    <!-- SUBSCRIPTIONS -->
    <?php if ($user->id && $supportConfig->download_notification): ?>
    <p>&nbsp;</p>

    <div class="alert">
        <h3><?php echo JText::_('pathway_subscriptions');?></h3>

        <p><?php echo JText::_('dl_click');?> <a
            href="<?php echo JRoute::_($link . 'task=downloads_subscribe&id=' . $row->id);?>"><?php echo JText::_('dl_here');?></a> <?php echo JText::_('dl_benotified');?>
            <br/>
            <?php echo JText::_('dl_seesubs');?>&nbsp;<a
                href="<?php echo JRoute::_($link . 'task=downloads_subscriptions');?>"><?php echo JText::_('tmpl_msg10');?></a>.
        </p>
    </div>

    <?php endif;?>

</div>