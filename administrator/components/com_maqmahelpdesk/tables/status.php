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

class MaQmaHelpdeskTableStatus extends JTable
{
	var $id = null;
	var $description = null;
	var $show = 1;
	var $status_group = null;
	var $isdefault = 0;
	var $user_access = 1;
	var $status_workflow = null;
	var $allow_old_status_back = 1;
	var $ticket_side = 0;
	var $isdefault_manager = 0;
	var $auto_status_agents = 0;
	var $auto_status_users = 0;
	var $ordering = 0;
	var $color = null;

	function __construct(&$_db)
	{
		parent::__construct('#__support_status', 'id', $_db);
	}
}