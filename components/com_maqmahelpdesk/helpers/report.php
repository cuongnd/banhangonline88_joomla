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

class HelpdeskReport
{
	// $return_datatype = 'numrecs' | 'duration' | 'dollarval'
	static function GetActivitiesTotals($userid, $activity_id, $from_datetime, $to_datetime, $return_datatype, $assign = 0, $client = -1)
	{
		$database = JFactory::getDBO();
		$supportConfig = HelpdeskUtility::GetConfig();

		// Check if it's for all clients
		$client_where = '';
		if ($client > 0 && $client != '-') {
			$client_where = " AND t.id_client='" . $client . "'";
		}
		if ($client == '-') {
			$client_where = " AND t.id_client='0'";
		}

		if ($assign > 0) {
			$assign_where = " AND t.assign_to='" . $assign . "'";
		} else {
			$assign_where = '';
		}

		if ($userid > 0) {
			$userid_where = " AND r.id_user='" . $userid . "'";
		} else {
			$userid_where = '';
		}

		if ($activity_id > 0) {
			$activity_where = " AND r.id_activity_type='" . $activity_id . "'";
		} else {
			$activity_where = '';
		}

		/*
		  if( $client_id > 0 ) {
			  $client_where = " AND t.id_user IN (SELECT u.id_user FROM #__support_client_users u WHERE u.id_client='".$client."')";
		  } else {
			  $client_where = '';
		  }

		  if( $wk_id > 0 ) {
			  $wk_where = " AND t.id_workgroup='".$workgroup."'";
		  } else {
			  $wk_where = '';
		  }
		  */

		switch ($return_datatype) {
			case 'numrecs':
				$sql = "SELECT COUNT(r.id) FROM #__support_ticket_resp r INNER JOIN #__support_ticket t ON t.id=r.id_ticket WHERE r.date>='" . $from_datetime . "' AND r.date<='" . $to_datetime . "'" . $userid_where . $activity_where . $assign_where . $client_where;
				//$wk_where;
				$database->setQuery($sql);
				$numrecs = $database->loadResult();
				if ($numrecs > 0) {
					return $numrecs;
				}
				break;

			case 'duration':
				$sql = "SELECT	(
					(SUM(
						TIME_TO_SEC( REPLACE(r.timeused,'.', ':') )
					))
					+
					(SUM(
						TIME_TO_SEC( REPLACE(r.tickettravel,'.', ':') )
					))
				) / 3600
					FROM #__support_ticket_resp r INNER JOIN #__support_ticket t ON t.id=r.id_ticket WHERE r.date>='" . $from_datetime . "' AND r.date<='" . $to_datetime . "'" . $userid_where . $activity_where . $assign_where . $client_where;
				$database->setQuery($sql);
				$duration = $database->loadResult();
				if ($duration > 0) {
					return HelpdeskDate::ConvertDecimalsToHoursMinutes($duration);
				}
				break;

			case 'dollarval':
				$sql = "SELECT SUM((( r.timeused + r.tickettravel) / 0.6 ) * r.user_rate) AS money
						FROM #__support_ticket_resp r INNER JOIN #__support_ticket t ON t.id=r.id_ticket WHERE r.date>='" . $from_datetime . "' AND r.date<='" . $to_datetime . "'" . $userid_where . $activity_where . $assign_where . $client_where;
				$database->setQuery($sql);
				$dollarval = $database->loadResult();
				return $supportConfig->currency . ' ' . number_format($dollarval, 2);
				break;
		}
	}

	static function GetTimeForDayInDecimals($date, $client, $workgroup, $user, $assign = 0)
	{
		$database = JFactory::getDBO();

		// Check if it's for all clients
		$client_where = '';
		if ($client > 0 && $client != '-') {
			$client_where = " AND t.id_client='" . $client . "'";
		}
		//if( $client == '-' ) {
		//	$client_where = " AND t.id_client='0'";
		//}

		// Check if it's for all workgroups
		$wk_where = '';
		if ($workgroup > 0) {
			$wk_where = " AND t.id_workgroup='" . $workgroup . "'";
		}

		// Check if it's for all users
		$user_where = '';
		if ($user > 0) {
			$user_where = " AND r.id_user='" . $user . "'";
		}

		// Check the assigned user
		$assign_where = '';
		if ($assign > 0) {
			$assign_where = " AND t.assign_to='" . $assign . "'";
		}

		$sql = "SELECT (
					(SUM(
						TIME_TO_SEC( REPLACE(r.timeused,'.', ':') )
					))
					+
					(SUM(
						TIME_TO_SEC( REPLACE(r.tickettravel,'.', ':') )
					))
				) AS money
				FROM #__support_ticket_resp r, #__support_ticket t
				WHERE t.id=r.id_ticket AND substring(r.`date`, 1, 10)=" . $database->quote($date) . "" . $user_where . $wk_where . $client_where . $assign_where;
		$database->setQuery($sql);
		if ($times = $database->loadResult())
		{
			return $times;
		}
	}

	static function CheckTime($time)
	{
		$time = str_replace('.', ':', $time);
		$time = explode(':', $time);
		$hours = $time[0];
		if (!isset($time[1]))
		{
			$time[1] = 0;
		}
		$mins = $time[1];

		if ($mins >= 60) {
			$mins = $mins - 60;
			$hours = $hours + 1;
		}

		if (strlen($mins) == 1) {
			$mins = $mins . '0';
		}

		if (strlen($hours) == 1) {
			$hours = '0' . $hours;
		}

		return ($hours == '' ? '00' : $hours) . ':' . ($mins == '' ? '00' : $mins);
	}
}
