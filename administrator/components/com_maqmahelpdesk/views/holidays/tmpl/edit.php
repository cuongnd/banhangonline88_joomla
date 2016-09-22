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
	static function display(&$holidayInfo, $lists)
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		$GLOBALS['holidays_title'] = ((isset($holidayInfo->id)) ? JText::_('edit') : JText::_('add')) . ' ' . JText::_('holiday'); ?>

	    <script language="javascript" type="text/javascript">
        Joomla.submitbutton = function (pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'holidays') {
                Joomla.submitform(pressbutton);
                return;
            }

            var re = /(0000|2008|2009|2010|2011|2012|2013|2014|2015|2016)[-](01|02|03|04|05|06|07|08|09|10|11|12)[-](01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31)/;
            var v = form.holiday_date.value;
            if (!re.test(v)) {
                alert("<?php echo JText::_('date_required'); ?>");
            } else {
                Joomla.submitform(pressbutton, document.getElementById('adminForm'));
            }
        }

        $jMaQma(document).ready(function(){
            $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
        });
	    </script>

	    <form action="index.php" method="post" id="adminForm" name="adminForm" class="label-inline">
			<?php echo JHtml::_('form.token'); ?>
	        <div class="breadcrumbs">
	            <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=holidays"><?php echo JText::_('holidays'); ?></a>
	            <span><?php echo JText::_('edit'); ?></span>
	        </div>
	        <div class="contentarea pad5">
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
	                            <input size="12"
	                                   type="text"
	                                   id="holiday_date"
	                                   name="holiday_date"
	                                   value="<?php echo (isset($holidayInfo->holiday_date)) ? $holidayInfo->holiday_date : ''; ?>"
	                                   maxlength="10" />
	                        </div>
	                    </div>
	                </div>
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
	                                   id="name"
	                                   name="name"
	                                   value="<?php echo (isset($holidayInfo->name)) ? $holidayInfo->name : ''; ?>"
	                                   maxlength="60" />
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>

	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" name="task" value=""/>
	        <input type="hidden" name="id" value="<?php echo (isset($holidayInfo->id)) ? $holidayInfo->id : '0'; ?>"/>
	    </form><?php
	}
}
