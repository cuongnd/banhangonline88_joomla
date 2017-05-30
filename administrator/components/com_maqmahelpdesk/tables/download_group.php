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

class MaQmaHelpdeskTableDownloadGroup extends JTable
{
	var $id = 0;
	var $gname = "";
	var $description = "";
	var $unregister = 0;
	var $isdefault = 0;

	function __construct(&$_db)
	{
		parent::__construct('#__support_dl_group', 'id', $_db);
	}
}