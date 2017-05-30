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

class MaQmaHelpdeskTableSchedule extends JTable
{
	var $id = null;
	var $profile = null;
	var $description = null;
	var $work_on_holidays = 0;

	function __construct(&$_db)
	{
		parent::__construct('#__support_schedule', 'id', $_db);
	}
}