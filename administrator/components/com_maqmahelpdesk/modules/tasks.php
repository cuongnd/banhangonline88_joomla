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

// Include helpers
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/date.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/task.php');

$imgpath = JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px'; ?>

<?php echo HelpdeskTask::SmallCalendar(HelpdeskDate::DateOffset("%m"), HelpdeskDate::DateOffset("%d"), HelpdeskDate::DateOffset("%Y")); ?>

<p><b><?php echo JText::_('legend'); ?>:</b>
	<br/>&nbsp;&nbsp;<img src="<?php echo $imgpath; ?>/status.png" align="absmiddle" border="0" hspace="2"
						  vspace="5"> <?php echo JText::_('task_ticket'); ?>
	<br/>&nbsp;&nbsp;<img src="<?php echo $imgpath; ?>/flag-yellow.png" align="absmiddle" border="0"
						  hspace="2" vspace="5"> <?php echo JText::_('task_isopen'); ?>
	<br/>&nbsp;&nbsp;<img src="<?php echo $imgpath; ?>/flag-green.png" align="absmiddle" border="0"
						  hspace="2" vspace="5"> <?php echo JText::_('task_isclosed'); ?>
	<br/>&nbsp;&nbsp;<img src="<?php echo $imgpath; ?>/flag-red.png" align="absmiddle" border="0" hspace="2"
						  vspace="5"> <?php echo JText::_('task_isdue'); ?></p>
<p><a href="index.php?option=com_maqmahelpdesk&task=calendar_new"
	  class="btn btn-success"><?php echo JText::_('add_task'); ?></a></p>

<script type="text/javascript">
    $jMaQma(document).ready(function () {
        $jMaQma('.showPopover').popover({'html':true, 'placement':'left', 'trigger':'hover'});
    });
</script>