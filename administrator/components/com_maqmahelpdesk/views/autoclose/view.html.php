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
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/autoclose.php';

require_once "components/com_maqmahelpdesk/views/autoclose/tmpl/default.php";

// Activities logger
HelpdeskUtility::ActivityLog('admin', 'autoclose', $task);

// Set toolbar and page title
HelpdeskAutoCloseAdminHelper::addToolbar();
HelpdeskAutoCloseAdminHelper::setDocument();

switch ($task)
{
	default:
		runAutoClose();
		break;
}

function runAutoClose()
{
	$database = JFactory::getDBO();
	$supportConfig = HelpdeskUtility::GetConfig();

	if ($supportConfig->ac_active == "1")
	{
		// Get the total number of records
		$sql = "SELECT t.id
				FROM #__support_ticket t, #__support_status s
				WHERE t.id_status=s.id AND s.status_group='O' AND to_days(now()) - to_days(t.last_update) > " . $supportConfig->ac_days;
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

				$sql = "UPDATE #__support_ticket
						SET id_status='$status',
							last_update='" . date("Y-m-d H:i:s") . "',
							autoclosed='" . date("Y-m-d") . "'
						WHERE id='" . $row->id . "'";
				$database->setQuery($sql);
				$database->query();
				$total++;
			}
		}

		$message = str_replace('%1', $supportConfig->ac_days, JText::_('autoclose_results'));
		$message = str_replace('%2', $total, $message);
	}
	else
	{
		$message = JText::_('autoclose_disabled');
	}

	HTML_autoClose::ShowAutoClose($message);
}
