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

class MaQmaHelpdeskTableDownloadVersion extends JTable
{
	var $id = null;
	var $id_download = 0;
	var $version = "";
	var $date = "";
	var $description = "";
	var $filename = "";
	var $filename_original = null;

	function __construct(&$_db)
	{
		parent::__construct('#__support_dl_version', 'id', $_db);
	}
}