<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_myarticles');?></h2>

    <h4><?php echo JText::_('myarticles_header');?></h4>

    <table width="100%" class="table table-striped table-bordered">
        <thead>
        <tr>
            <th><?php echo JText::_('title');?></th>
            <th width="75" class="tac"><?php echo JText::_('date_created');?></th>
            <th width="75" class="tac"><?php echo JText::_('date_updated');?></th>
            <th width="50" class="tac"><?php echo JText::_('views');?></th>
            <th width="80" class="tac"><?php echo JText::_('rating');?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($articles)) : ?>
            <?php foreach ($articles_rows as $row): ?>
            <tr>
                <td><a href="<?php echo $row['link'];?>"><?php echo $row['title'];?></a></td>
                <td width="75" class="tac"><?php echo $row['date_created'];?></td>
                <td width="75" class="tac"><?php echo $row['date_updated'];?></td>
                <td width="50" class="tac"><?php echo $row['views'];?></td>
                <td width="80" class="tac"><?php echo $row['rate_image'];?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
	        <tr>
	            <td colspan="5" height="10"><?php echo JText::_('kb_no_itens');?></td>
	        </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <small><?php echo $pagelinks;?></small>
    <small><?php echo $pagecounter;?></small>

</div>