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

class MaQmaHelpdeskTableDownloadAccess extends JTable
{
	var $id = 0;
	var $id_download = 0;
	var $id_user = 0;
	var $isactive = 0;
	var $serialno = null;
	var $servicefrom = null;
	var $serviceuntil = null;

	function __construct(&$_db)
	{
		parent::__construct('#__support_dl_access', 'id', $_db);
	}
}