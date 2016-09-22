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

class MaQmaHelpdeskTableStaff extends JTable
{
	var $id = null;
	var $id_user = null;
	var $id_workgroup = null;
	var $manager = 0;
	var $assign_report_users = null;
	var $can_delete = 1;
	var $level = 0;
	var $bugtracker = 1;

	function __construct(&$_db)
	{
		parent::__construct('#__support_permission', 'id', $_db);
	}
}