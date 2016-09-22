<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_search');?></h2>

    <div id="overDiv" style="position:absolute; visibility:hidden; z-index: 10000;"></div>
    <form name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="POST">
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup; ?>"/>
        <input type="hidden" name="task" value="kb_search"/>

        <h3><?php echo JText::_('search_by_keywords');?>:</h3>
        <table width="100%">
            <tr>
                <td><?php echo JText::_('include_at_least_one');?>:</td>
                <td><input type="text" class="inputbox" size="70" name="one_word"
                           value="<?php echo urldecode(htmlspecialchars($one_word)); ?>"/><br/></td>
            </tr>
            <tr>
                <td><?php echo JText::_('include_phrase');?>:</td>
                <td><input type="text" class="inputbox" size="70" name="exact_phrase"
                           value="<?php echo urldecode(htmlspecialchars($exact_phrase));?>"/><br/></td>
            </tr>
            <tr>
                <td><?php echo JText::_('include_all_words');?>:</td>
                <td><input type="text" class="inputbox" size="70" name="all_words"
                           value="<?php echo urldecode(htmlspecialchars($all_words));?>"/><br/></td>
            </tr>
            <tr>
                <td><?php echo JText::_('exclude_words');?>:</td>
                <td><input type="text" class="inputbox" size="70" name="exclude_words"
                           value="<?php echo urldecode(htmlspecialchars($exclude_words));?>"/><br/></td>
            </tr>
        </table>

        <h3><?php echo JText::_('advanced_search_options');?>:</h3>
        <table width="100%" border="0" cellspacing="0" cellpadding="3">
            <tr>
                <td width="20%"><?php echo JText::_('categories');?>:</td>
                <td width="50%"><?php echo $lists['category'];?></td>
                <td rowspan="2">
                    <div align="right">
                        <button type="submit" name="submit" class="btn"><?php echo JText::_('search');?></button>
                    </div>
                </td>
            </tr>
            <tr>
                <td><?php echo JText::_('date');?>:</td>
                <td><?php echo JText::_('from');?>:<?php echo $content_from_date;?> <?php echo JText::_('to');?>
                    :<?php echo $content_to_date;?> </td>
            </tr>
        </table>

        <p>&nbsp;</p>

        <table class="table table-striped table-bordered" cellspacing="0">
            <thead>
            <tr>
                <th><?php echo JText::_('title');?></th>
                <th width="125"><?php echo JText::_('date_updated');?></th>
                <th width="75"><?php echo JText::_('views');?></th>
            </tr>
            </thead><?php
            if (count($articles)) {
                $i = 0;
                foreach ($articles_rows as $row) {
                    ?>
                    <tr class="<?php echo (!$i ? 'first' : ($i % 2 ? 'even' : ''));?>">
                        <td width="99%">
                            <a href="<?php echo $row['link'];?>"><?php echo $row['title'];?></a>
                            <?php echo $row['link_edit'];?>
                            <?php if (!$row['approved']): ?>
                            <br/>
                            <small style="color:#f00000;font-weight:bold;">
                                <em><?php echo JText::_("PENDING_APPROVEMENT");?></em></small>
                            <?php endif;?>
	                        <br />
	                        <?php echo $row['content'];?>
                        </td>
                        <td width="125" nowrap="nowrap"><?php echo $row['date_updated'];?></td>
                        <td nowrap="nowrap"><?php echo $row['views'];?></td>
                    </tr><?php
                    $i++;
                }
            } else {
                echo JText::_('no_articles_category');
            } ?>
        </table>

    </form>

</div>