<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

//This counts the days in the week, up to 7
$day_count = 1;
?>
<table width="100%" id="eblog-calendar" class="hasTooltip">
	<tr>
		<th class="calendar_month" colspan="7">
			<a href="javascript:void(0)" class="prevMonth" onclick="<?php echo $previous; ?>">&#171;</a>
			<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=archive&layout=calendar&archiveyear='.$date['year'].'&archivemonth='.$date['month'].'&Itemid='.$itemId);?>"  class="currMonth"><?php echo JText::_( $calendar->title . '_SHORT' ) . ' ' . $date['year']; ?></a>
			<a href="javascript:void(0)" class="nextMonth"  onclick="<?php echo $next; ?>">&#187;</a>
		</th>
	</tr>
	<tr>
		<th class="calendar_day"><?php echo JText::_('SUN'); ?></th>
		<th class="calendar_day"><?php echo JText::_('MON'); ?></th>
		<th class="calendar_day"><?php echo JText::_('TUE'); ?></th>
		<th class="calendar_day"><?php echo JText::_('WED'); ?></th>
		<th class="calendar_day"><?php echo JText::_('THU'); ?></th>
		<th class="calendar_day"><?php echo JText::_('FRI'); ?></th>
		<th class="calendar_day"><?php echo JText::_('SAT'); ?></th>
	</tr>
	<tr>
<?php
while($calendar->blank > 0)
{
?>
	<td class="blank"></td>
<?php
	$calendar->blank = $calendar->blank-1;
	$day_count++;
}

//sets the first day of the month to 1
$day_num = 1;

//count up the days, untill we've done all of them in the month
while ( $day_num <= $calendar->days_in_month )
{
	$count = is_array( $postData->{$date['year']}->{$date['month']}->{$day_num} ) ? count( $postData->{$date['year']}->{$date['month']}->{$day_num} ) : 0;

	if($count)
	{
		$jdate = EasyBlogHelper::getDate($date['year'].'-'.$date['month'].'-'.$day_num);

		$doubleLenDay = ( strlen($day_num) <= 1 ) ? '0' . $day_num : $day_num;

		?>
			<td class="withpost">
                <span class="<?php echo $preFix; ?>_calendar">
					<div style="position:relative;text-align:center">
						<a href="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&view=archive&layout=calendar&archiveyear='.$date['year'].'&archivemonth='.$date['month'].'&archiveday='.$doubleLenDay.'&Itemid='.$itemId);?>"><?php echo $day_num ?></a>
					</div>
				</span>
				<?php echo EasyBlogTooltipHelper::getCalendarHTML( $postData->{$date['year']}->{$date['month']}->{$day_num} , $jdate->toFormat( $system->config->get( 'layout_dateformat' ) ), array('my'=>'left bottom','at'=>'left top','of'=>array('traverseUsing'=>'parent')), $itemId ); ?>
            </td>
		<?php
	}
	else
	{
		?>
		<td><span><?php echo $day_num ?></span></td>
		<?php
	}
	$day_num++;
	$day_count++;

	//Make sure we start a new row every week
	if ($day_count > 7)
	{
?>
		</tr><tr>
<?php
		$day_count = 1;
	}
}

while ( $day_count >1 && $day_count <=7 )
{
?>
	<td class="blank"></td>
<?php
	$day_count++;
}

?>
	</tr>
</table>
