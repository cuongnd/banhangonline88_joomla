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

class MaQmaHelpdeskTableDownloadCategory extends JTable
{
	var $id = null;
	var $cname = "";
	var $description = "";
	var $ordering = 0;
	var $published = 1;
	var $parent = 0;
	var $image = null;
	var $level = 1;
	var $slug = null;

	function __construct(&$_db)
	{
		parent::__construct('#__support_dl_category', 'id', $_db);
	}
}