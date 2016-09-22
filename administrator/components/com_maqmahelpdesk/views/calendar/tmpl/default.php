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
	static function display(&$row, $lists, $date_task, $hour_task, $mins_task, $ticket_info)
	{
		$GLOBALS['title_edit_calendar'] = ($row->id ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('task'); ?>

		<script type="text/javascript">
        $jMaQma(document).ready(function () {
            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
        });
		</script>

		<form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline">
			<?php echo JHtml::_('form.token'); ?>
			<div class="breadcrumbs">
				<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
				<a href="javascript:;"><?php echo JText::_('calendar'); ?></a>
				<span><?php echo JText::_('add_task'); ?></span>
			</div>
			<div class="contentarea pad5">
                <div class="row-fluid">
                    <div class="span6">
                        <div class="row-fluid">
                            <div class="span4 showPopover"
                                 data-original-title="<?php echo htmlspecialchars(JText::_('user')); ?>"
                                 data-content="<?php echo htmlspecialchars(JText::_('user')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('user'); ?>
			                    </span>
                            </div>
                            <div class="span8">
								<?php echo $lists['id_user']; ?>
                            </div>
                        </div>
                    </div>
                    <div class="span6">
                        <div class="row-fluid">
                            <div class="span4 showPopover"
                                 data-original-title="<?php echo htmlspecialchars(JText::_('status')); ?>"
                                 data-content="<?php echo htmlspecialchars(JText::_('status')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('status'); ?>
			                    </span>
                            </div>
                            <div class="span8">
                                <select name="status">
                                    <option
                                            value="O" <?php echo ($row->status == 'O' ? 'selected' : ''); ?>><?php echo JText::_('open'); ?></option>
                                    <option
                                            value="C" <?php echo ($row->status == 'C' ? 'selected' : ''); ?>><?php echo JText::_('closed'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6">
                        <div class="row-fluid">
                            <div class="span4 showPopover"
                                 data-original-title="<?php echo htmlspecialchars(JText::_('date')); ?>"
                                 data-content="<?php echo htmlspecialchars(JText::_('date')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('date'); ?>
			                    </span>
                            </div>
                            <div class="span8">
	                            <?php echo JHTML::Calendar($date_task, 'date_time', 'date_time', '%Y-%m-%d', array('class' => 'inputbox', 'size' => '12', 'maxlength' => '10')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="span6">
                        <div class="row-fluid">
                            <div class="span4 showPopover"
                                 data-original-title="<?php echo htmlspecialchars(JText::_('time')); ?>"
                                 data-content="<?php echo htmlspecialchars(JText::_('time')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('time'); ?>
			                    </span>
                            </div>
                            <div class="span8">
                                <input type="text"
                                       class="timepicker"
                                       id="taskmin"
                                       name="taskmin"
                                       maxlength="5"
                                       value="" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="row-fluid">
                            <div class="span2 showPopover"
                                 data-original-title="<?php echo htmlspecialchars(JText::_('description')); ?>"
                                 data-content="<?php echo htmlspecialchars(JText::_('description')); ?>">
			                    <span class="label">
				                    <?php echo JText::_('description'); ?>
			                    </span>
                            </div>
                            <div class="span10">
                                <textarea id="taskfield"
                                          name="taskfield"
                                          class="span10"
                                          style="height:50px;"><?php echo $row->task; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
			</div>

			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
			<input type="hidden" name="task" value=""/>
		</form><?php
	}
}
