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

defined('_JEXEC') or die('Direct Access to this location is not allowed.'); ?>

<table class="table table-bordered nobordertopleft" width="100%" style="margin-bottom:0;">
    <tr>
        <td width="20%" class="algcnt">
            <h4><?php echo number_format($previous30->tickets, 0);?><br />
                <small>(<?php echo number_format($previous60->tickets, 0);?>)</small></h4>
            <small><?php echo JText::_("TICKETS_CREATED");?></small>
        </td>
        <td width="20%" class="algcnt">
            <h4><?php echo number_format($closed30, 0);?><br />
                <small>(<?php echo number_format($closed60, 0);?>)</small></h4>
            <small><?php echo JText::_("TICKETS_CLOSED");?></small>
        </td>
        <td width="20%" class="algcnt">
            <h4><?php echo number_format($current, 0);?><br />
                <small style="color:<?php echo (($previous30->tickets - $closed30) > 0 ? '#cc0000' : '#00b050');?>;"><?php echo (($previous30->tickets - $closed30) > 0 ? JText::_("OVERLOAD_INCREASED") : JText::_("OVERLOAD_DECREASED"));?></small></h4>
            <small><?php echo JText::_("CURRENTLY_OPENED");?></small>
        </td>
        <td width="20%" class="algcnt">
            <h4><?php echo number_format($messages30, 0);?><br />
                <small>(<?php echo number_format($messages60, 0);?>)</small></h4>
            <small><?php echo JText::_("MESSAGES");?></small>
        </td>
        <td width="20%" class="algcnt rb">
            <h4><?php echo ($previous30->avgreply != '' ? gmdate("H:i", $previous30->avgreplysec) : '-');?><br />
                <small>(<?php echo ($previous60->avgreply != '' ? gmdate("H:i", $previous60->avgreplysec) : '-');?>)</small></h4>
            <small><?php echo JText::_("AVG_FIRST_REPLY");?></small>
        </td>
    </tr>
</table>