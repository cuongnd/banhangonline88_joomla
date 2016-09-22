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

class MaQmaHelpdeskTableDepartmentFields extends JTable
{
	var $id = null;
	var $id_workgroup = 0;
	var $id_category = null;
	var $id_field = 0;
	var $required = 0;
	var $support_only = 0;
	var $ordering = 0;
	var $new_only = 0;
	var $section = null;

	function __construct(&$_db)
	{
		parent::__construct('#__support_wk_fields', 'id', $_db);
	}
}