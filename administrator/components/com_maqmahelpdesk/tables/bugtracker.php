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

class MaQmaHelpdeskTableBugtracker extends JTable
{
	var $id = 0;
	var $id_user = 0;
	var $id_workgroup = 0;
	var $id_category = 0;
	var $priority = null;
	var $date_created = null;
	var $date_updated = null;
	var $title = null;
	var $content = null;
	var $status = null;
	var $type = null;

	function __construct(&$_db)
	{
		parent::__construct('#__support_bugtracker', 'id', $_db);
	}
}