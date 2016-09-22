<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_glossary');?></h2>

    <p><?php echo JText::_('glossary_header');?></p>

    <p>&nbsp;</p>

    <?php if (count($rows)) : ?>
    <div id="myList-nav"></div>
    <ul id="myList">
        <?php foreach ($rows as $row): ?>
        <li>
            <h3><?php echo str_replace("\'", "'", $row->title) . ($is_support ? '<a href="index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=glossary_edit&id_category=' . $id_category . '&id=' . $row->id . '"><img src="media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/edit.png" border="0" align="absmiddle" hspace="3" /></a>' : '');?></h3>
            <?php echo str_replace("\'", "'", $row->term);?></li>
        <?php endforeach;?>
    </ul>
    <?php else: ?>
    <p><?php echo JText::_('no_glossary');?></p>
    <?php endif; ?>

</div>