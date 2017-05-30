<div class="maqmahelpdesk container-fluid">

	<h2><?php echo ($category_name!='' ? $category_name : JText::_('pathway_kb')); ?></h2>

    <form name="adminForm" action="<?php echo JRoute::_("index.php");?>" method="POST">
        <?php echo JHtml::_('form.token'); ?>
        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
        <input type="hidden" name="Itemid" value="<?php echo $Itemid;?>"/>
        <input type="hidden" name="id_workgroup" value="<?php echo $id_workgroup; ?>"/>
        <input type="hidden" name="task" value="kb_search"/>

        <div id="kbsearch">
            <p style="font-size:16px;"><?php echo JText::_('wk_searchkb');?></p>
	        <div class="control span9">
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
            <div class="span2"><h4><a href="<?php echo $row['link'];?>"><?php echo $row['name'];?>
                (<?php echo $row['articles'];?>)</a></h4></div>
            <?php endforeach; ?>
        <div class="clear h20"></div>
        <?php endif; ?>

        <?php if (count($articles)) :
        foreach ($articles_rows as $row) : ?>
            <div class="span2">
                <ul class="kbbullet">
                    <li class="article"><a
                        href="<?php echo $row['link'];?>"><?php echo $row['title'];?></a> <?php echo $row['link_edit'];?>
                        <?php if (!$row['approved']): ?>
                            <br/>
                            <small style="color:#f00000;font-weight:bold;">
                                <em><?php echo JText::_("PENDING_APPROVEMENT");?></em></small>
                            <?php endif;?>
                    </li>
                </ul>
            </div><?php
        endforeach; ?>
        <div class="clear"></div>
        <?php endif; ?>

        <?php
        $start = (($page + 1) - 15) < 2 ? 1 : (($page + 1) - 15);
        $end = (($page + 1) + 15) <= $pages ? (($page + 1) + 15) : $pages;
        if ($pages > 1): ?>
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
        endif;?>

    </form>

</div>