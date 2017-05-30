<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: autoclose.php 646 2012-05-22 08:20:58Z pdaniel $
 * $LastChangedDate: 2012-05-22 09:20:58 +0100 (Ter, 22 Mai 2012) $
 *
 */

// Ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$CONFIG = new JConfig();
$database = JFactory::getDBO();

// Get config
$supportConfig = HelpdeskUtility::GetConfig();

$SecretWord = JRequest::getCmd('SecretWord', '', '', 'string');

if ($CONFIG->secret != $SecretWord)
{
	return false;
}

$sql = "SELECT publish
		FROM #__support_addon
		WHERE sname='tasks'";
$database->setQuery($sql);
$published = $database->loadResult();

if ($published && $supportConfig->ac_active)
{
	$sql = "SELECT t.id
			FROM #__support_ticket t, #__support_status s
			WHERE t.id_status=s.id AND s.status_group='O' AND to_days(now()) - to_days(t.date) > " . $supportConfig->ac_days;
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	if(!$supportConfig->autoclose_status)
	{
		$sql = "SELECT id
			FROM #__support_status
			WHERE status_group='C'";
		$database->setQuery($sql);
		$status = $database->loadResult();
	}
	else
	{
		$status = $supportConfig->autoclose_status;
	}

	$total = 0;

	if (count($rows) > 0)
	{
		for ($i = 0; $i < count($rows); $i++)
		{
			$row = &$rows[$i];

			$sql = "SELECT count(*)
					FROM #__support_ticket_resp
					WHERE id_ticket='" . $row->id . "'";
			$database->setQuery($sql);
			$resp = $database->loadResult();

			if ($resp > 0)
			{
				$sql = "UPDATE #__support_ticket
						SET id_status='" . $status . "',
							last_update='" . date("Y-m-d H:i:s") . "',
							autoclosed='" . date("Y-m-d") . "'
						WHERE id='" . $row->id . "'";
				$database->setQuery($sql);
				$database->query();
			}
		}
	}
}
