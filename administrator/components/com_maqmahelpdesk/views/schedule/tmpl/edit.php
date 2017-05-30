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
	static function display(&$scheduleInfo, $lists)
	{
		$editor = JFactory::getEditor(); ?>

	    <form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline">
			<?php echo JHtml::_('form.token'); ?>
	        <div class="breadcrumbs">
	            <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	            <a href="javascript:;"><?php echo JText::_('settings'); ?></a>
	            <a href="javascript:;"><?php echo JText::_('common'); ?></a>
	            <a href="javascript:;"><?php echo JText::_('expedient'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=schedule"><?php echo JText::_('schedules'); ?></a>
	            <span><?php echo JText::_('edit'); ?></span>
	        </div>
	        <div class="contentarea pad5">
	            <div class="row-fluid">
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('name')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('name')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('name'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
	                            <input type="text"
	                                   id="profile"
	                                   name="profile"
	                                   value="<?php echo (isset($scheduleInfo->profile)) ? $scheduleInfo->profile : ''; ?>"
	                                   maxlength="30" />
	                        </div>
	                    </div>
	                </div>
	                <div class="span6">
	                    <div class="row-fluid">
	                        <div class="span4 showPopover"
	                             data-original-title="<?php echo htmlspecialchars(JText::_('work_on_holidays')); ?>"
	                             data-content="<?php echo htmlspecialchars(JText::_('work_on_holidays')); ?>">
				                    <span class="label">
					                    <?php echo JText::_('work_on_holidays'); ?>
				                    </span>
	                        </div>
	                        <div class="span8">
								<?php echo $lists['work_on_holidays']; ?>
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
								<?php echo $editor->display('description', $scheduleInfo->description, '100%', '200', '75', '20');?>
	                        </div>
	                    </div>
	                </div>
	            </div>

	            <table class="table table-striped table-bordered">
	                <thead>
	                <tr>
	                    <th class="algcnt valgmdl"><?php echo JText::_('weekday'); ?></th>
	                    <th class="algcnt valgmdl"><?php echo JText::_('schedule_work_start'); ?></th>
	                    <th class="algcnt valgmdl"><?php echo JText::_('schedule_work_end'); ?></th>
	                    <th class="algcnt valgmdl"><?php echo JText::_('schedule_break_start'); ?></th>
	                    <th class="algcnt valgmdl"><?php echo JText::_('schedule_break_end'); ?></th>
	                </tr>
	                </thead>
	                <tbody><?php
						for ($w = 1; $w < 8; $w++)
						{
							$id = (isset($lists['schedule_weekdayInfo'][$w]['id'])) ? $lists['schedule_weekdayInfo'][$w]['id'] : '0';
							$weekday = (isset($lists['schedule_weekdayInfo'][$w]['weekday'])) ? $lists['schedule_weekdayInfo'][$w]['weekday'] : '0'; ?>
	                    <input type="hidden" name="schedule_id[]" value="<?php echo $id; ?>"/>
	                    <input type="hidden" name="schedule_weekday[]" value="<?php echo $weekday; ?>"/>
	                    <tr>
	                        <td class="algcnt valgmdl"><?php echo $lists['listweekday'][$w]; ?></td>
	                        <td class="algcnt valgmdl">
	                            <input type="text" class="timepicker" name="schedule_work_start[]" style="width:70px;"
	                                   value="<?php echo isset($lists['schedule_weekdayInfo'][$w]['work_start']) ? JString::substr($lists['schedule_weekdayInfo'][$w]['work_start'], 0, 5) : '00:00';?>"/>
	                        </td>
	                        <td class="algcnt valgmdl">
	                            <input type="text" class="timepicker" name="schedule_work_end[]" style="width:70px;"
	                                   value="<?php echo isset($lists['schedule_weekdayInfo'][$w]['work_end']) ? JString::substr($lists['schedule_weekdayInfo'][$w]['work_end'], 0, 5) : '00:00';?>"/>
	                        </td>
	                        <td class="algcnt valgmdl">
	                            <input type="text" class="timepicker" name="schedule_break_start[]" style="width:70px;"
	                                   value="<?php echo isset($lists['schedule_weekdayInfo'][$w]['break_start']) ? JString::substr($lists['schedule_weekdayInfo'][$w]['break_start'], 0, 5) : '00:00';?>"/>
	                        </td>
	                        <td class="algcnt valgmdl">
	                            <input type="text" class="timepicker" name="schedule_break_end[]" style="width:70px;"
	                                   value="<?php echo isset($lists['schedule_weekdayInfo'][$w]['break_end']) ? JString::substr($lists['schedule_weekdayInfo'][$w]['break_end'], 0, 5) : '00:00';?>"/>
	                        </td>
	                    </tr><?php
						} ?>
	                </tbody>
	            </table>
	        </div>

	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="task" value=""/>
	        <input type="hidden" name="id" value="<?php echo (isset($scheduleInfo->id)) ? $scheduleInfo->id : '0'; ?>"/>
	    </form>

	    <script type="text/javascript">
        Joomla.submitbutton = function (pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'schedule') {
                Joomla.submitform(pressbutton);
                return;
            }

            if (form.profile.value == "") {
                alert("<?php echo JText::_('profile_required'); ?>");
            } else {
                Joomla.submitform(pressbutton, document.getElementById('adminForm'));
            }

            var re = /(00|01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23)[:](0|1|2|3|4|5)\d{1}/;
            for (i = 0; i < form.elements.length; i++) {
                if (form.elements[i].name == "schedule_work_start[]" || form.elements[i].name == "schedule_work_end[]" || form.elements[i].name == "schedule_break_start[]" || form.elements[i].name == "schedule_break_start[]") {
                    var v = form.elements[i].value;
                    var n = form.elements[i].name;
                    if (!re.test(v)) {
                        form.elements[i].value = '00:00';
                    }
                }
            }
        }

	    $jMaQma(document).ready(function () {
	        $jMaQma(".timepicker").timepicker();
	        $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
	    });
	    </script><?php
	}
}
