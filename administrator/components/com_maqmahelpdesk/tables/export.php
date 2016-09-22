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

class MaQmaHelpdeskTableExport extends JTable
{
	var $id = null;
	var $name = null;
	var $description = null;
	var $isdefault = 0;
	var $export_tmpl = null;
	var $billableonly = 0;
	var $export_type = null;
	var $auto_save = 1;
	var $filter_statusid = 0;
	var $filter_wkid = 0;
	var $filter_clientid = 0;
	var $filter_userid = 0;
	var $update_exported = 0;

	function __construct(&$_db)
	{
		parent::__construct('#__support_export_profile', 'id', $_db);
	}
}