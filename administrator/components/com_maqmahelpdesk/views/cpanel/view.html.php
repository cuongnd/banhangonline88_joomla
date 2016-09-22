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

// Required helpers
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/dashboard.php';
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/reports.php';

// Javascript dependencies
HelpdeskUtility::AppendResource('jsapi', '//www.google.com/', 'js', true);

// Set toolbar and page title
HelpdeskDashboardAdminHelper::addToolbar();
HelpdeskDashboardAdminHelper::setDocument();

// Date settings
$date60 = mktime(0, 0, 0, date("m"), date("d") - 60, date("Y"));
$date30 = mktime(0, 0, 0, date("m"), date("d") - 30, date("Y"));
$previous_month1 = date("Y-m-d 00:00:00", $date60);
$previous_month2 = date("Y-m-d 00:00:00", $date30);

// Previous month
$sql = "SELECT
		    SEC_TO_TIME((SUM(unix_timestamp(`date_support`))/COUNT(*))-(SUM(unix_timestamp(`date`))/COUNT(*))) AS avgreply,
		    SUM(unix_timestamp(`date_support`)-unix_timestamp(`date`))/COUNT(*) AS avgreplysec,
		    COUNT(*) AS tickets
		FROM `#__support_ticket`
		WHERE (`date`)>='" . $previous_month1 . "' AND `date`<='" . $previous_month2 . "'";
$database->setQuery($sql);
$previous60 = $database->loadObject();

// Current month
$sql = "SELECT
		    SEC_TO_TIME((SUM(unix_timestamp(`date_support`))/COUNT(*))-(SUM(unix_timestamp(`date`))/COUNT(*))) AS avgreply,
		    SUM(unix_timestamp(`date_support`)-unix_timestamp(`date`))/COUNT(*) AS avgreplysec,
		    COUNT(*) AS tickets
		FROM `#__support_ticket`
		WHERE `date`>'" . $previous_month2 . "'";
$database->setQuery($sql);
$previous30 = $database->loadObject();

// Currently opened tickets
$sql = "SELECT
		    COUNT(*) AS tickets
		FROM `#__support_ticket` AS t
			 INNER JOIN `#__support_status` AS s ON s.`id`=t.`id_status`
		WHERE s.`status_group`='O'";
$database->setQuery($sql);
$current = $database->loadResult();

// Total messages 60 days ago
$sql = "SELECT
		    COUNT(*) AS messages
		FROM `#__support_ticket_resp` AS r
		WHERE r.`date`>='" . $previous_month1 . "'
		  AND r.`date` <= '" . $previous_month2 . "'";
$database->setQuery($sql);
$messages60 = $database->loadResult();

// Total messages last 30 days
$sql = "SELECT
		    COUNT(*) AS messages
		FROM `#__support_ticket_resp` AS r
		WHERE r.`date`>'" . $previous_month2 . "'";
$database->setQuery($sql);
$messages30 = $database->loadResult();

// Tickets closed 60 days ago
$sql = "SELECT
		    COUNT(*) AS tickets
		FROM `#__support_ticket` AS t
			 INNER JOIN `#__support_status` AS s ON s.`id`=t.`id_status`
		WHERE t.`last_update`>='" . $previous_month1 . "'
		  AND t.`last_update` <= '" . $previous_month2 . "'
		  AND s.`status_group`='C'";
$database->setQuery($sql);
$closed60 = $database->loadResult();

// Tickets closed last 30 days
$sql = "SELECT
		    COUNT(*) AS tickets
		FROM `#__support_ticket` AS t
			 INNER JOIN `#__support_status` AS s ON s.`id`=t.`id_status`
		WHERE t.`last_update`>'" . $previous_month2 . "'
		  AND s.`status_group`='C'";
$database->setQuery($sql);
$closed30 = $database->loadResult();

// Tickets open per status
$sql = "SELECT
		    COUNT(*) AS tickets,
		    s.`description`
		FROM `#__support_ticket` AS t
			 INNER JOIN `#__support_status` AS s ON s.`id`=t.`id_status`
		WHERE s.`status_group`='O'
		GROUP BY s.`description`";
$database->setQuery($sql);
$status = $database->loadObjectList();

include JPATH_SITE . '/administrator/components/com_maqmahelpdesk/views/cpanel/tmpl/default.php';
