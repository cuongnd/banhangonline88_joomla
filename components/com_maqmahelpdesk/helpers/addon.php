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

class HelpdeskAddon
{
	/*
	  * Function that calls the add-on's after the creation of a new ticket
	  * and the creation of a new reply and sends the information to the
	  * add-on to process it
	  * Params:
	  *		$new_ticket - indicates if it's a new ticket or a new reply
	  *		$ticket_id  - database id of the ticket
	  *		$reply_id	- database id of the reply
	  */
	static function Execute($type, $addon_action, $ticket_id, $reply_id = 0)
	{
		$database = JFactory::getDBO();

		// Get the ticket or the reply information
		$row = null;
		$sql = $addon_action ? "SELECT * FROM #__support_ticket WHERE id='" . $ticket_id . "'" : "SELECT t.ticketmask, t.id_user, t.assign_to, r.date, r.id_user, r.message, r.reply_summary, t.`an_name` FROM #__support_ticket_resp AS r INNER JOIN #__support_ticket AS t WHERE r.id='" . $reply_id . "' AND r.id_ticket='" . $ticket_id . "'";
		$database->setQuery($sql);
		$row = $database->loadObject();

		// Get the add-on's to run
		// - the 'execution' field have different values:
		//		- 1: normal (not used here)
		//		- 2: after new tickets and new activities
		//		- 3: in support center pages
		$database->setQuery("SELECT * FROM #__support_addon WHERE execution=" . $database->quote($type) . " AND publish='1'");
		$addons = $database->loadObjectList();

		for ($i = 0; $i < count($addons); $i++) {
			$addon = $addons[$i];
			include(JPATH_SITE . '/components/com_maqmahelpdesk/addon/' . $addon->sname . '/' . $addon->sname . '.php');
		}
	}
}
