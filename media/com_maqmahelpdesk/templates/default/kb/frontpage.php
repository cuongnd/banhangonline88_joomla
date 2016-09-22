<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('pathway_kb'); ?></h2>

    <form name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="POST">
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup; ?>"/>
        <input type="hidden" name="task" value="kb_search"/>

        <div id="kbsearch">
            <p style="font-size:16px;"><?php echo JText::_('wk_searchkb');?></p>
	        <div class="control">
		        <div class="input-append">
			        <input id="exact_phrase" name="exact_phrase" type="text"
			               style="width:80%; text-align:left; padding-top:4px; padding-bottom:4px; padding-left:10px;
                       height:22px; font-weight:bold; font-size:16px;"/><button type="submit" class="btn"
			           style="height:32px;"><?php echo JText::_('search');?></button>
		        </div>
	        </div>
            <p>
                <a href="<?php echo JURI::root();?>index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&task=rss&format=raw"
                   target="_blank"><img src="components/com_maqmahelpdesk/images/rssicon.jpg"
                                        align="absmiddle"/> <?php echo JText::_('subscribe_rss');?></a>
            </p>
        </div>

        <?php if (count($categories)) : ?>
        <?php foreach ($categories_rows as $row) : ?>
            <div class="span2" style="width:<?php echo ((100 - ($supportConfig->kb_number_columns * 3)) / $supportConfig->kb_number_columns);?>%;">
                <h4><?php echo $row['name'];?></h4>
                <ul class="kbbullet">
                    <?php foreach ($row['catarticles'] as $rowart) : ?>
                    <li class="<?php echo $rowart['type'];?>"><a href="<?php echo $rowart['link'];?>"
                                                                 title="<?php echo $rowart['title'];?>"><?php echo JString::substr($rowart['title'], 0, $supportConfig->kb_number_chars) . (strlen($rowart['title']) > $supportConfig->kb_number_chars ? '...' : '');?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <p><a href="<?php echo $row['link'];?>" title="<?php echo JText::_('kb_view_all')?>">
                    <small><?php echo JText::_('kb_view_all')?> &rsaquo;</small>
                </a></p>
            </div>
            <?php endforeach; ?>
        <div style="clear:both;"></div>
        <?php endif; ?>

    </form>

</div>