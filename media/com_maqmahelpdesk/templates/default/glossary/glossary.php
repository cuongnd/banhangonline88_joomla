<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_glossary');?></h2>

    <p><?php echo JText::_('glossary_select_category');?></p>

    <p>&nbsp;</p>

    <?php for ($i = 0; $i < count($rows); $i++) :
        $row = $rows[$i]; ?>
        <p><a href="<?php echo JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=glossary_category&id_category=' . $row->id);?>" title="<?php echo JText::_('glossary');?> <?php echo $row->name;?>"><?php echo $row->name;?></a> <span class="lbl"><?php echo $row->total;?></span></p>
    <?php endfor; ?>

</div>