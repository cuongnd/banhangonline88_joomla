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
	static function display(&$row, $lists)
	{
		$editor = JFactory::getEditor();
		$supportConfig = HelpdeskUtility::GetConfig();
		$GLOBALS['title_edit_announce'] = ($row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('announcement'); ?>

	    <script type="text/javascript">
        $jMaQma(document).ready(function () {
            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
        });
	    </script>

	    <form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline">
			<?php echo JHtml::_('form.token'); ?>
	        <div class="breadcrumbs">
	            <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=announce"><?php echo JText::_('announcements'); ?></a>
	            <span><?php echo JText::_('edit'); ?></span>
	        </div>
	        <div class="contentarea pad5">
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('workgroup')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('workgroup')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('workgroup'); ?>
			                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['workgroup']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('client')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('client')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('client'); ?>
			                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['client']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('frontpage')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('frontpage')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('frontpage'); ?>
			                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['frontpage']; ?>
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('urgent')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('urgent')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('urgent'); ?>
			                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['urgent']; ?>
	                        </div>
	                    </div>
	                </div>
	            </div>
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
	                                   id="introtext"
	                                   name="introtext"
	                                   value="<?php echo $row->introtext; ?>"
	                                   maxlength="100" />
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
	                <div class="span12">
	                    <div class="row-fluid">
	                        <div class="span2 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('body')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('body')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('body'); ?>
			                    </span>
	                        </div>
	                        <div class="span10">
		                        <?php if($supportConfig->editor == 'builtin'):?>
                                <textarea id="bodytext"
                                          name="bodytext"
                                          class="redactor_agent"
		                                  style="height:500px;"><?php echo $row->bodytext;?></textarea>
		                        <?php else:?>
		                        <?php echo $editor->display('bodytext', $row->bodytext, '98%', '500', '75', '20');?>
		                        <?php endif;?>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>

	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
	        <input type="hidden" name="task" value=""/>
	        <input type="hidden" name="date" value="<?php echo ($row->id > 0 ? $row->date : HelpdeskDate::DateOffset("%Y-%m-%d")); ?>"/>
	    </form><?php
	}
}
