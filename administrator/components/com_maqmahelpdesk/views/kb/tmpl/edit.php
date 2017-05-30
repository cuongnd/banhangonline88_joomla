<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class MaQmaHtmlEdit
{
	static function display(&$row, $lists, $kbAttachs, $rate, $wkrows, $kbComments, $prodcat, $fromticket)
	{
		$editor = JFactory::getEditor();
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

	    <script language="javascript" type="text/javascript">
        Joomla.submitbutton = function (pressbutton)
        {
            var form = document.adminForm;
            if (pressbutton == 'kb_search')
            {
                Joomla.submitform(pressbutton);
                return;
            }

            if (form.kbtitle.value == "") {
                alert("<?php echo JText::_('title_required'); ?>");
            } else {<?php
	            if($supportConfig->editor != 'builtin'):
	            echo $editor->save('content');
		        endif; ?>
                ArrangeFields();
                Joomla.submitform(pressbutton, document.getElementById('adminForm'));
            }
        }

        function ShowComment(arg, img)
        {
            $jMaQma("#" + arg).slideToggle();
        }

        function ShowCategory()
        {
            var obj;
            var arg;
            var totwks;
            var WKObj = document.adminForm.id_workgroup;

            for (i = 1; i < WKObj.length; i++)
            {
                if (WKObj[i].selected)
                {
                    arg = 'cat' + WKObj[i].value;
                    obj = MM_findObj(arg)
                    obj = obj.style;
                    obj.display = 'block';
                }
                else
                {
                    arg = 'cat' + WKObj[i].value;
                    obj = MM_findObj(arg)
                    obj = obj.style;
                    obj.display = 'none';
                }
            }
        }

        function ArrangeFields()
        {
            NEMPS = '';

            var CATObj = document.adminForm.id_category;
            for (i = 0; i < CATObj.length; i++)
            {
                if (CATObj[i].selected == true && CATObj[i].value != 'WK')
                {
                    NEMPS = NEMPS + CATObj[i].value + ",";
                }
            }

            document.adminForm.categories.value = NEMPS.substr(0, NEMPS.length - 1);
        }

        function FillValues()
        {
            EMPS1 = "<?php echo $prodcat;?>";
            EMPS = EMPS1.split(",");

            var CATObj = document.adminForm.id_category;
            for (i = 0; i < CATObj.length; i++)
            {
                for (z = 0; z < EMPS.length; z++)
                {
                    if (CATObj[i].value == EMPS[z])
                    {
                        CATObj[i].selected = true;
                    }
                }
            }
        }

        $jMaQma(document).ready(function(){
            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
        });
	    </script>

	    <form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data"
	          class="label-inline">
			<?php echo JHtml::_('form.token'); ?>
	    <div class="breadcrumbs">
	        <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	        <a href="index.php?option=com_maqmahelpdesk&task=kb"><?php echo JText::_('kb'); ?></a>
	        <span><?php echo JText::_('edit'); ?></span>
	    </div>
	    <div class="tabbable tabs-left contentarea">
	    <ul class="nav nav-tabs equalheight">
	        <li class="active"><a href="#tab1" data-toggle="tab"><img
	                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/kb.png"
	                border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('article_header');?></a></li>
	        <li><a href="#tab2" data-toggle="tab"><img
	                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/table.png"
	                border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('content');?></a></li>
	        <li><a href="#tab3" data-toggle="tab"><img
	                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/attach.png"
	                border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('attachments');?></a></li>
	        <li><a href="#tab4" data-toggle="tab"><img
	                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/comments.png"
	                border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('comment');?></a></li>
	    </ul>
	    <div class="tab-content contentbar withleft pad5">
	    <div id="tab1" class="tab-pane active equalheight">
	    <div class="row-fluid">
	        <div class="span12">
	            <div class="row-fluid">
	                <div class="span2 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('title')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('title')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('title'); ?>
				                    </span>
	                </div>
	                <div class="span10">
	                    <input type="text"
                               class="span10"
	                           id="kbtitle"
	                           name="kbtitle"
	                           value="<?php echo $row->kbtitle; ?>"
	                           maxlength="150"
	                           onblur="CreateSlug('kbtitle');" />
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="row-fluid">
	        <div class="span12">
	            <div class="row-fluid">
	                <div class="span2 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('slug')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('slug_tooltip')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('slug'); ?>
				                    </span>
	                </div>
	                <div class="span10">
	                    <input type="text"
                               class="span10"
	                           id="slug"
	                           name="slug"
	                           value="<?php echo $row->slug; ?>"
	                           maxlength="100" />
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="row-fluid">
	        <div class="span6">
	            <div class="row-fluid">
	                <div class="span4 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('keywords')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('keywords')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('keywords'); ?>
				                    </span>
	                </div>
	                <div class="span8">
	                    <input type="text"
	                           id="keywords"
	                           name="keywords"
	                           value="<?php echo $row->keywords; ?>"
	                           maxlength="100" />
	                </div>
	            </div>
	        </div>
	        <div class="span6">
	            <div class="row-fluid">
	                <div class="span4 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('code')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('code')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('code'); ?>
				                    </span>
	                </div>
	                <div class="span8">
	                    <input type="text"
	                           id="kbcode"
	                           name="kbcode"
	                           value="<?php echo $row->kbcode; ?>"
	                           maxlength="100" />
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="row-fluid">
	        <div class="span12">
	            <div class="row-fluid">
	                <div class="span2 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('categories')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('categories')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('categories'); ?>
				                    </span>
	                </div>
	                <div class="span10">
						<?php echo $lists['categories']; ?>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="row-fluid">
	        <div class="span6">
	            <div class="row-fluid">
	                <div class="span4 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('access')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('access')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('access'); ?>
				                    </span>
	                </div>
	                <div class="span8">
						<?php echo $lists['anonymous']; ?>
	                </div>
	            </div>
	        </div>
	        <div class="span6">
	            <div class="row-fluid">
	                <div class="span4 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('approved')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('approved')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('approved'); ?>
				                    </span>
	                </div>
	                <div class="span8">
						<?php echo $lists['approved']; ?>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="row-fluid">
	        <div class="span6">
	            <div class="row-fluid">
	                <div class="span4 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('show_faq')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('show_faq')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('show_faq'); ?>
				                    </span>
	                </div>
	                <div class="span8">
						<?php echo $supportConfig->faq_kb_manual ? $lists['faq'] : '<i>' . JText::_('disabled_config') . '</i>'; ?>
	                </div>
	            </div>
	        </div>
	        <div class="span6">
	            <div class="row-fluid">
	                <div class="span4 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('publish')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('publish')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('publish'); ?>
				                    </span>
	                </div>
	                <div class="span8">
						<?php echo $lists['publish']; ?>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="row-fluid">
	        <div class="span6">
	            <div class="row-fluid">
	                <div class="span4 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('date_created')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('date_created')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('date_created'); ?>
				                    </span>
	                </div>
	                <div class="span8">
						<?php echo ($row->date_created == '' ? HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S") : $row->date_created); ?>
	                </div>
	            </div>
	        </div>
	        <div class="span6">
	            <div class="row-fluid">
	                <div class="span4 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('date_updated')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('date_updated')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('date_updated'); ?>
				                    </span>
	                </div>
	                <div class="span8">
						<?php echo ($row->date_updated == '' ? HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S") : $row->date_updated); ?>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div class="row-fluid">
	        <div class="span6">
	            <div class="row-fluid">
	                <div class="span4 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('views')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('views')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('views'); ?>
				                    </span>
	                </div>
	                <div class="span8">
						<?php echo $row->views; ?>
	                </div>
	            </div>
	        </div>
	        <div class="span6">
	            <div class="row-fluid">
	                <div class="span4 showPopover"
	                     data-original-title="<?php echo htmlspecialchars(JText::_('rating')); ?>"
	                     data-content="<?php echo htmlspecialchars(JText::_('rating')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('rating'); ?>
				                    </span>
	                </div>
	                <div class="span8">
	                    <img src="../media/com_maqmahelpdesk/images/rating/<?php echo ($rate->ratecount > 0 ? number_format($rate->ratesum / $rate->ratecount, 0) : 0);?>star.png" />
	                </div>
	            </div>
	        </div>
	    </div>
	    </div>
	    <div id="tab2" class="tab-pane equalheight pad5">
	        <div class="row-fluid">
	            <div class="span12">
	                <div class="row-fluid">
	                    <div class="span2 showPopover"
	                         data-original-title="<?php echo htmlspecialchars(JText::_('content')); ?>"
	                         data-content="<?php echo htmlspecialchars(JText::_('content')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('content'); ?>
				                    </span>
	                    </div>
	                    <div class="span10">
		                    <?php if($supportConfig->editor == 'builtin'):?>
					        <textarea id="kbcontent"
					                  name="kbcontent"
					                  class="redactor_agent"
					                  style="height:500px;"><?php echo $row->content;?></textarea>
							<?php else:?>
							<?php echo $editor->display('kbcontent', str_replace('\"', '"', $row->content), '100%', '500', '75', '20');?>
							<?php endif;?>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	    <div id="tab3" class="tab-pane equalheight">
	        <table class="table table-striped table-bordered ontop" style="margin-top:215px;">
	            <thead>
	            <tr>
	                <td class="title"><?php echo JText::_('filename'); ?></td>
	                <td class="title"><?php echo JText::_('description'); ?></td>
	                <td width="70" align="center"><?php echo JText::_('delete'); ?></td>
	            </tr>
	            </thead>
	            <tbody><?php
					$k = 0;
					$j = 0;
					for ($i = 0, $n = count($kbAttachs); $i < $n; $i++) {
						$row_attach = $kbAttachs[$i];
						$link = JRoute::_('index.php?option=com_maqmahelpdesk&task=kb_download&id=' . $row_attach->id_file . '&extid=' . $row_attach->id . '&format=raw'); ?>
		                <tr class="<?php echo "row$k"; ?>">
		                    <td>
		                        <a href="<?php echo $link;?>">
									<?php echo $row_attach->filename; ?>
		                        </a>
		                    </td>
		                    <td><?php echo $row_attach->description;?></td>
		                    <td width="70" align="center">
		                        <a href="index.php?option=com_maqmahelpdesk&task=kb_delattach&id_attach=<?php echo $row_attach->id_file;?>&filename=<?php echo $row_attach->filename;?>&id=<?php echo $row->id;?>"><img
		                                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/delete.png"
		                                border="0" alt="<?php echo JText::_('delete');?>"/></a>
		                    </td>
		                </tr><?php
					} // for
					if (count($kbAttachs) == 0) {
						?>
		                <tr>
		                    <td class="first" colspan="3"><?php echo JText::_('no_attachments');?>.</td>
		                </tr><?php
					} ?>
	            </tbody>
	        </table>

	        <p>&nbsp;</p><?php
			if (count($supportConfig->attachs_num) > 0) {
				for ($i = 0; $i < count($supportConfig->attachs_num); $i++) {
					?>
	                <div class="field w100">
						<span class="label" rel="tooltip"
                              data-original-title="<?php echo htmlspecialchars(JText::_('attachments')); ?>">
							<?php echo JText::_('attachments') . ' (' . ($i + 1) . ')'; ?>
						</span>
	                    <input class="large"
	                           type="file"
	                           id="file<?php echo $i; ?>"
	                           name="file<?php echo $i; ?>"
	                           value="" />
	                </div>
	                <div class="field w100" style="height:110px;">
						<span class="label" rel="tooltip"
                              data-original-title="<?php echo htmlspecialchars(JText::_('attachs_details')); ?>">
							<?php echo JText::_('attachs_details'); ?>
						</span>
	                    <textarea id="desc<?php echo $i; ?>"
	                              name="desc<?php echo $i; ?>"
	                              style="height:100px;"
	                              class="large"></textarea>
	                </div><?php
				}
			} ?>
	        <div class="clr"></div>
	    </div>
	    <div id="tab4" class="tab-pane equalheight">
	        <table class="table table-striped table-bordered ontop">
	            <thead>
	            <tr>
	                <td class="title"><?php echo JText::_('comment'); ?></td>
	                <td width="70" align="center"><?php echo JText::_('delete'); ?></td>
	            </tr>
	            </thead>
	            <tbody><?php
					$k = 0;
					$j = 0;
					for ($i = 0, $n = count($kbComments); $i < $n; $i++) {
						$row_comment = $kbComments[$i]; ?>
	                <tr class="<?php echo "row$k"; ?>">
	                    <td>
	                        <div onclick="ShowComment('comment<?php echo $i;?>','imgmsg<?php echo $i;?>');"
	                             style="cursor:pointer;float:left;"><?php echo $row_comment->name . ' @ ' . $row_comment->date;?></div>
	                        <div style="display:none; border-left:10px solid #ececec;" id="comment<?php echo $i;?>"
	                             name="comment<?php echo $i;?>"><?php echo nl2br($row_comment->comment);?></div>
	                    </td>
	                    <td>
	                        <a href="index.php?option=com_maqmahelpdesk&task=kb_delcomment&id_comment=<?php echo $row_comment->id;?>&id=<?php echo $row->id;?>"><img
	                                src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/delete.png"
	                                border="0" alt="<?php echo JText::_('delete');?>" align="absmiddle"/></a>
	                    </td>
	                </tr>
						<?php
					} // for
					if (count($kbComments) == 0) {
						?>
	                <tr>
	                    <td class="first"><?php echo JText::_('no_user_comments');?>.</td>
	                </tr><?php
					} ?>
	            </tbody>
	        </table>
	        <div class="clr"></div>
	    </div>
	    </div>
	    </div>

			<?php if ($row->id > 0 || $fromticket > 0): ?>
	    <input type="hidden" name="id_workgroup" value=""/>
	    <script type="text/javascript"> ShowCategory();
	    FillValues(); </script>
			<?php endif;?>

	    <input type="hidden" name="categories" value="">
	    <input type="hidden" name="fromticket" value="<?php echo $fromticket;?>"/>
	    <input type="hidden" name="views" value="<?php echo $row->views;?>"/>
	    <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	    <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
	    <input type="hidden" name="id_user" value="<?php echo ($row->id > 0 ? $row->id_user : $user->id); ?>"/>
	    <input type="hidden" name="date_created"
	           value="<?php echo ($row->id > 0 ? $row->date_created : HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S")); ?>"/>
	    <input type="hidden" name="date_updated" value="<?php echo HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S"); ?>"/>
	    <input type="hidden" name="task" value=""/>
	    </form>

	    <script type='text/javascript'>
	    $jMaQma(document).ready(function () {
	        $jMaQma(".equalheight").equalHeights();
	    });
	    </script><?php
	}
}
