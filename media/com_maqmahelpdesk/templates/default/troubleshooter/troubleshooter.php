<div class="maqmahelpdesk container-fluid">

	<h2><?php echo ($id ? $trouble->title : JText::_('pathway_troubleshooter')); ?></h2>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" class="contentpane">
        <tr>
            <td width="60%" valign="top" class="contentdescription"
                colspan="2"><?php echo JText::_('troubleshooter_header');?></td>
        </tr>
    </table>

    <?php if (isset($trouble)): ?>
    <h3><?php echo $trouble->title;?></h3>
    <?php echo $trouble->description; ?>
    <?php endif;?>

    <br/>

    <?php if (count($rows)): ?>
    <?php foreach ($troubles as $row): ?>
        <a href="<?php echo $row['link'];?>"><?php echo $row['title'];?></a>
        <?php endforeach; ?>
    <?php else: ?>
    <?php echo JText::_('troubleshooter_norecords'); ?><br/>
    <?php endif;?>

    <br/><br/>
    <table width="100%">
        <tr>
            <td>
                <div align="center">
                    <input type="button" name="back" value="<?php echo JText::_('troubleshooter_back');?>"
                           onClick="javascript:history.go(-1);" class="btn icon arrowleft"/>
                    <input type="button" name="start" value="<?php echo JText::_('troubleshooter_start');?>"
                           onClick="<?php echo "javascript:window.location='" . JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=troubleshooter') . "';"?>"
                           class="btn"/>
                </div>
            </td>
        </tr>
    </table>

</div>