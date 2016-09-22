<div class="maqmahelpdesk container-fluid">

	<h2><?php echo JText::_('discussions');?></h2>

    <p>&nbsp;</p>

    <div id="discussions"><?php
        if (!count($rows)) {
            echo '<div class="alert">' . JText::_('register_not_found') . '</div>';
        } else {
            for ($i = 0; $i < count($rows); $i++) {
                $row = $rows[$i];
                $sql = "SELECT d.`id`, d.`title`, COUNT(m.`id`) AS total
						FROM `#__support_discussions` AS d
							 LEFT JOIN `#__support_discussions_messages` AS m ON m.`id_discussion`=d.`id` AND m.`published`=1
						WHERE d.`id_category`=" . $row->id . " AND d.`published`=1
						GROUP BY d.`id`, d.`title`
						ORDER BY d.`views` DESC
						LIMIT 0, 10";
                $database->setQuery($sql);

                $questions = $database->loadObjectList(); ?>
                <div class="discussion-category">
                    <h2><a
                        href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=discussions_category&id_category=<?php echo $row->id;?>"
                        title="<?php echo $row->name;?>"><?php echo $row->name;?></a></h2><?php
                    if (!count($questions)) {
                        ?>
                        <div class="alert">
                            <p><?php echo JText::_('register_not_found');?></p>

                            <p><a
                                href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=discussions_question&id_category=<?php echo $row->id;?>"
                                title="<?php echo JText::_('be_first_post');?>"><?php echo JText::_('be_first_post');?></a>
                            </p>
                        </div><?php
                    } else {
                        for ($x = 0; $x < count($questions); $x++) {
                            $question = $questions[$x]; ?>
                            <div><span class="lbl"><?php echo $question->total;?></span> <a
                                href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=discussions_view&id=<?php echo $question->id;?>"
                                title="<?php echo $question->title;?>"><?php echo $question->title;?></a></div><?php
                        }
                    } ?>
                    <div class="viewmore"><a
                        href="index.php?option=com_maqmahelpdesk&Itemid=<?php echo $Itemid;?>&id_workgroup=<?php echo $id_workgroup;?>&task=discussions_category&id_category=<?php echo $row->id;?>"
                        title="<?php echo JText::_('more')?>"><?php echo JText::_('more')?></a></div>
                </div><?php
            }
        } ?>
    </div>

    <div class="clear"></div>

</div>