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

class MaQmaHelpdeskTableAnnouncements extends JTable
{
	var $id = null;
	var $id_workgroup = 0;
	var $id_client = 0;
	var $date = null;
	var $introtext = null;
	var $bodytext = null;
	var $frontpage = 0;
	var $urgent = 0;
	var $sent = 0;
	var $slug = null;

	function __construct(&$_db)
	{
		parent::__construct('#__support_announce', 'id', $_db);
	}
}