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

class HelpdeskTask
{
	static function GetWeekStart($check_date, $start_day)
	{
		// Vai partir a data
		$year = substr($check_date, 0, 4);
		$month = substr($check_date, 5, 2);
		$day = substr($check_date, 8, 2);

		// Vai obter o dia da semana de hoje
		$day_of_week = date("w", mktime(0, 0, 0, $month, $day, $year));
		$first_day_of_week = date("Y-m-d", mktime(0, 0, 0, $month, $day - $day_of_week + $start_day, $year));

		return $first_day_of_week;
	}

	static function GetWeek($WEEK)
	{
		$id_workgroup = JRequest::getInt('id_workgroup', 0);
		$Itemid = JRequest::getInt('Itemid', 0);

		// Split the date into variables
		$date = explode('-', $WEEK);

		$first_day_of_week = date("d/m/Y", mktime(0, 0, 0, $date[1], $date[2], $date[0]));
		$second_day_of_week = date("d/m/Y", mktime(0, 0, 0, $date[1], $date[2] + 1, $date[0]));
		$third_day_of_week = date("d/m/Y", mktime(0, 0, 0, $date[1], $date[2] + 2, $date[0]));
		$fourth_day_of_week = date("d/m/Y", mktime(0, 0, 0, $date[1], $date[2] + 3, $date[0]));
		$fifth_day_of_week = date("d/m/Y", mktime(0, 0, 0, $date[1], $date[2] + 4, $date[0]));
		$sixth_day_of_week = date("d/m/Y", mktime(0, 0, 0, $date[1], $date[2] + 5, $date[0]));
		$seventh_day_of_week = date("d/m/Y", mktime(0, 0, 0, $date[1], $date[2] + 6, $date[0]));

		$sql_1 = date("Y-m-d", mktime(0, 0, 0, $date[1], $date[2], $date[0]));
		$sql_2 = date("Y-m-d", mktime(0, 0, 0, $date[1], $date[2] + 1, $date[0]));
		$sql_3 = date("Y-m-d", mktime(0, 0, 0, $date[1], $date[2] + 2, $date[0]));
		$sql_4 = date("Y-m-d", mktime(0, 0, 0, $date[1], $date[2] + 3, $date[0]));
		$sql_5 = date("Y-m-d", mktime(0, 0, 0, $date[1], $date[2] + 4, $date[0]));
		$sql_6 = date("Y-m-d", mktime(0, 0, 0, $date[1], $date[2] + 5, $date[0]));
		$sql_7 = date("Y-m-d", mktime(0, 0, 0, $date[1], $date[2] + 6, $date[0]));

		switch (date("w", mktime(0, 0, 0, $date[1], $date[2], $date[0]))) {
			case 0:
				$first_day_caption = JText::_('week_full_sunday');
				$second_day_caption = JText::_('week_full_monday');
				$third_day_caption = JText::_('week_full_tuesday');
				$fourth_day_caption = JText::_('week_full_wednesday');
				$fifth_day_caption = JText::_('week_full_thursday');
				$sixth_day_caption = JText::_('week_full_friday');
				$seventh_day_caption = JText::_('week_full_saturday');
				break;
			case 1:
				$first_day_caption = JText::_('week_full_monday');
				$second_day_caption = JText::_('week_full_tuesday');
				$third_day_caption = JText::_('week_full_wednesday');
				$fourth_day_caption = JText::_('week_full_thursday');
				$fifth_day_caption = JText::_('week_full_friday');
				$sixth_day_caption = JText::_('week_full_saturday');
				$seventh_day_caption = JText::_('week_full_sunday');
				break;
			case 2:
				$first_day_caption = JText::_('week_full_tuesday');
				$second_day_caption = JText::_('week_full_wednesday');
				$third_day_caption = JText::_('week_full_thursday');
				$fourth_day_caption = JText::_('week_full_friday');
				$fifth_day_caption = JText::_('week_full_saturday');
				$sixth_day_caption = JText::_('week_full_sunday');
				$seventh_day_caption = JText::_('week_full_monday');
				break;
			case 3:
				$first_day_caption = JText::_('week_full_wednesday');
				$second_day_caption = JText::_('week_full_thursday');
				$third_day_caption = JText::_('week_full_friday');
				$fourth_day_caption = JText::_('week_full_saturday');
				$fifth_day_caption = JText::_('week_full_sunday');
				$sixth_day_caption = JText::_('week_full_monday');
				$seventh_day_caption = JText::_('week_full_tuesday');
				break;
			case 4:
				$first_day_caption = JText::_('week_full_thursday');
				$second_day_caption = JText::_('week_full_friday');
				$third_day_caption = JText::_('week_full_saturday');
				$fourth_day_caption = JText::_('week_full_sunday');
				$fifth_day_caption = JText::_('week_full_monday');
				$sixth_day_caption = JText::_('week_full_tuesday');
				$seventh_day_caption = JText::_('week_full_wednesday');
				break;
			case 5:
				$first_day_caption = JText::_('week_full_friday');
				$second_day_caption = JText::_('week_full_saturday');
				$third_day_caption = JText::_('week_full_sunday');
				$fourth_day_caption = JText::_('week_full_monday');
				$fifth_day_caption = JText::_('week_full_tuesday');
				$sixth_day_caption = JText::_('week_full_wednesday');
				$seventh_day_caption = JText::_('week_full_thursday');
				break;
			case 6:
				$first_day_caption = JText::_('week_full_saturday');
				$second_day_caption = JText::_('week_full_sunday');
				$third_day_caption = JText::_('week_full_monday');
				$fourth_day_caption = JText::_('week_full_tuesday');
				$fifth_day_caption = JText::_('week_full_wednesday');
				$sixth_day_caption = JText::_('week_full_thursday');
				$seventh_day_caption = JText::_('week_full_friday');
				break;
		}

		$link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=calendar_day&date=';
		ob_start(); ?>

	<table class="table table-bordered">
		<thead>
		<tr>
			<th class="tac calendar_header">
				<a href="<?php echo $link . $sql_1 ?>"><?php echo $first_day_caption; ?>
					<br><?php echo $first_day_of_week ?></a>
			</th>
			<th class="tac calendar_header">
				<a href="<?php echo $link . $sql_2 ?>"><?php echo $second_day_caption; ?>
					<br><?php echo $second_day_of_week ?></a>
			</th>
			<th class="tac calendar_header">
				<a href="<?php echo $link . $sql_3 ?>"><?php echo $third_day_caption; ?>
					<br><?php echo $third_day_of_week ?></a>
			</th>
			<th class="tac calendar_header">
				<a href="<?php echo $link . $sql_4 ?>"><?php echo $fourth_day_caption; ?>
					<br><?php echo $fourth_day_of_week ?></a>
			</th>
			<th class="tac calendar_header">
				<a href="<?php echo $link . $sql_5 ?>"><?php echo $fifth_day_caption; ?>
					<br><?php echo $fifth_day_of_week ?></a>
			</th>
			<th class="tac calendar_header">
				<a href="<?php echo $link . $sql_6 ?>"><?php echo $sixth_day_caption; ?>
					<br><?php echo $sixth_day_of_week ?></a>
			</th>
			<th class="tac calendar_header">
				<a href="<?php echo $link . $sql_7 ?>"><?php echo $seventh_day_caption; ?>
					<br><?php echo $seventh_day_of_week ?></a>
			</th>
		</tr>
		</thead>
		<tr style="min-height:30px; height:30px;">
			<td class="day_content"><?php echo HelpdeskTask::GetTasksForDay($sql_1); ?></td>
			<td class="day_content"><?php echo HelpdeskTask::GetTasksForDay($sql_2); ?></td>
			<td class="day_content"><?php echo HelpdeskTask::GetTasksForDay($sql_3); ?></td>
			<td class="day_content"><?php echo HelpdeskTask::GetTasksForDay($sql_4); ?></td>
			<td class="day_content"><?php echo HelpdeskTask::GetTasksForDay($sql_5); ?></td>
			<td class="day_content"><?php echo HelpdeskTask::GetTasksForDay($sql_6); ?></td>
			<td class="day_content"><?php echo HelpdeskTask::GetTasksForDay($sql_7); ?></td>
		</tr>
	</table><?php
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	static function SmallCalendar($month, $day, $year)
	{
		$database = JFactory::getDBO();
		$supportConfig = HelpdeskUtility::GetConfig();

		//Get tasks for selected year and month
		$sql = "SELECT *
				FROM #__support_task
				WHERE month(date_time)=" . $database->quote($month) . " AND year(date_time)=" . $database->quote($year) . "
				ORDER BY date_time ASC";
		$database->setQuery($sql);
		$today_tasks = $database->loadObjectList();

		//If no parameter is passed use the current date.
		$this_month = getDate(mktime(0, 0, 0, $month, 1, $year));
		$next_month = getDate(mktime(0, 0, 0, $month + 1, 1, $year));

		//Find out when this month starts and ends.
		$first_week_day = $this_month["wday"];
		$days_in_this_month = round(($next_month[0] - $this_month[0]) / (60 * 60 * 24));

		$day_names = array();
		for ($n = 0, $t = (3 + $supportConfig->week_start) * 86400; $n < 7; $n++, $t += 86400)
			$day_names[$n] = ucfirst(gmstrftime('%A', $t)); #%A means full textual day name

		$calendar_html = '<table class="table table-striped table-bordered noleftborder">';
		$calendar_html .= '<thead><tr><th colspan="7" style="text-align:center;background:#efefef;">' . HelpdeskDate::GetMonthName($month) . ' ' . $year . '</th></tr>';
		$calendar_html .= '<tr>';
		foreach ($day_names as $d)
			$calendar_html .= '<th width="120" height="20" style="background:#efefef;">' . htmlentities(substr($d, 0, 3)) . '</th>';
		$calendar_html .= '</tr></thead>';
		$calendar_html .= '<tbody><tr>';

		//Fill the first week of the month with the appropriate number of blanks.
		for ($week_day = $supportConfig->week_start; $week_day < $first_week_day; $week_day++) {
			$calendar_html .= '<td>&nbsp;</td>';
		}

		//Create calendar
		$week_day = $first_week_day;
		for ($day_counter = 1; $day_counter <= $days_in_this_month; $day_counter++) {
			$week_day %= 7;

			if ($week_day == $supportConfig->week_start)
				$calendar_html .= '</tr><tr>';

			$curday = '';
			$tasks = '';
			$i = 0;
			for ($i = 0; $i < count($today_tasks); $i++) {
				$today_task = $today_tasks[$i];
				$curday = substr($today_task->date_time, 8, 2);
				if ($curday == $day_counter) {
					$tasks .= $today_task->task;
				}
			}

			//Do something different for the current day.
			if ($day == $day_counter) {
				$show_hints = 0;
				for ($i = 0; $i < count($today_tasks); $i++) {
					$today_task = $today_tasks[$i];
					$curday = substr($today_task->date_time, 8, 2);
					if ($curday == $day_counter) {
						$show_hints = 1;
					}
				}

				if ($show_hints == 0) {
					$calendar_html .= '<td><div align="center"><b>' . $day_counter . '</b></div></td>';
				} else {
					$popover = HelpdeskTask::GetCalendarPopovers($day_counter,$month,$year);
					$calendar_html .= '<td ' . ($popover!='' ? 'class="showPopover" data-original-title="' . strip_tags(JText::_('tasks')) . '" data-content="' . $popover . '"' : '') . '><div align="center"><b><span class="lbl lbl-success">' . $day_counter . '</span></b></div></td>';
				}
			} else {
				$show_hints = 0;
				for ($i = 0; $i < count($today_tasks); $i++) {
					$today_task = $today_tasks[$i];
					$curday = substr($today_task->date_time, 8, 2);
					if ($curday == $day_counter) {
						$show_hints = 1;
					}
				}
				if ($show_hints == 0) {
					$calendar_html .= '<td><div align="center">' . $day_counter . '</div></td>';
				} else {
					$popover = HelpdeskTask::GetCalendarPopovers($day_counter,$month,$year);
					$calendar_html .= '<td ' . ($popover!='' ? 'class="showPopover" data-original-title="' . strip_tags(JText::_('tasks')) . '" data-content="' . $popover . '"' : '') . '><div align="center"><b><span class="lbl lbl-success">' . $day_counter . '</span></b></div></td>';
				}
			}

			$week_day++;
		}

		$calendar_html .= '</tr></tbody>';
		$calendar_html .= '</table>';

		return ($calendar_html);
	}

	static function GetCalendarPopovers($day, $month, $year)
	{
		$database = JFactory::getDBO();
		$supportConfig = HelpdeskUtility::GetConfig();
		$calendar_html = '';

		//Get tasks for selected year and month
		$sql = "SELECT *
				FROM #__support_task
				WHERE day(date_time)=" . $database->quote($day) . " AND month(date_time)=" . $database->quote($month) . " AND year(date_time)=" . $database->quote($year) . "
				ORDER BY date_time ASC";
		$database->setQuery($sql);
		$tasks = $database->loadObjectList();

		for ($i = 0; $i < count($tasks); $i++) {
			$task = $tasks[$i];

			$task_hours = substr($task->date_time, 11, 2);
			$task_minutes = substr($task->date_time, 14, 2);
			$task_decimal = str_replace('-', '', substr($task->date_time, 0, 10)) . $task_hours . '.' . $task_minutes;
			$cur_decimal = date("YmdH.i");
			$calendar_html .= ($task->status == 'O' ? '<img src=\'' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/flag-' . ($task_decimal > $cur_decimal ? 'yellow' : 'red') . '.png\' align=\'absmiddle\' border=\'0\' />&nbsp;' : '<img src=\'' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/flag-green.png\' align=\'absmiddle\' border=\'0\' />&nbsp;') . "<b>" . $task_hours . ':' . $task_minutes . "</b> > " . substr($task->task, 0, 100) . ($task->id_ticket > 0 ? '&nbsp;<img src=\'' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/status.png\' align=\'absmiddle\' border=\'0\' />' : '');
			$calendar_html .= "<br />\n";
		}

		return $calendar_html;
	}

	static function GetTasksForDay($date, $module = 0, $modclass_sfx = '')
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		$id_workgroup = JRequest::getInt('id_workgroup', 0);
		$Itemid = JRequest::getInt('Itemid', 0);

		$database = JFactory::getDBO();
		$user = JFactory::getUser();

		$sql = "SELECT *
				FROM #__support_task 
				WHERE substring(`date_time`,1,10)=" . $database->quote($date) . " AND id_user IN (" . $user->id . ")
				ORDER BY `date_time` ASC";
		$database->setQuery($sql);
		$today_tasks = $database->loadObjectList();

		$tasks_for_day = '';

		if (count($today_tasks) == 0) {
			if ($module) {
				$tasks_for_day .= '<tr><td class="alglft">' . JText::_('no_open_tasks_today') . '</td></tr>';
			} else {
				$tasks_for_day .= "";
			}
		} else {
			for ($i = 0; $i < count($today_tasks); $i++) {
				$today_task = $today_tasks[$i];
				$link = "index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=calendar_edit&id=" . $today_task->id;

				if ($module) {
					$tasks_for_day .= '<tr>';
					$tasks_for_day .= '<td class="alglft">';
					$tasks_for_day .= '<span class="lbl lbl-inverse">' . substr($today_task->date_time, 11, 5) . '</span>' . ($today_task->id_ticket > 0 ? ' <img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/status.png" align="absmiddle" border="0" />' : '') . '<br /><a href="' . JRoute::_($link) . '" class="sublevel' . $modclass_sfx . '"><em>' . substr($today_task->task, 0, 100) . '</em></a>';
					$tasks_for_day .= '</td>';
					$tasks_for_day .= '</tr>';
				} else {
					$tasks_for_day .= '<span class="lbl lbl-inverse">' . substr($today_task->date_time, 11, 5) . '</span>' . ($today_task->id_ticket > 0 ? ' <img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/status.png" align="absmiddle" border="0" />' : '') . ($today_task->status == 'O' ? '&nbsp;<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/flag-' . ($i < date("d") ? 'yellow' : 'red') . '.png" align="absmiddle" border="0" />' : '&nbsp;<img src="' . JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/flag-green.png" align="absmiddle" border="0" />') . '<br /><a href="' . JRoute::_($link) . '"><em>' . substr($today_task->task, 0, 100) . "</em></a><br />\n";
				}
			}
		}

		return $tasks_for_day;
	}

	static function GetDay($date)
	{
		$date_part = explode('-', $date);

		switch (date("w", mktime(0, 0, 0, $date_part[1], $date_part[2], $date_part[0]))) {
			case '0':
				$date_text = JText::_('week_full_sunday');
				break;
			case '1':
				$date_text = JText::_('week_full_monday');
				break;
			case '2':
				$date_text = JText::_('week_full_tuesday');
				break;
			case '3':
				$date_text = JText::_('week_full_wednesday');
				break;
			case '4':
				$date_text = JText::_('week_full_thursday');
				break;
			case '5':
				$date_text = JText::_('week_full_friday');
				break;
			case '6':
				$date_text = JText::_('week_full_saturday');
				break;
		}

		ob_start(); ?>
	<table class="table table-bordered">
		<thead>
		<tr>
			<th class="day_header">
				<?php echo $date; ?> -
				<?php echo $date_text; ?>
			</th>
		</tr>
		</thead>
		<tr>
			<td class="day_content">
				<?php
				$TaskForDay = HelpdeskTask::GetTasksForDay($date);
				if ($TaskForDay == '') $TaskForDay = JText::_('no_tasks');
				echo $TaskForDay;
				?>
			</td>
		</tr>
	</table><?php
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	static function GetList($year, $month)
	{
		ob_start(); ?>
	<table class="table table-bordered">
		<tr>
			<td class="calendar_header" colspan="2">
				<?php echo HelpdeskDate::GetMonthName(str_pad($month,2,0,STR_PAD_LEFT)); ?> <?php echo $year; ?>
			</td>
		</tr>
		<?php

		$month = ($month < 10) ? "0" . $month : $month;
		for ($i = 1; $i <= HelpdeskDate::GetMonthDays($year, $month); $i++) {
			if (HelpdeskTask::GetTasksForDay($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i)) != '') {
				?>
				<tr>
					<td class="day_header" width="15%">
						<?php echo $year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i); ?>
					</td>
					<td class="day_content">
						<?php echo HelpdeskTask::GetTasksForDay($year . '-' . $month . '-' . ($i < 10 ? '0' . $i : $i)); ?>
					</td>
				</tr>
				<?php
			}
		} ?>
	</table><?php
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	static function Calendar($year, $month, $day_name_length = 3, $month_href = NULL, $first_day = 0)
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		$id_workgroup = JRequest::getInt('id_workgroup', 0);
		$Itemid = JRequest::getInt('Itemid', 0);

		$today = date('d');
		$days = array(1 => array(NULL, 'day', NULL), 2 => array(NULL, 'day', NULL), 3 => array(NULL, 'day', NULL), 4 => array(NULL, 'day', NULL), 5 => array(NULL, 'day', NULL), 6 => array(NULL, 'day', NULL), 7 => array(NULL, 'day', NULL), 8 => array(NULL, 'day', NULL), 9 => array(NULL, 'day', NULL), 10 => array(NULL, 'day', NULL), 11 => array(NULL, 'day', NULL), 12 => array(NULL, 'day', NULL), 13 => array(NULL, 'day', NULL), 14 => array(NULL, 'day', NULL), 15 => array(NULL, 'day', NULL), 16 => array(NULL, 'day', NULL), 17 => array(NULL, 'day', NULL), 18 => array(NULL, 'day', NULL), 19 => array(NULL, 'day', NULL), 20 => array(NULL, 'day', NULL), 21 => array(NULL, 'day', NULL), 22 => array(NULL, 'day', NULL), 23 => array(NULL, 'day', NULL), 24 => array(NULL, 'day', NULL), 25 => array(NULL, 'day', NULL), 26 => array(NULL, 'day', NULL), 27 => array(NULL, 'day', NULL), 28 => array(NULL, 'day', NULL), 29 => array(NULL, 'day', NULL), 30 => array(NULL, 'day', NULL), 31 => array(NULL, 'day', NULL), $today => array(NULL, 'today', NULL));
		$first_of_month = gmmktime(0, 0, 0, $month, 1, $year);
		$img_path = JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/calendar.png';
		$link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=calendar_week&date=';

		$day_names = array();
		for ($n = 0, $t = (3 + $first_day) * 86400; $n < 7; $n++, $t += 86400)
			$day_names[$n] = ucfirst(gmstrftime('%A', $t)); #%A means full textual day name

		list($month, $year, $month_name, $weekday) = explode(',', gmstrftime('%m,%Y,%B,%w', $first_of_month));
		$weekday = ($weekday + 7 - $first_day) % 7;
		$title = HelpdeskDate::GetMonthName($month) . '&nbsp;' . $year;

		$calendar = '<table class="table table-bordered">' . "\n" . '<caption class="month">' . $title . "</caption>\n<tr>";

		if ($day_name_length) {
			$calendar .= '<td class="weeklink"></td>';
			foreach ($day_names as $d)
				$calendar .= '<td class="day_header calendar_header tac" abbr="' . htmlentities($d) . '">' . htmlentities($day_name_length < 4 ? substr($d, 0, $day_name_length) : $d) . '</td>';
			$calendar .= "</tr>\n<tr>";
		}

		if ($weekday > 0) {
			$weekyear = date("W", mktime(0, 0, 0, $month, 1 - $weekday, $year));
			$calendar .= '<td class="tac calendar_task"><a href="' . $link . $year . '-' . $month . '-' . date("d", mktime(0, 0, 0, $month, 1 - $weekday, $year)) . '">' . JText::_('week') . '<br />' . $weekyear . '</a></td>' . '<td colspan="' . ($weekday) . '">&nbsp;</td>';
		} else {
			$weekyear = date("W", mktime(0, 0, 0, $month, 1 - $weekday, $year));
			$calendar .= '<td class="tac calendar_task"><a href="' . $link . $year . '-' . $month . '-01">' . JText::_('week') . '<br />' . $weekyear . '</a></td>';
		}
		for ($day = 1, $days_in_month = gmdate('t', $first_of_month); $day <= $days_in_month; $day++, $weekday++) {
			if ($weekday == 7) {
				$weekday = 0; #start a new week
				$calendar .= "</tr>\n<tr>";
				$weekyear = date("W", mktime(0, 0, 0, $month, $day, $year));
				$calendar .= '<td class="tac calendar_task"><a href="index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=calendar_week&date=' . $year . '-' . $month . '-' . ($day < 10 ? '0' . $day : $day) . '">' . JText::_('week') . '<br />' . $weekyear . '</a></td>';
			}
			if (isset($days[$day]) and is_array($days[$day])) {
				@list($link, $classes, $content) = $days[$day];
				if (is_null($content)) $content = $day;
				$calendar .= '<td' . ($classes ? ' class="' . htmlspecialchars($classes) . ' equalheight">&nbsp;' : '>&nbsp;') . '<span class="lbl">' . $content . '</span><br />' . HelpdeskTask::GetTasksForDay($year . '-' . $month . '-' . ($content < 10 ? '0' : '') . $content) . '</td>';
			}
			else $calendar .= '<td><span class="lbl">' . $day . '</span></td>';
		}
		if ($weekday != 7) $calendar .= '<td colspan="' . (7 - $weekday) . '">&nbsp;</td>';

		return $calendar . "</tr>\n</table>\n";
	}
}
