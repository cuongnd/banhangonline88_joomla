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

class MaQmaHelpdeskTablePriority extends JTable
{
	var $id = null;
	var $description = null;
	var $show = 1;
	var $timevalue = 0;
	var $timeunit = null;
	var $isdefault = 0;

	function __construct(&$_db)
	{
		parent::__construct('#__support_priority', 'id', $_db);
	}
}