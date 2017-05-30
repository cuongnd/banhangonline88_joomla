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

class MaQmaHelpdeskTableTask extends JTable
{
	var $id = null;
	var $id_ticket = 0;
	var $id_creator = 0;
	var $id_user = 0;
	var $date_time = null;
	var $task = null;
	var $status = null;
	var $timeused = 0;
	var $travel = 1;
	var $traveltime = 0;
	var $rate = 0;
	var $id_activity_rate = 0;
	var $id_activity_type = 0;
	var $start_time = null;
	var $end_time = null;
	var $break_time = null;
	var $end_date = null;

	function __construct(&$_db)
	{
		parent::__construct('#__support_task', 'id', $_db);
	}
}